<?php

namespace Dotdigitalgroup\B2b\Setup\Install\Type;

use Dotdigitalgroup\B2b\Setup\SchemaInterface;
use Dotdigitalgroup\Email\Setup\Install\Type\AbstractBatchInserter;
use Dotdigitalgroup\Email\Setup\Install\Type\InsertTypeInterface;

class InsertB2bQuoteTable extends AbstractBatchInserter implements InsertTypeInterface
{
    /**
     * @var string
     */
    protected $tableName = SchemaInterface::EMAIL_B2B_QUOTE_TABLE;

    /**
     * Don't offset the query for this migration
     * @var bool
     */
    protected $useOffset = false;

    /**
     * @inheritdoc
     */
    protected function getSelectStatement()
    {
        return $this->resourceConnection
            ->getConnection()
            ->select()
            ->from([
                'quote' => $this->resourceConnection->getTableName('negotiable_quote'),
            ], [
                'quote_id' => 'quote.quote_id',
            ])
            ->joinInner(
                ['customer' => $this->resourceConnection->getTableName('customer_entity')],
                'quote.creator_id = customer.entity_id',
                ['website_id' => 'customer.website_id']
            )
            ->joinInner(
                ['quote_grid' => $this->resourceConnection->getTableName('negotiable_quote_grid')],
                'quote.quote_id = quote_grid.entity_id',
                ['created_at' => 'quote_grid.created_at']
            )
            ->where(
                'quote.quote_id NOT IN (?)',
                $this->resourceConnection
                    ->getConnection()
                    ->select()
                    ->from(
                        $this->resourceConnection->getTableName(SchemaInterface::EMAIL_B2B_QUOTE_TABLE),
                        ['quote_id']
                    )
            )
            ->order('quote.quote_id')
            ;
    }

    /**
     * @inheritdoc
     */
    public function getInsertArray()
    {
        return [
            'quote_id',
            'website_id',
            'created_at',
        ];
    }
}
