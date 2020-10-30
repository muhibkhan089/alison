<?php

namespace Cminds\Multiwishlist\Controller\Manage;

use Magento\Wishlist\Model\Wishlist;

class Delete extends Index
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            if ($listId = $this->getRequest()->getParam('id')) {
                /** @var Wishlist $wishlist */
                $wishlist = $this->wishlistFactory->create()->load($listId);
                if ($wishlist->getId()) {
                    $customerId = (int)$wishlist->getCustomerId();
                    $currentCustomerId = (int)$this->customerSession->getCustomerId();
                    if ($customerId === $currentCustomerId) {
                        $title = $wishlist->getTitle() ?: __('No Title');
                        $wishlist->delete();
                        $this->messageManager->addSuccessMessage(__('Wishlist "%1" deleted successfully.', $title));
                        return $this->getSuccessRedirect();
                    }
                }
            }
        }
        return $this->_forward('index', 'noroute', 'cms');
    }
}
