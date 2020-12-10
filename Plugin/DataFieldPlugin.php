<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Helper\ConfigInterface;
use Dotdigitalgroup\Email\Helper\Data;
use Dotdigitalgroup\Email\Model\Connector\Datafield;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;

class DataFieldPlugin
{
    const DATA_MAPPING_PATH_PREFIX = 'b2b_extra_data';

    /**
     * @var Config
     */
    private $sharedCatalogConfig;

    /**
     * @var Data
     */
    private $emailHelper;

    /**
     * @param Config $sharedCatalogConfig
     * @param Data $emailHelper
     */
    public function __construct(
        Config $sharedCatalogConfig,
        Data $emailHelper
    ) {
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->emailHelper = $emailHelper;
    }

    /**
     * @param Datafield $dataField
     * @param bool $withXmlPathPrefixes
     * @return null
     */
    public function beforeGetContactDatafields(Datafield $dataField, bool $withXmlPathPrefixes = false)
    {
        $contactDataFields = ConfigInterface::CONTACT_B2B_DATA_FIELDS;
        $currentWebsite = $this->emailHelper->getWebsiteForSelectedScopeInAdmin();
        if (!$this->sharedCatalogConfig->isSharedCatalogEnabled($currentWebsite->getId())) {
            unset($contactDataFields['shared_catalog_name']);
        }

        $dataField->setContactDatafields($contactDataFields, self::DATA_MAPPING_PATH_PREFIX);
        return null;
    }
}
