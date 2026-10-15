START TRANSACTION;

DELETE FROM orders
WHERE order_no IN ('AAP-260717-1001', 'AAP-260717-1002', 'AAP-260717-1003');

DELETE FROM users
WHERE email IN ('buyer@example.com', 'sana@example.com');

DELETE FROM quote_requests
WHERE email IN ('farhan@nawabsweets.example', 'hira@urbanscents.example', 'omar@crustcorner.example');

DELETE FROM testimonials
WHERE (name = 'Bilal Ahmed' AND company_name = 'Ahmed Bakers')
   OR (name = 'Maham Raza' AND company_name = 'Mithai House')
   OR (name = 'Danish Khan' AND company_name = 'Slice Point Pizza');

DELETE FROM coupons WHERE code IN ('TRADE5', 'BULK1000');
UPDATE coupons SET expires_at = '2030-12-31', is_active = 1 WHERE code = 'WELCOME5';

UPDATE admins
SET name = 'AA Packages Admin',
    email = 'admin@aapackages.com',
    password_hash = '$2y$10$QZzNBa5Oh0GIxls5Jj45d.5rmkM.0g4Dh7iCr9O/Pau5kb8USXQoq',
    role = 'super_admin',
    is_active = 1
WHERE id = 1 OR email = 'admin@aapackages.local';

COMMIT;

DROP TABLE IF EXISTS payments;
