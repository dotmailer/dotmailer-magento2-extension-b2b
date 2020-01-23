<?php

namespace Dotdigitalgroup\B2b\Model\Config;

use Dotdigitalgroup\B2b\Model\SharedCatalog\Config;
use Magento\Backend\Block\Template\Context;

class Yesno extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Config
     */
    private $sharedCatalogConfig;

    /**
     * Yesno constructor.
     * @param Context $context
     * @param Config $sharedCatalogConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $sharedCatalogConfig,
        array $data = []
    ) {
        $this->sharedCatalogConfig = $sharedCatalogConfig;
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
        if (!$this->sharedCatalogConfig->isSharedCatalogEnabled()) {
            $element->setData('disabled', 1);
        }

        return parent::_getElementHtml($element);
    }
}
