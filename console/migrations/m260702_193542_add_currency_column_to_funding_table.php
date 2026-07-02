<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%funding}}`.
 */
class m260702_193542_add_currency_column_to_funding_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%funding}}', 'currency', $this->string(3));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%funding}}', 'currency');
    }
}
