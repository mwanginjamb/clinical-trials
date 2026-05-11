<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%opendata_access}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%trial}}`
 */
class m260511_084649_create_opendata_access_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%opendata_access}}', [
            'id' => $this->primaryKey(),
            'allow_publishing' => $this->boolean(),
            'repository_name' => $this->string(150),
            'study_identification_variable' => $this->string(),
            'sensitivity_analysis_result' => $this->text(),
            'effective_size_value' => $this->integer(),
            'adjustable_miltiple_comparison' => $this->string(250),
            'handling_missing_data' => $this->string(),
            'document_path' => $this->string(250),
            'quality_assessment_variable' => $this->string(),
            'risk_of_bias_assessment' => $this->string(150),
            'study_limitation' => $this->text(),
            'funding_source' => $this->string(),
            'potential_conflict_of_interest' => $this->string(250),
            'publication_bias_indicator' => $this->string(250),
            'heterogenity_measure' => $this->string(250),
            'confidential_interval' => $this->float(),
            'trial_id' => $this->integer()->notNUll(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-opendata_access-trial_id}}',
            '{{%opendata_access}}',
            'trial_id'
        );

        // add foreign key for table `{{%trial}}`
        $this->addForeignKey(
            '{{%fk-opendata_access-trial_id}}',
            '{{%opendata_access}}',
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
            '{{%fk-opendata_access-trial_id}}',
            '{{%opendata_access}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-opendata_access-trial_id}}',
            '{{%opendata_access}}'
        );

        $this->dropTable('{{%opendata_access}}');
    }
}
