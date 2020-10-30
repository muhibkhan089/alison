<?php

namespace Esparksinc\Consult\Block\Customer;

class Tab extends \Magento\Framework\View\Element\Html\Link\Current
{
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
       $this->_customerSession = $customerSession;
       parent::__construct($context, $defaultPath, $data);
   }

    // protected function _toHtml()
    // {    
    //     $responseHtml = null; //  need to return at-least null
    //     if($this->_customerSession->isLoggedIn()) {

    //         $customerGroup = $this->_customerSession->getCustomer()->getGroupId(); //Current customer groupID

    //         //Your Logic Here
    //         if($customerGroup == '4') {
    //             $responseHtml = parent::_toHtml(); //Return link html
    //         } 
    //     }
    //     return $responseHtml;
    // }

   protected function _toHtml()
   {
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $customerSession = $objectManager->create('Magento\Customer\Model\Session');
   $responseHtml = null; //  need to return at-least null
    // if(true)
   if($customerSession->isLoggedIn())
   {
        $customerGroup = $customerSession->getCustomer()->getGroupId(); //Current customer groupID
        if($customerGroup == '4') {
    $responseHtml = parent::_toHtml(); //Return link html
}
}
return $responseHtml;
}
}