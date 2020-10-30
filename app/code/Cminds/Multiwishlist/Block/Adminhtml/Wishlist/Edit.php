<?php

namespace Cminds\Multiwishlist\Block\Adminhtml\Wishlist;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{
    const WISHLIST_REGISTRY_KEY = 'cm_wishlist';

    /**
     * {@inheritdoc}
     */
    protected $_objectId = 'entity_id';

    /**
     * {@inheritdoc}
     */
    protected $_blockGroup = 'Cminds_Multiwishlist';

    /**
     * {@inheritdoc}
     */
    protected $_controller = 'adminhtml_wishlist';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->init();
    }

    /**
     * Init save button
     */
    public function init()
    {
        $this->buttonList->update('save', 'label', __('Save Wishlist'));
        $this->buttonList->update('back', 'label', __('Back To Customer'));
        $this->buttonList->remove('reset');
        $wishlist = $this->coreRegistry->registry(self::WISHLIST_REGISTRY_KEY);
        $customerId = $wishlist ? $wishlist->getCustomerId() : 0;
        $this->buttonList->update(
            'back',
            'onclick',
            'setLocation("' . $this->getUrl('customer/index/edit', ['id' => $customerId]) . '")'
        );

        $this->buttonList->add(
            'delete',
            [
                'label' => __('Delete'),
                'class' => 'delete',
                'onclick' => 'setLocation("' . $this->getUrl(
                        'wishlist/manage/delete', ['id' => $this->getRequest()->getParam('id')]
                    ) . '")'
            ],
            -100
        );
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeaderText()
    {
        if ($wishlist = $this->coreRegistry->registry(self::WISHLIST_REGISTRY_KEY)) {
            return __("Edit Wishlist '%1'", $this->escapeHtml($wishlist->getId()));
        }
        return __('New Wishlist');
    }
}