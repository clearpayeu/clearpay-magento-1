# Clearpay Magento 1 Extension Changelog

## Version 3.2.0

_Wed 17 Feb 2021 (AEST)_

### Supported Editions & Versions

- Magento Community Edition (CE) version 1.7 and later.
- Magento Enterprise Edition (EE) version 1.13 and later.

### Highlights

- Introduced the Express Checkout feature.

---

## Version 3.1.1

_Wed 16 Dec 2020 (AEST)_

### Supported Editions & Versions

- Magento Community Edition (CE) version 1.7 and later.
- Magento Enterprise Edition (EE) version 1.13 and later.

### Highlights

- Improved compatibility with PHP 7.1+.
- Fixed a defect where the instalment amount may have been rounded incorrectly on product page.
- Fixed a defect where Clearpay may have appeared to be available for orders of £0 on cart page.
- Refined API calls for orders that consist of virtual products only.
- Improved user experience by hiding Clearpay when currency is misconfigured.

---

## Version 3.1.0

_Wed 2 Sep 2020 (AEST)_

### Supported Editions & Versions

- Magento Community Edition (CE) version 1.7 and later.
- Magento Enterprise Edition (EE) version 1.13 and later.

### Highlights

- Standardized modal content by using Clearpay Global JS Library.
- Fixed a defect where API Mode could be read from default scope instead of website scope.

---

## Version 3.0.5

_Wed 17 Jun 2020 (AEST)_

### Supported Editions & Versions

- Magento Community Edition (CE) version 1.7 and later.
- Magento Enterprise Edition (EE) version 1.13 and later.

### Highlights

- Addressed a known potential XSS vulnerability in FancyBox v2.x.
- Optimised image assets; using CDN-hosted images instead of bundled images.
- Improved checkout assets to automatically update instalment amounts when option to spend available store credit is selected/deselected.
- Improved handling of invalid Afterpay/Clearpay configuration.

---

## Version 3.0.4

_Thu 19 Dec 2019 (AEDT)_

### Supported Editions & Versions

- Magento Community Edition (CE) version 1.7 and later.
- Magento Enterprise Edition (EE) version 1.13 and later.

### Highlights

- Allow admin users to restrict Clearpay from a given set of product categories.
- Improved fallback mechanism for unsupported checkouts.
- Improved support for virtual products.
- Improved support for HTTP/2.
- Improved compatibility with the "OneStepCheckout" checkout extension.
- Hide Clearpay elements from PDP for Grouped Products.

---

## Version 3.0.3

_Wed 09 Oct 2019 (GMT)_

### Supported Editions & Versions

- Magento Community Edition (CE) version 1.7 and later.
- Magento Enterprise Edition (EE) version 1.13 and later.

### Highlights

- Improved handling of complex Website, Store and Store View configurations.
- Improved handling of decimal payment limits.
- Improved processing of customer registration during checkout.

---

## Version 3.0.2

_Wed 25 Sep 2019 (GMT)_

### Supported Editions & Versions

- Magento Community Edition (CE) version 1.7 and later.
- Magento Enterprise Edition (EE) version 1.13 and later.

### Highlights

Version 3.0.2 of the Clearpay Magento 1 Extension includes:

- Improved support for TLS 1.2.
- Improved support for guest checkouts, where customer name is not provided.
- Improved handling of orders created by unsupported checkout extensions.
- Improved handling of Magento session expiry.
- Improved address display in the Clearpay portals, where address state is not provided.
- Improved compatibility between Afterpay and Clearpay modules in multi-regional Magento installations.
- Extended checkout extension support to include Amasty OneStepCheckout.
- Removed potentially sensitive information from log files.

---

## Version 3.0.1

_Wed 10 Oct 2018 (GMT)_

### Supported Editions & Versions

- Magento Community Edition (CE) version 1.7 and later.
  - Clearpay Magento 1 Extension v3.0.1 has been tested and verified on an instance of Magento CE v1.7.0.2
- Magento Enterprise Edition (EE) version 1.13 and later.
  - Clearpay Magento 1 Extension v3.0.1 has been tested and verified on an instance of Magento EE v1.13

### Highlights

Version 3.0.1 of the Clearpay Magento 1 Extension includes:

- Improved support for "chunked" HTTP messages on GET API calls.
