<?php

namespace Dotdigitalgroup\B2b\Model\Sync;

use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;
use Dotdigitalgroup\Email\Model\Sync\SyncInterface;
use Dotdigitalgroup\Email\Helper\Data as EmailHelper;
use Dotdigitalgroup\B2b\Model\NegotiableQuote\Config;
use Dotdigitalgroup\B2b\Model\NegotiableQuote\Data;
use Dotdigitalgroup\B2b\Api\NegotiableQuoteRepositoryInterface;
use Dotdigitalgroup\Email\Model\ImporterFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Dotdigitalgroup\Email\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class NegotiableQuote implements SyncInterface
{
    private const QUOTE_WEBSITE_BATCH_SIZE = 100;
    public const IMPORT_TYPE_B2B_QUOTES = 'B2B_Quotes';

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Data
     */
    private $quoteData;

    /**
     * @var NegotiableQuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ImporterFactory
     */
    private $importerFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var int
     */
    private $countNegotiableQuotes = 0;

    /**
     * @var mixed
     */
    private $start;

    /**
     * NegotiableQuote constructor.
     * @param EmailHelper $emailHelper
     * @param Config $config
     * @param Data $quoteData
     * @param NegotiableQuoteRepositoryInterface $quoteRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ImporterFactory $importerFactory
     * @param Logger $logger
     */
    public function __construct(
        EmailHelper $emailHelper,
        Config $config,
        Data $quoteData,
        NegotiableQuoteRepositoryInterface $quoteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ImporterFactory $importerFactory,
        Logger $logger
    ) {
        $this->emailHelper = $emailHelper;
        $this->config = $config;
        $this->quoteData = $quoteData;
        $this->quoteRepository = $quoteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->importerFactory = $importerFactory;
        $this->logger = $logger;
    }

    /**
     * Sync B2B Quotes.
     *
     * @param \DateTime|null $from
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @see SyncInterface
     */
    public function sync(?\DateTime $from = null)
    {
        $this->start = microtime(true);

        $websiteIdsForSync = $this->fetchWebsitesForSync();
        if (empty($websiteIdsForSync)) {
            return;
        }

        foreach ($websiteIdsForSync as $website) {
            $quotesForSync = $this->quoteRepository->getList(
                $this->searchCriteriaBuilder->addFilter('quote_imported', 0)
                    ->addFilter('website_id', $website->getId())
                    ->setPageSize(self::QUOTE_WEBSITE_BATCH_SIZE)
                    ->create()
            );

            $dataToSync = [];

            /** @var NegotiableQuoteInterface $item */
            foreach ($quotesForSync->getItems() as $item) {
                $dataToSync[] = $this->quoteData->augment(
                    $this->quoteRepository->getByQuoteId($item->getQuoteId())
                );
            }

            if (!empty($dataToSync)) {
                $this->importerFactory->create()
                    ->registerQueue(
                        self::IMPORT_TYPE_B2B_QUOTES,
                        $dataToSync,
                        \Dotdigitalgroup\Email\Model\Importer::MODE_BULK,
                        $website->getId()
                    );

                $this->quoteRepository->setImportedByIds($this->getIdsToSetAsImported($dataToSync));
                $this->countNegotiableQuotes += count($dataToSync);
            }
        }

        if ($this->countNegotiableQuotes) {
            $message = '----------- B2B Quotes sync ----------- : ' .
                gmdate('H:i:s', (int) microtime(true) - $this->start) .
                ', Total synced = ' . $this->countNegotiableQuotes;
            $this->logger->info($message);
        }
    }

    /**
     * Get ids to set as imported.
     *
     * @param array $dataToSync
     * @return array
     */
    private function getIdsToSetAsImported($dataToSync)
    {
        return array_map(function ($quote) {
            return $quote['id'];
        }, $dataToSync);
    }

    /**
     * Fetch websites for sync.
     *
     * @return array
     */
    private function fetchWebsitesForSync()
    {
        return array_filter($this->emailHelper->getWebsites(), function ($website) {
            return $this->emailHelper->isEnabled($website) && $this->config->isB2bQuoteSyncEnabled($website);
        });
    }
}
