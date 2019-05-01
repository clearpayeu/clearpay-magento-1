<?php
/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();


$status = 'clearpay_payment_review';
$state  = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;

$installer->run("INSERT INTO `{$this->getTable('sales_order_status')}` (`status`, `label`) VALUES ('{$status}', 'Clearpay Processing');");
$installer->run("INSERT INTO `{$this->getTable('sales/order_status_state')}` (`status`, `state`, `is_default`) VALUES ('{$status}', '{$state}', '0');");


$installer->endSetup();
