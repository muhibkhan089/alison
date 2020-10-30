<?php
namespace Redgiant\Core\Helper;

use Magento\Framework\Registry;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_objectManager;
    private $_registry;
    protected $_filterProvider;
    protected $_configFactory;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configFactory,
        Registry $registry
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_filterProvider = $filterProvider;
        $this->_registry = $registry;
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
        
    public function getBaseUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    
    public function getBaseLinkUrl() {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
    
    public function getConfig($config_path) {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getModel($model) {
        return $this->_objectManager->create($model);
    }
    
    public function getCurrentStore() {
        return $this->_storeManager->getStore();
    }
    
    public function filterContent($content) {
        return $this->_filterProvider->getPageFilter()->filter($content);
    }


    /***************For Ajax Layer***************/

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @return string
     */
    public static function jsonEncode($valueToEncode)
    {
        try {
            $encodeValue = self::getJsonHelper()->jsonEncode($valueToEncode);
        } catch (\Exception $e) {
            $encodeValue = '{}';
        }

        return $encodeValue;
    }
    /**
     * Decodes the given $encodedValue string which is
     * encoded in the JSON format
     *
     * @param string $encodedValue
     * @return mixed
     */
    public static function jsonDecode($encodedValue)
    {
        try {
            $decodeValue = self::getJsonHelper()->jsonDecode($encodedValue);
        } catch (\Exception $e) {
            $decodeValue = [];
        }

        return $decodeValue;
    }
    /**
     * @return \Magento\Framework\Json\Helper\Data|mixed
     */
    public static function getJsonHelper()
    {
        return ObjectManager::getInstance()->get(JsonHelper::class);
    }
}
