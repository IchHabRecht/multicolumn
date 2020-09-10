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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class FlexFormUtility
{
    /**
     * Flexform configuration
     *
     * @var array
     */
    protected $flex = [];

    public function __construct($flexformString = null)
    {
        if ($flexformString === null || empty($flexformString)) {
            return;
        }
        if (is_array($flexformString)) {
            $this->flex = $flexformString;
        } else {
            $this->flex = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($flexformString);
        }
    }

    /**
     * Returns the value of flexform setting
     *
     * @param string $sheet Name of sheet
     * @param string $key Name of flexform key
     *
     * @return    mixed        Flex value (typical a string)
     *
     * */
    public function getFlexValue($sheet, $key)
    {
        if (is_array($this->flex['data'])) {
            return $this->flex['data'][$sheet]['lDEF'][$key]['vDEF'];
        }
    }

    /**
     * Returns the flexform array
     *
     * @param    string        Key of column if none whole array is returned
     * @param string|null $key
     *
     * @return    array        Flexform array
     *
     * */
    public function getFlexArray($key = null)
    {
        $flexform = [];

        if (is_array($this->flex['data'])) {
            if ($key && $this->flex['data'][$key]['lDEF']) {
                foreach ($this->flex['data'][$key]['lDEF'] as $flexKey => $value) {
                    if ($value['vDEF']) {
                        $flexform[$flexKey] = $value['vDEF'];
                    }
                }
            } else {
                $flexform = $this->flex['data'];
            }
        }

        return $flexform;
    }

    /**
     * Generates the icons for the flexform selector layout
     *
     * @param array $params Array with current record and empty items array
     * */
    public function addFieldsToFlexForm(&$params)
    {
        $type = $params['config']['txMulitcolumnField'];
        if (isset($params['flexParentDatabaseRow'])) {
            $pid = $params['flexParentDatabaseRow']['pid'];
        } elseif (MathUtility::canBeInterpretedAsInteger($params['row']['uid'])) {
            $currentRecord = BackendUtility::getRecord('tt_content', (int)$params['row']['uid'], 'pid');
            $pid = $currentRecord['pid'];
        } elseif (!empty($_GET['edit']['tt_content'])) {
            $uidArray = array_keys($_GET['edit']['tt_content']);
            $uid = array_shift($uidArray);
            if ($uid > 0) {
                $pid = $uid;
            } else {
                $currentRecord = BackendUtility::getRecord('tt_content', abs($uid), 'pid');
                $pid = $currentRecord['pid'];
            }
        } else {
            $pid = 0;
        }
        $tsConfig = MulticolumnUtility::getTSConfig($pid, null);

        switch ($type) {
            case 'preSetLayout':
                if (is_array($tsConfig['layoutPreset.'])) {
                    // enable only specific effects
                    if (!empty($tsConfig['config.']['layoutPreset.']['enableLayouts'])) {
                        $this->filterItems($tsConfig['layoutPreset.'], $tsConfig['config.']['layoutPreset.']['enableLayouts']);
                    }
                    $this->buildItems($tsConfig['layoutPreset.'], $params);
                }
                break;
        }
    }

    protected function buildItems(array $config, &$params)
    {
        foreach ($config as $key => $item) {
            $params['items'][] = [
                $GLOBALS['LANG']->sL($item['label']),
                $key,
                $item['icon'],
            ];
        }
    }

    /**
     * Filter out items from an array
     *
     * @param array $items
     * @param string $filterList comma seperated list
     *
     * */
    protected function filterItems(array &$items, $filterList)
    {
        foreach ($items as $itemKey => $item) {
            if (!\TYPO3\CMS\Core\Utility\GeneralUtility::inList($filterList, str_replace('.', null, $itemKey))) {
                unset($items[$itemKey]);
            }
        }
    }
}
