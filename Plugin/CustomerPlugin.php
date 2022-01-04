<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Helper\ConfigInterface;
use Dotdigitalgroup\B2b\Helper\Data;
use Dotdigitalgroup\B2b\Model\Company\SalesRepresentative;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;
use Dotdigitalgroup\Email\Logger\Logger;
use Dotdigitalgroup\Email\Model\Apiconnector\Customer;
use Magento\Company\Model\Company;
use Magento\Customer\Model\Customer as CustomerModel;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @var Logger
     */
    private $logger;

    /**
     * @var SalesRepresentative
     */
    private $salesRepresentative;

    /**
     * CustomerPlugin constructor.
     * @param Data $helper
     * @param EmailHelper $emailHelper
     * @param GroupRepository $groupRepository
     * @param Config $sharedCatalogConfig
     * @param Logger $logger
     * @param SalesRepresentative $salesRepresentative
     */
    public function __construct(
        Data $helper,
        EmailHelper $emailHelper,
        GroupRepository $groupRepository,
        Config $sharedCatalogConfig,
        Logger $logger,
        SalesRepresentative $salesRepresentative
    ) {
        $this->helper = $helper;
        $this->emailHelper = $emailHelper;
        $this->groupRepository = $groupRepository;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->logger = $logger;
        $this->salesRepresentative = $salesRepresentative;
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
            $this->setSalesRepresentative($customer, $company);
            $this->setCreditData($customer, $company);

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

    /**
     * @param CustomerModel $customer
     * @param Company $company
     */
    private function setSalesRepresentative(CustomerModel &$customer, Company $company)
    {
        try {
            $salesRepresentative = $this->salesRepresentative->getUserByCompany($company);

            $firstName = $salesRepresentative->getFirstName();
            $lastName = $salesRepresentative->getLastName();
            $representativeEmail = $salesRepresentative->getEmail();
            $customer->setData('sales_representative', $firstName . ' ' . $lastName);
            $customer->setData('sales_rep_email', $representativeEmail);
        } catch (\Exception $e) {
            $this->logger->debug((string) $e);
        }
    }

    /**
     * @param CustomerModel $customer
     * @param Company $company
     */
    private function setCreditData(CustomerModel &$customer, Company $company)
    {
        try {
            if ($creditData = $this->helper->getCreditDataForCompany($company)) {
                $customer->setStoreCreditBalance($creditData->getCreditLimit() - $creditData->getBalance());
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->debug(
                'Could not fetch credit data, customer store credit balance not set.',
                [(string) $e]
            );
        }
    }
}
