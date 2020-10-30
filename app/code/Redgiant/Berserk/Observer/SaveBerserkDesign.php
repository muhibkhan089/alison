<?php
namespace Redgiant\Berserk\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveBerserkDesign implements ObserverInterface
{
    protected $_messageManager;
    protected $_cssGenerator;

    public function __construct(
        \Redgiant\Berserk\Model\Cssconfig\Generator $cssenerator,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_cssGenerator = $cssenerator;
        $this->_messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $message = 'Saved Berserk Design...';
        $this->_cssGenerator->generateCss('design', $observer->getData("website"), $observer->getData("store"));
    }
}