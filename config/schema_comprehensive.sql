-- Comprehensive Church Management System Schema Update

-- 1. Families Table (Grouping members into households)
CREATE TABLE IF NOT EXISTS families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- e.g., "The Smith Family"
    address VARCHAR(255),
    city VARCHAR(100),
    state VARCHAR(50),
    zip VARCHAR(20),
    primary_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Member Notes Table (Pastoral Care)
CREATE TABLE IF NOT EXISTS member_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    author_id INT NOT NULL, -- user_id of the pastor/admin
    note_content TEXT NOT NULL,
    visibility ENUM('private', 'admin', 'pastor', 'leader') DEFAULT 'private',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Small Groups Table
CREATE TABLE IF NOT EXISTS small_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    leader_id INT, -- user_id or member_id
    schedule_info VARCHAR(255), -- e.g. "Wednesdays at 7pm"
    location VARCHAR(255),
    type ENUM('Small Group', 'Ministry Team', 'Class') DEFAULT 'Small Group',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 4. Group Members Table
CREATE TABLE IF NOT EXISTS group_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    member_id INT NOT NULL,
    role ENUM('member', 'leader', 'co-leader') DEFAULT 'member',
    joined_at DATE,
    FOREIGN KEY (group_id) REFERENCES small_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE KEY (group_id, member_id)
);

-- 5. Attendance Table (Service/Event Level)
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT, -- For specific events
    service_date DATE NOT NULL, -- For regular Sunday services
    service_type ENUM('sunday_service', 'midweek_service', 'special_event') DEFAULT 'sunday_service',
    count INT DEFAULT 0, -- Headcount
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
);

-- 6. Individual Attendance Table (Check-in)
CREATE TABLE IF NOT EXISTS individual_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attendance_id INT NOT NULL,
    member_id INT NOT NULL,
    status ENUM('present', 'absent', 'excused') DEFAULT 'present',
    check_in_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attendance_id) REFERENCES attendance(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    UNIQUE KEY (attendance_id, member_id)
);

-- 7. Volunteer Roles Table
CREATE TABLE IF NOT EXISTS volunteer_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- e.g. Usher, Greeter, Media
    department_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- 8. Volunteer Assignments Table
CREATE TABLE IF NOT EXISTS volunteer_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    role_id INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    assigned_at DATE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES volunteer_roles(id) ON DELETE CASCADE,
    UNIQUE KEY (member_id, role_id)
);

-- 9. Add New Columns to Members Table
-- Note: Check if columns exist before adding (handled by application logic usually, but here we use simple ALTERs assuming fresh install or update)
-- We will handle "duplicate column" errors gracefully in PHP script or use a procedure if supported, but simple ALTER is standard for this context.

ALTER TABLE members ADD COLUMN family_id INT NULL;
ALTER TABLE members ADD COLUMN family_role ENUM('Head', 'Spouse', 'Child', 'Other') DEFAULT 'Head';
ALTER TABLE members ADD COLUMN gender ENUM('Male', 'Female');
ALTER TABLE members ADD COLUMN marital_status ENUM('Single', 'Married', 'Divorced', 'Widowed');
ALTER TABLE members ADD COLUMN membership_type ENUM('Guest', 'Regular Attender', 'Member', 'Leader') DEFAULT 'Guest';
ALTER TABLE members ADD COLUMN baptism_date DATE;
ALTER TABLE members ADD COLUMN salvation_date DATE;
ALTER TABLE members ADD COLUMN occupation VARCHAR(100);
ALTER TABLE members ADD COLUMN employer VARCHAR(100);
ALTER TABLE members ADD COLUMN source VARCHAR(50);
ALTER TABLE members ADD CONSTRAINT fk_family FOREIGN KEY (family_id) REFERENCES families(id) ON DELETE SET NULL;
