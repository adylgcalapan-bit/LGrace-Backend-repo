<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAdminAuditLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'audit_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'admin_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],

            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            'target_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],

            'target_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],

            'details' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
                'null'       => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('audit_id', true);
        $this->forge->addKey('admin_user_id');
        $this->forge->addKey('created_at');

        $this->forge->createTable('admin_audit_logs');
    }

    public function down()
    {
        $this->forge->dropTable('admin_audit_logs');
    }
}
