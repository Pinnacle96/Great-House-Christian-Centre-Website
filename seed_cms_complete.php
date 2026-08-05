<?php
require_once 'config/config.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";

    $data = [
        // --- ABOUT PAGE ---
        ['about', 'hero', 'badge', 'OUR STORY & MISSION', 'text'],
        ['about', 'hero', 'title', 'About <span class="text-brand-gold relative inline-block">GHCC</span>', 'richtext'],
        ['about', 'hero', 'subtitle', 'Discover who we are, what we believe, and where God is taking us as a family of faith united in purpose and power.', 'text'],
        ['about', 'hero', 'image', 'assets/images/about-hero.jpg', 'image'],
        
        ['about', 'vision', 'title', 'Our Vision', 'text'],
        ['about', 'vision', 'content', 'To raise a people of power, purpose, and passion for God\'s Kingdom.', 'text'],
        
        ['about', 'mission', 'title', 'Our Mission', 'text'],
        ['about', 'mission', 'content', 'To preach the undiluted Word of God and disciple believers into maturity.', 'text'],

        ['about', 'core_values', 'badge', 'OUR CORE VALUES', 'text'],
        ['about', 'core_values', 'title', 'What We <span class="text-brand-green">Stand For</span>', 'richtext'],
        ['about', 'core_values', 'subtitle', 'These foundational principles guide everything we do as a church family, reflecting our commitment to God and His people.', 'text'],
        
        ['about', 'core_values', 'card1_emoji', '👨‍👩‍👧‍👦', 'text'],
        ['about', 'core_values', 'card1_title', 'Family Community', 'text'],
        ['about', 'core_values', 'card1_content', 'We believe in the power of authentic community and treating every member as part of God\'s family.', 'text'],
        
        ['about', 'core_values', 'card2_emoji', '👑', 'text'],
        ['about', 'core_values', 'card2_title', 'Kingdom Culture', 'text'],
        ['about', 'core_values', 'card2_content', 'We uphold the values and principles of God\'s Kingdom in our daily lives and relationships.', 'text'],
        
        ['about', 'core_values', 'card3_emoji', '✨', 'text'],
        ['about', 'core_values', 'card3_title', 'Divine Purpose', 'text'],
        ['about', 'core_values', 'card3_content', 'We help every individual discover and walk in their God-ordained purpose and destiny.', 'text'],

        ['about', 'leadership', 'badge', 'MEET OUR LEADERS', 'text'],
        ['about', 'leadership', 'title', 'Our <span class="text-brand-green">Leadership</span> Team', 'richtext'],
        ['about', 'leadership', 'subtitle', 'Godly leaders dedicated to serving and guiding our church family with wisdom, integrity, and love.', 'text'],
        
        ['about', 'cta', 'badge', 'JOIN OUR FAMILY', 'text'],
        ['about', 'cta', 'title', 'Ready to Become Part of<br><span class="text-brand-gold">Something Great?</span>', 'richtext'],
        ['about', 'cta', 'subtitle', 'Whether you\'re looking for a spiritual home or just want to learn more about us, we can\'t wait to welcome you with open arms.', 'text'],

        // --- SERVICES PAGE ---
        ['services', 'hero', 'badge', 'WORSHIP WITH US', 'text'],
        ['services', 'hero', 'title', 'Services & Programs', 'text'],
        ['services', 'hero', 'subtitle', 'Join us as we gather to worship, learn, and grow together in Christ through our various weekly gatherings and ministries.', 'text'],
        ['services', 'hero', 'image', 'assets/images/services-hero.jpg', 'image'],
        
        ['services', 'weekly_services', 'badge', 'WEEKLY GATHERINGS', 'text'],
        ['services', 'weekly_services', 'title', 'Weekly Services', 'text'],
        ['services', 'weekly_services', 'subtitle', 'Join our vibrant community for powerful worship and life-changing messages every week.', 'text'],
        
        ['services', 'ministries', 'badge', 'OUR COMMUNITIES', 'text'],
        ['services', 'ministries', 'title', 'Ministries & Programs', 'text'],
        ['services', 'ministries', 'subtitle', 'Find your place and grow with others in our specialized ministry departments.', 'text'],
        
        ['services', 'cta', 'badge', 'JOIN THE FAMILY', 'text'],
        ['services', 'cta', 'title', 'Ready to Join Us This Week?', 'text'],
        ['services', 'cta', 'subtitle', 'Experience the life-changing power of worship and community. We can\'t wait to welcome you home.', 'text'],

        // --- SERMONS PAGE ---
        ['sermons', 'hero', 'badge', 'SERMONS & TEACHINGS', 'text'],
        ['sermons', 'hero', 'title', 'Sermons & Media', 'text'],
        ['sermons', 'hero', 'subtitle', 'Watch and listen to life-transforming messages from our ministry, designed to empower and equip you for kingdom impact.', 'text'],
        ['sermons', 'hero', 'image', 'assets/images/sermons-hero.jpg', 'image'],
        
        ['sermons', 'featured', 'badge', 'FEATURED MESSAGE', 'text'],
        ['sermons', 'featured', 'title', 'Walking in Dominion', 'text'],
        ['sermons', 'featured', 'description', 'Discover the divine principles of authority and how to exercise your spiritual dominion in every area of life. This life-transforming message explores the depth of God\'s power in the believer.', 'richtext'],
        
        ['sermons', 'sermon_grid', 'badge', 'RECENT MESSAGES', 'text'],
        ['sermons', 'sermon_grid', 'title', 'Browse More Sermons', 'text'],
        ['sermons', 'sermon_grid', 'subtitle', 'Browse our collection of recent teachings and messages from our weekly services and special programs.', 'text'],
        
        ['sermons', 'cta', 'badge', 'STAY CONNECTED', 'text'],
        ['sermons', 'cta', 'title', 'Never Miss a <span class="text-brand-gold">Message</span>', 'richtext'],
        ['sermons', 'cta', 'subtitle', 'Subscribe to our sermon updates and get notified when new life-transforming messages are available.', 'text'],

        // --- EVENTS PAGE ---
        ['events', 'hero', 'badge', 'UPCOMING EVENTS', 'text'],
        ['events', 'hero', 'title', 'Church Events', 'text'],
        ['events', 'hero', 'subtitle', 'Join us for life-changing experiences, community gatherings, and special services throughout the year.', 'text'],
        ['events', 'hero', 'image', 'assets/images/events-hero.jpg', 'image'],
        
        ['events', 'events_intro', 'badge', 'WHAT\'S HAPPENING', 'text'],
        ['events', 'events_intro', 'title', 'Upcoming Events', 'text'],
        ['events', 'events_intro', 'subtitle', 'Discover our upcoming programs, conferences, and special services.', 'text'],
        
        ['events', 'no_events', 'title', 'More Events Coming Soon', 'text'],
        ['events', 'no_events', 'subtitle', 'We\'re constantly planning new events and programs. Check back regularly or subscribe to stay updated.', 'text'],
        
        ['events', 'cta', 'badge', 'EVENT INQUIRIES', 'text'],
        ['events', 'cta', 'title', 'Want to Host an Event?', 'text'],
        ['events', 'cta', 'subtitle', 'Contact our administration office for inquiries regarding facility use and event partnerships.', 'text'],

        // --- CONTACT PAGE ---
        ['contact', 'hero', 'badge', 'GET IN TOUCH', 'text'],
        ['contact', 'hero', 'title', 'Contact Us', 'text'],
        ['contact', 'hero', 'subtitle', 'We\'re here to serve and support you on your spiritual journey. Reach out to us anytime.', 'text'],
        ['contact', 'hero', 'image', 'assets/images/contact-hero.jpg', 'image'],
        
        ['contact', 'connect', 'badge', 'CONNECT WITH US', 'text'],
        ['contact', 'connect', 'title', 'Get in <span class="text-brand-green">Touch</span>', 'richtext'],
        ['contact', 'connect', 'subtitle', 'Reach out to us for any questions, prayer requests, or to learn more about our ministry and how you can get involved.', 'text'],
        
        ['contact', 'contact_details', 'location_title', 'Our Location', 'text'],
        ['contact', 'contact_details', 'address', '123 Faith Street, Grace City, GC 10001', 'text'],
        ['contact', 'contact_details', 'map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63272.19626629624!2d4.687356448632801!3d7.627925400000014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1038185607a01587%3A0xfc355a72c10c11ec!2sGREAT%20HOUSE%20CHRISTIAN%20CENTER!5e0!3m2!1sen!2sng!4v1772541096361!5m2!1sen!2sng', 'text'],
        ['contact', 'contact_details', 'phone_title', 'Phone Number', 'text'],
        ['contact', 'contact_details', 'phone', '+123 456 7890', 'text'],
        ['contact', 'contact_details', 'email_title', 'Email Address', 'text'],
        ['contact', 'contact_details', 'email', 'info@ghcc.org', 'text'],
        
        ['contact', 'prayer_card', 'title', 'Need Prayer?', 'text'],
        ['contact', 'prayer_card', 'subtitle', 'We believe in the power of prayer. Send us your prayer requests and our prayer team will intercede for you.', 'text'],
        ['contact', 'prayer_card', 'cta_text', 'SUBMIT PRAYER REQUEST', 'text'],
        
        ['contact', 'cta', 'badge', 'VISIT US TODAY', 'text'],
        ['contact', 'cta', 'title', 'Experience God\'s Presence This Weekend', 'text'],
        ['contact', 'cta', 'subtitle', 'Join us for a transformative experience of worship, word, and community. We can\'t wait to welcome you home.', 'text'],

        // --- GIVE PAGE ---
        ['give', 'hero', 'badge', 'GENEROSITY', 'text'],
        ['give', 'hero', 'title', 'Give Online', 'text'],
        ['give', 'hero', 'subtitle', '"God loves a cheerful giver." — 2 Corinthians 9:7', 'text'],
        ['give', 'hero', 'image', 'assets/images/give-hero.jpg', 'image'],
        
        ['give', 'giving_form', 'badge', 'SECURE GIVING', 'text'],
        ['give', 'giving_form', 'title', 'Make a Donation', 'text'],
        ['give', 'giving_form', 'subtitle', 'Support the work of God through your generous giving. Your contribution helps us reach more lives.', 'text'],
        
        ['give', 'bank_details', 'account_name', 'Great House Christian Centre', 'text'],
        ['give', 'bank_details', 'account_number', '1234567890', 'text'],
        ['give', 'bank_details', 'bank_name', 'Zenith Bank PLC', 'text'],
        
        ['give', 'principles', 'title', 'Why We Give', 'text'],
        ['give', 'principles', 'item1', 'To honor God with our substance', 'text'],
        ['give', 'principles', 'item2', 'To support the work of the ministry', 'text'],
        ['give', 'principles', 'item3', 'To advance the Kingdom of God on earth', 'text'],
        ['give', 'principles', 'item4', 'To experience God\'s provision and blessing', 'text'],
        
        ['give', 'cta', 'badge', 'OUR IMPACT', 'text'],
        ['give', 'cta', 'title', 'Your Giving Makes a Difference', 'text'],
        ['give', 'cta', 'subtitle', 'Every gift supports our mission to spread the Gospel and serve our community. Thank you for your generosity.', 'text'],
    ];

    $stmt = $db->prepare("INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE content_value=VALUES(content_value), content_type=VALUES(content_type)");

    foreach ($data as $row) {
        $stmt->execute($row);
        echo "Seeded: {$row[0]} -> {$row[1]} -> {$row[2]}\n";
    }

    echo "Complete CMS seeding finished successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
