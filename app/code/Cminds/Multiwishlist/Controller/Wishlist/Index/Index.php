<?php

namespace Cminds\Multiwishlist\Controller\Wishlist\Index;

use Cminds\Multiwishlist\Helper\ModuleConfig;
use Magento\Framework\App\Action;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Wishlist\Controller\Index\Index
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * Index constructor.
     * @param Action\Context $context
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        Action\Context $context,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        ModuleConfig $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;
        parent::__construct($context, $wishlistProvider);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            $wishlist = $this->wishlistProvider->getWishlist();
            if (!$wishlist) {
                throw new NotFoundException(__('Page not found.'));
            }
            /** @var \Magento\Framework\View\Result\Page resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $wishlistTitle = $wishlist->getTitle() ?: __('No Title');
            $resultPage->getConfig()->getTitle()->set(__('Wishlist "%1"', $wishlistTitle));
            return $resultPage;
        }
        return parent::execute();
    }
}
