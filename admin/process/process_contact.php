<?php
require_once '../database/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basic validation
    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($contact)) {
        $errors[] = "Contact number is required";
    }
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact (name, contact_number, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $contact, $message]);
            
            // Redirect back with success message
            header("Location: ../../index.php?contact_status=success#contact");
            exit();
        } catch(PDOException $e) {
            // Redirect back with error message
            header("Location: ../../index.php?contact_status=error#contact");
            exit();
        }
    } else {
        // Redirect back with validation errors
        $error_string = implode(",", $errors);
        header("Location: ../../index.php?contact_status=validation&errors=" . urlencode($error_string) . "#contact");
        exit();
    }
}
?>