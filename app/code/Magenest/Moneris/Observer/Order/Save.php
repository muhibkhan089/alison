<?php
namespace Magenest\Moneris\Observer\Order;

use Magenest\Moneris\Model\Adminhtml\Source\OrderHandlerAction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\CreditmemoFactory;
use \Magento\Sales\Model\Service\CreditmemoService;

class Save implements ObserverInterface
{
    protected $orderFactory;
    protected $creditmemoFactory;
    protected $creditmemoService;
    protected $messageManager;

    public function __construct(
        CreditmemoFactory $creditmemoFactory,
        CreditmemoService $creditmemoService,
        OrderFactory $orderFactory,
        ManagerInterface $messageManager
    ) {
    
        $this->orderFactory = $orderFactory;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->messageManager = $messageManager;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        try {
            /** @var Order $order */
            $order = $observer->getOrder();
            $additionalInfo = $order->getPayment()->getAdditionalInformation();

            if ($order->getPayment() &&
                $order->getPayment()->getMethod() == \Magenest\Moneris\Block\Payment::MONERIS_CODE &&
                !$order->hasCreditmemos() &&
                isset($additionalInfo['order_action'])
            ) {
                $errMessage = isset($additionalInfo['order_action_handler_code'])
                    ? $this->getMessageError($additionalInfo['order_action_handler_code'])
                    : $this->getMessageError();
                $invoices = $order->getInvoiceCollection();
                if ($additionalInfo['order_action'] == OrderHandlerAction::ORDER_ACTION_CANCEL &&
                    !$order->getPayment()->getAmountRefunded()
                ) {
                    if ($order->hasInvoices()) {
                        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
                        foreach ($invoices as $invoice) {
                            $creditmemo = $this->creditmemoFactory->createByOrder($order);
                            $creditmemo->setInvoice($invoice);
                            $this->creditmemoService->refund($creditmemo);
                        }
                        $this->messageManager->addError(
                            "Your order has been canceled due to ".$errMessage
                        );
                    } elseif ($order->getState() != Order::STATE_CANCELED) {
                        $order->cancel()->save();
                        $this->messageManager->addError(
                            "Your order has been canceled due to ".$errMessage
                        );
                    }
                } elseif ($additionalInfo['order_action'] == OrderHandlerAction::ORDER_ACTION_HOLD &&
                    $order->getState() != Order::STATE_HOLDED
                ) {
                    $order->hold()->save();
                    $this->messageManager->addNotice(
                        "Your order is on hold due to ".$errMessage
                    );
                }
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug('Moneris Save Order Exception: ' . $e->getMessage());
            $this->messageManager->addError(
                "Something happened during processing order. Please contact admin for more information."
            );
        } finally {
            return $this;
        }
    }

    private function getMessageError($errorHandlerCode = null)
    {
        switch ($errorHandlerCode) {
            case OrderHandlerAction::ORDER_ACTION_AVS_HANDLER:
                return "Address Verification Service (AVS) check is not a match. Please try again. Checkout order details for more information.";
            case OrderHandlerAction::ORDER_ACTION_CVD_HANDLER:
                return "Card Validation Digits (CVD) check is not a match. Please try again. Checkout order details for more information.";
            default:
                return "Address Verification Service (AVS) or Card Validation Digits (CVD) is not a match. Please try again. Checkout order details for more information.";
        }
    }
}
