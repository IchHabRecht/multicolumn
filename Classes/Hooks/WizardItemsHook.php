<?php
namespace IchHabRecht\Multicolumn\Hooks;

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
