<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%investigator_team}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%trial}}`
 */
class m260511_073001_create_investigator_team_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%investigator_team}}', [
            'id' => $this->primaryKey(),
            'role' => $this->integer(),
            'institution' => $this->string(),
            'country' => $this->integer(),
            'name' => $this->string(150),
            'mobile_number' => $this->string(150),
            'email_address' => $this->string(150),
            'postal_address' => $this->string(150),
            'city' => $this->integer(),
            'trial_id' => $this->integer()->notNUll(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `trial_id`
        $this->createIndex(
            '{{%idx-investigator_team-trial_id}}',
            '{{%investigator_team}}',
            'trial_id'
        );

        // add foreign key for table `{{%trial}}`
        $this->addForeignKey(
            '{{%fk-investigator_team-trial_id}}',
            '{{%investigator_team}}',
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
            '{{%fk-investigator_team-trial_id}}',
            '{{%investigator_team}}'
        );

        // drops index for column `trial_id`
        $this->dropIndex(
            '{{%idx-investigator_team-trial_id}}',
            '{{%investigator_team}}'
        );

        $this->dropTable('{{%investigator_team}}');
    }
}
