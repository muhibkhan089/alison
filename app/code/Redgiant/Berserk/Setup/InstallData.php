<?php
/**
* Copyright © 2018 Redgiant. All rights reserved.
*/

namespace Redgiant\Berserk\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface {

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'brk_page_type', [
            'group' => 'Product Details',
            'type' => 'varchar',
            'sort_order' => 200,
            'backend' => '',
            'frontend' => '',
            'label' => 'Berserk Page Type',
            'input' => 'select',
            'source' => 'Redgiant\Berserk\Model\Attribute\Berserkpagetype',
            'class' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'wysiwyg_enabled' => false,
            'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
        ]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'brk_custom_block', [
            'group' => 'Product Details',
            'type' => 'text',
            'sort_order' => 202,
            'backend' => '',
            'frontend' => '',
            'label' => 'Berserk Custom Block',
            'input' => 'textarea',
            'class' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'wysiwyg_enabled' => true,
            'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
        ]);
    }

}
