<?php

namespace Redgiant\Filterproducts\Controller\Home;

class Latestlist extends \Magento\Framework\App\Action\Action {

    protected $blockFactory;
    protected $objectManagerMock;

    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
    }

    public function execute() {
        if ($this->getRequest()->isAjax()){
            $category_id=$this->getRequest()->getParam('category_id');
            $product_count=$this->getRequest()->getParam('product_count');
            $column_count=$this->getRequest()->getParam('column_count');
            $product_per_page=$this->getRequest()->getParam('product_per_page');
            $infinite=$this->getRequest()->getParam('infinite');
            $pagination_page = $this->getRequest()->getParam('pagination_page');
            $isotope = $this->getRequest()->getParam('isotope');
            $double_size_products = $this->getRequest()->getParam('double_size_products');
            $template_file = 'grid_items.phtml';
            if ($isotope && $isotope == 1)
                $template_file = 'isotope_grid_items.phtml';
            $block = $this->_view->getLayout()->createBlock('Redgiant\Filterproducts\Block\Home\LatestList')
                ->setTemplate($template_file)
                ->setData('category_id',$category_id)
                ->setData('product_count',$product_count)
                ->setData('column_count',$column_count)
                ->setData('product_per_page',$product_per_page)
                ->setData('infinite',$infinite)
                ->setData('pagination_page',$pagination_page)
                ->setData('isotope',$isotope)
                ->setData('double_size_products',$double_size_products)
                ->toHtml();
            $result = [
                'success' => true, 
                'html' => trim($block)
            ];

            $this->getResponse()->setBody(json_encode($result));
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}