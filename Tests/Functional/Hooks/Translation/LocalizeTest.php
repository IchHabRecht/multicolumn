<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Tests\Functional\Hooks\Translation;

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

class LocalizeTest extends FunctionalBaseTest
{
    /**
     * Localize a multicolumn container from default language
     *
     * @test
     */
    public function localizeContainerAndChildrenFromDefaultLanguage()
    {
        $cmdMap = [
            FunctionalBaseTest::CONTENT_TABLE => [
                1 => [
                    'localize' => 1,
                ],
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start([], $cmdMap);
        $dataHandler->process_cmdmap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $containerUid = $dataHandler->copyMappingArray[FunctionalBaseTest::CONTENT_TABLE][1];
        $childUid = $dataHandler->copyMappingArray[FunctionalBaseTest::CONTENT_TABLE][2];

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=' . $containerUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=0'
            . ' AND sys_language_uid=1'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count);

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=' . $childUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_TEXTPIC . '\''
            . ' AND colPos=10'
            . ' AND sys_language_uid=1'
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count);
    }

    /**
     * Localize a nested multicolumn container from default language
     *
     * @test
     */
    public function localizeNestedContainersAndChildrenFromDefaultLanguage()
    {
        $fixturePath = ORIGINAL_ROOT . 'typo3conf/ext/multicolumn/Tests/Functional/Fixtures/';
        $this->importDataSet($fixturePath . 'tt_content_nested_container.xml');

        $cmdMap = [
            FunctionalBaseTest::CONTENT_TABLE => [
                1 => [
                    'localize' => 1,
                ],
            ],
        ];

        $dataHandler = new DataHandler();
        $dataHandler->start([], $cmdMap);
        $dataHandler->process_cmdmap();

        $this->assertNoProssesingErrorsInDataHandler($dataHandler);

        $containerUid = $dataHandler->copyMappingArray[FunctionalBaseTest::CONTENT_TABLE][1];
        $childUid = $dataHandler->copyMappingArray[FunctionalBaseTest::CONTENT_TABLE][2];
        $nestedContainerUid = $dataHandler->copyMappingArray[FunctionalBaseTest::CONTENT_TABLE][4];
        $nestedChildUid = $dataHandler->copyMappingArray[FunctionalBaseTest::CONTENT_TABLE][5];

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=' . $containerUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=0'
            . ' AND sys_language_uid=1'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count, 'Container was not found');

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=' . $childUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_TEXTPIC . '\''
            . ' AND colPos=10'
            . ' AND sys_language_uid=1'
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count, 'Child was not found');

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=' . $nestedContainerUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=10'
            . ' AND sys_language_uid=1'
            . ' AND tx_multicolumn_parentid=' . $containerUid
        );
        $this->assertSame(1, $count, 'Nested container was not found');

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=' . $nestedChildUid
            . ' AND pid=1'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_TEXTPIC . '\''
            . ' AND colPos=10'
            . ' AND sys_language_uid=1'
            . ' AND tx_multicolumn_parentid=' . $nestedContainerUid
        );
        $this->assertSame(1, $count, 'Nested container child was not found');
    }
}
