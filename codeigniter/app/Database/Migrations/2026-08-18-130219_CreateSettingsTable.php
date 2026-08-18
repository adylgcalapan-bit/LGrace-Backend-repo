<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'setting_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'system_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'CPVS',
            ],

            'barangay_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'default'    => 'Barangay Saguing',
            ],

            'contact_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],

            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],

            'system_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'email_notifications' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],

            'report_notifications' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],

            'registration_notifications' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],

            'announcement_notifications' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],

            'session_timeout' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 30,
            ],

            'date_format' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'MM/DD/YYYY',
            ],

            'items_per_page' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10,
            ],

            'theme_preference' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'Light',
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('setting_id', true);
        $this->forge->createTable('settings');

        // Default system settings
        $this->db->table('settings')->insert([
            'system_name' => 'CPVS',
            'barangay_name' => 'Barangay Saguing',
            'contact_email' => 'admin@cpvs.com',
            'contact_number' => '09123456789',
            'system_description' =>
                'Community Problems Visibility System for barangay reporting and monitoring.',
            'email_notifications' => 1,
            'report_notifications' => 1,
            'registration_notifications' => 1,
            'announcement_notifications' => 1,
            'session_timeout' => 30,
            'date_format' => 'MM/DD/YYYY',
            'items_per_page' => 10,
            'theme_preference' => 'Light',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('settings', true);
    }
}
