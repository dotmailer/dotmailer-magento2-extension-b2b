<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

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

    /**
     * Around get tier prices.
     *
     * @param \Dotdigitalgroup\Email\Model\Product\TierPriceFinder $priceFinder
     * @param callable $proceed
     * @param Product $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetTierPrices(
        \Dotdigitalgroup\Email\Model\Product\TierPriceFinder $priceFinder,
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
                /** @var Product $product */
                $configurableProductInstance = $product->getTypeInstance();
                /** @var Configurable $configurableProductInstance */
                return $this->getMinTierPriceOfChildProducts($configurableProductInstance->getUsedProducts($product));
            case 'grouped':
                return $prices;
            default:
                //simple products
                return $this->getTierPricesOfSimpleProduct($product);
        }
    }

    /**
     * Get tier prices of simple product.
     *
     * @param Product $product
     * @return array
     */
    private function getTierPricesOfSimpleProduct($product)
    {
        $prices = [];
        if (!empty($product->getTierPrices())) {
            $count = 0;
            foreach ($this->getTierPricesKeys($product) as $pointer) {
                $tierPrices = $product->getTierPrices()[$pointer];
                $finalPrice = $tierPrices->getValue();
                $quantity = (int)$tierPrices->getQty();
                $percentage = $tierPrices->getExtensionAttributes()
                    ->getPercentageValue();
                $type = $this->getPercentageType($percentage);
                $this->setPrices($finalPrice, $quantity, $percentage, $type, $count, $prices);
            }
        }
        return $prices;
    }

    /**
     * Get minimum tier price of child products.
     *
     * @param array $childProducts
     * @return array
     */
    private function getMinTierPriceOfChildProducts($childProducts)
    {
        foreach ($childProducts as $childProduct) {
            if (empty($childProduct->getTierPrices())) {
                continue;
            }
            //Assume all children(simple) will have the same tier price. So just grab the first
            return $this->getTierPricesOfSimpleProduct($childProduct);
        }
        return [];
    }

    /**
     * Get tier price of bundled product.
     *
     * @param Product $product
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
            $percentage = $tierPrices->getExtensionAttributes()->getPercentageValue();
            $quantity = (int)$tierPrices->getQty();
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
     * Get keys of valid tier prices.
     *
     * @param Product $product
     * @return array
     */
    private function getTierPricesKeys($product)
    {
        $pointers = [];
        foreach ($product->getTierPrices() as $key => $prices) {
            if ($prices->getCustomerGroupId() == $this->contextService->getCustomerGroupId()) {
                $pointers[] = $key;
            }
        }
        return $pointers;
    }

    /**
     * Set prices.
     *
     * @param float $finalPrice
     * @param int $quantity
     * @param string|null $percentage
     * @param string $type
     * @param int $count
     * @param array $prices
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
     * Get percentage type.
     *
     * @param string|null $percentage
     * @return string
     */
    private function getPercentageType($percentage)
    {
        return $type = isset($percentage) ? 'Percentage Discount' : 'Fixed Price';
    }
}
