<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResidentNoToUsers extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('resident_no', 'users')) {

            $this->forge->addColumn('users', [
                'resident_no' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'user_id',
                ],
            ]);

            $residents = $this->db->table('users')
                ->select('user_id')
                ->where('role', 'resident')
                ->orderBy('user_id', 'ASC')
                ->get()
                ->getResultArray();

            $number = 1;

            foreach ($residents as $resident) {
                $this->db->table('users')
                    ->where('user_id', $resident['user_id'])
                    ->update([
                        'resident_no' => $number,
                    ]);

                $number++;
            }

            $this->db->query("
                ALTER TABLE users
                ADD UNIQUE KEY uq_users_resident_no (resident_no)
            ");
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('resident_no', 'users')) {

            $this->db->query("
                ALTER TABLE users
                DROP INDEX uq_users_resident_no
            ");

            $this->forge->dropColumn(
                'users',
                'resident_no'
            );
        }
    }
}
