<?php

namespace Cminds\Multiwishlist\Model\Product\Configurable;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class SuperAttributeRetriever
{
    /**
     * Product Repository.
     *
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * SuperAttributeRetriever constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * Get Super Attributes Data from the Product.
     * For instance:
     * Array (
     *     [93] => color
     *     [254] => size
     *)
     *
     * @param $productId
     *
     * @return array
     */
    public function getProductSuperAttributesData($productId)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->getById($productId);
        if ($product->getTypeId() != Configurable::TYPE_CODE) {
            return [];
        }

        /** @var Configurable $productTypeInstance */
        $productTypeInstance = $product
            ->getTypeInstance()
            ->setStoreFilter($product->getStoreId(), $product);

        $attributes = $productTypeInstance->getConfigurableAttributes($product);
        $superAttributeList = [];
        foreach($attributes as $_attribute){
            $attributeCode = $_attribute->getProductAttribute()->getAttributeCode();;
            $superAttributeList[$_attribute->getAttributeId()] = $attributeCode;
        }
        return $superAttributeList;
    }
}
