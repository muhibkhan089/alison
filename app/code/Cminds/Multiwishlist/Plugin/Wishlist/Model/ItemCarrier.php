<?php

namespace Cminds\Multiwishlist\Plugin\Wishlist\Model;

use Cminds\Multiwishlist\Helper\ModuleConfig;
use Cminds\Multiwishlist\Model\Wishlist\Builder\Product\Type\Bundle;
use Magento\Wishlist\Model\ItemCarrier as CoreItemCarrier;
use Magento\Catalog\Model\Product\Type as ProductType;

class ItemCarrier
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var Bundle
     */
    protected $bundleBuilder;

    /**
     * ItemCarrier constructor.
     *
     * @param ModuleConfig $moduleConfig
     * @param Bundle $bundleBuilder
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        Bundle $bundleBuilder
    )
    {
        $this->moduleConfig = $moduleConfig;
        $this->bundleBuilder = $bundleBuilder;
    }

    /**
     * @param CoreItemCarrier $subject
     * @param $wishlist
     * @param $qtys
     *
     * @return array
     */
    public function beforeMoveAllToCart(CoreItemCarrier $subject, $wishlist, $qtys)
    {
        $collection = $wishlist->getItemCollection()->setVisibilityFilter();

        foreach ($collection as $item) {
            if ($this->moduleConfig->isEnabled() === true && $item->getProduct()->getTypeId() === ProductType::TYPE_BUNDLE)
            {
                $buyRequest =  $this->bundleBuilder->prepareBestPriceRequest($item->getBuyRequest(),$item->getProduct());
                $item->mergeBuyRequest($buyRequest);
            }
        }

        return array($wishlist,$qtys);
    }
}