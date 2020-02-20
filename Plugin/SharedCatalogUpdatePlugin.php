<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\SharedCatalog\Contacts;
use Dotdigitalgroup\Email\Model\ResourceModel\Contact as ContactResource;
use Magento\SharedCatalog\Api\Data\SharedCatalogInterface;
use Magento\SharedCatalog\Model\Repository;
use Magento\SharedCatalog\Model\SharedCatalogBuilder;

class SharedCatalogUpdatePlugin
{
    /**
     * @var string
     */
    private $sharedCatalogName;

    /**
     * @var Contacts
     */
    private $contacts;

    /**
     * @var ContactResource
     */
    private $contactsResource;

    /**
     * @param Contacts $contacts
     * @param ContactResource $contactResource
     */
    public function __construct(
        Contacts $contacts,
        ContactResource $contactResource
    ) {
        $this->contacts = $contacts;
        $this->contactsResource = $contactResource;
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
        $this->sharedCatalogName = $sharedCatalog->getName();
        return $sharedCatalog;
    }

    /**
     * Compare name after save
     *
     * @param Repository $repository
     * @param int $sharedCatalogId
     * @param SharedCatalogInterface $sharedCatalog
     * @return int
     */
    public function afterSave(
        Repository $repository,
        int $sharedCatalogId,
        SharedCatalogInterface $sharedCatalog
    ) {
        if ($this->sharedCatalogName != $sharedCatalog->getName()) {
            // shared catalog name changed
            $this->reimportSharedCatalogContacts($sharedCatalog);
        }
        return $sharedCatalogId;
    }

    /**
     * Flag contacts for reimport
     *
     * @param SharedCatalogInterface $sharedCatalog
     */
    private function reimportSharedCatalogContacts(SharedCatalogInterface $sharedCatalog)
    {
        $sharedCatalogContactsCollection = $this->contacts->getContactsForSharedCatalog($sharedCatalog);

        if ($sharedCatalogContactsCollection->count() > 0) {
            $contacts = $sharedCatalogContactsCollection->toArray();
            $this->contactsResource->resetContacts(array_column($contacts['items'], 'customer_id'));
        }
    }
}
