<?php

/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

//--------------------------------------------------
/*
    # add custom columns
    ALTER TABLE sales_flat_order_payment
        ADD COLUMN clearpay_token varchar(255) DEFAULT NULL COMMENT 'Clearpay Order Token',
        ADD COLUMN clearpay_order_id varchar(255) DEFAULT NULL COMMENT 'Clearpay Order ID',
        ADD COLUMN clearpay_fetched_at TIMESTAMP NULL;

    # add custom order status
    INSERT INTO sales_order_status (`status`, `label`) VALUES ('clearpay_payment_review', 'Clearpay Processing');
    INSERT INTO sales_order_status_state (`status`, `state`, `is_default`) VALUES ('clearpay_payment_review', 'payment_review', '0');
*/
//--------------------------------------------------

	// add columns to sales/order_payment
	$table = $installer->getTable('sales_flat_order_payment');
	$installer->getConnection()->addColumn($table, "clearpay_token", "varchar(255) DEFAULT NULL COMMENT 'Clearpay Order Token'");
	$installer->getConnection()->addColumn($table, "clearpay_order_id", "varchar(255) DEFAULT NULL COMMENT 'Clearpay Order ID'");
	$installer->getConnection()->addColumn($table, "clearpay_fetched_at", "TIMESTAMP NULL");

	// add new status and map it to Payment Review state
	$status = 'clearpay_payment_review';
	$state  = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
	$installer->run("INSERT INTO `{$this->getTable('sales_order_status')}` (`status`, `label`) VALUES ('{$status}', 'Clearpay Processing');");
	$installer->run("INSERT INTO `{$this->getTable('sales_order_status_state')}` (`status`, `state`, `is_default`) VALUES ('{$status}', '{$state}', '0');");
//--------------------------------------------------

$installer->endSetup();
