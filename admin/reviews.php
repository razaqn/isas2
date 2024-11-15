<?php
require_once 'check.php';
checkAdminLogin();
require_once 'database/config.php';

// Get reviews
try {
    $stmt = $pdo->query("SELECT r.*, p.customer_name, 
                         GROUP_CONCAT(m.nama SEPARATOR ', ') as menu_names
                         FROM review r
                         JOIN pesan p ON r.pesan_id = p.id
                         JOIN pesan_detail pd ON p.id = pd.pesan_id
                         JOIN menu m ON pd.menu_id = m.id
                         GROUP BY r.id
                         ORDER BY r.review_date DESC");
    $reviews = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}

// Handle review status update
if (isset($_POST['review_id']) && isset($_POST['action'])) {
    $reviewId = $_POST['review_id'];
    $action = $_POST['action'];
    
    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE review SET status = 'approved' WHERE id = ?");
        } else if ($action === 'reject') {
            $stmt = $pdo->prepare("UPDATE review SET status = 'rejected' WHERE id = ?");
        }
        $stmt->execute([$reviewId]);
        header("Location: reviews.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Management - Osteria del Mare</title>
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

        .review-card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }

        .rating {
            color: #ffc107;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-approved {
            background-color: #28a745;
            color: #fff;
        }

        .status-rejected {
            background-color: #dc3545;
            color: #fff;
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

    <!-- Reviews Content -->
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Review Management</h2>
        </div>

        <?php foreach($reviews as $review): ?>
            <div class="card review-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Order Menu: <?php echo htmlspecialchars($review['menu_names']); ?></h5>
                            <div class="rating mb-2">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? '' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <span class="status-badge status-<?php echo $review['status']; ?>">
                            <?php echo ucfirst($review['status']); ?>
                        </span>
                    </div>
                    
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            By: <?php echo htmlspecialchars($review['customer_name']); ?> | 
                            <?php echo date('d M Y H:i', strtotime($review['review_date'])); ?>
                        </small>
                        
                        <?php if($review['status'] === 'pending'): ?>
                            <div>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if(empty($reviews)): ?>
            <div class="alert alert-info">
                No reviews found.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>