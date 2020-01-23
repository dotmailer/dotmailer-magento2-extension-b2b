<?php

namespace Dotdigitalgroup\B2b\Block\Adminhtml\Config\Report;

use Dotdigitalgroup\Email\Block\Adminhtml\Config\Report\AbstractConfigField;

class SharedCatalog extends AbstractConfigField
{
    /**
     * @deprecated
     * @var string
     */
    public $buttonLabel = 'Shared Catalog Report';

    /**
     * @var string
     */
    protected $linkUrlPath = 'dotdigitalgroup_email/importer/index';
}
