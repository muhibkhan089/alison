<?php

namespace Redgiant\Filterproducts\Block\Home;


class BestsellersList extends \Redgiant\Filterproducts\Block\Home\FilterlistBlock {
    
    public function getProducts() {
        $count = $this->getProductCount();
        $category_id = $this->getData("category_id");
        $collection = $this->_productCollectionFactory->create();
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP)->reset(\Magento\Framework\DB\Select::COLUMNS)->reset('from');
        $connection  = $this->_resource->getConnection();
        $collection->getSelect()->join(['e' => $connection->getTableName('catalog_product_entity')],'');

        if(!$category_id) {
            $category_id = $this->_helper->getRootCategoryId();
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $objectManager->create('Magento\Catalog\Model\Category')->load($category_id);
        if(isset($category) && $category) {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect('*')
                ->addUrlRewrite()
                ->addAttributeToFilter('is_saleable', 1, 'left')
                ->addCategoryFilter($category);
        } else {
            $collection->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('image')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('thumbnail')
                ->addAttributeToSelect('*')
                ->addUrlRewrite()
                ->addAttributeToFilter('is_saleable', 1, 'left');
        }
        
        $collection->getSelect()
            ->joinLeft(['soi' => $connection->getTableName('sales_order_item')], 'soi.product_id = e.entity_id', ['SUM(soi.qty_ordered) AS ordered_qty'])
            ->join(['order' => $connection->getTableName('sales_order')], "order.entity_id = soi.order_id",['order.state'])
            ->where("order.state <> 'canceled' and soi.parent_item_id IS NULL AND soi.product_id IS NOT NULL")
            ->group('soi.product_id')
            ->order('ordered_qty DESC')
            ->limit($count);
        
        return $collection;
    }

    public function getLoadedProductCollection() {
        return $this->getProducts();
    }

    public function getProductCount() {
        $limit = $this->getData("product_count");
        if(!$limit)
            $limit = 10;
        return $limit;
    } 
}
