SELECT 
  p.id AS product_id,
  p.name AS product_name,
  SUM(oi.quantity) AS total_quantity
FROM products p
JOIN order_items oi ON p.id = oi.product_id
GROUP BY p.id, p.name
ORDER BY total_quantity DESC
LIMIT 1;
