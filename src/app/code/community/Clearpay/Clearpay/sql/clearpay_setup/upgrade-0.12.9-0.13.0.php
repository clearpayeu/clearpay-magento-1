<?php
/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */
$installer = $this;

$installer->startSetup();

/**
 * Setup script to create new column on sales_flat_quote_payment for:
 * - clearpay_token
 * - clearpay_order_id
 */

	$table = $installer->getTable('sales/quote_payment');
	$installer->getConnection()->addColumn($table, 'clearpay_token', "varchar(255) DEFAULT NULL COMMENT 'Clearpay Order Token'");
	$installer->getConnection()->addColumn($table, 'clearpay_order_id', "varchar(255) DEFAULT NULL COMMENT 'Clearpay Order ID'");	

$installer->endSetup();
?>