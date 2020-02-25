<?php

namespace Dotdigitalgroup\B2b\Block\Adminhtml\Config\Report;

use Dotdigitalgroup\Email\Block\Adminhtml\Config\Report\AbstractConfigField;

class Quote extends AbstractConfigField
{
    /**
     * @deprecated
     * @var string
     */
    public $buttonLabel = 'B2B Quote Report';

    /**
     * @var string
     */
    protected $linkUrlPath = 'dotdigitalgroup_b2b/quote/index';
}
