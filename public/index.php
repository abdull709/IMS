<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Router;

$router = new Router();

$router->get('', 'AuthController@loginForm');
$router->get('login', 'AuthController@loginForm');
$router->post('login', 'AuthController@login');
$router->get('logout', 'AuthController@logout');

$router->get('dashboard', 'DashboardController@index');

$router->get('categories', 'CategoryController@index');
$router->get('categories/create', 'CategoryController@create');
$router->post('categories/store', 'CategoryController@store');
$router->get('categories/edit', 'CategoryController@edit');
$router->post('categories/update', 'CategoryController@update');
$router->post('categories/delete', 'CategoryController@delete');

$router->get('products', 'ProductController@index');
$router->get('products/create', 'ProductController@create');
$router->post('products/store', 'ProductController@store');
$router->get('products/view', 'ProductController@view');
$router->get('products/edit', 'ProductController@edit');
$router->post('products/update', 'ProductController@update');
$router->post('products/delete', 'ProductController@delete');

$router->get('inventory', 'InventoryController@index');
$router->post('inventory/stock-in', 'InventoryController@stockIn');
$router->post('inventory/adjust', 'InventoryController@adjust');
$router->get('inventory/movements', 'InventoryController@movements');
$router->get('inventory/low-stock', 'InventoryController@lowStock');

$router->get('sales', 'SaleController@index');
$router->get('sales/create', 'SaleController@create');
$router->post('sales/store', 'SaleController@store');
$router->get('sales/view', 'SaleController@view');
$router->get('api/products/search', 'SaleController@searchProducts');

$router->get('reports', 'ReportController@index');
$router->get('reports/inventory', 'ReportController@inventory');
$router->get('reports/sales', 'ReportController@sales');
$router->get('reports/low-stock', 'ReportController@lowStock');
$router->get('reports/stock-movements', 'ReportController@stockMovements');
$router->get('reports/product-sales', 'ReportController@productSales');

$router->get('users', 'UserController@index');
$router->get('users/create', 'UserController@create');
$router->post('users/store', 'UserController@store');
$router->get('users/edit', 'UserController@edit');
$router->post('users/update', 'UserController@update');
$router->post('users/delete', 'UserController@delete');

$router->get('profile', 'ProfileController@index');
$router->post('profile/update', 'ProfileController@update');
$router->post('profile/password', 'ProfileController@password');

$router->get('settings', 'SettingsController@index');
$router->post('settings/update', 'SettingsController@update');

$router->get('diagnostics/errors', 'DiagnosticsController@errors');

$router->dispatch();
