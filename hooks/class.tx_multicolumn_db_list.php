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

class tx_multicolumn_db_list
{
    public function makeQueryArray_post(&$queryParts)
    {
        //is colPos greater than 9 >
        if (!empty($queryParts['WHERE']) && ($queryParts['FROM'] === 'tt_content') && (preg_match('/colPos=(1[0-9])/', $queryParts['WHERE']))) {
            $queryParts['WHERE'] .= ' AND tx_multicolumn_parentid = 0';
        }
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cms/classes/class.tx_cms_backendlayout.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cms/classes/class.tx_cms_backendlayout.php']);
}
