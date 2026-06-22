<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%town}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%country}}`
 */
class m260622_074836_create_town_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%town}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'code' => $this->integer(),
            'country_id' => $this->integer(),
            'created_at' => $this->integer(26),
            'updated_at' => $this->integer(26),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        // creates index for column `country_id`
        $this->createIndex(
            '{{%idx-town-country_id}}',
            '{{%town}}',
            'country_id'
        );

        // add foreign key for table `{{%country}}`
        $this->addForeignKey(
            '{{%fk-town-country_id}}',
            '{{%town}}',
            'country_id',
            '{{%country}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%country}}`
        $this->dropForeignKey(
            '{{%fk-town-country_id}}',
            '{{%town}}'
        );

        // drops index for column `country_id`
        $this->dropIndex(
            '{{%idx-town-country_id}}',
            '{{%town}}'
        );

        $this->dropTable('{{%town}}');
    }
}
