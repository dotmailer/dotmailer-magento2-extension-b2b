<?php

namespace Dotdigitalgroup\B2b\Model;

use Dotdigitalgroup\B2b\Model\BulkCatalogUpdaterFactory;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Magento\Catalog\Api\Data\TierPriceInterface;
use Magento\Catalog\Pricing\Price\TierPrice;

class BulkCatalogUpdateManager
{
    /**
     * @var BulkCatalogUpdaterFactory
     */
    private $bulkCatalogUpdaterFactory;

    /**
     * @var BulkCatalogUpdater
     */
    private $bulkCatalogUpdater;

    /**
     * @var Config
     */
    private $sharedCatalogConfig;

    /**
     * @param BulkCatalogUpdaterFactory $bulkCatalogUpdaterFactory
     * @param Config $sharedCatalogConfig
     */
    public function __construct(
        BulkCatalogUpdaterFactory $bulkCatalogUpdaterFactory,
        Config $sharedCatalogConfig
    ) {
        $this->bulkCatalogUpdaterFactory = $bulkCatalogUpdaterFactory;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
    }

    /**
     * Add skus to bulk update.
     *
     * @param TierPriceInterface[] $pricesData
     */
    public function addProductSkusToBulkUpdate(array $pricesData)
    {
        foreach ($pricesData as $tierPrice) {
            if (!$this->sharedCatalogConfig->isSharedCatalogSyncEnabled($tierPrice->getWebsiteId())) {
                continue;
            }
            $this->getBulkCatalogUpdater()->addProductSku($tierPrice->getSku());
        }
    }

    /**
     * Complete update.
     */
    public function completeUpdate()
    {
        $this->getBulkCatalogUpdater()->completeUpdate();
        unset($this->bulkCatalogUpdater);
    }

    /**
     * Get bulk catalog updater.
     *
     * @return BulkCatalogUpdater
     */
    private function getBulkCatalogUpdater()
    {
        return $this->bulkCatalogUpdater
            ?: $this->bulkCatalogUpdater = $this->bulkCatalogUpdaterFactory->create();
    }
}
