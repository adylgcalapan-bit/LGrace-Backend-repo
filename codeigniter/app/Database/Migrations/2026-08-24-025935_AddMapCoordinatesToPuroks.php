<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMapCoordinatesToPuroks extends Migration
{
    public function up()
    {
        // Add latitude only if it does not exist yet
        if (!$this->db->fieldExists('latitude', 'puroks')) {
            $this->forge->addColumn('puroks', [
                'latitude' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,8',
                    'null'       => true,
                    'after'      => 'purok_name',
                ],
            ]);
        }

        // Add longitude only if it does not exist yet
        if (!$this->db->fieldExists('longitude', 'puroks')) {
            $this->forge->addColumn('puroks', [
                'longitude' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '11,8',
                    'null'       => true,
                    'after'      => 'latitude',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('longitude', 'puroks')) {
            $this->forge->dropColumn('puroks', 'longitude');
        }

        if ($this->db->fieldExists('latitude', 'puroks')) {
            $this->forge->dropColumn('puroks', 'latitude');
        }
    }
}
