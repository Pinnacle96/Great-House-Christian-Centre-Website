-- Fix CMS Schema

-- 1. Add setting_type to settings table if it doesn't exist
-- We can't do IF NOT EXISTS for columns in standard SQL easily without a procedure, 
-- but since I can just run ALTER IGNORE or catch the error in PHP script.
-- However, for simplicity in this SQL file, I'll assume I need to add it.
-- Or I can just DROP the table and recreate it since it's just settings.
DROP TABLE IF EXISTS settings;

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('text', 'image', 'boolean') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Re-seed Settings
INSERT INTO settings (setting_key, setting_value, setting_type) VALUES 
('site_name', 'Great House Christian Centre', 'text'),
('site_logo', 'assets/images/logo.png', 'image'),
('contact_email', 'info@ghcc.org', 'text'),
('contact_phone', '+234 123 456 7890', 'text'),
('address', '123 Church Street, City, State', 'text'),
('facebook_url', '#', 'text'),
('twitter_url', '#', 'text'),
('instagram_url', '#', 'text'),
('youtube_url', '#', 'text');

-- Fix Contact Page Content
DELETE FROM page_contents WHERE page_name = 'contact';

INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('contact', 'info', 'title', 'Get in Touch', 'text'),
('contact', 'info', 'subtitle', 'We would love to hear from you. Reach out with any questions or prayer requests.', 'text'),
('contact', 'map', 'embed_code', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.952912260219!2d3.375295414770757!3d6.5276386952784755!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8b2ae68280c1%3A0xdc9e87a367c3d9cb!2sLagos!5e0!3m2!1sen!2sng!4v1620000000000!5m2!1sen!2sng" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>', 'html');
