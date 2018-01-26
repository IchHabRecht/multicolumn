tx_multicolumn {
    config {
        advancedLayouts {
            maxNumberOfColumns = 10
            makeEqualElementBoxHeight {
                includeFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    fixHeight = EXT:multicolumn/Resources/Public/JavaScript/makeEqualElementBoxHeight.js
                }
            }

            makeEqualElementColumnHeight {
                includeFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    fixColumnHeight = EXT:multicolumn/Resources/Public/JavaScript/makeEqualElementBoxColumnHeight.js
                }
            }
        }

        effectBox {
            # set effect key to only allow specific effect (eg. sudoSliderTabSliding,easyAccordion)
            enableEffects =
        }

        layoutPreset {
            # setlayout key to only allow specific layout (eg. effectBox,3)
            enableLayouts =
        }
    }

    layoutPreset {
        1 {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_layout.xlf:layout.1
            icon = EXT:multicolumn/Resources/Public/Icons/Layout/1.gif
            config {
                # include a specific css for your preset layout
                #layoutCss = EXT:multicolumn/Resources/Public/Icons/Layout/1.css

                # container settings
                # % or px (if empty default measure is %)
                #containerMeasure = %
                # if you want to specifie a fixed width for column
                #containerWidth =
                #disableStyles = 1

                # column settings
                # number of columns
                columns = 2

                #columnMeasure =
                #column width (optionSplit)
                columnWidth = 49.9

                # column margin (optionSplit) css string is allowed like (10px 0 0 15px)
                #columnMargin =
                # column padding (optionSplit) css string is allowed like (10px 0 0 15px)
                columnPadding = 0 6px 0 0 || 0 0 0 6px
                # disable auto image shrink
                #disableImageShrink

                # Force equal height for each multicolumn item
                #makeEqualElementBoxHeight = 1

                # Force equal height for each multicolumn column
                #makeEqualElementColumnHeight = 1

                # disable the calculation of image width => take the result from plugin.tx_multicolumn_pi1.columnWidth
                #disableAutomaticImageWidthCalculation = 1
            }
        }

        2 {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_layout.xlf:layout.2
            icon = EXT:multicolumn/Resources/Public/Icons/Layout/2.gif
            config {
                columns = 3
                columnWidth = 33.33
                columnPadding = 0 6px 0 0 || 0 6px || 0 0 0 6px
            }
        }

        3 {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_layout.xlf:layout.3
            icon = EXT:multicolumn/Resources/Public/Icons/Layout/3.gif
            config {
                columns = 4
                columnWidth = 25
                columnPadding = 0 6px 0 0 |*| 0 6px |*| 0 0 0 6px
            }
        }

        10 {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_layout.xlf:layout.10
            icon = EXT:multicolumn/Resources/Public/Icons/Layout/10.gif
            config {
                columns = 2
                # option split is allowed!
                columnWidth = 67 || 33
                columnPadding = 0 6px 0 0 || 0 0 0 6px
            }
        }

        effectBox {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_effects.xlf:effectBox
            icon = EXT:multicolumn/Resources/Public/Icons/Layout/effectSlider.gif
            config {
                columns = 1
            }
        }
    }

    effectBox {
        # jquery sudo slider plugin options: http://webbies.dk/Sudo%20Slider/settings.html
        sudoSliderTabSliding {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_effects.xlf:effectBox.effect.slidingTab
            config {
                effectBoxClass = sudoSlider
                jsFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    sudoSliderJs = EXT:multicolumn/Resources/Public/JavaScript/jquery.sudoSlider.min.js
                    sudoSliderEffectbox = EXT:multicolumn/Resources/Public/JavaScript/sudoSliderEffectbox.js
                }

                cssFiles {
                    sudoSlider = EXT:multicolumn/Resources/Public/Css/sudoSlider.css
                }

                defaultOptions (
					numeric: true
					,fade: false
					,controlsAttr: 'class="sudoControls"'
					,prevNext : false
					,convertHeadingToNavigation : true
					,insertAfter : false
                )
            }
        }

        sudoSliderTabFade {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_effects.xlf:effectBox.effect.fadingTab
            config {
                effectBoxClass = sudoSlider
                jsFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    sudoSliderJs = EXT:multicolumn/Resources/Public/JavaScript/jquery.sudoSlider.min.js
                    sudoSliderEffectbox = EXT:multicolumn/Resources/Public/JavaScript/sudoSliderEffectbox.js
                }

                cssFiles {
                    sudoSlider = EXT:multicolumn/Resources/Public/Css/sudoSlider.css
                }

                defaultOptions (
					numeric: true
					,fade: true
					,controlsAttr: 'class="sudoControls"'
					,prevNext : false
					,convertHeadingToNavigation : true
					,insertAfter : false
                )
            }
        }

        sudoSliderTabFade2 {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_effects.xlf:effectBox.effect.fadingTab2
            config {
                effectBoxClass = sudoSlider
                jsFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    sudoSliderJs = EXT:multicolumn/Resources/Public/JavaScript/jquery.sudoSlider.min.js
                    sudoSliderEffectbox = EXT:multicolumn/Resources/Public/JavaScript/sudoSliderEffectbox.js
                }

                cssFiles {
                    sudoSlider = EXT:multicolumn/Resources/Public/Css/sudoSlider.css
                }

                defaultOptions (
					numeric: true
					,fade: true
					,prevNext : false
					,insertAfter : false
					,controlsShow : false
					,auto: true
					,pause: '3000'
					,beforeAniFunc: function(t){
						jQuery(this).children('.effectBoxText').hide();
					}
					,afterAniFunc: function(t){
						jQuery(this).children('.effectBoxText').slideDown(400);
					}
                )
            }
        }

        easyAccordion {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_effects.xlf:effectBox.effect.accordion
            config {
                effectBoxClass = mcAccordion
                jsFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    easyAccordionJs = EXT:multicolumn/Resources/Public/JavaScript/jquery.easyAccordion.js
                    easyAccordionInit = EXT:multicolumn/Resources/Public/JavaScript/easyAccordionInit.js
                }

                cssFiles {
                    easyAccordion = EXT:multicolumn/Resources/Public/Css/easyAccordion.css
                }
            }
        }

        vAccordion {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_effects.xlf:effectBox.effect.vAccordion
            config {
                effectBoxClass = vAccordion
                jsFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    vAccordionJs = EXT:multicolumn/Resources/Public/JavaScript/vAccordion.js
                }

                cssFiles {
                    vAccordion = EXT:multicolumn/Resources/Public/Css/vAccordion.css
                }

                defaultOptions (
					showFirst: true
                )
            }
        }

        simpleTabs {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_effects.xlf:effectBox.effect.simpleTabs
            config {
                effectBoxClass = vAccordion
                jsFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    simpleTabs = EXT:multicolumn/Resources/Public/JavaScript/simpleTabs.js
                }

                cssFiles {
                    simpleTabs = EXT:multicolumn/Resources/Public/Css/simpleTabs.css
                }

                defaultOptions (
					fixHeight : false
                )
            }
        }

        roundabout {
            label = LLL:EXT:multicolumn/Resources/Private/Language/locallang_effects.xlf:effectBox.effect.roundabout
            config {
                effectBoxClass = roundabout
                jsFiles {
                    jQuery = EXT:multicolumn/Resources/Public/JavaScript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    roundabout = EXT:multicolumn/Resources/Public/JavaScript/jquery.roundabout.js
                    roundaboutEffectbox = EXT:multicolumn/Resources/Public/JavaScript/multicolumnImplementation.js
                }

                cssFiles {
                    sudoSlider = EXT:multicolumn/Resources/Public/Css/roundabout.css
                }

                defaultOptions (
					minScale: 0.4,
					startingChild : 'random',
					responsive : true,
					minOpacity: 0.8,
					maxOpacity: 1.0,
					triggerFocusEvents : false
                )
            }
        }
    }
}
