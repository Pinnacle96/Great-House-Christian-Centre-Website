-- Seed CMS Content for Inner Pages

-- Services Page
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('services', 'hero', 'title', 'Our Services', 'text'),
('services', 'hero', 'subtitle', 'Join us for a time of worship, word, and fellowship.', 'text'),
('services', 'hero', 'image', 'assets/images/services-hero.jpg', 'image'),
('services', 'intro', 'title', 'Weekly Gatherings', 'text'),
('services', 'intro', 'content', 'We believe in the power of gathering together. Our services are designed to help you encounter God and build lasting relationships with others.', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Sermons Page
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('sermons', 'hero', 'title', 'Sermons', 'text'),
('sermons', 'hero', 'subtitle', 'Listen to life-changing messages from God''s word.', 'text'),
('sermons', 'hero', 'image', 'assets/images/sermons-hero.jpg', 'image'),
('sermons', 'intro', 'title', 'Message Archive', 'text'),
('sermons', 'intro', 'content', 'Missed a service? Want to re-listen to a powerful message? Browse our archive of sermons below.', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Events Page
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('events', 'hero', 'title', 'Upcoming Events', 'text'),
('events', 'hero', 'subtitle', 'See what''s happening at Great House Christian Centre.', 'text'),
('events', 'hero', 'image', 'assets/images/events-hero.jpg', 'image'),
('events', 'intro', 'title', 'Get Involved', 'text'),
('events', 'intro', 'content', 'There is always something happening at GHCC. From conferences to community outreach, find out how you can participate.', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Give Page
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('give', 'hero', 'title', 'Give', 'text'),
('give', 'hero', 'subtitle', 'Honor the Lord with your wealth.', 'text'),
('give', 'hero', 'image', 'assets/images/give-hero.jpg', 'image'),
('give', 'intro', 'title', 'Why We Give', 'text'),
('give', 'intro', 'content', 'Giving is an act of worship. Your generosity helps us continue our mission of transforming lives and spreading the Gospel.', 'text'),
('give', 'bank_transfer', 'title', 'Bank Transfer', 'text'),
('give', 'bank_transfer', 'details', 'Bank Name: GTBank\nAccount Name: Great House Christian Centre\nAccount Number: 0123456789', 'richtext')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Contact Page
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('contact', 'hero', 'title', 'Contact Us', 'text'),
('contact', 'hero', 'subtitle', 'We would love to hear from you.', 'text'),
('contact', 'hero', 'image', 'assets/images/contact-hero.jpg', 'image'),
('contact', 'info', 'address', '123 Church Street, Lagos, Nigeria', 'text'),
('contact', 'info', 'phone', '+234 800 123 4567', 'text'),
('contact', 'info', 'email', 'info@ghcc.org', 'text'),
('contact', 'map', 'embed_code', '<iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>', 'code')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);
