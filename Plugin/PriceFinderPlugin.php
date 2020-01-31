<?php

namespace Dotdigitalgroup\B2b\Plugin;

class PriceFinderPlugin
{
    /**
     * @var string
     */
    private $catalogContext = 'B2b';

    public function aroundGetMinPrices(
        \Dotdigitalgroup\Email\Model\Connector\PriceFinder $priceFinder,
        callable $proceed,
        $product,
        $module
    ) {
        if ($module !== $this->catalogContext) {
            return $proceed($product, $module);
        }

        $prices = [];
        if ($product->getTypeId() == 'simple') {
            if (!empty($product->getTierPrices())) {
                $prices['price'] = $product->getTierPrices()[0]->getData()['value'];
            } else {
                $prices['price'] = $product->getPrice();
            }
            $prices['special_price'] = $product->getSpecialPrice();
            return $prices;
        }
        $prices['price'] = 0;
        $prices['special_price'] = 0;
        return $prices;
    }
}
