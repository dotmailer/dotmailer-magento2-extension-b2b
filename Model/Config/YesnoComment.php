<?php

namespace Dotdigitalgroup\B2b\Model\Config;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;
use Magento\Backend\Block\Template\Context;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;

class YesnoComment extends AbstractBlock implements CommentInterface
{
    /**
     * @var Config
     */
    private $sharedCatalogConfig;

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * YesnoComment constructor.
     *
     * @param Context $context
     * @param Config $sharedCatalogConfig
     * @param EmailHelper $emailHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $sharedCatalogConfig,
        EmailHelper $emailHelper,
        array $data = []
    ) {
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->emailHelper = $emailHelper;
        parent::__construct($context, $data);
    }

    public function getCommentText($elementValue)
    {
        $currentWebsite = $this->emailHelper->getWebsiteForSelectedScopeInAdmin();
        if (!$this->sharedCatalogConfig->isSharedCatalogEnabled($currentWebsite->getId())) {
            $websitePath = $currentWebsite->getId() != 0 ? '/website/' . $currentWebsite->getId() : '';
            $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/btob' . $websitePath);
            return "Click <a href='$url'>here</a> to enable Shared Catalog";
        }
    }
}
