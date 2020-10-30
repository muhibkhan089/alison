<?php

namespace Cminds\Multiwishlist\Block\Wishlist;

class Link extends \Magento\Wishlist\Block\Link
{
    /**
     * @var string
     */
    protected $newTemplate = 'Cminds_Multiwishlist::link.phtml';

    /**
     * @var \Cminds\Multiwishlist\Helper\ModuleConfig
     */
    protected $moduleConfig;

    /**
     * Link constructor.
     * @param \Cminds\Multiwishlist\Helper\ModuleConfig $moduleConfig
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param array $data
     */
    public function __construct(
        \Cminds\Multiwishlist\Helper\ModuleConfig $moduleConfig,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        array $data = []
    ) {
        $this->moduleConfig = $moduleConfig;
        parent::__construct($context, $wishlistHelper, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            return $this->getUrl('wishlist/manage');
        }
        return parent::getHref();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            return __('My Wish Lists');
        }
        return parent::getLabel();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->moduleConfig->isEnabled() ? $this->newTemplate : $this->_template;
    }
}
