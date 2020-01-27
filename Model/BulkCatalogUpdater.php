<?php

namespace Dotdigitalgroup\B2b\Model;

use Dotdigitalgroup\Email\Model\ResourceModel\Catalog as CatalogResource;

class BulkCatalogUpdater
{
    /**
     * @var CatalogResource
     */
    private $catalogResource;

    /**
     * @var array
     */
    private $productSkus = [];

    /**
     * @param CatalogResource $catalogResource
     */
    public function __construct(CatalogResource $catalogResource)
    {
        $this->catalogResource = $catalogResource;
    }

    /**
     * @param string $productSku
     */
    public function addProductSku(string $productSku)
    {
        $this->productSkus[] = $productSku;
    }

    /**
     * Set all updated products to unprocessed
     */
    public function completeUpdate()
    {
        if (empty($this->productSkus)) {
            return;
        }

        $products = $this->catalogResource
            ->getProductsCollectionBySku($this->productSkus)
            ->toArray();

        $this->catalogResource->setUnprocessedByIds(array_column($products, 'entity_id'));
    }
}
