<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class ProtectReportNumbers extends Migration
{
    public function up()
    {
        // =========================================
        // 1. Read all existing report numbers
        // =========================================
        $reports = $this->db->table('reports')
            ->select('report_id, report_no')
            ->orderBy('report_id', 'ASC')
            ->get()
            ->getResultArray();

        $usedNumbers = [];
        $reportsToRepair = [];

        // Keep the first valid occurrence of every Report No.
        // NULL, 0, negative, or duplicate values will be repaired.
        foreach ($reports as $report) {
            $reportId = (int) ($report['report_id'] ?? 0);
            $reportNo = (int) ($report['report_no'] ?? 0);

            if (
                $reportId > 0 &&
                $reportNo > 0 &&
                !isset($usedNumbers[$reportNo])
            ) {
                $usedNumbers[$reportNo] = true;
                continue;
            }

            if ($reportId > 0) {
                $reportsToRepair[] = $reportId;
            }
        }

        // =========================================
        // 2. Fill the lowest available Report No.
        // =========================================
        $nextNumber = 1;

        foreach ($reportsToRepair as $reportId) {

            while (isset($usedNumbers[$nextNumber])) {
                $nextNumber++;
            }

            $this->db->table('reports')
                ->where('report_id', $reportId)
                ->update([
                    'report_no' => $nextNumber,
                ]);

            $usedNumbers[$nextNumber] = true;

            $nextNumber++;
        }

        // =========================================
        // 3. Verify no invalid Report No. remains
        // =========================================
        $invalidCount = $this->db->table('reports')
            ->groupStart()
            ->where('report_no IS NULL', null, false)
            ->orWhere('report_no <=', 0)
            ->groupEnd()
            ->countAllResults();

        if ($invalidCount > 0) {
            throw new RuntimeException(
                'Unable to repair all missing Report No. values.'
            );
        }

        // =========================================
        // 4. Verify no duplicates remain
        // =========================================
        $duplicate = $this->db->query("
            SELECT report_no
            FROM reports
            GROUP BY report_no
            HAVING COUNT(*) > 1
            LIMIT 1
        ")->getRowArray();

        if ($duplicate) {
            throw new RuntimeException(
                'Duplicate Report No. values still exist.'
            );
        }

        // =========================================
        // 5. Report No. must never be NULL
        // =========================================
        $this->db->query("
            ALTER TABLE reports
            MODIFY report_no INT NOT NULL
        ");

        // =========================================
        // 6. Protect Report No. from duplicates
        // =========================================
        $existingUniqueIndex = $this->db->query("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'reports'
              AND COLUMN_NAME = 'report_no'
              AND NON_UNIQUE = 0
            LIMIT 1
        ")->getRowArray();

        if (!$existingUniqueIndex) {
            $this->db->query("
                ALTER TABLE reports
                ADD UNIQUE KEY uq_reports_report_no (report_no)
            ");
        }
    }

    public function down()
    {
        // Remove only the unique index created by this migration.
        $ourIndex = $this->db->query("
            SELECT INDEX_NAME
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'reports'
              AND INDEX_NAME = 'uq_reports_report_no'
            LIMIT 1
        ")->getRowArray();

        if ($ourIndex) {
            $this->db->query("
                ALTER TABLE reports
                DROP INDEX uq_reports_report_no
            ");
        }

        // Restore nullable behavior only.
        // Existing Report No. values are intentionally preserved.
        $this->db->query("
            ALTER TABLE reports
            MODIFY report_no INT NULL
        ");
    }
}
