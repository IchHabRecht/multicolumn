<?php

declare(strict_types=1);

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
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\StringUtility;

class NewTest extends FunctionalBaseTest
{
    /**
     * Add a new multicolumn container to an empty page
     *
     * @test
     */
    public function addContainerToEmptyPageInDefaultLanguage()
    {
        $uniqueNewID = StringUtility::getUniqueId('NEW');
        $dataMap = [
            FunctionalBaseTest::CONTENT_TABLE => [
                $uniqueNewID => [
                    'pid' => 2,
                    'CType' => FunctionalBaseTest::CTYPE_MULTICOLUMN,
                    'header' => 'New multicolumn container',
                    'colPos' => 0,
                    'sys_language_uid' => 0,
                    'tx_multicolumn_parentid' => '',
                ],
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start($dataMap, []);
        $dataHandler->process_datamap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'pid=2'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=0'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count);
    }

    /**
     * Add a new multicolumn container to a page with multicolumn container in different column
     *
     * @test
     */
    public function addContainerToPageWithContainerInDifferentColumnInDefaultLanguage()
    {
        $uniqueNewID = StringUtility::getUniqueId('NEW');
        $dataMap = [
            FunctionalBaseTest::CONTENT_TABLE => [
                $uniqueNewID => [
                    'pid' => 1,
                    'CType' => FunctionalBaseTest::CTYPE_MULTICOLUMN,
                    'header' => 'New multicolumn container',
                    'colPos' => 1,
                    'sys_language_uid' => 0,
                    'tx_multicolumn_parentid' => '',
                ],
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start($dataMap, []);
        $dataHandler->process_datamap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=1'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count);
    }

    /**
     * Add a new multicolumn container to a page with multicolumn container in the same column
     *
     * @test
     */
    public function addContainerToPageWithContainerInSameColumnInDefaultLanguage()
    {
        $uniqueNewID = StringUtility::getUniqueId('NEW');
        $dataMap = [
            FunctionalBaseTest::CONTENT_TABLE => [
                $uniqueNewID => [
                    'pid' => 1,
                    'CType' => FunctionalBaseTest::CTYPE_MULTICOLUMN,
                    'header' => 'New multicolumn container',
                    'colPos' => 0,
                    'sys_language_uid' => 0,
                    'tx_multicolumn_parentid' => '',
                ],
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start($dataMap, []);
        $dataHandler->process_datamap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=0'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(2, $count);
    }

    /**
     * Add a new multicolumn container to an existing multicolumn container
     *
     * @test
     */
    public function addContainerToOtherContainerInDefaultLanguage()
    {
        $uniqueNewID = \TYPO3\CMS\Core\Utility\StringUtility::getUniqueId('NEW');
        $dataMap = [
            FunctionalBaseTest::CONTENT_TABLE => [
                $uniqueNewID => [
                    'pid' => 1,
                    'CType' => self::CTYPE_MULTICOLUMN,
                    'header' => 'Nested multicolumn container',
                    'colPos' => 10,
                    'sys_language_uid' => 0,
                    'tx_multicolumn_parentid' => 1,
                ],
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start($dataMap, []);
        $dataHandler->process_datamap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . self::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=10'
            . ' AND sys_language_uid=0'
            . ' AND tx_multicolumn_parentid=1'
        );
        $this->assertSame(1, $count);
    }
}
