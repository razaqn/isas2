<?php
header('Content-Type: application/json');
require_once 'config.php';

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
    $pdo->beginTransaction();

    // Insert ke tabel pesan
    $stmt = $pdo->prepare("
        INSERT INTO pesan (customer_name, customer_email, customer_contact, total_amount, notes, status) 
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $data['customerName'],
        $data['customerEmail'],
        $data['customerContact'],
        $data['totalAmount'],
        $data['notes']
    ]);
    
    $orderId = $pdo->lastInsertId();

    // Insert ke tabel pesan_detail
    $stmt = $pdo->prepare("
        INSERT INTO pesan_detail (pesan_id, menu_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($data['items'] as $item) {
        $stmt->execute([
            $orderId,
            $item['id'],
            $item['quantity'],
            $item['price']
        ]);

        // Update jumlah_beli di tabel menu
        $updateStmt = $pdo->prepare("
            UPDATE menu 
            SET jumlah_beli = jumlah_beli + ? 
            WHERE id = ?
        ");
        $updateStmt->execute([$item['quantity'], $item['id']]);
    }

    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'orderId' => $orderId
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process order: ' . $e->getMessage()
    ]);
}