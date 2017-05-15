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

require_once __DIR__ . '/../../../FunctionalBaseTest.php';

use TYPO3\CMS\Backend\ClickMenu\ClickMenu;
use TYPO3\CMS\Backend\Clipboard\Clipboard;
use TYPO3\CMS\Backend\ContextMenu\ContextMenu;
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

        if (!class_exists(ClickMenu::class)) {
            $expectedSubset = [
                'multicolumn-pasteinto-0' => [
                    'additionalAttributes' => [
                        'data-callback-module' => 'TYPO3/CMS/Multicolumn/ContextMenuActions',
                        'data-colpos' => 10,
                    ],
                    'callbackAction' => 'pasteIntoColumn',
                    'label' => 'Paste into column 1',
                    'type' => 'item',
                ],
                'multicolumn-pasteinto-1' => [
                    'additionalAttributes' => [
                        'data-callback-module' => 'TYPO3/CMS/Multicolumn/ContextMenuActions',
                        'data-colpos' => 11,
                    ],
                    'callbackAction' => 'pasteIntoColumn',
                    'label' => 'Paste into column 2',
                    'type' => 'item',
                ],
                'multicolumn-pasteinto-2' => [
                    'additionalAttributes' => [
                        'data-callback-module' => 'TYPO3/CMS/Multicolumn/ContextMenuActions',
                        'data-colpos' => 12,
                    ],
                    'callbackAction' => 'pasteIntoColumn',
                    'label' => 'Paste into column 3',
                    'type' => 'item',
                ],
            ];

            $GLOBALS['BE_USER']->pushModuleData(
                'clipboard',
                [
                    'normal' => [
                        'el' => [
                            $table . '|3' => 1,
                        ],
                    ],
                ]
            );
            $contextMenu = new ContextMenu();

            $this->assertArraySubset($expectedSubset, $contextMenu->getItems($table, $uid, ''));
        } else {
            $clipboardObject = new Clipboard();
            $clipboardObject->clipData = [
                'current' => 'normal',
                'normal' => [
                    'el' => [
                        $table . '|3' => 1,
                    ],
                ],
            ];
            $clipboardObject->lockToNormal();

            $clickMenu = new ClickMenu();
            $clickMenu->clipObj = $clipboardObject;
            $clickMenu->extClassArray = $GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'];
            $clickMenu->rec = BackendUtility::getRecordWSOL($table, $uid);
            $clickMenuContent = $clickMenu->printDBClickMenu($table, $uid);

            $pasteUrl = $clipboardObject->pasteUrl('tt_content', -$uid, 0);

            $this->assertContains(trim(GeneralUtility::quoteJSvalue($pasteUrl . '&tx_multicolumn[action]=pasteInto&colPos=10&tx_multicolumn_parentid=1'), '\''), $clickMenuContent);
            $this->assertContains(trim(GeneralUtility::quoteJSvalue($pasteUrl . '&tx_multicolumn[action]=pasteInto&colPos=11&tx_multicolumn_parentid=1'), '\''), $clickMenuContent);
            $this->assertContains(trim(GeneralUtility::quoteJSvalue($pasteUrl . '&tx_multicolumn[action]=pasteInto&colPos=12&tx_multicolumn_parentid=1'), '\''), $clickMenuContent);
        }
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

        $count = $this->getDatabaseConnection()->selectCount(
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
