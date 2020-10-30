<?php
namespace Redgiant\Berserk\Model\Config\Settings\Catalog;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('No Layout Update')], 
            ['value' => '1column', 'label' => __('1 column')], 
            ['value' => '2columns-left', 'label' => __('2 columns with left bar')], 
            ['value' => '2columns-right', 'label' => __('2 columns with right bar')], 
            ['value' => '3columns', 'label' => __('3 columns')], 
            ['value' => 'empty', 'label' => __('Empty')]
        ];
    }

    public function toArray()
    {
        return [
            '' => __('No Layout Update'),
            '1column' => __('1 column'),
            '2columns-left' => __('2 columns with left bar'),
            '2columns-right' => __('2 columns with right bar'),
            '3columns' => __('3 columns'),
            'empty' => __('Empty')
        ];
    }
}