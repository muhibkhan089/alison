<?php

namespace Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;

interface TypeBuilderInterface
{
    /**
     * Build buy request.
     *
     * @param Product $product
     * @param array $params
     *
     * @return DataObject
     */
    public function buildBuyRequest(Product $product, $params = []);

    /**
     * Update Origin Product.
     *
     * @param Product $product
     *
     * @return Product
     */
    public function updateOriginProduct(Product $product);
}
