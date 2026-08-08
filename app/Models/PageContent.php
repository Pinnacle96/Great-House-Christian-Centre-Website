<?php
namespace App\Models;

use App\Core\Model;

class PageContent extends Model {
    protected $table = 'page_contents';

    public static function getPageContent($pageName) {
        $model = new self();
        $model->ensureDefaultContent($pageName);

        $stmt = $model->db->prepare("SELECT id, section_name, content_key, content_value, content_type FROM page_contents WHERE page_name = :page_name");
        $stmt->execute(['page_name' => $pageName]);
        $results = $stmt->fetchAll();
        
        $content = [];
        foreach ($results as $row) {
            $content[$row['section_name']][$row['content_key']] = [
                'id' => $row['id'],
                'value' => $row['content_value'],
                'type' => $row['content_type']
            ];
        }
        return $content;
    }

    public function updateContent($id, $value) {
        $stmt = $this->db->prepare("UPDATE page_contents SET content_value = :value WHERE id = :id");
        return $stmt->execute(['value' => $value, 'id' => $id]);
    }

    private function ensureDefaultContent($pageName) {
        $defaults = self::defaultContent();
        if (!isset($defaults[$pageName])) {
            return;
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM page_contents WHERE page_name = ?");
        $stmt->execute([$pageName]);
        if ((int)$stmt->fetchColumn() > 0) {
            return;
        }

        $insert = $this->db->prepare("
            INSERT INTO page_contents (page_name, section_name, content_key, content_value, content_type)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($defaults[$pageName] as $row) {
            $insert->execute([$pageName, $row[0], $row[1], $row[2], $row[3] ?? 'text']);
        }
    }

    private static function defaultContent() {
        return [
            'home' => [
                ['hero', 'slide1_title', 'Great House Christian Center'],
                ['hero', 'slide1_subtitle', 'Helping men find fulfilment in life through Christ.'],
                ['hero', 'slide2_title', 'A Place of Transformation'],
                ['hero', 'slide2_subtitle', 'A community where faith, hope, and love come together to transform lives and build community.'],
                ['hero', 'slide3_title', 'Faith. Hope. Love.'],
                ['hero', 'slide3_subtitle', 'A church family where faith grows, lives are transformed, and love is shared with everyone.'],
                ['about_preview', 'badge', 'A PLACE OF TRANSFORMATION'],
                ['about_preview', 'title', 'Helping Men Find *Fulfilment*'],
                ['about_preview', 'content', 'Great House Christian Center exists to help men find fulfilment in life through Christ. We are reaching, raising, equipping, and empowering people for kingdom influence.'],
                ['about_preview', 'stat_number', '7'],
                ['about_preview', 'stat_label', 'Active Centres'],
                ['services_intro', 'badge', 'SERVICE TIMES'],
                ['services_intro', 'title', 'Join Us In *Worship*'],
                ['services_intro', 'subtitle', 'Sundays Fulfilment Service at 9:00 AM, Tuesdays WWGS at 5:00 PM, and Fridays Travail Service at 5:30 PM.'],
                ['latest_sermon', 'badge', 'LATEST MESSAGE'],
                ['latest_sermon', 'title', 'Word for the *Season*'],
                ['cta', 'badge', 'JOIN OUR COMMUNITY'],
                ['cta', 'title', 'A Place of *Transformation*'],
                ['cta', 'subtitle', 'Plan your visit, connect with a centre, or join us online for worship, teaching, prayer, and community.'],
                ['cta', 'stat1_number', '12'],
                ['cta', 'stat1_label', 'Ministries'],
                ['cta', 'stat2_number', '1500+'],
                ['cta', 'stat2_label', 'Weekly Attendance'],
                ['cta', 'stat3_number', '7'],
                ['cta', 'stat3_label', 'Centres'],
            ],
            'about' => [
                ['hero', 'badge', 'OUR STORY & MISSION'],
                ['hero', 'title', 'About *GHCC*'],
                ['hero', 'subtitle', 'Great House Christian Center is a place of transformation, community, and kingdom influence.'],
                ['vision', 'title', 'Our Vision'],
                ['vision', 'content', "Becoming a community of godly and excellent believers doing exploits and reflecting God's glory."],
                ['mission', 'title', 'Our Mission'],
                ['mission', 'content', 'Reaching, raising, equipping, and empowering men in their walk and work with God towards kingdom influence.'],
                ['core_values', 'badge', 'CORE VALUES'],
                ['core_values', 'title', 'What We *Stand For*'],
                ['core_values', 'subtitle', "These values shape our life together as a church family and keep us focused on Christ's mandate."],
                ['core_values', 'card1_title', 'Community'],
                ['core_values', 'card1_content', 'We grow better together in authentic community and loving relationships.'],
                ['core_values', 'card2_title', 'Kingdom Culture'],
                ['core_values', 'card2_content', "We live with a kingdom-first mindset, guided by God's Word and the Holy Spirit."],
                ['core_values', 'card3_title', 'Purpose'],
                ['core_values', 'card3_content', 'We help people discover fulfilment, purpose, and excellent service in Christ.'],
                ['leadership', 'badge', 'OUR LEADERS'],
                ['leadership', 'title', 'Our *Leadership* Team'],
                ['leadership', 'subtitle', 'Pastors and leaders serving the GHCC family with vision, care, and biblical teaching.'],
                ['cta', 'badge', 'READY TO VISIT?'],
                ['cta', 'title', 'Experience Great House Christian Center'],
                ['cta', 'subtitle', 'Join us this Sunday for worship, teaching, and community.'],
            ],
            'services' => [
                ['hero', 'badge', 'WORSHIP WITH US'],
                ['hero', 'title', 'Services & Programs'],
                ['hero', 'subtitle', 'Join our vibrant community for powerful worship and life-changing messages every week.'],
                ['weekly_services', 'badge', 'WEEKLY GATHERINGS'],
                ['weekly_services', 'title', 'Weekly Services'],
                ['weekly_services', 'subtitle', 'Sundays Fulfilment Service at 9:00 AM, Tuesdays WWGS at 5:00 PM, and Fridays Travail Service at 5:30 PM.'],
                ['ministries', 'badge', 'OUR COMMUNITIES'],
                ['ministries', 'title', 'Ministries & Programs'],
                ['ministries', 'subtitle', 'Find your place and grow with others in our ministry departments.'],
                ['cta', 'badge', 'JOIN THE FAMILY'],
                ['cta', 'title', 'Ready to Join Us This Week?'],
                ['cta', 'subtitle', 'Experience worship, teaching, prayer, and community with GHCC.'],
            ],
            'sermons' => [
                ['hero', 'badge', 'SERMONS & TEACHINGS'],
                ['hero', 'title', 'Watch & Listen'],
                ['hero', 'subtitle', 'Experience our services live or catch up on past messages.'],
                ['intro', 'title', 'Message Archive'],
                ['intro', 'content', 'Listen again to messages from GHCC and keep growing in faith and fulfilment.'],
                ['featured', 'badge', 'FEATURED MESSAGE'],
                ['featured', 'title', 'Word for the Season'],
                ['featured', 'description', 'Browse recent teachings and messages from our services.', 'richtext'],
                ['sermon_grid', 'badge', 'RECENT MESSAGES'],
                ['sermon_grid', 'title', 'Browse More Sermons'],
                ['sermon_grid', 'subtitle', 'Browse our collection of recent teachings and messages.'],
                ['cta', 'badge', 'STAY CONNECTED'],
                ['cta', 'title', 'Never Miss a *Message*'],
                ['cta', 'subtitle', 'Subscribe to sermon updates and stay connected to the GHCC family.'],
            ],
            'events' => [
                ['hero', 'badge', 'UPCOMING EVENTS'],
                ['hero', 'title', 'Church Events'],
                ['hero', 'subtitle', 'Join us for life-changing experiences, community gatherings, and special services.'],
                ['events_intro', 'badge', "WHAT'S HAPPENING"],
                ['events_intro', 'title', 'Upcoming Events'],
                ['events_intro', 'subtitle', 'Discover our upcoming programs, conferences, and special services.'],
                ['no_events', 'title', 'More Events Coming Soon'],
                ['no_events', 'subtitle', 'Check back regularly for new GHCC events and programs.'],
                ['cta', 'badge', 'EVENT INQUIRIES'],
                ['cta', 'title', 'Want to Host an Event?'],
                ['cta', 'subtitle', 'Contact our administration office for event and facility inquiries.'],
            ],
            'give' => [
                ['hero', 'badge', 'GENEROSITY'],
                ['hero', 'title', 'Give Online'],
                ['hero', 'subtitle', '"God loves a cheerful giver." - 2 Corinthians 9:7'],
                ['giving_form', 'badge', 'SECURE GIVING'],
                ['giving_form', 'title', 'Make a Donation'],
                ['giving_form', 'subtitle', 'Support the work of God through your generous giving. Your contribution helps GHCC reach, raise, equip, and empower more lives.'],
                ['bank_details', 'account_name', 'Great House Christian Centre'],
                ['bank_details', 'account_number', '1234567890'],
                ['bank_details', 'bank_name', 'Zenith Bank PLC'],
                ['principles', 'title', 'Why We Give'],
                ['principles', 'item1', 'To honor God with our substance'],
                ['principles', 'item2', 'To support the work of ministry'],
                ['principles', 'item3', "To advance God's kingdom influence"],
                ['principles', 'item4', "To participate in God's work through generosity"],
                ['cta', 'badge', 'OUR IMPACT'],
                ['cta', 'title', 'Your Giving Makes a Difference'],
                ['cta', 'subtitle', 'Every gift helps strengthen worship, discipleship, care, and outreach across the GHCC family.'],
            ],
            'contact' => [
                ['hero', 'badge', 'GET IN TOUCH'],
                ['hero', 'title', 'Contact *GHCC*'],
                ['hero', 'subtitle', "We're here to serve, support, and help you connect with the GHCC family."],
                ['connect', 'badge', 'CONNECT WITH US'],
                ['connect', 'title', 'Get in *Touch*'],
                ['connect', 'subtitle', 'Reach out for questions, prayer requests, directions, or information about worship services and centres.'],
                ['contact_details', 'location_title', 'Our Location'],
                ['contact_details', 'address', 'The Fulfilment Place, 7 Raimi Omole Street, Imo, Ilesa, Osun State'],
                ['contact_details', 'map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63272.19626629624!2d4.687356448632801!3d7.627925400000014!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1038185607a01587%3A0xfc355a72c10c11ec!2sGREAT%20HOUSE%20CHRISTIAN%20CENTER!5e0!3m2!1sen!2sng!4v1772541096361!5m2!1sen!2sng'],
                ['contact_details', 'phone_title', 'Phone Number'],
                ['contact_details', 'phone', '0811 417 3016'],
                ['contact_details', 'email_title', 'Email Address'],
                ['contact_details', 'email', 'info@ghccng.org'],
                ['prayer_card', 'title', 'Need Prayer?'],
                ['prayer_card', 'subtitle', 'Our prayer team is available to pray with you after services or by appointment. You can also submit prayer requests online.'],
                ['prayer_card', 'cta_text', 'SUBMIT PRAYER REQUEST'],
                ['cta', 'badge', 'VISIT US TODAY'],
                ['cta', 'title', "Experience God's Presence This Weekend"],
                ['cta', 'subtitle', 'Sundays Fulfilment Service: 09:00 AM. Tuesdays WWGS: 05:00 PM. Fridays Travail Service: 05:30 PM.'],
            ],
        ];
    }
}
