<?php
namespace Redgiant\Berserk\Model\Config\Settings\Catalog;

class Gridstyle implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'type1', 'label' => __('Style 1')], 
            ['value' => 'type2', 'label' => __('Style 2')], 
            ['value' => 'type3', 'label' => __('Style 3')], 
            ['value' => 'type4', 'label' => __('Style 4')],
            ['value' => 'type5', 'label' => __('Style 5')]
        ];
    }

    public function toArray()
    {
        return [
            'type1' => __('Style 1'),
            'type2' => __('Style 2'),
            'type3' => __('Style 3'),
            'type4' => __('Style 4'),
            'type5' => __('Style 5')
        ];
    }
}