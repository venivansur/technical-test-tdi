SELECT 
  p.id AS product_id,
  p.name AS product_name,
  p.stock + IFNULL(SUM(oi.quantity), 0) AS initial_stock,
  p.stock AS remaining_stock
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
GROUP BY p.id, p.name, p.stock;
