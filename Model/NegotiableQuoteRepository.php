<?php

namespace Dotdigitalgroup\B2b\Model;

use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;
use Dotdigitalgroup\B2b\Api\NegotiableQuoteRepositoryInterface;
use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterfaceFactory;
use Dotdigitalgroup\B2b\Model\ResourceModel\NegotiableQuoteFactory as NegotiableQuoteResourceFactory;
use Dotdigitalgroup\B2b\Model\Query\GetList;
use Magento\Framework\Api\SearchCriteriaInterface;

class NegotiableQuoteRepository implements NegotiableQuoteRepositoryInterface
{
    /**
     * @var NegotiableQuoteResourceFactory
     */
    private $negotiableQuoteResourceFactory;

    /**
     * @var NegotiableQuoteInterfaceFactory
     */
    private $negotiableQuoteFactory;

    /**
     * @var GetList
     */
    private $negotiableQuoteList;

    /**
     * NegotiableQuoteRepository constructor.
     * @param NegotiableQuoteResourceFactory $negotiableQuoteResourceFactory
     * @param NegotiableQuoteInterfaceFactory $negotiableQuoteFactory
     * @param GetList $negotiableQuoteList
     */
    public function __construct(
        NegotiableQuoteResourceFactory $negotiableQuoteResourceFactory,
        NegotiableQuoteInterfaceFactory $negotiableQuoteFactory,
        GetList $negotiableQuoteList
    ) {
        $this->negotiableQuoteResourceFactory = $negotiableQuoteResourceFactory;
        $this->negotiableQuoteFactory = $negotiableQuoteFactory;
        $this->negotiableQuoteList = $negotiableQuoteList;
    }

    /**
     * Get list.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        return $this->negotiableQuoteList->getList($searchCriteria);
    }

    /**
     * Get by id.
     *
     * @param int $id
     * @return NegotiableQuoteInterface
     */
    public function getById($id)
    {
        $negotiableQuote = $this->negotiableQuoteFactory->create();
        $this->negotiableQuoteResourceFactory->create()
            ->load($negotiableQuote, $id, 'id');
        return $negotiableQuote;
    }

    /**
     * Get by quote id.
     *
     * @param int $quoteId
     * @return NegotiableQuoteInterface
     */
    public function getByQuoteId($quoteId)
    {
        $negotiableQuote = $this->negotiableQuoteFactory->create();
        $this->negotiableQuoteResourceFactory->create()
            ->load($negotiableQuote, $quoteId, 'quote_id');
        return $negotiableQuote;
    }

    /**
     * Set imported by ids.
     *
     * @param array $ids
     */
    public function setImportedByIds($ids)
    {
        return $this->negotiableQuoteResourceFactory
            ->create()
            ->setImportedByIds($ids);
    }

    /**
     * Set unimported.
     *
     * @param int $quoteId
     */
    public function setUnimported($quoteId)
    {
        $this->negotiableQuoteResourceFactory
            ->create()
            ->setUnimported($quoteId);
    }

    /**
     * Set expiration date by id.
     *
     * @param string $expirationDate
     * @param int $id
     */
    public function setExpirationDateById($expirationDate, $id)
    {
        $this->negotiableQuoteResourceFactory
            ->create()
            ->setExpirationDateById($expirationDate, $id);
    }

    /**
     * Reset.
     *
     * @param null|string $from
     * @param null|string $to
     * @return int
     */
    public function reset($from = null, $to = null)
    {
        return $this->negotiableQuoteResourceFactory
            ->create()
            ->resetNegotiableQuotes($from, $to);
    }

    /**
     * Save.
     *
     * @param NegotiableQuoteInterface $quote
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save(NegotiableQuoteInterface $quote)
    {
        $this->negotiableQuoteResourceFactory
            ->create()
            ->save($quote);
    }
}
