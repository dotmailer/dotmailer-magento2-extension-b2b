<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Dotdigitalgroup\Email\Model\Sync\Catalog\Exporter;
use Dotdigitalgroup\Email\Model\ImporterFactory;
use Dotdigitalgroup\Email\Logger\Logger;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Catalog;

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
        Catalog $catalog
    ) {
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->exporter = $exporter;
        $this->importerFactory = $importerFactory;
        $this->logger = $logger;
        $this->catalog = $catalog;
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

        $sharedCatalogs = $this->catalog->getSharedCatalogList();
        $skusToProcess = $this->catalog->getSkusToProcess($productsToProcess);

        foreach ($sharedCatalogs as $catalog) {

            $productItems = $this->catalog->getProductsItemsInSharedCatalog(
                $skusToProcess,
                $catalog['customer_group_id']
            );

            $productsToProcessFromCatalog = $this->catalog->getProductIdsToProcess($productItems);

            if (!$productsToProcessFromCatalog) {
                continue;
            }

            $products = $this->exporter->exportCatalog($storeId, $productsToProcessFromCatalog, 'B2b');

            if ($products) {
                $success = $this->importerFactory->create()
                    ->registerQueue(
                        $importType . '_' . str_replace(' ', '_', $catalog['name']),
                        $products,
                        \Dotdigitalgroup\Email\Model\Importer::MODE_BULK,
                        $websiteId
                    );

                if ($success) {
                    $msg = 'Shared catalog ' . $importType . ' registered with Importer';
                    $this->logger->info($msg);
                }
            }
        }

        return $result;
    }
}
