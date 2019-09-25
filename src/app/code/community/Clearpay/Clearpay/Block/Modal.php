<?php

class Clearpay_Clearpay_Block_Modal extends Mage_Core_Block_Template
{
    /**
     * Get the Modal Redirection URL
     *
     * @return string
     */
    public function getModalRedirection()
    {
        $url = '';

        switch (Mage::app()->getStore()->getCurrentCurrencyCode())
        {
            default:
                $url = 'https://www.clearpay.co.uk/terms';
                break;
        }

        return $url;
    }

    /**
     * Get the Desktop Modal Assets
     *
     * @return string
     */
    public function getDesktopModalAssets()
    {
        $src = '';
        $srcset = '';

        switch (Mage::app()->getStore()->getCurrentCurrencyCode())
        {
            default:
                $src     =  'https://static.afterpay.com/clearpay-lightbox-desktop.png';
                $srcset  =  'https://static.afterpay.com/clearpay-lightbox-desktop.png 1x,';
                $srcset .= ' https://static.afterpay.com/clearpay-lightbox-desktop@2x.png 2x';
                break;
        }

        $img = '<img class="clearpay-modal-image" src="'.$src.'" srcset="'.$srcset.'" alt="Clearpay" />';

        return $img;
    }

    /**
     * Get the Mobile Modal Assets
     *
     * @return string
     */
    public function getMobileModalAssets()
    {
        $src = '';
        $srcset = '';

        switch (Mage::app()->getStore()->getCurrentCurrencyCode())
        {
            default:
                $src     =  'https://static.afterpay.com/clearpay-lightbox-mobile.png';
                $srcset  =  'https://static.afterpay.com/clearpay-lightbox-mobile.png 1x,';
                $srcset .= ' https://static.afterpay.com/clearpay-lightbox-mobile@2x.png 2x';
                break;
        }

        $img = '<img class="clearpay-modal-image-mobile" src="'.$src.'" srcset="'.$srcset.'" alt="Clearpay" />';

        return $img;
    }


}
