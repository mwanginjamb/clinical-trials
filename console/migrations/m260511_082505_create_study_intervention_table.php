<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_intervention}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%trial}}`
 */
class m260511_082505_create_study_intervention_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%study_intervention}}', [
            'id' => $this->primaryKey(),
            'intervention_name' => $this->string(150),
            'intervention_description' => $this->text(),
            'control_comparator' => $this->string(),
            'type_of_outcome' => $this->integer(),
            'trial_id' => $this->integer()->notNUll(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-study_intervention-trial_id}}',
            '{{%study_intervention}}',
            'trial_id'
        );

        // add foreign key for table `{{%trial}}`
        $this->addForeignKey(
            '{{%fk-study_intervention-trial_id}}',
            '{{%study_intervention}}',
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
            '{{%fk-study_intervention-trial_id}}',
            '{{%study_intervention}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-study_intervention-trial_id}}',
            '{{%study_intervention}}'
        );

        $this->dropTable('{{%study_intervention}}');
    }
}
