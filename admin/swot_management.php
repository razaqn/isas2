<?php
require_once 'check.php';
checkAdminLogin();
require_once 'database/config.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'add_item':
                    $stmt = $pdo->prepare("INSERT INTO swot_items (category_id, description, priority) VALUES (?, ?, ?)");
                    $stmt->execute([$_POST['category_id'], $_POST['description'], $_POST['priority']]);
                    break;

                case 'update_item':
                    $stmt = $pdo->prepare("UPDATE swot_items SET description = ?, priority = ?, status = ? WHERE id = ?");
                    $stmt->execute([$_POST['description'], $_POST['priority'], $_POST['status'], $_POST['item_id']]);
                    break;

                case 'delete_item':
                    $stmt = $pdo->prepare("DELETE FROM swot_items WHERE id = ?");
                    $stmt->execute([$_POST['item_id']]);
                    break;

                case 'add_timeline':
                    $stmt = $pdo->prepare("INSERT INTO development_timeline (period, title, description, sort_order) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$_POST['period'], $_POST['title'], $_POST['description'], $_POST['sort_order']]);
                    break;

                case 'update_timeline':
                    $stmt = $pdo->prepare("UPDATE development_timeline SET title = ?, description = ?, period = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute([$_POST['title'], $_POST['description'], $_POST['period'], $_POST['sort_order'], $_POST['timeline_id']]);
                    break;

                case 'delete_timeline':
                    $stmt = $pdo->prepare("DELETE FROM development_timeline WHERE id = ?");
                    $stmt->execute([$_POST['timeline_id']]);
                    break;

                case 'add_action':
                    $stmt = $pdo->prepare("INSERT INTO action_items (title, priority, status, timeline_months) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$_POST['title'], $_POST['priority'], $_POST['status'], $_POST['timeline_months']]);
                    break;

                case 'update_action':
                    $stmt = $pdo->prepare("UPDATE action_items SET title = ?, priority = ?, status = ?, timeline_months = ? WHERE id = ?");
                    $stmt->execute([$_POST['title'], $_POST['priority'], $_POST['status'], $_POST['timeline_months'], $_POST['action_id']]);
                    break;

                case 'delete_action':
                    $stmt = $pdo->prepare("DELETE FROM action_items WHERE id = ?");
                    $stmt->execute([$_POST['action_id']]);
                    break;
            }
            header("Location: swot_management.php");
            exit();
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Get SWOT data
try {
    // Get categories with their items
    $stmt = $pdo->query("SELECT c.*, 
                         (SELECT COUNT(*) FROM swot_items WHERE category_id = c.id AND status = 'active') as item_count 
                         FROM swot_categories c");
    $categories = $stmt->fetchAll();

    // Get all active SWOT items
    $stmt = $pdo->query("SELECT si.*, sc.name as category_name, sc.color, sc.icon 
                         FROM swot_items si 
                         JOIN swot_categories sc ON si.category_id = sc.id 
                         WHERE si.status = 'active' 
                         ORDER BY si.priority DESC, si.created_at DESC");
    $items = $stmt->fetchAll();

    // Get development timeline
    $stmt = $pdo->query("SELECT * FROM development_timeline ORDER BY period, sort_order");
    $timelineItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $timeline = [
        'short' => [],
        'medium' => [],
        'long' => []
    ];

    foreach ($timelineItems as $item) {
        $timeline[$item['period']][] = $item;
    }

    // Get action items
    $stmt = $pdo->query("SELECT * FROM action_items ORDER BY priority DESC, timeline_months ASC");
    $actions = $stmt->fetchAll();
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
    <title>SWOT Analysis Management - Osteria del Mare</title>
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
        }

        .swot-card:hover {
            transform: translateY(-5px);
        }

        .priority-high {
            border-left: 4px solid #dc3545;
        }

        .priority-medium {
            border-left: 4px solid #ffc107;
        }

        .priority-low {
            border-left: 4px solid #28a745;
        }

        .timeline-indicator {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .timeline-card {
            border-radius: 15px;
            margin-bottom: 1rem;
            transition: transform 0.3s;
        }

        .timeline-short { border-left: 4px solid #28a745; }
        .timeline-medium { border-left: 4px solid #ffc107; }
        .timeline-long { border-left: 4px solid #17a2b8; }

        .action-item {
            border-radius: 10px;
            margin-bottom: 0.5rem;
            transition: transform 0.3s;
        }

        .action-item:hover {
            transform: translateX(5px);
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

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>SWOT Analysis Management</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fas fa-plus"></i> Add SWOT Item
            </button>
        </div>

        <!-- SWOT Analysis Cards -->
        <div class="row mb-4">
            <?php foreach($categories as $category): ?>
            <div class="col-md-3 mb-4">
                <div class="card swot-card h-100" style="border-left: 5px solid <?php echo $category['color']; ?>">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="<?php echo $category['icon']; ?>" style="color: <?php echo $category['color']; ?>"></i>
                            <?php echo $category['name']; ?>
                        </h5>
                        <p class="text-muted mb-0"><?php echo $category['item_count']; ?> items</p>
                        
                        <!-- List items for this category -->
                        <div class="mt-3">
                            <?php foreach($items as $item): ?>
                                <?php if($item['category_id'] === $category['id']): ?>
                                <div class="card mb-2 priority-<?php echo $item['priority']; ?>">
                                    <div class="card-body p-2">
                                        <p class="mb-1"><?php echo htmlspecialchars($item['description']); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?php echo $item['priority'] === 'high' ? 'danger' : ($item['priority'] === 'medium' ? 'warning' : 'success'); ?>">
                                                <?php echo ucfirst($item['priority']); ?>
                                            </span>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="editItem(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteItem(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Development Timeline -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title">Development Timeline</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimelineModal">
                        <i class="fas fa-plus"></i> Add Timeline Item
                    </button>
                </div>

                <div class="row">
                    <!-- Short Term -->
                    <div class="col-md-4">
                        <div class="card border-success mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Short Term (0-3 months)</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($timeline['short'])): ?>
                                    <?php foreach($timeline['short'] as $item): ?>
                                        <div class="card timeline-card timeline-short mb-2">
                                            <div class="card-body p-3">
                                                <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                <p class="card-text small mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="editTimeline(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteTimeline(<?php echo $item['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No items yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Medium Term -->
                    <div class="col-md-4">
                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">Medium Term (3-6 months)</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($timeline['medium'])): ?>
                                    <?php foreach($timeline['medium'] as $item): ?>
                                        <div class="card timeline-card timeline-medium mb-2">
                                            <div class="card-body p-3">
                                                <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                <p class="card-text small mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="editTimeline(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteTimeline(<?php echo $item['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No items yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Long Term -->
                    <div class="col-md-4">
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Long Term (6-12 months)</h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($timeline['long'])): ?>
                                    <?php foreach($timeline['long'] as $item): ?>
                                        <div class="card timeline-card timeline-long mb-2">
                                            <div class="card-body p-3">
                                                <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                                <p class="card-text small mb-2"><?php echo htmlspecialchars($item['description']); ?></p>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="editTimeline(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteTimeline(<?php echo $item['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No items yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Items -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title">Action Items</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addActionModal">
                        <i class="fas fa-plus"></i> Add Action Item
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Timeline (Months)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($actions as $action): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($action['title']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $action['priority'] === 'high' ? 'danger' : ($action['priority'] === 'medium' ? 'warning' : 'success'); ?>">
                                        <?php echo ucfirst($action['priority']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($action['status']); ?></td>
                                <td><?php echo $action['timeline_months']; ?> months</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editAction(<?php echo htmlspecialchars(json_encode($action)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAction(<?php echo $action['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add SWOT Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add SWOT Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_item">
                        
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select" required>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit SWOT Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit SWOT Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_item">
                        <input type="hidden" name="item_id" id="edit_item_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" id="edit_priority" class="form-select" required>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <!-- Add Timeline Modal -->
    <div class="modal fade" id="addTimelineModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Timeline Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_timeline">
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Period</label>
                            <select name="period" class="form-select" required>
                                <option value="short">Short Term (0-3 months)</option>
                                <option value="medium">Medium Term (3-6 months)</option>
                                <option value="long">Long Term (6-12 months)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <!-- Add Action Item Modal -->
     <div class="modal fade" id="addActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Action Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_action">
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select" required>
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Not Started">Not Started</option>
                                <option value="Planning">Planning</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Timeline (Months)</label>
                            <input type="number" name="timeline_months" class="form-control" required min="1" max="12">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Action Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete_item">
                        <input type="hidden" name="item_id" id="delete_item_id">
                        <p>Are you sure you want to delete this SWOT item?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Timeline Modal -->
    <div class="modal fade" id="editTimelineModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Timeline Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_timeline">
                        <input type="hidden" name="timeline_id" id="edit_timeline_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="edit_timeline_title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_timeline_description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Period</label>
                            <select name="period" id="edit_timeline_period" class="form-select" required>
                                <option value="short">Short Term (0-3 months)</option>
                                <option value="medium">Medium Term (3-6 months)</option>
                                <option value="long">Long Term (6-12 months)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="edit_timeline_sort_order" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Action Item Modal -->
    <div class="modal fade" id="editActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Action Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_action">
                        <input type="hidden" name="action_id" id="edit_action_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" id="edit_action_title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" id="edit_action_priority" class="form-select" required>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_action_status" class="form-select" required>
                                <option value="Not Started">Not Started</option>
                                <option value="Planning">Planning</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Timeline (Months)</label>
                            <input type="number" name="timeline_months" id="edit_action_timeline" class="form-control" required min="1" max="12">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Action Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editItem(item) {
            document.getElementById('edit_item_id').value = item.id;
            document.getElementById('edit_description').value = item.description;
            document.getElementById('edit_priority').value = item.priority;
            document.getElementById('edit_status').value = item.status;
            new bootstrap.Modal(document.getElementById('editItemModal')).show();
        }

        function deleteItem(itemId) {
            document.getElementById('delete_item_id').value = itemId;
            new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
        }

        // Timeline functions
        function editTimeline(item) {
            document.getElementById('edit_timeline_id').value = item.id;
            document.getElementById('edit_timeline_title').value = item.title;
            document.getElementById('edit_timeline_description').value = item.description;
            document.getElementById('edit_timeline_period').value = item.period;
            document.getElementById('edit_timeline_sort_order').value = item.sort_order;
            new bootstrap.Modal(document.getElementById('editTimelineModal')).show();
        }

        function deleteTimeline(timelineId) {
            if (confirm('Are you sure you want to delete this timeline item?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_timeline">
                    <input type="hidden" name="timeline_id" value="${timelineId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Action Item functions
        function editAction(action) {
            document.getElementById('edit_action_id').value = action.id;
            document.getElementById('edit_action_title').value = action.title;
            document.getElementById('edit_action_priority').value = action.priority;
            document.getElementById('edit_action_status').value = action.status;
            document.getElementById('edit_action_timeline').value = action.timeline_months;
            new bootstrap.Modal(document.getElementById('editActionModal')).show();
        }

        function deleteAction(actionId) {
            if (confirm('Are you sure you want to delete this action item?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_action">
                    <input type="hidden" name="action_id" value="${actionId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>