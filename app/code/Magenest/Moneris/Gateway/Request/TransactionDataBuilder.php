<?php
namespace Magenest\Moneris\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class TransactionDataBuilder
 * @package Magenest\Moneris\Gateway\Request
 */
class TransactionDataBuilder extends AbstractDataBuilder implements BuilderInterface
{

    const ORDER_ID = 'order_id';

    const AMOUNT = 'amount';

    const CRYPT_TYPE = 'crypt_type';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        $order = $paymentDO->getOrder();

        return [
            self::REPLACE_KEY => [
                self::ORDER_ID => $order->getOrderIncrementId(),
                self::CRYPT_TYPE => '7', //TODO change it
            ]
        ];
    }
}
