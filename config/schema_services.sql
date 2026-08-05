-- Service Planning & Rostering Schema

-- 1. Services Table (The actual Sunday/Midweek service instance)
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) DEFAULT 'Sunday Service',
    service_date DATE NOT NULL,
    service_time TIME NOT NULL,
    type ENUM('Sunday Service', 'Midweek Service', 'Special Event') DEFAULT 'Sunday Service',
    series_title VARCHAR(200),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Service Roster (Who is serving on a specific service)
CREATE TABLE IF NOT EXISTS service_roster (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    member_id INT NOT NULL,
    team_id INT NOT NULL, -- Links to small_groups where type='Ministry Team'
    role VARCHAR(100), -- e.g. "Worship Leader", "Camera 1", "Greeter"
    status ENUM('pending', 'confirmed', 'declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES small_groups(id) ON DELETE CASCADE,
    UNIQUE KEY (service_id, member_id, team_id) -- One role per team per service
);
