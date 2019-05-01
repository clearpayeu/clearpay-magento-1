<?php
/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */

/* @var $installer Mage_Sales_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

$blockData = array(
    'identifier' => 'clearpay_video_banner',
    'title'      => 'Clearpay Video Banner',
    'content'    => '<div class="clearpay-banner">
    <a href="#" class="youtube-video" data-id="6nhNMv5TYDM"><img src="{{skin url=\'clearpay/images/banner_images/468-68.jpg\'}}" alt="Clearpay"></a>
</div>

<script type="text/javascript">
    jQuery(function ($) {
        $(".youtube-video").clearpayYoutube();
    });
   jQuery.noConflict();
</script>

<style>
.clearpay-banner {
    text-align: center;
    margin: 15px 20px;
}

.clearpay-banner img {
    max-width: 100%;
    max-height: 100%;
    text-align: center;
}
</style>
',
    'is_active'  => 1,
    'stores'     => array(0)
);

/** @var $block Mage_Cms_Model_Block */
$block = Mage::getModel('cms/block');

$block->load($blockData['identifier'], 'identifier');

if (!$block->isObjectNew()) {
    unset($blockData['identifier']);
}
$block->addData($blockData)->save();

$installer->endSetup();
