<?php
require_once 'includes/db.php';
session_start();

// Search and Filters Logic
$p_min = $_GET['min_price'] ?? 0;
$p_max = $_GET['max_price'] ?? 100000;
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

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pgs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Perfect PG | PG Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=2070&auto=format&fit=crop'); 
            height: 400px; 
            background-size: cover; 
            background-position: center; 
            display: flex; align-items: center; justify-content: center; text-align: center; color: white;
            padding: 2rem;
        }
        .filter-bar { background: var(--bg-card); padding: 1.5rem; border-radius: 20px; box-shadow: var(--shadow); margin-top: -50px; z-index: 10; position: relative; border: 1px solid var(--border); }
        .badge { padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge-boys { background: #dbeafe; color: #1e40af; }
        .badge-girls { background: #fdf2f8; color: #9d174d; }
        .badge-coed { background: #f3f4f6; color: #374151; }
    </style>
</head>
<body>
    <nav>
        <div class="brand" style="font-size: 1.6rem; font-weight: 800; color: var(--primary);"><i class="fas fa-home"></i> PG Connect</div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <?php if(isset($_SESSION['user'])): ?>
                <img src="<?= $_SESSION['user']['picture'] ?>" style="width: 35px; height: 35px; border-radius: 50%;">
                <span style="font-weight: 500;"><?= explode(' ', $_SESSION['user']['name'])[0] ?></span>
                <a href="logout.php" class="btn"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary"><i class="fab fa-google"></i> Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <div>
            <h1 style="font-size: 3.5rem; color: white; margin-bottom: 1rem;">Better Living Starts Here</h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">Secure, Affordable and Comfortable PG Accommodations across India.</p>
        </div>
    </header>

    <div class="container">
        <form id="filterForm" class="filter-bar grid" style="grid-template-columns: 2fr 1fr 1fr 1fr auto;" onsubmit="return false;">
            <input type="text" name="search" class="form-input" placeholder="Search Location or PG Name..." autocomplete="off">
            <select name="gender" class="form-input">
                <option value="">Any Gender</option>
                <option value="Boys">Boys Only</option>
                <option value="Girls">Girls Only</option>
                <option value="Co-ed">Co-ed</option>
            </select>
            <input type="number" name="min_price" class="form-input" placeholder="Min Price">
            <input type="number" name="max_price" class="form-input" placeholder="Max Price">
            <button type="button" class="btn btn-primary" onclick="loadListings()" style="padding: 0 2rem;"><i class="fas fa-search"></i> Find</button>
        </form>

        <section id="listingsGrid" class="grid">
            <!-- Listings will be loaded here via AJAX -->
        </section>

        <div id="loadMoreContainer" style="text-align: center; margin-top: 3rem; display: none;">
            <button onclick="loadMore()" class="btn btn-primary" style="padding: 1rem 3rem; background: transparent; color: var(--primary); border: 2px solid var(--primary);">Load More Accommodations</button>
        </div>
    </div>

    <script>
        let currentPage = 1;
        
        async function loadListings(isLoadMore = false) {
            if (!isLoadMore) {
                currentPage = 1;
                document.getElementById('listingsGrid').innerHTML = Array(3).fill('<div class="card" style="height: 350px; background: #eee; animate: pulse 1.5s infinite;"></div>').join('');
            }
            
            const grid = document.getElementById('listingsGrid');
            const form = new FormData(document.getElementById('filterForm'));
            const searchParams = new URLSearchParams(form);
            searchParams.append('page', currentPage);
            
            try {
                const response = await fetch(`api/get_pgs.php?${searchParams.toString()}`);
                const pgs = await response.json();
                
                if (pgs.length === 0 && !isLoadMore) {
                    grid.innerHTML = `<div style="grid-column: 1 / -1; text-align: center; padding: 5rem 0;">
                        <i class="fas fa-search-minus" style="font-size: 4rem; color: var(--border); margin-bottom: 1rem;"></i>
                        <h2>No accommodations found!</h2>
                        <p style="color: var(--text-muted);">Try adjusting your filters or search keywords.</p>
                    </div>`;
                    document.getElementById('loadMoreContainer').style.display = 'none';
                    return;
                }

                const html = pgs.map(pg => `
                    <div class="card">
                        <div class="card-img-container">
                            <img src="${pg.thumb || 'https://via.placeholder.com/400x300?text=No+Image'}" alt="${pg.title}">
                            <div style="position: absolute; top: 1rem; right: 1rem; display:flex; gap:0.5rem; align-items:center;">
                                <button onclick="toggleFavorite(${pg.id}, this)" style="background: white; border: none; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    <i class="fa${isFavorite(pg.id) ? 's' : 'r'} fa-heart" style="color: ${isFavorite(pg.id) ? '#f43f5e' : '#cbd5e1'}; font-size: 1rem;"></i>
                                </button>
                                <span class="badge badge-${pg.gender.toLowerCase()}">${pg.gender}</span>
                            </div>
                        </div>
                        <div style="padding: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                <h3 style="font-size: 1.25rem; font-weight: 700;">${pg.title}</h3>
                                <div style="text-align: right;">
                                    <span style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">₹${new Intl.NumberFormat().format(pg.price)}</span><label style="font-size: 0.8rem; color: var(--text-muted);">/mo</label>
                                </div>
                            </div>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">
                                <i class="fas fa-map-marker-alt"></i> ${pg.location}
                            </p>
                            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                                ${pg.amenities.split(', ').slice(0, 3).map(am => `
                                    <span style="font-size: 0.75rem; background: #eef2ff; color: #4f46e5; padding: 4px 8px; border-radius: 6px;">${am}</span>
                                `).join('')}
                            </div>
                            <a href="pg_detail.php?id=${pg.id}" class="btn btn-primary" style="width: 100%; justify-content: center; background: white; color: var(--primary); border: 1.5px solid var(--primary);">View Details</a>
                        </div>
                    </div>
                `).join('');

                if (isLoadMore) {
                    grid.insertAdjacentHTML('beforeend', html);
                } else {
                    grid.innerHTML = html;
                }

                // If fewer than limit returned, hide load more
                document.getElementById('loadMoreContainer').style.display = (pgs.length < 6) ? 'none' : 'block';

            } catch (error) {
                if (!isLoadMore) grid.innerHTML = '<p style="text-align:center; padding: 3rem; color: #ef4444;">Failed to load data. Please refresh.</p>';
            }
        }

        function loadMore() {
            currentPage++;
            loadListings(true);
        }

        // Favorites Handling
        function toggleFavorite(id, btn) {
            let favs = JSON.parse(localStorage.getItem('fav_pgs') || '[]');
            const icon = btn.querySelector('i');
            
            if (favs.includes(id)) {
                favs = favs.filter(fid => fid !== id);
                icon.className = 'far fa-heart';
                icon.style.color = '#cbd5e1';
            } else {
                favs.push(id);
                icon.className = 'fas fa-heart';
                icon.style.color = '#f43f5e';
                // Trigger micro-animation
                btn.style.transform = 'scale(1.3)';
                setTimeout(() => btn.style.transform = 'scale(1)', 200);
            }
            localStorage.setItem('fav_pgs', JSON.stringify(favs));
        }

        function isFavorite(id) {
            return JSON.parse(localStorage.getItem('fav_pgs') || '[]').includes(id);
        }

        // Live typing search
        let typingTimer;
        document.querySelector('input[name="search"]').addEventListener('keyup', () => {
             clearTimeout(typingTimer);
             typingTimer = setTimeout(loadListings, 500);
        });

        // Initial Load
        window.onload = loadListings;
    </script>
    </div>

    <footer style="margin-top: 5rem; padding: 3rem; background: var(--bg-card); border-top: 1px solid var(--border); text-align: center;">
        <div class="brand" style="font-size: 1.6rem; font-weight: 800; color: var(--primary); margin-bottom: 1rem;">PG Connect</div>
        <p style="color: var(--text-muted);">© 2026 Premium PG Accommodations Platform. All rights reserved.</p>
    </footer>
</body>
</html>
