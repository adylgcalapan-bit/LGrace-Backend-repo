<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailVerificationsTable extends Migration
{
    public function up()
    {
        // Do nothing if the table already exists.
        if ($this->db->tableExists('email_verifications')) {
            return;
        }

        $this->forge->addField([
            'verification_id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],

            'code_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'expires_at' => [
                'type' => 'DATETIME',
            ],

            'attempts' => [
                'type'       => 'TINYINT',
                'unsigned'   => true,
                'default'    => 0,
            ],

            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('verification_id', true);

        // One active verification record per email.
        $this->forge->addUniqueKey('email');

        $this->forge->createTable('email_verifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('email_verifications', true);
    }
}
