# VogueVault Database Schema Documentation

## Overview
VogueVault is a dynamic fashion marketplace built with Laravel, featuring a comprehensive database schema designed for scalability and performance. The database supports user management, product catalog, shopping cart functionality, order processing, and customer reviews.

## Database Configuration
- **Database Engine**: MySQL
- **Database Name**: voguevault
- **Character Set**: utf8mb4
- **Collation**: utf8mb4_unicode_ci

## Color Palette
The UI/UX follows the defined color palette:
- Primary: #819A91 (Sage Green)
- Secondary: #A7C1A8 (Light Green)
- Tertiary: #D1D8BE (Pale Green)
- Background: #EEEFE0 (Off-white)

## Database Tables

### 1. Users Table
Manages user accounts with role-based access control.

**Fields:**
- `id` (Primary Key) - Auto-incrementing user identifier
- `name` (String) - Full name of the user
- `email` (String, Unique) - Email address for login
- `email_verified_at` (Timestamp, Nullable) - Email verification timestamp
- `password` (String) - Hashed password
- `role` (Enum: 'user', 'admin') - User role, defaults to 'user'
- `phone` (String, Nullable) - Contact phone number
- `address` (Text, Nullable) - User's address
- `remember_token` (String, Nullable) - "Remember me" functionality
- `created_at` / `updated_at` (Timestamps) - Record timestamps

**Relationships:**
- Has many `carts`
- Has many `orders` 
- Has many `product_reviews`
- Has many `wishlists`

### 2. Categories Table
Organizes products into hierarchical categories.

**Fields:**
- `id` (Primary Key) - Category identifier
- `name` (String) - Category display name
- `slug` (String, Unique) - URL-friendly identifier
- `description` (Text, Nullable) - Category description
- `image` (String, Nullable) - Category image path
- `is_active` (Boolean) - Category visibility status
- `created_at` / `updated_at` (Timestamps)

**Relationships:**
- Has many `products`

### 3. Products Table
Central product catalog with comprehensive product information.

**Fields:**
- `id` (Primary Key) - Product identifier
- `name` (String) - Product name
- `slug` (String, Unique) - SEO-friendly URL
- `description` (Text, Nullable) - Full product description
- `short_description` (Text, Nullable) - Brief product summary
- `price` (Decimal 10,2) - Regular selling price
- `sale_price` (Decimal 10,2, Nullable) - Discounted price
- `sku` (String, Unique) - Stock Keeping Unit
- `stock` (Integer) - Available quantity
- `min_stock` (Integer) - Minimum stock threshold
- `category_id` (Foreign Key) - References categories table
- `images` (JSON, Nullable) - Product image paths array
- `attributes` (JSON, Nullable) - Size, color, material specifications
- `is_active` (Boolean) - Product visibility
- `is_featured` (Boolean) - Featured product flag
- `weight` (Decimal 8,2, Nullable) - Product weight for shipping
- `dimensions` (String, Nullable) - Product dimensions
- `created_at` / `updated_at` (Timestamps)

**Relationships:**
- Belongs to `category`
- Has many `cart_items`
- Has many `order_items`
- Has many `product_reviews`
- Has many `wishlists`

### 4. Carts Table
Shopping cart sessions for users and guests.

**Fields:**
- `id` (Primary Key) - Cart identifier
- `user_id` (Foreign Key) - References users table
- `session_id` (String, Nullable) - Guest session identifier
- `total_amount` (Decimal 10,2) - Cart total value
- `total_items` (Integer) - Number of items in cart
- `created_at` / `updated_at` (Timestamps)

**Relationships:**
- Belongs to `user`
- Has many `cart_items`

### 5. Cart Items Table
Individual items within shopping carts.

**Fields:**
- `id` (Primary Key) - Cart item identifier
- `cart_id` (Foreign Key) - References carts table
- `product_id` (Foreign Key) - References products table
- `quantity` (Integer) - Number of items
- `price` (Decimal 10,2) - Price at time of adding to cart
- `product_attributes` (JSON, Nullable) - Selected size, color, etc.
- `attributes_hash` (String, Nullable) - Hash for uniqueness constraint
- `created_at` / `updated_at` (Timestamps)

**Constraints:**
- Unique constraint on `cart_id`, `product_id`, `attributes_hash`

**Relationships:**
- Belongs to `cart`
- Belongs to `product`

### 6. Orders Table
Customer order information and status tracking.

**Fields:**
- `id` (Primary Key) - Order identifier
- `order_number` (String, Unique) - Human-readable order number
- `user_id` (Foreign Key) - References users table
- `status` (Enum) - Order status: pending, processing, shipped, delivered, cancelled, refunded
- `subtotal` (Decimal 10,2) - Order subtotal
- `tax_amount` (Decimal 10,2) - Applied tax
- `shipping_cost` (Decimal 10,2) - Shipping charges
- `discount_amount` (Decimal 10,2) - Applied discounts
- `total_amount` (Decimal 10,2) - Final order total
- `currency` (String 3) - Currency code (default: USD)

**Billing Address Fields:**
- `billing_first_name`, `billing_last_name`
- `billing_email`, `billing_phone`
- `billing_address`, `billing_city`, `billing_state`
- `billing_postal_code`, `billing_country`

**Shipping Address Fields:**
- `shipping_first_name`, `shipping_last_name`
- `shipping_phone`
- `shipping_address`, `shipping_city`, `shipping_state`
- `shipping_postal_code`, `shipping_country`

