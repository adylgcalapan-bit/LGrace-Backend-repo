<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPurokNoToPuroks extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('purok_no', 'puroks')) {

            $this->forge->addColumn('puroks', [
                'purok_no' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'purok_id',
                ],
            ]);

            /*
             * Preserve the current visible numbering
             * of ACTIVE Puroks first.
             */
            $activePuroks = $this->db->table('puroks')
                ->select('purok_id')
                ->where('is_active', 1)
                ->orderBy('purok_id', 'ASC')
                ->get()
                ->getResultArray();

            $number = 1;

            foreach ($activePuroks as $purok) {

                $this->db->table('puroks')
                    ->where(
                        'purok_id',
                        $purok['purok_id']
                    )
                    ->update([
                        'purok_no' => $number,
                    ]);

                $number++;
            }

            /*
             * Give any inactive Puroks their own
             * permanent numbers after the active ones.
             */
            $inactivePuroks = $this->db->table('puroks')
                ->select('purok_id')
                ->where('is_active', 0)
                ->orderBy('purok_id', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($inactivePuroks as $purok) {

                $this->db->table('puroks')
                    ->where(
                        'purok_id',
                        $purok['purok_id']
                    )
                    ->update([
                        'purok_no' => $number,
                    ]);

                $number++;
            }

            $this->db->query("
                ALTER TABLE puroks
                MODIFY purok_no INT UNSIGNED NOT NULL
            ");

            $this->db->query("
                ALTER TABLE puroks
                ADD UNIQUE KEY uq_puroks_purok_no (purok_no)
            ");
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('purok_no', 'puroks')) {

            $this->db->query("
                ALTER TABLE puroks
                DROP INDEX uq_puroks_purok_no
            ");

            $this->forge->dropColumn(
                'puroks',
                'purok_no'
            );
        }
    }
}
