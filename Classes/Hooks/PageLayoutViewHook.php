<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Hooks;

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
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Fluid\ViewHelpers\Be\InfoboxViewHelper;
use TYPO3\CMS\Lang\LanguageService;

class PageLayoutViewHook implements \TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface
{
    /**
     * CSS file to use for BE styling
     *
     * @var string
     */
    protected $cssFile = 'Resources/Public/Css/backend.css';

    /**
     * Mulitcolumn content element
     *
     * @var        array
     */
    protected $multiColCe;

    /** @var int */
    protected $multiColUid;

    /**
     * Instance of \IchHabRecht\Multicolumn\Utility\FlexFormUtility
     *
     * @var FlexFormUtility
     */
    protected $flex;

    /**
     * @var IconFactory
     */
    protected $iconFactory;

    /**
     * Reference of tx_cms_layout Object
     *
     * @var PageLayoutView
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
     * @param IconFactory $iconFactory
     */
    public function __construct(IconFactory $iconFactory = null)
    {
        $this->iconFactory = $iconFactory !== null ? $iconFactory : GeneralUtility::makeInstance(IconFactory::class);
    }

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
            $drawItem = false;
            $pageRenderer = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
            $cssFile = PathUtility::getAbsoluteWebPath(GeneralUtility::getFileAbsFileName('EXT:multicolumn/' . $this->cssFile));
            $pageRenderer->addCssFile($cssFile, 'stylesheet', 'screen');

