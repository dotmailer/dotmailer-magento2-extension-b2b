<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Api\NegotiableQuoteRepositoryInterface;
use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterfaceFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

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

    public function __construct(
        NegotiableQuoteInterfaceFactory $negotiableQuoteInterface,
        NegotiableQuoteRepositoryInterface $negotiableQuoteRepository,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->negotiableQuoteRepository = $negotiableQuoteRepository;
        $this->negotiableQuoteInterface = $negotiableQuoteInterface;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function afterSave($subject, $result, $quoteModel)
    {
        //Negotiable Quote edited either in frontend or backend
        if ($this->toSetUnimported($quoteModel)) {
            $this->negotiableQuoteRepository->setUnimported($quoteModel->getQuoteId());
        }

        //If data already exists in quoteModel we do not need to create new record in email_b2b_quote table
        if ($this->negotiableQuoteRepository->getByQuoteId($quoteModel->getQuoteId())->getData()) {
            return;
        }

        $customerObject = $this->customerRepositoryInterface->getById($quoteModel->getCreatorId());

        $ddgQuote = $this->negotiableQuoteInterface->create()
            ->setQuoteId($quoteModel->getQuoteId())
            ->setQuoteImported(0)
            ->setWebsiteId($customerObject->getWebsiteId());

        $this->negotiableQuoteRepository->save($ddgQuote);
    }

    /**
     * @param $quoteModel
     * @return bool
     */
    private function toSetUnimported($quoteModel)
    {
        $createdStatus = $quoteModel->getStatus() === 'created' || $quoteModel->getStatus() === 'submitted_by_admin';
        $hasData = $this->negotiableQuoteRepository->getByQuoteId($quoteModel->getQuoteId())->getData();
        return $createdStatus && $hasData;
    }
}
