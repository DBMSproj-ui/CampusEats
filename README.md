# CampusEats 🍱

CampusEats is a Laravel-based food ordering system built for campus environments. It includes features for students, vendors, and admins.

## 🚀 Features
- User roles: Students, Vendors, Admins
- Product listing, approval, and ordering
- Role-based dashboards
- Profile management, location filters
- Ratings and wishlist functionality

## ⚙️ Installation
```bash
git clone https://github.com/DBMSproj-ui/CampusEats.git
cd CampusEats
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
