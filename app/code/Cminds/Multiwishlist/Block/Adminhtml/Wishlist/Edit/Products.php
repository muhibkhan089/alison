<?php

namespace Cminds\Multiwishlist\Block\Adminhtml\Wishlist\Edit;

use Cminds\Multiwishlist\Block\Adminhtml\Wishlist\Edit;
use Magento\Backend\Block\Template;

class Products extends Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'Cminds_Multiwishlist::wishlist/products.phtml';

    /**
     * Products constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist()
    {

        return $this->registry->registry(Edit::WISHLIST_REGISTRY_KEY);
    }
}