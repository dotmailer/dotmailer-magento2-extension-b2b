<?php

namespace Dotdigitalgroup\B2b\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Dotdigitalgroup\B2b\Setup\SchemaInterface;

class Uninstall implements UninstallInterface
{
    /**
     * Invoked when remove-data flag is set during module uninstall.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();
        $connection->dropTable(
            $setup->getTable(SchemaInterface::EMAIL_B2B_QUOTE_TABLE)
        );
    }
}
