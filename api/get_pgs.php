<?php
require_once '../includes/db.php';

$p_min = $_GET['min_price'] ?? 0;
$p_max = $_GET['max_price'] ?? 1000000;
$gender = $_GET['gender'] ?? '';
$search = $_GET['search'] ?? '';

$sql = "SELECT p.*, (SELECT image_path FROM pg_images WHERE pg_id = p.id LIMIT 1) as thumb 
        FROM pg_listings p 
        WHERE price BETWEEN ? AND ? 
        AND is_available = 1";

$params = [$p_min, $p_max];

if ($gender) {
    $sql .= " AND gender = ?";
    $params[] = $gender;
}

if ($search) {
    $sql .= " AND (title LIKE ? OR location LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$limit = 6;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$sql .= " ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pgs = $stmt->fetchAll();

// Return as JSON for AJAX
header('Content-Type: application/json');
echo json_encode($pgs);
?>
