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
}

// Add typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'pi1/static/', 'Multicolumn');

// Add configuration flexform
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:multicolumn/flexform_ds.xml', 'multicolumn');
