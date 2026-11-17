<?php
$secret_token = "MyEyesAreAlwaysOnYou007!";

if (!isset($_POST['token']) || $_POST['token'] !== $secret_token) {
    http_response_code(403);
    die("Unauthorized access.");
}

if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
    $uploadfile = __DIR__ . '/temp_backup.sql';
    
    if (move_uploaded_file($_FILES['sql_file']['tmp_name'], $uploadfile)) {
        
        // --- UPDATED SUPABASE CREDENTIALS (IPv4 Safe) ---
        $db_host = 'aws-0-us-east-1.pooler.supabase.com'; // Use the pooler domain instead of direct project ref
        $db_port = '6543';                               // Supabase pooler transaction port (IPv4 friendly)
        $db_name = 'postgres';
        $db_user = 'postgres.nzcsphrvbsryxrjwofci';       // Pooler username format: postgres.[project-ref]
        $db_pass = 'Record@CE2026';            // Your actual database password

        try {
            $sql_content = file_get_contents($uploadfile);
            @unlink($uploadfile); // Clean up temp file

            // Connect to PostgreSQL via IPv4-forced PDO settings
            $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;options='--client_encoding=UTF8'";
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            $pdo->exec($sql_content);
            echo "SUCCESS";

        } catch (Exception $e) {
            echo "Import failed: " . $e->getMessage();
        }
    } else {
        echo "File move failed.";
    }
} else {
    echo "No file uploaded.";
}
?>