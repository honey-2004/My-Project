<?php
namespace Iovista\Blog\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $connection = $installer->getConnection();
            $tableName = $installer->getTable('iovista_blog_post');

            // Add title column if it doesn't exist
            if (!$connection->tableColumnExists($tableName, 'title')) {
                $connection->addColumn(
                    $tableName,
                    'title',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'Post Title'
                    ]
                );
            }

            // Add post_date column if it doesn't exist
            if (!$connection->tableColumnExists($tableName, 'post_date')) {
                $connection->addColumn(
                    $tableName,
                    'post_date',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        'nullable' => true,
                        'comment' => 'Post Date'
                    ]
                );
            }

            // Add short_description column if it doesn't exist
            if (!$connection->tableColumnExists($tableName, 'short_description')) {
                $connection->addColumn(
                    $tableName,
                    'short_description',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '64k',
                        'nullable' => true,
                        'comment' => 'Short Description'
                    ]
                );
            }

            // Add long_description column if it doesn't exist
            if (!$connection->tableColumnExists($tableName, 'long_description')) {
                $connection->addColumn(
                    $tableName,
                    'long_description',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '64k',
                        'nullable' => true,
                        'comment' => 'Long Description'
                    ]
                );
            }

            // Add banner_image column if it doesn't exist (rename from featured_image or add new)
            if (!$connection->tableColumnExists($tableName, 'banner_image')) {
                $connection->addColumn(
                    $tableName,
                    'banner_image',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'Banner Image'
                    ]
                );
            }

            // Add author column if it doesn't exist
            if (!$connection->tableColumnExists($tableName, 'author')) {
                $connection->addColumn(
                    $tableName,
                    'author',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'Author'
                    ]
                );
            }
        }

        $installer->endSetup();
    }
}






