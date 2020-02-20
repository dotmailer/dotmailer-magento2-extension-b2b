<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Helper\ConfigInterface;
use Dotdigitalgroup\B2b\Helper\Data;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;
use Dotdigitalgroup\Email\Model\Apiconnector\Customer;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\ResourceModel\GroupRepository;

class CustomerPlugin
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * @var Config
     */
    private $sharedCatalogConfig;

    /**
     * @param Data $helper
     * @param EmailHelper $emailHelper
     * @param GroupRepository $groupRepository
     * @param Config $sharedCatalogConfig
     */
    public function __construct(
        Data $helper,
        EmailHelper $emailHelper,
        GroupRepository $groupRepository,
        Config $sharedCatalogConfig
    ) {
        $this->helper = $helper;
        $this->emailHelper = $emailHelper;
        $this->groupRepository = $groupRepository;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
    }

    /**
     * @param Customer $subject
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSetContactData(Customer $subject)
    {
        /** @var CustomerModel $customer */
        $customer = $subject->getModel();
        $sharedCatalogName = ConfigInterface::SHARED_CATALOG_NAME_DEFAULT;

        if ($company = $this->helper->getCompanyForCustomer($customer)) {
            $customer->setCompany($company->getCompanyName());
            $customer->setCompanyStatus(ConfigInterface::COMPANY_STATUS_LABELS[$company->getStatus()]);
            $customer->setCustomerType(
                $this->helper->getCompanyAdmin($company)->getId() === $customer->getId()
                    ? ConfigInterface::CUSTOMER_TYPE_COMPANY_ADMIN
                    : ConfigInterface::CUSTOMER_TYPE_COMPANY_USER
            );

            if ($creditData = $this->helper->getCreditDataForCompany($company)) {
                $customer->setStoreCreditBalance($creditData->getCreditLimit() - $creditData->getBalance());
            }

            $currentWebsite = $this->emailHelper->getWebsiteForSelectedScopeInAdmin();
            if ($this->sharedCatalogConfig->isSharedCatalogEnabled($currentWebsite->getId())
                && $customerGroup = $this->groupRepository->getById($customer->getGroupId())
            ) {
                $sharedCatalogName = $customerGroup->getCode();
            }
        } else {
            $customer->setCustomerType(ConfigInterface::CUSTOMER_TYPE_INDIVIDUAL_USER);
        }

        $customer->setSharedCatalogName($sharedCatalogName);

        return null;
    }
}
