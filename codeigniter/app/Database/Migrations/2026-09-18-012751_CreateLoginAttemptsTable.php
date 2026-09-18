<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginAttemptsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'login_attempt_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'attempt_key' => [
                'type'       => 'CHAR',
                'constraint' => 64,
            ],

            'attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
            ],

            'locked_until' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey(
            'login_attempt_id',
            true
        );

        $this->forge->addUniqueKey(
            'attempt_key',
            'login_attempts_attempt_key_unique'
        );

        $this->forge->createTable(
            'login_attempts',
            true
        );
    }

    public function down()
    {
        $this->forge->dropTable(
            'login_attempts',
            true
        );
    }
}
