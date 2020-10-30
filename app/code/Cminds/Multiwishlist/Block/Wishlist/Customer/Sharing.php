<?php

namespace Cminds\Multiwishlist\Block\Wishlist\Customer;

// Change Magento\Wishlist\Block\Customer\Sharing to Esparksinc\Consult\Block\Sharing
use Esparksinc\Consult\Block\Sharing as WishlistSharing;

class Sharing extends WishlistSharing
{
    /**
     * @return current wishlist id
     */
    public function getCurrentWishlistId(){
        return $this->getRequest()->getParam('wishlist_id');
    }
}
