<?php

namespace Cminds\Multiwishlist\Controller\Manage;

use Cminds\Multiwishlist\Helper\ModuleConfig;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Wishlist\Model\WishlistFactory;

class Index extends AbstractAccount
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param ModuleConfig $moduleConfig
     * @param Session $customerSession
     * @param WishlistFactory $wishlistFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ModuleConfig $moduleConfig,
        Session $customerSession,
        WishlistFactory $wishlistFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->moduleConfig = $moduleConfig;
        $this->customerSession = $customerSession;
        $this->wishlistFactory = $wishlistFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Manage Wishlists'));
            return $resultPage;
        }
        return $this->_forward('index', 'noroute', 'cms');
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function getSuccessRedirect()
    {
        return $this->_redirect('wishlist/manage');
    }
}