**Additional Fields:**
- `payment_method` (String, Nullable) - Payment method used
- `payment_status` (String) - Payment status (default: pending)
- `shipping_method` (String, Nullable) - Selected shipping method
- `tracking_number` (String, Nullable) - Shipment tracking number
- `notes` (Text, Nullable) - Order notes
- `shipped_at` (Timestamp, Nullable) - Shipment date
- `delivered_at` (Timestamp, Nullable) - Delivery date
- `created_at` / `updated_at` (Timestamps)

**Relationships:**
- Belongs to `user`
- Has many `order_items`

### 7. Order Items Table
Individual products within orders.

**Fields:**
- `id` (Primary Key) - Order item identifier
- `order_id` (Foreign Key) - References orders table
- `product_id` (Foreign Key) - References products table
- `product_name` (String) - Product name at time of order
- `product_sku` (String) - Product SKU at time of order
- `product_price` (Decimal 10,2) - Product price at time of order
- `quantity` (Integer) - Quantity ordered
- `total_price` (Decimal 10,2) - Line total (quantity × price)
- `product_attributes` (JSON, Nullable) - Selected attributes
- `created_at` / `updated_at` (Timestamps)

**Relationships:**
- Belongs to `order`
- Belongs to `product`

### 8. Product Reviews Table
Customer product ratings and reviews.

**Fields:**
- `id` (Primary Key) - Review identifier
- `product_id` (Foreign Key) - References products table
- `user_id` (Foreign Key) - References users table
- `rating` (Integer) - Rating from 1-5 stars
- `title` (String, Nullable) - Review title
- `review` (Text, Nullable) - Review content
- `is_verified_purchase` (Boolean) - Verified purchase flag
- `is_approved` (Boolean) - Review approval status
- `created_at` / `updated_at` (Timestamps)

**Constraints:**
- Unique constraint on `product_id`, `user_id` (one review per user per product)

**Relationships:**
- Belongs to `product`
- Belongs to `user`

### 9. Wishlists Table
User wishlist/favorites functionality.

**Fields:**
- `id` (Primary Key) - Wishlist item identifier
- `user_id` (Foreign Key) - References users table
- `product_id` (Foreign Key) - References products table
- `created_at` / `updated_at` (Timestamps)

**Constraints:**
- Unique constraint on `user_id`, `product_id`

**Relationships:**
- Belongs to `user`
- Belongs to `product`

### 10. Additional Laravel Tables

**Password Reset Tokens:**
- Manages password reset functionality
- Fields: `email` (Primary), `token`, `created_at`

**Sessions:**
- User session management
- Fields: `id` (Primary), `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`

**Cache Table:**
- Application caching
- Fields: `key` (Primary), `value`, `expiration`

**Jobs Table:**
- Queue job management
- Fields: `id` (Primary), `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`

## Database Relationships Summary

```
Users (1) → (Many) Carts → (Many) Cart Items → (Many) Products
Users (1) → (Many) Orders → (Many) Order Items → (Many) Products
Users (1) → (Many) Product Reviews → (Many) Products
Users (1) → (Many) Wishlists → (Many) Products
Categories (1) → (Many) Products
```

## Indexing Strategy

**Primary Indexes:**
- All tables have primary key indexes on `id` fields

**Unique Indexes:**
- `users.email`
- `categories.slug`
- `products.slug`
- `products.sku`
- `orders.order_number`

**Foreign Key Indexes:**
- All foreign key columns are automatically indexed
- Improves join performance and referential integrity

**Composite Indexes:**
- `cart_items`: (cart_id, product_id, attributes_hash)
- `product_reviews`: (product_id, user_id)
- `wishlists`: (user_id, product_id)

## Data Types and Constraints

**Decimal Precision:**
- All price fields use DECIMAL(10,2) for accurate financial calculations
- Weight uses DECIMAL(8,2) for product specifications

**JSON Fields:**
- `products.images` - Array of image paths
- `products.attributes` - Product specifications
- `cart_items.product_attributes` - Selected options
- `order_items.product_attributes` - Ordered specifications

**Enum Fields:**
- `users.role`: 'user', 'admin'
- `orders.status`: 'pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'

## Security Considerations

1. **Password Security**: All passwords are hashed using Laravel's default bcrypt
2. **SQL Injection Prevention**: Laravel ORM provides automatic query parameter binding
3. **Mass Assignment Protection**: Model fillable/guarded properties control data access
4. **Foreign Key Constraints**: Maintain referential integrity with CASCADE deletes where appropriate
5. **Input Validation**: Server-side validation through Laravel form requests

## Performance Optimization

1. **Indexing**: Strategic indexes on frequently queried columns
2. **JSON Storage**: Flexible attribute storage without schema changes
3. **Normalized Design**: Reduces data redundancy while maintaining query efficiency
4. **Caching Strategy**: Built-in Laravel caching for frequently accessed data
5. **Eager Loading**: Prevent N+1 query problems with relationship loading

## Sample Data

The database includes seed data with:
- 1 Admin user and 1 customer user
- 3 Product categories (Women's, Men's, Accessories)
- 1 Sample product with complete attributes
- Authentication and session management setup

## Migration Commands

```bash
# Run all migrations
php artisan migrate

# Fresh migration (drops all tables and recreates)
php artisan migrate:fresh

# Seed the database
php artisan db:seed

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

This schema provides a robust foundation for the VogueVault fashion marketplace, supporting all required functionality while maintaining scalability and performance.