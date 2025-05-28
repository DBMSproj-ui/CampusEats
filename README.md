📦 CampusEats 🍱

A Laravel-based campus food ordering system
👥 User Roles:
    🎓 Students (Users)
    🏪 Vendors (Clients)
    🛠️ Admins
🎓 Student Features
    Role: users, authenticated via web guard
🛍️ Ordering & Wishlist
    Browse restaurants and food products
    filter food products
    Add items to cart and place orders
    Apply coupons during checkout
    Add restaurants to wishlist
📦 Order Management
    View order list and details
    Download invoice as PDF
    View order status (Pending, Confirmed, Processing, Delivered)
🧑‍💼 Profile
    Update name, email, password, photo, phone, address
    View/edit personal profile
⭐ Ratings & Reviews
    Submit ratings and feedback for vendors
    View vendor review breakdown
🏪 Vendor Features
    Role: clients, authenticated via client guard
🍽️ Menu & Product Management
    Add/edit/delete products and menus
    Upload product images, set prices, apply discounts
    Organize products by category, menu, city
📷 Gallery
    Add/edit/delete restaurant gallery images
🎟️ Coupon Management
    Create and manage discount coupons
    Set validity and target users
📦 Order Fulfillment
    View incoming orders grouped by order ID
    Mark orders as "Processing" and "Delivered"
    View detailed order info per product
📊 Reports
    Filter order reports by date, month, and year
💼 Profile
    Manage restaurant profile (cover photo, address, phone)
    Change vendor password
🛠️ Admin Features
    Role: admins, authenticated via admin guard
🧾 Product & Category Management
    View/add/edit/delete all products
    Manage food categories and cities
🛒 Order Control
    View and manage all orders
    Transition orders through statuses:
        Pending → Confirm → Processing → Delivered
📣 Banners
    Upload and manage homepage banners
🎟️ Approvals
    Approve or reject new vendors
    Change vendor status
📈 Reports
    View and filter reports by date, month, year
⭐ Review Moderation
    Approve/reject user reviews for vendors
🔐 Admin Profile
    Update admin info and password
    Admin dashboard with key stats
🔑 Security & Access
    Role-based route protection using guards and middleware
    Separate login panels for Admin, Vendor, and User
    Session-based access control


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

for adding
git add .
git commit -m "Added X feature"
git push
