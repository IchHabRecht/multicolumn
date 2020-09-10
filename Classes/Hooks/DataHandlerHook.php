<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Hooks;

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

use IchHabRecht\Multicolumn\Utility\DatabaseUtility;

class DataHandlerHook
{
    /** @var \TYPO3\CMS\Core\DataHandling\DataHandler */
    protected $pObj;

    /**
     * - Copy children of a multicolumn container
     * - Delete children of a multicolumn container
     * - Check if a seedy releation to a multicolumn container exits
     * - Check if pasteinto multicolumn container is requested
     *
     * @param string $command
     * @param string $table
     * @param string $id
     * @param int $value
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     * @param array $pasteUpdate
     * @param array $pasteDatamap
     *
     * @return void
     */
    public function processCmdmap_postProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj, $pasteUpdate, $pasteDatamap)
    {
        if ($table !== 'tt_content') {
            return;
        }
        $this->pObj = $pObj;

        $copyId = (int)($this->pObj->copyMappingArray[$table][$id] ?? $id);

        $targetPid = (int)($this->pObj->cmdmap['tt_content'][$id][$command]['target'] ?? $this->pObj->cmdmap['tt_content'][$id][$command] ?? 0);
        $targetElement = DatabaseUtility::getContentElement(abs($targetPid), 'uid,pid,CType,tx_multicolumn_parentid,colPos');

        // if pasteinto multicolumn container is requested?
        if ($targetPid < 0
            && ($targetElement['CType'] ?? '') === 'multicolumn'
            && ($targetElement['uid'] ?? 0) !== $id
        ) {
            $this->pObj->moveRecord_raw('tt_content', $copyId, $targetElement['pid'] ?? $targetPid);
            $this->pasteIntoMulticolumnContainer($command, $copyId, $id, $targetElement);
        } else {
            $containerChildren = DatabaseUtility::getContainerChildren($id);

            switch ($command) {
                case 'copy':
                    // copy children of a multicolumn container too
                    if ($containerChildren) {
                        if (($this->pObj->cmdmap[$table][$id][$command]['action'] ?? '') !== 'paste'
                            || $targetPid < 0
                        ) {
                            $record = $this->pObj->recordInfo($table, $copyId, 'pid');
                            $targetPid = $record['pid'];
                        }

                        if (isset($pasteUpdate['sys_language_uid'])) {
                            $sysLanguageUid = $pasteUpdate['sys_language_uid'];
                        } elseif (isset($pasteDatamap[$table][$copyId]['sys_language_uid'])) {
                            $sysLanguageUid = $pasteDatamap[$table][$copyId]['sys_language_uid'];
                        } else {
                            $contentElement = DatabaseUtility::getContentElement($copyId, 'sys_language_uid');
                            $sysLanguageUid = $contentElement['sys_language_uid'];
                        }

                        $this->copyMulticolumnContainer($id, $containerChildren, $targetPid, $sysLanguageUid);
                    } else {
                        // check if content element has a seedy relation to multicolumncontainer?
                        $row = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecordWSOL('tt_content', $copyId);

                        if (is_array($row)) {
                            $colPos = $targetElement['colPos'] ?? 0;
                            $multicolumnParentId = $targetElement['tx_multicolumn_parentid'] ?? 0;

                            if ($row['tx_multicolumn_parentid'] || $multicolumnParentId) {
                                // Update column position if:
                                // (1) was in the multicolumn before
                                //    or
                                // (2) copied after the element in the multicolumn
                                $updateRecordFields = [
                                    'tx_multicolumn_parentid' => $multicolumnParentId,
                                    'colPos' => $colPos,
                                ];
                                DatabaseUtility::updateContentElement($copyId, $updateRecordFields);
                            }
                        }
                    }
                    break;
                case 'delete':
                    // delete children too
                    if ($containerChildren) {
                        $this->deleteMulticolumnContainer($containerChildren);
                    }
                    break;
                case 'localize':
                    if ($containerChildren) {
                        $this->localizeMulticolumnChildren($containerChildren, $copyId, $value);
                    }

                    // reset remap stack record for multicolumn item (prevents double call of processDatamap_afterDatabaseOperations)
                    unset($this->pObj->remapStackRecords['tt_content'][$id]);
                    break;
            }
        }
    }

    /**
     * Paste an element into multicolumn container
     *
     * @param string $action : copy or move
     * @param int $updateId : content element to update
     * @param int $orginalId : orginal id of content element (copy from)
     * @param array $targetElement
     */
    protected function pasteIntoMulticolumnContainer($action, $updateId, $orginalId = null, array $targetElement = [])
    {
        $multicolumnId = (int)($targetElement['tx_multicolumn_parentid'] ?? 0) ?: (int)($targetElement['uid'] ?? 0);

        // stop if someone is trying to cut the multicolumn container inside the container
        if ($multicolumnId === $updateId) {
            return;
        }

        $updateRecordFields = [
            'colPos' => (int)($this->pObj->cmdmap['tt_content'][$orginalId][$action]['update']['colPos'] ?? $this->pObj->datamap['tt_content'][$orginalId]['colPos'] ?? 0),
            'tx_multicolumn_parentid' => $multicolumnId,
        ];

        DatabaseUtility::updateContentElement($updateId, $updateRecordFields);

        $containerChildren = DatabaseUtility::getContainerChildren($orginalId);
        // copy children too
        if ($containerChildren) {
            $pid = $this->pObj->pageCache ? key($this->pObj->pageCache) : key($this->pObj->cachedTSconfig);

            // copy or move
            ($action == 'copy') ? $this->copyMulticolumnContainer($updateId, $containerChildren, $pid) : $this->moveContainerChildren($containerChildren, $pid);
        }
    }

    /**
     * Delete multicolumn container with children elements (recursive)
     *
     * @param array $containerChildren Content elements of multicolumn container
     *
     * @return void
     */
    protected function deleteMulticolumnContainer(array $containerChildren)
    {
        foreach ($containerChildren as $child) {
            $this->pObj->deleteRecord('tt_content', $child['uid']);

            // if is element a multicolumn element ? delete children too (recursive)
            if ($child['CType'] == 'multicolumn') {
                $containerChildrenChildren = DatabaseUtility::getContainerChildren($child['uid']);
                if ($containerChildrenChildren) {
                    $this->deleteMulticolumnContainer($containerChildrenChildren);
                }
            }
        }
    }

    /**
     * Localize multicolumn children
     *
     * @param array $elementsToBeLocalized
     * @param int $multicolumnParentId
     * @param int $sysLanguageUid
     *
     * @return void
     */
    protected function localizeMulticolumnChildren(array $elementsToBeLocalized, $multicolumnParentId, $sysLanguageUid)
    {
        foreach ($elementsToBeLocalized as $element) {
            //create localization
            $newUid = $this->pObj->localize('tt_content', $element['uid'], $sysLanguageUid);
            if ($newUid) {
                $fields_values = [
                    'tx_multicolumn_parentid' => $multicolumnParentId,
                ];

                DatabaseUtility::updateContentElement($newUid, $fields_values);

                // if is element a multicolumn element ? localize children too (recursive)
                if ($element['CType'] == 'multicolumn') {
                    $containerChildrenChildren = DatabaseUtility::getContainerChildren($element['uid']);
                    if (!empty($containerChildrenChildren)) {
                        $this->localizeMulticolumnChildren($containerChildrenChildren, $newUid, $sysLanguageUid);
                    }
                }
            }
        }
    }

    /**
     * If an elements get copied outside from a multicontainer inside a multicolumncontainer add multicolumn parent id
     * to content element
     *
     * @param string $status
     * @param string $table
     * @param mixed $id
     * @param array $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     *
     * @return void
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj)
    {
        if ($status == 'new' && $table == 'tt_content' && $fieldArray['CType'] == 'multicolumn') {
            $this->pObj = $pObj;

            // get multicolumn status of element before?
            $fieldArray = $this->checkIfElementGetsCopiedOrMovedInsideOrOutsideAMulticolumnContainer($this->pObj->checkValue_currentRecord['pid'], $fieldArray);
        }
    }

    /**
     * If an element gets moved outside from a multicontainer inside to a multicolumncontainer
     * add tx_multicolumn_parentid to moved record
     *
     * @param string $table
     * @param int $uid
     * @param int $destPid
     * @param int $origDestPid
     * @param array $moveRec
     * @param array $updateFields
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     *
     * @return void
     */
    public function moveRecord_afterAnotherElementPostProcess($table, $uid, $destPid, $origDestPid, $moveRec, $updateFields, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj)
    {
        // check if we must update the move record
        if ($table == 'tt_content' && ($this->isMulticolumnContainer($uid) || DatabaseUtility::contentElementHasAMulticolumnParentContainer($uid) || (($origDestPid < 0) && DatabaseUtility::contentElementHasAMulticolumnParentContainer(abs($origDestPid))))) {
            $updateRecordFields = [];
            $updateRecordFields = $this->checkIfElementGetsCopiedOrMovedInsideOrOutsideAMulticolumnContainer($origDestPid, $updateRecordFields);

            DatabaseUtility::updateContentElement($uid, $updateRecordFields);

            // check language
            if ($origDestPid < 0) {
                $recordBeforeUid = abs($origDestPid);

                $row = DatabaseUtility::getContentElement($recordBeforeUid, 'sys_language_uid');
                $sysLanguageUid = $row['sys_language_uid'];

                $containerChildren = DatabaseUtility::getContainerChildren($uid);
                if (is_array($containerChildren)) {
                    $firstElement = $containerChildren[0];
                    // update only if destination has a diffrent langauge
                    if (!($firstElement['sys_language_uid'] == $sysLanguageUid)) {
                        $this->updateLanguage($containerChildren, $sysLanguageUid);
                    }
                }
            }

            // update children (only if container is moved to a new page)
            if ($moveRec['pid'] != $destPid) {
                $this->checkIfContainerHasChilds($table, $uid, $destPid, $pObj);
            }
        }
    }

    /**
     * If an element gets moved - move child records from multicolumn container too
     *
     * @param string $table
     * @param int $uid The record uid currently processing
     * @param int $destPid The page id of the moved record
     * @param array $moveRec Record to move
     * @param array $updateFields Updated fields
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     *
     * @return void
     */
    public function moveRecord_firstElementPostProcess($table, $uid, $destPid, array $moveRec, array $updateFields, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj)
    {
        if ($table !== 'tt_content') {
            return;
        }

        if ($this->isMulticolumnContainer($uid)) {
            $this->checkIfContainerHasChilds($table, $uid, $destPid, $pObj);
        } elseif (
            $destPid > 0
            && !empty($pObj->cmdmap['tt_content'][$uid])
            && DatabaseUtility::contentElementHasAMulticolumnParentContainer($uid)
        ) {
            $updateRecordFields = [
                'tx_multicolumn_parentid' => 0,
            ];
            DatabaseUtility::updateContentElement($uid, $updateRecordFields);
        }
    }

    /**
     * If an element gets moved - move child records from multicolumn container too
     *
     * @param string $table The table currently processing data for
     * @param int $uid The record uid currently processing
     * @param int $destPid The page id of the moved record
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     *
     * @return void
     */
    protected function checkIfContainerHasChilds($table, $uid, $destPid, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj)
    {
        $this->pObj = $pObj;

        $row = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecordWSOL($table, $uid);
        if ($row['CType'] == 'multicolumn') {
            $containerChildren = DatabaseUtility::getContainerChildren($row['uid']);
            if ($containerChildren) {
                $this->moveContainerChildren($containerChildren, $destPid);
            }
            // if element is moved as first element on page ? set multicolumn_parentid and colPos to 0
        } elseif ($row['tx_multicolumn_parentid']) {
            $multicolumnContainerExists = DatabaseUtility::getContentElement($row['tx_multicolumn_parentid'], 'uid', 'AND pid=' . $row['pid']);
            if (!$multicolumnContainerExists) {
                $updateRecordFields = [
                    'tx_multicolumn_parentid' => 0,
                    'colPos' => 0,
                ];
                DatabaseUtility::updateContentElement($row['uid'], $updateRecordFields);
            }
        }
    }

    /**
     * Move container children (recursive)
     *
     * @param array $containerChildren Children of multicolumn container
     * @param int $destPid : Target pid of page
     */
    protected function moveContainerChildren(array $containerChildren, $destPid)
    {
        foreach ($containerChildren as $child) {
            $this->pObj->moveRecord_raw('tt_content', $child['uid'], $destPid);
        }
    }

    /**
     * Updates the language of container children
     *
     * @param array $containerChildren Children of multicolumn container
     * @param int $sysLanguageUid
     *
     * @return void
     */
    protected function updateLanguage(array $containerChildren, $sysLanguageUid)
    {
        foreach ($containerChildren as $child) {
            $updateRecordFields = [
                'sys_language_uid' => $sysLanguageUid,
            ];
            DatabaseUtility::updateContentElement($child['uid'], $updateRecordFields);
        }
    }

    /**
     * Set new multicolumn container id for content elements and copies children of multicolumn container (recursive)
     *
     * @param int $id new multicolumn element id
     * @param array $elementsToCopy Content element array with uid, and pid
     * @param int $pid Target pid of page
     * @param int $sysLanguageUid
     *
     * @return void
     */
    protected function copyMulticolumnContainer($id, array $elementsToCopy, $pid, $sysLanguageUid = 0)
    {
        $overrideValues = [
            'tx_multicolumn_parentid' => $id,
            'sys_language_uid' => $sysLanguageUid,
        ];

        foreach ($elementsToCopy as $element) {
            $newUid = $this->pObj->copyRecord_raw('tt_content', $element['uid'], $pid, $overrideValues);

            // if is element a multicolumn element ? copy children too (recursive)
            if ($element['CType'] == 'multicolumn') {
                $containerChildren = DatabaseUtility::getContainerChildren($element['uid']);

                if ($containerChildren) {
                    $copiedMulticolumncontainer = DatabaseUtility::getContentElement($newUid, 'uid,pid');

                    $this->copyMulticolumnContainer($newUid, $containerChildren, $copiedMulticolumncontainer['pid'], $sysLanguageUid);
                }
            }
        }
    }

    /**
     * If an elements get copied outside from a multicontainer inside a multicolumncontainer or inverse
     * add or remove multicolumn parent id to content element
     *
     * @param int $pidToCheck
     * @param array $fieldArray The field array of a record
     *
     * @return array Modified field array
     */
    protected function checkIfElementGetsCopiedOrMovedInsideOrOutsideAMulticolumnContainer($pidToCheck, array &$fieldArray)
    {
        if ($pidToCheck < 0) {
            $elementId = abs($pidToCheck);
            $elementBefore = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord('tt_content', $elementId, 'tx_multicolumn_parentid, colPos');

            if ($elementBefore['tx_multicolumn_parentid']) {
                $fieldArray['tx_multicolumn_parentid'] = $elementBefore['tx_multicolumn_parentid'];
            } else {
                $fieldArray['tx_multicolumn_parentid'] = 0;
            }
            $fieldArray['colPos'] = $elementBefore['colPos'];
        }

        return $fieldArray;
    }

    /**
     * Check uid if is a multicolumn container
     *
     * @param int $uid
     *
     * @return bool
     */
    protected function isMulticolumnContainer($uid)
    {
        return !empty(DatabaseUtility::getContainerFromUid($uid, 'uid'));
    }
}
