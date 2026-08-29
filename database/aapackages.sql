CREATE DATABASE IF NOT EXISTS aapackages CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aapackages;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS payments, order_items, orders, reviews, product_images, product_variants, quantity_price_tiers, coupons, products, category_banners, categories, quote_requests, testimonials, usp_blocks, banners, pages, admins, users, settings;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(40) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  address TEXT,
  status ENUM('active','blocked') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(60) NOT NULL DEFAULT 'super_admin',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL UNIQUE,
  description VARCHAR(255) NOT NULL,
  image VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE category_banners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL UNIQUE,
  banner_image VARCHAR(255) NOT NULL,
  tagline VARCHAR(255) NOT NULL,
  cta_text VARCHAR(80) NOT NULL DEFAULT 'Shop This Category',
  cta_link VARCHAR(255) NOT NULL DEFAULT '#products',
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(210) NOT NULL UNIQUE,
  short_description VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  default_variant VARCHAR(120) NOT NULL,
  base_price DECIMAL(12,2) NOT NULL,
  stock_quantity INT NOT NULL DEFAULT 0,
  main_image VARCHAR(255) NOT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  INDEX idx_products_category (category_id),
  INDEX idx_products_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_variants (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  size_label VARCHAR(120) NOT NULL,
  material VARCHAR(120) NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  stock_quantity INT NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE quantity_price_tiers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  min_quantity INT NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE coupons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(60) NOT NULL UNIQUE,
  discount_type ENUM('percentage','fixed') NOT NULL,
  discount_value DECIMAL(12,2) NOT NULL,
  expires_at DATE,
  usage_limit INT NOT NULL DEFAULT 0,
  used_count INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_no VARCHAR(40) NOT NULL UNIQUE,
  user_id INT UNSIGNED NULL,
  customer_name VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) NOT NULL,
  business_name VARCHAR(190),
  address TEXT NOT NULL,
  city VARCHAR(100) NOT NULL,
  area VARCHAR(100) NOT NULL,
  notes TEXT,
  subtotal DECIMAL(12,2) NOT NULL,
  delivery_fee DECIMAL(12,2) NOT NULL DEFAULT 0,
  discount DECIMAL(12,2) NOT NULL DEFAULT 0,
  total DECIMAL(12,2) NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'pending',
  payment_method VARCHAR(60) NOT NULL,
  payment_status VARCHAR(40) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_orders_status (status),
  INDEX idx_orders_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  product_name VARCHAR(190) NOT NULL,
  variant_label VARCHAR(160),
  unit_price DECIMAL(12,2) NOT NULL,
  quantity INT NOT NULL,
  subtotal DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE payments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  transaction_ref VARCHAR(120),
  method VARCHAR(60) NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  status VARCHAR(40) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  rating TINYINT NOT NULL,
  comment TEXT,
  is_approved TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE quote_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  business_name VARCHAR(190),
  contact_name VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(190) NOT NULL,
  box_category VARCHAR(160) NOT NULL,
  box_size VARCHAR(120),
  quantity INT NOT NULL,
  custom_printing TINYINT(1) NOT NULL DEFAULT 0,
  logo_upload_path VARCHAR(255),
  message TEXT,
  internal_notes TEXT,
  status VARCHAR(40) NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_quote_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE banners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  position VARCHAR(80) NOT NULL DEFAULT 'home_hero',
  image_path VARCHAR(255) NOT NULL,
  kicker VARCHAR(100),
  heading VARCHAR(190) NOT NULL,
  subtext VARCHAR(255) NOT NULL,
  button_text VARCHAR(80) NOT NULL,
  button_link VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE testimonials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  company_name VARCHAR(160),
  photo_path VARCHAR(255),
  rating TINYINT NOT NULL DEFAULT 5,
  text TEXT NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usp_blocks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  icon_label VARCHAR(12) NOT NULL,
  title VARCHAR(120) NOT NULL,
  description VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  title VARCHAR(190) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO admins (name, email, password_hash, role) VALUES
('AA Packages Admin', 'admin@aapackages.local', '$2y$10$oNsez47g196dVBl.sk18sOVIXDvWAeyShyOWpdKlNfg0pmHMs9gRy', 'super_admin');

