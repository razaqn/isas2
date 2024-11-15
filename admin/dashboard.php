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

    // SWOT statistics
    $stmt = $pdo->query("SELECT 
        sc.name as category,
        COUNT(si.id) as total_items,
        SUM(CASE WHEN si.priority = 'high' THEN 1 ELSE 0 END) as priority_high
        FROM swot_categories sc
        LEFT JOIN swot_items si ON sc.id = si.category_id AND si.status = 'active'
        GROUP BY sc.id, sc.name");
    $swotStats = $stmt->fetchAll();

    // Timeline statistics
    $stmt = $pdo->query("SELECT 
        period,
        COUNT(*) as total_items
        FROM development_timeline
        GROUP BY period");
    $timelineStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Action Items statistics
    $stmt = $pdo->query("SELECT 
        COUNT(*) as total_actions,
        SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high_priority_count,
        SUM(CASE WHEN status = 'Not Started' THEN 1 ELSE 0 END) as not_started_count,
        SUM(CASE WHEN status IN ('In Progress', 'Planning') THEN 1 ELSE 0 END) as in_progress_count,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_count
        FROM action_items");
    $actionStats = $stmt->fetch();

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

        .progress-mini {
            height: 4px;
            margin-top: 8px;
        }

        .timeline-indicator {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .timeline-short { background-color: #d1e7dd; color: #0f5132; }
        .timeline-medium { background-color: #fff3cd; color: #664d03; }
        .timeline-long { background-color: #cff4fc; color: #055160; }

        .swot-summary-card { border-left: 4px solid; }
        .strengths-card { border-color: #28a745; }
        .weaknesses-card { border-color: #dc3545; }
        .opportunities-card { border-color: #17a2b8; }
        .threats-card { border-color: #ffc107; }
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

        <!-- Development Progress -->
        <div class="row mb-4">
            <!-- Timeline Progress -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Development Timeline</h5>
                            <a href="swot.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-external-link-alt"></i> View Details
                            </a>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="timeline-indicator timeline-short">
                                    Short Term (0-3 months)
                                </span>
                                <span class="badge bg-primary"><?php echo $timelineStats['short'] ?? 0; ?> items</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="timeline-indicator timeline-medium">
                                    Medium Term (3-6 months)
                                </span>
                                <span class="badge bg-primary"><?php echo $timelineStats['medium'] ?? 0; ?> items</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="timeline-indicator timeline-long">
                                    Long Term (6-12 months)
                                </span>
                                <span class="badge bg-primary"><?php echo $timelineStats['long'] ?? 0; ?> items</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Items Progress -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Action Items Progress</h5>
                            <span class="badge bg-primary"><?php echo $actionStats['total_actions']; ?> total</span>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Completed</span>
                                <span class="badge bg-success"><?php echo $actionStats['completed_count']; ?></span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar bg-success" style="width: <?php echo ($actionStats['completed_count'] / $actionStats['total_actions'] * 100); ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>In Progress</span>
                                <span class="badge bg-info"><?php echo $actionStats['in_progress_count']; ?></span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar bg-info" style="width: <?php echo ($actionStats['in_progress_count'] / $actionStats['total_actions'] * 100); ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Not Started</span>
                                <span class="badge bg-warning"><?php echo $actionStats['not_started_count']; ?></span>
                            </div>
                            <div class="progress progress-mini">
                                <div class="progress-bar bg-warning" style="width: <?php echo ($actionStats['not_started_count'] / $actionStats['total_actions'] * 100); ?>%"></div>
                            </div>
                        </div>
                        <?php if($actionStats['high_priority_count'] > 0): ?>
                        <div class="mt-3">
                            <small class="text-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo $actionStats['high_priority_count']; ?> high priority items
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SWOT Summary -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title">SWOT Analysis Summary</h5>
                    <a href="swot.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-external-link-alt"></i> View Full Analysis
                    </a>
                </div>
                <div class="row">
                    <?php foreach($swotStats as $stat): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card swot-summary-card h-100 <?php echo strtolower($stat['category']); ?>-card">
                            <div class="card-body">
                                <h6 class="card-title"><?php echo $stat['category']; ?></h6>
                                <p class="mb-0"><?php echo $stat['total_items']; ?> items</p>
                                <?php if($stat['priority_high'] > 0): ?>
                                <small class="text-danger">
                                    <?php echo $stat['priority_high']; ?> high priority
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
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
                    <a href="swot_management.php" class="btn btn-info">
                        <i class="fas fa-chart-line"></i> SWOT Management
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>