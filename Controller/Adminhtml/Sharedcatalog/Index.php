<?php

namespace Dotdigitalgroup\B2b\Controller\Adminhtml\Sharedcatalog;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Dotdigitalgroup_B2b::sharedcatalog';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Dotdigitalgroup_B2b::sharedcatalog');
        $resultPage->addBreadcrumb(__('Shared Catalog'), __('Shared Catalog'));
        $resultPage->addBreadcrumb(__('Reports'), __('Reports'));
        $resultPage->getConfig()->getTitle()->prepend(__('Shared Catalog Report'));

        return $resultPage;
    }
}
