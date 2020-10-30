<?php


namespace Esparksinc\Consult\Model\ResourceModel\Query;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'query_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'Esparksinc\Consult\Model\Query',
            'Esparksinc\Consult\Model\ResourceModel\Query'
        );
    }
}
