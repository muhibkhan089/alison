<?php
namespace Redgiant\Dailydeals\Controller\Adminhtml\Dailydeal;

class Edit extends \Redgiant\Dailydeals\Controller\Adminhtml\Dailydeal
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Result JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Redgiant\Dailydeals\Model\DailydealFactory $dailydealFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Redgiant\Dailydeals\Model\DailydealFactory $dailydealFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->backendSession    = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($dailydealFactory, $registry, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Redgiant_Dailydeals::dailydeal');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('dailydeal_id');
        /** @var \Redgiant\Dailydeals\Model\Dailydeal $dailydeal */
        $dailydeal = $this->initDailydeal();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Redgiant_Dailydeals::dailydeal');
        $resultPage->getConfig()->getTitle()->set(__('Dailydeals'));
        if ($id) {
            $dailydeal->load($id);
            if (!$dailydeal->getId()) {
                $this->messageManager->addError(__('This Dailydeal no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'rg_dailydeals/*/edit',
                    [
                        'dailydeal_id' => $dailydeal->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $title = $dailydeal->getId() ? $dailydeal->getRg_product_sku() : __('New Dailydeal');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->backendSession->getData('rg_dailydeals_dailydeal_data', true);
        if (!empty($data)) {
            $dailydeal->setData($data);
        }
        return $resultPage;
    }
}
