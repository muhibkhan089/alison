<?php

namespace Cminds\Multiwishlist\Model\ResourceModel\Wishlist\Collection;

use Magento\Customer\Controller\RegistryConstants;

class Grid extends \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Grid constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->registry = $registry;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));
        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     * @return \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $field = 'wishlist_id';
        $direction = self::SORT_ORDER_ASC;
        return parent::setOrder($field, $direction);
    }
}
