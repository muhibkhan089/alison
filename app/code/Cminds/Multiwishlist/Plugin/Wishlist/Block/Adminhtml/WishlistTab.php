<?php

namespace Cminds\Multiwishlist\Plugin\Wishlist\Block\Adminhtml;

use Cminds\Multiwishlist\Helper\ModuleConfig;

class WishlistTab
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * WishlistTab constructor.
     * @param ModuleConfig $moduleConfig
     */
    public function __construct(
        ModuleConfig $moduleConfig
    ) {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @param \Magento\Wishlist\Block\Adminhtml\WishlistTab $subject
     * @param bool $result
     * @return bool
     */
    public function afterCanShowTab(
        \Magento\Wishlist\Block\Adminhtml\WishlistTab $subject,
        $result
    ) {
        if ($this->moduleConfig->isEnabled() === true) {
            return false;
        }
        return $result;
    }
}