<?php

namespace Dotdigitalgroup\B2b\Model\Config\NegotiableQuote;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;
use Magento\Backend\Block\Template\Context;
use Dotdigitalgroup\B2b\Model\NegotiableQuote\Config;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;

class Comment extends AbstractBlock implements CommentInterface
{
    /**
     * @var Config
     */
    private $quoteConfig;

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * QuoteComment constructor.
     *
     * @param Context $context
     * @param Config $quoteConfig
     * @param EmailHelper $emailHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $quoteConfig,
        EmailHelper $emailHelper,
        array $data = []
    ) {
        $this->quoteConfig = $quoteConfig;
        $this->emailHelper = $emailHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param string $elementValue
     * @return string|null
     */
    public function getCommentText($elementValue)
    {
        $currentWebsite = $this->emailHelper->getWebsiteForSelectedScopeInAdmin();
        $quoteEnabledForScope = $this->quoteConfig->isB2bQuoteEnabled(
            $currentWebsite->getId()
        );

        if ($quoteEnabledForScope) {
            return null;
        }

        $websitePath = $currentWebsite->getId() != 0 ? '/website/' . $currentWebsite->getId() : '';
        $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/btob' . $websitePath);
        return "Please <a href='$url'>enable B2B Quote</a>";
    }
}
