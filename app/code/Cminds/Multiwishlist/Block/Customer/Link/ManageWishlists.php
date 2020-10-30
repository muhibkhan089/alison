<?php

namespace Cminds\Multiwishlist\Block\Customer\Link;

use Cminds\Multiwishlist\Helper\ModuleConfig;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;

class ManageWishlists extends Current
{
    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * ManageSubaccounts constructor.
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param ModuleConfig $moduleConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        ModuleConfig $moduleConfig,
        array $data = []
    ) {
        $this->moduleConfig = $moduleConfig;
        parent::__construct(
            $context,
            $defaultPath,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->moduleConfig->isEnabled() === true) {
            return parent::_toHtml();
        }
        return '';
    }
}
