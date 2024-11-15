<?php
require_once 'admin_check.php';
checkAdminLogin();
require_once '../database/config.php';

function uploadImage($file) {
    $target_dir = "uploads/menu/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . uniqid() . "." . $imageFileType;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return basename($target_file);
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'add') {
            // Handle new menu addition
            $image = uploadImage($_FILES['image']);
            if ($image) {
                $stmt = $pdo->prepare("INSERT INTO menu (nama, category, description, price, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['nama'],
                    $_POST['category'],
                    $_POST['description'],
                    $_POST['price'],
                    $image
                ]);
            }
        } elseif ($action === 'edit') {
            // Handle menu update
            if (!empty($_FILES['image']['name'])) {
                $image = uploadImage($_FILES['image']);
                if ($image) {
                    $stmt = $pdo->prepare("UPDATE menu SET nama=?, category=?, description=?, price=?, image=? WHERE id=?");
                    $stmt->execute([
                        $_POST['nama'],
                        $_POST['category'],
                        $_POST['description'],
                        $_POST['price'],
                        $image,
                        $_POST['id']
                    ]);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE menu SET nama=?, category=?, description=?, price=? WHERE id=?");
                $stmt->execute([
                    $_POST['nama'],
                    $_POST['category'],
                    $_POST['description'],
                    $_POST['price'],
                    $_POST['id']
                ]);
            }
        }
    } catch(PDOException $e) {
        // Handle error
        header('Location: ../menu.php?error=true');
        exit;
    }
    
    header('Location: ../menu.php?success=true');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    try {
        $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        header('Location: ../menu.php?success=true');
        exit;
    } catch(PDOException $e) {
        header('Location: ../menu.php?error=true');
        exit;
    }
}

header('Location: ../menu.php');
exit;
?>