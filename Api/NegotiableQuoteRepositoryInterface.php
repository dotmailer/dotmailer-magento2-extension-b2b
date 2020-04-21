<?php

namespace Dotdigitalgroup\B2b\Api;

use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface NegotiableQuoteRepositoryInterface
{
    /**
     * @param NegotiableQuoteInterface $quote
     */
    public function save(NegotiableQuoteInterface $quote);

    /**
     * @param $id
     * @return NegotiableQuoteInterface
     */
    public function getById($id);

    /**
     * @param $quoteId
     * @return NegotiableQuoteInterface
     */
    public function getByQuoteId($quoteId);

    /**
     * @param null $from
     * @param null $to
     * @return int
     */
    public function reset($from = null, $to = null);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param $quoteId
     */
    public function setUnimported($quoteId);

    /**
     * @param array $ids
     */
    public function setImportedByIds($ids);

    /**
     * @param $expirationDate
     * @param $id
     */
    public function setExpirationDateById($expirationDate, $id);
}
