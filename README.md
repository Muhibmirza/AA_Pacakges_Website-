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

Change both passwords immediately after the client demo. The default admin is sample data, not a production credential.

## New Storefront Features

- The first-order popup stores subscribers in `discount_leads` and issues the working `WELCOME5` coupon.
- The offer opens automatically once per browser session; closing it reveals the circular 5% button for reopening.
- WhatsApp uses `AA_WHATSAPP_NUMBER` from the environment (digits only, including country code).
- Contact Us submissions are stored in `contact_messages`.
- Admin pages `/admin/leads` and `/admin/contact-messages` provide management, deletion, search, and lead CSV export.
- Admin page `/admin/pages` controls the public About and Contact page headings and introductory content; contact details remain under Settings.
- Admin sessions use a rolling 8-hour lifetime by default. Change it with `AA_SESSION_TIMEOUT`.

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

## Free Client Demo Deployment

Recommended demo stack: a Render free Docker web service plus an Aiven free MySQL service. This keeps the existing PHP/MySQL application intact; Vercel and Netlify cannot run this PHP backend directly.

### 1. Create the free MySQL database

1. Create an Aiven account and a free MySQL service.
2. In Aiven, copy the host, port, database name, username, and password from the service connection page.
3. Keep SSL required. The included Render configuration uses `/etc/ssl/certs/ca-certificates.crt` as `AA_DB_SSL_CA`.
4. You do not need to import the SQL manually for a fresh database. The container entrypoint imports `database/aapackages.sql` when the `settings` table does not exist.

### 2. Deploy the PHP app on Render

1. Push this project to a private GitHub repository. Do not commit `.env` or any live payment secrets.
2. In Render choose **New > Blueprint** and select the repository. Render reads `render.yaml` and builds the included `Dockerfile`.
3. Enter these secret environment values when prompted: `AA_DB_HOST`, `AA_DB_PORT`, `AA_DB_NAME`, `AA_DB_USER`, and `AA_DB_PASS`.
4. Set `AA_ALLOWED_ORIGINS` to the final Render URL, for example `https://aa-packages-demo.onrender.com`.
5. Set `AA_WHATSAPP_NUMBER` to the final WhatsApp number in international digits-only form.
6. Deploy. Open `/health.php`; it should return `{"ok":true,"service":"aa-packages"}`.
7. Open `/admin` and sign in with `admin@aapackages.local` / `admin123`, then change the seeded credential before any real use.

Render free web services sleep after inactivity and their local filesystem is ephemeral. The stock assets are part of the deployed image, but images uploaded later through admin can disappear after a restart or redeploy. For a client demo this is acceptable; for production, set `AA_UPLOAD_PATH`/`AA_UPLOAD_URL` to persistent storage and use a paid service or object-storage adapter.

Aiven's free MySQL tier is intended for demos and small workloads. It currently provides 1 GB storage on a single node and may pause after extended inactivity. Upgrade both services before accepting production traffic or payments.

### 3. Existing database upgrade

For an existing local or hosted database, import `database/migrations/2026_10_13_marketing_catalog.sql` once. It is idempotent and adds the lead/contact tables, requested categories/products, images, variants, and `WELCOME5` coupon without deleting existing records.

### 4. Environment and CORS

Copy `.env.example` values into your hosting dashboard; PHP does not load `.env` automatically. `AA_API_BASE_URL` defaults to the same origin as the website. CORS is denied by default and only origins listed in comma-separated `AA_ALLOWED_ORIGINS` receive cross-origin credentials headers.

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
