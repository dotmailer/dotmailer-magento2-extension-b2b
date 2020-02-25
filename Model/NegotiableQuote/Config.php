<?php

namespace Dotdigitalgroup\B2b\Model\NegotiableQuote;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\NegotiableQuote\Model\Config as QuoteConfig;
use Dotdigitalgroup\B2b\Helper\ConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var QuoteConfig
     */
    private $quoteConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param QuoteConfig $quoteConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        QuoteConfig $quoteConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->quoteConfig = $quoteConfig;
    }

    /**
     * Check if B2B Features > Enable B2B Quote is set to Yes.
     *
     * @param int $websiteId
     * @return bool
     */
    public function isB2bQuoteEnabled($websiteId = 0)
    {
        return $this->quoteConfig->isActive(
            ScopeInterface::SCOPE_WEBSITES,
            $websiteId
        );
    }

    /**
     * Check if Sync Settings > B2B Quote Enabled is set to Yes.
     * Note that B2B Features > Enable B2B Quote must also be enabled, so we check this as well.
     *
     * @param int $websiteId
     * @return bool
     */
    public function isB2bQuoteSyncEnabled($websiteId = 0)
    {
        return $this->isB2bQuoteEnabled($websiteId)
            && $this->scopeConfig->isSetFlag(
                ConfigInterface::XML_PATH_CONNECTOR_SYNC_QUOTE_ENABLED,
                ScopeInterface::SCOPE_WEBSITES,
                $websiteId
            );
    }
}
