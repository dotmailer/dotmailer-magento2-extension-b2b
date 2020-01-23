<?php

namespace Dotdigitalgroup\B2b\Helper;

use Magento\Company\Api\Data\CompanyInterface;
use Magento\Customer\Model\Customer;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Api\Data\WebsiteInterface;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\CompanyCredit\Api\CreditDataProviderInterface;

class Data extends AbstractHelper
{
    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * @var CompanyManagementInterface
     */
    private $companyRepository;

    /**
     * @var CreditDataProviderInterface
     */
    private $creditDataProvider;

    /**
     * @param Context $context
     * @param EmailHelper $emailHelper
     */
    public function __construct(
        Context $context,
        EmailHelper $emailHelper,
        CompanyManagementInterface $companyRepository,
        CreditDataProviderInterface $creditDataProvider
    ) {
        $this->emailHelper = $emailHelper;
        $this->companyRepository = $companyRepository;
        $this->creditDataProvider = $creditDataProvider;
        parent::__construct($context);
    }

    /**
     * @param WebsiteInterface $website
     * @return array
     */
    public function getB2bAttributes(WebsiteInterface $website)
    {
        $store = $website->getDefaultStore();
        $mappedDataFields = $this->scopeConfig->getValue(
            'connector_data_mapping/b2b_extra_data',
            ScopeInterface::SCOPE_STORE,
            $store->getId()
        );

        return $mappedDataFields ?: [];
    }

    /**
     * @param Customer $customer
     * @return CompanyInterface
     */
    public function getCompanyForCustomer(Customer $customer)
    {
        return $this->companyRepository->getByCustomerId($customer->getId());
    }

    /**
     * @param CompanyInterface $company
     * @return \Magento\CompanyCredit\Api\Data\CreditDataInterface
     */
    public function getCreditDataForCompany(CompanyInterface $company)
    {
        return $this->creditDataProvider->get($company->getId());
    }

    /**
     * @param CompanyInterface $company
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCompanyAdmin(CompanyInterface $company)
    {
        return $this->companyRepository->getAdminByCompanyId($company->getId());
    }
}
