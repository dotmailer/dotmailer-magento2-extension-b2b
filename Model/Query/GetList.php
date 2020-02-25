<?php

namespace Dotdigitalgroup\B2b\Model\Query;

use Dotdigitalgroup\B2b\Model\ResourceModel\NegotiableQuote\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class GetList
{
    /**
     * @var SearchResultsFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        SearchResultsFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magento\Framework\Api\SearchResults $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);

        /** @var \Dotdigitalgroup\B2b\Model\ResourceModel\NegotiableQuote\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);
        $items = $collection->getItems();

        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }
}
