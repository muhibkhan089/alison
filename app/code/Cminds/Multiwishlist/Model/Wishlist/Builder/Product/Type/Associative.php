<?php

namespace Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable as ConfigurableTypeProduct;
use Magento\Catalog\Model\ProductFactory;
use Cminds\Multiwishlist\Model\Product\Configurable\SuperAttributeRetriever;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\TypeBuilderInterface;

class Associative implements TypeBuilderInterface
{
    /**
     * Configurable Product Type Object.
     *
     * @var ConfigurableTypeProduct
     */
    protected $configurableTypeProduct;

    /**
     * Super Attribute Retriever.
     *
     * @var SuperAttributeRetriever
     */
    protected $superAttributeRetriever;

    /**
     * Porduct Factory.
     *
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * Associative constructor.
     *
     * @param ConfigurableTypeProduct $configurableTypeProduct
     * @param SuperAttributeRetriever $superAttributeRetriever
     * @param ProductFactory $productFactory
     */
    public function __construct(
        ConfigurableTypeProduct $configurableTypeProduct,
        SuperAttributeRetriever $superAttributeRetriever,
        ProductFactory $productFactory
    ) {
        $this->configurableTypeProduct = $configurableTypeProduct;
        $this->superAttributeRetriever = $superAttributeRetriever;
        $this->productFactory = $productFactory;
    }

    /**
     * Build buy request for associative product.
     *
     * @param Product $product
     * @param array $params | is optional and is not used. Is created for the future cases.
     *
     * @return DataObject
     */
    public function buildBuyRequest(Product $product, $params = [])
    {
        $this->validate($product);

        $parentIds = $this->getAssociativeParentIds($product);
        $superAttributesArray = [];
        foreach ($parentIds as $parentId) {
            $superAttributesData = $this->superAttributeRetriever->getProductSuperAttributesData($parentId);
            foreach ($superAttributesData as $attributeId => $attributeKey) {
                $superAttributesArray[$attributeId] = (string)$product->getData($attributeKey);
            }

            break;
        }

        $params['super_attribute'] = $superAttributesArray;

        return new DataObject($params);
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
        $parentIds = $this->configurableTypeProduct->getParentIdsByChild($product->getId());
        foreach ($parentIds as $parentId) {
            $product = $this->productFactory->create()
                ->load((int)$parentId);

            break;
        }

        return $product;
    }

    /**
     * Validate Associative Product.
     *
     * @param Product $product
     *
     * @return bool
     * @throws LocalizedException
     */
    protected function validate(Product $product)
    {
        if ($this->isAssociative($product)) {
            return true;
        }

        throw new LocalizedException(__('The item for wishlist cannot be built.'));
    }

    /**
     * Get Parent Ids of the Associated Product.
     *
     * @param Product $product
     *
     * @return string[]
     */
    protected function getAssociativeParentIds(Product $product)
    {
        return $this->configurableTypeProduct->getParentIdsByChild($product->getId());
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
