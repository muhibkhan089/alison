<?php 
namespace Esparksinc\Consult\Model\Config\Source;
  
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
  
class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{ 
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [ 
            ['label'=>'', 'value'=>''],
            ['label'=>'Alberta', 'value'=>'alberta'],
            ['label'=>'British Columbia', 'value'=>'britishColumbia'],
            ['label'=>'Manitoba', 'value'=>'manitoba'],
            ['label'=>'Nova Scotia', 'value'=>'novaScotia'],
            ['label'=>'Ontario', 'value'=>'ontario'],
            ['label'=>'Yukon', 'value'=>'yukon'],
            ['label'=>'New Brunswick', 'value'=>'newBrunswick'],
            ['label'=>'Newfoundland & Labrador', 'value'=>'newfoundland'],
            ['label'=>'Northwest Territories', 'value'=>'northwestTerritories'],
            ['label'=>'Prince Edward Island', 'value'=>'princeEdward'],
            ['label'=>'Quebec', 'value'=>'quebec'],
            ['label'=>'Saskatchewan', 'value'=>'saskatchewan'],
            ['label'=>'Nunavut', 'value'=>'nunavut'],
        ];
        return $this->_options;
    }
  
    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->getAllOptions() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}