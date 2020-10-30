<?php

namespace Cminds\Multiwishlist\Block\Wishlist\Catalog\Product\View\AddTo;

use Cminds\Multiwishlist\Helper\ModuleConfig;

class Wishlist extends \Magento\Wishlist\Block\Catalog\Product\View\AddTo\Wishlist
{
    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'Magento_Wishlist::catalog/product/view/addto/wishlist.phtml';

    /**
     * @var string
     */
    protected $newTemplate = 'Cminds_Multiwishlist::catalog/product/view/addto/wishlist.phtml';

    /**
     * Wishlist constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $collectionFactory
     * @param ModuleConfig $moduleConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $collectionFactory,
        ModuleConfig $moduleConfig,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->moduleConfig = $moduleConfig;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        return $this->moduleConfig->isEnabled() ? $this->newTemplate : $this->_template;
    }

    /**
     * @return \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection|false
     */
    public function getWishlists()
    {
        if ($customerId = $this->customerSession->getCustomerId()) {
            return $this->collectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isLoggedInCustomer()
    {
        return $this->customerSession->isLoggedIn();
    }
}
