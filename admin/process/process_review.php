<?php
header('Content-Type: application/json');
require_once '../database/config.php';

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Ambil JSON dari request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validasi data
if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

try {
    // Insert review
    $stmt = $pdo->prepare("
        INSERT INTO review (pesan_id, customer_name, rating, review_text, status) 
        VALUES (?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $data['orderId'],
        $data['customerName'],
        $data['rating'],
        $data['reviewText']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Review submitted successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit review: ' . $e->getMessage()
    ]);
}