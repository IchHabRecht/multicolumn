<?php

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

class tx_multicolumn_t3lib_befunc
{
    /**
     * Manipulates the flexform output. If effectBox is choosen unset advanced layout tab
     *
     * @param array $dataStructArray Flexform datastruct
     * @param array $conf :    tca
     * @param array $row (reference) The record uid currently processing data for, [integer] or [string] (like 'NEW...')
     * @param string $table
     * @param string $fieldName
     */
    public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, $row, $table, $fieldName)
    {
        if ($table == 'tt_content' && $row['CType'] == 'multicolumn' && is_array($dataStructArray['sheets'])) {
            $flex = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_multicolumn_flexform', $row['pi_flexform']);
            $layout = $flex->getFlexValue('preSetLayout', 'layoutKey');

            if ($layout == 'effectBox.') {
                unset($dataStructArray['sheets']['advancedLayout']);
            } else {
                unset($dataStructArray['sheets']['effectBox']);
            }
        }
    }
}
