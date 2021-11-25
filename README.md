# Dotdigital for Magento B2B
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](LICENSE.md)
## B2B module
  
### Overview
This module is dedicated to Magento merchants that also sell to other businesses. You may be running a B2B model only or hybrid model B2B/B2C, but as long as you’re using the **Magento Commerce for B2B** module, you can benefit from this separate B2B extension and put all your B2B data to work with Dotdigital.
  
### Requirements
Here are the requirements for Dotdigital for Magento Commerce B2B:
- Available from Magento 2.3+
- Requires `magento/module-b2b`
- Requires Dotdigital extension versions:
  - `Dotdigitalgroup_Email` 4.14.0+
  - `Dotdigitalgroup_Enterprise` 1.8.0+ (if used)
  
### Activation
- To enable the module, run:
```
composer require dotdigital/dotdigital-magento2-extension-b2b
bin/magento setup:upgrade
```
- Ensure you have set valid API credentials in **Configuration > Dotdigital > Account Settings**
- Enable Shared Catalog and B2B Quote in **General > B2B Features** to use the related syncs.

## 1.5.0-RC1

##### What's new
- This module has been renamed `dotdigital/dotdigital-magento2-extension-b2b`.
- We've added a new plugin to provide additional configuration values to our integration insight data cron.
- We've added a new plugin to facilitate data migration by table.

##### Improvements
- `setup_version` has been removed from module.xml; in the Dashboard, we now use composer.json to provide the current active module version.
- Our ACL resources are now translatable.
- We updated the function of the 'Reset B2B Quotes' button in **Dotdigital > Developer**, in line with changes in the `Dotdigitalgroup_Email` module. 

## 1.0.0
  
###### What’s new
- Merchants using Magento's B2B module can now integrate with Dotdigital. Extra data is mapped for Companies, and new syncs are available for Shared Catalogs and negotiable B2B Quotes.
