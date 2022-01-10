<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\DotdigitalConfigInterface;
use Dotdigitalgroup\Email\Model\Sync\Integration\DotdigitalConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProviderPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ConfigProviderPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param DotdigitalConfig $subject
     * @param $result
     * @param string|int $storeId
     * @return array
     */
    public function afterGetConfigByStore(DotdigitalConfig $subject, $result, $storeId)
    {
        foreach (DotdigitalConfigInterface::CONFIGURATION_PATHS as $path) {
            $keys = explode("/", $path);
            $configValue = $this->scopeConfig->getValue(
                $path,
                ScopeInterface::SCOPE_STORES,
                $storeId
            );
            $result[$keys[0]][$keys[1]][$keys[2]] = (string) $configValue;
        }

        return $result;
    }
}
