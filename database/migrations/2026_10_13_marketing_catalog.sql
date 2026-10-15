CREATE TABLE IF NOT EXISTS discount_leads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160),
  email VARCHAR(190) NOT NULL UNIQUE,
  phone VARCHAR(40) NOT NULL,
  source VARCHAR(80) NOT NULL DEFAULT 'first_order_popup',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_discount_leads_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new','read','replied','closed') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_contact_messages_status (status),
  INDEX idx_contact_messages_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO categories (name, slug, description, image, sort_order, is_active) VALUES
('Tokra','tokra','Round and square woven tokra options for sweets, gifts, and hampers.','public/assets/images/stock/tokra.jpg',7,1),
('Attendance Cards','attendance-cards','Neat printed attendance cards supplied in practical business packs.','public/assets/images/stock/attendance-cards.jpg',8,1),
('Cake Tags','cake-tags','Decorative cake tags in different designs for bakeries and events.','public/assets/images/stock/attendance-cards.jpg',9,1),
('Paper Bags','paper-bags','Strong paper carry bags sized for bakery and retail orders.','public/assets/images/stock/paper-bags.jpg',10,1),
('Cake Trays','cake-trays','Food-safe cake trays and boards for display and delivery.','public/assets/images/stock/cake-trays.jpg',11,1),
('Donut Box','donut-box','Window and folding donut boxes for bakery counters and delivery.','public/assets/images/stock/donut-box.jpg',12,1),
('Cupcake Boxes','cupcake-boxes','Secure cupcake boxes for neat presentation and transport.','public/assets/images/stock/cupcake-boxes.jpg',13,1);

INSERT IGNORE INTO category_banners (category_id, banner_image, tagline, cta_text, cta_link)
SELECT id, image, CONCAT(description, ' Available for retail and wholesale orders.'), CONCAT('Shop ', name), '#products'
FROM categories WHERE slug IN ('tokra','attendance-cards','cake-tags','paper-bags','cake-trays','donut-box','cupcake-boxes');

