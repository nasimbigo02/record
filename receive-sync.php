<?php
$secret_token = "MyEyesAreAlwaysOnYou007!";

if (!isset($_POST['token']) || $_POST['token'] !== $secret_token) {
    http_response_code(403);
    die("Unauthorized access.");
}

if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
    $uploadfile = __DIR__ . '/temp_backup.sql';
    
    if (move_uploaded_file($_FILES['sql_file']['tmp_name'], $uploadfile)) {
        // Use an external free database or your cloud database credentials here
        $db_host = getenv('DB_HOST');
        $db_name = getenv('DB_NAME');
        $db_user = getenv('DB_USER');
        $db_pass = getenv('DB_PASS');

        try {
            $sql_content = file_get_contents($uploadfile);
            @unlink($uploadfile);

            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
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