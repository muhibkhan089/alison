<?php

namespace Cminds\Multiwishlist\Model\Wishlist;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Retriever;

class Manager
{
    /**
     * Product Builder for Wishlist.
     *
     * @var Retriever
     */
    protected $builderRetriever;

    /**
     * Wishlist Factory.
     *
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * Manager constructor.
     *
     * @param Retriever $retriever
     * @param WishlistFactory $wishlistFactory
     */
    public function __construct(
        Retriever $retriever,
        WishlistFactory $wishlistFactory
    ) {
        $this->builderRetriever = $retriever;
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * Add Product to Wishlist.
     * TODO move this method out of this class. This class should not be responsible for adding the product to wishlist.
     *
     * @param Product $product
     * @param array $params
     * @param null $wishlistId
     *
     * @return void
     */
    public function addProductToWishlist(Product $product, $params = [], $wishlistId = null)
    {
        $builder = $this->builderRetriever->getBuilder($product);
        $buyRequest = $builder->buildBuyRequest($product, $params);
        $product = $builder->updateOriginProduct($product);

        $this
            ->getWishlist($wishlistId)
            ->addNewItem($product, $buyRequest)
            ->save();
    }

    /**
     * Get Wishlist Object.
     * TODO in the future move this method to another object.
     *
     * @param null|int $wishlistId
     *
     * @return Wishlist
     * @throws LocalizedException
     */
    protected function getWishlist($wishlistId = null)
    {
        $wishlist = $this->wishlistFactory->create()
            ->load($wishlistId);
        if (!$wishlist->getId()) {
            throw new LocalizedException(__('Wishlist with %1 id doesn\'t exists', $wishlistId));
        }

        return $wishlist;
    }
}
