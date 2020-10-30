<?php

namespace Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type;

use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableTypeProduct;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\Associative;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\TypeBuilderInterface;

class Configurable implements TypeBuilderInterface
{
    const TYPE_CONFIGURABLE = 'configurable';

    /**
     * Configurable Product Type Object.
     *
     * @var ConfigurableTypeProduct
     */
    protected $configurableTypeProduct;

    /**
     * Product Builder for Wishlist for Associative Products.
     *
     * @var Associative
     */
    protected $associativeBuilder;

    /**
     * Configurable constructor.
     *
     * @param ConfigurableTypeProduct $configurableTypeProduct
     * @param Associative $associative
     */
    public function __construct(
        ConfigurableTypeProduct $configurableTypeProduct,
        Associative $associative
    ) {
        $this->configurableTypeProduct = $configurableTypeProduct;
        $this->associativeBuilder = $associative;
    }

    /**
     * Build buy request.
     *
     * @param Product $product
     * @param array $params
     *
     * @return DataObject
     */
    public function buildBuyRequest(Product $product, $params = [])
    {
        $this->validate($product);

        if (!$this->isAssociative($product)) {
            $buyRequest = new DataObject($params);

            return $buyRequest;
        }

        return $this->associativeBuilder->buildBuyRequest($product, $params);
    }

    /**
    * Update Original Product.
    *
    * @param Product $product
    *
    * @return Product
    */
    public function updateOriginProduct(Product $product)
    {
        if ($this->isAssociative($product)) {
            return $this->associativeBuilder->updateOriginProduct($product);
        }

        return $product;
    }

    /**
     * Validate whether it is configurable or associated product.
     *
     * @param Product $product
     *
     * @return bool
     * @throws LocalizedException
     */
    protected function validate(Product $product)
    {
        if ($product->getTypeId() !== self::TYPE_CONFIGURABLE) {
            if (!$this->isAssociative($product)) {
                throw new LocalizedException(__('The item for wishlist cannot be built.'));
            }
        }

        return true;
    }

    /**
     * Check if current simple product is associative.
     *
     * @param Product $product
     *
     * @return bool
     */
    protected function isAssociative(Product $product)
    {
        $parentIds = $this->configurableTypeProduct->getParentIdsByChild($product->getId());
        if ($parentIds) {
            return true;
        }

        return false;
    }
}
