<?php
/**
 */

namespace Redgiant\LayeredNavigation\Helper;

/**
 * Class Data
 * @package Redgiant\LayeredNavigation\Helper
 */
class Data extends \Redgiant\Core\Helper\Data
{
    const FILTER_TYPE_SLIDER = 'slider';
    const FILTER_TYPE_LIST   = 'list';
    const isAjax = true;

    /** @var \Redgiant\LayeredNavigation\Model\Layer\Filter */
    protected $filterModel;

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnabled($storeId = null)
    {
        return true && $this->isModuleOutputEnabled();
    }

    /**
     * @param $filters
     * @return mixed
     */
    public function getLayerConfiguration($filters)
    {
        $filterParams = $this->_getRequest()->getParams();
        foreach ($filterParams as $key => $param) {
            $filterParams[$key] = htmlspecialchars($param);
        }

        $config = new \Magento\Framework\DataObject([
            'active'             => array_keys($filterParams),
            'params'             => $filterParams,
            'isCustomerLoggedIn' => $this->_objectManager->create('Magento\Customer\Model\Session')->isLoggedIn(),
            'isAjax'             => $this->ajaxEnabled()
        ]);

        $this->getFilterModel()->getLayerConfiguration($filters, $config);

        return self::jsonEncode($config->getData());
    }

    /**
     * @return \Redgiant\LayeredNavigation\Model\Layer\Filter
     */
    public function getFilterModel()
    {
        if (!$this->filterModel) {
            $this->filterModel = $this->_objectManager->create('Redgiant\LayeredNavigation\Model\Layer\Filter');
        }

        return $this->filterModel;
    }

    public function ajaxEnabled() {
        return $this->getConfig('rg_layerednav/general/ajax_enable') && $this->isModuleOutputEnabled() ;
    }

    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }
}
