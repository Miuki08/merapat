<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'manager' && $_SESSION['role'] != 'officer')) {
    header("Location: user_login_register.php");
    exit();
}

if (isset($_GET['file'])) {
    $filePath = 'payment_proofs/' . basename($_GET['file']);
    
    // Check if file exists
    if (file_exists($filePath)) {
        // Get file info
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);
        
        // Set headers for download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Content-Length: $fileSize");
        header("Cache-Control: private");
        header("Pragma: public");
        
        // Clear output buffer
        ob_clean();
        flush();
        
        // Read the file
        readfile($filePath);
        exit;
    } else {
        // File not found
        header("Location: payment_view.php?error=file_not_found");
        exit();
    }
} else {
    // No file specified
    header("Location: payment_view.php");
    exit();
}
?>