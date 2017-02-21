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

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class tx_multicolumn_tcemainBaseTest extends FunctionalTestCase
{
    const CONTENT_TABLE = 'tt_content';
    const CTYPE_MULTICOLUMN = 'multicolumn';
    const CTYPE_TEXTPIC = 'textpic';

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/multicolumn',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->importDataSet('ntf://Database/pages.xml');
        $this->importDataSet('ntf://Database/sys_language.xml');
        $this->importDataSet('ntf://Database/pages_language_overlay.xml');

        $fixturePath = ORIGINAL_ROOT . 'typo3conf/ext/multicolumn/Tests/Functional/Fixtures/';
        $this->importDataSet($fixturePath . 'tt_content.xml');

        $this->setUpBackendUserFromFixture(1);
        Bootstrap::getInstance()->initializeLanguageObject();
    }

    /**
     * @param DataHandler $dataHandler
     */
    protected function assertNoProssesingErrorsInDataHandler(DataHandler $dataHandler)
    {
        $dataHandler->printLogErrorMessages('');
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();

        $this->assertSame(0, count($flashMessageQueue->getAllMessages()));
    }
}
