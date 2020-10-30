<?php
namespace Magenest\Moneris\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ConnectionType
 * @package Magenest\Moneris\Model\Adminhtml\Source
 */
class ConnectionType implements ArrayInterface
{
    const CONNECTION_TYPE_DIRECT = 'direct';
    const CONNECTION_TYPE_SHARED = 'shared';
    const CONNECTION_TYPE_REDIRECT = 'redirect';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CONNECTION_TYPE_DIRECT,
                'label' => 'Direct connection',
            ],
            [
                'value' => self::CONNECTION_TYPE_REDIRECT,
                'label' => 'Redirect connection',
            ]
        ];
    }
}
