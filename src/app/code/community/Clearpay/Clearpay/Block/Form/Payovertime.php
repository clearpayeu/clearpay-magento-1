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

    const TEMPLATE_OPTION_TITLE_DEFAULT = 'clearpay/checkout/title.phtml';

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

    public function getInstalmentAmountLast()
    {
        if (!$this->hasData('instalment_amount_last')) {
            $formatted = Mage::helper('clearpay')->calculateInstalmentLast();
            $this->setData('instalment_amount_last', $formatted);
        }

        return $this->getData('instalment_amount_last');
    }

    public function getOrderTotal()
    {
        $total = Mage::getSingleton('checkout/session')->getQuote()->getGrandTotal();
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
            'clearpayLogo' => $this->getSkinUrl('clearpay/images/clearpay-logo-163x31.png'),
            'orderAmountSubstitution' => '{order_amount}',
            'orderAmount' => $this->getOrderTotal(),
            'regionSpecificSubstitution' => '{region_specific_text}',
            'regionText' => $this->getRegionSpecificText(),
            'installmentAmountSubstitution' => '{instalment_amount}',
            'installmentAmount' => $this->getInstalmentAmount(),
            'installmentAmountSubstitutionLast' => '{instalment_amount_last}',
            'installmentAmountLast' => $this->getInstalmentAmountLast(),
            'imageCircleOneSubstitution' => '{img_circle_1}',
            'imageCircleOne' => $this->getSkinUrl('clearpay/images/checkout/circle_1@2x.png'),
            'imageCircleTwoSubstitution' => '{img_circle_2}',
            'imageCircleTwo' => $this->getSkinUrl('clearpay/images/checkout/circle_2@2x.png'),
            'imageCircleThreeSubstitution' => '{img_circle_3}',
            'imageCircleThree' => $this->getSkinUrl('clearpay/images/checkout/circle_3@2x.png'),
            'imageCircleFourSubstitution' => '{img_circle_4}',
            'imageCircleFour' => $this->getSkinUrl('clearpay/images/checkout/circle_4@2x.png')
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