<?php
namespace Magenest\Moneris\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Environment
 * @package Magenest\Moneris\Model\Adminhtml\Source
 */
class Environment implements ArrayInterface
{
    const ENVIRONMENT_CA = 'CA';
    const ENVIRONMENT_US = 'US';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENVIRONMENT_CA,
                'label' => 'Canada',
            ],
            [
                'value' => self::ENVIRONMENT_US,
                'label' => 'United State'
            ],
        ];
    }
}
