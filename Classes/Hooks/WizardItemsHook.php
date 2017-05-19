<?php
namespace CPSIT\Multicolumn\Hooks;

use TYPO3\CMS\Backend\Wizard\NewContentElementWizardHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class WizardItemsHook implements NewContentElementWizardHookInterface
{
    /**
     * @var int
     */
    protected $multiColumnParentId;

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
        $this->multiColumnParentId = (int)GeneralUtility::_GP('tx_multicolumn_parentid');
        if ($this->multiColumnParentId > 0) {
            $this->addMulticolumnParentId($wizardItems);
        }
    }

    /**
     * add mulitcolumn parentid to wizard params
     *
     * @param    array                    array of Wizard Items
     *
     * @return    void
     */
    protected function addMulticolumnParentId(array &$wizardItems)
    {
        foreach ($wizardItems as &$wizardItem) {
            if ($wizardItem['params']) {
                //add mulitcolumn parent id
                $wizardItem['params'] .= '&defVals[tt_content][tx_multicolumn_parentid]=' . $this->multiColumnParentId;
            }
        }
    }
}
