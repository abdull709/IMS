-- Select the IMS_database database in phpMyAdmin before importing this file.
SET NAMES utf8mb4;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS stock_movements;
DROP TABLE IF EXISTS sale_items;
DROP TABLE IF EXISTS sales;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    username VARCHAR(60) NOT NULL,
    email VARCHAR(120) NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email),
    INDEX idx_users_role_status (role, status)
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_name (category_name),
    INDEX idx_categories_status (status)
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(40) NOT NULL,
    product_name VARCHAR(160) NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    description TEXT NULL,
    cost_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    selling_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    quantity INT NOT NULL DEFAULT 0,
    reorder_level INT NOT NULL DEFAULT 5,
    unit VARCHAR(30) NOT NULL DEFAULT 'pcs',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_products_code (product_code),
    INDEX idx_products_name (product_name),
    INDEX idx_products_category (category_id),
    INDEX idx_products_status_stock (status, quantity, reorder_level),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_products_quantity_nonnegative CHECK (quantity >= 0),
    CONSTRAINT chk_products_reorder_nonnegative CHECK (reorder_level >= 0),
    CONSTRAINT chk_products_cost_nonnegative CHECK (cost_price >= 0),
    CONSTRAINT chk_products_selling_nonnegative CHECK (selling_price >= 0)
) ENGINE=InnoDB;

CREATE TABLE sales (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(40) NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    sale_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    discount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    amount_paid DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    payment_status ENUM('paid', 'partial', 'unpaid') NOT NULL DEFAULT 'unpaid',
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_sales_invoice (invoice_number),
    INDEX idx_sales_user_date (user_id, sale_date),
    INDEX idx_sales_date (sale_date),
    CONSTRAINT fk_sales_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_sales_amounts_nonnegative CHECK (subtotal >= 0 AND discount >= 0 AND total_amount >= 0 AND amount_paid >= 0 AND balance >= 0)
) ENGINE=InnoDB;

CREATE TABLE sale_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sale_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sale_items_sale (sale_id),
    INDEX idx_sale_items_product (product_id),
    CONSTRAINT fk_sale_items_sale
        FOREIGN KEY (sale_id) REFERENCES sales(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_sale_items_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_sale_items_quantity_positive CHECK (quantity > 0),
    CONSTRAINT chk_sale_items_amounts_nonnegative CHECK (unit_price >= 0 AND subtotal >= 0)
) ENGINE=InnoDB;

CREATE TABLE stock_movements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    movement_type ENUM('opening_stock', 'stock_in', 'sale', 'adjustment_in', 'adjustment_out') NOT NULL,
    quantity INT NOT NULL,
    quantity_before INT NOT NULL,
    quantity_after INT NOT NULL,
    reference_type VARCHAR(40) NULL,
    reference_id INT UNSIGNED NULL,
    remarks VARCHAR(255) NULL,
    user_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_stock_movements_product_date (product_id, created_at),
    INDEX idx_stock_movements_type (movement_type),
    INDEX idx_stock_movements_user (user_id),
    CONSTRAINT fk_stock_movements_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_stock_movements_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_stock_movements_quantities CHECK (quantity > 0 AND quantity_before >= 0 AND quantity_after >= 0)
) ENGINE=InnoDB;

CREATE TABLE settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(80) NOT NULL,
    setting_value TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_settings_key (setting_key)
) ENGINE=InnoDB;

INSERT INTO users (full_name, username, email, password, role, status) VALUES
('System Administrator', 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.', 'admin', 'active'),
('Sales Staff', 'staff', 'staff@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.', 'staff', 'active');

INSERT INTO settings (setting_key, setting_value) VALUES
('business_name', 'Local Business'),
('business_address', '123 Market Road, Lagos'),
('business_phone', '+234 800 000 0000'),
('business_email', 'info@example.com'),
('currency', 'NGN '),
('low_stock_default', '5'),
('receipt_footer', 'Thank you for your patronage.');
