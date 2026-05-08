<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
$success = $error = "";

// Fetch current details
$stmt = $pdo->prepare("SELECT * FROM pg_listings WHERE id = ?");
$stmt->execute([$id]);
$pg = $stmt->fetch();

if (!$pg) {
    header("Location: dashboard.php");
    exit();
}

// Fetch current images
$img_stmt = $pdo->prepare("SELECT * FROM pg_images WHERE pg_id = ?");
$img_stmt->execute([$id]);
$images = $img_stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = $_POST['title'];
    $location    = $_POST['location'];
    $price       = $_POST['price'];
    $gender      = $_POST['gender'];
    $room_type   = $_POST['room_type'];
    $amenities   = implode(', ', $_POST['amenities'] ?? []);
    $description = $_POST['description'];
    $contact     = $_POST['contact'];
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    try {
        $pdo->beginTransaction();

        $upd_stmt = $pdo->prepare("UPDATE pg_listings SET title=?, location=?, price=?, gender=?, room_type=?, amenities=?, description=?, contact=?, is_available=? WHERE id=?");
        $upd_stmt->execute([$title, $location, $price, $gender, $room_type, $amenities, $description, $contact, $is_available, $id]);

        // Handle new images if uploaded
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = '../uploads/';
            foreach ($_FILES['images']['name'] as $key => $image_name) {
                $tmp_name = $_FILES['images']['tmp_name'][$key];
                $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $new_filename = uniqid('pg_') . '_' . time() . '.' . $extension;
                    if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                        $ins_img = $pdo->prepare("INSERT INTO pg_images (pg_id, image_path) VALUES (?, ?)");
                        $ins_img->execute([$id, 'uploads/' . $new_filename]);
                    }
                }
            }
        }

        $pdo->commit();
        header("Location: dashboard.php?msg=updated");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit PG | Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <div class="brand" style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
            <i class="fas fa-edit"></i> Edit Listing
        </div>
        <a href="dashboard.php" class="btn btn-primary" style="background: #f3f4f6; color: var(--text-main);">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </nav>

    <div class="container" style="max-width: 900px;">
        <div class="card" style="padding: 2.5rem;">
            <form method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label class="form-label">PG Title</label>
                        <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($pg['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-input" value="<?= htmlspecialchars($pg['location']) ?>" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-input" value="<?= $pg['price'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender Preference</label>
                        <select name="gender" class="form-input">
                            <option value="Boys" <?= $pg['gender'] == 'Boys' ? 'selected' : '' ?>>Boys</option>
                            <option value="Girls" <?= $pg['gender'] == 'Girls' ? 'selected' : '' ?>>Girls</option>
                            <option value="Co-ed" <?= $pg['gender'] == 'Co-ed' ? 'selected' : '' ?>>Co-ed</option>
                        </select>
                    </div>
                     <div class="form-group">
                        <label class="form-label">Room Type</label>
                        <select name="room_type" class="form-input">
                            <option value="Single Room" <?= $pg['room_type'] == 'Single Room' ? 'selected' : '' ?>>Single Room</option>
                            <option value="Double Sharing" <?= $pg['room_type'] == 'Double Sharing' ? 'selected' : '' ?>>Double Sharing</option>
                            <option value="Triple Sharing" <?= $pg['room_type'] == 'Triple Sharing' ? 'selected' : '' ?>>Triple Sharing</option>
                        </select>
                    </div>
                </div>

                 <div class="form-group">
                    <label class="form-label">Availability Status</label>
                    <label style="display: flex; align-items: center; gap: 1rem; cursor: pointer;">
                        <input type="checkbox" name="is_available" <?= $pg['is_available'] ? 'checked' : '' ?> style="width: 20px; height: 20px;"> 
                        <span>Listing is currently available for booking</span>
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label">Current Images (Add more by uploading below)</label>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                        <?php foreach($images as $img): ?>
                        <div style="position: relative;">
                            <img src="../<?= $img['image_path'] ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border);">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="file" name="images[]" multiple class="form-input" accept="image/*">
                </div>

                <div class="form-group">
                    <label class="form-label">Amenities</label>
                    <?php $curr_amenities = explode(', ', $pg['amenities']); ?>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                        <?php foreach(['WiFi', 'Food', 'AC', 'Laundry', 'Parking', 'Cleaning'] as $a): ?>
                        <label><input type="checkbox" name="amenities[]" value="<?= $a ?>" <?= in_array($a, $curr_amenities) ? 'checked' : '' ?>> <?= $a ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-input" value="<?= htmlspecialchars($pg['contact']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input" rows="4"><?= htmlspecialchars($pg['description']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i class="fas fa-sync-alt"></i> Update Listing
                </button>
            </form>
        </div>
    </div>
</body>
</html>
