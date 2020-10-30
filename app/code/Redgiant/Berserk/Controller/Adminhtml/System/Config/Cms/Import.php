<?php
namespace Redgiant\Berserk\Controller\Adminhtml\System\Config\Cms;

use Magento\Framework\Controller\Result\JsonFactory;

class Import extends \Redgiant\Berserk\Controller\Adminhtml\System\Config\Cms
{
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);

        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $result = $this->_import();
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'valid' => (int)$result->getIsValid(),
            'import_path' => $result->getImportPath(),
            'message' => $result->getRequestMessage(),
        ]);
    }
}