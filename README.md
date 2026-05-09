# 🏠 PG Connect - Premium PG Accommodation Platform

**PG Connect** is a full-stack, modern web application designed to bridge the gap between PG owners and students/professionals looking for comfortable, affordable, and secure accommodations. The platform features a premium user experience with live search, dynamic filtering, and a powerful admin dashboard for listing management.

---

## 📸 Visual Showcase

### 🔍 Explore Accommodations
| | |
|:---:|:---:|
| ![Home Hero](https://github.com/user-attachments/assets/4ef43ada-a3a8-41a4-9153-b124d6d9c6f1) | ![Search & Filters](https://github.com/user-attachments/assets/1aac85a0-56dc-4347-b54a-04a38edcccf4) |
| ![Listing Grid](https://github.com/user-attachments/assets/3372466d-a14b-40cd-ad13-1b6cd6b3ea45) | |

### 📖 Detailed Listings
| | |
|:---:|:---:|
| ![PG Detail View](https://github.com/user-attachments/assets/ff83e96e-9beb-408e-8275-f653f6c8b621) | ![Amenities & Description](https://github.com/user-attachments/assets/73b75dff-2fe4-4483-9d4a-5c8e6fd90341) |
| ![Contact & Location](https://github.com/user-attachments/assets/e50354f3-f21a-4537-9772-3f906fa58aa2) | |

### 🛠️ Admin Management
| | |
|:---:|:---:|
| ![Admin Login](https://github.com/user-attachments/assets/1ef0b4b1-8d3a-46ee-8738-6d45bde1308e) | ![Admin Dashboard](https://github.com/user-attachments/assets/23ab451c-f7d9-4b7f-8a3e-f217ee60a82b) |
| ![Adding New PG](https://github.com/user-attachments/assets/e11474ea-17a6-424f-aaad-99b419f46786) | ![Image Uploads](https://github.com/user-attachments/assets/e46c33e5-075b-4e8d-95ce-7b6144214b33) |
| ![Editing Listings](https://github.com/user-attachments/assets/0452a1d0-eb7d-4ca2-ab66-c0fd34210378) | ![Dashboard Overview](https://github.com/user-attachments/assets/56e72871-c86d-4577-b16d-e9a1d72793fe) |
| ![Listing Actions](https://github.com/user-attachments/assets/f1d28614-97d8-4e70-a707-d2c245fef395) | |

---

## ✨ Features

- **Dynamic Search**: Live search by location or PG name with instant results.
- **Advanced Filtering**: Filter by Price Range (Min/Max), Gender (Boys, Girls, Co-ed), and room types.
- **Rich Media**: Multi-image support for each listing with a premium gallery view.
- **Interactive UI**: Micro-animations for favorite buttons and smooth transitions.
- **User Authentication**: Integrated with Auth0 for secure user login.
- **Admin Dashboard**: Comprehensive CRUD operations for listings, including image management and availability toggles.
- **Mobile Responsive**: Fully optimized for all screen sizes.

---

## 🚀 Technology Stack

- **Frontend**: HTML5, CSS3 (Vanilla with modern design patterns), JavaScript (Fetch API, AJAX).
- **Backend**: PHP 7.4+ (Vanilla PHP for core logic).
- **Database**: MySQL (Relational data management).
- **Authentication**: Auth0 (User) & Session-based Secure Hash (Admin).
- **Icons**: FontAwesome 6.4.0.
- **Styling**: Glassmorphism, CSS Gradients, and Shadow tokens.

---

## 🏗️ High-Level Design (HLD)

The system follows a **Modular Client-Server Architecture**:

1.  **Presentation Layer**: Built using Semantic HTML and Vanilla CSS. It uses AJAX (Fetch) to communicate with the backend for real-time searching without page reloads.
2.  **Application Logic Layer**: PHP scripts handle request processing, session management, and business rules.
    - `index.php`: Main entry point with search logic.
    - `api/get_pgs.php`: JSON endpoint for dynamic listings.
    - `admin/`: Secure zone for management.
3.  **Data Access Layer**: Uses PHP Data Objects (PDO) for secure, prepared SQL queries to prevent SQL injection.
4.  **Storage**: 
    - **MySQL**: Stores metadata (prices, locations, user info).
    - **Local Filesystem**: Stores uploaded images in the `uploads/` directory.

### Data Flow Diagram
```mermaid
graph LR
    User((User)) -->|Search/Filter| Frontend[Web UI]
    Frontend -->|AJAX Fetch| API[PHP API Endpoint]
    API -->|SQL Query| DB[(MySQL Database)]
    DB -->|Result Set| API
    API -->|JSON Data| Frontend
    Frontend -->|Render| User

    Admin((Admin)) -->|Manage Listings| Dashboard[Admin Dashboard]
    Dashboard -->|Upload Images| ServerFS[Server Filesystem]
    Dashboard -->|Update Metadata| DB
```

---

## 📊 Database Schema

The database is structured to handle relational data efficiently with cascading deletes for image management.

```mermaid
erDiagram
    admins {
        int id PK
        string email
        string password
    }
    users {
        int id PK
        string name
        string email
        string profile_pic
        string auth0_id
    }
    pg_listings {
        int id PK
        string title
        string location
        decimal price
        enum gender
        string room_type
        text amenities
        text description
        string contact
        boolean is_available
        timestamp created_at
    }
    pg_images {
        int id PK
        int pg_id FK
        string image_path
    }

    pg_listings ||--o{ pg_images : "has many"
```

### Table Definitions:
- **admins**: Stores administrative credentials.
- **users**: Syncs user data from Auth0.
- **pg_listings**: Core data for accommodations.
- **pg_images**: Links multiple image paths to a single listing.

---

## 🛠️ Installation & Setup

1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/JyotiGupta-alt/PG-Connect.git
    ```
2.  **Database Configuration**:
    - Import `schema.sql` into your MySQL server.
    - Update `includes/db.php` or `.env` with your database credentials (`host`, `dbname`, `username`, `password`).
3.  **Configure Auth0**:
    - Add your Auth0 Domain, Client ID, and Client Secret to the configuration.
4.  **Run Locally**:
    - Use XAMPP, WAMP, or any PHP server.
    - Ensure the `uploads/` directory is writable.

---

© 2026 PG Connect | Designed for Excellence.
