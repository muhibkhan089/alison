<?php
namespace Redgiant\Berserk\Model\Attribute;

class Berserkpagetype extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __('')], 
                ['value' => 'type1', 'label' => __('Type 1')], 
                ['value' => 'type2', 'label' => __('Type 2')]
            ];
        }
        
        return $this->_options;
    }
}
