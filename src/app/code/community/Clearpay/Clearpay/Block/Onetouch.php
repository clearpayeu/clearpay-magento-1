<?php

use Clearpay_Clearpay_Model_Method_Base as Clearpay_Base;
use Clearpay_Clearpay_Model_System_Config_Source_CartMode as CartMode;
use Clearpay_Clearpay_Model_System_Config_Source_ApiMode as ApiMode;

class Clearpay_Clearpay_Block_Onetouch extends Mage_Core_Block_Template
{
    /**
     * Render the block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ( Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Base::API_ENABLED_FIELD) &&
            Mage::getStoreConfig('clearpay/payovertime_cart/show_onetouch') != CartMode::NO &&
            Mage::helper('clearpay/checkout')->noConflict() &&
            Mage::getModel('clearpay/method_payovertime')->canUseForCheckoutSession() &&
            $this->isQuoteWithinLimits()
        ) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    /**
     * Calculate how much each instalment will cost
     *
     * @return string
     */
    public function getInstalmentAmount()
    {
        return Mage::helper('clearpay')->calculateInstalment();
    }

    /**
     * Calculate the final value of the transaction
     *
     * @return string
     */
    public function getTotalAmount()
    {
        return Mage::helper('clearpay')->calculateTotal();
    }

    public function isQuoteWithinLimits()
    {
        $total = $this->getTotalAmount();
        $min = Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Base::API_MIN_ORDER_TOTAL_FIELD);
        $max = Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Base::API_MAX_ORDER_TOTAL_FIELD);

        return ($total > 0 && $total >= $min && $total <= $max);
    }

    public function isExpress()
    {
        return Mage::getStoreConfig('clearpay/payovertime_cart/show_onetouch') == CartMode::EXPRESS;
    }

    public function isShippingRequired()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        return !$quote->isVirtual();
    }

    public function getCountryCode()
    {
        $countryCode = '';
        $currency = ApiMode::getCurrencyCode();

        if (array_key_exists($currency, Clearpay_Base::CURRENCY_PROPERTIES)){
            $countryCode = Clearpay_Base::CURRENCY_PROPERTIES[$currency]['jsCountry'];
        }

        return $countryCode;
    }

    public function getJsUrl()
    {
        $apiMode = Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Base::API_MODE_CONFIG_FIELD);
        $settings = ApiMode::getEnvironmentSettings($apiMode);
        $key = urlencode(Mage::getStoreConfig('clearpay/payovertime_cart/express_key'));

        return $settings[ApiMode::KEY_WEB_URL] . 'afterpay.js?merchant_key=' . $key;
    }
}
