<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_results}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%trial}}`
 */
class m260511_083151_create_study_results_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%study_results}}', [
            'id' => $this->primaryKey(),
            'permission_to_publish' => $this->boolean(),
            'summary_results' => $this->text(),
            'authority_committe_name' => $this->string(),
            'publisher' => $this->string(),
            'url_doi' => $this->string(250),
            'publication_type' => $this->integer(),
            'publication_title' => $this->string(250),
            'trial_id' => $this->integer()->notNUll(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-study_results-trial_id}}',
            '{{%study_results}}',
            'trial_id'
        );

        // add foreign key for table `{{%trial}}`
        $this->addForeignKey(
            '{{%fk-study_results-trial_id}}',
            '{{%study_results}}',
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
            '{{%fk-study_results-trial_id}}',
            '{{%study_results}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-study_results-trial_id}}',
            '{{%study_results}}'
        );

        $this->dropTable('{{%study_results}}');
    }
}
