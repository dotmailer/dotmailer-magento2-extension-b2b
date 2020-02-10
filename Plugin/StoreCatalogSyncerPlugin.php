<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Dotdigitalgroup\Email\Model\Sync\Catalog\Exporter;
use Dotdigitalgroup\Email\Model\ImporterFactory;
use Dotdigitalgroup\Email\Logger\Logger;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Catalog;
use Dotdigitalgroup\Email\Model\Sync\Catalog\SyncContextService;

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
     * @var ImporterFactory
     */
    private $importerFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Catalog
     */
    private $catalog;

    /**
     * @param Config $sharedCatalogConfig
     * @param Exporter $exporter
     * @param ImporterFactory $importerFactory
     * @param Logger $logger
     * @param Catalog $catalog
     */
    public function __construct(
        Config $sharedCatalogConfig,
        Exporter $exporter,
        ImporterFactory $importerFactory,
        Logger $logger,
        Catalog $catalog,
        SyncContextService $contextService
    ) {
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->exporter = $exporter;
        $this->importerFactory = $importerFactory;
        $this->logger = $logger;
        $this->catalog = $catalog;
        $this->contextService = $contextService;
    }

    /**
     * @param \Dotdigitalgroup\Email\Model\Sync\Catalog\StoreCatalogSyncer $storeCatalogSyncer
     * @param $result
     * @param $productsToProcess
     * @param $storeId
     * @param $websiteId
     * @param $importType
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterSyncByStore(
        \Dotdigitalgroup\Email\Model\Sync\Catalog\StoreCatalogSyncer $storeCatalogSyncer,
        $result,
        $productsToProcess,
        $storeId,
        $websiteId,
        $importType
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
                $sharedCatalogImportType = $importType . '_' . str_replace(' ', '_', $catalog['name']);

                $success = $this->importerFactory->create()
                    ->registerQueue(
                        $sharedCatalogImportType,
                        $products,
                        \Dotdigitalgroup\Email\Model\Importer::MODE_BULK,
                        $websiteId
                    );

                if ($success) {
                    $msg = 'Shared catalog ' . $sharedCatalogImportType . ' registered with Importer';
                    $this->logger->info($msg);
                }
            }
        }

        return $result;
    }
}
