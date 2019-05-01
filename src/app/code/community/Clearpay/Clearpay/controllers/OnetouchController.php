<?php
/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/**
 * Class Clearpay_Clearpay_OnetouchController
 *
 * Set the default payment method for the order to be Clearpay, then load the checkout.
*/
class Clearpay_Clearpay_OnetouchController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();

        $quote->getPayment()->setMethod('clearpaypayovertime')
            ->save();

        $helper = Mage::helper('checkout/url');
        $this->_redirectUrl($helper->getCheckoutUrl());
    }
}