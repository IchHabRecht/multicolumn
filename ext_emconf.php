<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "multicolumn".
 *
 * Auto generated 05-12-2017 13:57
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'multicolumn',
  'description' => 'The Multicolumn extension expands TYPO3 with a new content element called Multicolumn. With the Multicolumn content element it has never been easier to do multicolumn layouts with TYPO3',
  'category' => 'fe',
  'author' => 'Nicole Cordes (originally developed by snowflake productions gmbh)',
  'author_email' => 'typo3@cordes.co',
  'author_company' => 'biz-design',
  'state' => 'stable',
  'uploadfolder' => 0,
  'createDirs' => '',
  'clearcacheonload' => 1,
  'version' => '4.0.2',
  'constraints' =>
  array (
    'depends' => 
    array (
      'typo3' => '7.6.0-8.7.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  '_md5_values_when_last_written' => 'a:78:{s:9:"ChangeLog";s:4:"aa34";s:13:"composer.json";s:4:"f35e";s:13:"composer.lock";s:4:"78ff";s:21:"ext_conf_template.txt";s:4:"e72d";s:12:"ext_icon.gif";s:4:"925c";s:17:"ext_localconf.php";s:4:"3dda";s:14:"ext_tables.php";s:4:"5779";s:14:"ext_tables.sql";s:4:"ad91";s:15:"flexform_ds.xml";s:4:"ea83";s:13:"locallang.xml";s:4:"4cea";s:26:"locallang_csh_flexform.xml";s:4:"7fd9";s:16:"locallang_db.xml";s:4:"cdde";s:36:"Classes/ContextMenu/ItemProvider.php";s:4:"d9e0";s:53:"Classes/Form/FormDataProvider/TcaFlexEffectivePid.php";s:4:"a952";s:33:"Classes/Hooks/WizardItemsHook.php";s:4:"6bee";s:42:"Configuration/TCA/Overrides/tt_content.php";s:4:"544d";s:49:"Configuration/TSconfig/NewContentElementWizard.ts";s:4:"7dda";s:37:"Configuration/TSconfig/multicolumn.ts";s:4:"f772";s:49:"Resources/Public/Icons/tt_content_multicolumn.gif";s:4:"849c";s:49:"Resources/Public/JavaScript/ContextMenuActions.js";s:4:"44c0";s:28:"Tests/FunctionalBaseTest.php";s:4:"92dd";s:40:"Tests/Functional/Fixtures/tt_content.xml";s:4:"5897";s:57:"Tests/Functional/Fixtures/tt_content_nested_container.xml";s:4:"42a4";s:51:"Tests/Functional/Hooks/DefaultLanguage/CopyTest.php";s:4:"b2e7";s:53:"Tests/Functional/Hooks/DefaultLanguage/DeleteTest.php";s:4:"606b";s:50:"Tests/Functional/Hooks/DefaultLanguage/NewTest.php";s:4:"f5d7";s:52:"Tests/Functional/Hooks/DefaultLanguage/PasteTest.php";s:4:"a089";s:47:"Tests/Functional/Hooks/Translation/CopyTest.php";s:4:"57e9";s:51:"Tests/Functional/Hooks/Translation/LocalizeTest.php";s:4:"90cf";s:14:"doc/manual.sxw";s:4:"a391";s:44:"hooks/class.tx_multicolumn_alt_clickmenu.php";s:4:"1e79";s:41:"hooks/class.tx_multicolumn_cms_layout.php";s:4:"b081";s:38:"hooks/class.tx_multicolumn_db_list.php";s:4:"959d";s:43:"hooks/class.tx_multicolumn_t3lib_befunc.php";s:4:"7049";s:38:"hooks/class.tx_multicolumn_tcemain.php";s:4:"6493";s:50:"hooks/class.tx_multicolumn_tt_content_drawItem.php";s:4:"a4d7";s:31:"lib/class.tx_multicolumn_db.php";s:4:"c152";s:32:"lib/class.tx_multicolumn_div.php";s:4:"e88e";s:41:"lib/class.tx_multicolumn_emconfhelper.php";s:4:"5a7e";s:37:"lib/class.tx_multicolumn_flexform.php";s:4:"22ab";s:36:"lib/class.tx_multicolumn_pi_base.php";s:4:"18e4";s:37:"lib/class.tx_multicolumn_tce_eval.php";s:4:"f3ae";s:36:"lib/class.tx_multicolumn_tceform.php";s:4:"9bd9";s:14:"pi1/ce_wiz.gif";s:4:"1863";s:32:"pi1/class.tx_multicolumn_pi1.php";s:4:"6977";s:17:"pi1/locallang.xml";s:4:"62b9";s:24:"pi1/static/defaultTS.txt";s:4:"5bab";s:20:"pi1/static/setup.txt";s:4:"76c8";s:46:"pi_sitemap/class.tx_multicolumn_pi_sitemap.php";s:4:"0107";s:27:"pi_sitemap/static/setup.txt";s:4:"542c";s:21:"res/backend/style.css";s:4:"74de";s:25:"res/effects/locallang.xml";s:4:"0a65";s:46:"res/effects/easyAccordion/easyAccordionInit.js";s:4:"0d69";s:49:"res/effects/easyAccordion/jquery.easyAccordion.js";s:4:"efc6";s:35:"res/effects/easyAccordion/style.css";s:4:"ac20";s:43:"res/effects/roundabout/jquery.roundabout.js";s:4:"8032";s:51:"res/effects/roundabout/multicolumnImplementation.js";s:4:"fefa";s:37:"res/effects/roundabout/roundabout.css";s:4:"46c3";s:33:"res/effects/roundabout/shadow.png";s:4:"5a86";s:34:"res/effects/roundabout/sprites.png";s:4:"c9b2";s:36:"res/effects/simpleTabs/simpleTabs.js";s:4:"7f0b";s:32:"res/effects/simpleTabs/style.css";s:4:"c01e";s:47:"res/effects/sudoSlider/jquery.sudoSlider.min.js";s:4:"cc1e";s:32:"res/effects/sudoSlider/style.css";s:4:"4fc9";s:45:"res/effects/sudoSlider/sudoSliderEffectbox.js";s:4:"b54c";s:42:"res/effects/sudoSlider/images/btn_next.gif";s:4:"a1d5";s:42:"res/effects/sudoSlider/images/btn_prev.gif";s:4:"7301";s:32:"res/effects/vAccordion/style.css";s:4:"fb4f";s:36:"res/effects/vAccordion/vAccordion.js";s:4:"9a9f";s:24:"res/javascript/jQuery.js";s:4:"c80a";s:16:"res/layout/1.gif";s:4:"e38a";s:17:"res/layout/10.gif";s:4:"7286";s:16:"res/layout/2.gif";s:4:"c1a4";s:16:"res/layout/3.gif";s:4:"5f77";s:27:"res/layout/effectSlider.gif";s:4:"c982";s:24:"res/layout/locallang.xml";s:4:"f2be";s:45:"res/layout/makeEqualElementBoxColumnHeight.js";s:4:"ac80";s:39:"res/layout/makeEqualElementBoxHeight.js";s:4:"0aff";}',
);

