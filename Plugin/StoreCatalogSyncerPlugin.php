<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Dotdigitalgroup\Email\Model\Sync\Catalog\Exporter;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Catalog;
use Dotdigitalgroup\Email\Model\Sync\Catalog\SyncContextService;
use Dotdigitalgroup\Email\Model\Connector\KeyValidator;

class StoreCatalogSyncerPlugin
{
    /**
     * @var Config
     */
    private $sharedCatalogConfig;

    /**
     * @var Exporter
     */
    private $exporter;

    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @var KeyValidator
     */
    private $validator;

    /**
     * @var SyncContextService
     */
    private $contextService;

    /**
     * @param Config $sharedCatalogConfig
     * @param Exporter $exporter
     * @param Catalog $catalog
     * @param SyncContextService $contextService
     * @param KeyValidator $keyValidator
     */
    public function __construct(
        Config $sharedCatalogConfig,
        Exporter $exporter,
        Catalog $catalog,
        SyncContextService $contextService,
        KeyValidator $keyValidator
    ) {
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->exporter = $exporter;
        $this->catalog = $catalog;
        $this->contextService = $contextService;
        $this->validator = $keyValidator;
    }

    /**
     * @param \Dotdigitalgroup\Email\Model\Sync\Catalog\StoreCatalogSyncer $storeCatalogSyncer
     * @param array $result
     * @param array $productsToProcess
     * @param string $storeId
     * @param string $websiteId
     * @param string $catalogName
     * @return array[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterSyncByStore(
        \Dotdigitalgroup\Email\Model\Sync\Catalog\StoreCatalogSyncer $storeCatalogSyncer,
        $result,
        $productsToProcess,
        $storeId,
        $websiteId,
        $catalogName
    ) {
        if (!$this->sharedCatalogConfig->isSharedCatalogSyncEnabled($websiteId)) {
            return $result;
        }

        $this->contextService->setModule('B2b');
        $sharedCatalogs = $this->catalog->getSharedCatalogList();
        $skusToProcess = $this->catalog->getSkusToProcess($productsToProcess);

        foreach ($sharedCatalogs as $catalog) {
            $this->contextService->setCustomerGroupId($catalog['customer_group_id']);

            $productItems = $this->catalog->getProductsItemsInSharedCatalog(
                $skusToProcess,
                $catalog['customer_group_id']
            );

            $productsToProcessFromCatalog = $this->catalog->getProductIdsToProcess($productItems);

            if (!$productsToProcessFromCatalog) {
                continue;
            }

            $products = $this->exporter->exportCatalog($storeId, $productsToProcessFromCatalog);

            if ($products) {
                $cleanSharedCatalogName = $this->validator->cleanLabel($catalog['name'], '_');
                $sharedCatalogImportType = $catalogName . '_' . $cleanSharedCatalogName;

                $result[$sharedCatalogImportType] = [
                    'products' => $products,
                    'websiteId' => $websiteId
                ];
            }
        }

        return $result;
    }
}
