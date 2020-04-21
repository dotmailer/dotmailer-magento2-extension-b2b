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
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        return $this->negotiableQuoteList->getList($searchCriteria);
    }

    /**
     * @param $id
     * @return NegotiableQuoteInterface
     */
    public function getById($id)
    {
        return $this->negotiableQuoteFactory->create()->load($id, 'id');
    }

    /**
     * @param $quoteId
     * @return NegotiableQuoteInterface
     */
    public function getByQuoteId($quoteId)
    {
        return $this->negotiableQuoteFactory->create()->load($quoteId, 'quote_id');
    }

    /**
     * @param array $ids
     */
    public function setImportedByIds($ids)
    {
        return $this->negotiableQuoteResourceFactory
            ->create()
            ->setImportedByIds($ids);
    }

    /**
     * @param $quoteId
     */
    public function setUnimported($quoteId)
    {
        $this->negotiableQuoteResourceFactory
            ->create()
            ->setUnimported($quoteId);
    }

    /**
     * @param $expirationDate
     * @param $id
     */
    public function setExpirationDateById($expirationDate, $id)
    {
        $this->negotiableQuoteResourceFactory
            ->create()
            ->setExpirationDateById($expirationDate, $id);
    }

    /**
     * @param null $from
     * @param null $to
     * @return int
     */
    public function reset($from = null, $to = null)
    {
        return $this->negotiableQuoteResourceFactory
            ->create()
            ->resetNegotiableQuotes($from, $to);
    }

    /**
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
