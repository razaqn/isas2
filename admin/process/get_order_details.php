<?php
// Pastikan tidak ada output sebelum header
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set header JSON
header('Content-Type: application/json');

try {
    require_once '../database/config.php';
    
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid order ID');
    }

    $orderId = (int)$_GET['id'];

    // Get order details
    $orderStmt = $pdo->prepare("
        SELECT p.*, 
               GROUP_CONCAT(CONCAT(m.nama, ' (', pd.quantity, ')') SEPARATOR ', ') as order_items
        FROM pesan p
        LEFT JOIN pesan_detail pd ON p.id = pd.pesan_id
        LEFT JOIN menu m ON pd.menu_id = m.id
        WHERE p.id = ?
        GROUP BY p.id
    ");
    $orderStmt->execute([$orderId]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Order not found');
    }

    // Get order items detail
    $itemsStmt = $pdo->prepare("
        SELECT pd.quantity, pd.price as item_price, 
               m.nama, m.price as menu_price
        FROM pesan_detail pd
        JOIN menu m ON pd.menu_id = m.id
        WHERE pd.pesan_id = ?
    ");
    $itemsStmt->execute([$orderId]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items
    ]);

} catch (Exception $e) {
    error_log('Error getting order details: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>