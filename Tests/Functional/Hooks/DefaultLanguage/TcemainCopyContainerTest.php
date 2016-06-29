<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Nicole Cordes <cordes@cps-it.de>
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

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Tests\FunctionalTestCase;

class TcemainCopyContainerTest extends FunctionalTestCase
{
    const CTYPE_MULTICOLUMN = 'multicolumn';
    const TABLE_CONTENT = 'tt_content';

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/multicolumn',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->importDataSet(ORIGINAL_ROOT . 'typo3/sysext/core/Tests/Functional/Fixtures/pages.xml');
        $this->importDataSet(ORIGINAL_ROOT . 'typo3/sysext/core/Tests/Functional/Fixtures/sys_language.xml');
        $this->importDataSet(ORIGINAL_ROOT . 'typo3/sysext/core/Tests/Functional/Fixtures/pages_language_overlay.xml');

        $fixturePath = ORIGINAL_ROOT . 'typo3conf/ext/multicolumn/Tests/Functional/Fixtures/';
        $this->importDataSet($fixturePath . 'tt_content.xml');

        $this->setUpBackendUserFromFixture(1);
        Bootstrap::getInstance()->initializeLanguageObject();
    }

    /**
     * Copy an existing multicolumn container to another column
     *
     * @test
     */
    public function copyContainerAndChildrenToOtherColumnInDefaultLanguage()
    {
        $dataMap = [];
        $cmpMap = [
            self::TABLE_CONTENT => [
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
        $dataHandler->start($dataMap, $cmpMap);
        $dataHandler->process_cmdmap();

        $containerUid = $dataHandler->copyMappingArray[self::TABLE_CONTENT][1];
        $childUid = $dataHandler->copyMappingArray[self::TABLE_CONTENT][2];

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            self::TABLE_CONTENT,
            'uid=' . $containerUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . self::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=2'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count);

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            self::TABLE_CONTENT,
            'uid=' . $childUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'textpic\''
            . ' AND colPos=10'
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count);
    }

    /**
     * Copy an existing multicolumn container to another page
     *
     * @test
     */
    public function copyContainerAndChildrenToOtherPageInDefaultLanguage()
    {
        $dataMap = [];
        $cmpMap = [
            self::TABLE_CONTENT => [
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
        $dataHandler->start($dataMap, $cmpMap);
        $dataHandler->process_cmdmap();

        $containerUid = $dataHandler->copyMappingArray[self::TABLE_CONTENT][1];
        $childUid = $dataHandler->copyMappingArray[self::TABLE_CONTENT][2];

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            self::TABLE_CONTENT,
            'uid=' . $containerUid
            . ' AND pid=2'
            . ' AND deleted=0'
            . ' AND CType=\'' . self::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=0'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count);

        $count = $this->getDatabaseConnection()->exec_SELECTcountRows(
            '*',
            self::TABLE_CONTENT,
            'uid=' . $childUid
            . ' AND pid=2'
            . ' AND deleted=0'
            . ' AND CType=\'textpic\''
            . ' AND colPos=10'
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count);
    }
}
