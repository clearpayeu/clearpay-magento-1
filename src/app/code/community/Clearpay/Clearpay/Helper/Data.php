<?php

/**
 * Default Clearpay helper class
 *
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */
class Clearpay_Clearpay_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     */
    protected $logFileName = 'clearpay.log';

    /**
     * @var bool
     */
    protected $isDebugEnabled;

    /**
     * General logging method
     *
     * @param      $message
     * @param null $level
     */
    public function log($message, $level = null)
    {
        if ($this->isDebugMode() || $level != Zend_Log::DEBUG) {
            Mage::log($message, $level, $this->logFileName);
        }
    }

    /**
     * @return bool
     */
    public function isDebugMode()
    {
        if ($this->isDebugEnabled === null) {
            $this->isDebugEnabled = Mage::getStoreConfigFlag('clearpay/general/debug');
        }

        return $this->isDebugEnabled;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return $this
     * @throws Exception
     * @throws Clearpay_Clearpay_Exception
     */
    public function createInvoice(Mage_Sales_Model_Order $order)
    {
        /** @var Mage_Payment_Model_Method_Abstract $paymentMethod */
        $paymentMethod = $order->getPayment()->getMethodInstance();

        $createInvoice = $paymentMethod->getConfigData('invoice_create', $order->getStoreId());

        if ($createInvoice && $order->getId()) {
            if ($order->hasInvoices()) {
                throw Mage::exception('Clearpay_Clearpay', $this->__('Order already has invoice.'));
            }

            if (!$order->canInvoice()) {
                throw Mage::exception('Clearpay_Clearpay', $this->__("Order can't be invoiced."));
            }

            $invoice = $order->prepareInvoice();

            if ($invoice->getTotalQty() > 0) {
                //Invoice offline because our payment method do not support capture
                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);

                if ($order->getPayment()->getLastTransId()) {
                    $invoice->setTransactionId($order->getPayment()->getLastTransId());
                }

                $invoice->register();
                /** @var Mage_Core_Model_Resource_Transaction $transaction */
                $transaction = Mage::getModel('core/resource_transaction');

                $transaction
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());

                $transaction->save();

                $invoice->addComment($this->__('Clearpay Automatic invoice.'), false);

                // Send invoice email
                if (!$invoice->getEmailSent() && $paymentMethod->getConfigData('invoice_email', $order->getStoreId())) {
                    $invoice->sendEmail()->setEmailSent(true);
                }

                $invoice->save();
            }
        }

        return $this;
    }

    /**
     * Get the current version of the Clearpay extension
     *
     * @return string
     */
    public function getModuleVersion()
    {
        return (string) Mage::getConfig()->getModuleConfig('Clearpay_Clearpay')->version;
    }

    /**
     * Calculate The Instalments
     *
     * @param boolean $creditUsed
     * @return string
     */
    public function calculateInstalment($creditUsed = false)
    {
        $total = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();
        if ($creditUsed) {
            $total = $total - $this->getCustomerBalance();
            if ($total < 0) {
                $total = 0;
            }
        }
        $installment = round($total / 4, 2, PHP_ROUND_HALF_UP);
        return Mage::app()->getStore()->formatPrice($installment, false);
    }

    public function calculateInstalmentLast($creditUsed = false)
    {
        $total = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();
        if ($creditUsed) {
            $total = $total - $this->getCustomerBalance();
            if ($total < 0) {
                $total = 0;
            }
        }
        $prev_instalments = round($total / 4, 2, PHP_ROUND_HALF_UP);
        $installment = $total - 3 * $prev_instalments;
        return Mage::app()->getStore()->formatPrice($installment, false);
     }

    /**
     * Calculate The Total Amount
     *
     * @return string
     */
    public function calculateTotal()
    {
        $total = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();
        return $total;
    }


    //Enterprise Edition Only

    public function getCustomerBalance()
    {
        if (Mage::getEdition() == Mage::EDITION_ENTERPRISE &&
            Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $customerId = Mage::getSingleton('customer/session')->getId();
            $website_id = Mage::app()->getStore()->getWebsiteId();
            $balance = Mage::getModel('enterprise_customerbalance/balance')
                    ->setCustomerId($customerId)
                    ->setWebsiteId($website_id)
                    ->loadByCustomer();
            return $balance->getAmount();
        }
        return 0;
    }

    /**
     * Store Credit Manipulations
     *
     * @return bool
     */
    public function storeCreditPlaceOrder()
    {
        //process the Credit Memo on Orders
        if( Mage::getSingleton('customer/session')->isLoggedIn() && Mage::getSingleton('checkout/session')->getData('clearpayCustomerBalance') ) {
            $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getSingleton('sales/order')->loadByIncrementId($orderId);

            $balanceUsed = Mage::getSingleton('checkout/session')->getData('clearpayCustomerBalance');

            $order->setCustomerBalanceAmount( $balanceUsed );
            $order->setBaseCustomerBalanceAmount( $balanceUsed );
            $order->setCustomerBalanceInvoiced( $balanceUsed );
            $order->setBaseCustomerBalanceInvoiced( $balanceUsed );
            $order->setTotalPaid($order->getGrandTotal());

            $order->save();

            $this->customerBalanceDeductionFallback( $orderId, $balanceUsed );

            Mage::getSingleton('checkout/session')->unsetData('clearpayCustomerBalance');
        }

        // probably we are using the default checkout
        return true;
    }

    /**
     * Store Credit Deduction Fallback
     *
     * @return bool
     */
    public function customerBalanceDeductionFallback( $orderId, $balanceUsed ) {
        // Get the first customer in the store's ID
        $customerId = Mage::getSingleton('customer/session')->getId();

        $balance = Mage::getModel('enterprise_customerbalance/balance')
                ->setCustomerId($customerId)
                ->setWebsiteId(Mage::app()->getWebsite()->getId($orderId))
                ->loadByCustomer();

        if( $balance->getAmount() > 0 ) {
            //safeguard against a possibility of minus balance
            $balance->setAmountDelta( -1 * $balanceUsed )
                    ->setUpdatedActionAdditionalInfo("Order #" . $orderId ); // This field is optional but recommended.

            $this->log("Customer Balance deduction fallback engaged. Order: " . $orderId . " Balance Delta: " . $balanceUsed );
            $balance->save();
        }
    }

    /**
     * Store Credit Session Set
     *
     * @return void
     */
    public function storeCreditSessionSet($quote) {
        // Utilise Magento Session to preserve Store Credit details

    	$params = Mage::app()->getRequest()->getParams();

        if( Mage::getSingleton('customer/session')->isLoggedIn() && $quote->getCustomerBalanceAmountUsed() ) {
            Mage::getSingleton('checkout/session')->setData('clearpayCustomerBalance', $quote->getCustomerBalanceAmountUsed());
        }
    	else if( Mage::getSingleton('customer/session')->isLoggedIn() && !empty($params) && !empty($params["payment"]) && isset($params["payment"]["use_customer_balance"]) && $params["payment"]["use_customer_balance"] ) {

    	    // Handler for Default One Page Checkout
    	    $customerId = Mage::getSingleton('customer/session')->getId();
    	    $website_id = Mage::app()->getStore()->getWebsiteId();

    	    $balance = Mage::getModel('enterprise_customerbalance/balance')
                    ->setCustomerId($customerId)
                    ->setWebsiteId($website_id)
                    ->loadByCustomer();

    	    $quote->setUseCustomerBalance(1);
    	    $quote->setCustomerBalanceAmountUsed( $balance->getAmount() );

    	    $grand_total = $quote->getGrandTotal();
    	    $quote->setGrandTotal( $grand_total - $balance->getAmount() );

    	    $quote->save();

    	    Mage::getSingleton('checkout/session')->setData('clearpayCustomerBalance', $balance->getAmount());

    	}
    	Mage::getSingleton('checkout/session')->setData('clearpayGrandTotal', $quote->getGrandTotal());
    	Mage::getSingleton('checkout/session')->setData('clearpaySubtotal', $quote->getSubtotal());
    	return $quote;
    }

    /**
     * Store Credit Session Unset
     *
     * @return void
     */
    public function storeCreditSessionUnset() {
        if( Mage::getSingleton('checkout/session')->getData('clearpayCustomerBalance') ) {
            Mage::getSingleton('checkout/session')->unsetData('clearpayCustomerBalance');
        }

        if( Mage::getSingleton('checkout/session')->getData('clearpayGrandTotal') ) {
            Mage::getSingleton('checkout/session')->unsetData('clearpayGrandTotal');
        }

        if( Mage::getSingleton('checkout/session')->getData('clearpaySubtotal') ) {
            Mage::getSingleton('checkout/session')->unsetData('clearpaySubtotal');
        }
    }



    /**
     * Store Credit Session Handler for Capture Payment Phase
     *
     * @return void
     */
    public function storeCreditCapture($quote) {
        if( Mage::getSingleton('customer/session')->isLoggedIn() && Mage::getSingleton('checkout/session')->getData('clearpayCustomerBalance') ) {

	    //$grand_total = $quote->getGrandTotal();
	    $grand_total = Mage::getSingleton('checkout/session')->getData('clearpayGrandTotal');
	    $subtotal = Mage::getSingleton('checkout/session')->getData('clearpaySubtotal');
	    $balance = Mage::getSingleton('checkout/session')->getData('clearpayCustomerBalance');

	    $quote->setUseCustomerBalance(1);

	    $quote->setCustomerBalanceAmountUsed( $balance );
            $quote->setBaseCustomerBalanceAmountUsed( $balance );

	    if( $quote->getSubtotal() == $subtotal ) {
	    	$quote->setGrandTotal( $grand_total )->save();
	    }


	    $this->log($this->__('Store Credit being used: ' . $balance . ", Grand Total: " . $grand_total ));


            return $quote;
        }

        return $quote;
    }



    /**
     * Gift Card Session Set
     *
     * @return void
     */
    public function giftCardsSessionSet($quote) {

	// Utilise Magento Session to preserve Store Credit details
        if( $quote->getGiftCardsAmountUsed() ) {
            Mage::getSingleton('checkout/session')->setData('clearpayGiftCards', $quote->getGiftCards());
            Mage::getSingleton('checkout/session')->setData('clearpayGiftCardsAmount', $quote->getGiftCardsAmountUsed());
        }
	return $quote;
    }

    /**
     * Gift Card Session Unset
     *
     * @return void
     */
    public function giftCardsSessionUnset() {
        if( Mage::getSingleton('checkout/session')->getData('clearpayGiftCards') ) {
            Mage::getSingleton('checkout/session')->unsetData('clearpayGiftCards');
            Mage::getSingleton('checkout/session')->unsetData('clearpayGiftCardsAmount');
        }
    }



    /**
     * Gift Card Session Handler for Capture Payment Phase
     *
     * @return void
     */
    public function giftCardsCapture($quote) {

    	$balance = Mage::getSingleton('checkout/session')->getData('clearpayGiftCardsAmount');
    	$gift_cards = Mage::getSingleton('checkout/session')->getData('clearpayGiftCards');

        if( !empty($balance) && $balance > 0 ) {

	    $grand_total = $quote->getGrandTotal();

	    $quote->setGiftCardsAmountUsed( $balance );
	    $quote->setGiftCards( $gift_cards );

	    /*
	    if( $grand_total > $balance ) {
	    	$quote->setGrandTotal( $grand_total - $balance );
	    }
	    */


	    //deduct the gift card
	    $gift_cards_data = unserialize($gift_cards);
	    $gift_cards_account = Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
	            ->loadByCode($gift_cards_data[0]['c']);

	    if (!$gift_cards_account->getId()) {
	        Mage::throwException('Gift Card Code Not Found');
	    }
	    else {

		if( !empty($gift_card_account) && $gift_card_account->getGiftCardsAmount() >= $balance ) {
			$gift_cards_new_amount = $balance;
			$gift_cards_account->charge( $gift_cards_new_amount );
			//$gift_cards_account->setUpdatedActionAdditionalInfo("Order #" . $quote->getReservedOrderId() );
			$gift_cards_account->save();

			$this->log($this->__('Gift Cards used: ' . $gift_cards  . ' Amount being used: ' . $balance ));
		}
		else {
			$this->log($this->__('Gift Cards used: ' . $gift_cards  . ' Amount is deducted already' ));
		}


	        Mage::getSingleton('checkout/session')->unsetData('clearpayGiftCards');
	        Mage::getSingleton('checkout/session')->unsetData('clearpayGiftCardsAmount');
	    }


            return $quote;
        }

        return $quote;
    }


    /**
     * Gift Card Order Manipulations
     *
     * @return bool
     */
    public function giftCardsPlaceOrder()
    {
        if( Mage::getSingleton('checkout/session')->getData('clearpayGiftCards') ) {
            $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            $order = Mage::getSingleton('sales/order')->loadByIncrementId($orderId);

            $gift_cards = Mage::getSingleton('checkout/session')->getData('clearpayGiftCards');
            $balance_used = Mage::getSingleton('checkout/session')->getData('clearpayGiftCardsAmount');

            $order->setGiftCards( $gift_cards );
            $order->setGiftCardsAmount( $balance_used );
            $order->setGiftCardsInvoiced( $balance_used );

            $order->save();

            Mage::getSingleton('checkout/session')->unsetData('clearpayGiftCards');
            Mage::getSingleton('checkout/session')->unsetData('clearpayGiftCardsAmount');
	}

        return true;
    }


}
