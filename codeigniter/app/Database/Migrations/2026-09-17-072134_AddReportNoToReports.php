<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReportNoToReports extends Migration
{
    public function up()
    {
        if (! in_array('report_no', $this->db->getFieldNames('reports'), true)) {

            $this->forge->addColumn('reports', [
                'report_no' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'report_id',
                ],
            ]);

            $reports = $this->db->table('reports')
                ->select('report_id')
                ->orderBy('report_id', 'ASC')
                ->get()
                ->getResultArray();

            $number = 1;

            foreach ($reports as $report) {
                $this->db->table('reports')
                    ->where('report_id', $report['report_id'])
                    ->update([
                        'report_no' => $number,
                    ]);

                $number++;
            }

            $this->db->query("
                ALTER TABLE reports
                MODIFY report_no INT UNSIGNED NOT NULL
            ");

            $this->db->query("
                ALTER TABLE reports
                ADD UNIQUE KEY uq_reports_report_no (report_no)
            ");
        }
    }

    public function down()
    {
        if (in_array('report_no', $this->db->getFieldNames('reports'), true)) {

            $this->db->query("
                ALTER TABLE reports
                DROP INDEX uq_reports_report_no
            ");

            $this->forge->dropColumn(
                'reports',
                'report_no'
            );
        }
    }
}
