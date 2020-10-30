<?php

namespace Redgiant\Filterproducts\Block\Home;

class FeaturedList extends \Redgiant\Filterproducts\Block\Home\FilterlistBlock {

    public function getProducts() {
        $count = $this->getProductCount();                       
        $category_id = $this->getData("category_id");
        $collection = $this->_productCollectionFactory->create();
        $collection->clear()->getSelect()->reset(\Magento\Framework\DB\Select::WHERE)->reset(\Magento\Framework\DB\Select::ORDER)->reset(\Magento\Framework\DB\Select::LIMIT_COUNT)->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET)->reset(\Magento\Framework\DB\Select::GROUP);
        
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
                ->addAttributeToFilter('rg_featured', 1, 'left')
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
                ->addAttributeToFilter('is_saleable', 1, 'left')
                ->addAttributeToFilter('rg_featured', 1, 'left');
        }

        $collection->getSelect()
            ->order('rand()')
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
