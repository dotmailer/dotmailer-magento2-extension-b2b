<?php

namespace Dotdigitalgroup\B2b\Model\SharedCatalog;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\SharedCatalog\Model\Config as SharedCatalogConfig;

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
     * @param int $websiteId
     * @return bool
     */
    public function isSharedCatalogEnabled($websiteId = 0)
    {
        return $this->scopeConfig->isSetFlag(
            SharedCatalogConfig::CONFIG_SHARED_CATALOG,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }
}
