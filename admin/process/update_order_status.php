<?php
// Pastikan ini di paling atas file
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set header JSON sebelum output apapun
header('Content-Type: application/json');

try {
    // Include config setelah set header
    require_once '../database/config.php';
    
    // Validasi request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Baca raw input
    $input = file_get_contents('php://input');
    if (!$input) {
        throw new Exception('No input data received');
    }

    // Decode JSON
    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data: ' . json_last_error_msg());
    }

    // Validasi input
    if (!isset($data['orderId']) || !isset($data['status'])) {
        throw new Exception('Missing required parameters');
    }

    // Sanitasi input
    $orderId = filter_var($data['orderId'], FILTER_VALIDATE_INT);
    if ($orderId === false) {
        throw new Exception('Invalid order ID');
    }

    $status = filter_var($data['status'], FILTER_SANITIZE_STRING);
    $allowedStatuses = ['pending', 'completed', 'cancelled'];
    if (!in_array($status, $allowedStatuses)) {
        throw new Exception('Invalid status value');
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE pesan SET status = ? WHERE id = ?");
    if (!$stmt->execute([$status, $orderId])) {
        throw new Exception('Failed to update database');
    }

    // Check if any rows were affected
    if ($stmt->rowCount() === 0) {
        throw new Exception('Order not found');
    }

    // Successful response
    echo json_encode([
        'success' => true,
        'message' => 'Order status updated successfully'
    ]);

} catch (Exception $e) {
    // Log error untuk debugging
    error_log('Order status update error: ' . $e->getMessage());
    
    // Send error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>