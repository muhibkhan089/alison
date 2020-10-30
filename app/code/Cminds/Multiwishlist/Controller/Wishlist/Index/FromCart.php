<?php

namespace Cminds\Multiwishlist\Controller\Wishlist\Index;

use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Magento\Wishlist\Controller\Index\Fromcart as WishlistFromCart;
use Cminds\Multiwishlist\Helper\ModuleConfig;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Customer\Model\Session;
use Magento\Wishlist\Model\Wishlist;

class FromCart extends WishlistFromCart
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * FromCart constructor.
     *
     * @param Context $context
     * @param WishlistProviderInterface $wishlistProvider
     * @param WishlistHelper $wishlistHelper
     * @param CheckoutCart $cart
     * @param CartHelper $cartHelper
     * @param Escaper $escaper
     * @param Validator $formKeyValidator
     * @param ModuleConfig $moduleConfig
     * @param WishlistFactory $wishlistFactory
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        WishlistProviderInterface $wishlistProvider,
        WishlistHelper $wishlistHelper,
        CheckoutCart $cart,
        CartHelper $cartHelper,
        Escaper $escaper,
        Validator $formKeyValidator,
        ModuleConfig $moduleConfig,
        WishlistFactory $wishlistFactory,
        Session $customerSession
    )
    {
        $this->moduleConfig = $moduleConfig;
        $this->wishlistFactory = $wishlistFactory;
        $this->customerSession = $customerSession;
        parent::__construct(
            $context,
            $wishlistProvider,
            $wishlistHelper,
            $cart,
            $cartHelper,
            $escaper,
            $formKeyValidator
        );
    }

    public function execute()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                return $resultRedirect->setPath('*/*/');
            }
            /** @var Wishlist $wishlist */
            if ($wishlistTitle = $this->getRequest()->getParam('wishlist_title')) {
                $wishlist = $this->wishlistFactory->create();
                $wishlist->generateSharingCode()
                    ->setTitle($wishlistTitle)
                    ->setCustomerId($this->customerSession->getCustomerId())
                    ->save();
            } else {
                $wishlist = $this->wishlistProvider->getWishlist();
            }

            if (!$wishlist) {
                throw new NotFoundException(__('Page not found.'));
            }

            try {
                $itemId = (int)$this->getRequest()->getParam('item');
                $item = $this->cart->getQuote()->getItemById($itemId);
                if (!$item) {
                    throw new LocalizedException(
                        __('The requested cart item doesn\'t exist.')
                    );
                }

                $productId = $item->getProductId();
                $buyRequest = $item->getBuyRequest();
                $wishlist->addNewItem($productId, $buyRequest);

                $this->cart->getQuote()->removeItem($itemId);
                $this->cart->save();

                $this->wishlistHelper->calculate();
                $wishlist->save();

                $this->messageManager->addSuccessMessage(__(
                    "%1 has been moved to your wish list.",
                    $this->escaper->escapeHtml($item->getProduct()->getName())
                ));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('We can\'t move the item to the wish list.'));
            }

            return $resultRedirect->setUrl($this->cartHelper->getCartUrl());
        }

        return parent::execute();
    }
}
