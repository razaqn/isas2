<?php
require_once 'check.php';
checkAdminLogin();
require_once 'database/config.php';

try {
    // Get basic statistics
    $stmt = $pdo->query("SELECT COUNT(*) as monthly_orders FROM pesan 
                         WHERE MONTH(order_date) = MONTH(CURRENT_DATE())
                         AND YEAR(order_date) = YEAR(CURRENT_DATE())");
    $monthlyOrders = $stmt->fetch()['monthly_orders'];
    
    $stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM review");
    $avgRating = number_format($stmt->fetch()['avg_rating'], 1);
    
    $stmt = $pdo->query("SELECT m.category, COUNT(*) as count 
                         FROM pesan_detail pd 
                         JOIN menu m ON pd.menu_id = m.id 
                         GROUP BY m.category 
                         ORDER BY count DESC 
                         LIMIT 1");
    $topCategory = $stmt->fetch();

    // Get SWOT items grouped by category
    $stmt = $pdo->query("SELECT sc.*, si.id as item_id, si.description, si.priority, si.status
                         FROM swot_categories sc
                         LEFT JOIN swot_items si ON sc.id = si.category_id
                         WHERE si.status = 'active'
                         ORDER BY sc.id, si.priority DESC");
    $swotItems = [];
    while ($row = $stmt->fetch()) {
        if (!isset($swotItems[$row['name']])) {
            $swotItems[$row['name']] = [
                'icon' => $row['icon'],
                'color' => $row['color'],
                'items' => []
            ];
        }
        if ($row['item_id']) {
            $swotItems[$row['name']]['items'][] = [
                'description' => $row['description'],
                'priority' => $row['priority']
            ];
        }
    }

    // Get development timeline
    $stmt = $pdo->query("SELECT * FROM development_timeline ORDER BY period, sort_order");
    $timeline = [];
    while ($row = $stmt->fetch()) {
        $timeline[$row['period']][] = $row;
    }

    // Get action items
    $stmt = $pdo->query("SELECT * FROM action_items ORDER BY 
                         CASE priority 
                            WHEN 'high' THEN 1 
                            WHEN 'medium' THEN 2 
                            ELSE 3 
                         END, 
                         timeline_months");
    $actionItems = $stmt->fetchAll();

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
    <title>SWOT Analysis - Osteria del Mare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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

        .swot-card {
            border-radius: 15px;
            transition: transform 0.3s;
            height: 100%;
        }

        .swot-card:hover {
            transform: translateY(-5px);
        }

        .strengths-card {
            border-left: 5px solid #28a745;
        }

        .weaknesses-card {
            border-left: 5px solid #dc3545;
        }

        .opportunities-card {
            border-left: 5px solid #17a2b8;
        }

        .threats-card {
            border-left: 5px solid #ffc107;
        }

        .stat-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .recommendation-card {
            border-left: 5px solid #1A5F7A;
        }

        .timeline-container {
            position: relative;
            padding-left: 50px;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #1A5F7A;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -43px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #1A5F7A;
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
        <h2 class="mb-4">SWOT Analysis & Development Plan</h2>

        <!-- Context Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <h5><i class="fas fa-shopping-cart text-primary"></i> Monthly Orders</h5>
                    <h3><?php echo $monthlyOrders; ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5><i class="fas fa-star text-warning"></i> Average Rating</h5>
                    <h3><?php echo $avgRating; ?> / 5.0</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5><i class="fas fa-crown text-success"></i> Top Category</h5>
                    <h3><?php echo $topCategory['category'] ?? 'N/A'; ?></h3>
                </div>
            </div>
        </div>

        <!-- SWOT Analysis -->
        <div class="row mb-4">
            <?php foreach($swotItems as $categoryName => $category): ?>
            <div class="col-md-3 mb-4">
                <div class="card swot-card" style="border-left: 5px solid <?php echo $category['color']; ?>">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="<?php echo $category['icon']; ?>" style="color: <?php echo $category['color']; ?>"></i>
                            <?php echo $categoryName; ?>
                        </h5>
                        <ul class="list-unstyled">
                            <?php foreach($category['items'] as $item): ?>
                            <li class="mb-2">
                                <i class="fas fa-angle-right"></i>
                                <?php echo htmlspecialchars($item['description']); ?>
                                <?php if($item['priority'] === 'high'): ?>
                                    <span class="badge bg-danger">High</span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Development Recommendations -->
        <div class="card recommendation-card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Development Timeline</h5>
                
                <div class="timeline-container">
                    <?php 
                    $periods = [
                        'short' => 'Short Term (0-3 Months)',
                        'medium' => 'Medium Term (3-6 Months)',
                        'long' => 'Long Term (6-12 Months)'
                    ];
                    
                    foreach($periods as $period => $title): 
                        if(isset($timeline[$period])):
                    ?>
                    <div class="timeline-item">
                        <h6><?php echo $title; ?></h6>
                        <div class="card mb-3">
                            <div class="card-body">
                                <ul class="mb-0">
                                    <?php foreach($timeline[$period] as $item): ?>
                                    <li><?php echo htmlspecialchars($item['description']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>

        <!-- Action Items -->
        <div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-4">Priority Action Items</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Action Item</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Timeline</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($actionItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $item['priority'] === 'high' ? 'danger' : 
                                    ($item['priority'] === 'medium' ? 'warning' : 'info'); 
                            ?>">
                                <?php echo ucfirst($item['priority']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($item['status']); ?></td>
                        <td><?php echo $item['timeline_months']; ?> month<?php echo $item['timeline_months'] > 1 ? 's' : ''; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>