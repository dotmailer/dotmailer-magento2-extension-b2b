<?php

namespace Dotdigitalgroup\B2b\Model\Config\SharedCatalog;

use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;
use Magento\Backend\Block\Template\Context;

class Yesno extends \Magento\Config\Block\System\Config\Form\Field
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
     * Yesno constructor.
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $currentWebsite = $this->emailHelper->getWebsiteForSelectedScopeInAdmin();
        if (!$this->sharedCatalogConfig->isSharedCatalogEnabled($currentWebsite->getId())
            || !$this->emailHelper->isCatalogSyncEnabled($currentWebsite->getId())) {
            $element->setData('disabled', 'disabled');
        }

        return parent::_getElementHtml($element);
    }
}
