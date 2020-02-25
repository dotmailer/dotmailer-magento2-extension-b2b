<?php

namespace Dotdigitalgroup\B2b\Model;

use Dotdigitalgroup\Email\Model\ResourceModel\Cron\CollectionFactory as CronCollection;
use Dotdigitalgroup\Email\Logger\Logger;
use Dotdigitalgroup\B2b\Model\Sync\NegotiableQuote;

class Cron
{
    /**
     * @var CronCollection
     */
    private $cronCollection;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var NegotiableQuote
     */
    private $negotiableQuote;

    /**
     * Cron constructor.
     * @param CronCollection $cronCollection
     * @param Logger $logger
     * @param NegotiableQuote $negotiableQuote
     */
    public function __construct(
        CronCollection $cronCollection,
        Logger $logger,
        NegotiableQuote $negotiableQuote
    ) {
        $this->cronCollection = $cronCollection;
        $this->logger = $logger;
        $this->negotiableQuote = $negotiableQuote;
    }

    /**
     * @return void
     */
    public function syncNegotiableQuotes()
    {
        if ($this->jobHasAlreadyBeenRun('ddg_automation_sync_negotiable_quotes')) {
            $message = 'Skipping ddg_b2b_sync_negotiable_quotes job run';
            $this->logger->info($message);
        }

        $this->negotiableQuote->sync();
    }

    /**
     * @param string $jobCode
     * @return bool
     */
    private function jobHasAlreadyBeenRun($jobCode)
    {
        $currentRunningJob = $this->cronCollection->create()
            ->addFieldToFilter('job_code', $jobCode)
            ->addFieldToFilter('status', 'running')
            ->setPageSize(1);

        if ($currentRunningJob->getSize()) {
            $jobOfSameTypeAndScheduledAtDateAlreadyExecuted =  $this->cronCollection->create()
                ->addFieldToFilter('job_code', $jobCode)
                ->addFieldToFilter('scheduled_at', $currentRunningJob->getFirstItem()->getScheduledAt())
                ->addFieldToFilter('status', ['in' => ['success', 'failed']]);

            return ($jobOfSameTypeAndScheduledAtDateAlreadyExecuted->getSize()) ? true : false;
        }

        return false;
    }
}
