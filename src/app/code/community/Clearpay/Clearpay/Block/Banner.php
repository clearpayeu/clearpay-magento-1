<?php

/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

use Clearpay_Clearpay_Model_Method_Base as Clearpay_Base;

class Clearpay_Clearpay_Block_Banner extends Mage_Core_Block_Template
{
    const XML_CONFIG_PREFIX = 'clearpay/banner/';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_PREFIX . 'enabled');
    }

    /**
     * @param string $scriptUrl
     * @param bool   $addModuleVersion
     *
     * @return string
     */
    public function getScriptHtml($scriptUrl, $addModuleVersion = true)
    {
        if ($addModuleVersion) {
            $scriptUrl .= "?v=" . $this->getModuleVersion();
        }

        return "document.write('<script src=\"" . $scriptUrl . "\">" . '<\/script>\');';
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        /** @var Mage_Core_Model_Config_Element $moduleConfig */
        $moduleConfig = Mage::getConfig()->getModuleConfig($this->getModuleName());
        return (string)$moduleConfig->version;
    }

    public function getJsLibrary()
    {
        $apiMode = Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Base::API_MODE_CONFIG_FIELD);

        if ($apiMode == 'production') {
            $url = 'https://js.afterpay.com/afterpay-1.x.js';
        } else {
            $url = 'https://js.sandbox.afterpay.com/afterpay-1.x.js';
        }

        return $url;
    }

    public function getJsLocale()
    {
        $locale = 'en_GB';
        $currency = Mage::app()->getStore()->getCurrentCurrencyCode();

        if (array_key_exists($currency, Clearpay_Base::CURRENCY_PROPERTIES)){
            $locale = Clearpay_Base::CURRENCY_PROPERTIES[$currency]['jsLocale'];
        }

        return $locale;
    }

}
