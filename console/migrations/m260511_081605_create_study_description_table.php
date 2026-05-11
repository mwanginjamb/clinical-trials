<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_description}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%trial}}`
 */
class m260511_081605_create_study_description_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%study_description}}', [
            'id' => $this->primaryKey(),
            'study_website' => $this->string(),
            'lay_summary' => $this->text(),
            'scientific_summary' => $this->text(),
            'trial_id' => $this->integer()->notNUll(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-study_description-trial_id}}',
            '{{%study_description}}',
            'trial_id'
        );

        // add foreign key for table `{{%trial}}`
        $this->addForeignKey(
            '{{%fk-study_description-trial_id}}',
            '{{%study_description}}',
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
            '{{%fk-study_description-trial_id}}',
            '{{%study_description}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-study_description-trial_id}}',
            '{{%study_description}}'
        );

        $this->dropTable('{{%study_description}}');
    }
}
