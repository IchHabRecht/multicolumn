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

use IchHabRecht\Multicolumn\Utility\DatabaseUtility;

class tx_multicolumn_pi_sitemap extends tx_multicolumn_pi_base
{
    public $prefixId = 'tx_multicolumn_pi_sitemap';        // Same as class name

    public $scriptRelPath = 'pi_sitemap/class.tx_multicolumn_pi_sitemap.php';    // Path to this script relative to the extension dir.

    public $extKey = 'multicolumn';    // The extension key.

    public $pi_checkCHash = true;

    /**
     * Current cObj data
     *
     * @var        array
     */
    protected $currentCobjData;

    /**
     * The main method of the PlugIn
     *
     * @param    string $content : The PlugIn content
     * @param    array $conf : The PlugIn configuration
     *
     * @return    The content that is displayed on the website
     */
    public function main($content, $conf)
    {
        $content = '';

        $this->init($content, $conf);

        $uid = intval($this->cObj->stdWrap($this->conf['multicolumnContainerUid'], $this->conf['multicolumnContainerUid.']));
        if (!empty($uid)) {
            $elements = DatabaseUtility::getContentElementsFromContainer(null, null, $uid, 0, false, 'sectionIndex=1');
            if (count($elements)) {
                $listData = [
                    'sitemapItem' => $this->renderListItems('tt_content', 'sitemapItem', $elements),
                ];
                $content = $this->renderItem('sitemapList', $listData);
            }
        }

        return $content;
    }

    /**
     * Initalizes the plugin.
     *
     * @param    string $content : Content sent to plugin
     * @param    string[] $conf : Typoscript configuration array
     */
    protected function init($content, $conf)
    {
        $this->content = $content;
        $this->conf = $conf;
        $this->currentCobjData = $this->cObj->data;
    }
}

if (defined('TYPO3_MODE') && isset($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/multicolumn/pi_sitemap/class.tx_multicolumn_pi_sitemap.php'])) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/multicolumn/pi_sitemap/class.tx_multicolumn_pi_sitemap.php']);
}
