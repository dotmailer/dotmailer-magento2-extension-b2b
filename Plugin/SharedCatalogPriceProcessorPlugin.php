<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\BulkCatalogUpdateManager;
use Dotdigitalgroup\B2b\Model\BulkCatalogUpdater;
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
     * @param PriceProcessor $priceProcessor
     * @param array $pricesData
     * @return array
     */
    public function afterCreatePricesUpdate(PriceProcessor $priceProcessor, array $pricesData)
    {
        $this->bulkCatalogUpdateManager->addProductSkusToBulkUpdate($pricesData);
        return $pricesData;
    }

    /**
     * @param PriceProcessor $priceProcessor
     * @param array $pricesData
     * @return array
     */
    public function afterCreatePricesDelete(PriceProcessor $priceProcessor, array $pricesData)
    {
        $this->bulkCatalogUpdateManager->addProductSkusToBulkUpdate($pricesData);
        return $pricesData;
    }
}
