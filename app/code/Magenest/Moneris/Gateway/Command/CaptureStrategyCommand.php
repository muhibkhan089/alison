<?php
namespace Magenest\Moneris\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order;

class CaptureStrategyCommand implements CommandInterface
{
    /**
     * Moneris Direct sale command
     */
    const SALE = 'sale';

    /**
     * Moneris Direct capture command
     */
    const PRE_AUTH_CAPTURE = 'pre_auth_capture';

    /**
     * Moneris Vault Capture Command
     */
    const VAULT_CAPTURE = 'vault_capture';

    /**
     * @var Command\CommandPoolInterface
     */
    private $commandPool;

    /**
     * @param Command\CommandPoolInterface $commandPool
     */
    public function __construct(
        Command\CommandPoolInterface $commandPool
    ) {
        $this->commandPool = $commandPool;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $commandSubject)
    {
        /** @var PaymentDataObjectInterface $paymentObject */
        $paymentObject = SubjectReader::readPayment($commandSubject);

        /** @var Order\Payment $payment */
        $payment = $paymentObject->getPayment();
        ContextHelper::assertOrderPayment($payment);

        if ($payment instanceof Order\Payment
            && $payment->getAuthorizationTransaction()
        ) {
            return $this->commandPool
                ->get(self::PRE_AUTH_CAPTURE)
                ->execute($commandSubject);
        }

        if ($payment->getAdditionalInformation('public_hash')) {
            return $this->commandPool
                ->get(self::VAULT_CAPTURE)
                ->execute($commandSubject);
        }

        return $this->commandPool
            ->get(self::SALE)
            ->execute($commandSubject);
    }
}
