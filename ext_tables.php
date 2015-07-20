<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// add CType multicolumn
$TCA['tt_content']['types']['multicolumn'] = array(
	'showitem' => 'CType;;4;;1-1-1, hidden, header;;3;;2-2-2, linkToTop;;;;3-3-3,--div--;LLL:EXT:multicolumn/locallang_db.xml:tt_content.tx_multicolumn_tab.content, tx_multicolumn_items,--div--;LLL:EXT:multicolumn/locallang_db.xml:tt_content.tx_multicolumn_tab.config,pi_flexform,--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access, starttime, endtime, fe_group'
);

if (TYPO3_MODE == 'BE') {
	// add itemsProcFunc to colPos for dynamic colPos
	require_once(PATH_tx_multicolumn . 'lib/class.tx_multicolumn_tceform.php');

	// add clickmenu expansion
	$GLOBALS['TBE_MODULES_EXT']['xMOD_alt_clickmenu']['extendCMclasses'][] = array(
		'name' => 'tx_multicolumn_alt_clickmenu',
		'path' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('multicolumn') . 'hooks/class.tx_multicolumn_alt_clickmenu.php',
	);
}

// Add typoscript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'pi1/static/', 'Multicolumn');

// Add configuration flexform
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('*', 'FILE:EXT:multicolumn/flexform_ds.xml', 'multicolumn');
