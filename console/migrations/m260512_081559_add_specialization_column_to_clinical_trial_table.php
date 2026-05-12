<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%clinical_trial}}`.
 */
class m260512_081559_add_specialization_column_to_clinical_trial_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%clinical_trial}}', 'area_of_specialization', $this->integer());
        $this->addColumn('{{%clinical_trial}}', 'specialization_sub_section', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%clinical_trial}}', 'area_of_specialization');
        $this->dropColumn('{{%clinical_trial}}', 'specialization_sub_section');
    }
}
