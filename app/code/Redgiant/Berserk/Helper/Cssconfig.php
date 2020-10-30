<?php
namespace Redgiant\Berserk\Helper;

class Cssconfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_storeManager;
    protected $generatedCssFolder;
    protected $generatedCssPath;
    protected $generatedCssDir;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        
        $base = BP;
        
        $this->generatedCssFolder = 'berserk/configed_css/';
        $this->generatedCssPath = 'pub/media/'.$this->generatedCssFolder;
        $this->generatedCssDir = $base.'/'.$this->generatedCssPath;
        
        parent::__construct($context);
    }

    public function convertRGB($hex_color) {
        list($r, $g, $b) = sscanf($hex_color, "#%02x%02x%02x");
        return "$r,$g,$b";
    }
    
    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    
    public function getCssConfigDir()
    {
        return $this->generatedCssDir;
    }
    
    public function getSettingsFile()
    {
        return $this->getBaseMediaUrl(). $this->generatedCssFolder . 'settings_' . $this->_storeManager->getStore()->getCode() . '.css';
    }
    
    public function getDesignFile()
    {
        return $this->getBaseMediaUrl(). $this->generatedCssFolder . 'design_' . $this->_storeManager->getStore()->getCode() . '.css';
    }
    
    public function getBerserkWebDir()
    {
        return $this->getBaseMediaUrl().'berserk/web/';
    }
}
