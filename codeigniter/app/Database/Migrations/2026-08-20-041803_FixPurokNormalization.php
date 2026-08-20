<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixPurokNormalization extends Migration
{
    public function up()
    {
        // =====================================================
        // 1. CREATE PUROKS TABLE IF COMPLETELY MISSING
        // =====================================================

        if (! $this->db->tableExists('puroks')) {
            $this->db->query("
                CREATE TABLE `puroks` (
                    `purok_id` INT NOT NULL AUTO_INCREMENT,
                    `purok_name` VARCHAR(100) NOT NULL,
                    `leader_name` VARCHAR(150) NULL,
                    `contact_number` VARCHAR(20) NULL,
                    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`purok_id`),
                    UNIQUE KEY `unique_purok_name` (`purok_name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }


        // =====================================================
        // 2. INDEPENDENT COLUMN CHECKS
        //
        // Important:
        // Even if puroks already exists, missing columns
        // will still be added.
        // =====================================================

        if (! $this->db->fieldExists('leader_name', 'puroks')) {
            $this->db->query("
                ALTER TABLE `puroks`
                ADD `leader_name` VARCHAR(150) NULL
            ");
        }

        if (! $this->db->fieldExists('contact_number', 'puroks')) {
            $this->db->query("
                ALTER TABLE `puroks`
                ADD `contact_number` VARCHAR(20) NULL
            ");
        }

        if (! $this->db->fieldExists('is_active', 'puroks')) {
            $this->db->query("
                ALTER TABLE `puroks`
                ADD `is_active` TINYINT(1) NOT NULL DEFAULT 1
            ");
        }

        if (! $this->db->fieldExists('created_at', 'puroks')) {
            $this->db->query("
                ALTER TABLE `puroks`
                ADD `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
            ");
        }

        if (! $this->db->fieldExists('updated_at', 'puroks')) {
            $this->db->query("
                ALTER TABLE `puroks`
                ADD `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP
            ");
        }


        // =====================================================
        // 3. CANONICAL 30 PUROKS
        // =====================================================

        $puroks = [
            [
                'purok_id' => 1,
                'purok_name' => 'Purok Aleman',
                'leader_name' => 'Ma. Fe E. Gasang',
                'contact_number' => '0912-468-7565',
            ],
            [
                'purok_id' => 2,
                'purok_name' => 'Purok Arancello',
                'leader_name' => 'June Ann B. Jorillo',
                'contact_number' => '0995-813-0168',
            ],
            [
                'purok_id' => 3,
                'purok_name' => 'Purok Dam',
                'leader_name' => 'Normilyn S. Mayo',
                'contact_number' => '0938-3120-156',
            ],
            [
                'purok_id' => 4,
                'purok_name' => 'Purok Durian',
                'leader_name' => 'Rubina Diez',
                'contact_number' => null,
            ],
            [
                'purok_id' => 5,
                'purok_name' => 'Purok Golden Gate',
                'leader_name' => 'Joeffrey Angeles',
                'contact_number' => '0910-394-7420',
            ],
            [
                'purok_id' => 6,
                'purok_name' => 'Purok Lanzones',
                'leader_name' => 'May Ann A. Bansaleo',
                'contact_number' => '0945-076-2810',
            ],
            [
                'purok_id' => 7,
                'purok_name' => 'Purok Macopa',
                'leader_name' => 'Raquel B. Baclid',
                'contact_number' => '0909-690-9595',
            ],
            [
                'purok_id' => 8,
                'purok_name' => 'Purok Maligaya',
                'leader_name' => 'Edgar M. Bautista',
                'contact_number' => '0970-278-2394',
            ],
            [
                'purok_id' => 9,
                'purok_name' => 'Purok Maharlika',
                'leader_name' => 'Roy Dapan',
                'contact_number' => '0931-976-1490',
            ],
            [
                'purok_id' => 10,
                'purok_name' => 'Purok Mahayahay 1',
                'leader_name' => 'Faustino Oking, Jr.',
                'contact_number' => '0948-274-9112',
            ],
            [
                'purok_id' => 11,
                'purok_name' => 'Purok Mahayahay 2',
                'leader_name' => 'Loilyn Gamayao',
                'contact_number' => null,
            ],
            [
                'purok_id' => 12,
                'purok_name' => 'Purok Magnosteen',
                'leader_name' => 'Noel L. Taladoc',
                'contact_number' => '0943-840-3401',
            ],
            [
                'purok_id' => 13,
                'purok_name' => 'Purok Malinawon 1',
                'leader_name' => 'Paulino C. Rebuta',
                'contact_number' => '0938-687-2222',
            ],
            [
                'purok_id' => 14,
                'purok_name' => 'Purok Malinawon 2',
                'leader_name' => 'Rodel Pepito',
                'contact_number' => '0951-248-9447',
            ],
            [
                'purok_id' => 15,
                'purok_name' => 'Purok Mansanitas A',
                'leader_name' => 'Dante E. Alamis',
                'contact_number' => '0909-450-4210',
            ],
            [
                'purok_id' => 16,
                'purok_name' => 'Purok Mansanitas B',
                'leader_name' => 'Eliezar E. Peloriana',
                'contact_number' => '0946-704-5750',
            ],
            [
                'purok_id' => 17,
                'purok_name' => 'Purok Mangga',
                'leader_name' => 'Chilyn B. Bantacolo',
                'contact_number' => '0910-067-6483',
            ],
            [
                'purok_id' => 18,
                'purok_name' => 'Purok Marang',
                'leader_name' => 'Zenaida G. Poquita',
                'contact_number' => '0919-232-1509',
            ],
            [
                'purok_id' => 19,
                'purok_name' => 'Purok Nangka',
                'leader_name' => 'Mario T. Rulona',
                'contact_number' => null,
            ],
            [
                'purok_id' => 20,
                'purok_name' => 'Purok Narra',
                'leader_name' => 'Agustin E. Romblon, Jr.',
                'contact_number' => '0909-519-9472',
            ],
            [
                'purok_id' => 21,
                'purok_name' => 'Purok Passion Fruit',
                'leader_name' => 'Sammy E. Lubama',
                'contact_number' => '0963-056-3603',
            ],
            [
                'purok_id' => 22,
                'purok_name' => 'Purok Pinya',
                'leader_name' => 'Virgelio I. Baure',
                'contact_number' => '0909-758-7594',
            ],
            [
                'purok_id' => 23,
                'purok_name' => 'Purok Pag-Asa',
                'leader_name' => 'Nelaida P. Buscano',
                'contact_number' => '0951-508-9860',
            ],
            [
                'purok_id' => 24,
                'purok_name' => 'Purok Rambutan',
                'leader_name' => 'Jonathan V. Sales',
                'contact_number' => '0945-754-6187',
            ],
            [
                'purok_id' => 25,
                'purok_name' => 'Purok Sampaguita',
                'leader_name' => 'Mario C. Hewe',
                'contact_number' => '0909-580-1893',
            ],
            [
                'purok_id' => 26,
                'purok_name' => 'Purok San-Isidro',
                'leader_name' => 'Wilma E. Sarsale',
                'contact_number' => '0991-735-8186',
            ],
            [
                'purok_id' => 27,
                'purok_name' => 'Purok Santol-A',
                'leader_name' => 'Nita C. Varias',
                'contact_number' => '0817-1005-409',
            ],
            [
                'purok_id' => 28,
                'purok_name' => 'Purok Santol-B',
                'leader_name' => 'Niño Jay F. Abad',
                'contact_number' => null,
            ],
            [
                'purok_id' => 29,
                'purok_name' => 'Purok Santan',
                'leader_name' => 'Delmar S. Pacot',
                'contact_number' => '0946-397-5090',
            ],
            [
                'purok_id' => 30,
                'purok_name' => 'Purok Señorita',
                'leader_name' => 'Anzarie A. Baclid',
                'contact_number' => '0906-909-1364',
            ],
        ];


        // =====================================================
        // 4. NORMALIZE NAMES, INSERT MISSING PUROKS,
        //    AND COMPLETE BLANK DETAILS SAFELY
        // =====================================================

        // Fix known encoding/spelling variants of Señorita,
        // but only if the correct canonical name does not exist yet.
        $correctSenoritaExists = $this->db
            ->table('puroks')
            ->where('purok_name', 'Purok Señorita')
            ->countAllResults() > 0;

        if (! $correctSenoritaExists) {
            $badNames = [
                'Purok Se├▒orita',
                'Purok SeÃ±orita',
                'Purok Senorita',
            ];

            foreach ($badNames as $badName) {
                $badRecord = $this->db
                    ->table('puroks')
                    ->where('purok_name', $badName)
                    ->get()
                    ->getRowArray();

                if ($badRecord) {
                    $this->db
                        ->table('puroks')
                        ->where('purok_id', $badRecord['purok_id'])
                        ->update([
                            'purok_name' => 'Purok Señorita',
                        ]);

                    break;
                }
            }
        }

        foreach ($puroks as $purok) {

            // First check by Purok NAME.
            // This protects existing databases whose IDs may differ.
            $existingByName = $this->db
                ->table('puroks')
                ->where('purok_name', $purok['purok_name'])
                ->get()
                ->getRowArray();

            if ($existingByName) {
                $updates = [];

                // Preserve existing information.
                // Only fill fields that are blank.
                if (
                    empty($existingByName['leader_name'])
                    && ! empty($purok['leader_name'])
                ) {
                    $updates['leader_name'] = $purok['leader_name'];
                }

                if (
                    empty($existingByName['contact_number'])
                    && ! empty($purok['contact_number'])
                ) {
                    $updates['contact_number'] =
                        $purok['contact_number'];
                }

                if (! empty($updates)) {
                    $this->db
                        ->table('puroks')
                        ->where(
                            'purok_id',
                            $existingByName['purok_id']
                        )
                        ->update($updates);
                }

                continue;
            }

            // Name is missing.
            // Check whether the preferred canonical ID is available.
            $existingById = $this->db
                ->table('puroks')
                ->where('purok_id', $purok['purok_id'])
                ->get()
                ->getRowArray();

            $insertData = [
                'purok_name'     => $purok['purok_name'],
                'leader_name'    => $purok['leader_name'],
                'contact_number' => $purok['contact_number'],
                'is_active'      => 1,
            ];

            if (! $existingById) {
                // Preferred ID is free.
                // Use canonical ID so fresh/team databases stay aligned.
                $insertData['purok_id'] = $purok['purok_id'];
            }

            // If preferred ID is already occupied by another existing
            // record, let AUTO_INCREMENT choose a safe new ID instead
            // of overwriting existing data.
            $this->db
                ->table('puroks')
                ->insert($insertData);
        }
    }


    public function down()
    {
        // Intentionally non-destructive.
        // Do not delete existing Puroks or resident references.
    }
}
