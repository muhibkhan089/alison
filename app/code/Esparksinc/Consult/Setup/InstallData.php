<?php 
namespace Esparksinc\Consult\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
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
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(Customer::ENTITY, 'ergo_bio', [
            'type' => 'varchar',
            'label' => 'Bio',
            'input' => 'textarea',
            'required' => false,
            'visible' => true,
            'user_defined' => false,
            'system' => false,
            'sort_order' => 800,
            'position' => 800,
        ]);
        
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'ergo_bio');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $this->attributeResource->save($attribute);

        $eavSetup->addAttribute(Customer::ENTITY, 'ergo_province', [
                'type' => 'varchar',
                'label' => 'Province',
                'input' => 'select',
                'source' => \Esparksinc\Consult\Model\Config\Source\Options::class,
                'required' => false,
                'sort_order' => 1000,
                'visible' => true,
                'system' => false,
                'validate_rules' => '[]',
                'position' => 1000,
                'admin_checkout' => 1,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => false,
                'user_defined' => false,
                'option' => ['values' => []],
            ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'ergo_province');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $this->attributeResource->save($attribute);

        $eavSetup->addAttribute(Customer::ENTITY, 'ergo_credentials', [
            'type' => 'varchar',
            'label' => 'Credentials',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => false,
            'system' => false,
            'sort_order' => 990,
            'position' => 990,
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'ergo_credentials');
        $attribute->setData('used_in_forms', ['adminhtml_customer']);
        $this->attributeResource->save($attribute);

        $eavSetup->addAttribute(Customer::ENTITY, 'ergo_address', [
            'type' => 'varchar',
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