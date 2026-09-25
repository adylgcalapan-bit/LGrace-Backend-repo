<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSeparatedNameFieldsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'middle_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ]);

        // Backfill existing users without removing full_name.
        $this->db->query("
            UPDATE users
            SET
                first_name = CASE
                    WHEN TRIM(full_name) = '' THEN NULL
                    ELSE SUBSTRING_INDEX(TRIM(full_name), ' ', 1)
                END,

                last_name = CASE
                    WHEN TRIM(full_name) = '' THEN NULL
                    WHEN TRIM(full_name) NOT LIKE '% %' THEN NULL
                    ELSE SUBSTRING_INDEX(TRIM(full_name), ' ', -1)
                END,

                middle_name = CASE
                    WHEN TRIM(full_name) = '' THEN NULL
                    WHEN (
                        LENGTH(TRIM(full_name))
                        - LENGTH(REPLACE(TRIM(full_name), ' ', ''))
                    ) < 2 THEN NULL
                    ELSE TRIM(
                        SUBSTRING(
                            TRIM(full_name),
                            LENGTH(
                                SUBSTRING_INDEX(TRIM(full_name), ' ', 1)
                            ) + 2,
                            LENGTH(TRIM(full_name))
                            - LENGTH(
                                SUBSTRING_INDEX(TRIM(full_name), ' ', 1)
                            )
                            - LENGTH(
                                SUBSTRING_INDEX(TRIM(full_name), ' ', -1)
                            )
                            - 2
                        )
                    )
                END
        ");
    }

    public function down()
    {
        $this->forge->dropColumn(
            'users',
            [
                'first_name',
                'middle_name',
                'last_name',
            ]
        );
    }
}
