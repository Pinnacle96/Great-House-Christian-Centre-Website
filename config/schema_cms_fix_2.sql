-- Fix CMS Schema Part 2: Seed Home Page Slider & Sections

-- Home Hero Slider (3 slides)
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('home', 'hero', 'slide1_title', 'Welcome Home', 'text'),
('home', 'hero', 'slide1_subtitle', 'A place where everyone is welcome and lives are transformed.', 'text'),
('home', 'hero', 'slide1_image', 'assets/images/hero-bg.jpg', 'image'),

('home', 'hero', 'slide2_title', 'Experience God', 'text'),
('home', 'hero', 'slide2_subtitle', 'Join us for powerful worship and life-changing messages.', 'text'),
('home', 'hero', 'slide2_image', 'assets/images/worship.jpg', 'image'),

('home', 'hero', 'slide3_title', 'Find Your Purpose', 'text'),
('home', 'hero', 'slide3_subtitle', 'Discover your gifts and make a difference in our community.', 'text'),
('home', 'hero', 'slide3_image', 'assets/images/community.jpg', 'image')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Home About Preview
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('home', 'about_preview', 'badge', 'OUR STORY', 'text'),
('home', 'about_preview', 'title', 'A House of Power & Purpose', 'text'),
('home', 'about_preview', 'content', 'Great House Christian Centre is committed to raising believers who walk in power, live with purpose, and carry the passion of God''s Kingdom. We are a family of faith dedicated to transforming lives through the Gospel of Jesus Christ.', 'text'),
('home', 'about_preview', 'stat_number', '1000+', 'text'),
('home', 'about_preview', 'stat_label', 'Lives Transformed', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Home Services Section
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('home', 'services_section', 'badge', 'WEEKLY SERVICES', 'text'),
('home', 'services_section', 'title', 'Join Us In Worship', 'text'),
('home', 'services_section', 'subtitle', 'Experience the presence of God through our dynamic services designed to equip, empower, and encourage you in your faith journey.', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);

-- Home CTA Section
INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES 
('home', 'cta', 'badge', 'JOIN OUR COMMUNITY', 'text'),
('home', 'cta', 'title', 'Ready to Experience God''s Presence?', 'text'),
('home', 'cta', 'subtitle', 'We can''t wait to welcome you to our church family. Plan your visit today and experience the transformative love and power of God in our services.', 'text'),
('home', 'cta', 'stat1_number', '20+', 'text'),
('home', 'cta', 'stat1_label', 'Active Ministries', 'text'),
('home', 'cta', 'stat2_number', '1000+', 'text'),
('home', 'cta', 'stat2_label', 'Lives Impacted', 'text'),
('home', 'cta', 'stat3_number', '5+', 'text'),
('home', 'cta', 'stat3_label', 'Global Branches', 'text')
ON DUPLICATE KEY UPDATE content_value=VALUES(content_value);
