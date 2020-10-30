<?php
namespace Magenest\Moneris\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AbstractDataBuilder
 * @package Magenest\Moneris\Gateway\Request
 */
class MerchantDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * Store ID
     */
    const STORE_ID = 'store_id';

    /**
     * Api Token
     */
    const API_TOKEN = 'api_token';

    private $config;

    /**
     * AbstractDataBuilder constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        return [
            self::STORE_ID => $this->config->getValue(self::STORE_ID),
            self::API_TOKEN => $this->config->getValue(self::API_TOKEN)
        ];
    }
}
