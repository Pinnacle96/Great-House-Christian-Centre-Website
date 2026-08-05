-- CMS Content Schema

-- 1. Page Contents Table
CREATE TABLE IF NOT EXISTS page_contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_name VARCHAR(50) NOT NULL, -- e.g. 'home', 'about', 'contact'
    section_name VARCHAR(50) NOT NULL, -- e.g. 'hero', 'welcome', 'footer'
    content_key VARCHAR(50) NOT NULL, -- e.g. 'title', 'subtitle', 'image_url'
    content_value TEXT,
    content_type ENUM('text', 'html', 'image', 'link') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_content (page_name, section_name, content_key)
);

-- 2. Settings Table (Global Site Settings)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('text', 'image', 'boolean') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed Initial Data

-- Global Settings
INSERT INTO settings (setting_key, setting_value, setting_type) VALUES 
('site_name', 'Great House Christian Centre', 'text'),
('site_logo', 'assets/images/logo.png', 'image'),
('contact_email', 'info@ghcc.org', 'text'),
('contact_phone', '+234 123 456 7890', 'text'),
('address', '123 Church Street, City, State', 'text'),
('facebook_url', '#', 'text'),
('twitter_url', '#', 'text'),
('instagram_url', '#', 'text'),
('youtube_url', '#', 'text')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

-- Home Page Content
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('home', 'hero', 'title', 'Welcome Home', 'text'),
('home', 'hero', 'subtitle', 'A place where everyone is welcome and lives are transformed.', 'text'),
('home', 'hero', 'button_text', 'Plan Your Visit', 'text'),
('home', 'hero', 'button_link', '/contact', 'link'),
('home', 'hero', 'background_image', 'assets/images/hero-bg.jpg', 'image'),

('home', 'welcome', 'title', 'Join Us This Sunday', 'text'),
('home', 'welcome', 'content', 'Experience powerful worship, inspiring messages, and a community that feels like family. We have services at 9:00 AM and 11:00 AM.', 'text'),
('home', 'welcome', 'image', 'assets/images/welcome.jpg', 'image'),

('home', 'services', 'title', 'Our Service Times', 'text'),
('home', 'services', 'sunday_time', '9:00 AM & 11:00 AM', 'text'),
('home', 'services', 'midweek_time', 'Wednesday 6:00 PM', 'text'),

('home', 'ministries', 'title', 'Get Involved', 'text'),
('home', 'ministries', 'subtitle', 'Find your place in our community through our various ministries.', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- About Page Content
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('about', 'intro', 'title', 'Our Story', 'text'),
('about', 'intro', 'content', 'Founded in 2010, GHCC started as a small bible study group and has grown into a vibrant community of believers passionate about Jesus.', 'text'),
('about', 'intro', 'image', 'assets/images/about-intro.jpg', 'image'),

('about', 'beliefs', 'title', 'What We Believe', 'text'),
('about', 'beliefs', 'content', '<ul><li>We believe in one God...</li><li>We believe in the Bible...</li><li>We believe in salvation through Jesus Christ...</li></ul>', 'html'),

('about', 'leadership', 'title', 'Our Leadership', 'text'),
('about', 'leadership', 'subtitle', 'Meet the team serving our church family.', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Contact Page Content
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('contact', 'info', 'title', 'Get in Touch', 'text'),
('contact', 'info', 'subtitle', 'We would love to hear from you. Reach out with any questions or prayer requests.', 'text'),
('contact', 'map', 'embed_code', '<iframe src=\"...\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>', 'html')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Give Page Content (already referenced in GiveController)
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('give', 'hero', 'badge', 'GENEROSITY', 'text'),
('give', 'hero', 'title', 'Give Online', 'text'),
('give', 'hero', 'subtitle', '\"God loves a cheerful giver.\" — 2 Corinthians 9:7', 'text'),
('give', 'giving_form', 'badge', 'SECURE GIVING', 'text'),
('give', 'giving_form', 'title', 'Make a Donation', 'text'),
('give', 'giving_form', 'subtitle', 'Support the work of God through your generous giving. Your contribution helps us reach more lives.', 'text'),
('give', 'principles', 'title', 'Why We Give', 'text'),
('give', 'principles', 'item1', 'To honor God with our substance', 'text'),
('give', 'principles', 'item2', 'To support the work of the ministry', 'text'),
('give', 'principles', 'item3', 'To advance the Kingdom of God on earth', 'text'),
('give', 'principles', 'item4', 'To experience God''s provision and blessing', 'text'),
('give', 'cta', 'badge', 'OUR IMPACT', 'text'),
('give', 'cta', 'title', 'Your Giving Makes a Difference', 'text'),
('give', 'cta', 'subtitle', 'Every gift supports our mission to spread the Gospel and serve our community. Thank you for your generosity.', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);
