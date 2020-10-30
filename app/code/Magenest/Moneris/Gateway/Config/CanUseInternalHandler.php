<?php
namespace Magenest\Moneris\Gateway\Config;

use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\ConfigInterface;
use Magenest\Moneris\Model\Adminhtml\Source\ConnectionType;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;

/**
 * Class CanUseInternalHandler
 */
class CanUseInternalHandler implements ValueHandlerInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $subject, $storeId = null)
    {
        switch ($this->config->getValue('connection_type', $storeId)) {
            case ConnectionType::CONNECTION_TYPE_DIRECT:
                return 1;
            default:
                return 0;
        }
    }
}
