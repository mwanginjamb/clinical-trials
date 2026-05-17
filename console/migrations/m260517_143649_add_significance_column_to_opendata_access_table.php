<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%opendata_access}}`.
 */
class m260517_143649_add_significance_column_to_opendata_access_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%opendata_access}}', 'significant_p_value', $this->integer());
        $this->addColumn('{{%opendata_access}}', 'statistical_method_used', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%opendata_access}}', 'significant_p_value');
        $this->dropColumn('{{%opendata_access}}', 'statistical_method_used');
    }
}
