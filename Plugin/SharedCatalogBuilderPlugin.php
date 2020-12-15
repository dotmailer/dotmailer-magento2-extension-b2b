<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\SharedCatalog\Name;
use Magento\SharedCatalog\Api\Data\SharedCatalogInterface;
use Magento\SharedCatalog\Model\SharedCatalogBuilder;

class SharedCatalogBuilderPlugin
{
    /**
     * @var Name
     */
    private $sharedCatalogNameService;

    /**
     * @param Name $sharedCatalogNameService
     */
    public function __construct(
        Name $sharedCatalogNameService
    ) {
        $this->sharedCatalogNameService = $sharedCatalogNameService;
    }

    /**
     * Get shared catalog name before save
     *
     * @param SharedCatalogBuilder $sharedCatalogBuilder
     * @param SharedCatalogInterface $sharedCatalog
     * @return SharedCatalogInterface
     */
    public function afterBuild(
        SharedCatalogBuilder $sharedCatalogBuilder,
        SharedCatalogInterface $sharedCatalog
    ) {
        $this->sharedCatalogNameService->setSharedCatalogName(
            $sharedCatalog->getName()
        );
        return $sharedCatalog;
    }
}
