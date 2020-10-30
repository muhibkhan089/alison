<?php

namespace Cminds\MultiUserAccounts\Observer\Checkout;

use Cminds\MultiUserAccounts\Observer\Checkout\Quote\SubmitBefore;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class OnepageControllerSuccessAction implements ObserverInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * OnepageControllerSuccessAction constructor.
     *
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        // Reset temporary flag for quote change approval.
        $this->registry->register(
            SubmitBefore::CMINDS_MULTIUSERACCOUNTS_CHANGE_TEMP_USER_ID,
            null
        );
    }
}
