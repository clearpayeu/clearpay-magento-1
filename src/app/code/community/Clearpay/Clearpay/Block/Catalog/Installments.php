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

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        $product = $this->_getData('product');
        if (!$product) {
            $product = Mage::registry('product');
        }
        return $product;
    }

    public function isEnabled()
    {
        $product = $this->getProduct();
        return Mage::getStoreConfigFlag(self::XML_CONFIG_PREFIX . 'enable_' . $this->getPageType())
            && Mage::helper('clearpay/checkout')->noConflict()
            && Mage::getModel('clearpay/method_payovertime')->canUseForProduct($product)
            && !$product->isGrouped();
    }

    public function getCssSelectors()
    {
        $selectors = Mage::getStoreConfig(self::XML_CONFIG_PREFIX . $this->getPageType() . '_price_block_selectors');
        return explode("\n", $selectors);
    }

    public function getHtmlTemplate()
    {
        ob_start();
?>
<div style="position: relative; font-style: italic; line-height: 1.4;" class="clearpay-installments">
    or 4 interest-free payments of {price_here} with<br/>
    <img src="https://static.afterpay.com/integration/logo-clearpay-colour-79x15@2x.png" style="width: 76px; vertical-align: middle; display: inline;" />
    <a href="#clearpay-what-is-modal" class="clearpay-what-is-modal-trigger">Learn more</a>
</div>
<style type="text/css">.price-box.ciq_price_box .ciq_view_shipping{margin-top:35px}</style>
<?php
        return ob_get_clean();
    }

    public function getMinPriceLimit()
    {
        if (Mage::getStoreConfigFlag(self::XML_CONFIG_PREFIX . 'check_order_total_limits')) {
            // min order total limit for Clearpay Pay Over Time payment method
            return (float)Mage::getStoreConfig('payment/clearpaypayovertime/min_order_total');
        } else {
            return 0;
        }
    }

    public function getMaxPriceLimit()
    {
        if (Mage::getStoreConfigFlag(self::XML_CONFIG_PREFIX . 'check_order_total_limits')) {
            // max order total limit for Clearpay Pay Over Time payment method
            return (float)Mage::getStoreConfig('payment/clearpaypayovertime/max_order_total');
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

    public function getJsConfig()
    {
        return array(
            'selectors'          => $this->getCssSelectors(),
            'template'           => $this->getHtmlTemplate(),
            'priceSubstitution'  => '{price_here}',
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
