<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_population_eligibility}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%clinical_trial}}`
 */
class m260430_131215_create_study_population_eligibility_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%study_population_eligibility}}', [
            'id' => $this->primaryKey(),
            'health_condition_studied' => $this->string(),
            'type_of_eligibility' => $this->text(),
            'participant_target_number' => $this->integer(),
            'sample_size' => $this->integer(),
            'final_number_of_participants' => $this->integer(),
            'trial_id' => $this->integer(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-study_population_eligibility-trial_id}}',
            '{{%study_population_eligibility}}',
            'trial_id'
        );

        // add foreign key for table `{{%clinical_trial}}`
        $this->addForeignKey(
            '{{%fk-study_population_eligibility-trial_id}}',
            '{{%study_population_eligibility}}',
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
            '{{%fk-study_population_eligibility-trial_id}}',
            '{{%study_population_eligibility}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-study_population_eligibility-trial_id}}',
            '{{%study_population_eligibility}}'
        );

        $this->dropTable('{{%study_population_eligibility}}');
    }
}
