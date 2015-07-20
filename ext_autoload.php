<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('multicolumn');
return array(
	'tx_multicolumn_div' => $extensionPath . 'lib/class.tx_multicolumn_div.php',
	'tx_multicolumn_db' => $extensionPath . 'lib/class.tx_multicolumn_db.php',
	'tx_multicolumn_flexform' => $extensionPath . 'lib/class.tx_multicolumn_flexform.php',
);
?>