INSERT INTO users (name, email, phone, password_hash, address) VALUES
('Ahmed Bakery', 'buyer@example.com', '03000000001', '$2y$10$2ongVDqmnQVVMN..3BF6f.eereJ.L/adxYZ4lIVcPK1OsqR406bn6', 'Main Boulevard, Lahore'),
('Sana Gift Studio', 'sana@example.com', '03000000002', '$2y$10$2ongVDqmnQVVMN..3BF6f.eereJ.L/adxYZ4lIVcPK1OsqR406bn6', 'DHA Phase 5, Lahore');

INSERT INTO categories (name, slug, description, image, sort_order) VALUES
('Sweet/Mithai Boxes','sweet-mithai-boxes','Elegant food-safe boxes for mithai, sweets, and bakery assortments.','public/assets/images/categories/sweet-mithai-boxes.png',1),
('Cake Boxes','cake-boxes','Window and carry boxes for cakes, pastries, and desserts.','public/assets/images/categories/cake-boxes.png',2),
('Pizza Boxes','pizza-boxes','Corrugated pizza boxes for restaurants, cafes, and takeaways.','public/assets/images/categories/pizza-boxes.png',3),
('Perfume Boxes','perfume-boxes','Premium rigid and sleeve packaging for fragrance brands.','public/assets/images/categories/perfume-boxes.png',4),
('Gift Boxes','gift-boxes','Rigid gift packaging with refined finishes and clean presentation.','public/assets/images/categories/gift-boxes.png',5),
('Custom Printed Boxes','custom-printed-boxes','Print-ready packaging for brands, launches, and wholesale buyers.','public/assets/images/categories/custom-printed-boxes.png',6);

INSERT INTO category_banners (category_id, banner_image, tagline, cta_text, cta_link) VALUES
(1,'public/assets/images/categories/sweet-mithai-boxes.png','Decorative, food-safe packaging for mithai counters, wedding sweets, and premium gift assortments.','Shop Mithai Boxes','#products'),
(2,'public/assets/images/categories/cake-boxes.png','Strong cake boxes with clean window options and reliable board quality for bakeries.','Shop Cake Boxes','#products'),
(3,'public/assets/images/categories/pizza-boxes.png','Corrugated boxes sized for hot delivery, takeaway counters, and high-volume restaurant orders.','Shop Pizza Boxes','#products'),
(4,'public/assets/images/categories/perfume-boxes.png','Rigid and sleeve-style packaging for perfume, cosmetics, and premium retail products.','Shop Perfume Boxes','#products'),
(5,'public/assets/images/categories/gift-boxes.png','Retail-ready gift boxes with refined structure, neat finishes, and premium feel.','Shop Gift Boxes','#products'),
(6,'public/assets/images/categories/custom-printed-boxes.png','Bring your brand to the box with custom sizing, artwork, and wholesale print runs.','Request Custom Quote','/aapackages/quote');

