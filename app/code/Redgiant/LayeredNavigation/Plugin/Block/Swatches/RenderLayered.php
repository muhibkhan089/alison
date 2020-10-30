<?php
/**
 */

namespace Redgiant\LayeredNavigation\Plugin\Block\Swatches;

/**
 * Class RenderLayered
 * @package Redgiant\LayeredNavigation\Block\Plugin\Swatches
 */
class RenderLayered
{
    /** @var \Magento\Framework\UrlInterface */
    protected $_url;

    /** @var \Magento\Theme\Block\Html\Pager */
    protected $_htmlPagerBlock;

    /** @var \Redgiant\LayeredNavigation\Helper\Data */
    protected $_moduleHelper;

    /** @type \Magento\Catalog\Model\Layer\Filter\AbstractFilter */
    protected $filter;

    /**
     * RenderLayered constructor.
     *
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Theme\Block\Html\Pager $htmlPagerBlock
     * @param \Redgiant\LayeredNavigation\Helper\Data $moduleHelper
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Theme\Block\Html\Pager $htmlPagerBlock,
        \Redgiant\LayeredNavigation\Helper\Data $moduleHelper
    )
    {
        $this->_url            = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        $this->_moduleHelper   = $moduleHelper;
    }

    /**
     * @param \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return array
     */
    public function beforeSetSwatchFilter(\Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject, \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter)
    {
        $this->filter = $filter;

        return [$filter];
    }

    /**
     * @param \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject
     * @param $proceed
     * @param $attributeCode
     * @param $optionId
     *
     * @return string
     */
    public function aroundBuildUrl(
        \Magento\Swatches\Block\LayeredNavigation\RenderLayered $subject,
        $proceed,
        $attributeCode,
        $optionId
    )
    {
        if (!$this->_moduleHelper->isEnabled()) {
            return $proceed($attributeCode, $optionId);
        }

        $attHelper = $this->_moduleHelper->getFilterModel();
        if ($attHelper->isMultiple($this->filter)) {
            $value = $attHelper->getFilterValue($this->filter);

            if (!in_array($optionId, $value)) {
                $value[] = $optionId;
            } else {
                $key = array_search($optionId, $value);
                if ($key !== false) {
                    unset($value[$key]);
                }
            }
        } else {
            $value = [$optionId];
        }

        //Sort param on Url
        sort($value);

        $query = !empty($value) ? [$attributeCode => implode(',', $value)] : '';

        return $this->_url->getUrl(
            '*/*/*',
            ['_current' => true, '_use_rewrite' => true, '_query' => $query]
        );
    }
}
