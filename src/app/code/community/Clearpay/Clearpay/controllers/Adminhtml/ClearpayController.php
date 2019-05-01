<?php
/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/**
 * Class Clearpay_Clearpay_Adminhtml_ClearpayController
 *
 * Handles Admin-side Clearpay Operations 
 */
class Clearpay_Clearpay_Adminhtml_ClearpayController extends Mage_Adminhtml_Controller_Action
{
    public function updateAction()
    {
        $model = new Clearpay_Clearpay_Model_Observer();

        try {
            $model->updateOrderLimits();
            $this->_getSession()->addSuccess(Mage::helper('clearpay')->__('Successfully updated limits'));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }

        $this->_redirectReferer();
    }
}