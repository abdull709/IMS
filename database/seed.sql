USE inventory_management;

INSERT INTO categories (category_name, description, status) VALUES
('Beverages', 'Drinks and refreshment products.', 'active'),
('Food Items', 'Packaged and staple food items.', 'active'),
('Electronics', 'Small electronic products and accessories.', 'active'),
('Stationery', 'Office and school supplies.', 'active'),
('Household Items', 'Basic household products.', 'active')
ON DUPLICATE KEY UPDATE description = VALUES(description), status = VALUES(status);

INSERT INTO products
    (product_code, product_name, category_id, description, cost_price, selling_price, quantity, reorder_level, unit, status)
SELECT 'PRD-1001', 'Bottle Water Pack', id, 'Pack of bottled water.', 1200.00, 1500.00, 40, 10, 'pack', 'active'
FROM categories WHERE category_name = 'Beverages'
ON DUPLICATE KEY UPDATE product_name = VALUES(product_name);

INSERT INTO products
    (product_code, product_name, category_id, description, cost_price, selling_price, quantity, reorder_level, unit, status)
SELECT 'PRD-1002', 'Rice 5kg Bag', id, 'Packaged 5kg rice bag.', 5200.00, 6100.00, 8, 10, 'bag', 'active'
FROM categories WHERE category_name = 'Food Items'
ON DUPLICATE KEY UPDATE product_name = VALUES(product_name);

INSERT INTO products
    (product_code, product_name, category_id, description, cost_price, selling_price, quantity, reorder_level, unit, status)
SELECT 'PRD-1003', 'USB Charger', id, 'Portable wall charger.', 2500.00, 3500.00, 0, 5, 'pcs', 'active'
FROM categories WHERE category_name = 'Electronics'
ON DUPLICATE KEY UPDATE product_name = VALUES(product_name);

INSERT INTO products
    (product_code, product_name, category_id, description, cost_price, selling_price, quantity, reorder_level, unit, status)
SELECT 'PRD-1004', 'Exercise Book', id, '80 leaves notebook.', 250.00, 350.00, 75, 20, 'pcs', 'active'
FROM categories WHERE category_name = 'Stationery'
ON DUPLICATE KEY UPDATE product_name = VALUES(product_name);

INSERT INTO products
    (product_code, product_name, category_id, description, cost_price, selling_price, quantity, reorder_level, unit, status)
SELECT 'PRD-1005', 'Laundry Soap', id, 'Household washing soap.', 450.00, 650.00, 12, 6, 'pcs', 'active'
FROM categories WHERE category_name = 'Household Items'
ON DUPLICATE KEY UPDATE product_name = VALUES(product_name);

INSERT INTO stock_movements
    (product_id, movement_type, quantity, quantity_before, quantity_after, reference_type, reference_id, remarks, user_id)
SELECT p.id, 'opening_stock', p.quantity, 0, p.quantity, 'seed', p.id, 'Initial sample stock.', 1
FROM products p
WHERE p.product_code IN ('PRD-1001', 'PRD-1002', 'PRD-1003', 'PRD-1004', 'PRD-1005')
  AND p.quantity > 0
  AND NOT EXISTS (
      SELECT 1 FROM stock_movements sm
      WHERE sm.product_id = p.id AND sm.reference_type = 'seed'
  );
