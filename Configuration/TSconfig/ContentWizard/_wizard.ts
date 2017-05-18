# add multicolumn container to common tab
mod {
    wizards.newContentElement.wizardItems.common {
        elements {
            multicolumn {
                iconIdentifier = tx-multicolumn-wizard-icon
                title = LLL:EXT:multicolumn/locallang.xml:pi1_title
                description = LLL:EXT:multicolumn/locallang.xml:pi1_plus_wiz_description
                tt_content_defValues {
                    CType = multicolumn
                }
            }
        }
        show := addToList(multicolumn)
    }
}
