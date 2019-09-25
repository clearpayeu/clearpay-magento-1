<?php

/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */
class Clearpay_Clearpay_Model_Method_Payovertime extends Clearpay_Clearpay_Model_Method_Base
{
    /**
     * Constant variable
     */
    const CODE = 'clearpaypayovertime';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::CODE;
    protected $_isGateway    = true;
    protected $_canAuthorize = true;
    protected $_canCapture   = true;

    /**
     * Info and form blocks
     *
     * @var string
     */
    protected $_formBlockType = 'clearpay/form_payovertime';
    protected $_infoBlockType = 'clearpay/info';

    /**
     * Payment type code according to Clearpay API documentation.
     *
     * @var string
     */
    protected $clearpayPaymentTypeCode = 'PBI';

    /**
     * Capture the payment.
     *
     * Basically, this capture function is connecting API and check between session and Clearpay details
     * To make sure it is NOT fraud request
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this
     * @throws Mage_Core_Exception
     */
    function capture(Varien_Object $payment, $amount)
    {

            $session = Mage::getSingleton('checkout/session');
            $quote = $session->getQuote();

            $orderToken = $payment->getData('clearpay_token');
            $reserved_order_id = $quote->getReservedOrderId();

        //Check for stock levels here
        if( empty($orderToken) ) {
            // Perform the fallback in case of Unsupported checkout
            $this->fallbackMechanism('token_missing');
        }

        // Check total amount
        $data = Mage::getModel('clearpay/order')->getOrderByToken( $orderToken );

        /**
        * Validation to check between session and post request
        */
        if( !$data ) {
            // Check the order token being use
            $this->resetTransactionToken($quote);

            Mage::helper('clearpay')->log(
                'Clearpay gateway has rejected request. Invalid token. ' .
                ' Token Value: ' . $orderToken
            );

            Mage::throwException(
                Mage::helper('clearpay')->__('Clearpay gateway has rejected request. Invalid token.')
            );
        }
        else if( $reserved_order_id != $data->merchantReference ) {
            // Check order id
            $this->resetTransactionToken($quote);

            Mage::helper('clearpay')->log(
                'Clearpay gateway has rejected request. Incorrect merchant reference. ' .
                ' Quote Value: ' . $reserved_order_id .
                ' Clearpay API: ' . $data->merchantReference
            );

            Mage::throwException(
                Mage::helper('clearpay')->__('Clearpay gateway has rejected request. Incorrect merchant reference.')
            );
        }
        else if( round($quote->getGrandTotal(), 2) != round($data->totalAmount->amount, 2) ) {

            // Check the order amount
            $this->resetTransactionToken($quote);

            Mage::helper('clearpay')->log(
                'Clearpay gateway has rejected request. Invalid amount. ' .
                ' Quote Amount: ' . round($quote->getGrandTotal(), 2) .
                ' Clearpay API: ' . round($data->totalAmount->amount, 2)
            );

            Mage::throwException(
                Mage::helper('clearpay')->__('Clearpay gateway has rejected request. Invalid amount.')
            );
        }

        try {
            $data = Mage::getModel('clearpay/order')->directCapture( $orderToken, $reserved_order_id, $quote );
        }
        catch( Exception $e ) {
            $this->resetTransactionToken($quote);
            $this->resetPayment($payment);

            Mage::helper('clearpay')->log( 'Direct Capture Failed: ' . $e->getMessage() );

            Mage::throwException(
                Mage::helper('clearpay')->__( $e->getMessage() )
            );
        }


        if( !empty($data) && !empty($data->id) ) {
            $clearpayOrderId = $data->id;

            // save orderid to payment
            if ($payment) {
                $payment->setData('clearpay_order_id', $clearpayOrderId)->save();
                $quote->setData('clearpay_order_id', $clearpayOrderId)->save();
            }
        }


        switch($data->status) {
            case Clearpay_Clearpay_Model_Method_Base::RESPONSE_STATUS_APPROVED:
                $payment->setTransactionId($payment->getData('clearpay_order_id'))->save();
                break;
            case Clearpay_Clearpay_Model_Method_Base::RESPONSE_STATUS_DECLINED:

                $this->resetTransactionToken($quote);

                Mage::throwException(
                    Mage::helper('clearpay')->__('Clearpay payment has been declined. Please use other payment method.')
                );
                break;

            default:

                $this->resetTransactionToken($quote);
                Mage::throwException(
                    Mage::helper('clearpay')->__('Cannot find Clearpay payment. Please contact administrator.')
                );
                break;
        }

        return $this;
    }

    /**
     * Resetting the token the session
     *
     * @return bool
     */
    public function resetTransactionToken($quote) {

        Mage::getSingleton("checkout/session")->getQuote()->getPayment()->setData('clearpay_token', NULL)->save();

        if( Mage::getEdition() == Mage::EDITION_ENTERPRISE ) {
            Mage::helper('clearpay')->storeCreditSessionUnset();
            Mage::helper('clearpay')->giftCardsSessionUnset();
        }

        return true;
    }

    /**
     * Resetting the payment in the capture step
     *
     * @return bool
     */
    public function resetPayment($payment) {

        $payment->setData('clearpay_token', NULL)->save();

        return true;
    }

    /**
    * Fallback Mechanism hwen Capture is failing
    *
    * @param string    $error_code
    *
    * @return void
    * @throws Clearpay_Clearpay_Exception
    */

    private function fallbackMechanism($error_code) {
        //Unsupported checkout with unattached payovertime.js
        //Or checkout with payovertime.js attached, but no checkout specific JS codes
        $error_array = array(
            // 'invalid_object'
            // 'invalid_order_transaction_status',
            // 'invalid_token',
            'token_missing'
        );

        if( in_array($error_code, $error_array) ) {

            Mage::helper('clearpay')->log(
                sprintf('Unsupported Checkout detected, starting fallback mechanism: ' . $error_code ),
                Zend_Log::NOTICE
            );

            $fallback_url = Mage::getUrl( 'clearpay/payment/redirectFallback', array('_secure' => true) );

            Mage::app()->getResponse()->setRedirect($fallback_url);
            Mage::app()->getResponse()->sendResponse();

            // Throw this exception to avoid sending the PaymentFailedEmail
            throw new Mage_Payment_Model_Info_Exception(
                Mage::helper('clearpay')->__('Fallback Mechanism Triggered')
            );
        }
    }
}
