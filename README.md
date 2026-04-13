This is a well-scoped project. Based on the DailyCard.net screenshots you shared, I can map exactly how each product type works and design the system properly. Let me give you a complete engineering plan.

---

## MeaCash — Complete Laravel Engineering Blueprint

### What the Screenshots Tell Us

From your images, there are three distinct product interaction patterns:

**Fixed-package products** (Free Fire, IMO): User selects a predefined denomination (e.g. 110 jewels = $0.96), enters an account ID, and buys. The form changes based on the product type — some need only an account ID, others need email + password (for TikTok Coins, meaning the system logs in for them).

**Custom-quantity products** (BIGO HK, TikTok): User types any quantity or price, the system calculates the cost dynamically. This requires a price-per-unit stored on the product.

**Unavailable products** (USDT, PSN Lebanon): Products can be toggled on/off, and Lebanon-specific products like PSN LEB are marked unavailable — meaning regional availability must be tracked.

This tells us the data model needs to be far more flexible than a simple product table.

------

## Phase 1 — Database Schema Design

This is the most critical foundation. Every decision here affects everything else.

### Core Tables

**`categories`** — top-level groupings (Gaming, Gift Cards, Social Media, etc.)
```
id, name_ar, name_en, slug, icon, image, is_active, sort_order, seo_title, seo_description, timestamps
```

**`subcategories`** — child of category (Free Fire, PUBG, PlayStation, etc.)
```
id, category_id (FK), name_ar, name_en, slug, image, is_active, sort_order, seo_title, seo_description, timestamps
```

**`products`** — the heart of the catalog. Each product belongs to a subcategory and has a `product_type` that drives everything downstream.
```
id, subcategory_id (FK), supplier_id (FK nullable),
name_ar, name_en, description_ar, description_en, slug (unique),
product_type ENUM('fixed_package','custom_quantity','account_topup','manual_service'),
delivery_type ENUM('instant','timed','manual'),
delivery_time_minutes (nullable — used for timed),
cost_price DECIMAL(10,4), selling_price DECIMAL(10,4),
price_per_unit DECIMAL(10,4) (nullable — for custom_quantity type),
min_quantity INT, max_quantity INT (nullable),
image, is_active, is_featured, stock_alert_threshold,
seo_title, seo_description, seo_keywords,
sort_order, timestamps
```

The `product_type` enum directly maps to what you see in the DailyCard screenshots: `fixed_package` = Free Fire jewels with preset amounts, `custom_quantity` = BIGO HK where you type 50–10,000,000, `account_topup` = TikTok (needs email + password), `manual_service` = handled personally by admin.

**`product_packages`** — the preset denominations for `fixed_package` products. This is what creates the card grid you see in the Free Fire and IMO screenshots.
```
id, product_id (FK), name_ar, name_en,
amount (the quantity, e.g. 110),
cost_price DECIMAL(10,4), selling_price DECIMAL(10,4),
image, badge_text (e.g. "Fast"), is_available, sort_order, timestamps
```

**`product_form_fields`** — this solves the dynamic form requirement. Instead of hardcoding "enter account ID" into every product, each product defines its own required fields.
```
id, product_id (FK), field_key (e.g. "account_id", "email", "password", "player_id"),
label_ar, label_en, field_type ENUM('text','email','password','number','select'),
placeholder_ar, placeholder_en, is_required, sort_order, validation_rules JSON, timestamps
```

**`product_codes`** — the inventory of digital codes/keys for instant delivery products.
```
id, product_id (FK), package_id (FK nullable), code (encrypted), notes,
status ENUM('available','reserved','sold','failed'),
order_id (FK nullable), used_at, created_at
```

**`users`** — standard Laravel users extended.
```
id, name, email, phone, password,
preferred_language ENUM('ar','en'),
is_active, email_verified_at, timestamps
```

**`wallets`** — one wallet per user.
```
id, user_id (FK unique), balance DECIMAL(10,2), currency DEFAULT 'USD', timestamps
```

