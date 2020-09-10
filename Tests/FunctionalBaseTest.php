<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Tests\Functional;

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

use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class FunctionalBaseTest extends FunctionalTestCase
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

        $fixturePath = ORIGINAL_ROOT . 'typo3conf/ext/multicolumn/Tests/Functional/Fixtures/';

        $this->importDataSet('ntf://Database/pages.xml');
        $this->importDataSet('ntf://Database/sys_language.xml');
        $this->importDataSet($fixturePath . 'tt_content.xml');

        if (version_compare(TYPO3_version, '9.5', '>=')) {
            $this->importDataSet($fixturePath . 'pages_overlay.xml');
        } else {
            $this->importDataSet('ntf://Database/pages_language_overlay.xml');
        }

        $this->setUpBackendUserFromFixture(1);

        if (method_exists(Bootstrap::class, 'getInstance')) {
            $bootstrap = Bootstrap::getInstance();
        } else {
            $bootstrap = Bootstrap::class;
        }
        call_user_func([$bootstrap, 'initializeLanguageObject']);

        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $flashMessageService->getMessageQueueByIdentifier()->clear();
    }

    /**
     * @param DataHandler $dataHandler
     */
    protected function assertNoProssesingErrorsInDataHandler(DataHandler $dataHandler)
    {
        $dataHandler->printLogErrorMessages();
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();

        $this->assertSame(0, count($flashMessageQueue->getAllMessages()));
    }
}
