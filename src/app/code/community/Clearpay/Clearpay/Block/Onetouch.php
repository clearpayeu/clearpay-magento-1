<?php

class Clearpay_Clearpay_Block_Onetouch extends Mage_Core_Block_Template
{
    /**
     * Render the block
     *
     * @return string
     */
    protected function _toHtml()
    {
        if  ( Mage::getStoreConfigFlag('clearpay/payovertime_cart/show_onetouch')
                && Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Clearpay_Model_Method_Base::API_ENABLED_FIELD)
                && Mage::helper('clearpay/checkout')->noConflict()
                && Mage::getModel('clearpay/method_payovertime')->canUseForCheckoutSession()
                && $this->getTotalAmount() >= Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Clearpay_Model_Method_Base::API_MIN_ORDER_TOTAL_FIELD)
                && $this->getTotalAmount() <= Mage::getStoreConfig('payment/clearpaypayovertime/' . Clearpay_Clearpay_Model_Method_Base::API_MAX_ORDER_TOTAL_FIELD)
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
}
