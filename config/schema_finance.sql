-- Financial Stewardship Schema

-- 1. Funds Table (Designated giving categories)
CREATE TABLE IF NOT EXISTS funds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- e.g. "Tithes", "Building Fund", "Missions"
    description TEXT,
    is_tax_deductible BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Pledges Table (Capital campaigns)
CREATE TABLE IF NOT EXISTS pledges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    fund_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL, -- Total pledged amount
    start_date DATE,
    end_date DATE,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (fund_id) REFERENCES funds(id) ON DELETE CASCADE
);

-- 3. Update Donations Table (Link to funds and members)
-- Adding columns if they don't exist (using safe ALTERs in script later, but defining structure here)
-- Note: 'type' column in original schema is replaced/enhanced by 'fund_id' relation for better reporting

-- We will execute these ALTERs in the update script:
-- ALTER TABLE donations ADD COLUMN member_id INT NULL;
-- ALTER TABLE donations ADD COLUMN fund_id INT NULL;
-- ALTER TABLE donations ADD COLUMN payment_method ENUM('card', 'cash', 'check', 'transfer') DEFAULT 'card';
-- ALTER TABLE donations ADD COLUMN notes TEXT;
-- ALTER TABLE donations ADD CONSTRAINT fk_donation_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE SET NULL;
-- ALTER TABLE donations ADD CONSTRAINT fk_donation_fund FOREIGN KEY (fund_id) REFERENCES funds(id) ON DELETE SET NULL;

-- Seed Default Funds
INSERT INTO funds (name, description) VALUES 
('Tithes', 'General tithes for church operations'),
('Offerings', 'General offerings'),
('Building Fund', 'For future building projects'),
('Missions', 'Supporting global and local missions')
ON DUPLICATE KEY UPDATE name=name;
