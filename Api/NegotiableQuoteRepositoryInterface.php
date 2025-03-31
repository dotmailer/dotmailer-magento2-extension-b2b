<?php

namespace Dotdigitalgroup\B2b\Api;

use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface NegotiableQuoteRepositoryInterface
{
    /**
     * Save.
     *
     * @param NegotiableQuoteInterface $quote
     */
    public function save(NegotiableQuoteInterface $quote);

    /**
     * Get by id.
     *
     * @param int $id
     * @return NegotiableQuoteInterface
     */
    public function getById($id);

    /**
     * Get by quote id.
     *
     * @param int $quoteId
     * @return NegotiableQuoteInterface
     */
    public function getByQuoteId($quoteId);

    /**
     * Reset.
     *
     * @param null|string $from
     * @param null|string $to
     * @return int
     */
    public function reset($from = null, $to = null);

    /**
     * Get list.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Set unimported.
     *
     * @param int $quoteId
     */
    public function setUnimported($quoteId);

    /**
     * Set imported by ids.
     *
     * @param array $ids
     */
    public function setImportedByIds($ids);

    /**
     * Set expiration date by id.
     *
     * @param string $expirationDate
     * @param int $id
     */
    public function setExpirationDateById($expirationDate, $id);
}
