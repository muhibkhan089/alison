<?php

namespace Cminds\Multiwishlist\Model\Wishlist\Builder\Product;

use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\TypeBuilderInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableTypeProduct;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ModelConfigurableTypeProduct;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\Simple;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\Configurable;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\Bundle;
use Magento\Framework\Exception\LocalizedException;

class Retriever
{
    const TYPE_SIMPLE = 'simple';
    const TYPE_CONFIGURABLE = 'configurable';
    const TYPE_BUNDLE = 'bundle';


    /**
     * Array of product wishlist builders.
     *
     * @var array
     */
    protected $builders;

    /**
     * Simple Product Builder for Wishlist.
     *
     * @var Simple
     */
    protected $simpleBuilder;

    /**
     * Configurable Product Builder for Wishlist.
     *
     * @var Simple
     */
    protected $configurableBuilder;

    /**
     * Configurable Type Product Object.
     *
     * @var ConfigurableTypeProduct
     */
    protected $configurableTypeProduct;

    /**
     * @var Bundle
     */
    protected $bundleBuilder;

    /**
     * Retriever constructor.
     *
     * @param Simple $simpleBuilder
     * @param Configurable $configurableBuilder
     * @param Bundle $bundleBuilder
     * @param ConfigurableTypeProduct $configurableTypeProduct
     */
    public function __construct(
        Simple $simpleBuilder,
        Configurable $configurableBuilder,
        Bundle $bundleBuilder,
        ConfigurableTypeProduct $configurableTypeProduct
    ) {
        $this->simpleBuilder = $simpleBuilder;
        $this->configurableBuilder = $configurableBuilder;
        $this->bundleBuilder = $bundleBuilder;
        $this->configurableTypeProduct = $configurableTypeProduct;
    }

    /**
     * Get Product Builder for Wishilist.
     *
     * @param Product $product
     *
     * @return TypeBuilderInterface
     */
    public function getBuilder(Product $product)
    {
        if ($this->builders === null) {
            $this->init();
        }

        $type = $this->getProductTypeBuilder($product);
        foreach ($this->builders as $builderType => $builder) {
            if ($builderType === $type) {
                return $builder;
            }
        }

        return $this->builders[self::TYPE_SIMPLE];
    }

    /**
     * Get Product Builder Type.
     *
     * @param Product $product
     *
     * @return array|string
     * @throws LocalizedException
     */
    protected function getProductTypeBuilder(Product $product)
    {
        $productId = (int)$product->getId();
        if (!$productId) {
            throw new LocalizedException(__('The product id is not specified'));
        }

        $parentIds = $this->configurableTypeProduct->getParentIdsByChild($productId);
        if ($parentIds) {
            return self::TYPE_CONFIGURABLE;
        }

        return $product->getTypeId();
    }

    /**
     * Init product builders.
     */
    protected function init()
    {
        $this->builders[self::TYPE_SIMPLE] = $this->simpleBuilder;
        $this->builders[self::TYPE_CONFIGURABLE] = $this->configurableBuilder;
        $this->builders[self::TYPE_BUNDLE] = $this->bundleBuilder;
    }
}
