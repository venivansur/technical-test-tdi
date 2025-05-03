SELECT 
  o.id AS order_id,
  o.customer_name,
  o.order_date,
  SUM(oi.quantity * oi.price) AS total_price
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id, o.customer_name, o.order_date
ORDER BY total_price DESC
LIMIT 1;
