<?php
$secret_token = "MyEyesAreAlwaysOnYou007!";

if (!isset($_POST['token']) || $_POST['token'] !== $secret_token) {
    http_response_code(403);
    die("Unauthorized access.");
}

if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
    $uploadfile = __DIR__ . '/temp_backup.sql';
    
    if (move_uploaded_file($_FILES['sql_file']['tmp_name'], $uploadfile)) {
        
        // --- YOUR SUPABASE CREDENTIALS ---
        $db_host = 'db.nzcsphrvbsryxrjwofci.supabase.co'; // Change to your Supabase host
        $db_port = '5432';
        $db_name = 'postgres';
        $db_user = 'postgres';                    // Change to your Supabase database user
        $db_pass = 'Record@CE2026';            // Change to your actual password

        try {
            $sql_content = file_get_contents($uploadfile);
            @unlink($uploadfile); // Clean up temp file

            // Connect to PostgreSQL via PDO
            $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
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