<?php

namespace Dotdigitalgroup\B2b\Model\ResourceModel\NegotiableQuote;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * NegotiableQuote initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Dotdigitalgroup\B2b\Model\NegotiableQuote::class,
            \Dotdigitalgroup\B2b\Model\ResourceModel\NegotiableQuote::class
        );
    }
}
