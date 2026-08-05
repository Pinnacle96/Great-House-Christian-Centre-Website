-- Production configuration settings used by the admin settings screen.

INSERT INTO settings (setting_key, setting_value, setting_type) VALUES
('site_email', 'info@ghcc.org', 'text'),
('site_favicon', 'assets/logo/ghcc_logo.png', 'image'),
('paystack_public_key', '', 'text'),
('paystack_secret_key', '', 'password'),
('smtp_host', 'smtp.gmail.com', 'text'),
('smtp_port', '587', 'text'),
('smtp_encryption', 'tls', 'text'),
('smtp_user', '', 'text'),
('smtp_pass', '', 'password')
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);
