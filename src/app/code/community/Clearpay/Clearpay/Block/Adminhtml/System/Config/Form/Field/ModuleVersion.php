<?php

/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */
class Clearpay_Clearpay_Block_Adminhtml_System_Config_Form_Field_ModuleVersion extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    public function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return Mage::helper('clearpay')->getModuleVersion();
    }

}
