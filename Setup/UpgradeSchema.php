<?php

namespace Rcason\MqMysql\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        
        //Add name column
        $installer->getConnection()->addColumn(
            $installer->getTable('ce_queue_message'),
            'name',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => false,
                'comment' => 'Job name for search purposes',
                'after'     => 'entity_id'
            ]
        );
        $installer->getConnection()->addIndex(
            $installer->getTable('ce_queue_message'),
            $installer->getIdxName('ce_queue_message',['name']),
            ['name']
        );
        
        //Add job execution result column
        $installer->getConnection()->addColumn(
            $installer->getTable('ce_queue_message'),
            'result',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
                'nullable' => true,
                'comment' => 'Job execution result',
                'after'     => 'message_content'
            ]
        );

        $setup->endSetup();
    }
}