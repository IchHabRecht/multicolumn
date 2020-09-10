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

use TYPO3\CMS\Backend\Wizard\NewContentElementWizardHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class WizardItemsHook implements NewContentElementWizardHookInterface
{
    /**
     * modifies WizardItems array
     *
     * @param array $wizardItems array of Wizard Items
     * @param \TYPO3\CMS\Backend\Controller\ContentElement\NewContentElementController $parentObject New Content element wizard
     *
     * @return    void
     */
    public function manipulateWizardItems(&$wizardItems, &$parentObject)
    {
        $multiColumnParentId = (int)GeneralUtility::_GP('tx_multicolumn_parentid');
        if (empty($multiColumnParentId)) {
            return;
        }

        foreach ($wizardItems as $key => &$wizardItem) {
            if (strpos($key, '_') === false) {
                continue;
            }
            if (empty($wizardItem['tt_content_defValues'])) {
                $wizardItem['tt_content_defValues'] = [];
            }
            $wizardItem['tt_content_defValues']['tx_multicolumn_parentid'] = $multiColumnParentId;
            $wizardItem['params'] = GeneralUtility::implodeArrayForUrl('defVals[tt_content]', $wizardItem['tt_content_defValues']);
        }
    }
}
