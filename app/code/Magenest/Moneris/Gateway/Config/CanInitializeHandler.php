<?php
namespace Magenest\Moneris\Gateway\Config;

use Magenest\Moneris\Model\Adminhtml\Source\ConnectionType;
use Magento\Payment\Gateway\Config\ValueHandlerInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Sales\Model\Order\Payment;

/**
 * Class CanInitializeHandler
 */
class CanInitializeHandler implements ValueHandlerInterface
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
                return 0;
            default:
                return 1;
        }
    }
}
