<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "multicolumn".
 *
 * Auto generated 11-09-2020 09:43
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
  'version' => '5.1.1',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '8.7.0-10.4.99',
    ),
    'conflicts' => 
    array (
      'templavoila' => '0.0.0-0.0.0',
      'templavoilaplus' => '0.0.0-0.0.0',
    ),
    'suggests' => 
    array (
    ),
  ),
  '_md5_values_when_last_written' => 'a:68:{s:9:"ChangeLog";s:4:"710c";s:7:"LICENSE";s:4:"b234";s:9:"README.md";s:4:"7090";s:13:"composer.json";s:4:"15c4";s:13:"composer.lock";s:4:"e75f";s:12:"ext_icon.gif";s:4:"925c";s:17:"ext_localconf.php";s:4:"5a09";s:14:"ext_tables.sql";s:4:"ad91";s:16:"phpunit.xml.dist";s:4:"041c";s:24:"sonar-project.properties";s:4:"561d";s:36:"Classes/ContextMenu/ItemProvider.php";s:4:"4761";s:41:"Classes/Controller/AbstractController.php";s:4:"2414";s:42:"Classes/Controller/ContainerController.php";s:4:"f01f";s:52:"Classes/DataProcessing/ContainerSectionProcessor.php";s:4:"4f5d";s:42:"Classes/Evaluation/MaxColumnsEvaluator.php";s:4:"2aad";s:56:"Classes/Form/FormDataProvider/ContainerItemsProvider.php";s:4:"af8d";s:53:"Classes/Form/FormDataProvider/TcaFlexEffectivePid.php";s:4:"44db";s:33:"Classes/Hooks/DataHandlerHook.php";s:4:"e800";s:36:"Classes/Hooks/PageLayoutViewHook.php";s:4:"b030";s:33:"Classes/Hooks/WizardItemsHook.php";s:4:"85bb";s:35:"Classes/Utility/DatabaseUtility.php";s:4:"deb5";s:35:"Classes/Utility/FlexFormUtility.php";s:4:"35c9";s:38:"Classes/Utility/MulticolumnUtility.php";s:4:"a6d7";s:38:"Configuration/FlexForm/flexform_ds.xml";s:4:"aa18";s:44:"Configuration/TCA/Overrides/sys_template.php";s:4:"c840";s:42:"Configuration/TCA/Overrides/tt_content.php";s:4:"8bb2";s:57:"Configuration/TSconfig/NewContentElementWizard.typoscript";s:4:"aee8";s:45:"Configuration/TSconfig/multicolumn.typoscript";s:4:"af84";s:48:"Configuration/TypoScript/Container/constants.txt";s:4:"d41d";s:44:"Configuration/TypoScript/Container/setup.txt";s:4:"b464";s:46:"Configuration/TypoScript/Sitemap/constants.txt";s:4:"d41d";s:42:"Configuration/TypoScript/Sitemap/setup.txt";s:4:"b836";s:43:"Resources/Private/Language/de.locallang.xlf";s:4:"f4ee";s:46:"Resources/Private/Language/de.locallang_db.xlf";s:4:"af7c";s:50:"Resources/Private/Language/de.locallang_layout.xlf";s:4:"bf8d";s:47:"Resources/Private/Language/de.locallang_pi1.xlf";s:4:"72f1";s:40:"Resources/Private/Language/locallang.xlf";s:4:"747a";s:53:"Resources/Private/Language/locallang_csh_flexform.xlf";s:4:"6ff7";s:43:"Resources/Private/Language/locallang_db.xlf";s:4:"66b4";s:47:"Resources/Private/Language/locallang_layout.xlf";s:4:"2b23";s:44:"Resources/Private/Language/locallang_pi1.xlf";s:4:"8734";s:60:"Resources/Private/Templates/ContentElements/MenuSection.html";s:4:"f40e";s:65:"Resources/Private/Templates/ContentElements/MenuSectionPages.html";s:4:"cef0";s:32:"Resources/Public/Css/backend.css";s:4:"74de";s:38:"Resources/Public/Icons/multicolumn.gif";s:4:"1863";s:49:"Resources/Public/Icons/tt_content_multicolumn.gif";s:4:"849c";s:35:"Resources/Public/Icons/Layout/1.gif";s:4:"e38a";s:36:"Resources/Public/Icons/Layout/10.gif";s:4:"7286";s:35:"Resources/Public/Icons/Layout/2.gif";s:4:"c1a4";s:35:"Resources/Public/Icons/Layout/3.gif";s:4:"5f77";s:46:"Resources/Public/Icons/Layout/effectSlider.gif";s:4:"c982";s:38:"Resources/Public/Images/roundabout.png";s:4:"c9b2";s:49:"Resources/Public/JavaScript/ContextMenuActions.js";s:4:"005c";s:37:"Resources/Public/JavaScript/jQuery.js";s:4:"c80a";s:62:"Resources/Public/JavaScript/makeEqualElementBoxColumnHeight.js";s:4:"ac80";s:56:"Resources/Public/JavaScript/makeEqualElementBoxHeight.js";s:4:"0aff";s:28:"Tests/FunctionalBaseTest.php";s:4:"e652";s:43:"Tests/Functional/Fixtures/pages_overlay.xml";s:4:"d2a7";s:40:"Tests/Functional/Fixtures/tt_content.xml";s:4:"5897";s:57:"Tests/Functional/Fixtures/tt_content_nested_container.xml";s:4:"42a4";s:51:"Tests/Functional/Hooks/DefaultLanguage/CopyTest.php";s:4:"9b4e";s:53:"Tests/Functional/Hooks/DefaultLanguage/DeleteTest.php";s:4:"95f0";s:51:"Tests/Functional/Hooks/DefaultLanguage/MoveTest.php";s:4:"bcc0";s:50:"Tests/Functional/Hooks/DefaultLanguage/NewTest.php";s:4:"180e";s:52:"Tests/Functional/Hooks/DefaultLanguage/PasteTest.php";s:4:"8216";s:47:"Tests/Functional/Hooks/Translation/CopyTest.php";s:4:"14e0";s:51:"Tests/Functional/Hooks/Translation/LocalizeTest.php";s:4:"83f5";s:14:"doc/manual.sxw";s:4:"a391";}',
);

