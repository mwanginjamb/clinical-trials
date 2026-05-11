<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_purpose}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%clinical_trial}}`
 */
class m260430_130534_create_study_purpose_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%study_purpose}}', [
            'id' => $this->primaryKey(),
            'study_purpose' => $this->text(),
            'study_objective' => $this->text(),
            'study_hypothesis' => $this->string(),
            'type_of_study' => $this->integer(),
            'intervention' => $this->string(),
            'control_group_name' => $this->boolean(),
            'design_control_group_presence' => $this->boolean(),
            'phase_of_study' => $this->integer(),
            'randomization_method_name' => $this->string(),
            'masking_description' => $this->string(),
            'masking_status' => $this->boolean(),
            'trial_id' => $this->integer(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-study_purpose-trial_id}}',
            '{{%study_purpose}}',
            'trial_id'
        );

        // add foreign key for table `{{%clinical_trial}}`
        $this->addForeignKey(
            '{{%fk-study_purpose-trial_id}}',
            '{{%study_purpose}}',
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
            '{{%fk-study_purpose-trial_id}}',
            '{{%study_purpose}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-study_purpose-trial_id}}',
            '{{%study_purpose}}'
        );

        $this->dropTable('{{%study_purpose}}');
    }
}
