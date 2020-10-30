<?php

namespace Cminds\Multiwishlist\Block\Adminhtml\Customer\Edit\Tab\Wishlist;

use Cminds\Multiwishlist\Helper\ModuleConfig;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class Manage extends Template implements TabInterface
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * ManageTab constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ModuleConfig $moduleConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ModuleConfig $moduleConfig,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->moduleConfig = $moduleConfig;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Manage Wishlists');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            return $this->_authorization->isAllowed('Cminds_Multiwishlist::manage_wishlists');
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('wishlist/manage/index', ['_current' => true]);
    }

    /**
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
}
