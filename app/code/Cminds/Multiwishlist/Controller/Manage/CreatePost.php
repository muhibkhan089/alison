<?php

namespace Cminds\Multiwishlist\Controller\Manage;

use Magento\Wishlist\Model\Wishlist;

class CreatePost extends Index
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            if ($listTitle = $this->getRequest()->getParam('title')) {
                /** @var Wishlist $wishlist */
                $wishlist = $this->wishlistFactory->create();
                $wishlist->generateSharingCode()
                    ->setTitle($listTitle)
                    ->setCustomerId($this->customerSession->getCustomerId())
                    ->save();
                $this->messageManager->addSuccessMessage(__('Wishlist "%1" created successfully.', $listTitle));
                return $this->getSuccessRedirect();
            }
        }
        return $this->_forward('index', 'noroute', 'cms');
    }
}
