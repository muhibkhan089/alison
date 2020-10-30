<?php

namespace Cminds\Multiwishlist\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;

class Save extends Delete
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Wishlist $wishlist
     * @param WishlistFactory $wishlistFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Wishlist $wishlist,
        WishlistFactory $wishlistFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $resultPageFactory, $registry, $wishlist, $wishlistFactory);
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function execute()
    {
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();
        $formkey = $this->getRequest()->getParam('form_key', '');
        if ($wishlistId = $request->getParam('wishlist_id', 0)) {
            $wishlist = $this->wishlistFactory->create()
                ->load($wishlistId);
            if ($wishlist->getId()) {
                if ($title = $request->getParam('title', '')) {
                    $wishlist->setTitle($title);
                }
                $products = $request->getParam('products', []);
                if (!$products) {
                    $products = $request->getParam('product', []);
                }
                /** @var \Magento\Wishlist\Model\Item $item */
                foreach ($wishlist->getItemCollection() as $item) {
                    $productId = $item->getProduct()->getId();
                    if (in_array($productId, $products)) {
                        unset($products[array_search($productId, $products)]);
                    } else {
                        $item->delete();
                    }
                }

                $wishlist->save();

                $this->messageManager->addSuccessMessage(__('Wishlist has been saved successfully.'));
                return $resultRedirect->setPath('wishlist/manage/view', ['id' => $wishlistId]);
            }
        }
        $this->messageManager->addErrorMessage(__('An error occurred while saving wishlist.'));
        return $resultRedirect->setPath('customer/index/index');
    }
}
