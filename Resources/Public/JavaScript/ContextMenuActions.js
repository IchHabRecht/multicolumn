/**
 * Module: TYPO3/CMS/Multicolumn/ContextMenuActions
 *
 * Adds the processing to insert a record into a multicolumn column
 */
define(['TYPO3/CMS/Backend/ContextMenuActions'], function(ContextMenuActions) {
    'use strict';

    /**
     * @param {string} table
     * @param {int} uid of the page
     */
    ContextMenuActions.pasteIntoColumn = function(table, uid) {
        var $this = $(this);
        var performPaste = function() {
            var url = '&CB[paste]=' + table + '%7C-' + uid
                + '&CB[pad]=normal&prErr=1&uPT=1'
                + '&CB[update][tx_multicolumn_parentid]=' + uid
                + '&CB[update][colPos]=' + $this.data('colpos')
                + '&CB[update][sys_language_uid]=' + $this.data('language-uid')
                + '&redirect=' + ContextMenuActions.getReturnUrl();

            top.TYPO3.Backend.ContentContainer.setUrl(
                top.TYPO3.settings.RecordCommit.moduleUrl + url
            );
        };
        if (!$this.data('title')) {
            performPaste();
            return;
        }
        var $modal = Modal.confirm(
            $this.data('title'),
            $this.data('message'),
            Severity.warning, [
                {
                    text: $(this).data('button-close-text') || TYPO3.lang['button.cancel'] || 'Cancel',
                    active: true,
                    btnClass: 'btn-default',
                    name: 'cancel'
                },
                {
                    text: $(this).data('button-ok-text') || TYPO3.lang['button.ok'] || 'OK',
                    btnClass: 'btn-warning',
                    name: 'ok'
                }
            ]);

        $modal.on('button.clicked', function(e) {
            if (e.target.name === 'ok') {
                performPaste();
            }
            Modal.dismiss();
        });
    };

    return ContextMenuActions;
});
