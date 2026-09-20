-- Use this only when a valid default login reaches a generic 500 page.
-- Select the application database in phpMyAdmin before importing this file.
-- It repairs common older/partial users-table schemas without dropping data.

SET @current_database = DATABASE();

SELECT CONCAT('Repair target database: ', COALESCE(@current_database, '(none selected)')) AS message;

CREATE TABLE IF NOT EXISTS users (
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

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE users ADD COLUMN full_name VARCHAR(120) NOT NULL DEFAULT '''' AFTER id',
        'SELECT ''users.full_name already exists'' AS message'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @current_database
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'full_name'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE users ADD COLUMN email VARCHAR(120) NULL AFTER username',
        'SELECT ''users.email already exists'' AS message'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @current_database
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'email'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE users ADD COLUMN role ENUM(''admin'', ''staff'') NOT NULL DEFAULT ''staff'' AFTER password',
        'SELECT ''users.role already exists'' AS message'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @current_database
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'role'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE users ADD COLUMN status ENUM(''active'', ''inactive'') NOT NULL DEFAULT ''active'' AFTER role',
        'SELECT ''users.status already exists'' AS message'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @current_database
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'status'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL AFTER status',
        'SELECT ''users.last_login already exists'' AS message'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @current_database
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'last_login'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE users ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER last_login',
        'SELECT ''users.created_at already exists'' AS message'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @current_database
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'created_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE users ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at',
        'SELECT ''users.updated_at already exists'' AS message'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = @current_database
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'updated_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO users (full_name, username, email, password, role, status)
SELECT 'System Administrator', 'admin', 'admin@example.com',
       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.', 'admin', 'active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');

INSERT INTO users (full_name, username, email, password, role, status)
SELECT 'Sales Staff', 'staff', 'staff@example.com',
       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.', 'staff', 'active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'staff');

UPDATE users
SET full_name = CASE WHEN full_name = '' THEN 'System Administrator' ELSE full_name END,
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.',
    role = 'admin',
    status = 'active'
WHERE username = 'admin';

UPDATE users
SET full_name = CASE WHEN full_name = '' THEN 'Sales Staff' ELSE full_name END,
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.',
    role = 'staff',
    status = 'active'
WHERE username = 'staff';

-- A successful login immediately loads dashboard data from these tables. Create
-- any tables that were skipped during an earlier or partial schema import.
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_name (category_name),
    INDEX idx_categories_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
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
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sales (
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
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sale_items (
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
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS stock_movements (
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
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(80) NOT NULL,
    setting_value TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_settings_key (setting_key)
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('business_name', 'Local Business'),
('business_address', '123 Market Road, Lagos'),
('business_phone', '+234 800 000 0000'),
('business_email', 'info@example.com'),
('currency', 'NGN '),
('low_stock_default', '5'),
('receipt_footer', 'Thank you for your patronage.')
ON DUPLICATE KEY UPDATE setting_key = VALUES(setting_key);

SELECT id, username, email, role, status, last_login, created_at
FROM users
WHERE username IN ('admin', 'staff')
ORDER BY username;
