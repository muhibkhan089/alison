<?php
/**

 */

namespace Redgiant\LayeredNavigation\Model\Search;

use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder as SourceSearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Redgiant\LayeredNavigation\Helper\Data as LayerHelper;

/**
 * Builder for SearchCriteria Service Data Object
 */
class SearchCriteriaBuilder extends SourceSearchCriteriaBuilder
{
    /**
     * @var \Redgiant\LayeredNavigation\Helper\Data as LayerHelper;
     */
    protected $helper;

    /**
     * SearchCriteriaBuilder constructor.
     * @param LayerHelper $helper
     * @param ObjectFactory $objectFactory
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        LayerHelper $helper,
        ObjectFactory $objectFactory,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder
    )
    {
        $this->helper = $helper;
        parent::__construct($objectFactory, $filterGroupBuilder, $sortOrderBuilder);
    }

    /**
     * @param $attributeCode
     *
     * @return $this
     */
    public function removeFilter($attributeCode)
    {
        $this->filterGroupBuilder->removeFilter($attributeCode);

        return $this;
    }

    /**
     * @return SearchCriteriaBuilder
     */
    public function cloneObject()
    {
        $cloneObject = clone $this;
        $cloneObject->setFilterGroupBuilder($this->filterGroupBuilder->cloneObject());

        return $cloneObject;
    }

    /**
     * @param $filterGroupBuilder
     */
    public function setFilterGroupBuilder($filterGroupBuilder)
    {
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * Return the Data type class name
     *
     * @return string
     */
    protected function _getDataObjectType()
    {
        return 'Magento\Framework\Api\Search\SearchCriteria';
    }
}
