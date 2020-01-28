<?php

namespace Dotdigitalgroup\B2b\Model\SharedCatalog;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\SharedCatalog\Api\SharedCatalogRepositoryInterface;
use Magento\SharedCatalog\Api\ProductManagementInterface;
use Magento\SharedCatalog\Api\ProductItemRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;

class Catalog
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SharedCatalogRepositoryInterface
     */
    private $sharedCatalogRepository;

    /**
     * @var ProductManagementInterface
     */
    private $productManagement;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductItemRepositoryInterface
     */
    private $productItemRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SharedCatalogRepositoryInterface $sharedCatalogRepository
     * @param ProductManagementInterface $productManagement
     * @param ProductRepositoryInterface $productRepository
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductItemRepositoryInterface $productItemRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SharedCatalogRepositoryInterface $sharedCatalogRepository,
        ProductManagementInterface $productManagement,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory $productCollectionFactory,
        ProductItemRepositoryInterface $productItemRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sharedCatalogRepository = $sharedCatalogRepository;
        $this->productManagement = $productManagement;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productItemRepository = $productItemRepository;
    }

    /**
     * Get collection of product skus from an array of product ids.
     *
     * @param array $productIds
     * @return array
     */
    public function getSkusToProcess(array $productIds)
    {
        $skus = [];
        $productCollection = $this->productCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToSelect('sku');

        foreach ($productCollection as $item) {
            $skus[] = $item->getSku();
        }

        return $skus;
    }

    /**
     * Retrieve SharedCatalog list
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSharedCatalogList()
    {
        $sharedCatalogs = [];
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $items = $this->sharedCatalogRepository->getList($searchCriteria)->getItems();
        foreach ($items as $item) {
            $sharedCatalogs[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'type' => $item->getType(),
                'customer_group_id' => $item->getCustomerGroupId()
            ];
        }
        return $sharedCatalogs;
    }

    /**
     * Search a shared catalog for an array of product skus.
     * Note that shared catalogs are 1:1 with customer groups.
     * Returns an array of product items pulled from the shared_catalog_product_item table.
     *
     * @param $productSkus
     * @param $customerGroupId
     * @return array
     */
    public function getProductsItemsInSharedCatalog($productSkus, $customerGroupId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('sku', $productSkus, 'in')
            ->addFilter('customer_group_id', $customerGroupId)
            ->create();
        return $this->productItemRepository->getList($searchCriteria)->getItems();
    }

    /**
     * Fetch actual product ids from an array of shared catalog items.
     *
     * @param $productItems
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductIdsToProcess($productItems)
    {
        $productIds = [];

        foreach ($productItems as $item) {
            $productModel = $this->productRepository->get($item->getSku());
            $productIds[] = $productModel->getId();
        }

        return $productIds;
    }
}
