<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Utility;

/*
 * This file is part of the TYPO3 Multicolumn project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\BackendWorkspaceRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\FrontendRestrictionContainer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DatabaseUtility
{
    /**
     * Is the user in a workspace ?
     *
     * @return    bool        ture if the user is an a workspace
     */
    public static function isWorkspaceActive()
    {
        return !empty($GLOBALS['BE_USER']->workspace) || !empty($GLOBALS['TSFE']->sys_page->versioningPreview);
    }

    /**
     * Get content elements from tt_content table
     *
     * @param int $colPos
     * @param int $pid
     * @param int $mulitColumnParentId
     * @param int $sysLanguageUid
     * @param bool $showHidden
     * @param string $additionalWhere
     * @param PageLayoutView $cmsLayout
     *
     * @return array Array with database fields
     */
    public static function getContentElementsFromContainer($colPos, $pid, $mulitColumnParentId, $sysLanguageUid = 0, $showHidden = false, $additionalWhere = null, $cmsLayout = null)
    {
        $isWorkspace = self::isWorkspaceActive();

        $selectFields = '*';
        $fromTable = 'tt_content';

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        if ($isWorkspace) {
            $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(BackendWorkspaceRestriction::class));
        }
        $queryBuilder->select($selectFields)
            ->from($fromTable)
            ->where(
                $queryBuilder->expr()->eq('tx_multicolumn_parentid', (int)$mulitColumnParentId),
                $queryBuilder->expr()->eq('sys_language_uid', (int)$sysLanguageUid)
            )
            ->andWhere(self::enableFields($fromTable, $showHidden))
            ->orderBy('sorting');
        if (!empty($additionalWhere)) {
            $queryBuilder->andWhere($additionalWhere);
        }
        if ($colPos) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('colPos', (int)$colPos));
        }
        if ($pid && !$isWorkspace) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('pid', (int)$pid));
        }
        $res = $queryBuilder->execute();

        return $cmsLayout === null ? $res->fetchAll() : $cmsLayout->getResult($res, 'tt_content');
    }

    /**
     * Get number of content elements inside a multicolumn container
     *
     * @param int $mulitColumnId
     *
     * @return int
     */
    public static function getNumberOfContentElementsFromContainer($mulitColumnId)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder->count('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'tx_multicolumn_parentId',
                    $queryBuilder->createNamedParameter($mulitColumnId, \PDO::PARAM_INT)
                )
            )
            ->andWhere(self::enableFields('tt_content'))
            ->execute()
            ->fetchColumn(0);
    }

    /**
     * Get number of columns in the container
     *
     * @param int $mulitColumnId
     * @param array $row
     * @return int
     */
    public static function getNumberOfColumnsFromContainer($mulitColumnId, array $row = null)
    {
        $result = 0;
        if ($row === null || !isset($row['pi_flexform'])) {
            $row = self::getContentElement($mulitColumnId);
        }
        if (!empty($row['pi_flexform'])) {
            $flexObj = GeneralUtility::makeInstance(FlexFormUtility::class, $row['pi_flexform']);
            $layoutConfiguration = MulticolumnUtility::getLayoutConfiguration($row['pid'], $flexObj);
            $result = (int)$layoutConfiguration['columns'];
        }

        return $result;
    }

    /**
     * Get a single content element
     *
     * @param int $uid
     * @param string $selectFields
     * @param string $additionalWhere
     * @param bool $useDeleteClause
     *
     * @return array Element fields
     */
    public static function getContentElement($uid, $selectFields = '*', $additionalWhere = null, $useDeleteClause = true)
    {
        if (TYPO3_MODE == 'BE') {
            return \TYPO3\CMS\Backend\Utility\BackendUtility::getRecordWSOL('tt_content', $uid, $selectFields, $additionalWhere, $useDeleteClause);
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->setRestrictions(GeneralUtility::makeInstance(FrontendRestrictionContainer::class));
        $queryBuilder->select(...GeneralUtility::trimExplode(',', $selectFields, true))
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            )
            ->setMaxResults(1);
        if ($additionalWhere) {
            $queryBuilder->andWhere($additionalWhere);
        }

        return $queryBuilder->execute()
            ->fetchAll();
    }

    /**
     * Get multicolumn content elements from page uid
     *
     * @param int $pid
     * @param int $sysLanguageUid
     * @param int $selectFields
     *
     * @return array
     */
    public static function getContainersFromPid($pid, $sysLanguageUid = 0, $selectFields = 'uid,header')
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder->select(...GeneralUtility::trimExplode(',', $selectFields, true))
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'CType',
                    $queryBuilder->createNamedParameter('multicolumn', \PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->eq(
                    'sys_language_uid',
                    $queryBuilder->createNamedParameter($sysLanguageUid, \PDO::PARAM_INT)
                )
            )
            ->andWhere(self::enableFields('tt_content'))
            ->orderBy('sorting')
            ->execute()
            ->fetchAll();
    }

    /**
     * Get multicolumn content element from uid
     *
     * @param int $uid
     * @param string $selectFields
     * @return array|null
     */
    public static function getContainerFromUid($uid, $selectFields = 'uid,header')
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder->select(...GeneralUtility::trimExplode(',', $selectFields, true))
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'CType',
                    $queryBuilder->createNamedParameter('multicolumn', \PDO::PARAM_STR)
                )
            )
            ->andWhere(self::enableFields('tt_content'))
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();
    }

    /**
     * Checks if content element has an parent multicolumn content element
     *
     * @param int $uid
     *
     * @return bool
     */
    public static function contentElementHasAMulticolumnParentContainer($uid)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        $count = $queryBuilder->count('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->neq(
                    'tx_multicolumn_parentid',
                    $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
                )
            )
            ->andWhere(self::enableFields('tt_content'))
            ->execute()
            ->fetchColumn(0);

        return $count > 0;
    }

    /**
     * Updateds a content element
     *
     * @param int $uid
     * @param array $fieldValues
     *
     * @return void
     */
    public static function updateContentElement($uid, array $fieldValues)
    {
        if (empty($fieldValues)) {
            return;
        }

        $updateQueryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $updateQueryBuilder->update('tt_content')
            ->where(
                $updateQueryBuilder->expr()->eq(
                    'uid',
                    $updateQueryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                )
            );

        foreach ($fieldValues as $field => $value) {
            $updateQueryBuilder->set($field, $value);
        }

        $updateQueryBuilder->execute();
    }

    /**
     * Obtains children content elements for the multicolumn container
     *
     * @param int $containerUid
     * @param bool $showHidden
     *
     * @return array
     */
    public static function getContainerChildren($containerUid, $showHidden = true)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder->select('uid', 'pid', 'sys_language_uid', 'CType')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'tx_multicolumn_parentid',
                    $queryBuilder->createNamedParameter($containerUid, \PDO::PARAM_INT)
                )
            )
            ->andWhere(self::enableFields('tt_content', $showHidden))
            ->execute()
            ->fetchAll();
    }

    /**
     * Get enableFields frontend / backend
     *
     * @param string $table table name
     * @param bool $showHidden
     *
     * @return string
     */
    protected static function enableFields($table, $showHidden = false)
    {
        $enableFields = '1=1';
        if (TYPO3_MODE === 'FE') {
            $enableFields .= $GLOBALS['TSFE']->sys_page->enableFields($table, $showHidden);
        }

        return $enableFields;
    }
}
