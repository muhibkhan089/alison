<?php
namespace Redgiant\Dailydeals\Controller\Adminhtml\Dailydeal;

class Delete extends \Redgiant\Dailydeals\Controller\Adminhtml\Dailydeal
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('dailydeal_id');
        if ($id) {
            $rg_product_sku = "";
            try {
                /** @var \Redgiant\Dailydeals\Model\Dailydeal $dailydeal */
                $dailydeal = $this->dailydealFactory->create();
                $dailydeal->load($id);
                $rg_product_sku = $dailydeal->getRg_product_sku();
                $dailydeal->delete();
                $this->messageManager->addSuccess(__('The Dailydeal has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_rg_dailydeals_dailydeal_on_delete',
                    ['rg_product_sku' => $rg_product_sku, 'status' => 'success']
                );
                $resultRedirect->setPath('rg_dailydeals/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_rg_dailydeals_dailydeal_on_delete',
                    ['rg_product_sku' => $rg_product_sku, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('rg_dailydeals/*/edit', ['dailydeal_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Dailydeal to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('rg_dailydeals/*/');
        return $resultRedirect;
    }
}
