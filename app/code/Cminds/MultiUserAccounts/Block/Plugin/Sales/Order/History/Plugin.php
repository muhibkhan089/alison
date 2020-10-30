<?php

namespace Cminds\MultiUserAccounts\Block\Plugin\Sales\Order\History;

use Cminds\MultiUserAccounts\Block\Plugin\Sales\Order\Plugin as OrderPlugin;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount\CollectionFactory;


/**
 * Cminds MultiUserAccounts sales order history block plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin extends OrderPlugin
{
    /**
     * Sub Account Collection Factory.
     *
     * @var CollectionFactory
     */
    private $subaccountCollectionFactory;

    /**
     * Plugin constructor.
     *
     * @param CustomerSession $customerSession
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param ModuleConfig $moduleConfig
     * @param ViewHelper $viewHelper
     * @param OrderConfig $orderConfig
     * @param CollectionFactory $subaccountCollectionFactory
     */
    public function __construct(
        CustomerSession $customerSession,
        OrderCollectionFactory $orderCollectionFactory,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        OrderConfig $orderConfig,
        CollectionFactory $subaccountCollectionFactory
    ) {
        parent::__construct(
            $customerSession,
            $orderCollectionFactory,
            $moduleConfig,
            $viewHelper,
            $orderConfig,
            $subaccountCollectionFactory
        );
    }

    /**
     * Around getOrders plugin.
     *
     * @param BlockInterface $subject Subject object.
     * @param \Closure       $proceed Closure.
     *
     * @return OrderCollection|bool
     */
    public function aroundGetOrders(
        BlockInterface $subject, // Magento\Sales\Block\Order\History
        \Closure $proceed
    ) {
        
        if ($this->moduleConfig->isEnabled() === false) {
            return $proceed();
        }

        $orders = $this->getOrders();
        
        return $orders;
    }
}
