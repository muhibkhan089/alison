<?php

namespace Cminds\MultiUserAccounts\Observer\Adminhtml\Customer\SaveAfter;

use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Cminds\MultiUserAccounts\Model\SubaccountFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Cminds\MultiUserAccounts\Model\Service\Convert\Customer\ParentAccount as ParentAccountConverter;
use Cminds\MultiUserAccounts\Model\Service\Assign\Account;
use Magento\Framework\Message\ManagerInterface;

class ParentAccountUpdate implements ObserverInterface
{
    /**
     * Module Config.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Subaccount Factory.
     *
     * @var SubaccountFactory
     */
    private $subaccountFactory;

    /**
     * Request object.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * Registry Object.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Parent Account Converter.
     *
     * @var ParentAccountConverter
     */
    private $parentAccountConverter;

    /**
     * Account Assigner.
     *
     * @var Account
     */
    private $accountAssigner;

    /**
     * Message Manager.
     *
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * ParentAccountUpdate constructor.
     *
     * @param ModuleConfig $moduleConfig
     * @param SubaccountFactory $subaccountFactory
     * @param RequestInterface $requestInterface
     * @param Registry $registry
     * @param ParentAccountConverter $parentAccountConverter
     * @param Account $accountAssigner
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        SubaccountFactory $subaccountFactory,
        RequestInterface $requestInterface,
        Registry $registry,
        ParentAccountConverter $parentAccountConverter,
        Account $accountAssigner,
        ManagerInterface $messageManager
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->subaccountFactory = $subaccountFactory;
        $this->request = $requestInterface;
        $this->registry = $registry;
        $this->parentAccountConverter = $parentAccountConverter;
        $this->accountAssigner = $accountAssigner;
        $this->messageManager = $messageManager;
    }

    /**
     * Save parent customer id for the user after save on admin side.
     *
     * @param Observer $observer
     *
     * @return ParentAccountUpdate
     */
    public function execute(Observer $observer)
    {
        try {
            if ($this->moduleConfig->isEnabled() === false) {
                return $this;
            }

            $customerData = $this->getCustomerPostData();
            if (!$customerData) {
                return $this;
            }

            $this->manageParentAccountIdData();
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving parent account'));
        }
    }

    /**
     * Manage parent account id data.
     *
     * @return $this
     */
    protected function manageParentAccountIdData()
    {
        $entityId = $this->getCustomerId();
        if (!$entityId) {
            return $this;
        }

        $parentAccountId = $this->retrieveParentAccountId();
        if (!$parentAccountId) {
            /** If there is no parent account id, then make sure, that the customer is master account. */
            $this->parentAccountConverter->convertCustomer($entityId);
        } else {
            /** If the parent account id exists, then make sure, that the customer is sub account of that parent. */
            $this->accountAssigner->assignCustomerToParent($parentAccountId, $entityId);
        }
    }

    /**
     * Get customer id from the post.
     *
     * @return int|null
     */
    protected function getCustomerId()
    {
        $customerData = $this->getCustomerPostData();

        return isset($customerData['entity_id']) ? (int)$customerData['entity_id'] : null;
    }

    /**
     * Get customer post data.
     *
     * @return array
     */
    protected function getCustomerPostData()
    {
        return $this->request->getParam('customer') ?: [];
    }

    /**
     * Retrieve parent account id, which was sent by post.
     *
     * @return int|null
     */
    protected function retrieveParentAccountId()
    {
        $customerData = $this->getCustomerPostData();

        return isset($customerData['parent_account_id']) ? (int)$customerData['parent_account_id'] : null;
    }
}
