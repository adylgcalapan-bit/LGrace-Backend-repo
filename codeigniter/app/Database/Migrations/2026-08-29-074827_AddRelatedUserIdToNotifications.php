<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRelatedUserIdToNotifications extends Migration
{
    private function hasColumn(string $column, string $table): bool
    {
        try {
            return $this->db->fieldExists($column, $table);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function up()
    {
        if (! $this->hasColumn('related_user_id', 'notifications')) {
            $this->forge->addColumn('notifications', [
                'related_user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'after'      => 'report_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->hasColumn('related_user_id', 'notifications')) {
            $this->forge->dropColumn(
                'notifications',
                'related_user_id'
            );
        }
    }
}
