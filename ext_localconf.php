<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// define multicolumn path
define('PATH_tx_multicolumn', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('multicolumn'));

//hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['multicolumn'] =
    \IchHabRecht\Multicolumn\Hooks\DataHandlerHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['multicolumn'] =
    IchHabRecht\Multicolumn\Hooks\DataHandlerHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['moveRecordClass']['multicolumn'] =
    IchHabRecht\Multicolumn\Hooks\DataHandlerHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms']['db_new_content_el']['wizardItemsHook'][] =
    \IchHabRecht\Multicolumn\Hooks\WizardItemsHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem']['multicolumn'] =
    \IchHabRecht\Multicolumn\Hooks\PageLayoutViewHook::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['GLOBAL']['recStatInfoHooks']['multicolumn'] =
    \IchHabRecht\Multicolumn\Hooks\PageLayoutViewHook::class . '->addDeleteWarning';

// special eval
$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals'][\IchHabRecht\Multicolumn\Evaluation\MaxColumnsEvaluator::class] = '';

//add page TSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/Configuration/TSconfig/multicolumn.ts">');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/Configuration/TSconfig/NewContentElementWizard.ts">');

//add default TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('multicolumn', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/pi1/static/defaultTS.txt">', 43);
//add sitemap TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('multicolumn', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/pi_sitemap/static/setup.txt">', 43);

// Add frontend plugin
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43('multicolumn', 'pi1/class.tx_multicolumn_pi1.php', '_pi1', 'list_type', 1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43('multicolumn', 'pi_sitemap/class.tx_multicolumn_pi_sitemap.php', '_pi_sitemap', 'list_type', 1);

// Add dataProvider for FormEngine
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['flexFormSegment'][\IchHabRecht\Multicolumn\Form\FormDataProvider\TcaFlexEffectivePid::class] = [
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
    [
        'source' => 'EXT:multicolumn/pi1/ce_wiz.gif',
    ]
);
