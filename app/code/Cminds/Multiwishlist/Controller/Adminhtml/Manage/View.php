<?php

namespace Cminds\Multiwishlist\Controller\Adminhtml\Manage;

use Cminds\Multiwishlist\Block\Adminhtml\Wishlist\Edit;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Wishlist\Model\Wishlist;

class View extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Wishlist
     */
    protected $wishlist;

    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Wishlist $wishlist
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Wishlist $wishlist
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->wishlist = $wishlist;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $this->wishlist->load($id);
            if (!$this->wishlist->getId()) {
                $this->messageManager->addErrorMessage(__('Wishlist does not exist.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $this->wishlist->setData($data);
        }
        $this->registry->register(Edit::WISHLIST_REGISTRY_KEY, $this->wishlist);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Customer::customer_manage');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Wishlists'));
        $resultPage->addBreadcrumb(__('Customers'), __('Customers'));
        $resultPage->addBreadcrumb(__('Manage Wishlists'), __('Manage Wishlists'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cminds_Multiwishlist::manage_wishlists');
    }
}