<?php

namespace Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\TypeBuilderInterface;

class Bundle implements TypeBuilderInterface
{
    const TYPE_BUNDLE = 'bundle';

    /**
     * Build buy request for bundle products.
     *
     * @param Product $product
     * @param array $params
     *
     * @return DataObject
     */
    public function buildBuyRequest(Product $product, $params = [])
    {
        $buyRequest = $this->prepareBestPriceRequest($buyRequest = new DataObject($params), $product);

        return $buyRequest;
    }

    /**
     * Update Origin Product.
     *
     * @param Product $product
     *
     * @return Product
     */
    public function updateOriginProduct(Product $product)
    {
        return $product;
    }

    /**
     * @param $buyRequest
     * @param $bundleProduct
     *
     * @return mixed
     */
    public function prepareBestPriceRequest($buyRequest, $bundleProduct)
    {
        $selectionCollection = $bundleProduct->getTypeInstance(true)
            ->getSelectionsCollection(
                $bundleProduct->getTypeInstance(true)
                    ->getOptionsIds($bundleProduct),
                $bundleProduct
            );
        $optionsData = array();
        foreach ($selectionCollection as $selection) {
            $optionsData[$selection->getOptionId()][$selection->getSelectionId()] = $selection->getPrice();
        }

        $bestPrice = array();
        foreach ($optionsData as $key => $optionData) {
            $bestPrice[$key] = array_keys($optionData, min($optionData));
        }

        foreach ($bestPrice as $key => $data) {
            $newRequest['bundle_option'][$key] = $data[0];
        }

        $buyRequest['bundle_option'] = $newRequest['bundle_option'];
        $buyRequest['product'] = $bundleProduct->getId();

        return $buyRequest;
    }
}
