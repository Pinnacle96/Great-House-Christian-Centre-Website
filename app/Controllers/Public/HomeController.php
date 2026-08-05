<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Models\Branch;

class HomeController extends Controller {
    
    public function index() {
        $content = \App\Models\PageContent::getPageContent('home');
        
        // Fetch upcoming events
        $db = \App\Core\Database::getInstance()->getConnection();
        $headquarters = (new Branch())->headquarters();
        $branchId = (int)($headquarters['id'] ?? 0);
        $stmt = $db->prepare("
            SELECT *
            FROM events
            WHERE start_datetime >= CURDATE()
                AND (? = 0 OR branch_id = ?)
            ORDER BY start_datetime ASC
            LIMIT 3
        ");
        $stmt->execute([$branchId, $branchId]);
        $events = $stmt->fetchAll();

        $stmt = $db->prepare("
            SELECT *
            FROM sermons
            WHERE (? = 0 OR branch_id = ?)
            ORDER BY date_preached DESC, created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$branchId, $branchId]);
        $latestSermon = $stmt->fetch();

        $this->view('public/home', [
            'title' => 'Welcome Home',
            'content' => $content,
            'events' => $events,
            'latestSermon' => $latestSermon
        ]);
    }

    public function about() {
        $content = \App\Models\PageContent::getPageContent('about');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM team_members ORDER BY display_order ASC");
        $team = $stmt->fetchAll();

        $this->view('public/about', [
            'title' => 'About Us',
            'content' => $content,
            'team' => $team
        ]);
    }

    public function services() {
        $content = \App\Models\PageContent::getPageContent('services');
        $this->view('public/services', [
            'title' => 'Services & Programs',
            'content' => $content
        ]);
    }

    public function ministries() {
        $content = \App\Models\PageContent::getPageContent('ministries');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM departments ORDER BY name ASC");
        $ministries = $stmt->fetchAll();

        $this->view('public/ministries', [
            'title' => 'Our Ministries',
            'content' => $content,
            'ministries' => $ministries
        ]);
    }

    public function groups() {
        $content = \App\Models\PageContent::getPageContent('groups');
        
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM small_groups ORDER BY name ASC");
        $groups = $stmt->fetchAll();

        $this->view('public/groups', [
            'title' => 'Small Groups',
            'content' => $content,
            'groups' => $groups
        ]);
    }

    public function sermons() {
        // Fetch recent sermons from DB
        $db = \App\Core\Database::getInstance()->getConnection();
        $headquarters = (new Branch())->headquarters();
        $branchId = (int)($headquarters['id'] ?? 0);
        $stmt = $db->prepare("
            SELECT *
            FROM sermons
            WHERE (? = 0 OR branch_id = ?)
            ORDER BY date_preached DESC
            LIMIT 6
        ");
        $stmt->execute([$branchId, $branchId]);
        $sermons = $stmt->fetchAll();
        
        $content = \App\Models\PageContent::getPageContent('sermons');
        
        $this->view('public/sermons', [
            'title' => 'Sermons & Media',
            'sermons' => $sermons,
            'content' => $content
        ]);
    }

    public function events() {
        $db = \App\Core\Database::getInstance()->getConnection();
        $headquarters = (new Branch())->headquarters();
        $branchId = (int)($headquarters['id'] ?? 0);
        $stmt = $db->prepare("
            SELECT *
            FROM events
            WHERE start_datetime >= CURDATE()
                AND (? = 0 OR branch_id = ?)
            ORDER BY start_datetime ASC
        ");
        $stmt->execute([$branchId, $branchId]);
        $events = $stmt->fetchAll();

        $content = \App\Models\PageContent::getPageContent('events');

        $this->view('public/events', [
            'title' => 'Upcoming Events',
            'events' => $events,
            'content' => $content
        ]);
    }

    public function eventDetails($slug) {
        $db = \App\Core\Database::getInstance()->getConnection();
        $headquarters = (new Branch())->headquarters();
        $branchId = (int)($headquarters['id'] ?? 0);
        
        // Fetch event details from the headquarters branch.
        $stmt = $db->prepare("
            SELECT *
            FROM events
            WHERE slug = ?
                AND (? = 0 OR branch_id = ?)
            LIMIT 1
        ");
        $stmt->execute([$slug, $branchId, $branchId]);
        $event = $stmt->fetch();

        if (!$event) {
            // Redirect to events page if event not found
            header('Location: ' . APP_URL . '/events');
            exit;
        }

        // Fetch other upcoming events from the headquarters branch.
        $stmt = $db->prepare("
            SELECT *
            FROM events
            WHERE id != ?
                AND start_datetime >= CURDATE()
                AND (? = 0 OR branch_id = ?)
            ORDER BY start_datetime ASC
            LIMIT 3
        ");
        $stmt->execute([$event['id'], $branchId, $branchId]);
        $upcoming_events = $stmt->fetchAll();

        $content = \App\Models\PageContent::getPageContent('events');

        $this->view('public/event_details', [
            'title' => $event['title'],
            'event' => $event,
            'upcoming_events' => $upcoming_events,
            'content' => $content
        ]);
    }

    public function give() {
        $content = \App\Models\PageContent::getPageContent('give');
        $this->view('public/give', [
            'title' => 'Give Online',
            'content' => $content
        ]);
    }

    public function contact() {
        $content = \App\Models\PageContent::getPageContent('contact');
        $this->view('public/contact', [
            'title' => 'Contact Us',
            'content' => $content
        ]);
    }
}
