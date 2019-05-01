<?php
/**
 * Class Clearpay_Clearpay_Block_Require
 */
class Clearpay_Clearpay_Block_Require extends Mage_Core_Block_Template
{
    /**
     * @return array
     */
    public function getRequireJs()
    {
        return array(
            'payovertime' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS) . 'Clearpay/checkout/payovertime.js'
        );
    }

    /**
     * @return array
     */
    public function getRequireStyle()
    {
        return array(
            'payovertime' => $this->getSkinUrl('clearpay/css/clearpay.css'),
        );
    }
}