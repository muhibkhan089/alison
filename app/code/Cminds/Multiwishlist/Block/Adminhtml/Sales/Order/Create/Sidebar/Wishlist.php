<?php

namespace Cminds\Multiwishlist\Block\Adminhtml\Sales\Order\Create\Sidebar;

use Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\Wishlist as SalesWishlist;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Cminds\Multiwishlist\Helper\ModuleConfig;
use Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Session\Quote;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Sales\Model\Config;

class Wishlist extends SalesWishlist
{
    /**
     * @var string
     */
    protected $template = 'Magento_Sales::order/create/sidebar/items.phtml';
    /**
     * @var string
     */
    protected $newTemplate = 'Cminds_Multiwishlist::order/create/sidebar/items.phtml';
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Wishlist constructor.
     *
     * @param Context $context
     * @param Quote $sessionQuote
     * @param Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param Config $salesConfig
     * @param ModuleConfig $moduleConfig
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Quote $sessionQuote,
        Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        Config $salesConfig,
        ModuleConfig $moduleConfig,
        CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        $this->moduleConfig = $moduleConfig;
        $this->collectionFactory = $collectionFactory;
        $this->setDataId('wishlist');
        parent::__construct($context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $salesConfig,
            $data
        );
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->moduleConfig->isEnabled() ? $this->newTemplate : $this->template;
    }

    /**
     * @return bool|mixed
     */
    public function getWishlistCollection()
    {
        $collectionWishlist = $this->getData('wishlist_collection');
        if ($collectionWishlist === null) {
            $collectionWishlist = $this->getWishlists();
            if ($collectionWishlist) {
                $collectionWishlist = $collectionWishlist->getItems();
            }
            $this->setData('wishlist_collection', $collectionWishlist);
        }
        return $collectionWishlist;
    }

    /**
     * @param $wishlist
     *
     * @return mixed
     */
    public function getWishlistItems($wishlist)
    {
        $items = $wishlist->getItemCollection();
        foreach ($items as $item) {
            $product = $item->getProduct();
            $item->setName($product->getName());
            $item->setPrice($product->getFinalPrice(1));
            $item->setTypeId($product->getTypeId());
        }

        return $items;
    }

    /**
     * @return bool
     */
    public function getWishlists()
    {
        if($customerId = (int)$this->_getSession()->getCustomerId()){
        return $this->collectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId);
        }

        return false;
    }

    /**
     * @param $wishlist
     *
     * @return bool|int|void
     */
    public function getItemsCount($wishlist)
    {
        return count($wishlist->getItemCollection());
    }
}
