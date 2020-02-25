<?php

namespace Dotdigitalgroup\B2b\Model\NegotiableQuote;

use Dotdigitalgroup\B2b\Api\Data\NegotiableQuoteInterface;
use Magento\NegotiableQuote\Api\NegotiableQuoteRepositoryInterface;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Dotdigitalgroup\Email\Model\Catalog\UrlFinder;
use Dotdigitalgroup\Email\Model\Product\ImageFinder;
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
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $name;

    /**
     * @var
     */
    private $email;

    /**
     * @var
     */
    private $company;

    /**
     * @var
     */
    private $createdAt;

    /**
     * @var
     */
    private $updatedAt;

    /**
     * @var
     */
    private $quoteTotal;

    /**
     * @var
     */
    private $quoteNegotiated;

    /**
     * @var
     */
    private $salesRep;

    /**
     * @var
     */
    private $currency;

    /**
     * @var
     */
    private $status;

    /**
     * @var array
     */
    private $items = [];

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
     * @param NegotiableQuoteInterface $ddgQuote
     * @return array
     */
    public function augment(NegotiableQuoteInterface $ddgQuote)
    {
        $this->setInsightData($ddgQuote);
        return $this->buildQuoteData();
    }

    private function setInsightData($ddgQuote)
    {
        $this->setId($ddgQuote->getQuoteId());
        $this->setCreatedAt($ddgQuote->getCreatedAt());
        $this->setUpdatedAt($ddgQuote->getUpdatedAt());

        $negotiableQuote = $this->negotiableQuoteRepository->getById($ddgQuote->getQuoteId());
        $this->setName($negotiableQuote->getQuoteName());
        $this->setStatus($negotiableQuote->getStatus());
        $this->setQuoteTotal($negotiableQuote->getBaseOriginalTotalPrice());
        $this->setQuoteNegotiatedTotal($negotiableQuote->getBaseNegotiatedTotalPrice());

        try {
            $cartQuote = $this->cartRepository->get($negotiableQuote->getId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $cartQuote = null;
            $this->logger->debug((string) $e);
        }

        if ($cartQuote) {
            $this->setEmail($cartQuote->getCustomerEmail());

            $mageCompany = $this->companyManagement->getByCustomerId($cartQuote->getCustomerId());
            $this->setCompany($mageCompany->getCompanyName());
            $this->setSalesRep(
                $this->companyManagement->getSalesRepresentative(
                    $mageCompany->getSalesRepresentativeId()
                )
            );

            $this->setCurrency($cartQuote->getBaseCurrencyCode());

            $this->setQuoteItemsData($cartQuote);
        }
    }

    private function buildQuoteData()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'company' => $this->getCompany(),
            'created_date' => $this->dateTime->date(\Zend_Date::ISO_8601, $this->getCreatedAt()),
            'modified_date' => $this->dateTime->date(\Zend_Date::ISO_8601, $this->getUpdatedAt()),
            'quote_total' => $this->getQuoteTotal(),
            'quote_negotiated' => $this->getNegotiatedTotal(),
            'sales_rep' => $this->getSalesRep(),
            'currency' => $this->getCurrency(),
            'status' => $this->getStatus(),
            'items' => $this->getQuoteItemsData()
        ];
    }

    /**
     * @param \Magento\Quote\Model\Quote $cartQuote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setQuoteItemsData($cartQuote)
    {
        $this->items = [];

        foreach ($cartQuote->getAllVisibleItems() as $item) {

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

    private function getQuoteItemsData()
    {
        return $this->items;
    }

    private function setId($id)
    {
        $this->id = $id;
    }

    private function getId()
    {
        return (int) $this->id;
    }

    private function setName($name)
    {
        $this->name = $name;
    }

    private function getName()
    {
        return (string) $this->name;
    }

    private function setEmail(string $email)
    {
        $this->email = $email;
    }

    private function getEmail()
    {
        return (string) $this->email;
    }

    private function setCompany($company)
    {
        $this->company = $company;
    }

    private function getCompany()
    {
        return (string) $this->company;
    }

    private function setCreatedAt($date)
    {
        $this->createdAt = $date;
    }

    private function getCreatedAt()
    {
        return (string) $this->createdAt;
    }

    private function setUpdatedAt($date)
    {
        $this->updatedAt = $date;
    }

    private function getUpdatedAt()
    {
        return (string) $this->updatedAt;
    }

    private function setQuoteTotal($price)
    {
        $this->quoteTotal = round($price, 2);
    }

    private function getQuoteTotal()
    {
        return (float) $this->quoteTotal;
    }

    private function setQuoteNegotiatedTotal($price)
    {
        $this->quoteNegotiated = round($price, 2);
    }

    private function getNegotiatedTotal()
    {
        return (float) $this->quoteNegotiated;
    }

    private function setSalesRep($salesRep)
    {
        $this->salesRep = $salesRep;
    }

    private function getSalesRep()
    {
        return (string) $this->salesRep;
    }

    private function setStatus($status)
    {
        $this->status = $status;
    }

    private function getStatus()
    {
        return (string) $this->status;
    }

    private function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    private function getCurrency()
    {
        return (string) $this->currency;
    }
}
