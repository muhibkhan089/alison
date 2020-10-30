<?php
namespace Redgiant\Berserk\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveBerserkSettings implements ObserverInterface
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
        $message = 'Saved Berserk Settings...';
        $this->_cssGenerator->generateCss('settings', $observer->getData("website"), $observer->getData("store"));
    }
}