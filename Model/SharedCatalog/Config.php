<?php

namespace Dotdigitalgroup\B2b\Model\SharedCatalog;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\SharedCatalog\Model\Config as SharedCatalogConfig;
use Dotdigitalgroup\B2b\Helper\ConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if B2B Features > Enable Shared Catalog is set to Yes.
     *
     * @param int $websiteId
     * @return bool
     */
    public function isSharedCatalogEnabled($websiteId = 0)
    {
        return $this->scopeConfig->isSetFlag(
            SharedCatalogConfig::CONFIG_SHARED_CATALOG,
            ScopeInterface::SCOPE_WEBSITES,
            $websiteId
        );
    }

    /**
     * Check if Sync Settings > Shared Catalog Enabled is set to Yes.
     * Note that B2B Features > Shared Catalogs must also be enabled, so we check this as well.
     *
     * @param int $websiteId
     * @return bool
     */
    public function isSharedCatalogSyncEnabled($websiteId = 0)
    {
        return $this->isSharedCatalogEnabled($websiteId)
            && $this->scopeConfig->isSetFlag(
                ConfigInterface::XML_PATH_CONNECTOR_SYNC_SHARED_CATALOG_ENABLED,
                ScopeInterface::SCOPE_WEBSITES,
                $websiteId
            );
    }
}
