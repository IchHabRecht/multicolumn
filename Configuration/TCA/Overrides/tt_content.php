<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'mimetypes-x-content-multicolumn',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    [
        'source' => 'EXT:multicolumn/Resources/Public/Icons/tt_content_multicolumn.gif',
    ]
);
$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['multicolumn'] = 'mimetypes-x-content-multicolumn';

// Add multicolumn to CType
if (is_array($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'])) {
    $multicolumnAdded = false;
    $firstDivChecked = false;
    $sortedItems = [];

    foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as $key => $item) {
        if ($item[1] == '--div--' && $firstDivChecked & !$multicolumnAdded) {
            $sortedItems[] = [
                'LLL:EXT:multicolumn/locallang_db.xml:tx_multicolumn_multicolumn',
                'multicolumn',
                'EXT:multicolumn/tt_content_multicolumn.gif',
            ];
            $multicolumnAdded = true;
        }

        $firstDivChecked = true;
        $sortedItems[] = $item;
    }

    $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] = $sortedItems;
    unset($sortedItems, $firstDivChecked, $multicolumnAdded);
}

// Add CType multicolumn
$GLOBALS['TCA']['tt_content']['types']['multicolumn'] = [
    'showitem' => '--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general, hidden, header;;3;;2-2-2, linkToTop;;;;3-3-3, ' .
        '--div--;LLL:EXT:multicolumn/locallang_db.xml:tt_content.tx_multicolumn_tab.content, tx_multicolumn_items, ' .
        '--div--;LLL:EXT:multicolumn/locallang_db.xml:tt_content.tx_multicolumn_tab.config, pi_flexform, ' .
        '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime, fe_group',
];

$GLOBALS['TCA']['tt_content']['ctrl']['typeicons']['multicolumn'] = 'EXT:multicolumn/tt_content_multicolumn.gif';

// Add tx_multicolumn_parentid to tt_content table
$tempColumns = [
    'tx_multicolumn_parentid' => [
        'exclude' => 1,
        'label' => 'LLL:EXT:multicolumn/locallang_db.xml:tt_content.tx_multicolumn_parentid',
        'config' => [
            'type' => 'select',
            'foreign_table' => 'tt_content',
            'foreign_table_where' => 'AND tt_content.uid=###REC_FIELD_tx_multicolumn_parentid###',
            'itemsProcFunc' => 'tx_multicolumn_tceform->init',
            'multicolumnProc' => 'buildMulticolumnList',
            'items' => [
                ['', 0],
            ],
            'default' => 0,
            'size' => 1,
            'minitems' => 0,
            'maxitems' => 1,
            'wizards' => [
                '_PADDING' => 2,
                '_VERTICAL' => 1,
                'edit' => [
                    'type' => 'popup',
                    'title' => 'Edit',
                    'name' => 'wizard_edit',
                    'popup_onlyOpenIfSelected' => 1,
                    'icon' => 'edit2.gif',
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
$GLOBALS['TCA']['tt_content']['columns']['colPos']['config']['itemsProcFunc'] = 'tx_multicolumn_tceform->init';
$GLOBALS['TCA']['tt_content']['ctrl']['requestUpdate'] .= ',layoutKey,tx_multicolumn_parentid';
