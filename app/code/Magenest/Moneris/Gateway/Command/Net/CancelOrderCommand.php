<?php
namespace Magenest\Moneris\Gateway\Command\Net;

use Magento\Checkout\Model\Session;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Command\Result\BoolResultFactory;

class CancelOrderCommand implements CommandInterface
{
    protected $checkoutSession;
    protected $orderManagement;
    protected $resultFactory;

    public function __construct(
        OrderManagementInterface $orderManagement,
        Session $checkoutSession,
        BoolResultFactory $resultFactory
    ) {
        $this->resultFactory = $resultFactory;
        $this->checkoutSession = $checkoutSession;
        $this->orderManagement = $orderManagement;
    }

    public function execute(array $commandSubject)
    {
        $payment = SubjectReader::readPayment($commandSubject);
        $this->orderManagement->cancel($payment->getOrder()->getId());
        return $this->resultFactory->create(['result' => $this->checkoutSession->restoreQuote()]);
    }
}
