<?php
/**
 */

namespace Redgiant\LayeredNavigation\Plugin\Controller\Product;

use Magento\Framework\App\RequestInterface;
use Redgiant\LayeredNavigation\Helper\Data;

/**
 * Class CompareWishlist
 * @package Redgiant\LayeredNavigation\Plugin\Controller\Product
 */
class CompareWishlist
{
    /** @var \Magento\Framework\App\RequestInterface */
    protected $request;

    /** @var \Redgiant\LayeredNavigation\Helper\Data */
    protected $dataHelper;

    /**
     * Add constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Redgiant\LayeredNavigation\Helper\Data $helperData
     */
    public function __construct(
        RequestInterface $request,
        Data $helperData
    )
    {
        $this->request    = $request;
        $this->dataHelper = $helperData;
    }

    /**
     * @param \Magento\Catalog\Controller\Product\Compare\Add|\Magento\Wishlist\Controller\Index\Add $action
     * @param $page
     * @return mixed
     */
    public function afterExecute($action, $page)
    {
        if ($this->dataHelper->isEnabled() && $this->request->isAjax()) {
            return '';
        }

        return $page;
    }
}
