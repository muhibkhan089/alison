<?php

namespace Cminds\Multiwishlist\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        $wishlistTable = $setup->getTable('wishlist');
        $customerFKName = $setup->getFkName(
            'wishlist',
            'customer_id',
            'customer_entity',
            'entity_id'
        );
        $connection->dropForeignKey(
            $wishlistTable,
            $customerFKName
        );
        $connection->dropIndex(
            $wishlistTable,
            $setup->getIdxName(
                'wishlist',
                'customer_id',
                AdapterInterface::INDEX_TYPE_UNIQUE
            )
        );
        $connection->addForeignKey(
            $customerFKName,
            $wishlistTable,
            'customer_id',
            $setup->getTable('customer_entity'),
            'entity_id'
        );
        $connection->addColumn(
            $wishlistTable,
            'title',
            [
                'type' => Table::TYPE_TEXT,
                'size' => 256,
                'default' => '',
                'comment' => 'Wishlist title'
            ]
        );

        $setup->endSetup();
    }
}
