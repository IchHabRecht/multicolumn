<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\Utility;

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

use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class MulticolumnUtility
{
    /**
     * Start index of colpos
     **/
    const colPosStart = 10;

    /**
     * Get layout configuration options merged between typoscript and flexform options
     *
     * @param int $pageUid
     * @param FlexFormUtility $flex
     *
     * @return array
     */
    public static function getLayoutConfiguration($pageUid, FlexFormUtility $flex)
    {
        // load default config
        $config = self::getDefaultLayoutConfiguration();

        $layoutKey = $flex->getFlexValue('preSetLayout', 'layoutKey');
        // remove . from ts string
        if ($layoutKey) {
            $config['layoutKey'] = substr($layoutKey, 0, -1);
        }

        $tsConfig = self::getTSConfig($pageUid);
        if (isset($tsConfig[$layoutKey]['config.'])) {
            $tsConfig = $tsConfig[$layoutKey]['config.'];
        }

        //merge default config with ts config
        if (is_array($tsConfig)) {
            $config = array_merge($config, $tsConfig);
        }

        //merge with flexconfig
        $flexConfig = $flex->getFlexArray('advancedLayout');
        if (is_array($flexConfig)) {
            $config = array_merge($config, $flexConfig);
        }

        return $config;
    }

    /**
     * Get preset layout configuration from tsconfig
     *
     * @param int $pageUid
     * @param string $tsConfigKey
     *
     * @return array Preset layout configuration
     */
    public static function getTSConfig($pageUid, $tsConfigKey = 'layoutPreset')
    {
        $tsConfig = isset($GLOBALS['TSFE']->cObj) ? $GLOBALS['TSFE']->getPagesTSconfig() : \TYPO3\CMS\Backend\Utility\BackendUtility::getPagesTSconfig($pageUid);
        $tsConfig = empty($tsConfig['tx_multicolumn.'][$tsConfigKey . '.']) ? $tsConfig['tx_multicolumn.'] : $tsConfig['tx_multicolumn.'][$tsConfigKey . '.'];

        return $tsConfig;
    }

    /**
     * Calculates the maximal width  of the column in pixel based on {$styles.content.imgtext.colPos0.maxW}
     *
     * @param int $columnWidth
     * @param int $colPosMaxWidth
     * @return int
     */
    public static function calculateMaxColumnWidth($columnWidth, $colPosMaxWidth)
    {
        return floor(($colPosMaxWidth / 100) * $columnWidth);
    }

    /**
     * Evaluates the total width of padding in colum
     *
     * @param string $columnPadding CSS string link 10px 20px 30px;
     *
     * @return int
     */
    public static function getPaddingTotalWidth($columnPadding)
    {
        // FIXME Fails if parts are separated with more than once space.
        $padding = preg_split('/ /', trim($columnPadding));

        // how many css attributes are set?
        $paddingNum = count($padding);

        // calculate total width
        $paddingTotalWidth = ($paddingNum == 2) ? intval($padding[1]) * 2 : (intval($padding[1]) + intval($padding[3]));

        return $paddingTotalWidth;
    }

    /**
     * Returns default Layout configuration options
     *
     * @return array
     */
    public static function getDefaultLayoutConfiguration()
    {
        return [
            'layoutKey' => null,
            'layoutCss' => null,
            'columns' => 2,
            'containerMeasure' => '%',
            'containerWidth' => 100,
            'columnMeasure' => '%',
            'columnWidth' => null,
            'columnMargin' => null,
            'columnPadding' => null,
            'disableImageShrink' => null,
            'disableStyles' => null,
        ];
    }

    /**
     * Prefix the keys in an array
     *
     * @param array $array
     * @param string $prefix Prefix string (ex: 'LLL:')
     *
     * @return array Prefixed array
     */
    public static function prefixArray(array $array, $prefix)
    {
        $newArray = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && array_key_exists(0, $value)) {
                if (!empty($value[0]['target'])) {
                    $newArray[$prefix . $key] = $value[0]['target'];
                } else {
                    $newArray[$prefix . $key] = $value[0]['source'];
                }
            } else {
                $newArray[$prefix . $key] = $value;
            }
        }

        return $newArray;
    }

    /**
     * Reads EXT:multicolumn/Resources/Private/Language/locallang.xlf and returns the $LOCAL_LANG array found in that file.
     *
     * @param string|null $llFile
     * @return array
     */
    public static function includeBeLocalLang($llFile = null)
    {
        $llFile = GeneralUtility::getFileAbsFileName('EXT:multicolumn/' . ($llFile ?? 'Resources/Private/Language/locallang.xlf'));

        return self::readLLfile($llFile, $GLOBALS['LANG']->lang);
    }

    /**
     * Reads the language file and returns labels in the format compatible with
     * TYPO3 4.5. If the runtime cache is available, uses the cache to avoid
     * reading the same file many times.
     *
     * @param string $filePath
     * @param string $language
     *
     * @return array
     */
    public static function readLLfile($filePath, $language)
    {
        $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
        $labels = $languageFactory->getParsedData($filePath, $language);
        // We need to flatten labels
        $originalLabels = $labels;
        foreach ($originalLabels as $languageKey => $languageArray) {
            foreach ($languageArray as $stringId => $translationData) {
                $labels[$languageKey][$stringId] = $translationData[0]['target'];
            }
        }
        if (isset($runtimeCache)) {
            $runtimeCache->set($cacheIdentifier, $labels);
        }

        return $labels;
    }
}
