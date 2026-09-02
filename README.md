# Inventory Management System for a Local Business

This is a PHP/MySQL inventory and sales management system for a local business. It includes authentication, role-based access control, products, categories, stock tracking, sales recording, automatic stock deduction, receipts, reports, user management, profile management, and business settings.

## Requirements

- PHP 8.2 or newer
- MySQL 8 or MariaDB equivalent
- Apache
- XAMPP for local development
- Modern web browser

## Local Installation with XAMPP

1. Install XAMPP.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Copy this project folder to `C:\xampp\htdocs\inventory-management-system`.
4. Open phpMyAdmin at `http://localhost/phpmyadmin`.
5. Import `database/inventory_management.sql`.
6. Optional: import `database/seed.sql` for sample categories and products.
7. Open `config.php` and confirm the database settings:
   - host: `127.0.0.1`
   - database: `inventory_management`
   - user: `root`
   - password: empty by default on XAMPP
8. Visit `http://localhost/inventory-management-system/`.
9. Log in and change the default passwords.

## Default Development Login

- Admin: `admin` / `password`
- Staff: `staff` / `password`

## Application URL

Default XAMPP URL:

```text
http://localhost/inventory-management-system/
```

The app uses reliable query-string routing by default, such as:

```text
http://localhost/inventory-management-system/?route=dashboard
```

To use clean URLs, enable Apache rewrite support and set `clean_urls` to `true` in `config.php`.

## Core Modules

- Authentication: login, logout, secure sessions, inactive-user protection
- Authorization: admin and staff roles
- Dashboard: live product, category, stock, low-stock, sales, and transaction statistics
- Categories: create, edit, search, activate/deactivate, safe deletion
- Products: create, edit, view, search, filter, deactivate/delete where safe
- Inventory: stock-in, adjustments, low-stock view, stock movement history
- Sales: multi-product sale screen, server-side totals, transaction-safe stock deduction
- Receipts: printable invoice/receipt view
- Reports: inventory, sales, low stock, stock movements, product sales
- Administration: users and business settings
- Profile: profile update and password change

## Important Files

- `public/index.php`: front controller and route definitions
- `app/Core/Router.php`: application router
- `app/Core/Database.php`: PDO connection
- `app/Services/SalesService.php`: transactional sale processing
- `app/Services/InventoryService.php`: stock-in and adjustment logic
- `database/inventory_management.sql`: database schema and default users
- `database/seed.sql`: optional sample data
- `views/`: HTML/PHP views
- `public/assets/css/app.css`: application styling
- `public/assets/js/app.js`: interactive sale screen and layout behavior

## Hostinger or cPanel Deployment

1. Upload the project files using File Manager or FTP.
2. Point the domain or subdomain document root to the project folder, or to `public` if your host supports custom web roots.
3. Create a MySQL database in cPanel.
4. Create a MySQL user.
5. Assign the user to the database with required privileges.
6. Import `database/inventory_management.sql` into that database.
7. Optional: import `database/seed.sql`.
8. Edit `config.php` with the production database name, username, and password.
9. Set `debug` to `false`.
10. Set `base_url` to the production URL.
11. If the web root points directly to `public`, set `asset_prefix` to an empty string.
12. Configure `.htaccess` if using clean URLs.
13. Confirm `storage/logs` is writable.
14. Remove or change default development credentials.
15. Test login, product creation, sale creation, receipt printing, and reports.

## Security Notes

- Passwords are stored with PHP password hashes.
- SQL uses PDO prepared statements.
- Forms use CSRF tokens.
- Sessions are regenerated after login.
- Staff accounts cannot access administrator pages.
- Sales are processed inside database transactions.
- Stock updates use row locking and conditional updates to prevent overselling.

## Validation

This workspace did not have `php` or `mysql` available on PATH during generation. After installing XAMPP, run syntax checks with:

```powershell
C:\xampp\php\php.exe -l public\index.php
```

Repeat for files in `app` or use your editor's PHP diagnostics.
