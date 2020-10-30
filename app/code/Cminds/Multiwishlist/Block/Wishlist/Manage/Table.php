<?php

namespace Cminds\Multiwishlist\Block\Wishlist\Manage;

use Cminds\Multiwishlist\Helper\ModuleConfig;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;
use Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory;

class Table extends Template
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param ModuleConfig $moduleConfig
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param $listId
     * @return string
     */
    public function getListViewUrl($listId)
    {
        return $this->getUrl(
            'wishlist/index/index',
            ['wishlist_id' => $listId]
        );
    }

    /**
     * @param $listId
     * @return string
     */
    public function getListDeleteUrl($listId)
    {
        return $this->getUrl(
            'wishlist/manage/delete',
            ['id' => $listId]
        );
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('wishlist/manage/createPost');
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        $customerId = $this->customerSession->getCustomerId();
        return $this->collectionFactory->create()->addFieldToFilter('customer_id', $customerId);
    }
}
