<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\DataProcessing;

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

use IchHabRecht\Multicolumn\Utility\DatabaseUtility;
use IchHabRecht\Multicolumn\Utility\MulticolumnUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentDataProcessor;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class ContainerSectionProcessor implements DataProcessorInterface
{
    /**
     * @var ContentDataProcessor
     */
    protected $contentDataProcessor;

    public function __construct(ContentDataProcessor $contentDataProcessor = null)
    {
        $this->contentDataProcessor = $contentDataProcessor ?? GeneralUtility::makeInstance(ContentDataProcessor::class);
    }

    /**
     * Process content object data
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data (e.g. to be passed to a Fluid View)
     * @return array the processed data as key/value store
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData)
    {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        $sourceVariableName = (string)$cObj->stdWrapValue('sourceProcessedDataKey', $processorConfiguration, 'content');

        if (empty($processedData[$sourceVariableName])) {
            return $processedData;
        }

        // The variable to be used within the result
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'records');

        $processedRecordVariables = [];
        foreach ($processedData[$sourceVariableName] as $key => $data) {
            $data[$targetVariableName] = [];
            $processedRecordVariables[$key] = $data;
            if (empty($data['data']['CType']) || $data['data']['CType'] !== 'multicolumn') {
                continue;
            }

            $numberOfColumns = DatabaseUtility::getNumberOfColumnsFromContainer($data['data']['uid'], $data['data']);
            for ($i = 0; $i < $numberOfColumns; $i++) {
                $elements = DatabaseUtility::getContentElementsFromContainer(
                    MulticolumnUtility::colPosStart + $i,
                    $data['data']['pid'],
                    $data['data']['uid'],
                    $data['data']['sys_language_uid'],
                    $this->getTypoScriptFrontendController()->showHiddenRecords,
                    'sectionIndex=1'
                );
                foreach ($elements as $element) {
                    $contentObjectRenderer = GeneralUtility::makeInstance(ContentObjectRenderer::class);
                    $contentObjectRenderer->start($element, 'tt_content');
                    $data[$targetVariableName][] = $this->contentDataProcessor->process(
                        $contentObjectRenderer,
                        $processorConfiguration,
                        [
                            'data' => $element,
                        ]
                    );
                }
            }
            $processedRecordVariables[$key] = $data;
        }

        $processedData[$targetVariableName] = $processedRecordVariables;

        return $processedData;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
