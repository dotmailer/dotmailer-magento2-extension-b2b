<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Magento\Company\Model\ResourceModel\Customer as CustomerResource;
use Dotdigitalgroup\Email\Model\ResourceModel\Contact as ContactResource;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\LocalizedException;
use Magento\SharedCatalog\Controller\Adminhtml\SharedCatalog\Company\Assign;

class SharedCatalogCompanyAssignPlugin
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
     * @param ContactResource $contactResource
     * @param CustomerResource $customerResource
     */
    public function __construct(
        ContactResource $contactResource,
        CustomerResource $customerResource
    ) {
        $this->customerResource = $customerResource;
        $this->contactResource = $contactResource;
    }

    /**
     * @param Assign $assign
     * @param Json $result
     * @return Json
     */
    public function afterExecute(Assign $assign, Json $result)
    {
        try {
            $customerIds = $this->customerResource->getCustomerIdsByCompanyId(
                $assign->getRequest()->getParam('company_id')
            );
        } catch (LocalizedException $e) {
            $customerIds = null;
        }

        if ($customerIds) {
            $this->contactResource->resetContacts($customerIds);
        }

        return $result;
    }
}
