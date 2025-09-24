# VogueVault Database Entity Relationship Diagram

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│     USERS       │     │   CATEGORIES    │     │    PRODUCTS     │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ id (PK)         │     │ id (PK)         │     │ id (PK)         │
│ name            │     │ name            │     │ name            │
│ email (UNIQUE)  │     │ slug (UNIQUE)   │     │ slug (UNIQUE)   │
│ password        │     │ description     │     │ description     │
│ role (ENUM)     │     │ image           │     │ short_desc      │
│ phone           │     │ is_active       │     │ price           │
│ address         │     │ created_at      │     │ sale_price      │
│ email_verified  │     │ updated_at      │     │ sku (UNIQUE)    │
│ remember_token  │     └─────────────────┘     │ stock           │
│ created_at      │              │               │ min_stock       │
│ updated_at      │              │               │ category_id (FK)│
└─────────────────┘              │               │ images (JSON)   │
         │                       │               │ attributes(JSON)│
         │                       └─────────┐     │ is_active       │
         │                                 │     │ is_featured     │
         │                                 │     │ weight          │
         │                                 │     │ dimensions      │
         │                                 └────▶│ created_at      │
         │                                       │ updated_at      │
         │                                       └─────────────────┘
         │                                                │
         │                                                │
         ▼                                                ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│      CARTS      │     │   CART_ITEMS    │     │ PRODUCT_REVIEWS │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤
│ id (PK)         │     │ id (PK)         │     │ id (PK)         │
│ user_id (FK)    │────▶│ cart_id (FK)    │     │ product_id (FK) │──┐
│ session_id      │     │ product_id (FK) │──┐  │ user_id (FK)    │  │
│ total_amount    │     │ quantity        │  │  │ rating (1-5)    │  │
│ total_items     │     │ price           │  │  │ title           │  │
│ created_at      │     │ prod_attrs(JSON)│  │  │ review          │  │
│ updated_at      │     │ attrs_hash      │  │  │ is_verified     │  │
└─────────────────┘     │ created_at      │  │  │ is_approved     │  │
         │               │ updated_at      │  │  │ created_at      │  │
         │               └─────────────────┘  │  │ updated_at      │  │
         │                        │          │  └─────────────────┘  │
         │                        └──────────┘            │          │
         │                                                │          │
         ▼                                                ▼          │
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐  │
│     ORDERS      │     │   ORDER_ITEMS   │     │   WISHLISTS     │  │
├─────────────────┤     ├─────────────────┤     ├─────────────────┤  │
│ id (PK)         │     │ id (PK)         │     │ id (PK)         │  │
│ order_number    │────▶│ order_id (FK)   │     │ user_id (FK)    │  │
│ user_id (FK)    │     │ product_id (FK) │──┐  │ product_id (FK) │──┘
│ status (ENUM)   │     │ product_name    │  │  │ created_at      │
│ subtotal        │     │ product_sku     │  │  │ updated_at      │
│ tax_amount      │     │ product_price   │  │  └─────────────────┘
│ shipping_cost   │     │ quantity        │  │
│ discount_amount │     │ total_price     │  │
│ total_amount    │     │ prod_attrs(JSON)│  │
│ currency        │     │ created_at      │  │
│ billing_*       │     │ updated_at      │  │
│ shipping_*      │     └─────────────────┘  │
│ payment_method  │              │           │
│ payment_status  │              │           │
│ shipping_method │              │           │
│ tracking_number │              │           │
│ notes           │              │           │
│ shipped_at      │              └───────────┘
│ delivered_at    │
│ created_at      │
│ updated_at      │
└─────────────────┘

RELATIONSHIPS:
═══════════════

Users (1) ──── (Many) Carts
Users (1) ──── (Many) Orders  
Users (1) ──── (Many) Product_Reviews
Users (1) ──── (Many) Wishlists

Categories (1) ──── (Many) Products

Products (1) ──── (Many) Cart_Items
Products (1) ──── (Many) Order_Items
Products (1) ──── (Many) Product_Reviews
Products (1) ──── (Many) Wishlists

Carts (1) ──── (Many) Cart_Items

Orders (1) ──── (Many) Order_Items

KEY FEATURES:
════════════

✅ Role-based User Management (admin/user)
✅ Product Catalog with Categories
✅ Shopping Cart with Session Support
✅ Complete Order Management
✅ Customer Reviews & Ratings
✅ Wishlist/Favorites System
✅ JSON Attributes for Flexible Product Data
✅ Comprehensive Address Management
✅ Inventory Tracking
✅ Sales Price Support
✅ Order Status Tracking
✅ Payment & Shipping Integration Ready

CONSTRAINTS:
═══════════

• Unique constraints on email, slugs, SKUs, order numbers
• Foreign key constraints with CASCADE deletes
• Composite unique constraints for cart_items and reviews
• JSON validation for product attributes
• Enum constraints for user roles and order status
```