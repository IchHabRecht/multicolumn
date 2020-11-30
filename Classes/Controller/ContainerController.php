<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Controller;

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
use IchHabRecht\Multicolumn\Utility\FlexFormUtility;
use IchHabRecht\Multicolumn\Utility\MulticolumnUtility;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ContainerController extends AbstractController
{
    public $prefixId = 'tx_multicolumn_pi1';        // Same as class name

    public $extKey = 'multicolumn';    // The extension key.

    public $pi_checkCHash = true;

    /**
     * Current cObj data
     *
     * @var        array
     */
    protected $currentCobjData;

    /**
     * Current cObjrecord string eg. tt_content:23
     *
     * @var        string
     */
    protected $currentCobjRecordString;

    /**
     * Incremented in parent cObj->RECORDS
     * and cObj->CONTENT before each record rendering.
     *
     * @var        int
     */
    protected $currentCobjParentRecordNumber;

    /**
     * Instance of \IchHabRecht\Multicolumn\Utility\FlexFormUtility
     *
     * @var FlexFormUtility
     */
    protected $flex;

    /**
     * Layout configuration array from ts / flexform
     *
     * @var        array
     */
    protected $layoutConfiguration;

    /**
     * Layout configuration array from ts / flexform with option split
     *
     * @var        array
     */
    protected $layoutConfigurationSplited;

    /**
     * multicolumn uid
     *
     * @var        int
     */
    protected $multicolumnContainerUid;

    /**
     * maxWidth before
     *
     * @var        int
     */
    protected $TSFEmaxWidthBefore;

    /** @var string[] */
    protected $llPrefixed;

    /**
     * The main method of the PlugIn
     *
     * @param string $content : The PlugIn content
     * @param array $conf : The PlugIn configuration
     *
     * @return   string The content that is displayed on the website
     */
    public function main($content, $conf)
    {
        $this->init($content, $conf);
        // typoscript is not included
        if (!$this->conf['includeFromStatic']) {
            return $this->showFlashMessage($this->llPrefixed['lll:error.typoscript.title'], $this->llPrefixed['lll:error.typoscript.message']);
        }

        return $this->renderMulticolumnView();
    }

    /**
     * Initalizes the plugin.
     *
     * @param string $content : Content sent to plugin
     * @param string[] $conf : Typoscript configuration array
     */
    protected function init($content, $conf)
    {
        $this->content = $content;
        $this->conf = $conf;
        $this->pi_loadLL('EXT:multicolumn/Resources/Private/Language/locallang_pi1.xlf');

        $this->currentCobjData = $this->cObj->data;
        $this->currentCobjParentRecordNumber = $this->cObj->parentRecordNumber;
        $this->currentCobjRecordString = $this->cObj->currentRecord;

        //fallback to default
        $LLkey = (!empty($this->LOCAL_LANG[$this->LLkey])) ? $this->LLkey : 'default';
        $this->llPrefixed = MulticolumnUtility::prefixArray($this->LOCAL_LANG[$LLkey], 'lll:');
        $this->pi_setPiVarDefaults();

        $context = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class);
        $currentLanguageUid = $context->getPropertyFromAspect('language', 'id');
        $legacyOverlayType = $context->getPropertyFromAspect('language', 'legacyOverlayType');

        // Check if sys_language_contentOL is set and take $this->cObj->data['_LOCALIZED_UID']
        if ($legacyOverlayType && $currentLanguageUid && $this->cObj->data['_LOCALIZED_UID']) {
            $this->multicolumnContainerUid = $this->cObj->data['_LOCALIZED_UID'];
        } else {
            // take default uid from cObj->data
            $this->multicolumnContainerUid = $this->cObj->data['uid'];
        }

        $this->flex = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(FlexFormUtility::class, $this->cObj->data['pi_flexform']);
        // store current max width
        $this->TSFEmaxWidthBefore = isset($GLOBALS['TSFE']->register['maxImageWidth']) ? $GLOBALS['TSFE']->register['maxImageWidth'] : null;

        $this->layoutConfiguration = MulticolumnUtility::getLayoutConfiguration(null, $this->flex);

        //include layout css
        if (!empty($this->layoutConfiguration['layoutCss']) || !empty($this->layoutConfiguration['layoutCss.'])) {
            $files = is_array($this->layoutConfiguration['layoutCss.']) ? $this->layoutConfiguration['layoutCss.'] : ['layoutCss' => $this->layoutConfiguration['layoutCss']];
            $this->includeCssJsFiles($files);
        }

        // force equal height ?
        $config = MulticolumnUtility::getTSConfig($GLOBALS['TSFE']->id, 'config');
        if (!empty($this->layoutConfiguration['makeEqualElementBoxHeight'])) {
            if (is_array($config['advancedLayouts.']['makeEqualElementBoxHeight.']['includeFiles.'])) {
                $this->includeCssJsFiles($config['advancedLayouts.']['makeEqualElementBoxHeight.']['includeFiles.']);
            }
        }
        // force equal height for each column
        if (!empty($this->layoutConfiguration['makeEqualElementColumnHeight'])) {
            if (is_array($config['advancedLayouts.']['makeEqualElementColumnHeight.']['includeFiles.'])) {
                $this->includeCssJsFiles($config['advancedLayouts.']['makeEqualElementColumnHeight.']['includeFiles.']);
            }
        }

        // do option split
        $this->layoutConfigurationSplited = GeneralUtility::makeInstance(TypoScriptService::class)
            ->explodeConfigurationForOptionSplit($this->layoutConfiguration, (int)$this->layoutConfiguration['columns']);
    }

    protected function renderMulticolumnView()
    {
        $listItemData = $this->buildColumnData();
        //append config from column 0 for global config container width
        $listData = $listItemData[0];
        $listData['content'] = $this->renderListItems('_NO_TABLE', 'column', $listItemData, $this->llPrefixed);
        $listData['makeEqualElementBoxHeight'] = $this->layoutConfiguration['makeEqualElementBoxHeight'];
        $listData['makeEqualElementColumnHeight'] = $this->layoutConfiguration['makeEqualElementColumnHeight'];
        $listData['containerUid'] = $this->multicolumnContainerUid;

        return $this->renderItem('columnContainer', $listData);
    }

    /**
     * Gets the data for each column
     *
     * @return    array            column data
     */
    protected function buildColumnData()
    {
        $numberOfColumns = $this->layoutConfiguration['columns'];
        $columnContent = [];
        $disableImageShrink = $this->layoutConfiguration['disableImageShrink'] ? true : false;

        $columnNumber = 0;
        while ($columnNumber < $numberOfColumns) {
            $multicolumnColPos = MulticolumnUtility::colPosStart + $columnNumber;

            $splitedColumnConf = $this->layoutConfigurationSplited[$columnNumber];
            $conf = array_merge($this->layoutConfiguration, $splitedColumnConf);

            $colPosMaxImageWidth = $this->renderColumnWidth();

            $columnData = $conf;
            $columnData['columnWidth'] = $conf['columnWidth'] ? $conf['columnWidth'] : round(100 / $numberOfColumns);

            if (empty($this->layoutConfiguration['disableAutomaticImageWidthCalculation'])) {
                // evaluate columnWidth in pixels
                if ($conf['containerMeasure'] == 'px' && $conf['containerWidth']) {
                    $columnData['columnWidthPixel'] = round($conf['containerWidth'] / $numberOfColumns);
                } elseif ($conf['columnMeasure'] == 'px' && $conf['columnWidth']) {
                    // if columnWidth and column measure is set
                    $columnData['columnWidthPixel'] = $conf['columnWidth'];
                } elseif ($colPosMaxImageWidth) {
                    // if container width is set in percent (default 100%)
                    $columnData['columnWidthPixel'] = MulticolumnUtility::calculateMaxColumnWidth($columnData['columnWidth'], $colPosMaxImageWidth, $numberOfColumns);
                }

                // calculate total column padding width
                if ($columnData['columnPadding']) {
                    $columnData['columnPaddingTotalWidthPixel'] = MulticolumnUtility::getPaddingTotalWidth($columnData['columnPadding']);
                }
                // do auto scale if requested
                $maxImageWidth = $disableImageShrink ? null : (isset($columnData['columnWidthPixel']) ? ($columnData['columnWidthPixel'] - $columnData['columnPaddingTotalWidthPixel']) : null);
            } else {
                $maxImageWidth = $colPosMaxImageWidth;
            }

            $columnData['colPos'] = $multicolumnColPos;
            $contentElements = DatabaseUtility::getContentElementsFromContainer($columnData['colPos'], $this->cObj->data['pid'], $this->multicolumnContainerUid, $this->cObj->data['sys_language_uid']);
            if ($contentElements) {
                $GLOBALS['TSFE']->register['maxImageWidth'] = $maxImageWidth;
                $GLOBALS['TSFE']->register['maxImageWidthInText'] = $maxImageWidth;

                $columnData['content'] = $this->renderListItems('tt_content', 'columnItem', $contentElements, $this->llPrefixed);
            }

            $columnContent[] = $columnData;
            $columnNumber++;
        }

        // restore maxWidth
        $GLOBALS['TSFE']->register['maxImageWidth'] = $this->TSFEmaxWidthBefore;

        return $columnContent;
    }

    /**
     * Evaluates the maxwidth of current column
     *
     * @param string $confName Path to typoscript to render each element with
     * @param array $recordsArray Array which contains elements (array) for typoscript rendering
     * @param array $appendData Additinal data
     *
     * @return    string        All items rendered as a string
     */
    protected function renderColumnWidth()
    {
        $conf = is_array($this->layoutConfiguration) ? $this->layoutConfiguration : [];
        $colPosData = array_merge([
            'colPos' => $this->cObj->data['colPos'],
            'CType' => $this->cObj->data['CType'],
        ], $conf);

        return intval($this->renderItem('columnWidth', $colPosData));
    }
}
