<?php

namespace Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\TypeBuilderInterface;

class Simple implements TypeBuilderInterface
{
    /**
     * Build buy request for simple products.
     *
     * @param Product $product
     * @param array $params | is optional and is not used. Is created for the future cases.
     *
     * @return DataObject
     */
    public function buildBuyRequest(Product $product, $params = [])
    {
        $buyRequest = new DataObject($params);

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
}
