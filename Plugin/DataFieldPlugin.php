<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Helper\Config;
use Dotdigitalgroup\Email\Model\Connector\Datafield;

class DataFieldPlugin
{
    const DATA_MAPPING_PATH_PREFIX = 'b2b_extra_data';

    /**
     * @param Datafield $dataField
     * @param array $result
     * @return null
     */
    public function beforeGetContactDatafields(Datafield $dataField)
    {
        $dataField->setContactDatafields(Config::CONTACT_B2B_DATA_FIELDS, self::DATA_MAPPING_PATH_PREFIX);
        return null;
    }
}