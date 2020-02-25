<?php

namespace Dotdigitalgroup\B2b\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Dotdigitalgroup\B2b\Setup\SchemaInterface as Schema;
use Dotdigitalgroup\Email\Logger\Logger;

class NegotiableQuote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Initialize resource.
     */
    public function _construct()
    {
        $this->_init(Schema::EMAIL_B2B_QUOTE_TABLE, 'id');
    }

    public function __construct(
        Context $context,
        Logger $logger,
        $connectionName = null
    ) {
        $this->logger = $logger;
        parent::__construct($context, $connectionName);
    }

    /**
     * @param array $ids
     */
    public function setImportedByIds($ids)
    {
        try {
            $coreResource = $this->getConnection();
            $tableName = $this->getTable(Schema::EMAIL_B2B_QUOTE_TABLE);

            $coreResource->update(
                $tableName,
                [
                    'quote_imported' => 1,
                    'updated_at' => gmdate('Y-m-d H:i:s'),
                ],
                ["quote_id IN (?)" => $ids]
            );
        } catch (\Exception $e) {
            $this->logger->debug((string) $e);
        }
    }

    /**
     * @param $quoteId
     */
    public function setUnimported($quoteId)
    {
        try {
            $conn = $this->getConnection();
            $where = $conn->quoteInto(
                'quote_id = ?',
                $quoteId
            );

            $conn->update(
                $this->getTable(Schema::EMAIL_B2B_QUOTE_TABLE),
                [
                    'quote_imported' => 0
                ],
                $where
            );
        } catch (\Exception $e) {
            $this->logger->debug((string) $e);
        }
    }

    /**
     * Reset the email b2b quotes for re-import.
     *
     * @param string $from
     * @param string $to
     *
     * @return int
     */
    public function resetNegotiableQuotes($from = null, $to = null)
    {
        $conn = $this->getConnection();
        if ($from && $to) {
            $where = [
                'created_at >= ?' => $from . ' 00:00:00',
                'created_at <= ?' => $to . ' 23:59:59',
                'quote_imported' => 1
            ];
        } else {
            $where = [
                'quote_imported' => 1
            ];
        }
        $num = $conn->update(
            $this->getTable(Schema::EMAIL_B2B_QUOTE_TABLE),
            ['quote_imported' => 0],
            $where
        );

        return $num;
    }
}
