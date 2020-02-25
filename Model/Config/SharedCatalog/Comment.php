<?php

namespace Dotdigitalgroup\B2b\Model\Config\SharedCatalog;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;
use Magento\Backend\Block\Template\Context;
use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;

class Comment extends AbstractBlock implements CommentInterface
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
     * SharedCatalogComment constructor.
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

    /**
     * @param string $elementValue
     * @return string|null
     */
    public function getCommentText($elementValue)
    {
        $currentWebsite = $this->emailHelper->getWebsiteForSelectedScopeInAdmin();
        $catalogSyncEnabledForScope = $this->emailHelper->isCatalogSyncEnabled($currentWebsite->getId());
        $sharedCatalogSyncEnabledForScope = $this->sharedCatalogConfig->isSharedCatalogEnabled(
            $currentWebsite->getId()
        );

        if ($sharedCatalogSyncEnabledForScope && $catalogSyncEnabledForScope) {
            return null;
        }

        $comment = "Please ";

        if (!$sharedCatalogSyncEnabledForScope) {
            $websitePath = $currentWebsite->getId() != 0 ? '/website/' . $currentWebsite->getId() : '';
            $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/btob' . $websitePath);
            $comment .= "<a href='$url'>enable Shared Catalog</a>";
        }
        if (!$sharedCatalogSyncEnabledForScope && !$catalogSyncEnabledForScope) {
            $comment .= " and";
        }
        if (!$catalogSyncEnabledForScope) {
            $comment .= " enable regular catalog sync";
        }

        $comment .= ".";

        return $comment;
    }
}
