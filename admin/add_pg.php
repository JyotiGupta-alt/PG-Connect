<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireLogin();

$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = $_POST['title'];
    $location    = $_POST['location'];
    $price       = $_POST['price'];
    $gender      = $_POST['gender'];
    $room_type   = $_POST['room_type'];
    $amenities   = implode(', ', $_POST['amenities'] ?? []);
    $description = $_POST['description'];
    $contact     = $_POST['contact'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO pg_listings (title, location, price, gender, room_type, amenities, description, contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $location, $price, $gender, $room_type, $amenities, $description, $contact]);
        
        $pg_id = $pdo->lastInsertId();

        // Image Upload Handling
        if (!empty($_FILES['images']['name'][0])) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            foreach ($_FILES['images']['name'] as $key => $image_name) {
                $tmp_name = $_FILES['images']['tmp_name'][$key];
                $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $max_size = 2 * 1024 * 1024; // 2MB

                if (in_array(strtolower($extension), $allowed) && $_FILES['images']['size'][$key] <= $max_size) {
                    $new_filename = uniqid('pg_') . '_' . time() . '.' . $extension;
                    $target_path = $upload_dir . $new_filename;
                    $db_path = 'uploads/' . $new_filename;

                    if (move_uploaded_file($tmp_name, $target_path)) {
                        $img_stmt = $pdo->prepare("INSERT INTO pg_images (pg_id, image_path) VALUES (?, ?)");
                        $img_stmt->execute([$pg_id, $db_path]);
                    }
                }
            }
        }

        $pdo->commit();
        $success = "PG Listing created successfully!";
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
    <title>Add New PG | Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <div class="brand" style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
            <i class="fas fa-plus-circle"></i> Add New listing
        </div>
        <a href="dashboard.php" class="btn btn-primary" style="background: #f3f4f6; color: var(--text-main);">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </nav>

    <div class="container" style="max-width: 900px;">
        <?php if($success): ?>
            <div style="background: #dcfce7; color: #166534; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;"><?= $success ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;"><?= $error ?></div>
        <?php endif; ?>

        <div class="card" style="padding: 2.5rem;">
            <form method="POST" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label class="form-label">PG Title</label>
                        <input type="text" name="title" class="form-input" placeholder="Luxury Stay for Boys" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location (City, Area)</label>
                        <input type="text" name="location" class="form-input" placeholder="Sector 44, Gurgaon" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem;">
                    <div class="form-group">
                        <label class="form-label">Price (Monthly)</label>
                        <input type="number" name="price" class="form-input" placeholder="8500" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-input" required>
                            <option value="Boys">Boys</option>
                            <option value="Girls">Girls</option>
                            <option value="Co-ed">Co-ed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Room Type</label>
                        <select name="room_type" class="form-input" required>
                            <option value="Single Room">Single Room</option>
                            <option value="Double Sharing">Double Sharing</option>
                            <option value="Triple Sharing">Triple Sharing</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Upload Images (Max 5)</label>
                    <input type="file" name="images[]" multiple class="form-input" accept="image/*">
                    <small style="color: var(--text-muted);">Hold Ctrl to select multiple images.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Amenities</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <label><input type="checkbox" name="amenities[]" value="WiFi"> WiFi</label>
                        <label><input type="checkbox" name="amenities[]" value="Food"> Food</label>
                        <label><input type="checkbox" name="amenities[]" value="AC"> AC</label>
                        <label><input type="checkbox" name="amenities[]" value="Laundry"> Laundry</label>
                        <label><input type="checkbox" name="amenities[]" value="Parking"> Parking</label>
                        <label><input type="checkbox" name="amenities[]" value="Cleaning"> Daily Cleaning</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contact Details (Phone / WhatsApp)</label>
                    <input type="text" name="contact" class="form-input" placeholder="+91 9876543210" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input" rows="4" placeholder="Briefly describe the PG features..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                    <i class="fas fa-save"></i> Publish Listing
                </button>
            </form>
        </div>
    </div>
</body>
</html>
