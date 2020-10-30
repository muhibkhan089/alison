<?php
namespace Redgiant\Berserk\Helper;

use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_objectManager;
    private $_registry;
    protected $_filterProvider;
    private $_messageManager;
    protected $_configFactory;
    private $_checkedPurchaseCode;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory,
        Registry $registry
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_filterProvider = $filterProvider;
        $this->_registry = $registry;
        $this->_messageManager = $messageManager;
        $this->_configFactory = $configFactory;
        
        parent::__construct($context);
    }

    public function isAdmin() {
        $om = \Magento\Framework\App\ObjectManager::getInstance(); 
        $app_state = $om->get('\Magento\Framework\App\State');
        $area_code = $app_state->getAreaCode();
        if($area_code == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getBaseLinkUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getModel($model) {
        return $this->_objectManager->create($model);
    }

    public function getRootCategoryId() {
        return $this->_storeManager->getStore()->getRootCategoryId();
    }

    public function getCurrentStore() {
        return $this->_storeManager->getStore();
    }

    public function filterContent($content) {
        return $this->_filterProvider->getPageFilter()->filter($content);
    }

    public function getCategoryProductIds($current_category) {
        $category_products = $current_category->getProductCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_saleable', 1, 'left')
            ->addAttributeToSort('position','asc');
        $cat_prod_ids = $category_products->getAllIds();
        
        return $cat_prod_ids;
    }

    public function getPrevProduct($product) {
        $current_category = $product->getCategory();
        if(!$current_category) {
            foreach($product->getCategoryCollection() as $parent_cat) {
                $current_category = $parent_cat;
            }
        }
        if(!$current_category)
            return false;
        $cat_prod_ids = $this->getCategoryProductIds($current_category);
        $_pos = array_search($product->getId(), $cat_prod_ids);
        if (isset($cat_prod_ids[$_pos - 1])) {
            $prev_product = $this->getModel('Magento\Catalog\Model\Product')->load($cat_prod_ids[$_pos - 1]);
            return $prev_product;
        }
        return false;
    }

    public function getNextProduct($product) {
        $current_category = $product->getCategory();
        if(!$current_category) {
            foreach($product->getCategoryCollection() as $parent_cat) {
                $current_category = $parent_cat;
            }
        }
        if(!$current_category)
            return false;
        $cat_prod_ids = $this->getCategoryProductIds($current_category);
        $_pos = array_search($product->getId(), $cat_prod_ids);
        if (isset($cat_prod_ids[$_pos + 1])) {
            $next_product = $this->getModel('Magento\Catalog\Model\Product')->load($cat_prod_ids[$_pos + 1]);
            return $next_product;
        }
        return false;
    }

    public function getCurrentProduct() {
        return $this->_registry->registry('current_product');
    }
    public function getColClass($perrow = null) {
        if(!$perrow) {
            $perrow = $this->getConfig('berserk_settings/catalog/product_per_row');
        }

        switch($perrow){
            case 2:
                return 'col-12 col-md-6 col-xl-6';
                break;
            case 3:
                return 'col-12 col-md-6 col-xl-4';
                break;
            case 4:
                return 'col-12 col-md-6 col-xl-3';
                break;
            case 5:
                return 'col-12 col-md-6 col-xl-3-custom';
                break;
            case 6:
                return 'col-12 col-md-6 col-xl-2';
                break;
        }
        
        return;
    }
}