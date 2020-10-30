<?php
namespace Magenest\Moneris\Model\Config;

use Magento\Backend\App\ConfigInterface;
use Magento\Backend\Block\Template\Context;

class Config extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var ConfigInterface
     */
    protected $_config;


    /**
     * @param Context $context
     * @param ConfigInterface $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        array $data = array()
    ) {
        $this->_config=$config;
        parent::__construct($context, $data);
    }

    /**
     * create element for Access token field in store configuration
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        $field_config = $element->getData('field_config');
        $id = $field_config['id'];
        if (!($id = $field_config['id'])) {
            throw new \Exception('Element Id not found!');
        }
        $approved_url = $this->_urlBuilder->getBaseUrl().'moneris/payment/complete';
        $declined_url = $this->_urlBuilder->getBaseUrl().'moneris/order/cancel';
        $response_url = $this->_urlBuilder->getBaseUrl().'moneris/payment/completeus';
        switch ($id) {
            case 'approved_url':
                $element->addData([
                    'value' => $approved_url
                ]);
                break;
            case 'declined_url':
                $element->addData([
                    'value' => $declined_url
                ]);
                break;
            case 'response_url':
                $element->addData([
                    'value' => $response_url
                ]);
                break;
            case 'cancel_url':
                $element->addData([
                    'value' => $declined_url
                ]);
                break;
            default:
                $element->addData([
                    'value' => $response_url
                ]);
                break;
        }
        return parent::_renderValue($element);
    }
}
