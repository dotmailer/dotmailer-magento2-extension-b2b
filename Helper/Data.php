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
     * @param CompanyManagementInterface $companyRepository
     * @param CreditDataProviderInterface $creditDataProvider
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
     * Get B2B attributes.
     *
     * @param WebsiteInterface $website
     * @return array
     */
    public function getB2bAttributes(WebsiteInterface $website)
    {
        $mappedDataFields = $this->scopeConfig->getValue(
            'connector_data_mapping/b2b_extra_data',
            ScopeInterface::SCOPE_WEBSITES,
            $website->getId()
        );

        return $mappedDataFields ?: [];
    }

    /**
     * Get company for customer.
     *
     * @param Customer $customer
     * @return CompanyInterface
     */
    public function getCompanyForCustomer(Customer $customer)
    {
        return $this->companyRepository->getByCustomerId($customer->getId());
    }

    /**
     * Get credit data for company.
     *
     * @param CompanyInterface $company
     * @return \Magento\CompanyCredit\Api\Data\CreditDataInterface
     */
    public function getCreditDataForCompany(CompanyInterface $company)
    {
        return $this->creditDataProvider->get($company->getId());
    }

    /**
     * Get company admin.
     *
     * @param CompanyInterface $company
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCompanyAdmin(CompanyInterface $company)
    {
        return $this->companyRepository->getAdminByCompanyId($company->getId());
    }
}
