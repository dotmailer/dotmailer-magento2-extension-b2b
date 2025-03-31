<?php

namespace Dotdigitalgroup\B2b\Model\NegotiableQuote;

use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NegotiableQuote\Api\NegotiableQuoteRepositoryInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Dotdigitalgroup\Email\Model\Catalog\UrlFinder;
use Dotdigitalgroup\Email\Model\Product\ImageFinder;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use Magento\NegotiableQuote\Api\NegotiableQuoteItemManagementInterface;
use Dotdigitalgroup\Email\Logger\Logger;

class Data
{
    /**
     * @var NegotiableQuoteRepositoryInterface
     */
    private $negotiableQuoteRepository;

    /**
     * @var CompanyManagementInterface
     */
    private $companyManagement;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var \Dotdigitalgroup\Email\Model\Catalog\UrlFinder
     */
    private $urlFinder;

    /**
     * @var ImageFinder
     */
    private $imageFinder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var NegotiableQuoteItemManagementInterface
     */
    private $negotiableQuoteItemManager;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * @var float
     */
    private $quoteTotal;

    /**
     * @var float
     */
    private $quoteNegotiated;

    /**
     * @var string
     */
    private $salesRep;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $status;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @var string
     */
    private $expirationDate;

    /**
     * Data constructor.
     *
     * @param NegotiableQuoteRepositoryInterface $negotiableQuoteRepository
     * @param CompanyManagementInterface $companyManagement
     * @param CartRepositoryInterface $cartRepository
     * @param ProductRepositoryInterface $productRepository
     * @param DateTime $dateTime
     * @param UrlFinder $urlFinder
     * @param ImageFinder $imageFinder
     * @param StoreManagerInterface $storeManager
     * @param NegotiableQuoteItemManagementInterface $negotiableQuoteItemManager
     * @param Logger $logger
     */
    public function __construct(
        NegotiableQuoteRepositoryInterface $negotiableQuoteRepository,
        CompanyManagementInterface $companyManagement,
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository,
        DateTime $dateTime,
        UrlFinder $urlFinder,
        ImageFinder $imageFinder,
        StoreManagerInterface $storeManager,
        NegotiableQuoteItemManagementInterface $negotiableQuoteItemManager,
        Logger $logger
    ) {
        $this->negotiableQuoteRepository = $negotiableQuoteRepository;
        $this->companyManagement = $companyManagement;
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->dateTime = $dateTime;
        $this->urlFinder = $urlFinder;
        $this->imageFinder = $imageFinder;
        $this->storeManager = $storeManager;
        $this->negotiableQuoteItemManager = $negotiableQuoteItemManager;
        $this->logger = $logger;
    }

    /**
     * Augment quote data.
     *
     * @param NegotiableQuoteInterface $ddgQuote
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function augment(NegotiableQuoteInterface $ddgQuote)
    {
        $this->setInsightData($ddgQuote);
        return $this->buildQuoteData();
    }

    /**
     * Set insight data.
     *
     * @param NegotiableQuoteInterface $ddgQuote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setInsightData($ddgQuote)
    {
        /** @var \Dotdigitalgroup\B2b\Model\NegotiableQuote $ddgQuote */
        $this->setId($ddgQuote->getQuoteId());
        $this->setCreatedAt($ddgQuote->getCreatedAt());
        $this->setUpdatedAt($ddgQuote->getUpdatedAt());
        $this->setExpirationDate($ddgQuote->getExpirationDate());

        $negotiableQuote = $this->negotiableQuoteRepository->getById($ddgQuote->getQuoteId());
        $this->setName($negotiableQuote->getQuoteName());
        $this->setStatus($negotiableQuote->getStatus());
        $this->setQuoteTotal($negotiableQuote->getBaseOriginalTotalPrice());
        $this->setQuoteNegotiatedTotal($negotiableQuote->getBaseNegotiatedTotalPrice());

        try {
            $cartQuote = $this->cartRepository->get($negotiableQuote->getQuoteId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $cartQuote = null;
            $this->logger->debug((string)$e);
        }

        if ($cartQuote) {
            $this->setEmail($cartQuote->getCustomer()->getEmail());

            $mageCompany = $this->companyManagement->getByCustomerId($cartQuote->getCustomer()->getId());
            $this->setCompany($mageCompany->getCompanyName());
            $this->setSalesRep(
                $this->companyManagement->getSalesRepresentative(
                    $mageCompany->getSalesRepresentativeId()
                )
            );

            $this->setCurrency($cartQuote->getCurrency()->getBaseCurrencyCode());

            $this->setQuoteItemsData($cartQuote);
        }
    }

