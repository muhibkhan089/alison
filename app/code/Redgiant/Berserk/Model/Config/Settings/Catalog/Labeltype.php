<?php
namespace Redgiant\Berserk\Model\Config\Settings\Catalog;

class Labeltype implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Top Label')], 
            ['value' => 2, 'label' => __('Side Label')], 
            ['value' => 3, 'label' => __('Side Cornerd Label')]
        ];
    }

    public function toArray()
    {
        return [
            1 => __('Top Label'),
            2 => __('Side Label'),
            3 => __('Side Cornerd Label')
        ];
    }
}