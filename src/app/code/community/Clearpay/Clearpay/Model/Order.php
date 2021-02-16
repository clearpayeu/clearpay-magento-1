<?php
/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

class Clearpay_Clearpay_Model_Order extends Clearpay_Clearpay_Model_Method_Payovertime
{
    /**
     * Overrides in order to obfuscate sensitive info in log (JIRA PIP-419)
     */
     protected function _logRequest($url, $type, $call, $body, $level = Zend_Log::DEBUG)
     {
         $helper = Mage::helper('clearpay');

         $helper->log(array(
             'url' => $url,
             'type' => $type,
             'call' => $call,
             'body' => $this->_sanitizeContent($body)
         ), $level);
     }

    /**
     * The actual function that obfuscates personal info.
     * The keywords ['consumer', 'billing', 'shipping'] are derived from the API documents
     */
    protected function _sanitizeContent($body)
    {
        if (is_array($body)) {
            foreach ($body as $i => $val)
            {
                if (in_array($i, ['consumer', 'billing', 'shipping'], TRUE)) {
                    foreach ($body[$i] as $j => $sensitive)
                    {
                        $body[$i][$j] = preg_replace('/\S/', '*', $sensitive);
                    }
                } else {
                    $body[$i] = $this->_sanitizeContent($val);
                }
            }
        }
        return $body;
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Start creating order for Clearpay
     *
     * @param $quote
     *
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function start($quote)
    {

        // Magento calculate the totals
        $quote->collectTotals();

        // Check if total is 0 and Clearpay won't processing it
        if (!$quote->getGrandTotal() && !$quote->hasNominalItems()) {
            Mage::throwException(Mage::helper('clearpay')->__('Clearpay does not support processing orders with zero amount. To complete your purchase, proceed to the standard checkout process.'));
        }

        // Reserved order Id and save it to quote
        $quote->reserveOrderId()->save();

        // Clearpay build order token request - accommodate both Ver 0 and 1
        $postData = $this->getApiAdapter()->buildOrderTokenRequest($quote, array('merchantOrderId' => $quote->getReservedOrderId()), $this->clearpayPaymentTypeCode);

        $gatewayUrl = $this->getApiAdapter()->getApiRouter()->getOrdersApiUrl();

        // Request order token to API
        $result = $this->_sendRequest($gatewayUrl, $postData, 'POST', 'StartClearpayPayment');
        $resultObject = json_decode($result);

        // Check if token is NOT in response
        if ( empty($resultObject->orderToken) && empty($resultObject->token) ) {
            throw Mage::exception('Clearpay_Clearpay', 'Clearpay API Gateway Error.');
        } else {
            // Save token to the sales_flat_quote_payment

            //API Ver 0
            if( !empty($resultObject->orderToken) ) {
                $orderToken = $resultObject->orderToken;
            }
            else if( !empty($resultObject->token) ) {
                $orderToken = $resultObject->token;
            }

            try {
                $payment = $quote->getPayment();
                $payment->setData('clearpay_token', $orderToken);
                $payment->save();

                // Added to log
                Mage::helper('clearpay')->log(
                    sprintf('Token successfully saved for reserved order %s. token=%s', $quote->getReservedOrderId(), $orderToken),
                    Zend_Log::NOTICE
                );
            }
            catch (Exception $e) {
                // Add error message
                $message = 'Exception during initial Clearpay Token saving.';

                $this->helper()->log($this->__($message . ' %s', $e->getMessage()), Zend_Log::ERR);

                Mage::throwException(
                        Mage::helper('clearpay')->__($message)
                    );
            }

            return $orderToken;
        }

    }

    /**
     * Start Express Checkout
     *
     * @param $quote
     *
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function startExpress($quote)
    {
        // Magento calculate the totals
        $quote->collectTotals();

        // Check if total is 0 and Clearpay won't processing it
        if (!$quote->getGrandTotal() && !$quote->hasNominalItems()) {
            Mage::throwException(Mage::helper('clearpay')->__('Clearpay does not support processing orders with zero amount. To complete your purchase, proceed to the standard checkout process.'));
        }

        // Reserved order Id and save it to quote
        $quote->reserveOrderId()->save();

        // Clearpay build order token request - accommodate both Ver 0 and 1
        $postData = $this->getApiAdapter()->buildExpressOrderTokenRequest($quote);

        $gatewayUrl = $this->getApiAdapter()->getApiRouter()->getOrdersApiUrl();

        // Request order token to API
        $result = $this->_sendRequest($gatewayUrl, $postData, 'POST', 'StartClearpayExpress');
        $resultObject = json_decode($result);

        // Check if token is NOT in response
        if ( empty($resultObject->token) ) {
            throw Mage::exception('Clearpay_Clearpay', 'Clearpay API Gateway Error.');
        } else {
            // Save token to the sales_flat_quote_payment

            $orderToken = $resultObject->token;

            try {
                $payment = $quote->getPayment();
                $payment->setData('clearpay_token', $orderToken);
                $payment->save();

                // Added to log
                Mage::helper('clearpay')->log(
                    sprintf('Token successfully saved for reserved order %s. token=%s', $quote->getReservedOrderId(), $orderToken),
                    Zend_Log::NOTICE
                );
            }
            catch (Exception $e) {
                // Add error message
                $message = 'Exception during initial Clearpay Token saving.';

                $this->helper()->log($this->__($message . ' %s', $e->getMessage()), Zend_Log::ERR);

                Mage::throwException(
                        Mage::helper('clearpay')->__($message)
                    );
            }

            return $orderToken;
        }

    }

    /**
     * Start creating order for Clearpay
     *
     * @param string                    $orderToken
     * @param string                    $merchantOrderId
     * @param Mage_Sales_Model_Quote    $quote
     *
     * @return mixed
     * @throws Clearpay_Clearpay_Exception
     */
    public function directCapture( $orderToken, $merchantOrderId, $quote )
    {
        $postData = $this->getApiAdapter()->buildDirectCaptureRequest($orderToken,$merchantOrderId,$quote);

        $gatewayUrl = $this->getApiAdapter()->getApiRouter()->getDirectCaptureApiUrl();

        // Request order token to API
        $result = $this->_sendRequest($gatewayUrl, $postData, 'POST', 'StartClearpayDirectCapture');
        $resultObject = json_decode($result);

        // Check if token is NOT in response
        if( !empty($resultObject->errorCode) || !empty($resultObject->errorId) ) {

            throw Mage::exception('Clearpay_Clearpay', $resultObject->message);
        }
        else if ( empty($resultObject->id) ) {
            throw Mage::exception('Clearpay_Clearpay', 'Clearpay API Gateway Error');
        }
        else {
            return $resultObject;
        }
    }

    /**
     * Check Clearpay order details using the token
     *
     * @param string                    $orderToken
     * @param Mage_Sales_Model_Quote    $quote
     *
     * @return mixed
     * @throws Clearpay_Clearpay_Exception
     */
    public function getOrderByToken( $orderToken ) {
        $gatewayUrl = $this->getApiAdapter()->getApiRouter()->getOrdersApiUrl( $orderToken, 'token' );

        // Request order token to API
        $result = $this->_sendRequest($gatewayUrl, false, 'GET', 'Get order by token ' . $orderToken);
        $resultObject = json_decode($result);

        return $resultObject;
    }

    /**
     * Placing order to Magento
     *
     * @return bool
     * @throws Exception
     */
    public function place()
    {
        // Converting quote to order
        $service = Mage::getModel('sales/service_quote', $this->_getSession()->getQuote());
        $service->submitAll();

        $session = $this->_getSession();
        $quote = $session->getQuote();

        $session->setLastQuoteId($quote->getId())
            ->setLastSuccessQuoteId($quote->getId())
            ->clearHelperData();

        $order = $service->getOrder();
        if ($order) {
            $order->setData('clearpay_order_id', $quote->getData('clearpay_order_id'));
            $order->save();

            $paymentMethod = $order->getPayment()->getMethodInstance();
            if (!$order->getEmailSent() && $paymentMethod->getConfigData('order_email')) {
                $order->sendNewOrderEmail();
            }

            // add order information to the session
            $session->setLastOrderId($order->getId())
                ->setLastRealOrderId($order->getIncrementId());
        }

        Mage::dispatchEvent(
            'checkout_submit_all_after',
            array('order' => $order, 'quote' => $quote, 'recurring_profiles' => array())
        );

        return $order ? true : false;
    }
}
