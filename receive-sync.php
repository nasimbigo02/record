<?php
$secret_token = "MyEyesAreAlwaysOnYou007!";

if (!isset($_POST['token']) || $_POST['token'] !== $secret_token) {
    http_response_code(403);
    die("Unauthorized access.");
}

if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
    $uploadfile = __DIR__ . '/temp_backup.sql';
    
    if (move_uploaded_file($_FILES['sql_file']['tmp_name'], $uploadfile)) {
        
        // --- FIX IS HERE: Ensure host is your remote database server, NOT 'localhost' ---
        $db_host = 'sql102.infinityfree.com'; 
        $db_name = 'if0_43440338_ranks';
        $db_user = 'if0_43440338';
        $db_pass = 'Record2026';

        try {
            $sql_content = file_get_contents($uploadfile);
            @unlink($uploadfile);

            // Using explicit TCP port 3306 forces network connection instead of local socket file search
            $dsn = "mysql:host=$db_host;port=3306;dbname=$db_name;charset=utf8mb4";
            
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