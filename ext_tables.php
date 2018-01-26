<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE == 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1492078309] = \IchHabRecht\Multicolumn\ContextMenu\ItemProvider::class;
}

// Add configuration flexform
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:multicolumn/flexform_ds.xml', 'multicolumn');
