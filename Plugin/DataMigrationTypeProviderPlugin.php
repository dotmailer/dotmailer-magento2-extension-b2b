<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\Email\Setup\Install\DataMigrationTypeProvider;
use Dotdigitalgroup\B2b\Setup\Install\Type\InsertB2bQuoteTable;

class DataMigrationTypeProviderPlugin
{
    /**
     * @var InsertB2bQuoteTable
     */
    private $insertB2bQuoteTable;

    /**
     * DataMigrationTypeProviderPlugin constructor.
     * @param InsertB2bQuoteTable $insertB2bQuoteTable
     */
    public function __construct(
        InsertB2bQuoteTable $insertB2bQuoteTable
    ) {
        $this->insertB2bQuoteTable = $insertB2bQuoteTable;
    }

    /**
     * @param DataMigrationTypeProvider $dataMigrationTypeProvider
     * @param $result
     * @return array
     */
    public function afterGetTypes(DataMigrationTypeProvider $dataMigrationTypeProvider, $result)
    {
        return $result += get_object_vars($this);
    }
}
