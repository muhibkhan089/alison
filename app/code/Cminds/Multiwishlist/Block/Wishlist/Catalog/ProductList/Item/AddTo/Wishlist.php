<?php

namespace Cminds\Multiwishlist\Block\Wishlist\Catalog\ProductList\Item\AddTo;

use Magento\Customer\Model\Session;
use Magento\Wishlist\Block\Catalog\Product\ProductList\Item\AddTo\Wishlist as MagentoWishlist;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;
use Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory;
use Magento\Catalog\Block\Product\Context;

class Wishlist extends MagentoWishlist
{
    protected $customerSession;
    protected $collectionFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get customer's wishlist.
     *
     * @return Collection|false
     */
    public function getWishlists()
    {
        if ($customerId = $this->customerSession->getCustomerId()) {
            return $this->collectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId);
        }
        return false;
    }

    /**
     * Check if the customer is logged in.
     *
     * @return bool
     */
    public function isLoggedInCustomer()
    {
        return $this->customerSession->isLoggedIn();
    }
}