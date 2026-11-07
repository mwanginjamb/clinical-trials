<?php

use yii\db\Migration;

/**
 * Repairs environments where m260721_212627_create_region_table is recorded as applied
 * but the `region` table is missing.
 *
 * Only acts when the table does not exist. It then recreates it with the same schema and
 * seeds the ids that StudyTimeline::getCentreRegion() used before regions moved to this
 * table, so existing study_timeline.centre_region values (1–8) keep their meaning.
 */
class m261107_200000_restore_region_table extends Migration
{
    private const LEGACY_REGIONS = [
        1 => 'Nairobi (HQ)',
        2 => 'Busia',
        3 => 'Kwale',
        4 => 'Mombasa',
        5 => 'Kilifi Region',
        6 => 'Kisumu',
        7 => 'Siaya',
        8 => 'Kirinyaga',
    ];

    public function safeUp()
    {
        if ($this->db->getTableSchema('{{%region}}', true) !== null) {
            echo "    > region table already exists, nothing to do.\n";
            return;
        }

        $this->createTable('{{%region}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description' => $this->text(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(26),
            'updated_at' => $this->integer(26),
        ]);

        $now = time();
        foreach (self::LEGACY_REGIONS as $id => $name) {
            $this->insert('{{%region}}', ['id' => $id, 'name' => $name, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function safeDown()
    {
        // Intentionally a no-op: the table belongs to m260721_212627_create_region_table.
        return true;
    }
}
