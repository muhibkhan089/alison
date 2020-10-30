<?php
namespace Magenest\Moneris\Controller\Payment;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Sales\Api\OrderManagementInterface;

/**
 * Class Complete
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Complete extends Action
{
    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var OrderManagementInterface
     */
    private $orderManagement;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var PaymentDataObjectFactory
     */
    private $paymentDataObjectFactory;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param CommandPoolInterface $commandPool
     * @param LoggerInterface $logger
     * @param Session $checkoutSession
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param SessionManager $sessionManager
     */
    public function __construct(
        Context $context,
        CommandPoolInterface $commandPool,
        LoggerInterface $logger,
        Session $checkoutSession,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        OrderManagementInterface $orderManagement,
        SessionManager $sessionManager
    ) {
        parent::__construct($context);
        $this->commandPool = $commandPool;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->orderManagement = $orderManagement;
        $this->sessionManager = $sessionManager;
        $this->_resultFactory = $context->getResultFactory();
    }

    /**
     * @return ResultInterface
     * @throws \Exception
     */
    public function execute()
    {

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $params = $this->getRequest()->getParams();
            if (!isset($params['response_order_id'])) {
                throw new \Exception('Could not find Order Id');
            }
            $orderIncrementId = $params['response_order_id'];
            /** @var Order $order */
            $order = \Magento\Framework\App\ObjectManager::getInstance()->create(Order::class)->loadByIncrementId($orderIncrementId);
            if (!$order->getId()) {
                throw new \Exception('We can\'t find Order Id');
            }
            $payment = $order->getPayment();
            $arguments['payment'] = $this->paymentDataObjectFactory->create($payment);
            $arguments['response'] = $params;

            $this->commandPool->get('complete')->execute($arguments);
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
            $this->messageManager->addError($e->getMessage());
            $resultRedirect->setPath('moneris/order/cancel');
            return $resultRedirect;
        }

        $resultRedirect->setPath('checkout/onepage/success');

        return $resultRedirect;
    }
}
