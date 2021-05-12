<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;
use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterfaceFactory;
use Dotdigitalgroup\B2b\Api\NegotiableQuoteRepositoryInterface;
use Dotdigitalgroup\Email\Logger\Logger;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\NegotiableQuote\Model\NegotiableQuoteRepository;

class NegotiableQuotePlugin
{
    /**
     * @var NegotiableQuoteRepositoryInterface
     */
    private $negotiableQuoteRepository;

    /**
     * @var NegotiableQuoteInterfaceFactory
     */
    private $negotiableQuoteInterface;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        NegotiableQuoteInterfaceFactory $negotiableQuoteInterface,
        NegotiableQuoteRepositoryInterface $negotiableQuoteRepository,
        CustomerRepositoryInterface $customerRepositoryInterface,
        DateTime $dateTime,
        Logger $logger
    ) {
        $this->negotiableQuoteRepository = $negotiableQuoteRepository;
        $this->negotiableQuoteInterface = $negotiableQuoteInterface;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * @param NegotiableQuoteRepository $subject
     * @param bool $result
     * @param NegotiableQuoteInterface $quoteModel
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterSave(NegotiableQuoteRepository $subject, $result, $quoteModel)
    {
        //Negotiable Quote edited either in frontend or backend
        if ($this->toSetUnimported($quoteModel)) {
            $this->negotiableQuoteRepository->setUnimported($quoteModel->getQuoteId());
            $this->negotiableQuoteRepository
                ->setExpirationDateById(
                    $this->dateTime->formatDate($quoteModel->getExpirationPeriod() . ' 23:59:59', true),
                    $quoteModel->getQuoteId()
                );
        }

        //If data already exists in quoteModel we do not need to create new record in email_b2b_quote table
        if ($this->negotiableQuoteRepository->getByQuoteId($quoteModel->getQuoteId())->getData()) {
            return $result;
        }

        try {
            $customerObject = $this->customerRepositoryInterface->getById($quoteModel->getCreatorId());

            $ddgQuote = $this->negotiableQuoteInterface->create()
                ->setQuoteId($quoteModel->getQuoteId())
                ->setQuoteImported(0)
                ->setWebsiteId($customerObject->getWebsiteId())
                ->setExpirationDate($quoteModel->getExpirationPeriod());

            $this->negotiableQuoteRepository->save($ddgQuote);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->debug(
                'Could not save B2B quote for customer with id: ' . $quoteModel->getCreatorId(),
                [(string) $e]
            );
        }

        return $result;
    }

    /**
     * @param $quoteModel
     * @return bool
     */
    private function toSetUnimported($quoteModel)
    {
        $createdStatus = (
            $quoteModel->getStatus() === 'created' ||
            $quoteModel->getStatus() === 'submitted_by_admin' ||
            $quoteModel->getStatus() === 'processing_by_admin'
        );

        $hasData = $this->negotiableQuoteRepository->getByQuoteId($quoteModel->getQuoteId())->getData();
        return $createdStatus && $hasData;
    }
}
