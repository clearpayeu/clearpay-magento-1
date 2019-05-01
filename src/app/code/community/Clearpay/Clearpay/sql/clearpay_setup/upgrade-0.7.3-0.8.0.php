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
	    CREATE TABLE `{$installer->getTable('clearpay/clearpay_shipped_api_queue')}` (
	      `shipped_api_queue_id` INT(11) NOT NULL auto_increment,
	      `payment_id` INT(11) NOT NULL,
	      `tracking_number` VARCHAR(255),
	      `courier` VARCHAR(255),
	      `errors_count` INT(11),
	      PRIMARY KEY  (`shipped_api_queue_id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
$installer->endSetup();
