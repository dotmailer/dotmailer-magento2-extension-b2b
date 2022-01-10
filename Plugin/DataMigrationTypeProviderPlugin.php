<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Setup\Install\Type\InsertB2bQuoteTable;
use Dotdigitalgroup\B2b\Setup\SchemaInterface;
use Dotdigitalgroup\Email\Setup\Install\DataMigrationTypeProvider;

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
     * @param string|null $table
     * @return array
     */
    public function afterGetTypes(DataMigrationTypeProvider $dataMigrationTypeProvider, $result, $table = null)
    {
        // no $table supplied - add this class's types to the list
        if (!$table) {
            return $result += get_object_vars($this);
        }

        // request for this type's table
        if ($table === SchemaInterface::EMAIL_B2B_QUOTE_TABLE) {
            return get_object_vars($this);
        }

        return $result;
    }
}
