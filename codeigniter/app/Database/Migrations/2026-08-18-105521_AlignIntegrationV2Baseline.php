<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlignIntegrationV2Baseline extends Migration
{
    public function up()
    {
        // ==========================================
        // USERS: align old database with current app
        // ==========================================

        if ($this->db->fieldExists('fullname', 'users')
            && ! $this->db->fieldExists('full_name', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                CHANGE `fullname` `full_name` VARCHAR(100) NOT NULL
            ");
        }

        if ($this->db->fieldExists('phone_number', 'users')
            && ! $this->db->fieldExists('mobile_number', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                CHANGE `phone_number` `mobile_number` VARCHAR(20) NULL
            ");
        }

        if (! $this->db->fieldExists('username', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `username` VARCHAR(100) NULL AFTER `mobile_number`
            ");

            $this->db->query("
                UPDATE `users`
                SET `username` = CONCAT('user_', `user_id`)
                WHERE `username` IS NULL OR `username` = ''
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

        if (! $this->db->fieldExists('profile_image', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `profile_image` VARCHAR(255) NULL AFTER `address`
            ");
        }

        if (! $this->db->fieldExists('updated_at', 'users')) {
            $this->db->query("
                ALTER TABLE `users`
                ADD `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`
            ");
        }

        // ==========================================
        // REPORTS: add fields expected by current API
        // ==========================================

        if (! $this->db->fieldExists('address', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `address` VARCHAR(255) NULL AFTER `longitude`
            ");
        }

        if (! $this->db->fieldExists('priority', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `priority` VARCHAR(50) NULL AFTER `status`
            ");
        }

        if (! $this->db->fieldExists('updated_reported', 'reports')) {
            $this->db->query("
                ALTER TABLE `reports`
                ADD `updated_reported` DATETIME NULL AFTER `report_date`
            ");
        }

        // ==========================================
        // NOTIFICATIONS
        // ==========================================

        if (! $this->db->tableExists('notifications')) {
            $this->db->query("
                CREATE TABLE `notifications` (
                    `notification_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `user_id` INT(11) NOT NULL,
                    `report_id` INT(11) NULL,
                    `message` TEXT NOT NULL,
                    `status` ENUM('Unread','Read') NOT NULL DEFAULT 'Unread',
                    `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`notification_id`),
                    KEY `idx_notifications_user` (`user_id`),
                    KEY `idx_notifications_report` (`report_id`),
                    CONSTRAINT `notifications_user_fk`
                        FOREIGN KEY (`user_id`)
                        REFERENCES `users` (`user_id`)
                        ON DELETE CASCADE
                        ON UPDATE RESTRICT,
                    CONSTRAINT `notifications_report_fk`
                        FOREIGN KEY (`report_id`)
                        REFERENCES `reports` (`report_id`)
                        ON DELETE CASCADE
                        ON UPDATE RESTRICT
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }

        // ==========================================
        // ANNOUNCEMENTS
        // ==========================================

        if (! $this->db->tableExists('announcements')) {
            $this->db->query("
                CREATE TABLE `announcements` (
                    `announcement_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `user_id` INT(11) NULL,
                    `title` VARCHAR(200) NOT NULL,
                    `content` TEXT NOT NULL,
                    `category` VARCHAR(100) NULL,
                    `publish_date` DATETIME NULL,
                    `status` ENUM('Draft','Published') NOT NULL DEFAULT 'Draft',
                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    PRIMARY KEY (`announcement_id`),
                    KEY `idx_announcements_user` (`user_id`),
                    CONSTRAINT `announcements_user_fk`
                        FOREIGN KEY (`user_id`)
                        REFERENCES `users` (`user_id`)
                        ON DELETE SET NULL
                        ON UPDATE RESTRICT
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }

    public function down()
    {
        // Intentionally non-destructive.
        // This migration aligns legacy/local schemas and may run on
        // databases where some structures already existed beforehand.
    }
}