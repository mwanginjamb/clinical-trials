<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%study_intervention}}`.
 */
class m260517_122737_add_outcome_description_column_to_study_intervention_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%study_intervention}}', 'outcome_description', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%study_intervention}}', 'outcome_description');
    }
}
