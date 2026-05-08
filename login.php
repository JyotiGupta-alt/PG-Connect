<?php
require_once 'includes/db.php';
session_start();

/**
 * AUTH0 INTEGRATION (Placeholder logic)
 * In production, you would use Auth0's PHP SDK or a redirect to their /authorize endpoint.
 */

// Simulation of a successful Google Auth0 Redirect
if (isset($_GET['mock_login'])) {
    $user_data = [
        'name' => 'Demo User',
        'email' => 'user@example.com',
        'picture' => 'https://ui-avatars.com/api/?name=Demo+User&background=4f46e5&color=fff',
        'sub' => 'google-oauth2|123456789'
    ];

    // Sync with local database
    $stmt = $pdo->prepare("INSERT INTO users (name, email, profile_pic, auth0_id) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), profile_pic=VALUES(profile_pic)");
    $stmt->execute([$user_data['name'], $user_data['email'], $user_data['picture'], $user_data['sub']]);
    
    $_SESSION['user'] = $user_data;
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | PG Connect</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; height: 100vh; background: #f3f4f6;">

    <div class="card" style="width: 450px; padding: 3rem; text-align: center;">
        <div class="brand" style="font-size: 2.2rem; font-weight: 800; color: var(--primary); margin-bottom: 2rem;">PG CONNECT</div>
        <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">Welcome to the family!</h1>
        <p style="color: var(--text-muted); margin-bottom: 2.5rem;">Join thousands of others finding their next home.</p>

        <a href="?mock_login=1" class="btn btn-primary" style="width: 100%; justify-content: center; height: 55px; background: white; color: var(--text-main); border: 1.5px solid var(--border); margin-bottom: 1rem;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google Logo" style="width: 20px; margin-right: 12px;"> Continue with Google
        </a>
        
        <p style="font-size: 0.8rem; color: var(--text-muted);">
            By clicking "Continue", you agree to our Terms of Service and Privacy Policy.
        </p>

        <div style="margin-top: 3rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <a href="admin/login.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Admin Access</a>
        </div>
    </div>

</body>
</html>
