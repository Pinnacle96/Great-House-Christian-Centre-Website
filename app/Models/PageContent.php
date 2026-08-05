<?php
namespace App\Models;

use App\Core\Model;

class PageContent extends Model {
    protected $table = 'page_contents';

    public static function getPageContent($pageName) {
        $model = new self();
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
}