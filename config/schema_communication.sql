-- Communication Module Schema

-- 1. Communication Logs Table
CREATE TABLE IF NOT EXISTS communication_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT, -- Admin/User who sent it
    recipient_type ENUM('individual', 'group', 'all_members') NOT NULL,
    recipient_group_id INT, -- If sent to a small group
    recipient_count INT DEFAULT 0, -- How many people received it
    channel ENUM('email', 'sms') NOT NULL,
    subject VARCHAR(255), -- For email
    message_body TEXT NOT NULL,
    status ENUM('draft', 'sent', 'failed') DEFAULT 'draft',
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (recipient_group_id) REFERENCES small_groups(id) ON DELETE SET NULL
);

-- 2. Individual Message Status (Detailed delivery report)
CREATE TABLE IF NOT EXISTS message_recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_id INT NOT NULL,
    member_id INT NOT NULL,
    contact_detail VARCHAR(255), -- The email or phone number used
    status ENUM('pending', 'sent', 'failed', 'delivered') DEFAULT 'pending',
    error_message VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (log_id) REFERENCES communication_logs(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);
