<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Nicole Cordes <cordes@cps-it.de>
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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

require_once __DIR__ . '/../../../FunctionalBaseTest.php';

use TYPO3\CMS\Backend\ClickMenu\ClickMenu;
use TYPO3\CMS\Backend\Clipboard\Clipboard;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class tx_multicolumn_alt_clickmenuTest extends tx_multicolumn_tcemainBaseTest
{
    /**
     * Ensure multicolumn containers in clickmenu items with record in clipboard
     *
     * @test
     */
    public function findMulticolumnColumnsInClickmenuInDefaultLanguage()
    {
        $table = tx_multicolumn_tcemainBaseTest::CONTENT_TABLE;
        $uid = 10;

        $clipObj = new Clipboard();
        $clipObj->clipData = [
            'current' => 'normal',
            'normal' => [
                'el' => [
                    $table . '|3' => 1,
                ],
            ],
        ];
        $clipObj->lockToNormal();

        $clickMenu = new ClickMenu();
        $clickMenu->clipObj = $clipObj;
        $clickMenu->extClassArray = $GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'];
        $clickMenu->rec = BackendUtility::getRecordWSOL($table, $uid);
        $clickMenuContent = $clickMenu->printDBClickMenu($table, $uid);

        $pasteUrl = $clipObj->pasteUrl('tt_content', -$uid, 0);

        $this->assertContains(trim(GeneralUtility::quoteJSvalue($pasteUrl . '&tx_multicolumn[action]=pasteInto&colPos=10&tx_multicolumn_parentid=1'), '\''), $clickMenuContent);
        $this->assertContains(trim(GeneralUtility::quoteJSvalue($pasteUrl . '&tx_multicolumn[action]=pasteInto&colPos=11&tx_multicolumn_parentid=1'), '\''), $clickMenuContent);
        $this->assertContains(trim(GeneralUtility::quoteJSvalue($pasteUrl . '&tx_multicolumn[action]=pasteInto&colPos=12&tx_multicolumn_parentid=1'), '\''), $clickMenuContent);
    }

    /**
     * Paste a record into a multicolumn container
     *
     * @test
     */
    public function pasteRecordIntoContainerInDefaultLanguage()
    {
        $cmdMap = [
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => [
                3 => [
                    'move' => -10,
                ],
            ],
        ];

        $_GET = [
            'colPos' => 12,
            'tx_multicolumn' => [
                'action' => 'pasteInto',
            ],
            'tx_multicolumn_parentid' => 10,
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start([], $cmdMap);
        $dataHandler->process_cmdmap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
            'uid=3'
            . ' AND pid=1'
            . ' AND colPos=12'
            . ' AND tx_multicolumn_parentid=10'
        );
        $this->assertSame(1, $count);
    }
}
