<?php
namespace Esparksinc\Consult\Block;
class Ergo extends \Magento\Framework\View\Element\Template
{
	private $_customerGroup;
	private $_customAttribute;
    private $_customerFactory;
    private $_eavConfig;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
		\Magento\Customer\Api\CustomerRepositoryInterface $customAttribute,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Eav\Model\Config $eavConfig,
		array $data = []
	)
	{
		$this->_customerGroup = $customerCollectionFactory;
		$this->_customAttribute = $customAttribute;
        $this->_customerFactory = $customerFactory;
        $this->_eavConfig = $eavConfig;
		parent::__construct($context, $data);
	}
    public function getAllProvince()
    {
        $attributeCode = "ergo_province";
        $attribute = $this->_eavConfig->getAttribute('customer', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        return $options;

    }
	public function getCustomerCollection($value)
    {   
        $collection = $this->_customerGroup->create();
        return $collection->addFieldToFilter("group_id","4")->addFieldToFilter("ergo_province",$value);

    }
    public function getBio($id)
    {   
        $customerRepository = $this->_customAttribute->getById($id);
        $cattrValue = $customerRepository->getCustomAttribute('ergo_bio');
        if ($cattrValue) {
            return $cattrValue->getValue();
        }
        return $cattrValue;
    }
    public function getCredentials($id)
    {   
        $customerRepository = $this->_customAttribute->getById($id);
        $cattrValue = $customerRepository->getCustomAttribute('ergo_credentials');
        if ($cattrValue) {
            return $cattrValue->getValue();
        }
        return $cattrValue;
    }
    public function getAddress($id)
    {   
        $customerRepository = $this->_customAttribute->getById($id);
        $cattrValue = $customerRepository->getCustomAttribute('ergo_address');
        if ($cattrValue) {
            return $cattrValue->getValue();
        }
        return $cattrValue;

    }
    public function getProvinceValue($id)
    {   
        $customerRepository = $this->_customAttribute->getById($id);
        $cattrValue = $customerRepository->getCustomAttribute('ergo_province')->getValue();
        return $cattrValue;
    }
    public function getProvinceText($id)
    {   
        $customer = $this->_customerFactory->create()->load($id);
        $cattrValue = $customer->getAttribute('ergo_province')->getSource()->getOptionText($customer->getData('ergo_province'));
        return $cattrValue;
    }
    public function getFormAction()
    {
            // companymodule is given in routes.xml
            // controller_name is folder name inside controller folder
            // action is php file name inside above controller_name folder

        // return 'magento2/survey/index/post';
        return $this->getUrl('ergo/index/post');
        // here controller_name is index, action is post
    }
    public function provinceCount($value)
    {
        $collection = $this->_customerGroup->create();
        return $collection->addFieldToFilter("group_id","4")->addFieldToFilter("ergo_province",$value)->getSize();
    }
}