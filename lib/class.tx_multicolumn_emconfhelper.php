<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2013 snowflake productions GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper;

/**
 * This class implements a compatibility check for the extension. It will output
 * error messages in EM in case if issues are found.
 */
class tx_multicolumn_emconfhelper
{

    /**
     * Checks if cms layout is xclassed
     *
     * @return string Messages as HTML if something needs to be reported
     */
    public function checkCompatibility()
    {
        $content = '';

        $GLOBALS['LANG']->includeLLFile('EXT:multicolumn/locallang.xml');

        // check templavoila
        if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('templavoila')) {
            $content .= $this->renderFlashMessage(
                $GLOBALS['LANG']->getLL('emconfhelper.templavoila.title'),
                $GLOBALS['LANG']->getLL('emconfhelper.templavoila.message'),
                InfoboxViewHelper::STATE_INFO
            );
        }

        return $content;
    }

    /**
     * Checks if cms layout XCLASS has implemented tx_cms_layout_tt_content_drawItemHook to process
     * mulitcolumn elements
     *
     * @param string $XCLASS Absolute path to XCLASS file
     *
     * @return boolean true if tx_cms_layout_tt_content_drawItemHook exists
     */
    protected function checkIfDrawItemHookExists($XCLASS)
    {
        $drawItemHookExists = true;

        $fileContents = file_get_contents($XCLASS);
        // check if tt_content_drawItem( method exists?
        if (strpos($fileContents, 'tt_content_drawItem(')) {
            // check if tx_cms_layout_tt_content_drawItemHook is implemented
            if (!strpos($fileContents, 'tx_cms_layout_tt_content_drawItemHook')) {
                $drawItemHookExists = false;
            }
        }

        return $drawItemHookExists;
    }

    /**
     * Renders a flash message
     *
     * @param string $title
     * @param string $message
     * @param int $type
     * @return string
     */
    protected function renderFlashMessage($title, $message, $type = InfoboxViewHelper::STATE_WARNING)
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:backend/Resources/Private/Templates/InfoBox.html'));
        $view->assignMultiple([
            'title' => $title,
            'message' => $message,
            'state' => $type
        ]);

        return $view->render();
    }

    /**
     * Builds uninstall link for XCLASS extension
     *
     * @return string Flash message content
     */
    protected function buildUninstallLink($extKey)
    {
        $image = '<img src="uninstall.gif" width="16" height="16" align="top" alt="" />';

        return '<a title="Remove ' . $extKey . '" href="' . htmlspecialchars('index.php?CMD[showExt]=' . $extKey . '&CMD[remove]=1') . '">' . $image . ' ' . $extKey . '</a>';
    }

    /**
     * Filters out ext key from the XCLASS string
     *
     * @return string Extension key from xclass
     */
    protected function getExtKeyByXCLASS($XCLASS)
    {
        $splitedByExtName = preg_split('/ext\//', $XCLASS);
        list($extKey) = preg_split('/\//', $splitedByExtName[1], 2);

        return $extKey;
    }
}

?>