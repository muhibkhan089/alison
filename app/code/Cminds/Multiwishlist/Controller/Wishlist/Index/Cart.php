<?php

namespace Cminds\Multiwishlist\Controller\Wishlist\Index;

use Braintree\Collection;
use Magento\Wishlist\Model\ResourceModel\Item\Option\Collection as OptionCollection;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Framework\Controller\ResultFactory;
use Cminds\Multiwishlist\Helper\ModuleConfig;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\Bundle;
use Magento\Wishlist\Controller\AbstractIndex;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\LocaleQuantityProcessor;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Checkout\Model\Cart as ModelCart;
use Magento\Checkout\Helper\Cart as HelperCart;
use Magento\Wishlist\Model\Item\OptionFactory;
use Magento\Catalog\Helper\Product;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Escaper;
use Magento\Wishlist\Helper\Data;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\Json;
use Magento\Wishlist\Model\Item;

/**
 * Class Cart
 *
 * @package Index
 */
class Cart extends AbstractIndex
{
    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var LocaleQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var ModelCart
     */
    protected $cart;

    /**
     * @var HelperCart
     */
    protected $cartHelper;

    /**
     * @var OptionFactory
     */
    private $optionFactory;

    /**
     * @var Product
     */
    protected $productHelper;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var Bundle
     */
    protected $bundleBuilder;

    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;


    /**
     * Cart constructor.
     *
     * @param Context $context
     * @param WishlistProviderInterface $wishlistProvider
     * @param LocaleQuantityProcessor $quantityProcessor
     * @param ItemFactory $itemFactory
     * @param ModelCart $cart
     * @param OptionFactory $optionFactory
     * @param Product $productHelper
     * @param Escaper $escaper
     * @param Data $helper
     * @param HelperCart $cartHelper
     * @param Validator $formKeyValidator
     * @param Bundle $bundleBuilder
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        Context $context,
        WishlistProviderInterface $wishlistProvider,
        LocaleQuantityProcessor $quantityProcessor,
        ItemFactory $itemFactory,
        ModelCart $cart,
        OptionFactory $optionFactory,
        Product $productHelper,
        Escaper $escaper,
        Data $helper,
        HelperCart $cartHelper,
        Validator $formKeyValidator,
        Bundle $bundleBuilder,
        ModuleConfig $moduleConfig
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->quantityProcessor = $quantityProcessor;
        $this->itemFactory = $itemFactory;
        $this->cart = $cart;
        $this->optionFactory = $optionFactory;
        $this->productHelper = $productHelper;
        $this->escaper = $escaper;
        $this->helper = $helper;
        $this->cartHelper = $cartHelper;
        $this->formKeyValidator = $formKeyValidator;
        $this->moduleConfig = $moduleConfig;
        $this->bundleBuilder = $bundleBuilder;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|Redirect|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/*/');
        }

        $itemId = (int)$this->getRequest()->getParam('item');
        /* @var $item Item */
        $item = $this->itemFactory->create()->load($itemId);
        if (!$item->getId()) {
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }
        $wishlist = $this->wishlistProvider->getWishlist($item->getWishlistId());
        if (!$wishlist) {
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }

        // Set qty
        $qty = $this->getRequest()->getParam('qty');
        $postQty = $this->getRequest()->getPostValue('qty');
        if ($postQty !== null && $qty !== $postQty) {
            $qty = $postQty;
        }
        if (is_array($qty)) {
            if (isset($qty[$itemId])) {
                $qty = $qty[$itemId];
            } else {
                $qty = 1;
            }
        }
        $qty = $this->quantityProcessor->process($qty);
        if ($qty) {
            $item->setQty($qty);
        }

        $redirectUrl = $this->_url->getUrl('*/*');
        $configureUrl = $this->_url->getUrl(
            '*/*/configure/',
            [
                'id' => $item->getId(),
                'product_id' => $item->getProductId(),
            ]
        );

        try {
            /** @var OptionCollection $options */
            $options = $this->optionFactory->create()
                ->getCollection()
                ->addItemFilter([$itemId]);
            $item->setOptions($options->getOptionsByItem($itemId));
            $buyRequest = $this->productHelper->addParamsToBuyRequest(
                $this->getRequest()->getParams(),
                ['current_config' => $item->getBuyRequest()]
            );
            if ($this->moduleConfig->isEnabled() === true && $item->getProduct()->getTypeId() === ProductType::TYPE_BUNDLE)
            {
                $buyRequest =  $this->bundleBuilder->prepareBestPriceRequest($buyRequest,$item->getProduct());
            }

            $item->mergeBuyRequest($buyRequest);
            $item->addToCart($this->cart, true);
            $this->cart->save()
                ->getQuote()
                ->collectTotals();
            $wishlist->save();

            if (!$this->cart->getQuote()->getHasError()) {
                $message = __(
                    'You added %1 to your shopping cart.',
                    $this->escaper->escapeHtml($item->getProduct()->getName())
                );
                $this->messageManager->addSuccess($message);
            }

            if ($this->cartHelper->getShouldRedirectToCart()) {
                $redirectUrl = $this->cartHelper->getCartUrl();
            } else {
                $refererUrl = $this->_redirect->getRefererUrl();
                if ($refererUrl && $refererUrl != $configureUrl) {
                    $redirectUrl = $refererUrl;
                }
            }
        } catch (ProductException $e) {
            $this->messageManager->addError(__('This product(s) is out of stock.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addNotice($e->getMessage());
            $redirectUrl = $configureUrl;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add the item to the cart right now.'));
        }

        $this->helper->calculate();

        if ($this->getRequest()->isAjax()) {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['backUrl' => $redirectUrl]);

            return $resultJson;
        }

        $resultRedirect->setUrl($redirectUrl);

        return $resultRedirect;
    }
}
