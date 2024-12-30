<?php
require_once 'check.php';
checkAdminLogin();
require_once 'database/config.php';

// Get statistics
try {
    // Basic statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total_menu FROM menu");
    $totalMenu = $stmt->fetch()['total_menu'];
    
    $stmt = $pdo->query("SELECT SUM(jumlah_beli) as total_orders FROM menu");
    $totalOrders = $stmt->fetch()['total_orders'] ?? 0;
    
    $stmt = $pdo->query("SELECT COUNT(*) as unread_messages FROM contact WHERE status = 'unread'");
    $unreadMessages = $stmt->fetch()['unread_messages'];

    // Review statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total_reviews,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_reviews
        FROM review");
    $reviewStats = $stmt->fetch();
    $totalReviews = $reviewStats['total_reviews'];
    $pendingReviews = $reviewStats['pending_reviews'];

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
    <title>Admin Dashboard - Osteria del Mare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: #1A5F7A;
            margin-bottom: 1rem;
        }
        
        .navbar {
            background-color: #1A5F7A;
        }
        
        .navbar-brand {
            color: white !important;
            font-family: 'Palatino', serif;
        }
        
        .nav-link {
            color: white !important;
        }
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
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container">
        <h2 class="mb-4">Dashboard</h2>
        
        <!-- Main Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-utensils stat-icon"></i>
                    <h3><?php echo $totalMenu; ?></h3>
                    <p class="text-muted mb-0">Total Menu Items</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-shopping-cart stat-icon"></i>
                    <h3><?php echo $totalOrders; ?></h3>
                    <p class="text-muted mb-0">Total Orders</p>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-star stat-icon"></i>
                    <h3><?php echo $totalReviews; ?></h3>
                    <p class="text-muted mb-0">Total Reviews</p>
                    <?php if($pendingReviews > 0): ?>
                    <p class="review-stat mb-0">
                        <span class="text-warning">
                            <i class="fas fa-clock"></i> <?php echo $pendingReviews; ?> pending
                        </span>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <div class="stat-card">
                    <i class="fas fa-envelope stat-icon"></i>
                    <h3><?php echo $unreadMessages; ?></h3>
                    <p class="text-muted mb-0">Unread Messages</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-5">
            <div class="card-body">
                <h5 class="card-title mb-4">Quick Actions</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="menu.php" class="btn btn-primary">
                        <i class="fas fa-utensils"></i> Manage Menu
                    </a>
                    <a href="orders.php" class="btn btn-primary">
                        <i class="fas fa-shopping-cart"></i> View Orders
                    </a>
                    <a href="reviews.php" class="btn btn-primary">
                        <i class="fas fa-star"></i> Manage Reviews
                    </a>
                    <a href="messages.php" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> View Messages
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>