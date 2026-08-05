<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

$pages = [
    'services' => [
        'hero' => [
            'title' => 'Services & Programs',
            'subtitle' => 'Join us as we gather to worship, learn, and grow together in Christ through our various weekly gatherings and ministries.',
            'badge' => 'WORSHIP WITH US'
        ],
        'weekly_services' => [
            'title' => 'Weekly Services',
            'subtitle' => 'Join our vibrant community for powerful worship and life-changing messages every week.',
            'badge' => 'WEEKLY GATHERINGS'
        ],
        'ministries' => [
            'title' => 'Ministries & Programs',
            'subtitle' => 'Find your place and grow with others in our specialized ministry departments.',
            'badge' => 'OUR COMMUNITIES'
        ],
        'cta' => [
            'title' => 'Ready to Join Us This Week?',
            'subtitle' => "Experience the life-changing power of worship and community. We can't wait to welcome you home.",
            'badge' => 'JOIN THE FAMILY'
        ]
    ],
    'sermons' => [
        'hero' => [
            'title' => 'Sermons & Media',
            'subtitle' => 'Watch and listen to life-transforming messages from our ministry, designed to empower and equip you for kingdom impact.',
            'badge' => 'SERMONS & TEACHINGS'
        ],
        'featured' => [
            'title' => 'Walking in Dominion',
            'description' => "Discover the divine principles of authority and how to exercise your spiritual dominion in every area of life. This life-transforming message explores the depth of God's power in the believer.",
            'badge' => 'FEATURED MESSAGE'
        ],
        'sermon_grid' => [
            'title' => 'Browse More Sermons',
            'subtitle' => 'Browse our collection of recent teachings and messages from our weekly services and special programs.',
            'badge' => 'RECENT MESSAGES'
        ],
        'cta' => [
            'title' => 'Ready to Experience More?',
            'subtitle' => 'Join us live or watch our previous services to grow in your walk with God.',
            'badge' => 'JOIN US LIVE'
        ]
    ],
    'events' => [
        'hero' => [
            'title' => 'Church Events',
            'subtitle' => 'Join us for life-changing experiences, community gatherings, and special services throughout the year.',
            'badge' => 'UPCOMING EVENTS'
        ],
        'events_intro' => [
            'title' => 'Upcoming Events',
            'subtitle' => 'Discover our upcoming programs, conferences, and special services.',
            'badge' => "WHAT'S HAPPENING"
        ],
        'no_events' => [
            'title' => 'More Events Coming Soon',
            'subtitle' => "We're constantly planning new events and programs. Check back regularly or subscribe to stay updated.",
            'badge' => 'STAY TUNED'
        ],
        'cta' => [
            'title' => 'Want to Host an Event?',
            'subtitle' => 'Contact our administration office for inquiries regarding facility use and event partnerships.',
            'badge' => 'EVENT INQUIRIES'
        ]
    ],
    'give' => [
        'hero' => [
            'title' => 'Give Online',
            'subtitle' => '"God loves a cheerful giver." — 2 Corinthians 9:7',
            'badge' => 'GENEROSITY'
        ],
        'giving_form' => [
            'title' => 'Make a Donation',
            'subtitle' => 'Support the work of God through your generous giving. Your contribution helps us reach more lives.',
            'badge' => 'SECURE GIVING'
        ],
        'principles' => [
            'title' => 'Why We Give',
            'subtitle' => 'Biblical principles of giving and stewardship in the Kingdom of God.',
            'badge' => 'GIVING PRINCIPLES',
            'item1' => 'To honor God with our substance',
            'item2' => 'To support the work of the ministry',
            'item3' => 'To advance the Kingdom of God on earth',
            'item4' => "To experience God's provision and blessing"
        ],
        'cta' => [
            'title' => 'Your Giving Makes a Difference',
            'subtitle' => 'Every gift supports our mission to spread the Gospel and serve our community. Thank you for your generosity.',
            'badge' => 'OUR IMPACT'
        ]
    ],
    'contact' => [
        'hero' => [
            'title' => 'Contact Us',
            'subtitle' => "We're here to serve and support you on your spiritual journey. Reach out to us anytime.",
            'badge' => 'GET IN TOUCH'
        ],
        'connect' => [
            'title' => 'Get in Touch',
            'subtitle' => 'Reach out to us for any questions, prayer requests, or to learn more about our ministry and how you can get involved.',
            'badge' => 'CONNECT WITH US'
        ],
        'contact_details' => [
            'location_title' => 'Our Location',
            'address' => '123 Faith Street, Grace City, GC 10001',
            'phone_title' => 'Phone Number',
            'phone' => '+123 456 7890',
            'email_title' => 'Email Address',
            'email' => 'info@ghcc.org'
        ],
        'prayer_card' => [
            'title' => 'Need Prayer?',
            'subtitle' => 'We believe in the power of prayer. Send us your prayer requests and our prayer team will intercede for you.',
            'cta_text' => 'SUBMIT PRAYER REQUEST'
        ],
        'cta' => [
            'title' => "Experience God's Presence This Weekend",
            'subtitle' => "Join us for a transformative experience of worship, word, and community. We can't wait to welcome you home.",
            'badge' => 'VISIT US TODAY'
        ]
    ]
];

foreach ($pages as $page_name => $sections) {
    foreach ($sections as $section_name => $content) {
        foreach ($content as $key => $value) {
            // Check if exists
            $stmt = $db->prepare("SELECT id FROM page_contents WHERE page_name = ? AND section_name = ? AND content_key = ?");
            $stmt->execute([$page_name, $section_name, $key]);
            if (!$stmt->fetch()) {
                $stmt = $db->prepare("INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type) VALUES (?, ?, ?, ?, 'text')");
                $stmt->execute([$page_name, $section_name, $key, $value]);
                echo "Inserted: $page_name -> $section_name -> $key\n";
            } else {
                echo "Skipped: $page_name -> $section_name -> $key (already exists)\n";
            }
        }
    }
}

echo "Seeding completed!\n";
