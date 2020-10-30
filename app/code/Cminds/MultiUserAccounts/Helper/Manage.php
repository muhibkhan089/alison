<?php

namespace Cminds\MultiUserAccounts\Helper;

use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Cminds\MultiUserAccounts\Model\Config;

class Manage extends AbstractHelper
{
    /**
     * Session object.
     *
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Customer Repository Interface
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Module Config.
     *
     * @var Config
     */
    private $moduleConfig;

    /**
     * Manage constructor.
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param Config $moduleConfig
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Config $moduleConfig
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->moduleConfig = $moduleConfig;

        parent::__construct($context);
    }

    /**
     * Check if converted to parent account customer can manage sub accounts.
     *
     * @param $customerId
     *
     * @return bool
     */
    public function getCanConvertedParentsManageSubAccountsValue($customerId)
    {
        $value = true;

        if (!$this->moduleConfig->canParentAccountManageSubaccounts()) {
            return false;
        }

        return $value;
    }
}
