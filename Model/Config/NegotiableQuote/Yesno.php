<?php

namespace Dotdigitalgroup\B2b\Model\Config\NegotiableQuote;

use Dotdigitalgroup\B2b\Model\NegotiableQuote\Config;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;
use Magento\Backend\Block\Template\Context;

class Yesno extends \Magento\Config\Block\System\Config\Form\Field
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
     * Yesno constructor.
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
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $currentWebsite = $this->emailHelper->getWebsiteForSelectedScopeInAdmin();
        if (!$this->quoteConfig->isB2bQuoteEnabled($currentWebsite->getId())) {
            $element->setData('disabled', 'disabled');
        }

        return parent::_getElementHtml($element);
    }
}
