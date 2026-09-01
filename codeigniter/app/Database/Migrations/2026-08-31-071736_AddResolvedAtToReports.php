<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResolvedAtToReports extends Migration
{
    public function up()
    {
        $fields = [
            'resolved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_reported',
            ],
        ];

        $this->forge->addColumn('reports', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('reports', 'resolved_at');
    }
}
