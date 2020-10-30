<?php
/**
 */

namespace Redgiant\LayeredNavigation\Plugin\Model\Adapter;

use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Preprocessor
 * @package Redgiant\LayeredNavigation\Model\Plugin\Adapter
 */
class Preprocessor
{
    /**
     * @type \Redgiant\LayeredNavigation\Helper\Data
     */
    protected $_moduleHelper;

    /**
     * @type \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Preprocessor constructor.
     * @param \Redgiant\LayeredNavigation\Helper\Data $moduleHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Redgiant\LayeredNavigation\Helper\Data $moduleHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_moduleHelper = $moduleHelper;
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor $subject
     * @param \Closure $proceed
     * @param $filter
     * @param $isNegation
     * @param $query
     * @return string
     */
    public function aroundProcess(\Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor $subject, \Closure $proceed, $filter, $isNegation, $query)
    {
        $productMetadata = $this->objectManager->get(ProductMetadataInterface::class);
        $version         = $productMetadata->getVersion(); //will return the magento version

        if ($this->_moduleHelper->isEnabled() && ($filter->getField() === 'category_ids')) {
            if (version_compare($version, '2.1.13', '>=') && version_compare($version, '2.1.15', '<=')) {
                return 'category_products_index.category_id IN (' . $filter->getValue() . ')';
            }

            return 'category_ids_index.category_id IN (' . $filter->getValue() . ')';
        }

        return $proceed($filter, $isNegation, $query);
    }
}