**`wallet_transactions`** — every single movement of money, immutable records.
```
id, wallet_id (FK), type ENUM('topup','purchase','refund','admin_adjustment'),
amount DECIMAL(10,2), balance_before, balance_after,
reference_type (morphable), reference_id,
description_ar, description_en, created_at
```

**`topup_requests`** — when a user uploads a receipt to fund their wallet.
```
id, user_id (FK), payment_method ENUM('omt','wish','usdt'),
amount_requested DECIMAL(10,2), receipt_image_path,
status ENUM('pending','approved','rejected'),
admin_note, processed_by (FK to users nullable), processed_at, timestamps
```

**`orders`** — the purchase record.
```
id, order_number (unique, e.g. MC-2024-00001), user_id (FK),
product_id (FK), package_id (FK nullable),
quantity INT DEFAULT 1,
unit_price DECIMAL(10,2), total_price DECIMAL(10,2),
cost_price DECIMAL(10,2), profit DECIMAL(10,2),
status ENUM('pending','processing','completed','failed','refunded'),
delivery_type, fulfillment_data JSON (stores account_id, player_id etc.),
delivery_notes TEXT, fulfilled_at, confirmed_at (when user presses "received"), timestamps
```

**`order_items`** — the actual codes/keys delivered to the user. Separated from orders because one order could theoretically deliver multiple codes.
```
id, order_id (FK), code_id (FK nullable), delivered_value TEXT (encrypted),
type ENUM('code','account_credentials','manual_note'), revealed_at, timestamps
```

**`payment_methods`** — admin configures their OMT number, Wish ID, USDT wallet address from the dashboard.
```
id, method ENUM('omt','wish','usdt'), display_name_ar, display_name_en,
account_identifier (the number/address), instructions_ar, instructions_en,
is_active, timestamps
```

**`suppliers`** — optional but clean to have from day one.
```
id, name, contact_name, email, phone, notes, is_active, timestamps
```

**`admin_settings`** — key-value store for site-wide settings (site name, maintenance mode, etc.)
```
id, key (unique), value, group, timestamps
```

**`contact_messages`** and **`feedbacks`** — simple tables for the contact form and post-purchase ratings.

---

## Phase 2 — Laravel Project Structure

Use Laravel 11 with a modular architecture using the `app/Modules/` pattern, not the default flat structure. This keeps the codebase navigable as it grows.

```
app/
├── Models/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          ← all admin controllers here
│   │   ├── Api/            ← if you add mobile later
│   │   └── Store/          ← customer-facing controllers
│   ├── Requests/           ← Form Requests for all validation
│   └── Middleware/
├── Services/               ← business logic lives here, NOT in controllers
│   ├── OrderFulfillmentService.php
│   ├── WalletService.php
│   ├── ProductFormBuilderService.php
│   └── AnalyticsService.php
├── Actions/                ← single-responsibility action classes (optional but clean)
├── Jobs/                   ← queued jobs (FulfillOrderJob, SendCodeJob)
├── Observers/              ← model observers (OrderObserver, WalletObserver)
├── Policies/               ← authorization policies
└── Events/ + Listeners/    ← event-driven architecture

resources/
├── views/
│   ├── admin/              ← admin Blade templates
│   ├── store/              ← customer store templates
│   └── emails/
├── js/
│   ├── admin/
│   └── store/
└── lang/
    ├── ar/
    └── en/
```

---

## Phase 3 — Task Breakdown by Sprint

**Sprint 1 — Foundation (Week 1–2)**

Install Laravel 11, configure MySQL and Redis, set up Laravel Breeze or Jetstream for auth scaffolding, install Spatie/laravel-permission for roles (Admin, Customer), create all migrations, run seeders for test data, set up the bilingual middleware (store locale in session and user preference), configure queue worker with Redis.

**Sprint 2 — Admin Catalog (Week 2–3)**

Build category CRUD with image upload (using Spatie/laravel-medialibrary), subcategory CRUD inheriting from category, product CRUD with the `product_type` selector that dynamically shows/hides the relevant fields (use Alpine.js for the reactive form), package management for fixed-package products, product form field builder (admin drags/adds fields like "account_id" with label in Arabic and English), product code bulk import via CSV, and stock level monitoring.