INSERT INTO products (category_id, name, slug, short_description, description, default_variant, base_price, stock_quantity, main_image, is_featured) VALUES
(1,'Royal Mithai Box 1kg','royal-mithai-box-1kg','Food-safe mithai box with rigid lid and divider insert.','Designed for sweet shops and wedding trays. Includes inner dividers, durable food-safe board, and premium finish suitable for counter display and takeaway.','1kg / Food-grade board / Pack of 25',1450,420,'public/assets/images/products/sweet-mithai-boxes.png',1),
(1,'Blue Pattern Mithai Tray','blue-pattern-mithai-tray','Decorative tray box for assorted sweets and dry fruit.','A refined tray box for assortments, gifting, and seasonal sweet packaging.','750g / Printed board / Pack of 25',1280,360,'public/assets/images/products/sweet-mithai-boxes.png',1),
(1,'Premium Drawer Sweet Box','premium-drawer-sweet-box','Drawer-style rigid sweet box with pull tab.','Rigid drawer construction for premium mithai gifting and corporate sweet packs.','1.5kg / Rigid board / Pack of 20',2400,110,'public/assets/images/products/sweet-mithai-boxes.png',0),
(2,'Window Cake Box 2lb','window-cake-box-2lb','White cake box with clear display window and blue trim.','Strong window cake box for bakeries needing clean presentation and easy product visibility.','2lb / Window board / Pack of 25',1320,300,'public/assets/images/products/cake-boxes.png',1),
(2,'Tall Cake Carrier 4lb','tall-cake-carrier-4lb','Tall carry box for layered cakes and custom cakes.','Tall profile with secure folding flaps for cakes that need additional height.','4lb / Food-grade board / Pack of 20',1780,220,'public/assets/images/products/cake-boxes.png',1),
(2,'Pastry Window Box','pastry-window-box','Low-profile window box for pastries and dessert slices.','Ideal for bakeries, cafes, and dessert counters selling assorted pastry packs.','12 x 8 x 3 inch / Pack of 25',1180,280,'public/assets/images/products/cake-boxes.png',0),
(3,'Classic Pizza Box 12 inch','classic-pizza-box-12-inch','Corrugated pizza box for hot delivery and takeaway.','Single-wall corrugated pizza box with easy folding lock and ventilation-friendly profile.','12 inch / Corrugated / Pack of 50',1850,620,'public/assets/images/products/pizza-boxes.png',1),
(3,'Family Pizza Box 16 inch','family-pizza-box-16-inch','Large pizza box for restaurants and party orders.','Durable corrugated construction for larger pizzas and high-volume delivery use.','16 inch / Corrugated / Pack of 50',2650,300,'public/assets/images/products/pizza-boxes.png',1),
(3,'Slice Box Pack','slice-box-pack','Compact triangular slice box for counters and events.','Practical single-slice packaging for cafes, food stalls, and events.','Slice size / Corrugated / Pack of 100',1550,500,'public/assets/images/products/pizza-boxes.png',0),
(4,'Rigid Perfume Box 50ml','rigid-perfume-box-50ml','Premium rigid carton for fragrance bottles.','A structured box for perfume, attar, and cosmetics brands needing a refined retail presence.','50ml / Rigid board / Pack of 25',2200,160,'public/assets/images/products/perfume-boxes.png',1),
(4,'Sleeve Perfume Box','sleeve-perfume-box','Drawer-style sleeve box with smooth opening feel.','Premium sleeve-style box with room for foam insert and branded print.','100ml / Sleeve rigid / Pack of 20',2750,120,'public/assets/images/products/perfume-boxes.png',0),
(4,'Cosmetic Folding Carton','cosmetic-folding-carton','Lightweight folding carton for perfume and cosmetics.','Efficient folding carton for retail shelves and promotional packaging.','30ml / SBS board / Pack of 100',1800,350,'public/assets/images/products/perfume-boxes.png',0),
(5,'Magnetic Gift Box Medium','magnetic-gift-box-medium','Rigid magnetic gift box for premium retail packing.','Clean gift box for apparel, accessories, corporate gifting, and seasonal hampers.','Medium / Rigid magnetic / Pack of 10',2950,90,'public/assets/images/products/gift-boxes.png',1),
(5,'Drawer Gift Box Small','drawer-gift-box-small','Compact drawer box with pull tab and premium finish.','Small rigid drawer box for gifts, jewelry, and specialty retail products.','Small / Rigid drawer / Pack of 20',2100,140,'public/assets/images/products/gift-boxes.png',0),
(5,'Luxury Gift Hamper Box','luxury-gift-hamper-box','Large rigid box for hampers and celebration gifts.','Strong hamper packaging for corporate gifts, Eid boxes, and curated bundles.','Large / Rigid board / Pack of 10',3600,75,'public/assets/images/products/gift-boxes.png',1),
(6,'Custom Printed Mailer Box','custom-printed-mailer-box','Print-ready mailer box for ecommerce and product launches.','Customizable mailer box for brand campaigns, ecommerce shipping, and subscription packs.','9 x 6 x 3 inch / Corrugated / Pack of 50',2400,260,'public/assets/images/products/custom-printed-boxes.png',1),
(6,'Custom Sleeve Box','custom-sleeve-box','Sleeve packaging with custom artwork and clean finish.','Ideal for product kits, cosmetics, gifts, and premium retail presentations.','Custom size / Sleeve board / Pack of 25',2650,170,'public/assets/images/products/custom-printed-boxes.png',0),
(6,'Printed Food Box','printed-food-box','Custom printed food box for restaurants and bakeries.','Food-grade board with print-ready exterior for business branding.','8 x 8 x 3 inch / Pack of 50',2050,320,'public/assets/images/products/custom-printed-boxes.png',1);