    /**
     * Build quote data.
     *
     * @return array
     */
    private function buildQuoteData()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'company' => $this->getCompany(),
            'created_date' => $this->dateTime->date(\DateTime::ATOM, $this->getCreatedAt()),
            'modified_date' => $this->dateTime->date(\DateTime::ATOM, $this->getUpdatedAt()),
            'expiration_date' => $this->dateTime->date(\DateTime::ATOM, $this->getExpirationDate()),
            'quote_total' => $this->getQuoteTotal(),
            'quote_negotiated' => $this->getNegotiatedTotal(),
            'sales_rep' => $this->getSalesRep(),
            'currency' => $this->getCurrency(),
            'status' => $this->getStatus(),
            'items' => $this->getQuoteItemsData(),
        ];
    }

    /**
     * Set quote items data.
     *
     * @param CartInterface $cartQuote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setQuoteItemsData($cartQuote)
    {
        $this->items = [];

        /** @var Quote $cartQuote */
        $visibleItems = $cartQuote->getAllVisibleItems();

        foreach ($visibleItems as $item) {

            $product = $this->productRepository->getById($item->getProduct()->getId(), false, $cartQuote->getStoreId());
            $store = $this->storeManager->getStore($cartQuote->getStoreId());
            $mediaPath = $this->imageFinder->getProductImageUrl($item, $store);

            $this->items[] = [
                'name' => $item->getName(),
                'unitPrice' => $this->negotiableQuoteItemManager->getOriginalPriceByItem($item),
                'salePrice' => round($item->getBasePriceInclTax(), 2),
                'sku' => $item->getSku(),
                'quantity' => $item->getQty(),
                'imageUrl' => $this->urlFinder->getPath($mediaPath),
                'productUrl' => $this->urlFinder->fetchFor($product),
            ];
        }
    }

    /**
     * Get quote items data.
     *
     * @return array
     */
    private function getQuoteItemsData()
    {
        return $this->items;
    }

    /**
     * Set id.
     *
     * @param int $id
     */
    private function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id.
     */
    private function getId()
    {
        return (int) $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    private function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name.
     */
    private function getName()
    {
        return (string) $this->name;
    }

    /**
     * Set email.
     *
     * @param string $email
     */
    private function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Get email.
     */
    private function getEmail()
    {
        return (string) $this->email;
    }

    /**
     * Set company.
     *
     * @param string $company
     */
    private function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Get company.
     */
    private function getCompany()
    {
        return (string) $this->company;
    }

    /**
     * Set created at.
     *
     * @param string $date
     */
    private function setCreatedAt($date)
    {
        $this->createdAt = $date;
    }

    /**
     * Get created at.
     */
    private function getCreatedAt()
    {
        return (string) $this->createdAt;
    }

    /**
     * Set updated at.
     *
     * @param string $date
     */
    private function setUpdatedAt($date)
    {
        $this->updatedAt = $date;
    }

    /**
     * Get updated at.
     */
    private function getUpdatedAt()
    {
        return (string) $this->updatedAt;
    }

    /**
     * Set quote total.
     *
     * @param float $price
     */
    private function setQuoteTotal($price)
    {
        $this->quoteTotal = round($price, 2);
    }

    /**
     * Get quote total.
     */
    private function getQuoteTotal()
    {
        return (float) $this->quoteTotal;
    }

    /**
     * Set quote negotiated total.
     *
     * @param float $price
     */
    private function setQuoteNegotiatedTotal($price)
    {
        $this->quoteNegotiated = round($price, 2);
    }

    /**
     * Get quote negotiated total.
     */
    private function getNegotiatedTotal()
    {
        return (float) $this->quoteNegotiated;
    }

    /**
     * Set sales rep.
     *
     * @param string $salesRep
     */
    private function setSalesRep($salesRep)
    {
        $this->salesRep = $salesRep;
    }

    /**
     * Get sales rep.
     */
    private function getSalesRep()
    {
        return (string) $this->salesRep;
    }

    /**
     * Set status.
     *
     * @param string $status
     */
    private function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status.
     */
    private function getStatus()
    {
        return (string) $this->status;
    }

    /**
     * Set currency.
     *
     * @param string $currency
     */
    private function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency.
     */
    private function getCurrency()
    {
        return (string) $this->currency;
    }

    /**
     * Set expiration date.
     *
     * @param string $expirationDate
     */
    private function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * Get expiration date.
     */
    private function getExpirationDate()
    {
        return (string) $this->expirationDate;
    }
}
