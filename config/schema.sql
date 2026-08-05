-- GHCC Database Schema

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE, -- Superadmin, Pastor, Department Leader, Member, Registration Manager, Registration Team, Admin
    description TEXT
);

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    leader_id INT, -- user_id of the leader
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    dob DATE,
    department_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    joined_at DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS sermon_series (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    thumbnail VARCHAR(255),
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sermons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    preacher VARCHAR(100),
    series_id INT,
    description TEXT,
    video_url VARCHAR(255), -- Embed URL
    audio_url VARCHAR(255), -- File path
    thumbnail VARCHAR(255),
    date_preached DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (series_id) REFERENCES sermon_series(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME,
    location VARCHAR(255),
    thumbnail VARCHAR(255),
    requires_registration BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(100), -- Optional if anonymous
    donor_email VARCHAR(100),
    amount DECIMAL(10, 2) NOT NULL,
    type ENUM('tithe', 'offering', 'seed', 'partnership') NOT NULL,
    transaction_id VARCHAR(100), -- Payment gateway ID
    status ENUM('pending', 'successful', 'failed') DEFAULT 'pending',
    donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS prayer_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    request TEXT NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    status ENUM('new', 'prayed', 'archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS communications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    channel ENUM('email', 'sms') NOT NULL,
    target_audience ENUM('all_members', 'active_members', 'specific_department') DEFAULT 'all_members',
    department_id INT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    sent_count INT DEFAULT 0,
    failed_count INT DEFAULT 0,
    total_recipients INT DEFAULT 0,
    scheduled_for TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Seed Data
INSERT INTO roles (id, name, description) VALUES 
(1, 'Superadmin', 'Full access to all branches and system settings'),
(2, 'Pastor', 'Can manage sermons, events, members'),
(3, 'Department Leader', 'Can manage their department members'),
(4, 'Member', 'Can view member-only content'),
(5, 'Registration Manager', 'Can manage all event registrations'),
(6, 'Registration Team', 'Can view and check in event registrations'),
(7, 'Admin', 'Branch-scoped administrative access');

-- Default Superadmin User (Password: password123)
-- Hash generated for 'password123'
INSERT INTO users (name, email, password, role_id) VALUES 
('System Superadmin', 'admin@ghcc.org', '$2y$10$SDsOXY52DVF.NN595bFbH.IxjV0.IOkbU8ENwfmgcjbBqfdHbTBfu', 1);

CREATE TABLE IF NOT EXISTS newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS page_contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(50) NOT NULL,
    section_name VARCHAR(50) NOT NULL,
    content_key VARCHAR(50) NOT NULL,
    content_value TEXT,
    content_type ENUM('text', 'image', 'html') DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (page_name, section_name, content_key)
);

-- Extend settings for Paystack and PHPMailer
INSERT INTO settings (setting_key, setting_value) VALUES 
('paystack_public_key', ''),
('paystack_secret_key', ''),
('mail_host', 'smtp.gmail.com'),
('mail_port', '587'),
('mail_username', ''),
('mail_password', ''),
('mail_from_name', 'Great House Christian Centre'),
('mail_encryption', 'tls'),
('site_logo', 'assets/logo/ghcc_logo.png'),
('site_favicon', 'assets/logo/ghcc_logo.png');

-- Slider Content
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('home', 'hero', 'slide1_title', 'WELCOME TO GREAT HOUSE', 'text'),
('home', 'hero', 'slide1_subtitle', 'Raising a people of power, purpose, and passion for Gods Kingdom.', 'text'),
('home', 'hero', 'slide1_image', 'assets/img/bg.jpg', 'image'),
('home', 'hero', 'slide2_title', 'WALK IN POWER & PURPOSE', 'text'),
('home', 'hero', 'slide2_subtitle', 'Discovering your divine mandate and walking in the fullness of Gods destiny.', 'text'),
('home', 'hero', 'slide2_image', 'assets/img/2.jpg', 'image'),
('home', 'hero', 'slide3_title', 'A PLACE TO BELONG', 'text'),
('home', 'hero', 'slide3_subtitle', 'Join a vibrant community of believers dedicated to kingdom transformation.', 'text'),
('home', 'hero', 'slide3_image', 'assets/img/222.jpg', 'image');
