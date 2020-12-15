<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\BulkCatalogUpdateManager;
use Magento\SharedCatalog\Model\ResourceModel\ProductItem\Price\Consumer;

class SharedCatalogPriceConsumerPlugin
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
     * Update all product records when consumer operations are complete
     *
     * @param Consumer $consumer
     */
    public function afterProcessOperations(Consumer $consumer)
    {
        $this->bulkCatalogUpdateManager->completeUpdate();
    }
}
