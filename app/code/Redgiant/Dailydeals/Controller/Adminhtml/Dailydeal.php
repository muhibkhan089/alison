<?php
namespace Redgiant\Dailydeals\Controller\Adminhtml;

abstract class Dailydeal extends \Magento\Backend\App\Action
{
    /**
     * Dailydeal Factory
     *
     * @var \Redgiant\Dailydeals\Model\DailydealFactory
     */
    protected $dailydealFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     *
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * constructor
     *
     * @param \Redgiant\Dailydeals\Model\DailydealFactory $dailydealFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Redgiant\Dailydeals\Model\DailydealFactory $dailydealFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->dailydealFactory      = $dailydealFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context);
    }

    /**
     * Init Dailydeal
     *
     * @return \Redgiant\Dailydeals\Model\Dailydeal
     */
    protected function initDailydeal()
    {
        $dailydealId  = (int) $this->getRequest()->getParam('dailydeal_id');
        /** @var \Redgiant\Dailydeals\Model\Dailydeal $dailydeal */
        $dailydeal    = $this->dailydealFactory->create();
        if ($dailydealId) {
            $dailydeal->load($dailydealId);
        }
        $this->coreRegistry->register('rg_dailydeals_dailydeal', $dailydeal);
        return $dailydeal;
    }
}
