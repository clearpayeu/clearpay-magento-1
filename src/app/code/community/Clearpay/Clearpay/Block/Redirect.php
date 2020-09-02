<?php

/**
 * Clearpay payment redirect block
 *
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/**
 * Class Clearpay_Clearpay_Block_Form_Abstract
 *
 * @method Clearpay_Clearpay_Block_Redirect setOrderToken(string $token);
 * @method string getOrderToken();
 * @method Clearpay_Clearpay_Block_Redirect setReturnUrl(string $url);
 * @method Clearpay_Clearpay_Block_Redirect setRedirectJsUrl(string $url)
 */

use Clearpay_Clearpay_Model_Method_Base as Clearpay_Base;

class Clearpay_Clearpay_Block_Redirect extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('clearpay/redirect.phtml');
    }

    /**
     * @return string
     */
    public function getCancelOrderUrl()
    {
        return $this->getUrl('clearpay/payment/cancel', array('_secure' => true));
    }

    /**
     * Get the return URL of the Clearpay, will return false if using API V1
     * @return string | null
     */
    public function getReturnUrl()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getRedirectJsUrl()
    {
        $apiMode      = Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Base::API_MODE_CONFIG_FIELD);
        $settings     = Clearpay_Clearpay_Model_System_Config_Source_ApiMode::getEnvironmentSettings($apiMode);

        return $settings[Clearpay_Clearpay_Model_System_Config_Source_ApiMode::KEY_WEB_URL] . 'afterpay.js';
    }
    /**
     * @return Array
     */
    public function getCountryCode()
    {
        $countryCode = '';
        $currency = Clearpay_Clearpay_Model_System_Config_Source_ApiMode::getCurrencyCode();

        if (array_key_exists($currency, Clearpay_Base::CURRENCY_PROPERTIES)){
            $countryCode = Clearpay_Base::CURRENCY_PROPERTIES[$currency]['jsCountry'];
        }

        return array("countryCode" => $countryCode);
    }
}