INSERT INTO product_images (product_id, image_path, sort_order)
SELECT id, main_image, 1 FROM products;

INSERT INTO product_variants (product_id, size_label, material, price, stock_quantity)
SELECT id, 'Standard', default_variant, base_price, stock_quantity FROM products;

INSERT INTO quantity_price_tiers (product_id, min_quantity, unit_price)
SELECT id, 50, ROUND(base_price * .96, 2) FROM products;
INSERT INTO quantity_price_tiers (product_id, min_quantity, unit_price)
SELECT id, 100, ROUND(base_price * .91, 2) FROM products;
INSERT INTO quantity_price_tiers (product_id, min_quantity, unit_price)
SELECT id, 500, ROUND(base_price * .82, 2) FROM products;

INSERT INTO banners (position, image_path, kicker, heading, subtext, button_text, button_link, sort_order) VALUES
('home_hero','public/assets/images/categories/custom-printed-boxes.png','Custom Printed Packaging','Boxes that make your brand look ready for retail','Custom printed mailer, gift, food, and product boxes for growing businesses and wholesale buyers.','Get a Quote','/aapackages/quote',1),
('home_hero','public/assets/images/categories/cake-boxes.png','Bakery Packaging','Cake and dessert boxes built for daily bakery counters','Window cake boxes, pastry boxes, and sweet packaging with practical wholesale pricing.','Shop Bakery Boxes','/aapackages/category/cake-boxes',2),
('home_hero','public/assets/images/categories/pizza-boxes.png','Restaurant Supply','Pizza boxes for takeaway, delivery, and bulk restaurant supply','Corrugated boxes with reliable structure and fast repeat ordering for food businesses.','Shop Pizza Boxes','/aapackages/category/pizza-boxes',3);

INSERT INTO usp_blocks (icon_label, title, description, sort_order) VALUES
('Q','Premium Quality Board','Food-safe, rigid, and corrugated options selected for daily retail use.',1),
('P','Custom Printing','Brand-ready artwork, logo placement, and repeatable print runs.',2),
('W','Wholesale Pricing','Practical quantity tiers for bakeries, restaurants, and retailers.',3),
('F','Fast Turnaround','Responsive quoting and reliable supply for urgent packaging needs.',4);

INSERT INTO testimonials (name, company_name, rating, text, sort_order) VALUES
('Bilal Ahmed','Ahmed Bakers',5,'The cake boxes are strong, clean, and consistent. Reordering for branches is simple.',1),
('Maham Raza','Mithai House',5,'Their mithai boxes look premium and hold up well during busy wedding season.',2),
('Danish Khan','Slice Point Pizza',5,'Good pizza box quality and reliable bulk pricing for our delivery operation.',3);

INSERT INTO coupons (code, discount_type, discount_value, expires_at, usage_limit) VALUES
('TRADE5','percentage',5,'2026-12-31',200),
('BULK1000','fixed',1000,'2026-12-31',50);

INSERT INTO orders (order_no, user_id, customer_name, phone, email, business_name, address, city, area, subtotal, delivery_fee, discount, total, status, payment_method, payment_status) VALUES
('AAP-260717-1001',1,'Ahmed Bakery','03000000001','buyer@example.com','Ahmed Bakers','Main Boulevard','Lahore','Gulberg',6350,350,0,6700,'processing','cash_on_delivery','pending'),
('AAP-260717-1002',2,'Sana Gift Studio','03000000002','sana@example.com','Sana Gift Studio','DHA Phase 5','Lahore','DHA',6550,350,0,6900,'out-for-delivery','bank_transfer','paid'),
('AAP-260717-1003',NULL,'Walk-in Buyer','03000000003','guest@example.com','','Model Town','Lahore','Model Town',2950,350,0,3300,'delivered','jazzcash','paid');

