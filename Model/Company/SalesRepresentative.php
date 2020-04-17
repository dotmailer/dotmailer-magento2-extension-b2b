<?php

namespace Dotdigitalgroup\B2b\Model\Company;

use Magento\Company\Model\Company;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;

class SalesRepresentative
{
    /**
     * @var UserCollectionFactory
     */
    private $userCollectionFactory;

    /**
     * SalesRepresentative constructor.
     * @param UserCollectionFactory $userCollectionFactory
     */
    public function __construct(
        UserCollectionFactory $userCollectionFactory
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * @param Company $company
     * @return \Magento\Framework\DataObject
     */
    public function getUserByCompany(Company $company)
    {
        return $this->userCollectionFactory->create()
            ->addFieldToFilter('main_table.user_id', $company->getSalesRepresentativeId())
            ->getFirstItem();
    }
}
