<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPurokIdToReports extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('purok_id', 'reports')) {
            $this->forge->addColumn('reports', [
                'purok_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'after'      => 'category_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('purok_id', 'reports')) {
            $this->forge->dropColumn('reports', 'purok_id');
        }
    }
}
