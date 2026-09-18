<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIncidentDateToReports extends Migration
{
    public function up()
    {
        $this->forge->addColumn('reports', [
            'incident_date' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'description',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('reports', 'incident_date');
    }
}
