<?php
namespace Redgiant\Dailydeals\Model\ResourceModel\Dailydeal;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'dailydeal_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'rg_dailydeals_dailydeal_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'dailydeal_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Redgiant\Dailydeals\Model\Dailydeal', 'Redgiant\Dailydeals\Model\ResourceModel\Dailydeal');
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'dailydeal_id', $labelField = 'rg_product_sku', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
