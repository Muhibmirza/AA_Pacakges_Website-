# AA Packages

Full PHP + MySQL ecommerce build for AA Packages packaging boxes.

AA Packages is a responsive packaging ecommerce website for custom cake, pizza,
gift, perfume, mithai, and printed boxes. It includes product browsing, cart and
checkout flows, quote requests, customer accounts, order tracking, an admin
dashboard, and configurable payment methods.

## Local Setup

1. Copy this folder to your XAMPP/WAMP/Laragon web root. Current expected URL is `http://localhost/aapackages/`.
2. Create/import the database from `database/aapackages.sql` in phpMyAdmin or MySQL CLI.
3. Edit `includes/db.php` if your MySQL username/password/database name differs.
4. Visit `http://localhost/aapackages/`.
5. Admin panel is hidden from the frontend at `http://localhost/aapackages/admin`.

## Test Accounts

- Admin: `admin@aapackages.local` / `admin123`
- Customer: `buyer@example.com` / `customer123`

## Payment Configuration

Payment gateway configuration lives in `includes/payment-config.php` and reads live secrets from environment variables.

To go live:

1. Open `includes/payment-config.php`.
2. Set `AA_PAYMENT_MODE=live`.
3. Set card gateway keys with `AA_VISA_PUBLIC_KEY`, `AA_VISA_SECRET_KEY`, `AA_MASTERCARD_PUBLIC_KEY`, `AA_MASTERCARD_SECRET_KEY`, `AA_CARD_PUBLIC_KEY`, and `AA_CARD_SECRET_KEY`.
4. For JazzCash, set `AA_JAZZCASH_MERCHANT_ID`, `AA_JAZZCASH_PASSWORD`, and `AA_JAZZCASH_SALT`.
5. For Easypaisa, set `AA_EASYPAISA_STORE_ID` and `AA_EASYPAISA_HASH_KEY`.
6. Do not place live keys in PHP, JS, CSS, SQL, or frontend templates.
7. Update bank transfer account title, bank name, and IBAN.
8. Test each gateway first with that provider's official sandbox credentials and a small test order before enabling live checkout.

The current implementation records sandbox transactions and keeps the code modular so real gateway API calls can be inserted in the payment section without changing checkout views.

## Contact Information

- Office: B 307 Sector 11-E, Karachi, Pakistan
- Postal Code: 75850
- Call us: +92 310 8504003
- Email: contact@aapackageskarachi.com

## Security Notes

- Clean URLs are enabled through `.htaccess`, e.g. `/shop`, `/category/cake-boxes`, `/product/window-cake-box-2lb`, `/admin/dashboard`.
- Rate limits are configurable in `includes/config.php` under `RATE_LIMITS`.
- Forms use CSRF tokens, prepared PDO statements, strict validation helpers, and generic user-facing errors.
- Full error details are logged server-side under `storage/logs/app.log`.
- Uploads validate MIME type and image content, use random filenames, and `uploads/.htaccess` blocks executable files.
- Database credentials can be configured with `AA_DB_HOST`, `AA_DB_NAME`, `AA_DB_USER`, and `AA_DB_PASS`.
- There are no Composer/npm dependencies in this project, so no dependency advisory audit is required until packages are added.

## Asset Notes

- The supplied logo source is stored in `public/assets/images/logo/aa-packages-logo-source.png`.
- A transparent working logo is stored in `public/assets/images/logo/aa-packages-logo.png`.
- Generated cohesive category/product photos are stored under `public/assets/images/categories/` and `public/assets/images/products/`.
- Admin-uploaded files are organized under `uploads/products/`, `uploads/categories/`, `uploads/banners/`, `uploads/logo/`, and `uploads/quotes/`.

## Structure

- `controllers/` MVC-style request handlers.
- `models/` PDO-backed domain models.
- `views/` frontend and admin templates.
- `includes/` database, config, security, helpers, payment config.
- `public/assets/css/` branded design system and admin CSS.
- `public/assets/js/` sliders, reveal animations, 3D banner parallax, AJAX cart drawer.
- `database/aapackages.sql` schema plus realistic sample data.
