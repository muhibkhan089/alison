<?php
namespace Magenest\Moneris\Gateway\Response;

use Magenest\Moneris\Model\Adminhtml\Source\OrderHandlerAction;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Model\Config;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\ConfigInterface;

class CVDHandler implements HandlerInterface
{
    const XML_PATH_CVD_ENABLE = 'cvd_enable';
    const XML_PATH_CVD_FAIL = 'cvd_fail';
    const XML_PATH_CVD_NULL = 'cvd_null';

    const CVD_FAIL_CODE = ['1N','1D'];
    const CVD_NULL_CODE = ['1P','1S','1U','Other'];
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * CVDHandler constructor.
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        if ($this->config->getValue(self::XML_PATH_CVD_ENABLE)) {
            if (isset($response['CvdResultCode']) && $response['CvdResultCode'] != 'null') {
                //CVD was Verified
                $payment->setAdditionalInformation(
                    'cvd_response_code',
                    $response['CvdResultCode']
                );

                //FAIL
                if (in_array($response['CvdResultCode'], self::CVD_FAIL_CODE)) {
                    $this->doOrderAction($this->config->getValue(self::XML_PATH_CVD_FAIL), $payment);
                    return;
                }

                //NULL
                if (in_array($response['CvdResultCode'], self::CVD_NULL_CODE)) {
                    $this->doOrderAction($this->config->getValue(self::XML_PATH_CVD_NULL), $payment);
                    return;
                }
            } else {
                //CVD was NOT Verified
                $payment->setAdditionalInformation(
                    'cvd_response_code',
                    'null'
                );
                $this->doOrderAction($this->config->getValue(self::XML_PATH_CVD_NULL), $payment);
                return;
            }
        }
    }

    /**
     * @param $action
     * @param $payment
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function doOrderAction($action, $payment)
    {
        switch ($action) {
            case OrderHandlerAction::ORDER_ACTION_CANCEL:
                $payment->setIsTransactionClosed(true);
                $payment->setAdditionalInformation('order_action', OrderHandlerAction::ORDER_ACTION_CANCEL);
                $payment->setAdditionalInformation('order_action_handler_code', OrderHandlerAction::ORDER_ACTION_CVD_HANDLER);
                throw new \Magento\Framework\Exception\LocalizedException(__('Your CVD is not valid! Please check your payment information.'));
            case OrderHandlerAction::ORDER_ACTION_HOLD:
                $payment->setIsTransactionClosed(false);
                $payment->setAdditionalInformation('order_action', OrderHandlerAction::ORDER_ACTION_HOLD);
                $payment->setAdditionalInformation('order_action_handler_code', OrderHandlerAction::ORDER_ACTION_CVD_HANDLER);
                return;
            default:
                return;
        }
    }
}
