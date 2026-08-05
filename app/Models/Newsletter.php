<?php
namespace App\Models;

use App\Core\Model;

class Newsletter extends Model {
    protected $table = 'newsletters';

    public function subscribe($email) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO newsletters (email) VALUES (:email)");
        return $stmt->execute(['email' => $email]);
    }

    public function getAllSubscribers() {
        return $this->findAll();
    }
}
