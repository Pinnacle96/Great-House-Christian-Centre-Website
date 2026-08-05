<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

use App\Core\Database;

function slugify($text) {
    // Replace non-letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database.\n";

    // 1. Add slug column
    try {
        $db->exec("ALTER TABLE events ADD COLUMN slug VARCHAR(255) UNIQUE AFTER title");
        echo "Added 'slug' column.\n";
    } catch (PDOException $e) {
        echo "Column 'slug' might already exist or error: " . $e->getMessage() . "\n";
    }

    // 2. Populate slugs for existing events
    $stmt = $db->query("SELECT id, title FROM events");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updateStmt = $db->prepare("UPDATE events SET slug = :slug WHERE id = :id");

    foreach ($events as $event) {
        $slug = slugify($event['title']);
        // Ensure uniqueness (simple check for existing records)
        $originalSlug = $slug;
        $counter = 1;
        while (true) {
            $check = $db->prepare("SELECT id FROM events WHERE slug = ? AND id != ?");
            $check->execute([$slug, $event['id']]);
            if ($check->fetch()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            } else {
                break;
            }
        }

        $updateStmt->execute(['slug' => $slug, 'id' => $event['id']]);
        echo "Updated event '{$event['title']}' with slug: $slug\n";
    }

    echo "Migration completed successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
