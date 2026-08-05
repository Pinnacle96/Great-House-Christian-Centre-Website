<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

function upsertSetting(PDO $db, $key, $value, $type = 'text') {
    $stmt = $db->prepare("
        INSERT INTO settings (setting_key, setting_value, setting_type)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ");
    $stmt->execute([$key, $value, $type]);
}

function upsertContent(PDO $db, $page, $section, $key, $value, $type = 'text') {
    $stmt = $db->prepare("
        INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE content_value = VALUES(content_value), content_type = VALUES(content_type)
    ");
    $stmt->execute([$page, $section, $key, $value, $type]);
}

function upsertTeamMember(PDO $db, $name, $role, $bio, $displayOrder) {
    $stmt = $db->prepare("SELECT id FROM team_members WHERE name = ? LIMIT 1");
    $stmt->execute([$name]);
    $id = $stmt->fetchColumn();

    if ($id) {
        $stmt = $db->prepare("UPDATE team_members SET role = ?, bio = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$role, $bio, $displayOrder, $id]);
        return;
    }

    $stmt = $db->prepare("
        INSERT INTO team_members (name, role, bio, display_order)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$name, $role, $bio, $displayOrder]);
}

function deleteTeamMember(PDO $db, $name) {
    $stmt = $db->prepare("DELETE FROM team_members WHERE name = ?");
    $stmt->execute([$name]);
}

function upsertSermon(PDO $db, $branchId, $title, $preacher, $datePreached, $description) {
    $stmt = $db->prepare("SELECT id FROM sermons WHERE title = ? AND preacher = ? LIMIT 1");
    $stmt->execute([$title, $preacher]);
    $id = $stmt->fetchColumn();

    if ($id) {
        $stmt = $db->prepare("UPDATE sermons SET branch_id = ?, date_preached = ?, description = ? WHERE id = ?");
        $stmt->execute([$branchId, $datePreached, $description, $id]);
        return;
    }

    $stmt = $db->prepare("
        INSERT INTO sermons (branch_id, title, preacher, date_preached, description)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$branchId, $title, $preacher, $datePreached, $description]);
}

try {
    $db = Database::getInstance()->getConnection();

    $stmt = $db->query("SELECT id FROM branches WHERE slug = 'ghcc-ilesa' LIMIT 1");
    $ilesaId = $stmt->fetchColumn();
    if (!$ilesaId) {
        $stmt = $db->query("SELECT id FROM branches WHERE is_active = 1 ORDER BY id ASC LIMIT 1");
        $ilesaId = $stmt->fetchColumn();
    }

    upsertSetting($db, 'site_name', 'Great House Christian Center');
    upsertSetting($db, 'site_email', 'info@ghccng.org');
    upsertSetting($db, 'contact_email', 'info@ghccng.org');
    upsertSetting($db, 'contact_phone', '(+234) 703 090 7726');
    upsertSetting($db, 'address', '7 Raimi Omole Street, Ilesa 233285, Osun State, Nigeria');

    $content = [
        ['home', 'hero', 'slide1_title', 'Great House Christian Center'],
        ['home', 'hero', 'slide1_subtitle', 'Helping men find fulfilment in life through Christ.'],
        ['home', 'hero', 'slide2_title', 'A Place of Transformation'],
        ['home', 'hero', 'slide2_subtitle', 'A community where faith, hope, and love come together to transform lives and build community.'],
        ['home', 'hero', 'slide3_title', 'Faith. Hope. Love.'],
        ['home', 'hero', 'slide3_subtitle', 'A church family where faith grows, lives are transformed, and love is shared with everyone.'],
        ['home', 'about_preview', 'badge', 'A PLACE OF TRANSFORMATION'],
        ['home', 'about_preview', 'title', 'Helping Men Find *Fulfilment*'],
        ['home', 'about_preview', 'content', 'Great House Christian Center exists to help men find fulfilment in life through Christ. We are reaching, raising, equipping, and empowering people for kingdom influence.'],
        ['home', 'about_preview', 'stat_number', '7'],
        ['home', 'about_preview', 'stat_label', 'Active Centres'],
        ['home', 'services_intro', 'badge', 'SERVICE TIMES'],
        ['home', 'services_intro', 'title', 'Join Us In *Worship*'],
        ['home', 'services_intro', 'subtitle', 'Sundays Fulfilment Service at 9:00 AM, Tuesdays WWGS at 5:00 PM, and Fridays Travail Service at 5:30 PM.'],
        ['home', 'latest_sermon', 'badge', 'LATEST MESSAGE'],
        ['home', 'latest_sermon', 'title', 'Word for the *Season*'],
        ['home', 'cta', 'badge', 'JOIN OUR COMMUNITY'],
        ['home', 'cta', 'title', 'A Place of *Transformation*'],
        ['home', 'cta', 'subtitle', 'Plan your visit, connect with a centre, or join us online for worship, teaching, prayer, and community.'],
        ['home', 'cta', 'stat1_number', '12'],
        ['home', 'cta', 'stat1_label', 'Ministries'],
        ['home', 'cta', 'stat2_number', '1500+'],
        ['home', 'cta', 'stat2_label', 'Weekly Attendance'],
        ['home', 'cta', 'stat3_number', '7'],
        ['home', 'cta', 'stat3_label', 'Centres'],

        ['about', 'hero', 'title', 'About *GHCC*'],
        ['about', 'hero', 'subtitle', 'Great House Christian Center is a place of transformation, community, and kingdom influence.'],
        ['about', 'vision', 'title', 'Our Vision'],
        ['about', 'vision', 'content', "Becoming a community of godly and excellent believers doing exploits and reflecting God's glory."],
        ['about', 'mission', 'title', 'Our Mission'],
        ['about', 'mission', 'content', 'Reaching, raising, equipping, and empowering men in their walk and work with God towards kingdom influence.'],
        ['about', 'core_values', 'badge', 'CORE VALUES'],
        ['about', 'core_values', 'title', 'What We *Stand For*'],
        ['about', 'core_values', 'subtitle', "These values shape our life together as a church family and keep us focused on Christ's mandate."],
        ['about', 'core_values', 'card1_content', 'We grow better together in authentic community and loving relationships.'],
        ['about', 'core_values', 'card2_content', "We live with a kingdom-first mindset, guided by God's Word and the Holy Spirit."],
        ['about', 'core_values', 'card3_content', 'We help people discover fulfilment, purpose, and excellent service in Christ.'],
        ['about', 'leadership', 'badge', 'OUR LEADERS'],
        ['about', 'leadership', 'title', 'Our *Leadership* Team'],
        ['about', 'leadership', 'subtitle', 'Pastors and leaders serving the GHCC family with vision, care, and biblical teaching.'],
        ['about', 'cta', 'badge', 'READY TO VISIT?'],
        ['about', 'cta', 'title', 'Experience Great House Christian Center'],
        ['about', 'cta', 'subtitle', 'Join us this Sunday for worship, teaching, and community.'],

        ['contact', 'hero', 'badge', 'GET IN TOUCH'],
        ['contact', 'hero', 'title', 'Contact *GHCC*'],
        ['contact', 'hero', 'subtitle', "We're here to serve, support, and help you connect with the GHCC family."],
        ['contact', 'connect', 'badge', 'CONNECT WITH US'],
        ['contact', 'connect', 'title', 'Get in *Touch*'],
        ['contact', 'connect', 'subtitle', 'Reach out for questions, prayer requests, directions, or information about worship services and centres.'],
        ['contact', 'contact_details', 'location_title', 'Our Location'],
        ['contact', 'contact_details', 'address', '7 Raimi Omole Street, Ilesa 233285, Osun State, Nigeria'],
        ['contact', 'contact_details', 'phone_title', 'Phone Number'],
        ['contact', 'contact_details', 'phone', '(+234) 703 090 7726'],
        ['contact', 'contact_details', 'email_title', 'Email Address'],
        ['contact', 'contact_details', 'email', 'info@ghccng.org'],
        ['contact', 'prayer_card', 'title', 'Need Prayer?'],
        ['contact', 'prayer_card', 'subtitle', 'Our prayer team is available to pray with you after services or by appointment. You can also submit prayer requests online.'],
        ['contact', 'prayer_card', 'cta_text', 'SUBMIT PRAYER REQUEST'],
        ['contact', 'cta', 'badge', 'VISIT US TODAY'],
        ['contact', 'cta', 'title', "Experience God's Presence This Weekend"],
        ['contact', 'cta', 'subtitle', 'Sundays Fulfilment Service: 09:00 AM. Tuesdays WWGS: 05:00 PM. Fridays Travail Service: 05:30 PM.'],

        ['give', 'hero', 'badge', 'GENEROSITY'],
        ['give', 'hero', 'title', 'Give Online'],
        ['give', 'hero', 'subtitle', '"God loves a cheerful giver." - 2 Corinthians 9:7'],
        ['give', 'giving_form', 'subtitle', 'Support the work of God through your generous giving. Your contribution helps GHCC reach, raise, equip, and empower more lives.'],
        ['give', 'principles', 'item1', 'To honor God with our substance'],
        ['give', 'principles', 'item2', 'To support the work of ministry'],
        ['give', 'principles', 'item3', "To advance God's kingdom influence"],
        ['give', 'principles', 'item4', "To participate in God's work through generosity"],
        ['give', 'cta', 'title', 'Your Giving Makes a Difference'],
        ['give', 'cta', 'subtitle', 'Every gift helps strengthen worship, discipleship, care, and outreach across the GHCC family.'],

        ['sermons', 'hero', 'title', 'Watch & Listen'],
        ['sermons', 'hero', 'subtitle', 'Experience our services live or catch up on past messages.'],
        ['sermons', 'intro', 'title', 'Message Archive'],
        ['sermons', 'intro', 'content', 'Listen again to messages from GHCC and keep growing in faith and fulfilment.'],
    ];

    foreach ($content as $row) {
        upsertContent($db, $row[0], $row[1], $row[2], $row[3], $row[4] ?? 'text');
    }

    deleteTeamMember($db, 'Pastor Dr. Adewusi Bibilari');
    deleteTeamMember($db, 'Pastor Samuel Adetunji Ogundeyibi');

    upsertTeamMember($db, 'Pastor Segun Oduyebo', 'Founder & Senior Pastor', 'Provides visionary leadership and biblical teaching for the GHCC family.', 1);
    upsertTeamMember($db, 'Dr. Bibiloni Ademusi', 'Resident Pastor, GHCC Ibadan', 'Serves GHCC Ibadan at The Fulfilment Place, Tollgate, Ibadan.', 2);
    upsertTeamMember($db, 'Mr. Abraham', 'Resident Pastor / Contact, GHCC Ikeja', 'Serves GHCC Ikeja at The Fulfilment Place, 3 Toyin Street, Ikeja.', 3);
    upsertTeamMember($db, 'Mrs. Adenike Ige', 'Resident Pastor / Contact, GHCC Lekki', 'Serves GHCC Lekki at The Fulfilment Place, Amode Area, Lekki.', 4);
    upsertTeamMember($db, 'Pastor Mrs. Abiola Oriade', 'Resident Pastor, GHCC Ile-Ife', 'Serves GHCC Ile-Ife at The Fulfilment Place, Mayfair, Ile-Ife.', 5);
    upsertTeamMember($db, 'Pastor Dayo Jubee', 'Resident Pastor, GHCC Osogbo', 'Serves GHCC Osogbo at The Fulfilment Place, NUJ Hall, Osogbo.', 6);
    upsertTeamMember($db, 'Pastor Favour', 'Resident Pastor, GHCC Potters Assembly', 'Serves GHCC Potters Assembly at The Fulfilment Place, Ido-Ijesa.', 7);
    upsertTeamMember($db, 'Pastor Peter Okon', 'Resident Pastor, GHCC Ilesa', 'Serves GHCC Ilesa at The Fulfilment Place, 7 Raimi Omole Street, Ilesa.', 8);

    if ($ilesaId) {
        upsertSermon(
            $db,
            (int)$ilesaId,
            'Voice Of God',
            'Pastor Segun Oduyebo',
            '2026-01-24',
            'A GHCC message from Pastor Segun Oduyebo.'
        );
    }

    echo "GHCC live-site content seed complete.\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
