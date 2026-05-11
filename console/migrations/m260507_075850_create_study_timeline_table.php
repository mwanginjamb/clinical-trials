<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%study_timeline}}`.
 */
class m260507_075850_create_study_timeline_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%study_timeline}}', [
            'id' => $this->primaryKey(),
            'study_duration' => $this->integer(),
            'study_site_location' => $this->string(),
            'centre_postal_address' => $this->string(),
            'anticipated_start_date' => $this->date(),
            'anticipated_end_date' => $this->date(),
            'recruitment_status' => $this->integer(),
            'recruiting_country' => $this->integer(),
            'centre_pysical_address' => $this->string(),
            'centre_region' => $this->integer(),
            'trial_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'updated_by' => $this->integer(),
            'created_by' => $this->integer(),
        ]);


        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-study_timeline-trial_id}}',
            '{{%study_timeline}}',
            'trial_id'
        );

        // add foreign key for table `{{%trial}}`
        $this->addForeignKey(
            '{{%fk-study_timeline-trial_id}}',
            '{{%study_timeline}}',
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
        $this->dropTable('{{%study_timeline}}');
    }
}
