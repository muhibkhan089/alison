<?php
namespace Magenest\Moneris\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order;

class AuthorizeStrategyCommand implements CommandInterface
{
    /**
     * Moneris pre authorize command
     */
    const PRE_AUTH = 'pre_auth';

    /**
     * Moneris Vault Capture Command
     */
    const VAULT_AUTHORIZE = 'vault_authorize';

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

        if ($payment->getAdditionalInformation('public_hash')) {
            return $this->commandPool
                ->get(self::VAULT_AUTHORIZE)
                ->execute($commandSubject);
        }

        return $this->commandPool
            ->get(self::PRE_AUTH)
            ->execute($commandSubject);
    }
}
