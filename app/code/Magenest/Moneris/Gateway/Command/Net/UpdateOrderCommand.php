<?php
namespace Magenest\Moneris\Gateway\Command\Net;

use Magento\Payment\Gateway\Command;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magenest\Moneris\Gateway\Helper\ResponseReader;

/**
 * Class UpdateOrderCommand
 */
class UpdateOrderCommand implements CommandInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ConfigInterface $config,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->config = $config;
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $commandSubject)
    {
        $paymentDO = SubjectReader::readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);
        $response = ResponseReader::readResponse($commandSubject);
        if (array_key_exists('trans_name', $response)) {
            $transactionType = $response['trans_name'];
        } else {
            $transactionType = $response['txn_type'];
        }
        switch ($transactionType) {
            case 'purchase':
                $payment->capture();
                break;
            case 'preauth':
                $payment->authorize(
                    false,
                    $paymentDO->getOrder()->getGrandTotalAmount()
                );
                break;
        }
        $this->orderRepository->save($payment->getOrder());
    }
}
