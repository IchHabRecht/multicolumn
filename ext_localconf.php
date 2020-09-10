<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

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
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\IchHabRecht\Multicolumn\Evaluation\MaxColumnsEvaluator::class] = '';

//add page TSconfig
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/Configuration/TSconfig/multicolumn.typoscript">');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:multicolumn/Configuration/TSconfig/NewContentElementWizard.typoscript">');

// Add frontend plugin
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
    'multicolumn',
    'Classes/Controller/ContainerController.php',
    '_pi1',
    'CType',
    true
);

// Add dataProvider for FormEngine
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['flexFormSegment'][\IchHabRecht\Multicolumn\Form\FormDataProvider\TcaFlexEffectivePid::class] = [
    'depends' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowDefaultValues::class,
    ],
    'before' => [
        \TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class,
    ],
];

// Add item provider for context menu
$GLOBALS['TYPO3_CONF_VARS']['BE']['ContextMenu']['ItemProviders'][1492078309] = \IchHabRecht\Multicolumn\ContextMenu\ItemProvider::class;

// Register newContentElementWizard icon
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
    'tx-multicolumn-wizard-icon',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    [
        'source' => 'EXT:multicolumn/Resources/Public/Icons/multicolumn.gif',
    ]
);
$iconRegistry->registerIcon(
    'mimetypes-x-content-multicolumn',
    \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
    [
        'source' => 'EXT:multicolumn/Resources/Public/Icons/tt_content_multicolumn.gif',
    ]
);
