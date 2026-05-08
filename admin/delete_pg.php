<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch images to delete them from physical storage
    $stmt = $pdo->prepare("SELECT image_path FROM pg_images WHERE pg_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll();

    foreach ($images as $img) {
        $path = '../' . $img['image_path'];
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // Delete from DB (ON DELETE CASCADE handles pg_images)
    $del = $pdo->prepare("DELETE FROM pg_listings WHERE id = ?");
    $del->execute([$id]);
}

header("Location: dashboard.php?msg=deleted");
exit();
?>
