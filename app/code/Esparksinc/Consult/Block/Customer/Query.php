<?php
namespace Esparksinc\Consult\Block\Customer;

class Query extends \Magento\Framework\View\Element\Template
{
    private $_queryFacotry;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Esparksinc\Consult\Model\QueryFactory $queryFacotry,
        array $data = []
    )
    {
        $this->_queryFacotry = $queryFacotry;
        parent::__construct($context, $data);
    }
    
    public function getTitle()
    {
        return 'Query List';
    }
    // public function getConsult($customerId)
    // {
    //     $query = $this->_queryFacotry->create()->getCollection()->addFieldToFilter('ergo_id',$customerId)->getData();
    //     return $query;
    // }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->forPager()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'query.list.history.pager'
            )->setAvailableLimit(array(5=>5,10=>10,15=>15,20=>20))
            ->setShowPerPage(true)->setCollection(
                $this->forPager()
            );
            $this->setChild('pager', $pager);
            $this->forPager()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function forPager()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        $customerId=$customerSession->getCustomer()->getId();
        


        //get values of current page
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
    //get values of current limit
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;

        $collection = $this->_queryFacotry->create()->getCollection()->addFieldToFilter('ergo_id',$customerId);

        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        
        return $collection;
    }
}
?>