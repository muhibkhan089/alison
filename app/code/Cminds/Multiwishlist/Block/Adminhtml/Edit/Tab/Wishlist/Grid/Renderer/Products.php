<?php

namespace Cminds\Multiwishlist\Block\Adminhtml\Edit\Tab\Wishlist\Grid\Renderer;

class Products extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        /** @var \Magento\Wishlist\Model\Wishlist $row */
        $result = '<ul>';
        /** @var \Magento\Wishlist\Model\Item $item */
        foreach ($row->getItemCollection() as $item) {
            $result .= "<li>{$item->getProduct()->getName()}</li>";
        }
        $result .= '</ul>';
        return $result;
    }
}
