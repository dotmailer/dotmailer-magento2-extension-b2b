<?php

namespace Dotdigitalgroup\B2b\Observer;

use Magento\Company\Model\Company;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Company\Model\ResourceModel\Customer as CustomerResource;
use Dotdigitalgroup\Email\Model\ResourceModel\Contact as ContactResource;

class CompanyUpdated implements ObserverInterface
{
    /**
     * @var CustomerResource
     */
    private $customerResource;

    /**
     * @var ContactResource
     */
    private $contactResource;

    /**
     * @param CustomerResource $customerResource
     * @param ContactResource $contactResource
     */
    public function __construct(
        CustomerResource $customerResource,
        ContactResource $contactResource
    ) {
        $this->customerResource = $customerResource;
        $this->contactResource = $contactResource;
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Company $company */
        $company = $observer->getData('object');

        if ($observer->getEvent()->getName() === 'company_save_after' && !$company->hasDataChanges()) {
            // company was saved, but no changes were made
            return;
        }

        // flag company customers for reimport
        $this->contactResource->resetContacts(
            $this->customerResource->getCustomerIdsByCompanyId($company->getId())
        );
    }
}
