<?php
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

class PageLayoutViewHook
{
    /**
     * Expands the delete warning with "(This multicolumn container has X content elements(s)...)
     * before you delete a records
     * @param array $params
     */
    public function addDeleteWarning(array $params)
    {
        if (!$params[0] == 'tt_content') {
            return;
        }

        // adjust delete warning
        if ($params['2']['CType'] == 'multicolumn') {
            $numberOfContentElements = tx_multicolumn_db::getNumberOfContentElementsFromContainer($params['2']['uid']);

            $LL = tx_multicolumn_div::includeBeLocalLang();

            // no children found? return!
            if (!$numberOfContentElements) {
                $this->restoreOrginalDeleteWarning($LL);

                return;
            }

            $llGlobal = &$GLOBALS['LOCAL_LANG'];

            // add multicolumn delete warning
            foreach ($LL as $llKey => $ll) {
                $deleteWarningOrginal = isset($llGlobal[$llKey]['deleteWarningOrginal']) ? $llGlobal[$llKey]['deleteWarningOrginal'] : $llGlobal[$llKey]['deleteWarning'];

                $cmsLayoutDeleteWarning = $ll['cms_layout.deleteWarning'];
                if (is_array($cmsLayoutDeleteWarning)) {
                    $cmsLayoutDeleteWarning = $cmsLayoutDeleteWarning[0]['target'];
                }
                $deleteWarningMulticolumn = str_replace('%s', $numberOfContentElements, $cmsLayoutDeleteWarning);
                $deleteWarning = isset($llGlobal[$llKey]['deleteWarningOrginal']) ? $llGlobal[$llKey]['deleteWarningOrginal'] : $llGlobal[$llKey]['deleteWarning'];

                if (is_array($llGlobal[$llKey]['deleteWarning'])) {
                    foreach ($llGlobal[$llKey]['deleteWarning'] as &$llValue) {
                        $llValue = $deleteWarning[0];
                    }
                } else {
                    $llGlobal[$llKey]['deleteWarningOrginal'] = isset($llGlobal[$llKey]['deleteWarningOrginal']) ? $llGlobal[$llKey]['deleteWarningOrginal'] : $llGlobal[$llKey]['deleteWarning'];
                }

                if (is_array($llGlobal[$llKey]['deleteWarning'])) {
                    foreach ($llGlobal[$llKey]['deleteWarning'] as &$llValue) {
                        $llValue['source'] = $deleteWarningOrginal[0]['source'] . chr(10) . $deleteWarningMulticolumn;
                        $llValue['target'] = $deleteWarningOrginal[0]['target'] . chr(10) . $deleteWarningMulticolumn;
                    }
                } else {
                    $llGlobal[$llKey]['deleteWarning'] = $deleteWarningOrginal . chr(10) . $deleteWarningMulticolumn;
                }
            }

            unset($llGlobal);
        }
    }

    protected function restoreOrginalDeleteWarning(array $LL)
    {
        foreach ($LL as $llKey => $ll) {
            if ($GLOBALS['LOCAL_LANG'][$llKey]['deleteWarningOrginal']) {
                $GLOBALS['LOCAL_LANG'][$llKey]['deleteWarning'] = $GLOBALS['LOCAL_LANG'][$llKey]['deleteWarningOrginal'];
            }
        }
    }
}
