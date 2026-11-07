<?php

use yii\db\Migration;

/**
 * Approval documents (PDFs) attached to an ethical approval; up to 3 per approval,
 * enforced in {@see \frontend\models\EthicalApproval}.
 *
 * Files live under @uploads/ethical-approvals/{ethical_approval_id}/{stored_name}.
 */
class m261108_120000_create_ethical_approval_document_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%ethical_approval_document}}', [
            'id' => $this->primaryKey(),
            'ethical_approval_id' => $this->integer()->notNull(),
            'original_name' => $this->string(255)->notNull(),
            'stored_name' => $this->string(64)->notNull()->unique(),
            'mime_type' => $this->string(100)->notNull(),
            'size' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->createIndex(
            '{{%idx-ethical_approval_document-ethical_approval_id}}',
            '{{%ethical_approval_document}}',
            'ethical_approval_id'
        );

        $this->addForeignKey(
            '{{%fk-ethical_approval_document-ethical_approval_id}}',
            '{{%ethical_approval_document}}',
            'ethical_approval_id',
            '{{%ethical_approval}}',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-ethical_approval_document-ethical_approval_id}}', '{{%ethical_approval_document}}');
        $this->dropIndex('{{%idx-ethical_approval_document-ethical_approval_id}}', '{{%ethical_approval_document}}');
        $this->dropTable('{{%ethical_approval_document}}');
    }
}
