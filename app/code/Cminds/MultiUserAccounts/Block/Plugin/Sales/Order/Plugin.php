<?php

namespace Cminds\MultiUserAccounts\Block\Plugin\Sales\Order;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount\CollectionFactory as SubaccountCollectionFactory;

/**
 * Cminds MultiUserAccounts recent sales order history block plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * Order collection object.
     *
     * @var OrderCollection
     */
    protected $orders;

    /**
     * Session object.
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Order collection factory object.
     *
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * Module config object.
     *
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * View helper object.
     *
     * @var ViewHelper
     */
    protected $viewHelper;

    /**
     * Order config object.
     *
     * @var OrderConfig
     */
    protected $orderConfig;

    /**
     * Sub Account Collection Factory.
     *
     * @var SubaccountCollectionFactory
     */
    private $subaccountCollectionFactory;

    /**
     * Object initialization..
     *
     * @param CustomerSession $customerSession Session object.
     * @param OrderCollectionFactory $orderCollectionFactory Order collection
     *     factory object.
     * @param ModuleConfig $moduleConfig Module config object.
     * @param ViewHelper $viewHelper View helper object.
     * @param OrderConfig $orderConfig Order config object.
     *     resource model object.
     * @param SubaccountCollectionFactory $subaccountCollectionFactory
     */
    public function __construct(
        CustomerSession $customerSession,
        OrderCollectionFactory $orderCollectionFactory,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        OrderConfig $orderConfig,
        SubaccountCollectionFactory $subaccountCollectionFactory
    ) {
        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->orderConfig = $orderConfig;
        $this->subaccountCollectionFactory = $subaccountCollectionFactory;
    }

    /**
     * Around getOrders plugin.
     *
     * @param BlockInterface $subject Subject object.
     * @param \Closure $proceed Closure.
     * @param string $key Key.
     * @param mixed $index Index.
     *
     * @return OrderCollection|bool
     */
    public function aroundGetData(
        BlockInterface $subject, // Magento\Sales\Block\Order\Recent
        \Closure $proceed,
        $key = '',
        $index = null
    ) {
        if ($key !== 'orders') {
            return $proceed($key, $index);
        }

        if ($this->moduleConfig->isEnabled() === false) {
            return $proceed($key, $index);
        }

        if ($this->orders === null) {
            $this->orders = $this
                ->getOrders()
                ->setPageSize('5')
                ->load();
        }

        return $this->orders;
    }

    /**
     * Return order collection.
     *
     * @return OrderCollection|bool
     */
    protected function getOrders()
    {
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) { // check if needed
            return false;
        }

        $parentIds = $targetAccountIds = [];
        $parentCanSeeSubaccountsOrderHistory = (bool)$this->moduleConfig->getParentCanSeeSubaccountsOrderHistory();

        $isSubAccount = (bool)$this->viewHelper->isSubaccountLoggedIn();
        if( true === $isSubAccount) {
            /** @var SubaccountTransportInterface $subaccountTransportDataObject */
            $subaccountTransportDataObject = $this->customerSession
                ->getSubaccountData();
            $customerId = $subaccountTransportDataObject->getCustomerId();
            // if customer can view parent account orders 
            // and orders from other sibling subaccounts
            if( true === (bool)$subaccountTransportDataObject->getAccountOrderHistoryViewPermission()
                && true === $parentCanSeeSubaccountsOrderHistory
            ){
                // add master id to array in order to include master orders too
                $targetAccountIds[] = $parentIds[] = $subaccountTransportDataObject->getParentCustomerId();
            }
        } else {
            $customerId = $this->customerSession->getCustomerId();
        }

        $targetAccountIds[] = $parentIds[] = $customerId;

        // if veiwing child orders is allowed
        if( true === $parentCanSeeSubaccountsOrderHistory ){
            $subaccountCollection = $this->subaccountCollectionFactory->create()
                ->addFieldToSelect(
                    'customer_id'
                )
                ->addFieldToFilter(
                    'parent_customer_id', [ "in" => $parentIds ]
                )
                ->setOrder(
                    'created_at','desc'
                );
            
            if( $subaccountCollection ){
                foreach ($subaccountCollection as $subAccount) {
                    $targetAccountIds[] = $subAccount->getCustomerId();
                }
            }
        }
        
        $orderCollection = $this->orderCollectionFactory->create()
            ->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'status',
                ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
            )->setOrder(
                'created_at',
                'desc'
            )->addFieldToFilter(
                'customer_id',
                ['in'=>$targetAccountIds]
            );

        
        return $orderCollection;
    }
}
