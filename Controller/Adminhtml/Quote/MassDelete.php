<?php

namespace Dotdigitalgroup\B2b\Controller\Adminhtml\Quote;

use Dotdigitalgroup\B2b\Model\ResourceModel\NegotiableQuote;
use Dotdigitalgroup\B2b\Model\ResourceModel\NegotiableQuote\CollectionFactory;
use Dotdigitalgroup\Email\Helper\MassDeleteCsrf;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends MassDeleteCsrf
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Dotdigitalgroup_B2b::quotes';

    /**
     * MassDelete constructor.
     * @param NegotiableQuote $collectionResource
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        NegotiableQuote $collectionResource,
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->collectionResource = $collectionResource;
        parent::__construct($context);
    }
}
