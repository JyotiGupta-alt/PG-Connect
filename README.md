# 🏠 PG Connect - Accommodation Platform

A premium, modern, and responsive web application for browsing and managing PG (Paying Guest) accommodations. Built with PHP, MySQL, and a focus on visual excellence.

![Homepage Mockup](https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=1200&auto=format&fit=crop)

## 🚀 Features
- **Modern UI/UX**: Vibrant gradients, glassmorphism, and smooth animations.
- **Smart Filters**: Search by price, gender, and location.
- **Admin Dashboard**: Full CRUD for PG listings with multi-image uploads.
- **Security**: Environment variables for DB credentials, SQL injection protection via PDO.
- **Responsive**: Mobile-first design for all devices.

## 🛠️ Prerequisites
- **PHP**: 8.0 or higher
- **Composer**: For dependency management
- **MySQL / MariaDB**: 5.7 or higher
- **Web Server**: Apache (via XAMPP/WAMP) or PHP's built-in server

## 📦 Installation & Setup

### 1. Clone the Project
```bash
git clone https://github.com/yourusername/pg-accommodation.git
cd pg-accommodation
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
Copy the `.env.example` to `.env` and update your database credentials:
```bash
cp .env.example .env
```
Open `.env` and set:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`

### 4. Database Setup
1. Create a database named `pg_accommodation`.
2. Import `schema.sql` into your MySQL server.
   - **Default Admin**: `admin@pg.com` / `admin123`

### 5. Running Locally
**Using PHP Built-in Server:**
```bash
php -S localhost:8000
```
Visit: `http://localhost:8000`

## 🌐 Production Deployment
To host this project on platforms like **Heroku**, **Railway**, or **DigitalOcean**:

1. **GitHub**: Push your code to a GitHub repository.
2. **Env Vars**: Set the environment variables (`DB_HOST`, etc.) in your hosting provider's dashboard.
3. **Database**: Use a managed MySQL service (like Aiven, Clever Cloud, or the provider's built-in DB).
4. **Composer**: The platform will automatically run `composer install`.

## 🔒 Security Notes
- The `.env` file is ignored by Git to prevent leaking credentials.
- `.htaccess` is included to block access to sensitive files on Apache servers.
- All database queries use PDO prepared statements to prevent SQL Injection.

## 📄 License
MIT License - feel free to use and modify for your own projects!
