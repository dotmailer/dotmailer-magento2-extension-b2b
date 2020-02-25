<?php

namespace Dotdigitalgroup\B2b\Controller\Adminhtml\Run;

use Dotdigitalgroup\Email\Helper\Data;
use Dotdigitalgroup\B2b\Api\NegotiableQuoteRepositoryInterface;

class NegotiableQuoteReset extends \Magento\Backend\App\AbstractAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Dotdigitalgroup_B2b::config';

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var NegotiableQuoteRepositoryInterface
     */
    private $negotiableQuote;

    /**
     * @var Data
     */
    private $helper;

    /**
     * NegotiableQuoteReset constructor.
     * @param NegotiableQuoteRepositoryInterface $negotiableQuote
     * @param \Magento\Backend\App\Action\Context $context
     * @param Data $data
     */
    public function __construct(
        NegotiableQuoteRepositoryInterface $negotiableQuote,
        \Magento\Backend\App\Action\Context $context,
        Data $data
    ) {
        $this->negotiableQuote  = $negotiableQuote;
        $this->messageManager = $context->getMessageManager();
        $this->helper         = $data;
        parent::__construct($context);
    }

    /**
     * Refresh negotiable quotes contacts.
     *
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $from = $params['from'];
        $to = $params['to'];
        if ($from && $to) {
            $error = $this->helper->validateDateRange(
                $from,
                $to
            );
            if (is_string($error)) {
                $this->messageManager->addErrorMessage($error);
            } else {
                $this->negotiableQuote->reset($from, $to);
                $this->messageManager->addSuccessMessage(__('Done.'));
            }
        } else {
            $this->negotiableQuote->reset();
            $this->messageManager->addSuccessMessage(__('Done.'));
        }

        $redirectUrl = $this->getUrl(
            'adminhtml/system_config/edit',
            ['section' => 'connector_developer_settings']
        );
        $this->_redirect($redirectUrl);
    }
}
