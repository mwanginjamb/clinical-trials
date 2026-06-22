<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%country}}`.
 */
class m260622_074702_create_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%country}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'code' => $this->integer(),
            'created_at' => $this->integer(26),
            'updated_at' => $this->integer(26),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%country}}');
    }
}
