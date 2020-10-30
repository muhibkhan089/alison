<?php

namespace Redgiant\Filterproducts\Block\Home;

class LatestList extends \Redgiant\Filterproducts\Block\Home\FilterlistBlock {
    
    public function getProducts() {
        $count = $this->getProductCount();
        $pagination_page = $this->getData("pagination_page");
        if (!$pagination_page)
            $pagination_page = 0;
        $category_id = $this->getData("category_id");
        $this->setData("ajax_url","redgiant_filterproducts/home/latestlist");
        $collection = $this->_productCollectionFactory->create();
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP);
        $offset = $pagination_page * $count;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if(!$category_id) {
            $category_id = $this->_helper->getRootCategoryId();
        }

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
                ->addCategoryFilter($category)
                ->addAttributeToSort('created_at','desc');
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
                ->addAttributeToFilter('is_saleable', 1, 'left')
                ->addAttributeToSort('created_at','desc');
        }
        
        $collection->getSelect()
                ->order('created_at','desc')
                ->limit($count, $offset);
                
        return $collection;
    }

    public function getLoadedProductCollection() {
        return $this->getProducts();
    }

    public function getProductCount() {
        $limit = $this->getData("product_per_page");
        if (!$limit)
            $limit = $this->getData("product_count");
        if (!$limit)
            $limit = 8;
        return $limit;
    }
}
