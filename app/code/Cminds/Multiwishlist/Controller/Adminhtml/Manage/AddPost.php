<?php

namespace Cminds\Multiwishlist\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Catalog\Model\ProductFactory;
use Cminds\Multiwishlist\Model\Wishlist\Manager;

class AddPost extends Action
{
    /**
     * Product Factory.
     *
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * Wishlist Factory.
     *
     * @var WishlistFactory
     */
    private $wishlistFactory;

    /**
     * Wishlist Manager.
     *
     * @var Manager
     */
    private $wishlistManager;

    /**
     * AddPost constructor.
     *
     * @param Context $context
     * @param WishlistFactory $wishlistFactory
     * @param ProductFactory $productFactory
     * @param Manager $manager
     */
    public function __construct(
        Context $context,
        WishlistFactory $wishlistFactory,
        ProductFactory $productFactory,
        Manager $manager
    ) {
        parent::__construct($context);

        $this->wishlistFactory = $wishlistFactory;
        $this->productFactory = $productFactory;
        $this->wishlistManager = $manager;
    }

    /**
     * Add Products to the Wishlist.
     *
     * @return AddPost
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (!$params) {
            return $this;
        }

        if (!isset($params['wishlist_id'])) {
            return $this;
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        $wishlistId = $params['wishlist_id'] ?: 0;
        if ($wishlistId) {
            $wishlist = $this->wishlistFactory->create()
                ->load($wishlistId);
            if ($wishlist->getId()) {
                $products = $this
                    ->getRequest()
                    ->getParam('product', []);
                if (!$products) {
                    return $this;
                }

                foreach ($products as $productId) {
                    try {
                        $product = $this->productFactory->create()
                            ->load((int)$productId);
                        if (!$product->getId()) {
                            continue;
                        }

                        $this->wishlistManager->addProductToWishlist($product, $params, $wishlistId);
                    } catch (LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                }

                $this->messageManager->addSuccessMessage(__('Wishlist has been saved successfully.'));

                return $resultRedirect->setPath('wishlist/manage/view', ['id' => $wishlistId]);
            }
        }

        $this->messageManager->addErrorMessage(__('An error occurred while saving wishlist.'));

        return $resultRedirect->setPath('customer/index/index');
    }
}
