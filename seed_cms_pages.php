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
        // Services Page
        ['services', 'hero', 'title', 'Our Services', 'text'],
        ['services', 'hero', 'subtitle', 'Join us for a time of worship, word, and fellowship.', 'text'],
        ['services', 'hero', 'image', 'assets/images/services-hero.jpg', 'image'],
        ['services', 'intro', 'title', 'Weekly Gatherings', 'text'],
        ['services', 'intro', 'content', 'We believe in the power of gathering together. Our services are designed to help you encounter God and build lasting relationships with others.', 'richtext'],

        // Sermons Page
        ['sermons', 'hero', 'title', 'Sermons', 'text'],
        ['sermons', 'hero', 'subtitle', 'Listen to life-changing messages from God\'s word.', 'text'],
        ['sermons', 'hero', 'image', 'assets/images/sermons-hero.jpg', 'image'],
        ['sermons', 'intro', 'title', 'Message Archive', 'text'],
        ['sermons', 'intro', 'content', 'Missed a service? Want to re-listen to a powerful message? Browse our archive of sermons below.', 'richtext'],

        // Events Page
        ['events', 'hero', 'title', 'Upcoming Events', 'text'],
        ['events', 'hero', 'subtitle', 'See what\'s happening at Great House Christian Centre.', 'text'],
        ['events', 'hero', 'image', 'assets/images/events-hero.jpg', 'image'],
        ['events', 'intro', 'title', 'Get Involved', 'text'],
        ['events', 'intro', 'content', 'There is always something happening at GHCC. From conferences to community outreach, find out how you can participate.', 'richtext'],

        // Give Page
        ['give', 'hero', 'title', 'Give', 'text'],
        ['give', 'hero', 'subtitle', 'Honor the Lord with your wealth.', 'text'],
        ['give', 'hero', 'image', 'assets/images/give-hero.jpg', 'image'],
        ['give', 'intro', 'title', 'Why We Give', 'text'],
        ['give', 'intro', 'content', 'Giving is an act of worship. Your generosity helps us continue our mission of transforming lives and spreading the Gospel.', 'richtext'],
        ['give', 'bank_transfer', 'title', 'Bank Transfer', 'text'],
        ['give', 'bank_transfer', 'details', "Bank Name: GTBank\nAccount Name: Great House Christian Centre\nAccount Number: 0123456789", 'richtext'],

        // Contact Page
        ['contact', 'hero', 'title', 'Contact Us', 'text'],
        ['contact', 'hero', 'subtitle', 'We would love to hear from you.', 'text'],
        ['contact', 'hero', 'image', 'assets/images/contact-hero.jpg', 'image'],
        ['contact', 'info', 'address', '123 Church Street, Lagos, Nigeria', 'text'],
        ['contact', 'info', 'phone', '+234 800 123 4567', 'text'],
        ['contact', 'info', 'email', 'info@ghcc.org', 'text'],
        ['contact', 'map', 'embed_code', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.952912260219!2d3.375295414770757!3d6.527638695278792!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8b2ae68280c1%3A0xdc9e59a73066c845!2sLagos!5e0!3m2!1sen!2sng!4v1625647000000!5m2!1sen!2sng" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>', 'code']
    ];

    $stmt = $db->prepare("INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE content_value=VALUES(content_value), content_type=VALUES(content_type)");

    foreach ($data as $row) {
        $stmt->execute($row);
        echo "Seeded: {$row[0]} -> {$row[1]} -> {$row[2]}\n";
    }

    echo "CMS Pages seeding completed successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