INSERT IGNORE INTO products (category_id, name, slug, short_description, description, default_variant, base_price, stock_quantity, main_image, is_featured, is_active) VALUES
((SELECT id FROM categories WHERE slug='cake-boxes'),'Cake Box 1 Pond 7 x 7 x 2.5','cake-box-1-pond-7x7x2-5','Compact cake box for standard one-pound cakes.','Food-safe folding cake box sized for one-pound bakery cakes and reliable takeaway.','1 Pond / 7 x 7 x 2.5 inch / Pack of 25',950,300,'public/assets/images/products/cake-boxes.png',1,1),
((SELECT id FROM categories WHERE slug='cake-boxes'),'Cake Box 1 Pond 7 x 7 x 4','cake-box-1-pond-7x7x4','Taller one-pound cake box for decorated cakes.','Extra-height food-safe cake box for one-pound cakes with toppings and decoration.','1 Pond / 7 x 7 x 4 inch / Pack of 25',1100,260,'public/assets/images/products/cake-boxes.png',0,1),
((SELECT id FROM categories WHERE slug='cake-boxes'),'Cake Box 2 Pond','cake-box-2-pond','Strong folding box for two-pound cakes.','Reliable bakery box with comfortable clearance for standard two-pound cakes.','2 Pond / Food-grade board / Pack of 25',1350,280,'public/assets/images/products/cake-boxes.png',1,1),
((SELECT id FROM categories WHERE slug='cake-boxes'),'Cake Box 3 Pond','cake-box-3-pond','Large food-safe box for three-pound cakes.','Structured large cake box for celebration cakes, bakery display, and delivery.','3 Pond / Food-grade board / Pack of 20',1650,220,'public/assets/images/products/cake-boxes.png',0,1),
((SELECT id FROM categories WHERE slug='cake-boxes'),'2 Pastry Box','2-pastry-box','Compact folding box for two pastries.','Small pastry box for clean counter presentation and takeaway orders.','2 Pastry / Pack of 50',720,420,'public/assets/images/stock/cupcake-boxes.jpg',0,1),
((SELECT id FROM categories WHERE slug='cake-boxes'),'4 Pastry Box','4-pastry-box','Bakery box sized for four pastries.','Practical folding pastry box for assorted slices, desserts, and bakery counters.','4 Pastry / Pack of 50',880,380,'public/assets/images/stock/cupcake-boxes.jpg',0,1),
((SELECT id FROM categories WHERE slug='cake-boxes'),'6 Pastry Box','6-pastry-box','Roomy pastry box for six-piece assortments.','Food-safe box for six pastries, dessert assortments, and family takeaway orders.','6 Pastry / Pack of 50',1050,340,'public/assets/images/stock/cupcake-boxes.jpg',0,1),
((SELECT id FROM categories WHERE slug='sweet-mithai-boxes'),'Mithai Box 125 gm','mithai-box-125-gm','Small mithai box for individual servings and favors.','Compact food-safe sweet box for 125 gram portions, events, and counter sales.','125 gm / Pack of 50',650,500,'public/assets/images/products/sweet-mithai-boxes.png',0,1),
((SELECT id FROM categories WHERE slug='sweet-mithai-boxes'),'Mithai Box 250 gm','mithai-box-250-gm','Neat folding box for 250 gram sweet portions.','Reliable 250 gram mithai packaging for sweet shops, gifting, and events.','250 gm / Pack of 50',780,480,'public/assets/images/products/sweet-mithai-boxes.png',1,1),
((SELECT id FROM categories WHERE slug='sweet-mithai-boxes'),'Mithai Box 0.5 kg Top Bottom','mithai-box-half-kg-top-bottom','Two-piece top and bottom mithai box.','Premium half-kilogram sweet box with separate lid and base for polished presentation.','0.5 kg / Top Bottom / Pack of 25',980,360,'public/assets/images/products/sweet-mithai-boxes.png',0,1),
((SELECT id FROM categories WHERE slug='sweet-mithai-boxes'),'Mithai Box 0.5 kg Folding','mithai-box-half-kg-folding','Efficient folding mithai box for half-kilogram packs.','Food-safe folding construction for everyday half-kilogram mithai orders.','0.5 kg / Folding / Pack of 25',850,400,'public/assets/images/products/sweet-mithai-boxes.png',0,1),
((SELECT id FROM categories WHERE slug='sweet-mithai-boxes'),'Mithai Box 1 kg Top Bottom','mithai-box-1-kg-top-bottom','Premium two-piece box for one kilogram of sweets.','Structured lid-and-base mithai box for gifting and high-quality retail presentation.','1 kg / Top Bottom / Pack of 25',1350,320,'public/assets/images/products/sweet-mithai-boxes.png',1,1),
((SELECT id FROM categories WHERE slug='sweet-mithai-boxes'),'Mithai Box 1 kg Folding','mithai-box-1-kg-folding','Practical folding sweet box for one-kilogram orders.','Durable one-kilogram folding box for busy sweet counters and wholesale supply.','1 kg / Folding / Pack of 25',1180,360,'public/assets/images/products/sweet-mithai-boxes.png',0,1),
((SELECT id FROM categories WHERE slug='pizza-boxes'),'Small Pizza Box','small-pizza-box','Compact corrugated pizza box for personal sizes.','Ventilated food-safe corrugated box for small pizzas and takeaway service.','Small / Corrugated / Pack of 50',1400,600,'public/assets/images/stock/pizza-boxes.jpg',0,1),
((SELECT id FROM categories WHERE slug='pizza-boxes'),'Medium Pizza Box','medium-pizza-box','Restaurant-grade pizza box for regular sizes.','Strong corrugated delivery box for medium pizzas and everyday restaurant use.','Medium / Corrugated / Pack of 50',1850,560,'public/assets/images/stock/pizza-boxes.jpg',1,1),
((SELECT id FROM categories WHERE slug='pizza-boxes'),'Large Pizza Box','large-pizza-box','Large corrugated box for family pizzas.','Structured large pizza box for family orders, delivery, and takeaway.','Large / Corrugated / Pack of 50',2450,420,'public/assets/images/stock/pizza-boxes.jpg',1,1),
((SELECT id FROM categories WHERE slug='tokra'),'Tokra 3 KG Round & Square','tokra-3-kg-round-square','Three-kilogram tokra available in round and square shapes.','Woven tokra packaging for mithai, fruit, gifts, and celebration hampers.','3 KG / Round or Square',1200,180,'public/assets/images/stock/tokra.jpg',1,1),
((SELECT id FROM categories WHERE slug='tokra'),'Tokra 5 KG Round & Square','tokra-5-kg-round-square','Five-kilogram tokra available in round and square shapes.','Medium woven tokra for larger sweet assortments, gifts, and event hampers.','5 KG / Round or Square',1600,140,'public/assets/images/stock/tokra.jpg',0,1),
((SELECT id FROM categories WHERE slug='tokra'),'Tokra 10 KG Round & Square','tokra-10-kg-round-square','Large ten-kilogram tokra in round and square shapes.','Large-capacity woven tokra for corporate gifts, weddings, and bulk presentation.','10 KG / Round or Square',2400,90,'public/assets/images/stock/tokra.jpg',0,1),
((SELECT id FROM categories WHERE slug='attendance-cards'),'Attendance Cards 100 Pcs Packet','attendance-cards-100-pcs','Printed attendance cards supplied in 100-piece packets.','Clear, practical attendance cards for schools, offices, workshops, and staff records.','100 Pcs Packet',850,250,'public/assets/images/stock/attendance-cards.jpg',0,1),
((SELECT id FROM categories WHERE slug='cake-tags'),'Cake Tags Assorted Designs','cake-tags-assorted-designs','Decorative cake tags available in different designs.','Ready-to-use cake tags for birthdays, celebrations, bakery counters, and custom cakes.','Assorted Designs / Pack of 100',700,300,'public/assets/images/stock/attendance-cards.jpg',0,1),
((SELECT id FROM categories WHERE slug='paper-bags'),'Paper Bag 1 Pond','paper-bag-1-pond','Compact paper carry bag for one-pound bakery boxes.','Strong handled paper bag for one-pound cakes, sweets, gifts, and counter orders.','1 Pond / Pack of 50',950,420,'public/assets/images/stock/paper-bags.jpg',1,1),
((SELECT id FROM categories WHERE slug='paper-bags'),'Paper Bag 2 Pond','paper-bag-2-pond','Roomy paper carry bag for two-pound bakery boxes.','Durable handled paper bag for larger bakery boxes and retail purchases.','2 Pond / Pack of 50',1200,360,'public/assets/images/stock/paper-bags.jpg',0,1),
((SELECT id FROM categories WHERE slug='cake-trays'),'Food-Safe Cake Tray','food-safe-cake-tray','Firm cake tray for display, handling, and delivery.','Food-safe cake board and tray with a clean finish for bakeries and home bakers.','Standard Sizes / Pack of 25',900,300,'public/assets/images/stock/cake-trays.jpg',0,1),
((SELECT id FROM categories WHERE slug='donut-box'),'Window Donut Box','window-donut-box','Donut box with clear viewing window.','Presentation-ready folding box for assorted donuts and bakery takeaway.','6 Donuts / Pack of 25',1150,260,'public/assets/images/stock/donut-box.jpg',1,1),
((SELECT id FROM categories WHERE slug='cupcake-boxes'),'Cupcake Carry Box','cupcake-carry-box','Secure cupcake box with fitted insert.','Food-safe cupcake carrier that keeps decorated cupcakes separated during delivery.','6 Cupcakes / Pack of 25',1250,240,'public/assets/images/stock/cupcake-boxes.jpg',1,1);

