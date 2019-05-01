<?php

/**
 * 
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/**
 * Class Clearpay_Clearpay_Block_Form_Abstract
 * Abstract class for all payment form blocks.
 * @method void setRedirectMessage(string $message);
 */
abstract class Clearpay_Clearpay_Block_Form_Abstract extends Mage_Payment_Block_Form
{
    /**
     * Get payment method redirect message
     *
     * @return string
     */
    public function getRedirectMessage()
    {
        if ($this->hasData('redirect_message')) {
            return $this->getData('redirect_message');
        } else {
            return $this->getMethod()->getConfigData('message');
        }
    }
}
