-- Use this only when a valid default login reaches a generic 500 page.
-- Select the application database in phpMyAdmin before importing this file.
-- It repairs common older/partial users-table schemas without dropping data.

SET @current_database = DATABASE();

SELECT CONCAT('Repair target database: ', COALESCE(@current_database, '(none selected)')) AS message;

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
    role = 'admin',
    status = 'active'
WHERE username = 'admin';

UPDATE users
SET full_name = CASE WHEN full_name = '' THEN 'Sales Staff' ELSE full_name END,
    role = 'staff',
    status = 'active'
WHERE username = 'staff';

SELECT id, username, email, role, status, last_login, created_at
FROM users
WHERE username IN ('admin', 'staff')
ORDER BY username;