INSERT INTO product_images (product_id, image_path, sort_order)
SELECT p.id, p.main_image, 1 FROM products p LEFT JOIN product_images pi ON pi.product_id = p.id
WHERE pi.id IS NULL AND p.id > 0;

INSERT INTO product_variants (product_id, size_label, material, price, stock_quantity)
SELECT p.id, p.default_variant, 'Food-safe packaging board', p.base_price, p.stock_quantity FROM products p
LEFT JOIN product_variants pv ON pv.product_id = p.id WHERE pv.id IS NULL;

INSERT IGNORE INTO coupons (code, discount_type, discount_value, expires_at, usage_limit, is_active)
VALUES ('WELCOME5','percentage',5,'2027-12-31',0,1);

INSERT INTO pages (slug, title, content) VALUES
('about','Packaging that protects your product and presents your brand well.','AA Packages began with a simple focus: help bakeries, sweet shops, restaurants, gift sellers, and growing brands find packaging that is sturdy, presentable, and sensibly priced.\n\nToday we support retail orders, wholesale quantities, custom sizes, and branded print runs. Our team works closely with customers so the box fits the product, workflow, and budget.'),
('contact','Tell us what you need. We will help you find the right box.','Share your product, preferred box size, and required quantity. Our team will respond with suitable packaging options, wholesale pricing, and custom printing guidance.')
ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content);
