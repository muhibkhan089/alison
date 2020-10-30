<?php

namespace Redgiant\Filterproducts\Block\Home;

class FilterlistBlock extends \Magento\Framework\View\Element\Template {
    protected $_collection;
    protected $_resource;
    protected $_productCollectionFactory;
    protected $_helper;
    protected $_urlHelper;
    protected $_wishlistHelper;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context, 
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Redgiant\Berserk\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        $this->_resource = $resource;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_helper = $helper;
        $this->_wishlistHelper = $context->getWishlistHelper();
        $this->_urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice($product)
    {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }
        return $price;
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_block = $objectManager->get('\Magento\Catalog\Block\Product\ListProduct');
        $url =  $_block->getAddToCartUrl($product);

        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->_urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * Retrieve add to wishlist params
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToWishlistParams($product)
    {
        return $this->_wishlistHelper->getAddParams($product);
    }
    
    /**
     * Whether redirect to cart enabled
     *
     * @return bool
     */
    public function isRedirectToCartEnabled()
    {
        return $this->_scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getReviewsSummaryHtml($product, $templateType) {
        $review_model = $this->_helper->getModel('\Magento\Review\Model\Review');
        $review_model->getEntitySummary($product);
        $reviewRating = (int) $product->getRatingSummary()->getRatingSummary();
        $reviewcount = $product->getRatingSummary()->getReviewsCount();
        $html = '';
        if($reviewRating > 0) {
            $html = '<div class="product-reviews-summary" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
                        <div class="rating-summary">
                        <span class="label"><span>Rating:</span></span>
                        <div class="rating-result" title="' . $reviewRating . '%">
                            <span style="width:' . $reviewRating . '%"><span><span itemprop="ratingValue">' . $reviewRating . '</span>% of <span itemprop="bestRating">100</span></span></span>
                        </div>
                    </div>
                    </div>';
        }
        return $html;
    }

    public function getProductDetailsHtml($product) {
        $html = '';
        $isEnabled = $this->_scopeConfig->getValue('weltpixel_quickview/general/enable_product_listing',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!empty($isEnabled)) {
            $productUrl = $this->_urlBuilder->getUrl('weltpixel_quickview/catalog_product/view', array('id' => $product->getId()));
            $html = '<a class="weltpixel-quickview action" data-quickview-url=' . $productUrl . ' href="javascript:void(0);"><i class="fas fa-search"></i></a>';
        }
        
        return $html;
    }
}
?>