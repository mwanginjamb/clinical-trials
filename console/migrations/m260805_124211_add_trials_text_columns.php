<?php

use yii\db\Migration;

class m260805_124211_add_trials_text_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%clinical_trial}}', 'scientific_title', $this->text());
        $this->alterColumn('{{%clinical_trial}}', 'public_title', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // revert to string type with max length of 255
        $this->alterColumn('{{%clinical_trial}}', 'scientific_title', $this->string(255));
        $this->alterColumn('{{%clinical_trial}}', 'public_title', $this->string(255));

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260805_124211_add_trials_text_columns cannot be reverted.\n";

        return false;
    }
    */
}
