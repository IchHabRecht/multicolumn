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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper;
use TYPO3\CMS\Lang\LanguageService;

class tx_multicolumn_tt_content_drawItem implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface
{
    /**
     * CSS file to use for BE styling
     *
     * @var string
     */
    protected $cssFile = 'res/backend/style.css';

    /**
     * Mulitcolumn content element
     *
     * @var        array
     */
    protected $multiColCe;

    /** @var int */
    protected $multiColUid;

    /**
     * Instance of tx_multicolumn_flexform
     *
     * @var        tx_multicolumn_flexform
     */
    protected $flex;

    /**
     * Reference of tx_cms_layout Object
     *
     * @var        tx_cms_layout
     */
    protected $pObj;

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
     * Reference of tx_cms_layout Object
     *
     * @var \TYPO3\CMS\Core\TypoScript\TemplateService
     */
    protected $tmpl;

    /**
     * Locallang array
     *
     * @var        array
     */
    protected $LL;

    /**
     * Is effectbox?
     *
     * @var        bool
     */
    protected $isEffectBox;

    /**
     * Preprocesses the preview rendering of a content element.
     *
     * @param \TYPO3\CMS\Backend\View\PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionalities
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     *
     * @return void
     */
    public function preProcess(\TYPO3\CMS\Backend\View\PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row)
    {
        // return if not multicolumn
        if ($row['CType'] == 'multicolumn') {
            $pageRenderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
            $pageRenderer->addCssFile(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('multicolumn') . $this->cssFile, 'stylesheet', 'screen');

            $this->flex = GeneralUtility::makeInstance('tx_multicolumn_flexform', $row['pi_flexform']);
            $this->pObj = $parentObject;
            $this->tmpl = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TemplateService::class);
            $this->LL = tx_multicolumn_div::includeBeLocalLang();
            $this->isEffectBox = ($this->flex->getFlexValue('preSetLayout', 'layoutKey') == 'effectBox.');

            $this->multiColCe = $row;
            $this->multiColUid = intval($row['uid']);

            $this->layoutConfiguration = tx_multicolumn_div::getLayoutConfiguration($this->multiColCe['pid'], $this->flex);

            if ($this->layoutConfiguration['columns']) {
                // do option split
                $this->layoutConfigurationSplited = $this->tmpl->splitConfArray($this->layoutConfiguration, $this->layoutConfiguration['columns']);
                $itemContent .= $this->buildColumns($this->layoutConfiguration['columns']);
            }
        }
    }

    /**
     * Builds the columns markup
     *
     * @param    int $numberOfColumns : how many columns to build
     *
     * @return    string            html content
     */
    protected function buildColumns($numberOfColumns)
    {
        //build columns
        $markup = '</span><table class="multicolumn t3-page-columns"><tr>';
        $columnIndex = 0;
        $multicolumnColPos = 0;

        $widthOfAllColumnsInPx = 0;
        foreach ($this->layoutConfigurationSplited as $columnConfiguration) {
            if ($columnConfiguration['columnMeasure'] == 'px') {
                $widthOfAllColumnsInPx += $columnConfiguration['columnWidth'];
            }
        }

        while ($columnIndex < $numberOfColumns) {
            $multicolumnColPos = tx_multicolumn_div::colPosStart + $columnIndex;

            $splitedColumnConf = $this->layoutConfigurationSplited[$columnIndex];
            if ($splitedColumnConf['columnMeasure'] == '%') {
                $columnWidth = $splitedColumnConf['columnWidth'] ? $splitedColumnConf['columnWidth'] : round(100 / $numberOfColumns);
            } else {
                $columnWidth = $splitedColumnConf['columnWidth'] ? round($splitedColumnConf['columnWidth'] * 100 / $widthOfAllColumnsInPx) : round(100 / $numberOfColumns);
            }

            //create header
            $this->buildColumn($columnWidth, $columnIndex, $multicolumnColPos, $markup);
            $columnIndex++;
        }

        $markup .= '</tr>';
        $markup .= '</table>';
        // are there any lost content elements?
        $markup .= $this->buildLostContentElementsRow($multicolumnColPos);
        $markup .= '<span>';

        return $markup;
    }

    /**
     * Builds a single column with conten telements
     *
     * @param int $columnWidth : width of column
     * @param int $columnIndex : number of column
     * @param int $colPos
     * @param string $markup
     *
     * @return string            $column markup
     */
    protected function buildColumn($columnWidth, $columnIndex, $colPos, &$markup)
    {
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
        $markup .= '<td id="column_' . (int)$this->multiColCe['uid'] . '_' . (int)$colPos . '" '
            . 'class="t3-page-column t3-page-column-' . (int)$columnIndex . ' column column' . (int)$columnIndex . '" '
            . 'style="width: ' . $columnWidth . '%">'
            . '<div class="innerContent">';

        $newParams = $this->getNewRecordParams($this->multiColCe['pid'], $colPos, $this->multiColCe['uid'], $this->multiColCe['sys_language_uid']);
        $columnNumber = $columnIndex + 1;
        /** @noinspection PhpUndefinedMethodInspection */
        $columnLabel = $this->isEffectBox
            ? $this->getLanguageService()->getLLL('cms_layout.effectBox', $this->LL)
            : $this->getLanguageService()->getLLL('cms_layout.columnTitle', $this->LL) . ' ' . $columnNumber;

        $markup .= $this->pObj->tt_content_drawColHeader($columnLabel, null, $newParams);

        $markup .= '<div class="t3-page-ce t3js-page-new-ce t3-page-ce-wrapper-new-ce" id="colpos-' . (int)$colPos . '-' . 'tt-content-' . (int)$this->multiColCe['uid'] .
            '-' . StringUtility::getUniqueId() . '">';
        $markup .= '<a href="#" '
            . 'onclick="' . htmlspecialchars($newParams) . '" '
            . 'title="' . $this->getLanguageService()->getLL('newContentElement', true) . '" '
            . 'class="btn btn-default btn-sm">'
            . $iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL)->render()
            . ' '
            . $this->getLanguageService()->getLL('content', true) . '</a>';
        $markup .= '</div>';

        $markup .= $this->buildColumnContentElements($colPos, $this->multiColCe['pid'], $this->multiColCe['uid'], $this->multiColCe['sys_language_uid']);

        $markup .= '</div></td>';
    }

    /**
     * Builds the overview of content elements for the column
     *
     * @param    int $colPos
     * @param    int $pid page id
     * @param    int $mulitColumnParentId parent id of multicolumn content element
     * @param    int $sysLanguageUid sys language uid
     *
     * @return string
     */
    protected function buildColumnContentElements($colPos, $pid, $mulitColumnParentId, $sysLanguageUid)
    {
        $result = '';
        $showHidden = $this->pObj->tt_contentConfig['showHidden'] ? true : false;

        $elements = tx_multicolumn_db::getContentElementsFromContainer($colPos, $pid, $mulitColumnParentId, $sysLanguageUid, $showHidden, null, $this->pObj);
        if ($elements) {
            $result = $this->renderContentElements($elements);
        }

        return $result;
    }

    /**
     * Builds the lost content elements container
     *
     * @param    int $lastColumnNumber last visible columnNumber
     *
     * @return    string            $column markup
     */
    protected function buildLostContentElementsRow($lastColumnNumber)
    {
        $markup = '';
        $additionalWhere = ' deleted = 0 AND (colPos >' . intval($lastColumnNumber) . ' OR colPos < ' . tx_multicolumn_div::colPosStart . ') AND tx_multicolumn_parentid = ' . $this->multiColUid;

        $elements = tx_multicolumn_db::getContentElementsFromContainer(null, null, $this->multiColUid, $this->multiColCe['sys_language_uid'], true, $additionalWhere, $this->pObj);

        if ($elements) {
            $markup = '<div class="lostContentElementContainer">';
            $view = GeneralUtility::makeInstance(StandaloneView::class);
            $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:backend/Resources/Private/Templates/InfoBox.html'));
            $view->assignMultiple([
                'title' => $this->getLanguageService()->getLLL('cms_layout.lostElements.title', $this->LL),
                'message' => $this->getLanguageService()->getLLL('cms_layout.lostElements.message', $this->LL),
                'state' => InfoboxViewHelper::STATE_WARNING,
            ]);
            $markup .= $view->render();
            $markup .= $this->renderContentElements($elements, 'lostContentElements', true);
            $markup .= '</div>';
        }

        return $markup;
    }

    /**
     * Render content elements like class.tx_cms_layout.php
     *
     * @param array $rowArr records form tt_content table
     * @param string $additionalClasses to append to <ul>
     * @param bool $lostElements
     */
    protected function renderContentElements(array $rowArr, $additionalClasses = null, $lostElements = false)
    {
        $content = '<ul class="contentElements ' . $additionalClasses . '">';

        $item = 0;
        foreach ($rowArr as $rKey => $row) {
            if (is_array($row) && (int)$row['t3ver_state'] != 2) {
                $statusHidden = ($this->pObj->isDisabled('tt_content', $row) ? ' t3-page-ce-hidden' : '');

                $ceClass = 't3-page-ce' . $statusHidden;
                $content .= '<li id="element_' . $row['tx_multicolumn_parentid'] . '_' . $row['colPos'] . '_' . $row['uid'] . '" class="contentElement item' . $item . '"><div class="' . $ceClass . '">';

                $space = $this->pObj->tt_contentConfig['showInfo'] ? 15 : 5;

                // render diffrent header
                if ($lostElements) {
                    // prevents fail edit icon
                    $currentNextThree = $this->pObj->tt_contentData['nextThree'];
                    $this->pObj->tt_contentData['nextThree'][$row['uid']] = $row['uid'];

                    $content .= $this->pObj->tt_content_drawHeader($row, $space, true, true);

                    // restore next three
                    $this->pObj->tt_contentData['nextThree'] = $currentNextThree;
                } else {
                    $content .= $this->addMultiColumnParentIdToCeHeader($this->pObj->tt_content_drawHeader($row, $space, false, true));
                }
                // pre crop bodytext
                if ($row['bodytext']) {
                    $row['bodytext'] = strip_tags(preg_replace('/<br.?\\/?>/', LF, $row['bodytext']));
                    $row['bodytext'] = GeneralUtility::fixed_lgd_cs($row['bodytext'], 50);
                }

                $content .= '<div class="t3-page-ce-body-inner" ' . (isset($row['_ORIG_uid']) ? ' class="ver-element"' : '') . '>' . $this->pObj->tt_content_drawItem($row) . '</div>';
                $content .= '</div></div></li>';
                $item++;
            } else {
                unset($rowArr[$rKey]);
            }
        }

        $content .= '</ul>';

        return $content;
    }

    /**
     * Adds tx_multicolumn_parentid to default db_new_content_el.php? query string
     *
     * @param string $headerContent
     *
     * @return    string        Substituted content
     */
    protected function addMultiColumnParentIdToCeHeader($headerContent)
    {
        return str_replace('db_new_content_el.php?', 'db_new_content_el.php?tx_multicolumn_parentid=' . $this->multiColUid . '&amp;', $headerContent);
    }

    /**
     * Generates the url for the insertRecord links. Special value tx_multicolumn is considered here...
     *
     * @param    int $pid record id
     * @param    int $colPos column position value
     * @param    int $mulitColumnParentId content id, reference where this content element belongs to
     * @param    int $sysLanguageUid System language
     *
     * @return    string
     */
    public function getNewRecordParams($pid, $colPos, $mulitColumnParentId, $sysLanguageUid = 0)
    {
        $params = '&id=' . (int)$pid;
        $params .= '&colPos=' . (int)$colPos;
        $params .= '&tx_multicolumn_parentid=' . (int)$mulitColumnParentId;
        $params .= '&sys_language_uid=' . (int)$sysLanguageUid;
        $params .= '&uid_pid=' . (int)$pid;
        $params .= '&returnUrl=' . rawurlencode(GeneralUtility::getIndpEnv('REQUEST_URI'));

        return 'window.location.href=' . GeneralUtility::quoteJSvalue(
            BackendUtility::getModuleUrl('new_content_element') . $params
        ) . ';';
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
