<?php

namespace Dotdigitalgroup\B2b\Block\Adminhtml\Config\Developer;

use Dotdigitalgroup\Email\Block\Adminhtml\Config\AbstractButton;

class Resetquotes extends AbstractButton
{
    /**
     * Get disabled.
     *
     * @return bool
     */
    protected function getDisabled()
    {
        return false;
    }

    /**
     * Get button label.
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function getButtonLabel()
    {
        return  __('Run Now');
    }

    /**
     * Get button url.
     *
     * @return string
     */
    protected function getButtonUrl()
    {
        $query = [
            '_query' => [
                'from' => '',
                'to' => '',
                'tp' => ''
            ]
        ];
        return  $this->_urlBuilder->getUrl('dotdigitalgroup_b2b/run/negotiablequotereset', $query);
    }
}
