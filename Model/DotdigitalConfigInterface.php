<?php

namespace Dotdigitalgroup\B2b\Model;

use Dotdigitalgroup\B2b\Helper\ConfigInterface;

interface DotdigitalConfigInterface
{
    const CONFIGURATION_PATHS = [
        ConfigInterface::XML_PATH_CONNECTOR_B2B_CUSTOMER_TYPE,
        ConfigInterface::XML_PATH_CONNECTOR_B2B_COMPANY,
        ConfigInterface::XML_PATH_CONNECTOR_B2B_COMPANY_STATUS,
        ConfigInterface::XML_PATH_CONNECTOR_B2B_STORE_CREDIT_BALANCE,
        ConfigInterface::XML_PATH_CONNECTOR_SYNC_SHARED_CATALOG_ENABLED,
        ConfigInterface::XML_PATH_CONNECTOR_SYNC_QUOTE_ENABLED,
    ];
}
