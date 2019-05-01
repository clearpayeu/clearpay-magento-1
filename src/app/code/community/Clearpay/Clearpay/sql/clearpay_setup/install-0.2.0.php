<?php

/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();


$table = $installer->getTable('sales_flat_order_payment');
$installer->getConnection()->addColumn($table, "clearpay_token", "varchar(255) DEFAULT NULL COMMENT 'Clearpay Order Token'");
$installer->getConnection()->addIndex($table, "IDX_SALES_FLAT_ORDER_PAYMENT_CLEARPAY_TOKEN", "clearpay_token", Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE);


$installer->endSetup();
