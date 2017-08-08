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

class tx_multicolumn_tcemainCopyTest extends tx_multicolumn_tcemainBaseTest
{
    /**
     * Copy a multicolumn container to another column
     *
     * @test
     */
    public function copyContainerAndChildrenToOtherColumnInDefaultLanguage()
    {
        $cmdMap = [
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => [
                1 => [
                    'copy' => [
                        'action' => 'paste',
                        'target' => 1,
                        'update' => [
                            'colPos' => 2,
                            'sys_language_uid' => 0,
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
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count);
    }

    /**
     * Copy a multicolumn container to another page
     *
     * @test
     */
    public function copyContainerAndChildrenToOtherPageInDefaultLanguage()
    {
        $cmdMap = [
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => [
                1 => [
                    'copy' => [
                        'action' => 'paste',
                        'target' => 2,
                        'update' => [
                            'colPos' => 0,
                            'sys_language_uid' => 0,
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
            . ' AND pid=2'
            . ' AND deleted=0'
            . ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=0'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count);

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
            'uid=' . $childUid
            . ' AND pid=2'
            . ' AND deleted=0'
            . ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_TEXTPIC . '\''
            . ' AND colPos=10'
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count);
    }

    /**
     * Copy a multicolumn child record to another column
     *
     * @test
     */
    public function copyChildFromContainerToOtherColumnInDefaultLanguage()
    {
        $cmdMap = [
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => [
                2 => [
                    'copy' => [
                        'action' => 'paste',
                        'target' => '1',
                        'update' => [
                            'colPos' => 2,
                            'sys_language_uid' => 0,
                        ],
                    ],
                ],
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start([], $cmdMap);
        $dataHandler->process_cmdmap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $copiedUID = $dataHandler->copyMappingArray[tx_multicolumn_tcemainBaseTest::CONTENT_TABLE][2];

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
            'uid=' . $copiedUID
            . ' AND deleted=0'
            . ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_TEXTPIC . '\''
            . ' AND colPos=2'
            . ' AND tx_multicolumn_parentid=0'
            . ' AND sys_language_uid=0'
            . ' AND pid=1'
        );
        $this->assertSame(1, $count);
    }

    /**
     * Copy a record into a multicolumn container after another record
     *
     * @test
     */
    public function copyRecordIntoContainerAfterRecordInDefaultLanguage()
    {
        $cmdMap = [
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => [
                3 => [
                    'copy' => -2,
                ],
            ],
        ];
        $dataHandler = new DataHandler();
        $dataHandler->start([], $cmdMap);
        $dataHandler->process_cmdmap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
            'pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_TEXTPIC . '\''
            . ' AND colPos=10'
            . ' AND sys_language_uid=0'
            . ' AND tx_multicolumn_parentid=1'
        );
        $this->assertSame(2, $count);
    }

    /**
     * Copy a multicolumn container to another multicolumn container
     *
     * @test
     */
    public function copyContainerAndChildrenToOtherContainerInDefaultLanguage()
    {
        $cmdMap = [
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => [
                1 => [
                    'copy' => -2,
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
            . ' AND colPos=10'
            . ' AND tx_multicolumn_parentid=1'
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
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count);
    }
}