INSERT INTO order_items (order_id, product_id, product_name, variant_label, unit_price, quantity, subtotal) VALUES
(1,4,'Window Cake Box 2lb','2lb / Window board / Pack of 25',1320,2,2640),
(1,7,'Classic Pizza Box 12 inch','12 inch / Corrugated / Pack of 50',1850,2,3700),
(2,13,'Magnetic Gift Box Medium','Medium / Rigid magnetic / Pack of 10',2950,1,2950),
(2,15,'Luxury Gift Hamper Box','Large / Rigid board / Pack of 10',3600,1,3600),
(3,13,'Magnetic Gift Box Medium','Medium / Rigid magnetic / Pack of 10',2950,1,2950);

INSERT INTO payments (order_id, transaction_ref, method, amount, status) VALUES
(1,'SANDBOX-AAP-260717-1001','cash_on_delivery',6700,'pending'),
(2,'BT-AAP-260717-1002','bank_transfer',6900,'paid'),
(3,'JC-AAP-260717-1003','jazzcash',3300,'paid');

INSERT INTO quote_requests (business_name, contact_name, phone, email, box_category, box_size, quantity, custom_printing, message, status) VALUES
('Nawab Sweets','Farhan Ali','03001112223','farhan@nawabsweets.example','Sweet/Mithai Boxes','1kg',2000,1,'Need Eid gift box printing with logo and blue pattern.','new'),
('Urban Scents','Hira Malik','03004445556','hira@urbanscents.example','Perfume Boxes','50ml',1000,1,'Rigid box with insert and matte lamination.','contacted'),
('Crust Corner','Omar Saeed','03007778889','omar@crustcorner.example','Pizza Boxes','12 inch',5000,0,'Plain white pizza boxes for monthly supply.','quoted');

INSERT INTO pages (slug, title, content) VALUES
('about','About AA Packages','<p>AA Packages supplies practical, premium packaging boxes for bakeries, sweet shops, restaurants, perfume brands, gift shops, ecommerce sellers, and wholesale buyers.</p><p>We focus on reliable board quality, clean presentation, and repeatable supply for businesses that need packaging every day.</p>'),
('contact','Contact Us','<p>Office. B 307 Sector 11-E, Karachi, Pakistan<br>Postal Code: 75850<br>Call us. +92 310 8504003<br>Email: contact@aapackageskarachi.com<br>Hours: Monday to Saturday, 10:00 AM to 7:00 PM</p><p>Send your packaging requirements through the quote form for business and wholesale pricing.</p>'),
('faqs','FAQs','<h2>Do you handle bulk orders?</h2><p>Yes. Share quantity, size, material, and printing requirements through the quote form.</p><h2>Can boxes be custom printed?</h2><p>Yes. Logo printing, artwork, sizing, and material options can be quoted.</p>'),
('terms','Terms & Conditions','<p>Orders are processed according to confirmed product specifications, quantities, payment method, and delivery details. Custom printed orders require artwork approval before production.</p>'),
('privacy','Privacy Policy','<p>Customer information is used only for account, order, quote, delivery, and support purposes. We do not sell customer data.</p>'),
('returns','Return & Refund Policy','<p>Packaging items with confirmed manufacturing damage or delivery damage should be reported within 24 hours with clear photos. Custom printed boxes are not returnable after artwork approval unless a production fault is confirmed.</p>');

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name','AA Packages'),
('site_logo','public/assets/images/logo/aa-packages-logo.png'),
('theme_primary','#1565C0'),
('theme_primary_light','#4FC3F7'),
('theme_secondary','#0D47A1'),
('contact_phone','+92 310 8504003'),
('contact_email','contact@aapackageskarachi.com'),
('postal_code','75850'),
('contact_address','B 307 Sector 11-E, Karachi, Pakistan'),
('business_hours','Mon-Sat, 10:00 AM - 7:00 PM'),
('footer_about','AA Packages supplies reliable, print-ready packaging for bakeries, sweet shops, restaurants, perfume brands, retailers, and wholesale buyers.'),
('map_embed','');
