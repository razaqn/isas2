<?php
require_once 'check.php';
checkAdminLogin();
require_once 'database/config.php';

// Get orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Get total orders
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pesan");
    $totalOrders = $stmt->fetch()['total'];
    $totalPages = ceil($totalOrders / $limit);

    // Get orders with details - PERBAIKAN PADA QUERY
    $query = "
        SELECT p.*, 
               COUNT(pd.id) as total_items,
               GROUP_CONCAT(CONCAT(m.nama, ' (', pd.quantity, ')') SEPARATOR ', ') as order_items
        FROM pesan p
        LEFT JOIN pesan_detail pd ON p.id = pd.pesan_id
        LEFT JOIN menu m ON pd.menu_id = m.id
        GROUP BY p.id
        ORDER BY p.order_date DESC
        LIMIT $offset, $limit
    ";
    
    $stmt = $pdo->query($query);
    $orders = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar {
            background-color: #FC51D7;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .order-status {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-completed { background-color: #dcfce7; color: #166534; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="bg-light">
     <!-- Navbar -->
     <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Osteria del Mare - Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Orders</h2>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($order['customer_email']); ?></small><br>
                                    <small><?php echo htmlspecialchars($order['customer_contact']); ?></small>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <?php echo htmlspecialchars($order['order_items']); ?>
                                    </span>
                                </td>
                                <td>€<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="order-status status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" 
                                                class="btn btn-sm btn-primary" 
                                                onclick="viewOrder(<?php echo $order['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                onclick="updateStatus(<?php echo $order['id']; ?>, 'completed')"
                                                <?php echo $order['status'] === 'completed' ? 'disabled' : ''; ?>>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger"
                                                onclick="updateStatus(<?php echo $order['id']; ?>, 'cancelled')"
                                                <?php echo $order['status'] === 'cancelled' ? 'disabled' : ''; ?>>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetails">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function viewOrder(orderId) {
    const modal = new bootstrap.Modal(document.getElementById('orderModal'));
    const orderDetails = document.getElementById('orderDetails');
    
    // Show loading state
    orderDetails.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading order details...</p>
        </div>
    `;
    
    modal.show();

    try {
        const response = await fetch(`process/get_order_details.php?id=${orderId}`);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server response was not JSON');
        }

        const data = await response.json();

        if (data.success) {
            // Format order date
            const orderDate = new Date(data.order.order_date).toLocaleString();
            
            // Generate status badge
            const statusBadge = `
                <span class="order-status status-${data.order.status}">
                    ${data.order.status.charAt(0).toUpperCase() + data.order.status.slice(1)}
                </span>
            `;

            orderDetails.innerHTML = `
                <div class="mb-4">
                    <h6 class="border-bottom pb-2">Order Information</h6>
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-1"><strong>Order ID:</strong> #${data.order.id}</p>
                            <p class="mb-1"><strong>Date:</strong> ${orderDate}</p>
                            <p class="mb-0"><strong>Status:</strong> ${statusBadge}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="border-bottom pb-2">Customer Information</h6>
                    <p class="mb-1"><strong>Name:</strong> ${data.order.customer_name}</p>
                    <p class="mb-1"><strong>Email:</strong> ${data.order.customer_email}</p>
                    <p class="mb-0"><strong>Contact:</strong> ${data.order.customer_contact}</p>
                </div>

                <div class="mb-4">
                    <h6 class="border-bottom pb-2">Order Items</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.items.map(item => `
                                    <tr>
                                        <td>${item.nama}</td>
                                        <td class="text-center">${item.quantity}</td>
                                        <td class="text-end">€${Number(item.price).toFixed(2)}</td>
                                        <td class="text-end">€${(item.quantity * item.price).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td class="text-end"><strong>€${Number(data.order.total_amount).toFixed(2)}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                ${data.order.notes ? `
                    <div class="mb-0">
                        <h6 class="border-bottom pb-2">Notes</h6>
                        <p class="mb-0">${data.order.notes}</p>
                    </div>
                ` : ''}
            `;
        } else {
            throw new Error(data.message || 'Failed to load order details');
        }
    } catch (error) {
        console.error('Error:', error);
        orderDetails.innerHTML = `
            <div class="alert alert-danger mb-0">
                <i class="fas fa-exclamation-circle me-2"></i>
                Error loading order details: ${error.message}
            </div>
        `;
    }
}

        async function updateStatus(orderId, status) {
            if (!confirm(`Are you sure you want to mark this order as ${status}?`)) {
                return;
            }

            try {
                // Show loading indicator
                const loadingToast = showToast('Updating order status...', 'info');
                
                const response = await fetch('process/update_order_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        orderId: orderId,
                        status: status
                    })
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server response was not JSON');
                }

                const result = await response.json();

                if (result.success) {
                    // Hide loading toast
                    hideToast(loadingToast);
                    // Show success message
                    showToast('Order status updated successfully', 'success');
                    // Reload page after short delay
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error(result.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Error details:', error);
                showToast('Error updating order status: ' + error.message, 'error');
            }
        }

        // Fungsi helper untuk menampilkan toast messages
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type} position-fixed bottom-0 end-0 m-3`;
            toast.innerHTML = `
                <div class="toast-header">
                    <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            `;
            document.body.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            return toast;
        }

        function hideToast(toastElement) {
            const bsToast = bootstrap.Toast.getInstance(toastElement);
            if (bsToast) {
                bsToast.hide();
            }
        }
    </script>
</body>
</html>