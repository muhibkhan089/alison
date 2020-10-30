<?php 
namespace Esparksinc\Consult\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    private $eavSetupFactory;
    
    private $eavConfig;
    
    private $attributeResource;
    
    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
    }
    
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        if ( version_compare( $context->getVersion(), '1.0.3', '<' ) ) {


            $eavSetup->addAttribute(Customer::ENTITY, 'ergo_bio', [
            'type' => 'text',
            'label' => 'Bio',
            'input' => 'textarea',
            'required' => false,
            'visible' => true,
            'user_defined' => false,
            'system' => false,
            'sort_order' => 800,
            'position' => 800,
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'ergo_address');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $this->attributeResource->save($attribute);

        $eavSetup->addAttribute(Customer::ENTITY, 'ergo_address', [
            'type' => 'text',
            'label' => 'Ergonomist Address',
            'input' => 'textarea',
            'required' => false,
            'visible' => true,
            'user_defined' => false,
            'system' => false,
            'sort_order' => 1100,
            'position' => 1100,
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'ergo_address');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $this->attributeResource->save($attribute);
        }
    }
}