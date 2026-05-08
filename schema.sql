-- Create Database
CREATE DATABASE IF NOT EXISTS pg_accommodation;
USE pg_accommodation;

-- Admin Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- User Table (Auth0 Sync)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    profile_pic TEXT,
    auth0_id VARCHAR(255) UNIQUE
);

-- PG Listings Table
CREATE TABLE IF NOT EXISTS pg_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    gender ENUM('Boys', 'Girls', 'Co-ed') NOT NULL,
    room_type VARCHAR(100) NOT NULL,
    amenities TEXT, -- Comma-separated or JSON string
    description TEXT,
    contact VARCHAR(20) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PG Images Table
CREATE TABLE IF NOT EXISTS pg_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pg_id INT NOT NULL,
    image_path TEXT NOT NULL,
    FOREIGN KEY (pg_id) REFERENCES pg_listings(id) ON DELETE CASCADE
);

-- Default Admin (Password: admin123)
-- Hash generated using password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO admins (email, password) VALUES ('admin@pg.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') ON DUPLICATE KEY UPDATE email=email;
