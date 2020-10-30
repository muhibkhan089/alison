<?php

namespace Cminds\Multiwishlist\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;

class Delete extends View
{
    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * Delete constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Wishlist $wishlist
     * @param WishlistFactory $wishlistFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Wishlist $wishlist,
        WishlistFactory $wishlistFactory
    ) {
        $this->wishlistFactory = $wishlistFactory;
        parent::__construct($context, $resultPageFactory, $registry, $wishlist);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id = $this->getRequest()->getParam('id')) {
            $wishlist = $this->wishlistFactory->create()
                ->load($id);
            if ($wishlist->getId()) {
                try {
                    $customerId = $wishlist->getCustomerId();
                    $wishlist->delete();
                    $this->messageManager->addSuccessMessage(__('Wishlist has been deleted successfully.'));
                    return $resultRedirect->setPath('customer/index/edit', ['id' => $customerId]);
                } catch (\Exception $e) {
                    //goto redirect
                }
            }
        }
        $this->messageManager->addErrorMessage(__('Wishlist does not exist.'));
        return $resultRedirect->setPath('customer/index/index');
    }
}