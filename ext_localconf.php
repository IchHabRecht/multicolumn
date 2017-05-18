<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// define multicolumn path
define('PATH_tx_multicolumn', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('multicolumn'));
define('PATH_tx_multicolumn_rel', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY));

//hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'tx_multicolumn_tcemain';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'tx_multicolumn_tcemain';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['moveRecordClass'][] = 'tx_multicolumn_tcemain';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks'][] = 'tx_multicolumn_cms_layout->addDeleteWarning';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass'][] = 'tx_multicolumn_t3lib_befunc';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list.inc']['makeQueryArray']['multicolumn'] = 'tx_multicolumn_db_list';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = 'tx_multicolumn_tt_content_drawItem';

// special eval
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_multicolumn_tce_eval'] = '';

//add page TSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/Configuration/TSconfig/multicolumn.ts">');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/Configuration/TSconfig/ContentWizard/NewContentElementWizard.ts">');

//add default TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('multicolumn', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/pi1/static/defaultTS.txt">', 43);
//add sitemap TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('multicolumn', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/pi_sitemap/static/setup.txt">', 43);

// Add frontend plugin
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi1/class.tx_multicolumn_pi1.php', '_pi1', 'list_type', 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43($_EXTKEY, 'pi_sitemap/class.tx_multicolumn_pi_sitemap.php', '_pi_sitemap', 'list_type', 1);

// Add dataProvider for FormEngine
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['flexFormSegment'][\CPSIT\Multicolumn\Form\FormDataProvider\TcaFlexEffectivePid::class] = [
    'depends' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowDefaultValues::class,
    ],
    'before' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class,
    ],
];

// Register newContentElementWizard icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'tx-multicolumn-wizard-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:' . $_EXTKEY . '/pi1/ce_wiz.gif']
);
