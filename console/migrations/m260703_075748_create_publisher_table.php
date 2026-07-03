<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%publisher}}`.
 */
class m260703_075748_create_publisher_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%publisher}}', [
            'id' => $this->primaryKey(),
            'publisher' => $this->string(250),
            'created_at' => $this->integer(25),
            'updated_at' => $this->string(25),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%publisher}}');
    }
}
