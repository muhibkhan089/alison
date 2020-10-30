<?php
namespace Magenest\Moneris\Gateway\Http;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Framework\Xml\Generator;
use Magenest\Moneris\Model\Adminhtml\Source\Environment;
use Magenest\Moneris\Gateway\Request\AbstractDataBuilder;

/**
 * Class AbstractTransferFactory
 */
abstract class AbstractTransferFactory implements TransferFactoryInterface
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var TransferBuilder
     */
    protected $transferBuilder;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * Transaction Type
     *
     * @var string
     */
    private $action;

    /**
     * AbstractTransferFactory constructor.
     *
     * @param ConfigInterface $config
     * @param TransferBuilder $transferBuilder
     * @param Generator $generator
     * @param null $action
     */
    public function __construct(
        ConfigInterface $config,
        TransferBuilder $transferBuilder,
        Generator $generator,
        $action = null
    ) {
        $this->config = $config;
        $this->transferBuilder = $transferBuilder;
        $this->generator = $generator;
        $this->action = $action;
    }

    /**
     * @return null|string
     */
    private function getAction()
    {
        return $this->action;
    }

    /**
     * @return bool
     */
    protected function isUsCountry()
    {
        if ($this->config->getValue('environment') == Environment::ENVIRONMENT_US) {
            return true;
        }

        return false;
    }

    /**
     * Get request URL
     *
     * @param string $additionalPath
     * @return string
     */
    public function getUrl($additionalPath = '')
    {
        $prefix = (bool)$this->config->getValue('sandbox_flag') ? 'sandbox_' : '';
        $after = $this->isUsCountry() ? '_us' : '';
        $path = $prefix . 'moneris_gateway' . $after;
        $gateway = $this->config->getValue($path);
        if ($additionalPath == '') {
            $additionalPath = $this->config->getValue('moneris_path_servlet' . $after);
        }
        return trim($gateway) . $additionalPath;
    }

    /**
     * Convert to XML and replace some tags don't need
     *
     * @param array $request
     * @return string
     */
    protected function convertToXml($request)
    {
        if (isset($request[AbstractDataBuilder::REPLACE_KEY])) {
            $prefix = $this->isUsCountry() ? 'us_' : '';
            $action = $prefix . $this->getAction();

            $request[$action] = $request[AbstractDataBuilder::REPLACE_KEY];
            unset($request[AbstractDataBuilder::REPLACE_KEY]);
        }
        $request = ['request' => $request];
        $xml = $this->generator->arrayToXml($request);
        $xml = str_replace($this->listTagsNeedReplace(), '', $xml);

        return $xml;
    }

    /**
     * List Tags need removed
     *
     * @return array
     */
    public function listTagsNeedReplace()
    {
        return [
            '<items>',
            '</items>'
        ];
    }
}
