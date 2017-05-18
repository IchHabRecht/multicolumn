tx_multicolumn {
    config {
        advancedLayouts {
            maxNumberOfColumns = 10
            makeEqualElementBoxHeight {
                includeFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    fixHeight = EXT:multicolumn/res/layout/makeEqualElementBoxHeight.js
                }
            }

            makeEqualElementColumnHeight {
                includeFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }

                    fixColumnHeight = EXT:multicolumn/res/layout/makeEqualElementBoxColumnHeight.js
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
            label = LLL:EXT:multicolumn/res/layout/locallang.xml:layout.1
            icon = EXT:multicolumn/res/layout/1.gif
            config {
                # include a specific css for your preset layout
                #layoutCss = EXT:multicolumn/res/layout/1.css

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
            label = LLL:EXT:multicolumn/res/layout/locallang.xml:layout.2
            icon = EXT:multicolumn/res/layout/2.gif
            config {
                columns = 3
                columnWidth = 33.33
                columnPadding = 0 6px 0 0 || 0 6px || 0 0 0 6px
            }

        }
        3 {
            label = LLL:EXT:multicolumn/res/layout/locallang.xml:layout.3
            icon = EXT:multicolumn/res/layout/3.gif
            config {
                columns = 4
                columnWidth = 25
                columnPadding = 0 6px 0 0 |*| 0 6px |*| 0 0 0 6px
            }

        }

        10 {
            label = LLL:EXT:multicolumn/res/layout/locallang.xml:layout.10
            icon = EXT:multicolumn/res/layout/10.gif
            config {
                columns = 2
                # option split is allowed!
                columnWidth = 67 || 33
                columnPadding = 0 6px 0 0 || 0 0 0 6px
            }
        }

        effectBox {
            label = LLL:EXT:multicolumn/res/effects/locallang.xml:effectBox
            icon = EXT:multicolumn/res/layout/effectSlider.gif
            config {
                columns = 1
            }
        }
    }

    effectBox {
        # jquery sudo slider plugin options: http://webbies.dk/Sudo%20Slider/settings.html
        sudoSliderTabSliding {
            label = LLL:EXT:multicolumn/res/effects/locallang.xml:effectBox.effect.slidingTab
            config {
                effectBoxClass = sudoSlider
                jsFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }
                    sudoSliderJs = EXT:multicolumn/res/effects/sudoSlider/jquery.sudoSlider.min.js
                    sudoSliderEffectbox = EXT:multicolumn/res/effects/sudoSlider/sudoSliderEffectbox.js
                }
                cssFiles {
                    sudoSlider = EXT:multicolumn/res/effects/sudoSlider/style.css
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
            label = LLL:EXT:multicolumn/res/effects/locallang.xml:effectBox.effect.fadingTab
            config {
                effectBoxClass = sudoSlider
                jsFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }
                    sudoSliderJs = EXT:multicolumn/res/effects/sudoSlider/jquery.sudoSlider.min.js
                    sudoSliderEffectbox = EXT:multicolumn/res/effects/sudoSlider/sudoSliderEffectbox.js
                }
                cssFiles {
                    sudoSlider = EXT:multicolumn/res/effects/sudoSlider/style.css
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
            label = LLL:EXT:multicolumn/res/effects/locallang.xml:effectBox.effect.fadingTab2
            config {
                effectBoxClass = sudoSlider
                jsFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }
                    sudoSliderJs = EXT:multicolumn/res/effects/sudoSlider/jquery.sudoSlider.min.js
                    sudoSliderEffectbox = EXT:multicolumn/res/effects/sudoSlider/sudoSliderEffectbox.js
                }
                cssFiles {
                    sudoSlider = EXT:multicolumn/res/effects/sudoSlider/style.css
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
            label = LLL:EXT:multicolumn/res/effects/locallang.xml:effectBox.effect.accordion
            config {
                effectBoxClass = mcAccordion
                jsFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }
                    easyAccordionJs = EXT:multicolumn/res/effects/easyAccordion/jquery.easyAccordion.js
                    easyAccordionInit = EXT:multicolumn/res/effects/easyAccordion/easyAccordionInit.js
                }
                cssFiles {
                    easyAccordion = EXT:multicolumn/res/effects/easyAccordion/style.css
                }
            }
        }

        vAccordion {
            label = LLL:EXT:multicolumn/res/effects/locallang.xml:effectBox.effect.vAccordion
            config {
                effectBoxClass = vAccordion
                jsFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }
                    vAccordionJs = EXT:multicolumn/res/effects/vAccordion/vAccordion.js
                }
                cssFiles {
                    vAccordion = EXT:multicolumn/res/effects/vAccordion/style.css
                }
                defaultOptions (
					showFirst: true
                )
            }
        }

        simpleTabs {
            label = LLL:EXT:multicolumn/res/effects/locallang.xml:effectBox.effect.simpleTabs
            config {
                effectBoxClass = vAccordion
                jsFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }
                    simpleTabs = EXT:multicolumn/res/effects/simpleTabs/simpleTabs.js
                }
                cssFiles {
                    simpleTabs = EXT:multicolumn/res/effects/simpleTabs/style.css
                }

                defaultOptions (
					fixHeight : false
                )
            }
        }

        roundabout {
            label = LLL:EXT:multicolumn/res/effects/locallang.xml:effectBox.effect.roundabout
            config {
                effectBoxClass = roundabout
                jsFiles {
                    jQuery = EXT:multicolumn/res/javascript/jQuery.js
                    jQuery {
                        priority = 1
                    }
                    roundabout = EXT:multicolumn/res/effects/roundabout/jquery.roundabout.js
                    roundaboutEffectbox = EXT:multicolumn/res/effects/roundabout/multicolumnImplementation.js
                }
                cssFiles {
                    sudoSlider = EXT:multicolumn/res/effects/roundabout/roundabout.css
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
