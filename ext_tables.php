<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE == 'BE') {
    // Add clickmenu expansion
    $GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = [
        'name' => 'tx_multicolumn_alt_clickmenu',
        'path' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('multicolumn') . 'hooks/class.tx_multicolumn_alt_clickmenu.php',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1492078309] = \CPSIT\Multicolumn\ContextMenu\ItemProvider::class;
}

// register wizard icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'tx-multicolumn-wizard-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    ['source' => 'EXT:' . $_EXTKEY . '/pi1/ce_wiz.gif']
);

// Add typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'pi1/static/', 'Multicolumn');

// Add configuration flexform
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:multicolumn/flexform_ds.xml', 'multicolumn');
