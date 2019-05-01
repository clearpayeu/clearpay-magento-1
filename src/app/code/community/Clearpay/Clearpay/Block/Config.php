<?php

class Clearpay_Clearpay_Block_Config extends Mage_Core_Block_Template
{
    /**
     * Get the payment mode
     *
     * @return mixed (redirect | lightbox)
     */
    public function getMode()
    {
        return Mage::getStoreConfig('clearpay/payovertime_checkout/checkout_mode');
    }
}