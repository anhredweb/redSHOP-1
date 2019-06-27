<?php
/**
 * @package     redSHOP
 * @subpackage  Steps
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Administrator\plugins;
use AcceptanceTester\AdminManagerJoomla3Steps;
use PluginManagerJoomla3Page;

/**
 * Class PluginPaymentManagerJoomla
 * @since 2.1.2
 */
class PluginPaymentManagerJoomla extends AdminManagerJoomla3Steps
{
	/**
	 * @param $type
	 * @throws \Exception
	 * @since 2.1.2
	 */
	public function disableType($type)
	{
		$I = $this;
		$I->amOnPage(PluginManagerJoomla3Page::$URL);
		$I->checkForPhpNoticesOrWarnings();
		$I->waitForElementVisible(PluginManagerJoomla3Page::$btnClear, 10);
		$I->click(PluginManagerJoomla3Page:: $btnClear);
		$I->waitForElement(PluginManagerJoomla3Page::$btnSearchTool, 30);
		$I->click(PluginManagerJoomla3Page::$btnSearchTool);
		$I->waitForElementVisible(PluginManagerJoomla3Page::$fieldType, 60);
		$I->click(PluginManagerJoomla3Page::$fieldType);
		$I->waitForElementVisible(PluginManagerJoomla3Page::$inputType, 30);
		$I->fillField(PluginManagerJoomla3Page::$inputType, $type);
		$I->pressKey(PluginManagerJoomla3Page::$inputType, \Facebook\WebDriver\WebDriverKeys::ENTER);
		$I->checkAllResults();
		$I->click(PluginManagerJoomla3Page::$btnDisable);
		$I->waitForText(PluginManagerJoomla3Page::$messageDisable,30, PluginManagerJoomla3Page:: $idInstallSuccess);
	}

	/**
	 * @param $pluginName
	 * @param $vendorID
	 * @param $secretWord
	 * @throws \Exception
	 */
	public function config2CheckoutPlugin($pluginName, $vendorID, $secretWord)
	{
		$I = $this;
		$I->amOnPage(PluginManagerJoomla3Page:: $URL);
		$I->searchForItem($pluginName);
		$pluginManagerPage = new PluginManagerJoomla3Page;
		$I->waitForElement($pluginManagerPage->searchResultPluginName($pluginName), 30);
		$I->waitForElementVisible(PluginManagerJoomla3Page:: $searchResultRow, 30);
		$I->waitForText($pluginName, 30, PluginManagerJoomla3Page:: $searchResultRow);
		$I->click($pluginName);
		$I->waitForElementVisible( PluginManagerJoomla3Page:: $vendorID ,30);
		$I->fillField( PluginManagerJoomla3Page:: $vendorID , $vendorID);
		$I->fillField(PluginManagerJoomla3Page::$secretWords, $secretWord);
		$I->clickToolbarButton(PluginManagerJoomla3Page:: $buttonSaveClose);
		$I->waitForText(PluginManagerJoomla3Page::$pluginSaveSuccessMessage, 30, PluginManagerJoomla3Page:: $idInstallSuccess);
	}

	/**
	 * @param $pluginName
	 * @param $accessId
	 * @param $transactionKey
	 * @param $md5Key
	 * @throws \Exception
	 * @since 2.1.2
	 */
	public function configAuthorizeDPMPlugin($pluginName, $accessId, $transactionKey, $md5Key)
	{
		$I = $this;
		$I->amOnPage(PluginManagerJoomla3Page::$URL);
		$I->checkForPhpNoticesOrWarnings();
		$I->searchForItem($pluginName);
		$pluginManagerPage = new PluginManagerJoomla3Page;
		$I->waitForElement($pluginManagerPage->searchResultPluginName($pluginName), 30);
		$I->checkExistenceOf($pluginName);
		$I->waitForText($pluginName, 30, PluginManagerJoomla3Page:: $searchResultRow);
		$I->waitForElementVisible($pluginManagerPage->searchResultPluginName($pluginName), 30);
		$I->click($pluginName);
		$I->waitForElementVisible(PluginManagerJoomla3Page::$fieldAccessId, 60);
		$I->fillField(PluginManagerJoomla3Page:: $fieldAccessId, $accessId);
		$I->fillField(PluginManagerJoomla3Page:: $fieldTransactionID, $transactionKey);
		$I->fillField(PluginManagerJoomla3Page:: $fieldMd5Key, $md5Key);
		$I->waitForElementVisible(PluginManagerJoomla3Page::$fieldTestMode, 60);
		$I->click( PluginManagerJoomla3Page::$fieldTestMode);

		// Choosing Test Mode to Yes
		$I->waitForElementVisible(PluginManagerJoomla3Page::$optionTestModeYes, 60);
		$I->click(PluginManagerJoomla3Page::$optionTestModeYes);
		$I->clickToolbarButton(PluginManagerJoomla3Page:: $buttonSaveClose);
		$I->waitForText(PluginManagerJoomla3Page::$pluginSaveSuccessMessage, 30, PluginManagerJoomla3Page:: $idInstallSuccess);
	}
}