**Sprint 3 — Wallet & Top-up System (Week 3–4)**

Build the `WalletService` with `credit()`, `debit()`, and `getBalance()` methods — every call must be wrapped in a database transaction with balance validation before deducting. Build the top-up request form for customers (payment method selector that shows the admin's configured account numbers, file upload for receipt). Build the admin approval panel: admin sees all pending requests with the receipt image, can approve or reject with a note. On approval, `WalletService::credit()` is called automatically. Build the full transaction history view for both admin and customer.

**Sprint 4 — Order System (Week 4–5)**

The checkout flow is: customer selects product/package → fills dynamic form (driven by `product_form_fields`) → system validates wallet balance → creates order in `pending` status → charges wallet via `WalletService::debit()` → dispatches `FulfillOrderJob` to the queue. The `FulfillOrderJob` checks the delivery type: if `instant`, it finds an available code from `product_codes`, marks it `sold`, stores it in `order_items`, marks order `completed`, and fires a notification. If `timed` or `manual`, it notifies the admin. Build the customer-facing order detail page showing the delivered code with a "Mark as Received" button that sets `confirmed_at`. Build the admin order management panel.

**Sprint 5 — Analytics Dashboard (Week 5–6)**

Build the `AnalyticsService` that powers all charts. Use chart.js on the frontend. Key metrics: daily/weekly/monthly revenue chart, profit vs cost breakdown by product, best-selling products ranking, order status distribution, wallet top-up volume by payment method, low-stock alerts panel, VIP customer list (sorted by total spend). All queries should be cached with Redis (5-minute cache for chart data, 1-hour for rankings).

**Sprint 6 — SEO, Polish & Testing (Week 6–7)**

Add `spatie/laravel-sitemap` for auto-generated sitemaps, implement proper `<meta>` tags using per-product SEO fields, add `<link rel="alternate" hreflang="ar/en">` for bilingual SEO, set up Laravel Telescope for debugging, write feature tests for the critical paths (order creation, wallet deduction, code delivery), write unit tests for `WalletService` and `FulfillmentService`.

---

## Phase 4 — Critical Code Patterns

**The WalletService** is the most dangerous piece of code in the entire system. A bug here means money disappears or gets duplicated. Here is the correct pattern:

```php
// app/Services/WalletService.php
class WalletService
{
    public function debit(User $user, float $amount, string $description, Model $reference): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $description, $reference) {
            // Lock the row to prevent race conditions
            $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ($wallet->balance < $amount) {
                throw new InsufficientBalanceException("Balance: {$wallet->balance}, Required: {$amount}");
            }

            $balanceBefore = $wallet->balance;
            $wallet->decrement('balance', $amount);

            return WalletTransaction::create([
                'wallet_id'      => $wallet->id,
                'type'           => 'purchase',
                'amount'         => -$amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $wallet->fresh()->balance,
                'reference_type' => get_class($reference),
                'reference_id'   => $reference->id,
                'description_en' => $description,
            ]);
        });
    }
}
```

The `lockForUpdate()` is non-negotiable — without it, two simultaneous purchases can both read the same balance and both succeed even when only one should.

**The ProductFormBuilderService** renders the dynamic checkout form based on the product's configured fields:

```php
class ProductFormBuilderService
{
    public function getFormFields(Product $product): Collection
    {
        return $product->formFields()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($field) => [
                'key'         => $field->field_key,
                'label'       => app()->getLocale() === 'ar' ? $field->label_ar : $field->label_en,
                'type'        => $field->field_type,
                'placeholder' => app()->getLocale() === 'ar' ? $field->placeholder_ar : $field->placeholder_en,
                'required'    => $field->is_required,
                'rules'       => $field->validation_rules ?? [],
            ]);
    }
}
```

---

## Phase 5 — Admin Dashboard Design

The admin dashboard should use a dark sidebar layout (similar to DailyCard). Use Blade with Alpine.js and Tailwind CSS — avoid heavy JS frameworks for the admin since it keeps the build simple and fast.

The sidebar navigation groups are: Dashboard Overview, Catalog (Categories / Subcategories / Products / Codes), Orders (All Orders / Pending Fulfillment), Wallet (Top-up Requests / Transactions), Users (All Users / VIP Customers), Finance (Payment Methods / Suppliers), Content (Contact / Feedback), Settings (Site Settings / Admin Settings).

The dashboard homepage shows six KPI cards at the top (Today's revenue, Today's orders, Pending approvals, Low stock alerts, Total wallet balance, Active users today), then two charts side by side (revenue trend line chart for 30 days, and a doughnut chart of orders by product category), then a table of the last 10 orders and a table of pending top-up requests.

---

## Phase 6 — Bilingual Implementation

Install `mcamara/laravel-localization` for URL-based language switching (`/ar/products` vs `/en/products`). All model name/description fields have `_ar` and `_en` suffixes. Create a Blade helper:

```php
// In AppServiceProvider or a helper file
function t(string $arField, string $enField): string {
    return app()->getLocale() === 'ar' ? $arField : $enField;
}
// Usage in Blade: {{ t($product->name_ar, $product->name_en) }}
```

Set `dir="rtl"` on the `<html>` tag when locale is Arabic. Use Tailwind's RTL variants (`rtl:text-right`, `rtl:flex-row-reverse`) for layout flipping.

---

## Phase 7 — Security Checklist

Every code stored in `product_codes.code` and `order_items.delivered_value` must be encrypted using Laravel's `encrypt()` / `decrypt()` helpers or a dedicated `Encrypted` cast on the model. Never store codes in plain text.

Top-up receipt images go into a private S3 bucket or a non-public Laravel storage disk — never in `public/`. Only admins with the correct policy can access them. Use `Storage::temporaryUrl()` to generate short-lived links for viewing.

Rate-limit the checkout endpoint with Laravel's built-in throttle middleware: `throttle:10,1` (10 orders per minute per user) to prevent abuse.

Add `spatie/laravel-activitylog` to log all admin actions — who approved a top-up, who modified a product price, who deleted a code. This is your audit trail.

---

## Phase 8 — AI Assistant Integration Strategy

When using Cursor, Copilot, or Claude Code during development, the most effective approach is to always provide context by pasting the relevant model/service when asking for help. For example, rather than asking "write me a wallet service," paste the `WalletService` skeleton and the migration and say "implement the credit method following this exact debit pattern." This produces far more accurate, project-consistent code than starting from scratch.

Generate all migrations first and commit them before writing any model code. Then generate models with casts and relationships. Then services. Then controllers. This order matters — the AI assistant performs much better when it can see the schema in the migration file before being asked to write business logic.

Use AI tools to generate test cases: paste a service method and ask "write PHPUnit tests covering edge cases including insufficient balance, concurrent access, and the happy path." This produces genuinely useful test coverage you would otherwise skip.

---

## Development Roadmap Summary

**Week 1:** Project setup, migrations, auth, roles, bilingual middleware.
**Week 2:** Admin catalog — categories, subcategories, products, dynamic form fields.
**Week 3:** Product codes import, checkout flow skeleton, wallet service.
**Week 4:** Top-up request system with receipt upload, admin approval flow.
**Week 5:** Order fulfillment pipeline — instant delivery, manual queue, code delivery to user dashboard.
**Week 6:** Analytics dashboard with Chart.js, profit tracking, low-stock alerts.
**Week 7:** SEO meta tags, sitemap, frontend polish, performance optimization (N+1 queries, eager loading).
**Week 8:** Testing, staging deployment, security audit, go-live.

---

The first thing to build, literally day one, is **the migrations** — all of them in the correct order with proper foreign keys and indexes. Before writing a single controller or view, run `php artisan migrate` and confirm the schema is correct. Every subsequent decision flows from that foundation. When you're ready to start, send me your preferred stack choice (Blade + Alpine vs Inertia + Vue) and I'll generate the complete migration files and model classes for you.