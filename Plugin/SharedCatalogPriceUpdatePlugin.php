<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\BulkCatalogUpdater;
use Dotdigitalgroup\B2b\Model\BulkCatalogUpdaterFactory;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Magento\Bundle\Pricing\Price\TierPrice;
use Magento\SharedCatalog\Model\ResourceModel\ProductItem\Price\Consumer;
use Magento\SharedCatalog\Model\ResourceModel\ProductItem\Price\PriceProcessor;

class SharedCatalogPriceUpdatePlugin
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
     * @param PriceProcessor $priceProcessor
     * @param array $pricesData
     * @return array
     */
    public function afterCreatePricesUpdate(PriceProcessor $priceProcessor, array $pricesData)
    {
        $this->addProductSkusToBulkUpdate($pricesData);
        return $pricesData;
    }

    /**
     * @param PriceProcessor $priceProcessor
     * @param array $pricesData
     * @return array
     */
    public function afterCreatePricesDelete(PriceProcessor $priceProcessor, array $pricesData)
    {
        $this->addProductSkusToBulkUpdate($pricesData);
        return $pricesData;
    }

    /**
     * Update all product records when consumer operations are complete
     *
     * @param Consumer $consumer
     */
    public function afterProcessOperations(Consumer $consumer)
    {
        $this->getBulkCatalogUpdater()->completeUpdate();
        unset($this->bulkCatalogUpdater);
    }

    /**
     * @param array $pricesData
     */
    private function addProductSkusToBulkUpdate(array $pricesData)
    {
        foreach ($pricesData as $tierPrice) {
            /** @var TierPrice $tierPrice */
            if (!$this->sharedCatalogConfig->isSharedCatalogSyncEnabled($tierPrice->getWebsiteId())) {
                continue;
            }
            $this->getBulkCatalogUpdater()->addProductSku($tierPrice->getSku());
        }
    }

    /**
     * @return BulkCatalogUpdater
     */
    private function getBulkCatalogUpdater()
    {
        return $this->bulkCatalogUpdater
            ?: $this->bulkCatalogUpdater = $this->bulkCatalogUpdaterFactory->create();
    }
}