            $this->flex = GeneralUtility::makeInstance(FlexFormUtility::class, $row['pi_flexform']);
            $this->pObj = $parentObject;
            $this->tmpl = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TemplateService::class);
            $this->LL = MulticolumnUtility::includeBeLocalLang();

            $this->multiColCe = $row;
            $this->multiColUid = intval($row['uid']);

            $this->layoutConfiguration = MulticolumnUtility::getLayoutConfiguration($this->multiColCe['pid'], $this->flex);

            if ($this->layoutConfiguration['columns']) {
                // do option split
                $this->layoutConfigurationSplited = GeneralUtility::makeInstance(TypoScriptService::class)
                    ->explodeConfigurationForOptionSplit($this->layoutConfiguration, (int)$this->layoutConfiguration['columns']);
                $itemContent .= $this->buildColumns($this->layoutConfiguration['columns']);
            }
        }
    }

    /**
     * Expands the delete warning with "(This multicolumn container has X content elements(s)...)
     * before you delete a records
     *
     * @param array $params
     */
    public function addDeleteWarning(array $params)
    {
        if (!$params[0] == 'tt_content') {
            return;
        }

        // adjust delete warning
        if ($params['2']['CType'] == 'multicolumn') {
            $numberOfContentElements = DatabaseUtility::getNumberOfContentElementsFromContainer($params['2']['uid']);

            $LL = MulticolumnUtility::includeBeLocalLang();

            // no children found? return!
            if (!$numberOfContentElements) {
                $this->restoreOrginalDeleteWarning($LL);

                return;
            }

            $llGlobal = &$GLOBALS['LOCAL_LANG'];

            // add multicolumn delete warning
            foreach ($LL as $llKey => $ll) {
                $deleteWarningOrginal = isset($llGlobal[$llKey]['deleteWarningOrginal']) ? $llGlobal[$llKey]['deleteWarningOrginal'] : $llGlobal[$llKey]['deleteWarning'];

                $cmsLayoutDeleteWarning = $ll['cms_layout.deleteWarning'];
                if (is_array($cmsLayoutDeleteWarning)) {
                    $cmsLayoutDeleteWarning = $cmsLayoutDeleteWarning[0]['target'];
                }
                $deleteWarningMulticolumn = str_replace('%s', $numberOfContentElements, $cmsLayoutDeleteWarning);
                $deleteWarning = isset($llGlobal[$llKey]['deleteWarningOrginal']) ? $llGlobal[$llKey]['deleteWarningOrginal'] : $llGlobal[$llKey]['deleteWarning'];

                if (is_array($llGlobal[$llKey]['deleteWarning'])) {
                    foreach ($llGlobal[$llKey]['deleteWarning'] as &$llValue) {
                        $llValue = $deleteWarning[0];
                    }
                } else {
                    $llGlobal[$llKey]['deleteWarningOrginal'] = isset($llGlobal[$llKey]['deleteWarningOrginal']) ? $llGlobal[$llKey]['deleteWarningOrginal'] : $llGlobal[$llKey]['deleteWarning'];
                }

                if (is_array($llGlobal[$llKey]['deleteWarning'])) {
                    foreach ($llGlobal[$llKey]['deleteWarning'] as &$llValue) {
                        $llValue['source'] = $deleteWarningOrginal[0]['source'] . chr(10) . $deleteWarningMulticolumn;
                        $llValue['target'] = $deleteWarningOrginal[0]['target'] . chr(10) . $deleteWarningMulticolumn;
                    }
                } else {
                    $llGlobal[$llKey]['deleteWarning'] = $deleteWarningOrginal . chr(10) . $deleteWarningMulticolumn;
                }
            }

            unset($llGlobal);
        }
    }

    /**
     * Builds the columns markup
     *
     * @param int $numberOfColumns : how many columns to build
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
            $multicolumnColPos = MulticolumnUtility::colPosStart + $columnIndex;

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
        $columnLabel = $this->getLanguageService()->getLLL('cms_layout.columnTitle', $this->LL) . ' ' . ($columnIndex + 1);
        $language = $this->multiColCe['sys_language_uid'];

        $markup .= '<td id="column_' . (int)$this->multiColCe['uid'] . '_' . (int)$colPos . '" '
            . 'class="t3-page-column t3-page-column-' . (int)$columnIndex . ' column column' . (int)$columnIndex . '" '
            . 'style="width: ' . $columnWidth . '%">'
            . '<div class="innerContent" data-colpos="' . $colPos . '" data-language-uid="' . $language . '">';

        $markup .= $this->pObj->tt_content_drawColHeader($columnLabel);

        $markup .= '<div class="t3js-sortable t3js-sortable-lang t3js-sortable-lang-' . $language . ' t3-page-ce-wrapper">';

        $markup .= '<div class="t3-page-ce" data-page="' . $this->multiColCe['pid'] . '">';
        $markup .= '<div class="t3js-page-new-ce t3-page-ce-wrapper-new-ce"'
            . ' id="colpos-' . (int)$colPos . '-' . 'tt-content-' . (int)$this->multiColCe['uid'] . '-' . StringUtility::getUniqueId() . '"'
            . ' data-page="' . $this->multiColCe['pid'] . '">';
        $markup .= $this->getNewContentElementButton($this->multiColCe['pid'], $colPos, $this->multiColCe['uid'], $this->multiColCe['sys_language_uid']);
        $markup .= '</div>';
        $markup .= '<div class="t3-page-ce-dropzone-available t3js-page-ce-dropzone-available"></div>';
        $markup .= '</div></div>';

        $markup .= $this->buildColumnContentElements($colPos, $this->multiColCe['pid'], $this->multiColCe['uid'], $this->multiColCe['sys_language_uid']);

        $markup .= '</div></td>';
    }

    /**
     * Builds the overview of content elements for the column
     *
     * @param int $colPos
     * @param int $pid page id
     * @param int $mulitColumnParentId parent id of multicolumn content element
     * @param int $sysLanguageUid sys language uid
     *
     * @return string
     */
    protected function buildColumnContentElements($colPos, $pid, $mulitColumnParentId, $sysLanguageUid)
    {
        $result = '';
        $showHidden = $this->pObj->tt_contentConfig['showHidden'] ? true : false;

        $elements = DatabaseUtility::getContentElementsFromContainer($colPos, $pid, $mulitColumnParentId, $sysLanguageUid, $showHidden, null, $this->pObj);
        if ($elements) {
            $result = $this->renderContentElements($elements);
        }

        return $result;
    }

    /**
     * Builds the lost content elements container
     *
     * @param int $lastColumnNumber last visible columnNumber
     *
     * @return    string            $column markup
     */
    protected function buildLostContentElementsRow($lastColumnNumber)
    {
        $markup = '';
        $additionalWhere = ' deleted = 0 AND (colPos >' . intval($lastColumnNumber) . ' OR colPos < ' . MulticolumnUtility::colPosStart . ') AND tx_multicolumn_parentid = ' . $this->multiColUid;

        $elements = DatabaseUtility::getContentElementsFromContainer(null, null, $this->multiColUid, $this->multiColCe['sys_language_uid'], true, $additionalWhere, $this->pObj);

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
        $content = '<div class="t3-page-ce-wrapper contentElements ' . $additionalClasses . '">';

        $item = 0;
        foreach ($rowArr as $rKey => $row) {
            if (is_array($row) && (int)$row['t3ver_state'] != 2) {
                $statusHidden = ($this->pObj->isDisabled('tt_content', $row) ? ' t3-page-ce-hidden' : '');

                $content .= '<div'
                    . ' id="element_' . $row['tx_multicolumn_parentid'] . '_' . $row['colPos'] . '_' . $row['uid'] . '"'
                    . ' class="t3-page-ce t3js-page-ce contentElement item' . $item . $statusHidden . '" data-uid="' . $row['uid'] . '">';

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
                $content .= '</div>';

                $content .= '<div class="t3-page-ce t3js-page-new-ce t3-page-ce-wrapper-new-ce" id="colpos-' . (int)$row['colPos'] . '-' . 'tt-content-' . (int)$row['uid'] .
                    '-' . StringUtility::getUniqueId() . '">';
                $content .= $this->getNewContentElementButton($this->multiColCe['pid'], $row['colPos'], $this->multiColCe['uid'], $this->multiColCe['sys_language_uid'], $row['uid']);

                $content .= '<div class="t3-page-ce-dropzone-available t3js-page-ce-dropzone-available"></div>';

                $content .= '</div></div>';

                $item++;
            } else {
                unset($rowArr[$rKey]);
            }
        }

        $content .= '</div>';

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

    protected function getNewContentElementButton(int $pid, int $colPos, int $mulitColumnParentId, int $sysLanguageUid = 0, int $uid_pid = null): string
    {
        $urlParameters = [
            'id' => $pid,
            'colPos' => $colPos,
            'tx_multicolumn_parentid' => $mulitColumnParentId,
            'sys_language_uid' => $sysLanguageUid,
            'uid_pid' => ($uid_pid !== null ? -$uid_pid : $pid),
            'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI'),
        ];
        if (version_compare(TYPO3_version, '9.0', '<')) {
            $tsConfig = BackendUtility::getModTSconfig($pid, 'mod');
            $moduleName = $tsConfig['properties']['newContentElementWizard.']['override'] ?? 'new_content_element';
            $url = BackendUtility::getModuleUrl($moduleName, $urlParameters);
        } else {
            $tsConfig = BackendUtility::getPagesTSconfig($pid);
            $routeName = $tsConfig['mod.']['newContentElementWizard.']['override'] ?? 'new_content_element_wizard';
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $url = (string)$uriBuilder->buildUriFromRoute($routeName, $urlParameters);
        }
        $title = htmlspecialchars($this->getLanguageService()->getLL('newContentElement'));
        $button = '<a href="' . htmlspecialchars($url) . '"'
            . ' title="' . $title . '"'
            . ' data-title="' . $title . '"'
            . ' class="btn btn-default btn-sm t3js-toggle-new-content-element-wizard">'
            . $this->iconFactory->getIcon('actions-document-new', Icon::SIZE_SMALL)->render()
            . ' '
            . htmlspecialchars($this->getLanguageService()->getLL('content')) . '</a>';

        return $button;
    }

    protected function restoreOrginalDeleteWarning(array $LL)
    {
        foreach ($LL as $llKey => $ll) {
            if ($GLOBALS['LOCAL_LANG'][$llKey]['deleteWarningOrginal']) {
                $GLOBALS['LOCAL_LANG'][$llKey]['deleteWarning'] = $GLOBALS['LOCAL_LANG'][$llKey]['deleteWarningOrginal'];
            }
        }
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
