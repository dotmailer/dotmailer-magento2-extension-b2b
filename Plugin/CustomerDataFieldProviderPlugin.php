<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Helper\Data;
use Dotdigitalgroup\Email\Model\Customer\CustomerDataFieldProvider;

class CustomerDataFieldProviderPlugin
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * After get additional data fields.
     *
     * @param CustomerDataFieldProvider $customerDataFieldProvider
     * @param array $result
     * @return array
     */
    public function afterGetAdditionalDataFields(
        CustomerDataFieldProvider $customerDataFieldProvider,
        array $result
    ) {
        return $result += $this->helper->getB2bAttributes($customerDataFieldProvider->getWebsite());
    }
}
