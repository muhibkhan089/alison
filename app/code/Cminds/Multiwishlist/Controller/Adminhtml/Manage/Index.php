<?php

namespace Cminds\Multiwishlist\Controller\Adminhtml\Manage;

class Index extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
