<?php
/**
 **
 */

namespace Redgiant\LayeredNavigation\Controller\Search\Result;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Session;
use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Redgiant\Core\Helper\Data as HelperData;

/**
 * Class Index
 * @package Redgiant\LayeredNavigation\Controller\Search\Result
 */
class Index extends Action
{
    /**
     * Catalog session
     *
     * @var Session
     */
    protected $_catalogSession;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @type JsonData
     */
    protected $_jsonHelper;

    /**
     * @type \Redgiant\LayeredNavigation\Helper\Data
     */
    protected $_moduleHelper;

    /**
     * @type Data
     */
    protected $_helper;

    /**
     * @var QueryFactory
     */
    private $_queryFactory;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $catalogSession
     * @param StoreManagerInterface $storeManager
     * @param QueryFactory $queryFactory
     * @param Resolver $layerResolver
     * @param Data $helper
     * @param JsonData $jsonHelper
     * @param HelperData $moduleHelper
     */
    public function __construct(
        Context $context,
        Session $catalogSession,
        StoreManagerInterface $storeManager,
        QueryFactory $queryFactory,
        Resolver $layerResolver,
        Data $helper,
        JsonData $jsonHelper,
        HelperData $moduleHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_storeManager   = $storeManager;
        $this->_catalogSession = $catalogSession;
        $this->_queryFactory   = $queryFactory;
        $this->layerResolver   = $layerResolver;
        $this->_jsonHelper     = $jsonHelper;
        $this->_moduleHelper   = $moduleHelper;
        $this->_helper         = $helper;
        $this->resultPageFactory = $resultPageFactory;
        $this->_scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->layerResolver->create(Resolver::CATALOG_LAYER_SEARCH);
        /* @var $query \Magento\Search\Model\Query */
        $query = $this->_queryFactory->get();

        $query->setStoreId($this->_storeManager->getStore()->getId());

        if ($query->getQueryText() != '') {
            if ($this->_helper->isMinQueryLength()) {
                $query->setId(0)->setIsActive(1)->setIsProcessed(1);
            } else {
                $query->saveIncrementalPopularity();

                if ($query->getRedirect()) {
                    $this->getResponse()->setRedirect($query->getRedirect());

                    return;
                }
            }

            $this->_helper->checkNotes();
            $page = $this->resultPageFactory->create();
            $panelLayout = $this->_scopeConfig->getValue('berserk_settings/catalog/layout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
            if($panelLayout!=''){
                $page->getConfig()->setPageLayout($panelLayout);
                
            }else{
                $this->_view->loadLayout();
            }
            //$this->_view->loadLayout();

            if (true && $this->getRequest()->isAjax()) {
                $navigation = $this->_view->getLayout()->getBlock('catalogsearch.leftnav');
                $products   = $this->_view->getLayout()->getBlock('search.result');
                $result     = [
                    'products'   => $products->toHtml(),
                    'navigation' => $navigation->toHtml()
                ];
                $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($result));
            } else {
                return $page;//$this->_view->renderLayout();
            }
        } else {
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }
}
