<?php
namespace IchHabRecht\Multicolumn\Tests\Functional\Hooks\DefaultLanguage;

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

use IchHabRecht\Multicolumn\Tests\Functional\FunctionalBaseTest;
use TYPO3\CMS\Backend\ContextMenu\ContextMenu;
use TYPO3\CMS\Core\DataHandling\DataHandler;

class PasteTest extends FunctionalBaseTest
{
    /**
     * Ensure multicolumn containers in clickmenu items with record in clipboard
     *
     * @test
     */
    public function findMulticolumnColumnsInClickmenuInDefaultLanguage()
    {
        $table = FunctionalBaseTest::CONTENT_TABLE;
        $uid = 10;

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
    }

    /**
     * Paste a record into a multicolumn container
     *
     * @test
     */
    public function pasteRecordIntoContainerInDefaultLanguage()
    {
        $cmdMap = [
            FunctionalBaseTest::CONTENT_TABLE => [
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
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=3'
            . ' AND pid=1'
            . ' AND colPos=12'
            . ' AND tx_multicolumn_parentid=10'
        );
        $this->assertSame(1, $count);
    }
}
