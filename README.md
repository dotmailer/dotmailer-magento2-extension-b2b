# dotdigital Engagement Cloud for Magento B2B
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](LICENSE.md)
## B2B module
  
### Overview
​​This module is dedicated to Magento merchants that also sell to other businesses. You may be running a B2B model only or hybrid model B2B/B2C, but as long as you’re using the **Magento Commerce for B2B** module, you can benefit from this separate dotdigital B2B extension and put all your B2B data to work with Engagement Cloud.
  
### Requirements
Here are the requirements for dotdigital for Magento Commerce B2B:
- Available from Magento 2.3+
- Requires `magento/module-b2b`
- Requires dotdigital extension versions:
  - dotdigitalgroup Email 4.3.6+
  - dotdigitalgroup Enterprise 1.0.6+ (if used)
  
### Activation
- To enable the module, run:
```
composer require dotmailer/dotmailer-magento2-extension-b2b
bin/magento setup:upgrade
```
- Ensure you have set valid API credentials in **Configuration > dotdigital > Account Settings**
- Enable Shared Catalog and B2B Quote in **General > B2B Features** to use the related syncs.
  
## 1.0.0
  
###### What’s new
- Merchants using Magento's B2B module can now integrate with Engagement Cloud. Extra data is mapped for Companies, and new syncs are available for Shared Catalogs and negotiable B2B Quotes.
