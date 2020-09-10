<?php

declare(strict_types=1);

namespace IchHabRecht\Multicolumn\ContextMenu;

/*
 * This file is part of the TYPO3 Multicolumn project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read
 * LICENSE file that was distributed with this source code.
 */

use IchHabRecht\Multicolumn\Utility\DatabaseUtility;
use IchHabRecht\Multicolumn\Utility\MulticolumnUtility;
use TYPO3\CMS\Backend\ContextMenu\ItemProviders\RecordProvider;
use TYPO3\CMS\Core\Localization\LanguageService;

class ItemProvider extends RecordProvider
{
    /**
     * @param array $items
     * @return array
     */
    public function addItems(array $items): array
    {
        $this->initialize();
        if ($this->record['CType'] !== 'multicolumn'
            || !$this->canBePastedAfter()
        ) {
            return $items;
        }

        $languageService = $this->getLanguageService();

        $newItems = [];
        $defaultLabel = $languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:cm.pasteinto')
            ?: $languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:cm.pasteinto');
        $columns = DatabaseUtility::getNumberOfColumnsFromContainer($this->record['uid'], $this->record);
        for ($i = 0; $i < $columns; $i++) {
            $newItems['multicolumn-pasteinto-' . $i] = [
                'label' => $defaultLabel . ' ' . $languageService->sL('LLL:EXT:multicolumn/Resources/Private/Language/locallang.xlf:multicolumColumn.clickmenu')
                    . ' ' . ($i + 1),
                'iconIdentifier' => 'actions-document-paste-into',
                'callbackAction' => 'pasteIntoColumn',
            ];
        }

        $newItems = $this->prepareItems($newItems);

        $position = array_search('divider2', array_keys($items), true);
        $beginning = array_slice($items, 0, $position, true);
        $end = array_slice($items, $position - 1, null, true);

        $items = $beginning + $newItems + $end;

        return $items;
    }

    /**
     * @return bool
     */
    public function canHandle(): bool
    {
        return $this->table === 'tt_content';
    }

    /**
     * @param string $itemName
     * @param string $type
     * @return bool
     */
    protected function canRender(string $itemName, string $type): bool
    {
        return true;
    }

    /**
     * @param string $itemName
     * @return array
     */
    protected function getAdditionalAttributes(string $itemName): array
    {
        $parts = explode('-', $itemName);
        $column = (int)array_pop($parts);

        return [
            'data-callback-module' => 'TYPO3/CMS/Multicolumn/ContextMenuActions',
            'data-colpos' => (MulticolumnUtility::colPosStart + $column),
        ];
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 43;
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
