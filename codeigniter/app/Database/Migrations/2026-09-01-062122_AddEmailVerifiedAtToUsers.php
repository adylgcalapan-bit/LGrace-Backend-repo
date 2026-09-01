<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailVerifiedAtToUsers extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('email_verified_at', 'users')) {
            return;
        }

        $this->forge->addColumn('users', [
            'email_verified_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'is_active',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('email_verified_at', 'users')) {
            $this->forge->dropColumn(
                'users',
                'email_verified_at'
            );
        }
    }
}
