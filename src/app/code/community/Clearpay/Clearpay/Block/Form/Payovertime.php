<?php

/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */
class Clearpay_Clearpay_Block_Form_Payovertime extends Clearpay_Clearpay_Block_Form_Abstract
{
    const TITLE_TEMPLATE_SELECTOR_ID = 'clearpay_checkout_payovertime_headline';

    const DETAIL_TEMPLATE_SELECTOR_ID = 'clearpay_checkout_payovertime_form';

    const CONFIG_PATH_CHECKOUT_TITLE_TEMPLATE = 'clearpay/payovertime_checkout/checkout_headline_html_template';

    const CONFIG_PATH_CHECKOUT_DETAILS_TEMPLATE = 'clearpay/payovertime_checkout/checkout_details_html_template';

    const CONFIG_PATH_SHOW_DETAILS = 'clearpay/payovertime_checkout/show_checkout_details';

    const TEMPLATE_OPTION_DETAILS_DEFAULT = 'clearpay/form/payovertime.phtml';

    const TEMPLATE_OPTION_TITLE_CUSTOM = 'clearpay/checkout/title_custom.phtml';

    const TEMPLATE_OPTION_DETAILS_CUSTOM = 'clearpay/form/payovertime_custom.phtml';

    protected function _construct()
    {
        parent::_construct();

        // logic borrowed from Mage_Paypal_Block_Standard_form
        $block = Mage::getConfig()->getBlockClassName('core/template');
        $block = new $block;
        $block->setTemplateHelper($this);
        $block->setTemplate(self::TEMPLATE_OPTION_TITLE_CUSTOM);

        if (Mage::getStoreConfigFlag(self::CONFIG_PATH_SHOW_DETAILS)) {
            $this->setTemplate(self::TEMPLATE_OPTION_DETAILS_CUSTOM);
        } else {
            $this->setTemplate('');
        }
        $this->setMethodTitle('')
            ->setMethodLabelAfterHtml($block->toHtml());
    }

    public function getInstalmentAmount()
    {
        if (!$this->hasData('instalment_amount')) {
            $formatted = Mage::helper('clearpay')->calculateInstalment();
            $this->setData('instalment_amount', $formatted);
        }

        return $this->getData('instalment_amount');
    }

    public function getInstalmentAmountCreditUsed()
    {
        if (!$this->hasData('instalment_amount_credit_used')) {
            $formatted = Mage::helper('clearpay')->calculateInstalment(true);
            $this->setData('instalment_amount_credit_used', $formatted);
        }

        return $this->getData('instalment_amount_credit_used');
    }

    public function getInstalmentAmountLast()
    {
        if (!$this->hasData('instalment_amount_last')) {
            $formatted = Mage::helper('clearpay')->calculateInstalmentLast();
            $this->setData('instalment_amount_last', $formatted);
        }

        return $this->getData('instalment_amount_last');
    }

    public function getInstalmentAmountLastCreditUsed()
    {
        if (!$this->hasData('instalment_amount_last_credit_used')) {
            $formatted = Mage::helper('clearpay')->calculateInstalmentLast(true);
            $this->setData('instalment_amount_last_credit_used', $formatted);
        }

        return $this->getData('instalment_amount_last_credit_used');
    }

    public function getOrderTotal()
    {
        $total = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();
        return Mage::app()->getStore()->formatPrice($total, false);
    }

    public function getOrderTotalCreditUsed()
    {
        $total = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();
        $total = $total - Mage::helper('clearpay')->getCustomerBalance();
        if ($total < 0) {
            $total = 0;
        }
        return Mage::app()->getStore()->formatPrice($total, false);
    }

    public function getDetailsConfiguration()
    {
        $config = $this->_getCommonConfiguration();
        $config['template'] = $this->_getCustomDetailTemplate();
        $config['cssSelector'] = '#' . $this->getDetailsTemplateId();
        return $config;
    }

    public function getTitleConfiguration()
    {
        $config = $this->_getCommonConfiguration();
        $config['template'] = $this->_getCustomTitleTemplate();
        $config['cssSelector'] = '#' . $this->getTitleTemplateId();
        return $config;
    }

    public function getDetailsTemplateId()
    {
        return self::DETAIL_TEMPLATE_SELECTOR_ID;
    }

    public function getTitleTemplateId()
    {
        return self::TITLE_TEMPLATE_SELECTOR_ID;
    }

    public function getRegionSpecificText()
    {
        if (Mage::app()->getStore()->getCurrentCurrencyCode() == 'GBP') {
            return 'fortnightly with';
        }
    }

    private function _getCommonConfiguration()
    {
        return array(
            'clearpayLogoSubstitution' => '{clearpay_logo}',
            'clearpayLogo' => 'https://static.afterpay.com/integration/logo-clearpay-colour-79x15@2x.png',
            'orderAmountSubstitution' => '{order_amount}',
            'orderAmount' => $this->getOrderTotal(),
            'orderAmountCreditUsed' => $this->getOrderTotalCreditUsed(),
            'regionSpecificSubstitution' => '{region_specific_text}',
            'regionText' => $this->getRegionSpecificText(),
            'installmentAmountSubstitution' => '{instalment_amount}',
            'installmentAmount' => $this->getInstalmentAmount(),
            'installmentAmountCreditUsed' => $this->getInstalmentAmountCreditUsed(),
            'installmentAmountSubstitutionLast' => '{instalment_amount_last}',
            'installmentAmountLast' => $this->getInstalmentAmountLast(),
            'installmentAmountLastCreditUsed' => $this->getInstalmentAmountLastCreditUsed(),
            'imageCircleOneSubstitution' => '{img_circle_1}',
            'imageCircleOne' => 'https://static.afterpay.com/checkout/circle_1@2x.png',
            'imageCircleTwoSubstitution' => '{img_circle_2}',
            'imageCircleTwo' => 'https://static.afterpay.com/checkout/circle_2@2x.png',
            'imageCircleThreeSubstitution' => '{img_circle_3}',
            'imageCircleThree' => 'https://static.afterpay.com/checkout/circle_3@2x.png',
            'imageCircleFourSubstitution' => '{img_circle_4}',
            'imageCircleFour' => 'https://static.afterpay.com/checkout/Circle_4@2x.png',
            'creditUsedSelector' => '#use_customer_balance'
        );
    }

    private function _getCustomDetailTemplate()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_CHECKOUT_DETAILS_TEMPLATE);
    }

    private function _getCustomTitleTemplate()
    {
        return Mage::getStoreConfig(self::CONFIG_PATH_CHECKOUT_TITLE_TEMPLATE);
    }
}
