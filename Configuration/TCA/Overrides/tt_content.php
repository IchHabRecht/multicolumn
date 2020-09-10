<?php

declare(strict_types=1);

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['multicolumn'] = 'mimetypes-x-content-multicolumn';

// Add multicolumn to CType
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
    'tt_content',
    'CType',
    [
        'LLL:EXT:multicolumn/Resources/Private/Language/locallang_db.xlf:tx_multicolumn_multicolumn',
        'multicolumn',
        'mimetypes-x-content-multicolumn',
    ],
    'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:CType.div.lists',
    'before'
);

// Add CType multicolumn
$GLOBALS['TCA']['tt_content']['types']['multicolumn'] = [
    'showitem' => '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general, --palette--;;general, --palette--;;headers,'
        . ' --div--;LLL:EXT:multicolumn/Resources/Private/Language/locallang_db.xlf:tt_content.tx_multicolumn_tab.content, tx_multicolumn_items,'
        . ' --div--;LLL:EXT:multicolumn/Resources/Private/Language/locallang_db.xlf:tt_content.tx_multicolumn_tab.config, pi_flexform,'
        . ' --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, --palette--;;appearanceLinks, --palette--;;hidden, --palette--;;access',
];

// Add tx_multicolumn_parentid to tt_content table
$tempColumns = [
    'tx_multicolumn_parentid' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:multicolumn/Resources/Private/Language/locallang_db.xlf:tt_content.tx_multicolumn_parentid',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tt_content',
            'foreign_table_where' => 'AND tt_content.uid=###REC_FIELD_tx_multicolumn_parentid###',
            'itemsProcFunc' => \IchHabRecht\Multicolumn\Form\FormDataProvider\ContainerItemsProvider::class . '->init',
            'multicolumnProc' => 'buildMulticolumnList',
            'items' => [
                ['', 0],
            ],
            'default' => 0,
            'size' => 1,
            'minitems' => 0,
            'maxitems' => 1,
            'onChange' => 'reload',
            'wizards' => [
                '_PADDING' => 2,
                '_VERTICAL' => 1,
                'edit' => [
                    'type' => 'popup',
                    'title' => 'Edit',
                    'name' => 'wizard_edit',
                    'popup_onlyOpenIfSelected' => 1,
                    'icon' => 'actions-open',
                    'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                ],
            ],
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('tt_content', 'general', 'tx_multicolumn_parentid', 'before:colPos');

$GLOBALS['TCA']['tt_content']['columns']['colPos']['config']['itemsProcFunctions'] = [
    'default' => $GLOBALS['TCA']['tt_content']['columns']['colPos']['config']['itemsProcFunc'],
];
$GLOBALS['TCA']['tt_content']['columns']['colPos']['config']['multicolumnProc'] = 'buildDynamicCols';
$GLOBALS['TCA']['tt_content']['columns']['colPos']['config']['itemsProcFunc'] =
    \IchHabRecht\Multicolumn\Form\FormDataProvider\ContainerItemsProvider::class . '->init';

// Add configuration flexform
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:multicolumn/Configuration/FlexForm/flexform_ds.xml',
    'multicolumn'
);
