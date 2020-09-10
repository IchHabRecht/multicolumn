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

class MoveTest extends FunctionalBaseTest
{
    /**
     * Move a multicolumn container to another page
     *
     * @test
     */
    public function moveContainerAndChildrenToOtherPageInDefaultLanguage()
    {
        $cmdMap = [
            FunctionalBaseTest::CONTENT_TABLE => [
                1 => [
                    'move' => [
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

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=1'
            . ' AND pid=2'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_MULTICOLUMN . '\''
            . ' AND colPos=0'
            . ' AND tx_multicolumn_parentid=0'
        );
        $this->assertSame(1, $count);

        $count = $this->getDatabaseConnection()->selectCount(
            '*',
            FunctionalBaseTest::CONTENT_TABLE,
            'uid=2'
            . ' AND pid=2'
            . ' AND deleted=0'
            . ' AND CType=\'' . FunctionalBaseTest::CTYPE_TEXTPIC . '\''
            . ' AND colPos=10'
            . ' AND tx_multicolumn_parentid=1'
        );
        $this->assertSame(1, $count);
    }
}
