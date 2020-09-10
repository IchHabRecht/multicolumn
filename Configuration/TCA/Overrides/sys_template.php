<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'multicolumn',
    'Configuration/TypoScript/Container/',
    'Multicolumn - Main Plugin'
);

if (ExtensionManagementUtility::isLoaded('fluid_styled_content')) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'multicolumn',
        'Configuration/TypoScript/Sitemap/',
        'Multicolumn - Section Index'
    );
}
