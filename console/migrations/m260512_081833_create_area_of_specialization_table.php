<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%area_of_specialization}}`.
 */
class m260512_081833_create_area_of_specialization_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%area_of_specialization}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'description' => $this->text(),
            'created_at' => $this->integer(25),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%area_of_specialization}}');
    }
}
