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

use TYPO3\CMS\Core\DataHandling\DataHandler;

class tx_multicolumn_tcemainTranslationCopyTest extends tx_multicolumn_tcemainBaseTest
{
    /**
     * Copy a multicolumn container to another column
     *
     * @test
     */
    public function copyContainerAndChildrenToOtherColumnInOtherLanguage()
    {
        $cmdMap = [
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => [
                1 => [
                    'copy' => [
                        'action' => 'paste',
                        'target' => 1,
                        'update' => [
                            'colPos' => 2,
                            'sys_language_uid' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start([], $cmdMap);
        $dataHandler->process_cmdmap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $containerUid = $dataHandler->copyMappingArray[tx_multicolumn_tcemainBaseTest::CONTENT_TABLE][1];
        $childUid = $dataHandler->copyMappingArray[tx_multicolumn_tcemainBaseTest::CONTENT_TABLE][2];

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
            'uid=' . $containerUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=2'
            . ' AND sys_language_uid=1'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count);

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
            'uid=' . $childUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_TEXTPIC . '\''
            . ' AND colPos=10'
            . ' AND sys_language_uid=1'
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count);
    }
}
