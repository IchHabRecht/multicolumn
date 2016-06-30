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

class tx_multicolumn_tcemainDeleteTest extends tx_multicolumn_tcemainBaseTest {
	/**
	 * Delete a container and its children
	 *
	 * @test
	 */
	public function deleteContainerDeletesChildrenInDefaultLanguage() {
		$cmdMap = array(
			tx_multicolumn_tcemainBaseTest::CONTENT_TABLE => array(
				1 => array(
					'delete' => 1,
				),
			),
		);

		$dataHandler = new DataHandler();
		$dataHandler->start(array(), $cmdMap);
		$dataHandler->process_cmdmap();

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
