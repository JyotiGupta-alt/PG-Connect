<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, email, password FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        setAdminSession($admin['id'], $admin['email']);
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | PG Accommodation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">

    <div class="card" style="width: 400px; padding: 3rem; margin: 1rem; border-radius: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <i class="fas fa-user-shield" style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;"></i>
            <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem;">Admin Login</h1>
            <p style="color: var(--text-muted);">Welcome back! Please login to your account.</p>
        </div>

        <?php if ($error): ?>
        <div style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center;">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="admin@pg.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 1rem;">
                Sign In
            </button>
        </form>
    </div>

</body>
</html>
