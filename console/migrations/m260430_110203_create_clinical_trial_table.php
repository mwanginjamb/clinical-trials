<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%clinical_trial}}`.
 */
class m260430_110203_create_clinical_trial_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%clinical_trial}}', [
            'id' => $this->primaryKey(),
            'scientific_title' => $this->string(),
            'public_title' => $this->string(),
            'scientific_acronym' => $this->string(),
            'protocol_version' => $this->string(),
            'registration_status' => $this->integer(),
            'protocol_number' => $this->string(),
            'registration_number' => $this->string(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%clinical_trial}}');
    }
}
