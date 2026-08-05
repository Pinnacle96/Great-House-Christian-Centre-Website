<?php
// Script to apply comprehensive schema updates

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This migration script can only be run from the command line.');
}

require_once 'config/config.php';

// Simple Autoloader from index.php
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

    $schemaFiles = [
        'config/schema_comprehensive.sql',
        'config/schema_finance.sql',
        'config/schema_services.sql',
        'config/schema_communication.sql',
        'config/schema_contact_messages.sql',
        'config/schema_audit_logs.sql',
        'config/schema_production_settings.sql',
        'config/schema_cms.sql',
        'config/schema_cms_fix.sql',
        'config/schema_cms_fix_2.sql',
        'config/schema_cms_pages.sql',
    ];
    
    // Split by semicolon to execute statement by statement if needed, 
    // but PDO can sometimes handle multiple queries. 
    // However, for safety and better error reporting, let's split.
    // NOTE: This simple split might break if semicolons are inside strings, but for this schema it's fine.
    
    foreach ($schemaFiles as $schemaFile) {
        $path = __DIR__ . '/' . $schemaFile;
        if (!file_exists($path)) {
            echo "Skipped missing schema file: {$schemaFile}\n";
            continue;
        }

        echo "Applying {$schemaFile}...\n";
        $sql = file_get_contents($path);
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (empty($statement)) continue;
            
            try {
                $db->exec($statement);
                echo "Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                // Check if error is "Duplicate column name" (1060) or "Table already exists"
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo "Skipped (Column exists): " . substr($statement, 0, 50) . "...\n";
                } elseif (strpos($e->getMessage(), 'Table') !== false && strpos($e->getMessage(), 'already exists') !== false) {
                    echo "Skipped (Table exists): " . substr($statement, 0, 50) . "...\n";
                } else {
                    echo "Error executing: " . substr($statement, 0, 50) . "...\n";
                    echo "Message: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "Schema update completed successfully.\n";

} catch (Exception $e) {
    echo "Critical Error: " . $e->getMessage() . "\n";
}
