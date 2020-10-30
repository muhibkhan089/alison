<?php

namespace Magenest\Moneris\Gateway\Command\Net;

use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magenest\Moneris\Gateway\Helper\ResponseReader;
use Magento\Payment\Gateway\ConfigInterface;

/**
 * Class UpdateDetailsCommand
 */
class UpdateDetailsCommand implements CommandInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var HandlerInterface
     */
    private $handler;
    /**
     * @var ValidatorInterface
     */
    private $validatorUS;

    /**
     * @var HandlerInterface
     */
    private $handlerUS;

    /**
     * UpdateDetailsCommand constructor.
     * @param ConfigInterface $config
     * @param ValidatorInterface $validator
     * @param HandlerInterface $handler
     */
    public function __construct(
        ConfigInterface $config,
        ValidatorInterface $validator,
        HandlerInterface $handler,
        ValidatorInterface $validatorUS,
        HandlerInterface $handlerUS
    ) {
        $this->config = $config;
        $this->validator = $validator;
        $this->handler = $handler;
        $this->validatorUS = $validatorUS;
        $this->handlerUS = $handlerUS;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $commandSubject)
    {
        $paymentDO = SubjectReader::readPayment($commandSubject);
        $response = ResponseReader::readResponse($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);
        if ($this->isUsCountry() == 1) {
            if ($this->validatorUS) {
                $result = $this->validatorUS->validate(
                    [
                        'payment' => $paymentDO,
                        'response' => $response
                    ]
                );
                if (!$result->isValid()) {
                    throw new CommandException(
                        __(implode("\n", $result->getFailsDescription()))
                    );
                }
            }

            if ($this->handlerUS) {
                $this->handlerUS->handle($commandSubject, $response);
            }
        } else {
            if ($this->validator) {
                $result = $this->validator->validate(
                    [
                        'payment' => $paymentDO,
                        'response' => $response
                    ]
                );
                if (!$result->isValid()) {
                    throw new CommandException(
                        __(implode("\n", $result->getFailsDescription()))
                    );
                }
            }

            if ($this->handler) {
                $this->handler->handle($commandSubject, $response);
            }
        }
    }

    public function isUsCountry()
    {
        if ($this->config->getValue('environment') == 'US') {
            return true;
        }

        return false;
    }
}
