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

require_once __DIR__ . '/../../../FunctionalBaseTest.php';

use TYPO3\CMS\Core\DataHandling\DataHandler;

class tx_multicolumn_tcemainNewTest extends tx_multicolumn_tcemainBaseTest {
	/**
	 * Add a new multicolumn container to an empty page
	 *
	 * @test
	 */
	public function addContainerToEmptyPageInDefaultLanguage() {
		$uniqueNewID = $this->getUniqueId('NEW');
		$dataMap = array(
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => array(
				$uniqueNewID => array(
					'pid' => 2,
					'CType' => tx_multicolumn_tcemainBaseTest::CTYPE_MULTICOLUMN,
					'header' => 'New multicolumn container',
					'colPos' => 0,
					'sys_language_uid' => 0,
					'tx_multicolumn_parentid' => '',
				),
			),
		);

		$dataHandler = new DataHandler();
		$dataHandler->start($dataMap, array());
		$dataHandler->process_datamap();

		$count = $this->getDatabaseConnection()->exec_SELECTcountRows(
			'*',
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
			'pid=2'
			. ' AND deleted=0'
			. ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_MULTICOLUMN . '\''
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
	public function addContainerToPageWithContainerInDifferentColumnInDefaultLanguage() {
		$uniqueNewID = $this->getUniqueId('NEW');
		$dataMap = array(
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => array(
				$uniqueNewID => array(
					'pid' => 1,
					'CType' => tx_multicolumn_tcemainBaseTest::CTYPE_MULTICOLUMN,
					'header' => 'New multicolumn container',
					'colPos' => 1,
					'sys_language_uid' => 0,
					'tx_multicolumn_parentid' => '',
				),
			),
		);

		$dataHandler = new DataHandler();
		$dataHandler->start($dataMap, array());
		$dataHandler->process_datamap();

		$count = $this->getDatabaseConnection()->exec_SELECTcountRows(
			'*',
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
			'pid=1'
			. ' AND deleted=0'
			. ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_MULTICOLUMN . '\''
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
	public function addContainerToPageWithContainerInSameColumnInDefaultLanguage() {
		$uniqueNewID = $this->getUniqueId('NEW');
		$dataMap = array(
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => array(
				$uniqueNewID => array(
					'pid' => 1,
					'CType' => tx_multicolumn_tcemainBaseTest::CTYPE_MULTICOLUMN,
					'header' => 'New multicolumn container',
					'colPos' => 0,
					'sys_language_uid' => 0,
					'tx_multicolumn_parentid' => '',
				),
			),
		);

		$dataHandler = new DataHandler();
		$dataHandler->start($dataMap, array());
		$dataHandler->process_datamap();

		$count = $this->getDatabaseConnection()->exec_SELECTcountRows(
			'*',
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
			'pid=1'
			. ' AND deleted=0'
			. ' AND CType=\'' . tx_multicolumn_tcemainBaseTest::CTYPE_MULTICOLUMN . '\''
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
	public function addContainerToOtherContainerInDefaultLanguage() {
		$uniqueNewID = $this->getUniqueId('NEW');
		$dataMap = array(
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => array(
				$uniqueNewID => array(
					'pid' => 1,
					'CType' => self::CTYPE_MULTICOLUMN,
					'header' => 'Nested multicolumn container',
					'colPos' => 10,
					'sys_language_uid' => 0,
					'tx_multicolumn_parentid' => 1,
				),
			),
		);

		$dataHandler = new DataHandler();
		$dataHandler->start($dataMap, array());
		$dataHandler->process_datamap();

		$count = $this->getDatabaseConnection()->exec_SELECTcountRows(
			'*',
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE,
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
