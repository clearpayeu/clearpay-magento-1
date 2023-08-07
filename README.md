[![deprecated](https://img.shields.io/badge/stability-deprecated-red)](https://business.adobe.com/blog/basics/support-magento-1-software-ends-june-30-2020)

## 1.1 New Clearpay Installation

This section outlines the steps to install the Clearpay plugin on your Magento instance for the first time.

Magento can be installed in any folder on your server. For the purposes of this document, `[MAGENTO]` will refer to the root folder where Magento is installed.

1. The Clearpay plugin for Magento 1 is available as a `.zip` or `tar.gz` file from the Clearpay GitHub repository
1. Unzip the file and copy everything in `/src/` to `[MAGENTO]/`
1. Login to the Magento Admin and navigate to "System > Cache Management"
1. Flush the cache storage by selecting _Flush Cache Storage_

## 1.2 Website Configuration

Clearpay operates under a list of assumptions based on Magento configurations. To align with these assumptions, the Magento configurations must reflect the below.

1. **Website Currency must be set to GBP**

    Inside the Magento Admin, navigate to "System > Configuration > Currency Setup". Set the base, display and allowed currency as appropriate to match your Clearpay Merchant Account.

1. **Postal Code must be mandatory**

    Inside the Magento Admin, navigate to "System > Configuration > General". Ensure _Postal Code_ is **not** configured as optional for any country that Clearpay is being applied to.

## 1.3 Clearpay Merchant Setup

To configure your Clearpay Merchant Account credentials in the Magento Admin, please complete the steps below. The prerequisite for this section is to obtain a Clearpay Merchant ID and Secret Key from Clearpay.

1. Inside the Magento Admin, navigate to "System > Configuration > Sales > Payment Methods > Clearpay"
1. Enter the _Merchant ID_ and _Merchant Secret Key_ (provided by Clearpay)
1. Enable Clearpay by selecting "Yes" from the _Enabled_ dropdown
1. Configure the _API Mode_ - select _Sandbox_ for testing on a staging instance, or _Production_ for a live website with legitimate transactions
1. Save the configuration
1. Click the _Update Payment Limits_ button to retrieve the Minimum and Maximum Clearpay Order values

## 1.4 Clearpay Display Configuration

1. Inside the Magento Admin, navigate to "System > Configuration > Sales > Clearpay"
1. Enable _Debug Mode_ to log transactions and additional valuable data
1. Configure the display of the Clearpay elements presented on Product Detail Pages (PDP), the cart page and at the checkout
1. After saving any changes, navigate to "System > Cache Management"
1. Flush the cache storage by selecting _Flush Cache Storage_

## 1.5 Upgrade Of Clearpay Installation

This section outlines the steps to upgrade an existing installation of the Clearpay plugin to a new version.

The process of upgrading the Clearpay plugin for Magento 1 involves the complete removal of all plugin files, followed by copying the new files.

`[MAGENTO]` will refer to the root folder where you have installed your version of Magento.

1. Remove folder: `[MAGENTO]/app/code/community/Clearpay`
1. Remove folder: `[MAGENTO]/app/design/adminhtml/default/default/template/clearpay`
1. Remove file: `[MAGENTO]/app/design/frontend/base/default/layout/clearpay.xml`
1. Remove folder: `[MAGENTO]/app/design/frontend/base/default/template/clearpay`
1. Remove file: `[MAGENTO]/app/etc/modules/Clearpay_Clearpay.xml`
1. Remove folder: `[MAGENTO]/js/Clearpay`
1. Remove folder: `[MAGENTO]/skin/frontend/base/default/clearpay`
1. The Clearpay plugin for Magento 1 is available as a `.zip` or `tar.gz` file from the Clearpay GitHub repository
1. Unzip the file and copy everything in `/src/` to `[MAGENTO]/`
1. Login to the Magento Admin and navigate to "System > Cache Management"
1. Flush the cache storage by selecting _Flush Cache Storage_
