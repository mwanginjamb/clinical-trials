<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%specialization_subsection}}`.
 */
class m260512_081931_create_specialization_subsection_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%specialization_subsection}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(),
            'description' => $this->text(),
            'area_of_specialization_id' => $this->integer(),
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
        $this->dropTable('{{%specialization_subsection}}');
    }
}
