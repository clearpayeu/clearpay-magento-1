<?php

/**
 * @package   Clearpay_Clearpay
 * @author    Clearpay
 * @copyright 2016-2018 Clearpay https://www.clearpay.co.uk
 */
class Clearpay_Clearpay_Block_Banner extends Mage_Core_Block_Template
{
    const XML_CONFIG_PREFIX = 'clearpay/banner/';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_CONFIG_PREFIX . 'enabled');
    }

    /**
     * @return array
     */
    public function getCssSelector()
    {
        $selectors = Mage::getStoreConfig(self::XML_CONFIG_PREFIX . 'banner_block_selector');
        return explode("\n", $selectors);
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        return array(
            'selector'  => $this->getCssSelector(),
            'className' => 'clearpay-banner'
        );
    }

    /**
     * @param string $scriptUrl
     * @param bool   $addModuleVersion
     *
     * @return string
     */
    public function getScriptHtml($scriptUrl, $addModuleVersion = true)
    {
        if ($addModuleVersion) {
            $scriptUrl .= "?v=" . $this->getModuleVersion();
        }

        return "document.write('<script src=\"" . $scriptUrl . "\">" . '<\/script>\');';
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        /** @var Mage_Core_Model_Config_Element $moduleConfig */
        $moduleConfig = Mage::getConfig()->getModuleConfig($this->getModuleName());
        return (string)$moduleConfig->version;
    }
}
