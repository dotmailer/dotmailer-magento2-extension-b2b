<?php

namespace Dotdigitalgroup\B2b\Model\SharedCatalog;

use Dotdigitalgroup\Email\Model\ResourceModel\Contact\Collection as ContactCollection;
use Dotdigitalgroup\Email\Model\ResourceModel\Contact\CollectionFactory as ContactCollectionFactory;
use Magento\SharedCatalog\Api\Data\SharedCatalogInterface;

class Contacts
{
    /**
     * @var ContactCollectionFactory
     */
    private $contactCollectionFactory;

    /**
     * @param ContactCollectionFactory $contactCollectionFactory
     */
    public function __construct(ContactCollectionFactory $contactCollectionFactory)
    {
        $this->contactCollectionFactory = $contactCollectionFactory;
    }

    /**
     * @param SharedCatalogInterface $sharedCatalog
     * @return ContactCollection
     */
    public function getContactsForSharedCatalog(SharedCatalogInterface $sharedCatalog)
    {
        $contactCollection = $this->contactCollectionFactory->create();
        $contactCollection->join(
            ['ce' => $contactCollection->getTable('customer_entity')],
            'ce.entity_id = main_table.customer_id'
        )->addFieldToFilter(
            'ce.group_id',
            $sharedCatalog->getCustomerGroupId()
        );

        return $contactCollection;
    }
}
