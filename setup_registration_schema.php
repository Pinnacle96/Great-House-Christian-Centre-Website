<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";

    $sql = "
    CREATE TABLE IF NOT EXISTS `registrations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `event_id` INT NOT NULL,
        `user_id` INT NULL DEFAULT NULL,
        `first_name` VARCHAR(100) NOT NULL,
        `middle_name` VARCHAR(100) NULL,
        `last_name` VARCHAR(100) NOT NULL,
        `gender` ENUM('Male', 'Female') NULL,
        `dob` DATE NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `address_city` VARCHAR(100) NULL,
        `address_state` VARCHAR(100) NULL,
        `address_country` VARCHAR(100) NULL,
        `church_name` VARCHAR(150) NULL,
        `church_location` VARCHAR(150) NULL,
        `church_role` VARCHAR(100) NULL,
        `attendance_mode` ENUM('online', 'onsite') NOT NULL DEFAULT 'onsite',
        `is_first_time` BOOLEAN DEFAULT FALSE,
        `referral_source` VARCHAR(100) NULL,
        `ministry_interests` TEXT NULL,
        `prayer_request` TEXT NULL,
        `registration_code` VARCHAR(20) NOT NULL UNIQUE,
        `status` ENUM('confirmed', 'cancelled') NOT NULL DEFAULT 'confirmed',
        `checked_in_at` DATETIME NULL DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
        INDEX `idx_event_email` (`event_id`, `email`),
        INDEX `idx_code` (`registration_code`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($sql);
    echo "Created 'registrations' table successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
