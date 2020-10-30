<?php
namespace Magenest\Moneris\Model\Order;

use \Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManager;
use Magento\Quote\Model\QuoteManagement;
use Psr\Log\LoggerInterface;

class Create
{

    /**
     * @var CheckoutSession $checkoutSession
     */
    private $checkoutSession;


    /**
     * @var StoreManager $storeManager
     */
    private $storeManager;


    /**
     * @var QuoteManagement $quoteManagement
     */
    private $quoteManagement;

    /**
     * Create constructor.
     * @param CheckoutSession $checkoutSession
     * @param StoreManager $storeManager
     * @param QuoteManagement $quoteManagement
     */

    public function __construct(
        CheckoutSession $checkoutSession,
        StoreManager $storeManager,
        QuoteManagement $quoteManagement
    ) {
    
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->quoteManagement = $quoteManagement;
    }

    /**
     * Return quote information
     * @return Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Return last order id
     * @return int
     */
    public function getLastOrderId()
    {
        $this->checkoutSession->getQuote()->reserveOrderId();
        return $this->checkoutSession->getQuote()->getReservedOrderId();
    }

    /**
     * @param $handlingSubject
     * @param $response
     * @return array
     * @throws \Exception
     */
    public function createOrder($handlingSubject, $response)
    {
        try {
            $message = 'Sorry, but something went wrong. Your order can\'t be created. Please contact with admin for further help.';
            $quote = $this->getQuote();
            $quote->getPayment()->importData(
                [
                    'method' => 'moneris_cc_vault',
                    'public_hash' => @$handlingSubject['public_hash']
                ]
            );

            $quote->collectTotals()->save();
            $this->ignoreAddressValidation($quote);
            $order = $this->quoteManagement->submit($quote);

            $order->setEmailSent(0);
            if ($order->getEntityId()) {
                $result['order_id']= $order->getRealOrderId();
                $this->checkoutSession->setLastQuoteId($quote->getId());
                $this->checkoutSession->setLastSuccessQuoteId($quote->getId());
                $this->checkoutSession->setLastOrderId($order->getId());
                $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
                $this->checkoutSession->setLastOrderStatus($order->getStatus());
            } else {
                $result=['error'=>1,'msg'=>$message];
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * Make sure addresses will be saved without validation errors
     *
     * @return void
     */
    private function ignoreAddressValidation(\Magento\Quote\Model\Quote $quote)
    {
        $quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$quote->getIsVirtual()) {
            $quote->getShippingAddress()->setShouldIgnoreValidation(true);
            if (!$quote->getBillingAddress()->getEmail()) {
                $quote->getBillingAddress()->setSameAsBilling(1);
            }
        }
    }
}
