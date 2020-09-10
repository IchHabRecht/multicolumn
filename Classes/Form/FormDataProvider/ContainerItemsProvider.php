<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Form\FormDataProvider;

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
use IchHabRecht\Multicolumn\Utility\MulticolumnUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class ContainerItemsProvider
{
    /**
     * Current items
     *
     * @var        array
     */
    protected $items = [];

    /**
     * How many items exists?
     *
     * @var        int
     */
    protected $itemsCount = 0;

    /**
     * TCA config of colPos
     *
     * @var        array
     */
    protected $config = [];

    /**
     * Current row
     *
     * @var        array
     */
    protected $row = [];

    /**
     * Locallang array
     *
     * @var        array
     */
    protected $LL = [];

    /**
     * Decide what to to do. Action is defined in TCA $itemsProc['config']['multicolumnProc']
     *
     * @param    array $itemsProc
     * @param \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems $pObj
     * @param    int $pid : Target pid of page
     */
    public function init($itemsProc, \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems $pObj)
    {
        // call proFunc
        if (!empty($itemsProc['config']['itemsProcFunctions'])) {
            foreach ($itemsProc['config']['itemsProcFunctions'] as $procFunc) {
                if (!empty($procFunc)) {
                    \TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($procFunc, $itemsProc, $pObj);
                }
            }
        }

        $this->items = &$itemsProc['items'];
        $this->itemsCount = count($this->items);
        $this->config = $itemsProc['config'];
        $this->row = $itemsProc['row'];
        $this->LL = MulticolumnUtility::includeBeLocalLang($this->config['multicolumnLL']);

        call_user_func([
            self::class,
            $this->config['multicolumnProc'],
        ]);
    }

    /**
     * Builds a list of all multicolumn container of current pid to use in itemsProc[items]
     */
    protected function buildMulticolumnList()
    {
        if ($this->row['pid'] > 0) {
            $pid = $this->row['pid'];
        } else {
            $record = BackendUtility::getRecord('tt_content', abs($this->row['pid']), 'pid');
            $pid = $record['pid'] ?? 0;
        }
        if ($containers = DatabaseUtility::getContainersFromPid($pid, $this->row['sys_language_uid'][0])) {
            if ($this->items) {
                $itemsUidList = $this->getItemsUidList();
            }

            $multicolumnContainerItem = 1;
            foreach ($containers as $container) {
                // do not list current container
                if ($this->row['uid'] == $container['uid']) {
                    continue;
                }

                if (!\TYPO3\CMS\Core\Utility\GeneralUtility::inList($itemsUidList, $container['uid'])) {
                    $title = $container['header'] ? $container['header'] : $GLOBALS['LANG']->getLLL('pi1_title', $this->LL) . ' ' . $multicolumnContainerItem . ' (uid: ' . $container['uid'] . ')';
                    $this->items[] = [
                        0 => $title,
                        1 => $container['uid'],
                        2 => null,
                    ];
                    $multicolumnContainerItem++;
                }
            }
        }
    }

    /**
     * Get all uids of $itemsProc['items']
     */
    protected function getItemsUidList()
    {
        $itemsUidList = null;
        $comma = null;

        foreach ($this->items as $item) {
            if ($item[1]) {
                $itemsUidList = $comma . $item[1];
                $comma = ',';
            }
        }

        return $itemsUidList;
    }

    /**
     * Add dynamic colPos to content element if its inside a multicolumn container
     */
    protected function buildDynamicCols()
    {
        if (!$this->row['tx_multicolumn_parentid']) {
            return;
        }

        $numberOfColumns = DatabaseUtility::getNumberOfColumnsFromContainer($this->row['tx_multicolumn_parentid']);

        $columnIndex = 0;
        $columnTitle = $GLOBALS['LANG']->getLLL('multicolumColumn', $this->LL) . ' ' . $GLOBALS['LANG']->getLLL('cms_layout.columnTitle', $this->LL);

        while ($columnIndex < $numberOfColumns) {
            $this->items[] = [
                0 => $columnTitle . ' ' . ($columnIndex + 1),
                1 => MulticolumnUtility::colPosStart + $columnIndex,
                2 => null,
            ];

            $columnIndex++;
        }
    }
}
