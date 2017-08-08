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

class tx_multicolumn_tcemainDeleteTest extends tx_multicolumn_tcemainBaseTest
{
    /**
     * Delete a container and its children
     *
     * @test
     */
    public function deleteContainerDeletesChildrenInDefaultLanguage()
    {
        $cmdMap = [
            tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => [
                1 => [
                    'delete' => 1,
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
            'uid IN (1,2)'
            . ' AND deleted=1'
            . ' AND sys_language_uid=0'
        );
        $this->assertSame(2, $count);
    }
}
