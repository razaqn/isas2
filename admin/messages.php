<?php
require_once 'check.php';
checkAdminLogin();
require_once 'database/config.php';

// Mark message as read
if(isset($_POST['message_id']) && isset($_POST['action'])) {
    try {
        $stmt = $pdo->prepare("UPDATE contact SET status = 'read' WHERE id = ?");
        $stmt->execute([$_POST['message_id']]);
        header("Location: messages.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
}

// Delete message
if(isset($_POST['message_id']) && isset($_POST['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM contact WHERE id = ?");
        $stmt->execute([$_POST['message_id']]);
        header("Location: messages.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        die();
    }
}

// Get messages with sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

try {
    $query = "SELECT * FROM contact";
    
    // Apply filter
    if($filter === 'unread') {
        $query .= " WHERE status = 'unread'";
    } else if($filter === 'read') {
        $query .= " WHERE status = 'read'";
    }
    
    // Apply sorting
    if($sort === 'oldest') {
        $query .= " ORDER BY created_at ASC";
    } else {
        $query .= " ORDER BY created_at DESC";
    }
    
    $stmt = $pdo->query($query);
    $messages = $stmt->fetchAll();
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
    <title>Message Management - Osteria del Mare</title>
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

        .message-card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }

        .message-card:hover {
            transform: translateY(-2px);
        }

        .unread {
            border-left: 4px solid #1A5F7A;
            background-color: #f8f9fa;
        }

        .message-date {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .message-actions {
            opacity: 0;
            transition: opacity 0.2s;
        }

        .message-card:hover .message-actions {
            opacity: 1;
        }

        .filters select {
            min-width: 120px;
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

    <!-- Messages Content -->
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Message Management</h2>
            <div class="filters d-flex gap-2">
                <select class="form-select" onchange="window.location.href='?sort=<?php echo $sort; ?>&filter=' + this.value">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Messages</option>
                    <option value="unread" <?php echo $filter === 'unread' ? 'selected' : ''; ?>>Unread Only</option>
                    <option value="read" <?php echo $filter === 'read' ? 'selected' : ''; ?>>Read Only</option>
                </select>
                <select class="form-select" onchange="window.location.href='?filter=<?php echo $filter; ?>&sort=' + this.value">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                </select>
            </div>
        </div>

        <?php foreach($messages as $message): ?>
            <div class="card message-card <?php echo $message['status'] === 'unread' ? 'unread' : ''; ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">
                                <?php if($message['status'] === 'unread'): ?>
                                    <i class="fas fa-circle text-primary" style="font-size: 0.5rem; vertical-align: middle;"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($message['name']); ?>
                            </h5>
                            <p class="message-date mb-2">
                                <i class="far fa-clock"></i>
                                <?php echo date('d M Y H:i', strtotime($message['created_at'])); ?>
                            </p>
                        </div>
                        <div class="message-actions">
                            <?php if($message['status'] === 'unread'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <button type="submit" name="action" value="mark_read" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-check"></i> Mark as Read
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <button type="submit" name="delete" value="true" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <p class="mb-1"><strong>Contact:</strong> <?php echo htmlspecialchars($message['contact_number']); ?></p>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if(empty($messages)): ?>
            <div class="alert alert-info">
                No messages found.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>