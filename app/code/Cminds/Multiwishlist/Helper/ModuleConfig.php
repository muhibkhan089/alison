<?php

namespace Cminds\Multiwishlist\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class ModuleConfig extends AbstractHelper
{
    /**
     * @var string
     */
    protected $storeCode;

    /**
     * @var string
     */
    protected $moduleConfigScope = 'multiwishlist/general/';

    /**
     * ModuleConfig constructor.
     * @param StoreManagerInterface $storeManagerInterface
     * @param Context $context
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        StoreManagerInterface $storeManagerInterface,
        Context $context
    ) {
        $this->storeCode = $storeManagerInterface->getStore()->getCode();
        parent::__construct($context);
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getConfigValue($name)
    {
        return $this->scopeConfig->getValue(
            $this->moduleConfigScope . $name,
            ScopeInterface::SCOPE_STORE,
            $this->storeCode
        );
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getConfigValue('enabled');
    }
}
