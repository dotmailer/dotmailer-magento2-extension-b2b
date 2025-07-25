# Dotdigital for Adobe Commerce B2B
[![Packagist Version](https://img.shields.io/packagist/v/dotdigital/dotdigital-magento2-extension-b2b?color=green&label=stable)](https://github.com/dotmailer/dotmailer-magento2-extension-b2b/releases)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](LICENSE.md)
  
## Overview
This module is dedicated to Magento merchants that also sell to other businesses. You may be running a B2B model only or hybrid model B2B/B2C, but as long as you’re using the **B2B for Adobe Commerce** module, you can benefit from this separate B2B extension and put all your B2B data to work with Dotdigital.
  
## Requirements
- Requires Magento 2.3.7+
- Requires `magento/module-b2b`
- Requires Dotdigital extension versions:
  - `Dotdigitalgroup_Email` 4.32.0+
  - `Dotdigitalgroup_Enterprise` 1.10.0+ (if used)

## Installation
We encourage merchants to install our core modules via our combined **Dotdigital - Marketing Automation** extension, available on the [Magento Marketplace](https://marketplace.magento.com/dotdigital-dotdigital-magento2-os-package.html).

**Steps:**
1. First, ‘purchase’ the [core extension](https://marketplace.magento.com/dotdigital-dotdigital-magento2-os-package.html).
2. Any existing `require` instructions in your composer.json relating to `dotmailer/*` packages must be removed.
3. Now, require the correct packages.
```
composer require dotdigital/dotdigital-magento2-os-package
composer require dotdigital/dotdigital-magento2-extension-enterprise
composer require dotdigital/dotdigital-magento2-extension-b2b
```

## Activation
- Ensure you have set valid API credentials in **Configuration > Dotdigital > Account Settings**
- Enable Shared Catalog and B2B Quote in **General > B2B Features** to use the related syncs.

## Changelog

### 1.9.0

##### What's new
- The `TierPriceFinderPlugin` is removed. Tier prices are now exported and synced via the Dotdigitalgroup_Email module.

### 1.8.5

##### Improvements
- We removed implicitly null types for compatibility with PHP 8.4.
- All outstanding PHPStan and coding standards errors have been resolved.

### 1.8.4

##### Improvements
- We updated our packages in composer.json for compatibility with magento/extension-b2b v1.5.0

### 1.8.3

##### Bug fixes
- We added a plugin to fix processing of in-progress B2B_Quotes imports.

### 1.8.2

##### Bug fixes
- Our requirement for `psr/log` has been updated for Magento 2.4.7.

### 1.8.1 

##### Improvements
- Our CustomerPlugin does not now require company statuses to match fixed values.

### 1.8.0

##### What's new
- The module now requires PHP 7.4+ and Magento 2.3.7+.

### 1.7.1

##### Improvements
- We've updated the module's composer requirements in line with the core code it uses.

### 1.7.0

##### Improvements
- We replaced usages of `SearchResultsFactory` and `SearchResults` classes with `SearchResultsInterfaceFactory` and `SearchResultsInterface` respectively.

### 1.6.0

##### Improvements 
- We updated some class references in line with the contact sync refactor in the Email module.

### 1.5.3

##### Improvements
- We updated a class reference in line with the automated setup feature in the Email module.

### 1.5.2

##### Improvements
- The B2B quote data migration now inherits from a new abstract class in the Email module.
- PHP 7.2 is now a minimum requirement for running this module.

### 1.5.1

##### Bug fixes
- We fixed a bug with missing company admins breaking the contact sync.
- We added the `indexType` param to our `index` schema definitions (this is required for PHP 8.1).

### 1.5.0

##### What's new
- This module has been renamed `dotdigital/dotdigital-magento2-extension-b2b`.

##### Improvements
- We've added a new plugin to provide additional configuration values to our integration insight data cron.
- We've added a new plugin to facilitate data migration by table.
- We've updated the plugin that fetches shared catalogs for sync, in line with our catalog sync improvements.
- `setup_version` has been removed from module.xml; in the Dashboard, we now use composer.json to provide the current active module version.
- Our ACL resources are now translatable.
- We updated the function of the 'Reset B2B Quotes' button in **Dotdigital > Developer**, in line with changes in the `Dotdigitalgroup_Email` module.
- We’ve prevented missing companies from blocking contact sync.

### 1.0.0
  
###### What’s new
- Merchants using Magento's B2B module can now integrate with Dotdigital. Extra data is mapped for Companies, and new syncs are available for Shared Catalogs and negotiable B2B Quotes.
