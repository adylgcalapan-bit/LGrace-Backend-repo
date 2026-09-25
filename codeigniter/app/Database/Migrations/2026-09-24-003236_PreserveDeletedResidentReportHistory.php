<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PreserveDeletedResidentReportHistory extends Migration
{
    public function up()
    {
        // Allow reports to remain even after the resident account is deleted.
        $this->db->query("
            ALTER TABLE `reports`
            MODIFY `user_id` INT(11) NULL
        ");

        // Store the original reporter name for historical admin records.
        if (! $this->db->fieldExists('reporter_name_snapshot', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `reporter_name_snapshot` VARCHAR(255) NULL
                AFTER `user_id`
            ");
        }
    }

    public function down()
    {
        /*
         * Intentionally non-destructive.
         *
         * Deleted resident accounts may leave reports with user_id = NULL.
         * Reverting user_id back to NOT NULL could break those historical
         * reports, so this migration should preserve the data.
         */
    }
}
