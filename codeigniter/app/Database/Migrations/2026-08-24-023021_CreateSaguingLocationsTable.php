<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaguingLocationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'location_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'location_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],

            'location_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],

            'latitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
            ],

            'longitude' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
            ],

            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],

            'search_keywords' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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

        $this->forge->addKey('location_id', true);

        $this->forge->createTable('saguing_locations', true);
    }

    public function down()
    {
        $this->forge->dropTable('saguing_locations', true);
    }
}
