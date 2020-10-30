<?php

namespace Cminds\Multiwishlist\Controller\Wishlist\Index;

use Cminds\Multiwishlist\Helper\ModuleConfig;
use Magento\Catalog\Model\Product\Type;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\Bundle;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Model\WishlistFactory;

class Add extends \Magento\Wishlist\Controller\Index\Add
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
     * @var Bundle
     */
    protected $bundleBuilder;

    /**
     * Add constructor.
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param ProductRepositoryInterface $productRepository
     * @param Validator $formKeyValidator
     * @param WishlistFactory $wishlistFactory
     * @param ModuleConfig $moduleConfig
     * @param Bundle $bundleBuilder
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        ProductRepositoryInterface $productRepository,
        Validator $formKeyValidator,
        WishlistFactory $wishlistFactory,
        ModuleConfig $moduleConfig,
        Bundle $bundleBuilder
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->wishlistFactory = $wishlistFactory;
        $this->bundleBuilder = $bundleBuilder;
        parent::__construct($context, $customerSession, $wishlistProvider, $productRepository, $formKeyValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                return $resultRedirect->setPath('*/');
            }
            $request = $this->getRequest();

            /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
            if ($wishlistTitle = $request->getParam('wishlist_title')) {
                $wishlist = $this->wishlistFactory->create();
                $wishlist->generateSharingCode()
                    ->setTitle($wishlistTitle)
                    ->setCustomerId($this->_customerSession->getCustomerId())
                    ->save();
            } else {
                $wishlist = $this->wishlistProvider->getWishlist();
            }
            if (!$wishlist) {
                throw new NotFoundException(__('Page not found.'));
            }

            $session = $this->_customerSession;

            $requestParams = $request->getParams();
            if ($session->getBeforeWishlistRequest()) {
                $requestParams = $session->getBeforeWishlistRequest();
                $session->unsBeforeWishlistRequest();
            }

            $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;
            if (!$productId) {
                $resultRedirect->setPath('*/');
                return $resultRedirect;
            }

            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if (!$product || !$product->isVisibleInCatalog()) {
                $this->messageManager->addErrorMessage(__('We can\'t specify a product.'));
                $resultRedirect->setPath('*/');
                return $resultRedirect;
            }

            try {
                $buyRequest = new \Magento\Framework\DataObject($requestParams);
                if ($product->getTypeId() === Type::TYPE_BUNDLE){
                    $buyRequest =  $this->bundleBuilder->prepareBestPriceRequest($buyRequest,$product);
                }
                $result = $wishlist->addNewItem($product, $buyRequest);
                if (is_string($result)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__($result));
                }
                if ($wishlist->isObjectNew()) {
                    $wishlist->save();
                }
                $this->_eventManager->dispatch(
                    'wishlist_add_product',
                    ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
                );

                $referer = $session->getBeforeWishlistUrl();
                if ($referer) {
                    $session->setBeforeWishlistUrl(null);
                } else {
                    $referer = $this->_redirect->getRefererUrl();
                }

                $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();
                $this->messageManager->addComplexSuccessMessage(
                    'addProductToWishlistCustom',
                    array (
                        'product_name' => $product->getName(),
                        'list_title' => $wishlist->getTitle() ?: '',
                        'referer' => $referer
                    )
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t add the item to Wish List right now: %1.', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t add the item to Wish List right now.')
                );
            }

            $resultRedirect->setPath('*', ['wishlist_id' => $wishlist->getId()]);
            return $resultRedirect;
        }
        return parent::execute();
    }
}
