<?php
/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();
$installer->run("
    ALTER TABLE `{$installer->getTable('sales_flat_order_payment')}`
    DROP COLUMN clearpay_token,
    DROP COLUMN clearpay_order_id,
    DROP COLUMN clearpay_fetched_at;
");

$installer->run("
    DELETE FROM `{$installer->getTable('sales_order_status_state')}` WHERE status='clearpay_payment_review';
");

$installer->run("
    DELETE FROM `{$installer->getTable('sales_order_status')}` WHERE status='clearpay_payment_review';
");

$installer->run("
    DROP TABLE IF EXISTS `{$installer->getTable('clearpay_shipped_api_queue')}`;
");

$installer->run("
    DELETE FROM `{$installer->getTable('core_resource')}` WHERE code='clearpay_setup';
");

$installer->endSetup();
?>