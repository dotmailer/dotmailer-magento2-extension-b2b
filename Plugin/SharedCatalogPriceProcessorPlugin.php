<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\BulkCatalogUpdateManager;
use Dotdigitalgroup\B2b\Model\BulkCatalogUpdater;
use Magento\Catalog\Api\Data\TierPriceInterface;
use Magento\SharedCatalog\Model\ResourceModel\ProductItem\Price\PriceProcessor;

class SharedCatalogPriceProcessorPlugin
{
    /**
     * @var BulkCatalogUpdateManager
     */
    private $bulkCatalogUpdateManager;

    /**
     * @param BulkCatalogUpdateManager $bulkCatalogUpdateManager
     */
    public function __construct(
        BulkCatalogUpdateManager $bulkCatalogUpdateManager
    ) {
        $this->bulkCatalogUpdateManager = $bulkCatalogUpdateManager;
    }

    /**
     * After create prices update.
     *
     * @param PriceProcessor $priceProcessor
     * @param TierPriceInterface[] $pricesData
     * @return array
     */
    public function afterCreatePricesUpdate(PriceProcessor $priceProcessor, array $pricesData)
    {
        $this->bulkCatalogUpdateManager->addProductSkusToBulkUpdate($pricesData);
        return $pricesData;
    }

    /**
     * After create prices delete.
     *
     * @param PriceProcessor $priceProcessor
     * @param TierPriceInterface[] $pricesData
     * @return array
     */
    public function afterCreatePricesDelete(PriceProcessor $priceProcessor, array $pricesData)
    {
        $this->bulkCatalogUpdateManager->addProductSkusToBulkUpdate($pricesData);
        return $pricesData;
    }
}
