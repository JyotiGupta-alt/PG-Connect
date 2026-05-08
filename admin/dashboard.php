<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

// Fetch all PG listings with their primary thumbnail
$stmt = $pdo->prepare("
    SELECT p.*, (SELECT image_path FROM pg_images WHERE pg_id = p.id LIMIT 1) as thumb 
    FROM pg_listings p 
    ORDER BY p.created_at DESC
");
$stmt->execute();
$pgs = $stmt->fetchAll();

if (isset($_GET['logout'])) logout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | PG Manager</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-card { padding: 1.5rem; background: var(--bg-card); border-radius: 20px; box-shadow: var(--shadow); margin-top: 1rem; border: 1px solid var(--border); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { font-weight: 600; color: var(--text-muted); }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .status-available { background: #dcfce7; color: #166534; }
        .status-full { background: #fee2e2; color: #991b1b; }
        .thumb-sm { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
    </style>
</head>
<body>
    <nav>
        <div class="brand" style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
            <i class="fas fa-hotel"></i> PG Admin
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <span style="color: var(--text-muted);">Hi, <?= $_SESSION['admin_email'] ?></span>
            <a href="?logout=1" class="btn btn-primary" style="background: #ef4444; color: white; padding: 0.5rem 1rem;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="dash-layout">
        <div class="sidebar">
            <div style="margin-bottom: 2rem; font-weight: 600; color: var(--text-muted); letter-spacing: 1px; font-size: 0.8rem; text-transform: uppercase;">Menu</div>
            <a href="dashboard.php" class="btn" style="width: 100%; justify-content: flex-start; background: #eef2ff; color: var(--primary); margin-bottom: 0.5rem;">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="add_pg.php" class="btn" style="width: 100%; justify-content: flex-start; color: var(--text-muted); margin-bottom: 0.5rem;">
                <i class="fas fa-plus-circle"></i> Add New PG
            </a>
        </div>

        <div class="main-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Listing Management</h1>
                    <p style="color: var(--text-muted);">Manage your PG accommodations from here.</p>
                </div>
                <a href="add_pg.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Listing
                </a>
            </div>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>PG Name</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pgs as $pg): ?>
                        <tr>
                            <td>
                                <?php if($pg['thumb']): ?>
                                    <img src="../<?= $pg['thumb'] ?>" class="thumb-sm">
                                <?php else: ?>
                                    <div class="thumb-sm" style="background: #e2e8f0; display: flex; align-items: center; justify-content: center;"><i class="fas fa-image" style="color: #94a3b8;"></i></div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($pg['title']) ?></strong><br><small style="color: var(--text-muted);"><?= $pg['room_type'] ?></small></td>
                            <td><?= htmlspecialchars($pg['location']) ?></td>
                            <td>₹<?= number_format($pg['price'], 0) ?>/mo</td>
                            <td>
                                <span class="status-badge <?= $pg['is_available'] ? 'status-available' : 'status-full' ?>">
                                    <?= $pg['is_available'] ? 'Available' : 'Full' ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="edit_pg.php?id=<?= $pg['id'] ?>" class="btn" style="padding: 0.5rem; background: #f3f4f6; color: var(--text-main);"><i class="fas fa-edit"></i></a>
                                    <a href="delete_pg.php?id=<?= $pg['id'] ?>" class="btn" style="padding: 0.5rem; background: #fee2e2; color: #ef4444;" onclick="return confirm('Archive this record?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($pgs)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 4rem; color: var(--text-muted);">
                                <i class="fas fa-hotel" style="font-size: 3rem; margin-bottom: 1rem; display: block; opacity: 0.2;"></i>
                                No PG listings found. Start by adding one!
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
