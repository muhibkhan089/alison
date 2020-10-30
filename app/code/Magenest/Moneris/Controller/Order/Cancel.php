<?php
namespace Magenest\Moneris\Controller\Order;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Webapi\Exception;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Cancel extends Action
{
    protected $commandPool;
    protected $logger;
    protected $orderRepository;
    protected $paymentDataObjectFactory;
    protected $checkoutSession;

    private $testData = [
        'response_order_id' =>  000000006,
        ''
    ];

    public function __construct(
        Context $context,
        CommandPoolInterface $commandPool,
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->commandPool = $commandPool;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute()
    {
        $cancelResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $orderId = $this->checkoutSession->getData('last_order_id');

        if (!is_numeric($orderId)) {
            $cancelResult->setHttpResponseCode(Exception::HTTP_BAD_REQUEST);
            $cancelResult->setData(['message' => __('Sorry, but something went wrong')]);
            return $cancelResult;
        } else {
            try {
                $order = $this->orderRepository->get((int)$orderId);
                $payment = $order->getPayment();
                if ($payment) {
                    ContextHelper::assertOrderPayment($payment);
                    $paymentDataObject = $this->paymentDataObjectFactory->create($payment);

                    $commandResult = $this->commandPool->get('cancel_order')->execute(['payment' => $paymentDataObject]);

                    $cancelResult->setData($commandResult->get());
                } else {
                    $cancelResult->setHttpResponseCode(Exception::HTTP_BAD_REQUEST);
                    $cancelResult->setData(['message' => __('Could not get Payment Data')]);
                    return $cancelResult;
                }
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
                return $this->getErrorResponse($cancelResult);
            }
        }
        return $this->_redirect('checkout/cart/index');
    }
}
