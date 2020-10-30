<?php
namespace Redgiant\Berserk\Model\Config\Settings\Installation;

class Demoversion implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('All')], 
            ['value' => 'demo01', 'label' => __('Demo 1 - Shop Clothes')], 
            ['value' => 'demo02', 'label' => __('Demo 2 - Shop Drone')], 
            ['value' => 'demo03', 'label' => __('Demo 3 - Shop Electronics')], 
            ['value' => 'demo04', 'label' => __('Demo 4 - Shop Modern')], 
            ['value' => 'demo05', 'label' => __('Demo 5 - Shop Trendy')], 
            ['value' => 'demo06', 'label' => __('Demo 6 - Shop Video')], 
            ['value' => 'demo07', 'label' => __('Demo 7 - Shop Sports')]
        ];
    }

    public function toArray()
    {
        return [
            '0' => __('All'), 
            'demo01' => __('Demo 1 - Shop Clothes'), 
            'demo02' => __('Demo 2 - Shop Drone'), 
            'demo03' => __('Demo 3 - Shop Electronics'), 
            'demo04' => __('Demo 4 - Shop Modern'), 
            'demo05' => __('Demo 5 - Shop Trendy'), 
            'demo06' => __('Demo 6 - Shop Video'), 
            'demo07' => __('Demo 7 - Shop Sports')
        ];
    }
}
