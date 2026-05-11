<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ethical_approval}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%trial}}`
 */
class m260511_080339_create_ethical_approval_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ethical_approval}}', [
            'id' => $this->primaryKey(),
            'ethical_regulatory_body' => $this->integer(),
            'approved_by_ethical_committee' => $this->boolean(),
            'document_number' => $this->string(),
            'document_path' => $this->string(),
            'trial_id' => $this->integer()->notNUll(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-ethical_approval-trial_id}}',
            '{{%ethical_approval}}',
            'trial_id'
        );

        // add foreign key for table `{{%trial}}`
        $this->addForeignKey(
            '{{%fk-ethical_approval-trial_id}}',
            '{{%ethical_approval}}',
            'trial_id',
            '{{%clinical_trial}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%trial}}`
        $this->dropForeignKey(
            '{{%fk-ethical_approval-trial_id}}',
            '{{%ethical_approval}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-ethical_approval-trial_id}}',
            '{{%ethical_approval}}'
        );

        $this->dropTable('{{%ethical_approval}}');
    }
}
