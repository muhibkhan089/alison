<?php
namespace Magenest\Moneris\Model\Ui\Direct;

use Magenest\Moneris\Block\Payment;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magenest\Moneris\Model\Adminhtml\Source\Environment;
use Magento\Customer\Model\Session;

/**
 * Class ConfigProvider
 */
final class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ConfigInterface
     */
    private $vaultConfig;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * Constructor
     *
     * @param ConfigInterface $config
     * @param ConfigInterface $vaultConfig
     * @param UrlInterface $urlBuilder
     * @param Session $customerSession
     */
    public function __construct(
        ConfigInterface $config,
        ConfigInterface $vaultConfig,
        UrlInterface $urlBuilder,
        Session $customerSession
    ) {
    

        $this->config = $config;
        $this->vaultConfig = $vaultConfig;
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                Payment::MONERIS_CODE => [
                    'connectionType' => $this->config->getValue('connection_type'),
                    'cvd' => $this->config->getValue('cvd_enable'),
                    'orderCancelUrl' => $this->urlBuilder->getUrl(
                        'moneris/order/cancel',
                        ['_secure' => true]
                    ),
                    'paymentUrl' => $this->getPaymentUrl(),
                    'hppid' => $this->config->getValue('hpp_id'),
                    'hppkey' => $this->config->getValue('hpp_key'),
                    'isUSCountry' => $this->isUsCountry(),
                    'getOrderData' => $this->urlBuilder->getUrl(
                        'moneris/order/getorderdata',
                        ['_secure'=> true]
                    ),
                    'getKeyData' => $this->urlBuilder->getUrl(
                        'moneris/key/getkeydata',
                        ['_secure'=> true]
                    ),
                    'isValid' => $this->checkStoreInfo(),
                    'isLoggedIn' => $this->isLoggedIn(),
                    'isVaultEnabled' => $this->vaultConfig->getValue('active'),
                ]
            ]
        ];
    }

    public function getStoreInfo()
    {
        return array(
            $this->config->getValue('store_id'),
            $this->config->getValue('api_token')
        );
    }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getApiUrl()
    {
        $prefix = (bool)$this->config->getValue('sandbox_flag') ? 'sandbox_' : '';
        $after = $this->isUsCountry()  ? '_us' : '';
        $path = $prefix . 'moneris_gateway' . $after;
        $gateway = $this->config->getValue($path);

        $apiRequest = 'moneris_path_servlet'.$after;
        $apiUrl = $this->config->getValue($apiRequest);

        return trim($gateway) . $apiUrl;
    }

    public function getPaymentUrl()
    {
        $prefix = (bool)$this->config->getValue('sandbox_flag') ? 'sandbox_' : '';
        $after = $this->isUsCountry()  ? '_us' : '';
        $path = $prefix . 'moneris_gateway' . $after;
        $gateway = $this->config->getValue($path);
        $preload_request = 'moneris_path_preload_request'.$after;
        $additionalPath = $this->config->getValue($preload_request);
        return trim($gateway) . $additionalPath;
    }

    public function checkStoreInfo()
    {
        $httpHeaders = new \Zend\Http\Headers();
        $httpHeaders->addHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);

        $request = new \Zend\Http\Request();
        $request->setHeaders($httpHeaders);
        $request->setUri($this->getPaymentUrl());
        $request->setMethod(\Zend\Http\Request::METHOD_POST);

        $params = new \Zend\Stdlib\Parameters([
            'hpp_id' => $this->config->getValue('hpp_id'),
            'hpp_key' => $this->config->getValue('hpp_key')
        ]);
        $request->setQuery($params);

        $client = new \Zend\Http\Client();
        $options = [
            'adapter'   => 'Zend\Http\Client\Adapter\Curl',
            'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
            'maxredirects' => 0,
            'timeout' => 30
        ];
        $client->setOptions($options);

        $response = $client->send($request);
        if ($response->getContent() == 'failed' || $response->getContent() == 'Invalid store credentials.') {
            return false;
        }
        return true;
    }

    public function isUsCountry()
    {
        if ($this->config->getValue('environment') == Environment::ENVIRONMENT_US) {
            return true;
        }

        return false;
    }
}
