<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeTeamDatabaseSchema extends Migration
{
    private function tableExists(string $table): bool
    {
        return $this->db->query(
            'SHOW TABLES LIKE ' . $this->db->escape($table)
        )->getNumRows() > 0;
    }

    public function up()
    {
        // =====================================================
        // 1. PUROKS
        // Required by registration/resident management.
        // =====================================================

        if (! $this->tableExists('puroks')) {
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

            // Keep IDs consistent across team databases.
            $puroks = [
                ['purok_id' => 1,  'purok_name' => 'Purok Aleman'],
                ['purok_id' => 2,  'purok_name' => 'Purok Arancello'],
                ['purok_id' => 3,  'purok_name' => 'Purok Dam'],
                ['purok_id' => 4,  'purok_name' => 'Purok Durian'],
                ['purok_id' => 5,  'purok_name' => 'Purok Golden Gate'],
                ['purok_id' => 6,  'purok_name' => 'Purok Lanzones'],
                ['purok_id' => 7,  'purok_name' => 'Purok Macopa'],
                ['purok_id' => 8,  'purok_name' => 'Purok Maligaya'],
                ['purok_id' => 9,  'purok_name' => 'Purok Maharlika'],
                ['purok_id' => 10, 'purok_name' => 'Purok Mahayahay 1'],
                ['purok_id' => 11, 'purok_name' => 'Purok Mahayahay 2'],
                ['purok_id' => 12, 'purok_name' => 'Purok Magnosteen'],
                ['purok_id' => 13, 'purok_name' => 'Purok Malinawon 1'],
                ['purok_id' => 14, 'purok_name' => 'Purok Malinawon 2'],
                ['purok_id' => 15, 'purok_name' => 'Purok Mansanitas A'],
                ['purok_id' => 16, 'purok_name' => 'Purok Mansanitas B'],
                ['purok_id' => 17, 'purok_name' => 'Purok Mangga'],
                ['purok_id' => 18, 'purok_name' => 'Purok Marang'],
                ['purok_id' => 19, 'purok_name' => 'Purok Nangka'],
                ['purok_id' => 20, 'purok_name' => 'Purok Narra'],
                ['purok_id' => 21, 'purok_name' => 'Purok Passion Fruit'],
                ['purok_id' => 22, 'purok_name' => 'Purok Pinya'],
                ['purok_id' => 23, 'purok_name' => 'Purok Pag-Asa'],
                ['purok_id' => 24, 'purok_name' => 'Purok Rambutan'],
                ['purok_id' => 25, 'purok_name' => 'Purok Sampaguita'],
                ['purok_id' => 26, 'purok_name' => 'Purok San-Isidro'],
                ['purok_id' => 27, 'purok_name' => 'Purok Santol-A'],
                ['purok_id' => 28, 'purok_name' => 'Purok Santol-B'],
                ['purok_id' => 29, 'purok_name' => 'Purok Santan'],
                ['purok_id' => 30, 'purok_name' => 'Purok Señorita'],
            ];

            $this->db->table('puroks')->insertBatch($puroks);
        }


        // =====================================================
        // 2. USERS
        // Old cipher_db:
        // fullname, phone_number, status
        //
        // integration-v2:
        // full_name, mobile_number, username, purok_id,
        // profile_image, is_active, updated_at
        // =====================================================

        if (
            $this->db->fieldExists('fullname', 'users')
            && ! $this->db->fieldExists('full_name', 'users')
        ) {
            $this->db->query("
                ALTER TABLE `users`
                CHANGE `fullname` `full_name` VARCHAR(150) NOT NULL
            ");
        }

        if ($this->db->fieldExists('full_name', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                MODIFY `full_name` VARCHAR(150) NOT NULL
            ");
        }

        if (
            $this->db->fieldExists('phone_number', 'users')
            && ! $this->db->fieldExists('mobile_number', 'users')
        ) {
            $this->db->query("
                ALTER TABLE `users`
                CHANGE `phone_number` `mobile_number` VARCHAR(20) NULL
            ");
        }

        if (! $this->db->fieldExists('mobile_number', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `mobile_number` VARCHAR(20) NULL
            ");
        }

        if (! $this->db->fieldExists('username', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `username` VARCHAR(100) NULL
            ");

            $this->db->query("
                UPDATE `users`
                SET `username` = CONCAT('user_', `user_id`)
                WHERE `username` IS NULL
                   OR `username` = ''
            ");

            $this->db->query("
                ALTER TABLE `users`
                MODIFY `username` VARCHAR(100) NOT NULL
            ");

            $this->db->query("
                ALTER TABLE `users`
                ADD UNIQUE KEY `users_username_unique` (`username`)
            ");
        }

        if (! $this->db->fieldExists('purok_id', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `purok_id` INT NULL
            ");
        }

        if (! $this->db->fieldExists('profile_image', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `profile_image` VARCHAR(255) NULL
            ");
        }

        if (! $this->db->fieldExists('is_active', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `is_active` TINYINT(1) NOT NULL DEFAULT 1
            ");

            // Preserve old active/inactive status.
            if ($this->db->fieldExists('status', 'users')) {
                $this->db->query("
                    UPDATE `users`
                    SET `is_active` =
                        CASE
                            WHEN LOWER(`status`) = 'active' THEN 1
                            ELSE 0
                        END
                ");
            }
        }

        if (! $this->db->fieldExists('updated_at', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `updated_at` TIMESTAMP NULL DEFAULT NULL
            ");
        }


        // =====================================================
        // 3. CATEGORIES
        // =====================================================

        if (
            $this->db->tableExists('category')
            && ! $this->tableExists('categories')
        ) {
            $this->forge->renameTable('category', 'categories');
        }

        if (
            $this->tableExists('categories')
            && ! $this->db->fieldExists('is_active', 'categories')
        ) {
            $this->db->query("
                ALTER TABLE `categories`
                ADD `is_active` TINYINT(1) NOT NULL DEFAULT 1
            ");
        }


        // =====================================================
        // 4. IMAGES
        // =====================================================

        if (
            $this->tableExists('image')
            && ! $this->tableExists('images')
        ) {
            $this->forge->renameTable('image', 'images');
        }


        // =====================================================
        // 5. REPORTS
        //
        // cipher_db uses:
        // longitude + report_date
        //
        // integration-v2 uses:
        // longtitude + date_reported
        // =====================================================

        if (! $this->db->fieldExists('is_anonymous', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `is_anonymous` TINYINT(1) NOT NULL DEFAULT 0
            ");
        }

        if (
            $this->db->fieldExists('longitude', 'reports')
            && ! $this->db->fieldExists('longtitude', 'reports')
        ) {
            $this->db->query("
                ALTER TABLE `reports`
                CHANGE `longitude` `longtitude` DECIMAL(11,8) NULL
            ");
        }

        if (! $this->db->fieldExists('address', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `address` VARCHAR(255) NULL
            ");
        }

        if (! $this->db->fieldExists('priority', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `priority` VARCHAR(20) NULL
            ");
        }

        if (
            $this->db->fieldExists('report_date', 'reports')
            && ! $this->db->fieldExists('date_reported', 'reports')
        ) {
            $this->db->query("
                ALTER TABLE `reports`
                CHANGE `report_date`
                       `date_reported`
                       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ");
        }

        if (! $this->db->fieldExists('date_reported', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `date_reported`
                    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ");
        }

        if (! $this->db->fieldExists('updated_reported', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `updated_reported` TIMESTAMP NULL DEFAULT NULL
            ");
        }


        // =====================================================
        // 6. PASSWORD RESET TOKENS
        // =====================================================

        if (! $this->tableExists('password_reset_tokens')) {
            $this->db->query("
                CREATE TABLE `password_reset_tokens` (
                    `reset_id` INT NOT NULL AUTO_INCREMENT,
                    `user_id` INT NOT NULL,
                    `token_hash` CHAR(64) NOT NULL,
                    `expires_at` DATETIME NOT NULL,
                    `used_at` DATETIME NULL,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`reset_id`),
                    UNIQUE KEY `unique_reset_token` (`token_hash`),
                    KEY `idx_reset_user` (`user_id`),
                    CONSTRAINT `fk_reset_user`
                        FOREIGN KEY (`user_id`)
                        REFERENCES `users` (`user_id`)
                        ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }


        // =====================================================
        // 7. REMEMBER TOKENS
        // =====================================================

        if (! $this->tableExists('remember_tokens')) {
            $this->db->query("
                CREATE TABLE `remember_tokens` (
                    `remember_id` INT NOT NULL AUTO_INCREMENT,
                    `user_id` INT NOT NULL,
                    `token_hash` CHAR(64) NOT NULL,
                    `expires_at` DATETIME NOT NULL,
                    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`remember_id`),
                    UNIQUE KEY `unique_token_hash` (`token_hash`),
                    KEY `idx_remember_user` (`user_id`),
                    CONSTRAINT `fk_remember_user`
                        FOREIGN KEY (`user_id`)
                        REFERENCES `users` (`user_id`)
                        ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }

    public function down()
    {
        /*
         * Intentionally non-destructive.
         *
         * This migration is designed to normalize different team databases
         * while preserving existing users, reports, categories, images,
         * and other local data.
         */
    }
}
