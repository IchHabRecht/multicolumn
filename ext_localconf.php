<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// define multicolumn path
define('PATH_tx_multicolumn', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('multicolumn'));
define('PATH_tx_multicolumn_rel', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY));
define('PATH_tx_multicolumn_pi_base', PATH_tx_multicolumn . 'lib/class.tx_multicolumn_pi_base.php');

//hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:multicolumn/hooks/class.tx_multicolumn_tcemain.php:tx_multicolumn_tcemain';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:multicolumn/hooks/class.tx_multicolumn_tcemain.php:tx_multicolumn_tcemain';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['moveRecordClass'][] = 'EXT:multicolumn/hooks/class.tx_multicolumn_tcemain.php:tx_multicolumn_tcemain';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks'][] = 'EXT:multicolumn/hooks/class.tx_multicolumn_cms_layout.php:tx_multicolumn_cms_layout->addDeleteWarning';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms']['db_new_content_el']['wizardItemsHook'][] = 'EXT:multicolumn/hooks/class.tx_multicolumn_wizardItemsHook.php:tx_multicolumn_wizardItemsHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass'][] = 'EXT:multicolumn/hooks/class.tx_multicolumn_t3lib_befunc.php:tx_multicolumn_t3lib_befunc';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list.inc']['makeQueryArray']['multicolumn'] = 'EXT:multicolumn/hooks/class.tx_multicolumn_db_list.php:tx_multicolumn_db_list';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = 'EXT:multicolumn/hooks/class.tx_multicolumn_tt_content_drawItem.php:tx_multicolumn_tt_content_drawItem';

// special eval
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_multicolumn_tce_eval'] = 'EXT:multicolumn/lib/class.tx_multicolumn_tce_eval.php';

//add page TSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/tsconfig.txt">');
//add default TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('multicolumn', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/pi1/static/defaultTS.txt">', 43);
//add sitemap TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('multicolumn', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/pi_sitemap/static/setup.txt">', 43);

// Add frontend plugin
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_multicolumn_pi1.php', '_pi1', 'list_type', 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi_sitemap/class.tx_multicolumn_pi_sitemap.php', '_pi_sitemap', 'list_type', 1);
