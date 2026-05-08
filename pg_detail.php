<?php
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM pg_listings WHERE id = ?");
$stmt->execute([$id]);
$pg = $stmt->fetch();

if (!$pg) {
    header("Location: index.php");
    exit();
}

// Fetch all images for gallery
$img_stmt = $pdo->prepare("SELECT image_path FROM pg_images WHERE pg_id = ?");
$img_stmt->execute([$id]);
$images = $img_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pg['title']) ?> | PG Accommodations in <?= htmlspecialchars($pg['location']) ?></title>
    <meta name="description" content="<?= substr(htmlspecialchars($pg['description']), 0, 160) ?>...">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Schema.org JSON-LD for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Accommodation",
      "name": "<?= htmlspecialchars($pg['title']) ?>",
      "description": "<?= htmlspecialchars($pg['description']) ?>",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "<?= htmlspecialchars($pg['location']) ?>"
      },
      "amenityFeature": [
        <?php 
        $ams = explode(', ', $pg['amenities']);
        echo '"' . implode('","', $ams) . '"';
        ?>
      ],
      "offers": {
        "@type": "Offer",
        "price": "<?= $pg['price'] ?>",
        "priceCurrency": "INR"
      }
    }
    </script>
    <style>
        .gallery-main { width: 100%; height: 500px; border-radius: 24px; overflow: hidden; position: relative; box-shadow: var(--shadow); background: #eee; }
        .gallery-main img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; cursor: zoom-in; }
        .gallery-thumbs { display: flex; gap: 1rem; margin-top: 1.5rem; overflow-x: auto; padding-bottom: 1rem; }
        .gallery-thumbs img { width: 100px; height: 100px; border-radius: 12px; cursor: pointer; object-fit: cover; opacity: 0.6; transition: 0.3s; border: 2px solid transparent; }
        .gallery-thumbs img.active { opacity: 1; border-color: var(--primary); }
        .feature-card { background: var(--bg-card); padding: 1.5rem; border-radius: 20px; border: 1px solid var(--border); display: flex; align-items: center; gap: 1rem; }
        .amenity-chip { background: #f3f4f6; padding: 10px 20px; border-radius: 30px; font-weight: 500; color: var(--text-main); border: 1px solid var(--border); display: flex; align-items: center; gap: 0.5rem; }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" style="text-decoration: none; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; color: var(--text-main);">
            <i class="fas fa-arrow-left"></i> Back to search
        </a>
        <div class="brand" style="font-size: 1.4rem; font-weight: 800; color: var(--primary);">PG CONNECT</div>
        <div></div>
    </nav>

    <div class="container">
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem;">
            <!-- Left Side: Visuals -->
            <div>
                <div class="gallery-main">
                    <?php if (!empty($images)): ?>
                        <img src="<?= $images[0]['image_path'] ?>" id="mainImage">
                    <?php else: ?>
                         <div style="height:100%; display:flex; align-items:center; justify-content:center; color: #94a3b8;"><i class="fas fa-image fa-4x"></i></div>
                    <?php endif; ?>
                </div>
                <div class="gallery-thumbs">
                    <?php foreach($images as $key => $img): ?>
                        <img src="<?= $img['image_path'] ?>" class="<?= $key == 0 ? 'active' : '' ?>" onclick="changeImage(this)">
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 3rem;">
                    <h2 style="font-size: 2rem; margin-bottom: 1.5rem;">Description</h2>
                    <p style="line-height: 1.8; color: var(--text-muted); white-space: pre-line;">
                        <?= htmlspecialchars($pg['description']) ?>
                    </p>
                </div>

                <div style="margin-top: 3rem;">
                    <h2 style="font-size: 2rem; margin-bottom: 1.5rem;">Amenities Provided</h2>
                    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                        <?php 
                        $icons = ['WiFi' => 'wifi', 'Food' => 'utensils', 'AC' => 'wind', 'Laundry' => 'tshirt', 'Parking' => 'car', 'Cleaning' => 'broom'];
                        foreach(explode(', ', $pg['amenities']) as $am): ?>
                        <div class="amenity-chip">
                            <i class="fas fa-<?= $icons[$am] ?? 'check-circle' ?>" style="color: var(--primary);"></i> <?= $am ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Side: Contact & Info -->
            <div>
                <div class="card" style="padding: 2.5rem; position: sticky; top: 120px;">
                    <div style="margin-bottom: 2rem;">
                        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($pg['title']) ?></h1>
                        <p style="color: var(--text-muted);"><i class="fas fa-map-marker-alt" style="color: #f43f5e;"></i> <?= htmlspecialchars($pg['location']) ?></p>
                    </div>

                    <div style="background: #f8fafc; border-radius: 16px; padding: 1.5rem; margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span style="color: var(--text-muted);">Rent/Month</span>
                            <span style="font-size: 1.8rem; font-weight: 800; color: var(--text-main);">₹<?= number_format($pg['price'], 0) ?></span>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <div style="flex: 1; text-align: center; background: white; padding: 10px; border-radius: 12px; border: 1px solid var(--border);">
                                <small style="display: block; color: var(--text-muted);">Gender</small>
                                <strong><?= $pg['gender'] ?></strong>
                            </div>
                            <div style="flex: 1; text-align: center; background: white; padding: 10px; border-radius: 12px; border: 1px solid var(--border);">
                                <small style="display: block; color: var(--text-muted);">Room Type</small>
                                <strong><?= $pg['room_type'] ?></strong>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; gap: 1rem;">
                        <a href="tel:<?= $pg['contact'] ?>" class="btn btn-primary" style="width: 100%; justify-content: center; height: 55px; font-size: 1.1rem;">
                            <i class="fas fa-phone-alt"></i> Call Owner
                        </a>
                        <a href="https://wa.me/<?= str_replace(['+', ' ', '-'], '', $pg['contact']) ?>?text=Hello, I'm interested in your PG: <?= urlencode($pg['title']) ?>" class="btn" style="width: 100%; justify-content: center; height: 55px; font-size: 1.1rem; background: #25d366; color: white;">
                            <i class="fab fa-whatsapp"></i> WhatsApp Chat
                        </a>
                    </div>

                    <div style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 12px; height: 12px; border-radius: 50%; background: #22c55e;"></div>
                        <p style="font-weight: 500; color: #15803d;">Active & Available Now</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function changeImage(el) {
            document.getElementById('mainImage').src = el.src;
            document.querySelectorAll('.gallery-thumbs img').forEach(i => i.classList.remove('active'));
            el.classList.add('active');
        }
    </script>
</body>
</html>
