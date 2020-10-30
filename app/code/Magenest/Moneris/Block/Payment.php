<?php
namespace Magenest\Moneris\Block;

use Magento\Payment\Block\Form;
use Magento\Payment\Model\Config;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magenest\Moneris\Model\Adminhtml\Source\ConnectionType;

/**
 * Class Payment
 */
class Payment extends Template
{
    const MONERIS_CODE = 'moneris';
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getPaymentConfig()
    {
        return json_encode(
            [
                'code' => self::MONERIS_CODE,
            ],
            JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * @return string
     */
    public function getConnectionType()
    {
        return ConnectionType::CONNECTION_TYPE_DIRECT;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return self::MONERIS_CODE;
    }

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        if ($this->config->getValue('connection_type') !== ConnectionType::CONNECTION_TYPE_DIRECT) {
            return '';
        }

        return parent::toHtml();
    }
}
