<?php
namespace Redgiant\Berserk\Model\Config\Settings\Product;

class Imagesize implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 3, 'label' => __('3/12')], 
            ['value' => 6, 'label' => __('6/12')], 
            ['value' => 9, 'label' => __('9/12')]
        ];
    }

    public function toArray()
    {
        return [
            3 => __('3/12'),
            6 => __('6/12'),
            9 => __('9/12')
        ];
    }
}