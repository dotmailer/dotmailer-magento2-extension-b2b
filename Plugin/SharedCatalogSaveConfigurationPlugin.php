<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\Email\Helper\Data;
use Magento\Framework\Controller\Result\Redirect;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Magento\SharedCatalog\Api\ProductManagementInterface;
use Magento\SharedCatalog\Model\Form\Storage\Wizard as WizardStorage;
use Dotdigitalgroup\Email\Model\ResourceModel\Catalog as CatalogResource;
use Magento\SharedCatalog\Model\Form\Storage\WizardFactory as WizardStorageFactory;
use Magento\SharedCatalog\Controller\Adminhtml\SharedCatalog\Configure\Save as SaveSharedCatalogAction;

class SharedCatalogSaveConfigurationPlugin
{
    /**
     * @var WizardStorageFactory
     */
    private $wizardStorageFactory;

    /**
     * @var CatalogResource
     */
    private $catalogResource;

    /**
     * @var Config
     */
    private $sharedCatalogConfig;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var ProductManagementInterface
     */
    private $productManagement;

    /**
     * @var array
     */
    private $originalSharedCatalogProducts;

    /**
     * @param WizardStorageFactory $wizardStorageFactory
     * @param CatalogResource $catalogResource
     * @param Config $sharedCatalogConfig
     * @param Data $helper
     * @param ProductManagementInterface $productManagement
     */
    public function __construct(
        WizardStorageFactory $wizardStorageFactory,
        CatalogResource $catalogResource,
        Config $sharedCatalogConfig,
        Data $helper,
        ProductManagementInterface $productManagement
    ) {
        $this->wizardStorageFactory = $wizardStorageFactory;
        $this->catalogResource = $catalogResource;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->helper = $helper;
        $this->productManagement = $productManagement;
    }

    /**
     * Get product SKUs in the shared catalog before applying changes
     *
     * @param SaveSharedCatalogAction $save
     * @return null
     */
    public function beforeExecute(SaveSharedCatalogAction $save)
    {
        $this->originalSharedCatalogProducts = $this->productManagement->getProducts(
            $save->getRequest()->getParam('catalog_id')
        );
        return null;
    }

    /**
     * Flag any products assigned or unassigned for reimport
     *
     * @param SaveSharedCatalogAction $save
     * @param Redirect $redirect
     * @return Redirect
     */
    public function afterExecute(SaveSharedCatalogAction $save, $redirect)
    {
        if (!$this->isSharedCatalogSyncEnabledForSelectedWebsite()) {
            return $redirect;
        }

        /** @var WizardStorage $wizardStorage */
        $wizardStorage = $this->wizardStorageFactory->create([
            'key' => $save->getRequest()->getParam('configure_key'),
        ]);

        $assignmentChanges = array_merge(
            array_diff($wizardStorage->getAssignedProductSkus(), $this->originalSharedCatalogProducts),
            $wizardStorage->getUnassignedProductSkus()
        );

        if (!empty($assignmentChanges)) {
            $products = $this->catalogResource
                ->getProductsCollectionBySku($assignmentChanges)
                ->toArray();

            $this->catalogResource->setUnprocessedByIds(array_column($products, 'entity_id'));
        }

        return $redirect;
    }

    /**
     * @return bool
     */
    private function isSharedCatalogSyncEnabledForSelectedWebsite()
    {
        return $this->sharedCatalogConfig->isSharedCatalogSyncEnabled(
            $this->helper->getWebsiteForSelectedScopeInAdmin()
        );
    }
}
