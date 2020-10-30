<?php
namespace Magenest\Moneris\Gateway\Request;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Vault\Model\ResourceModel\PaymentToken\CollectionFactory;

/**
 * Class TransactionDataBuilder
 * @package Magenest\Moneris\Gateway\Request
 */
class VaultDetailsDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    const ACTION = 'res_purchase_cc';

    const DATA_KEY = 'data_key';

    protected $paymentTokenCollectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->paymentTokenCollectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        /** @var PaymentDataObjectInterface $paymentObject */
        $paymentObject = SubjectReader::readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $paymentObject->getPayment();
        $order = $payment->getOrder();

        if (!$order->getCustomerId()) {
            throw new LocalizedException(__('Could not find customer ID'));
        }
        /** @var PaymentToken $token */
        $token = $this->paymentTokenCollectionFactory->create()
            ->addFieldToFilter('customer_id', $order->getCustomerId())
            ->addFieldToFilter('public_hash', $payment->getAdditionalInformation('public_hash'))
            ->getFirstItem();
        if (!$token->getGatewayToken()) {
            throw new LocalizedException(__('Could not find token for this card. Please use a new one.'));
        }
        return [
            self::REPLACE_KEY => [
                self::DATA_KEY => $token->getGatewayToken(),
                GetKeyDataBuilder::CUST_ID => $order->getCustomerId()
            ]
        ];
    }
}
