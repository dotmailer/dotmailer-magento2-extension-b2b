<?php

namespace Dotdigitalgroup\B2b\Plugin;

class TierPriceFinderPlugin
{
    /**
     * @var string
     */
    private $catalogContext = 'B2b';

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Dotdigitalgroup\Email\Model\Sync\Catalog\SyncContextService
     */
    private $contextService;

    /**
     * PriceFinderPlugin constructor.
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Dotdigitalgroup\Email\Model\Sync\Catalog\SyncContextService $contextService
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Dotdigitalgroup\Email\Model\Sync\Catalog\SyncContextService $contextService
    ) {
        $this->productRepository = $productRepository;
        $this->contextService = $contextService;
    }

    public function aroundGetTierPrices(
        \Dotdigitalgroup\Email\Model\Connector\TierPriceFinder $priceFinder,
        callable $proceed,
        $product
    ) {
        if ($this->contextService->getModule() !== $this->catalogContext) {
            return $proceed($product);
        }

        $prices = [];

        switch ($product->getTypeId()) {
            case 'bundle':
                return $this->getTierPriceOfBundledProduct($product);
            case 'configurable':
                return $this->getMinTierPriceOfChildProducts($product->getTypeInstance()->getUsedProducts($product));
            case 'grouped':
                return $prices;
            default:
                //simple products
                return $this->getTierPricesOfSimpleProduct($product);
        }
    }

    /**
     * @param $product
     * @return array
     */
    private function getTierPricesOfSimpleProduct($product)
    {
        $prices = [];
        if (!empty($product->getTierPrices())) {
            $count = 0;
            foreach ($this->getTierPricesKeys($product) as $pointer) {
                $tierPrices = $product->getTierPrices()[$pointer];
                $finalPrice = $tierPrices->getData()['value'];
                $quantity = (int)$tierPrices->getData()['qty'];
                $percentage = $tierPrices->getExtensionAttributes()
                    ->getPercentageValue();
                $type = $this->getPercentageType($percentage);
                $this->setPrices($finalPrice, $quantity, $percentage, $type, $count, $prices);
            }
        }
        return $prices;
    }

    /**
     * @param $childProducts
     * @return array
     */
    private function getMinTierPriceOfChildProducts($childProducts)
    {
        foreach ($childProducts as $childProduct) {
            if (empty($childProduct->getTierPrices())) {
                continue;
            }
            //Assume all children(simple) will have the same tier price. So just grab the first
            return $this->getTierPriceOfSimpleProduct($childProduct);
        }
    }

    /**
     * @param $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getTierPriceOfBundledProduct($product)
    {
        $typeInstance = $product->getTypeInstance();
        $childGroups = $typeInstance->getChildrenIds($product->getId(), true);
        $prices = [];
        $count = 0;
        foreach ($this->getTierPricesKeys($product) as $pointer) {
            $tierPrices = $product->getTierPrices()[$pointer];
            $percentage = $tierPrices->getData()['extension_attributes']->getPercentageValue();
            $quantity = (int)$tierPrices->getData()['qty'];
            $type = $this->getPercentageType($percentage);
            $tempArr = [];
            $price = [];
            foreach ($childGroups as $groupPointer => $groups) {
                foreach ($groups as $simpleProductId) {
                    $childProduct = $this->productRepository->getById($simpleProductId);
                    $tempArr[] = $childProduct->getPrice() - ($percentage/100) * $childProduct->getPrice();
                }
                $price[$groupPointer] = min($tempArr);
                $tempArr = [];
            }
            if ($finalPrice = array_sum($price)) {
                $this->setPrices($finalPrice, $quantity, $percentage, $type, $count, $prices);
            }
        }

        return $prices;
    }

    /**
     * @param $product
     * @return array
     */
    private function getTierPricesKeys($product)
    {
        $pointers = [];
        foreach ($product->getTierPrices() as $key => $prices) {
            if ($prices->getData()['customer_group_id'] == $this->contextService->getCustomerGroupId()) {
                $pointers[] = $key;
            }
        }
        return $pointers;
    }

    /**
     * @param $finalPrice
     * @param $quantity
     * @param $percentage
     * @param $type
     * @param $count
     * @param $prices
     */
    private function setPrices($finalPrice, $quantity, $percentage, $type, &$count, &$prices)
    {
        $prices[$count]['price'] = $finalPrice;
        $prices[$count]['quantity'] = $quantity;
        $prices[$count]['percentage'] = $percentage;
        $prices[$count]['type'] = $type;
        $count++;
    }

    /**
     * @param $percentage
     * @return string
     */
    private function getPercentageType($percentage)
    {
        return $type = isset($percentage) ? 'Percentage Discount' : 'Fixed Price';
    }
}
