<?php
namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Branch;

class BranchPublicController extends Controller {

    public function index() {
        $branchModel = new Branch();
        $branches = $branchModel->publicList();

        $this->view('public/branches', [
            'title' => 'Our Centres',
            'branches' => $branches
        ]);
    }

    public function show($slug) {
        $branchModel = new Branch();
        $branch = $branchModel->findBySlug($slug);
        if (!$branch) {
            $this->redirect('/branches');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT * FROM events
            WHERE branch_id = ? AND start_datetime >= CURDATE()
            ORDER BY start_datetime ASC
            LIMIT 4
        ");
        $stmt->execute([$branch['id']]);
        $events = $stmt->fetchAll();

        $this->view('public/branch_show', [
            'title' => $branch['name'],
            'branch' => $branch,
            'events' => $events
        ]);
    }

    public function events($slug) {
        $branchModel = new Branch();
        $branch = $branchModel->findBySlug($slug);
        if (!$branch) {
            $this->redirect('/branches');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT * FROM events
            WHERE branch_id = ? AND start_datetime >= CURDATE()
            ORDER BY start_datetime ASC
        ");
        $stmt->execute([$branch['id']]);
        $events = $stmt->fetchAll();

        $this->view('public/branch_events', [
            'title' => $branch['name'] . ' Events',
            'branch' => $branch,
            'events' => $events
        ]);
    }

    public function eventDetails($slug, $eventSlug) {
        $branchModel = new Branch();
        $branch = $branchModel->findBySlug($slug);
        if (!$branch) {
            $this->redirect('/branches');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM events WHERE branch_id = ? AND slug = ? LIMIT 1");
        $stmt->execute([$branch['id'], $eventSlug]);
        $event = $stmt->fetch();
        if (!$event) {
            $this->redirect('/branches/' . $branch['slug'] . '/events');
        }

        $stmt = $db->prepare("
            SELECT * FROM events
            WHERE branch_id = ? AND id != ? AND start_datetime >= CURDATE()
            ORDER BY start_datetime ASC
            LIMIT 3
        ");
        $stmt->execute([$branch['id'], $event['id']]);
        $upcoming_events = $stmt->fetchAll();

        $content = \App\Models\PageContent::getPageContent('events');

        $this->view('public/event_details', [
            'title' => $event['title'],
            'event' => $event,
            'branch' => $branch,
            'upcoming_events' => $upcoming_events,
            'content' => $content
        ]);
    }
}
