<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%funding}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%clinical_trial}}`
 */
class m260511_111834_create_funding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%funding}}', [
            'id' => $this->primaryKey(),
            'sponsor_name' => $this->string(250),
            'Amount' => $this->float(),
            'country' => $this->integer(),
            'funding_Sector' => $this->integer(),
            'trial_id' => $this->integer(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'update_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-funding-trial_id}}',
            '{{%funding}}',
            'trial_id'
        );

        // add foreign key for table `{{%clinical_trial}}`
        $this->addForeignKey(
            '{{%fk-funding-trial_id}}',
            '{{%funding}}',
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
        // drops foreign key for table `{{%clinical_trial}}`
        $this->dropForeignKey(
            '{{%fk-funding-trial_id}}',
            '{{%funding}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-funding-trial_id}}',
            '{{%funding}}'
        );

        $this->dropTable('{{%funding}}');
    }
}
