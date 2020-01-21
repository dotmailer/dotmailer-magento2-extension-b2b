<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Helper\Config;
use Dotdigitalgroup\B2b\Helper\Data;
use Dotdigitalgroup\Email\Model\Apiconnector\Customer;
use Magento\Customer\Model\Customer as CustomerModel;

class CustomerPlugin
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param Customer $subject
     * @return null
     */
    public function beforeSetContactData(Customer $subject)
    {
        /** @var CustomerModel $customer */
        $customer = $subject->getModel();

        if ($company = $this->helper->getCompanyForCustomer($customer)) {
            $customer->setCompany($company->getCompanyName());
            $customer->setCompanyStatus(Config::COMPANY_STATUS_LABELS[$company->getStatus()]);
            $customer->setCustomerType(
                $this->helper->getCompanyAdmin($company)->getId() === $customer->getId()
                    ? Config::CUSTOMER_TYPE_COMPANY_ADMIN
                    : Config::CUSTOMER_TYPE_COMPANY_USER
            );
            if ($creditData = $this->helper->getCreditDataForCompany($company)) {
                $customer->setStoreCreditBalance($creditData->getCreditLimit() - $creditData->getBalance());
            }
        } else {
            $customer->setCustomerType(Config::CUSTOMER_TYPE_INDIVIDUAL_USER);
        }

        return null;
    }
}
