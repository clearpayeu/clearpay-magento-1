<?php

/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 *
 * @method string getPageType()
 * @method Clearpay_Clearpay_Block_Catalog_Installments setPageType(string $pageType)
 */
class Clearpay_Clearpay_Block_Catalog_Installments extends Mage_Core_Block_Template
{
    const XML_CONFIG_PREFIX = 'clearpay/payovertime_installments/';

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_PREFIX . 'enable_' . $this->getPageType());

    }

    public function getCssSelectors()
    {
        $selectors = Mage::getStoreConfig(self::XML_CONFIG_PREFIX . $this->getPageType() . '_price_block_selectors');
        return explode("\n", $selectors);
    }

    public function getHtmlTemplate()
    {
        $result = Mage::getStoreConfig(self::XML_CONFIG_PREFIX . $this->getPageType() . '_html_template');
        $result = str_replace(
            '{skin_url}',
            Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN),
            $result
        );
        return $result;
    }

    public function getMinPriceLimit()
    {
        if (Mage::getStoreConfigFlag(self::XML_CONFIG_PREFIX . 'check_order_total_limits')) {
            // min order total limit for Clearpay Pay Over Time payment method
            return (int)Mage::getStoreConfig('payment/clearpaypayovertime/min_order_total');
        } else {
            return 0;
        }
    }

    public function getMaxPriceLimit()
    {
        if (Mage::getStoreConfigFlag(self::XML_CONFIG_PREFIX . 'check_order_total_limits')) {
            // max order total limit for Clearpay Pay Over Time payment method
            return (int)Mage::getStoreConfig('payment/clearpaypayovertime/max_order_total');
        } else {
            return 0;
        }
    }

    public function getStoreConfigEnabled()
    {
        if (Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Clearpay_Model_Method_Base::API_ENABLED_FIELD)) {
            // plugin enabled / disabled
            return 1;
        } else {
            return 0;
        }
    }

    public function getInstallmentsAmount()
    {
        return (int)Mage::getStoreConfig('payment/clearpaypayovertime/installments_amount');
    }

    public function getRegionSpecificText()
    {
        if (Mage::app()->getStore()->getCurrentCurrencyCode() == 'GBP') {
            return 'fortnightly with';
        }
    }

    public function getJsConfig()
    {
        return array(
            'selectors'          => $this->getCssSelectors(),
            'template'           => $this->getHtmlTemplate(),
            'priceSubstitution'  => '{price_here}',
            'regionSpecific'     => '{region_specific_text}',
            'regionText'         => $this->getRegionSpecificText(),
            'minPriceLimit'      => $this->getMinPriceLimit(),
            'maxPriceLimit'      => $this->getMaxPriceLimit(),
            'installmentsAmount' => $this->getInstallmentsAmount(),
            'clearpayEnabled'    => $this->getStoreConfigEnabled(),
            'priceFormat'        => Mage::app()->getLocale()->getJsPriceFormat(),
            'currencySymbol'     => Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol(),
            'className'          => 'clearpay-installments-amount'
        );
    }

    protected function _toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        } else {
            return parent::_toHtml();
        }
    }

}