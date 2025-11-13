<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable()->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Employees table
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('hire_date')->nullable();
            $table->string('avatar')->nullable();
            $table->string('role')->nullable();
            $table->decimal('hourly_wage', 10, 2)->nullable();
            $table->decimal('monthly_salary', 10, 2)->nullable();
            $table->integer('experience_years')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Customers table
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code')->nullable();
            $table->string('title')->nullable();
            $table->string('account_type')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->json('preferences')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0.00);
            $table->integer('order_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->boolean('is_vip')->default(false);
            $table->boolean('allergy')->default(false);
            $table->timestamp('last_order_at')->nullable();
            $table->string('authorized_person')->nullable();
            $table->decimal('credit_limit', 15, 2)->nullable()->default(0);
            $table->decimal('current_balance', 15, 2)->nullable()->default(0);
            $table->timestamps();

            $table->index(['phone', 'email']);
            $table->index('loyalty_points');
            $table->index('code');
            $table->index('account_type');
            $table->index('tax_number');
        });

        // 4. Categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('stock_type')->default(0);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('level')->default(0);
            $table->string('path')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
            $table->index(['parent_id', 'is_active']);
            $table->index(['level', 'sort_order']);
            $table->index('path');
        });

        // 5. Menu Items table
        Schema::create('menu_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->text('ingredients')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->integer('prep_time')->default(15);
            $table->boolean('is_available')->default(true);
            $table->text('allergens')->nullable();
            $table->json('nutrition_info')->nullable();
            $table->boolean('is_stock')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['is_available', 'category_id']);
        });

        // 6. Menu Item Variants table
        Schema::create('menu_item_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_item_id');
            $table->string('name');
            $table->decimal('price_adjustment', 10, 2)->default(0.00);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

        // 7. Menu Item Addons table
        Schema::create('menu_item_addons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_item_id');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

        // 8. Tables table
        Schema::create('tables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('table_number')->unique();
            $table->string('table_type')->default('dine_in');
            $table->boolean('is_reservable')->default(true);
            $table->boolean('is_smoking_allowed')->default(false);
            $table->json('features')->nullable();
            $table->decimal('location_x', 10, 2)->nullable();
            $table->decimal('location_y', 10, 2)->nullable();
            $table->integer('capacity');
            $table->string('location')->nullable();
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->index(['status', 'is_active']);
            $table->index(['table_type', 'is_reservable']);
        });

        // 9. Laravel system tables
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // 10. Settings table
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('restaurant_name')->nullable();
            $table->string('salon_name')->nullable();
            $table->string('restaurant_type')->default('restaurant');
            $table->text('phone_number')->nullable();
            $table->text('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Turkey');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('business_license')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('restaurant_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->time('opening_time')->default('09:00');
            $table->time('closing_time')->default('22:00');
            $table->json('weekly_schedule')->nullable();
            $table->json('holiday_schedule')->nullable();
            $table->boolean('is_24_hours')->default(false);
            $table->string('currency', 3)->default('TRY');
            $table->string('currency_symbol', 5)->default('₺');
            $table->decimal('default_tax_rate', 5, 2)->default(18.00);
            $table->boolean('tax_inclusive_pricing')->default(true);
            $table->boolean('service_charge_enabled')->default(false);
            $table->decimal('service_charge_rate', 5, 2)->default(10.00);
            $table->boolean('delivery_enabled')->default(false);
            $table->boolean('takeaway_enabled')->default(true);
            $table->boolean('dine_in_enabled')->default(true);
            $table->boolean('reservation_enabled')->default(true);
            $table->boolean('online_ordering_enabled')->default(false);
            $table->integer('max_table_capacity')->default(4);
            $table->integer('reservation_advance_days')->default(30);
            $table->decimal('staff_commission_rate', 5, 2)->default(5.00);
            $table->boolean('auto_assign_waiter')->default(false);
            $table->integer('max_orders_per_waiter')->default(10);
            $table->timestamps();
        });

        // 11. Order system tables
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_number')->unique();
            $table->enum('order_source', ['dine_in', 'takeaway', 'delivery', 'online', 'phone', 'walk_in', 'reservation'])->default('dine_in');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->unsignedBigInteger('waiter_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_postal_code')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'served', 'cancelled'])->default('pending');
            $table->timestamp('order_time')->useCurrent();
            $table->timestamp('served_at')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('service_charge', 10, 2)->default(0.00);
            $table->decimal('delivery_fee', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->timestamp('delivery_time')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->enum('payment_method', ['cash', 'card', 'online', 'wallet', 'points'])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_group_order')->default(false);
            $table->string('tracking_number')->nullable();
            $table->json('status_history')->nullable();
            $table->string('external_order_id')->nullable();
            $table->string('external_platform')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('table_id')->references('id')->on('tables')->onDelete('set null');
            $table->foreign('waiter_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('menu_item_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->enum('status', ['pending', 'preparing', 'ready', 'served'])->default('pending');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

        // Sales tables
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sale_number')->unique();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->string('date')->nullable();
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->decimal('total_tax', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->decimal('paid', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'online', 'wallet', 'points'])->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('seller_id')->references('id')->on('employees')->onDelete('set null');
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('menu_item_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

        // 12. Inventory & Stock tables
        // Payment methods table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type'); // cash, card, online, wallet, points
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('short_name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tax_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('rate', 5, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('manager')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('manager')->references('id')->on('employees')->onDelete('set null');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_item_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->enum('movement_type', ['in', 'out', 'transfer', 'adjustment']);
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['menu_item_id', 'movement_type']);
            $table->index(['reference_type', 'reference_id'], 'sm_ref_type_ref_idx');
        });

        // 13. Expense & Financial tables
        Schema::create('expense_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type');
            $table->text('description')->nullable();
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('expense_number')->unique();
            $table->unsignedBigInteger('expense_type_id');
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->date('date');
            $table->string('receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('expense_type_id')->references('id')->on('expense_types')->onDelete('cascade');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('set null');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->index(['date', 'expense_type_id']);
        });

        Schema::create('expense_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('expense_id');
            $table->unsignedBigInteger('expense_category_id');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_number')->unique();
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->index(['type', 'date']);
            $table->index(['reference_type', 'reference_id'], 'trans_ref_type_ref_idx');
        });

        // 14. Income management tables
        Schema::create('income_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('incomes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('income_number')->unique();
            $table->unsignedBigInteger('income_category_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->string('payment_method')->default('cash');
            $table->string('reference_number')->nullable();
            $table->string('status')->default('TAMAMLANDI');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('income_category_id')->references('id')->on('income_categories')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['date', 'status']);
            $table->index(['income_category_id', 'date']);
            $table->index(['account_id', 'date']);
            $table->index(['customer_id', 'date']);
            $table->index('income_number');
            $table->index('status');
        });

        Schema::create('income_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('income_id');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->foreign('income_id')->references('id')->on('incomes')->onDelete('cascade');
            $table->index('income_id');
        });

        // 15. Income types table
        Schema::create('income_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['sort_order']);
        });

        // 16. Customer account transactions table
        Schema::create('customers_account_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id');
            $table->date('date');
            $table->string('account');
            $table->string('type');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('transaction_type')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->index(['customer_id', 'date']);
            $table->index(['customer_id', 'type']);
            $table->index(['transaction_type', 'reference_id'], 'cat_trans_type_ref_idx');
            $table->index('date');
            $table->index('type');
        });

        // 16. Simplified Checks table
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->enum('type', ['verilen', 'alınan'])->default('alınan');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('check_number');
            $table->decimal('amount', 15, 2);
            $table->date('issue_date');
            $table->date('maturity_date');
            $table->enum('status', ['PENDING', 'CLEARED', 'BOUNCED', 'CANCELLED'])->default('PENDING');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['maturity_date']);
            $table->index('status');
        });

        // 17. Simplified Promissory Notes table
        Schema::create('promissory_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->enum('type', ['verilen', 'alınan'])->default('alınan');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('note_number');
            $table->decimal('amount', 15, 2);
            $table->date('issue_date');
            $table->date('maturity_date');
            $table->enum('status', ['ACTIVE', 'PAID', 'OVERDUE', 'CANCELLED'])->default('ACTIVE');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['maturity_date']);
            $table->index('status');
        });

        // 18. Reservation tables
        Schema::create('reservations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reservation_number')->unique();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->date('start_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->integer('party_size')->default(1);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('discount_type')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('color')->nullable();
            $table->timestamps();

            $table->foreign('table_id')->references('id')->on('tables')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->index(['start_date', 'status']);
        });

        Schema::create('reservations_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('menu_item_id');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

        // 19. Additional system tables
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['order_id', 'changed_at']);
        });

        Schema::create('employee_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('menu_item_id');
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

        Schema::create('employee_service_commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('menu_item_id');
            $table->decimal('commission_rate', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
        });

        // 20. Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payment_number')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('payment_amount', 10, 2);
            $table->string('payment_method');
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('invoice_status', ['pending', 'generated', 'sent', 'paid'])->default('pending');
            $table->text('payment_note')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('set null');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->index(['payment_status', 'created_at']);
            $table->index(['customer_id', 'created_at']);
        });

        // 21. Permission system tables
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->unique(['user_id', 'permission_id']);
        });

        // 22. Package features table
        Schema::create('package_features', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('package_name');
            $table->integer('max_employees')->default(0);
            $table->integer('max_tables')->default(0);
            $table->integer('max_menu_items')->default(0);
            $table->boolean('delivery_enabled')->default(false);
            $table->boolean('online_ordering_enabled')->default(false);
            $table->boolean('reservation_enabled')->default(false);
            $table->boolean('inventory_enabled')->default(false);
            $table->boolean('reports_enabled')->default(false);
            $table->timestamps();
        });

        // 23. Loyalty system tables
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->enum('transaction_type', ['earned', 'redeemed', 'expired', 'bonus']);
            $table->integer('points');
            $table->string('reason', 255);
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index('transaction_type');
        });

        Schema::create('loyalty_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('points_per_tl', 8, 2)->default(1.00);
            $table->decimal('tl_per_point', 8, 4)->default(0.01);
            $table->integer('min_redemption')->default(100);
            $table->integer('max_redemption_percent')->default(50);
            $table->integer('birthday_bonus')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 24. Staff shifts table
        Schema::create('staff_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('shift_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('actual_start_time')->nullable();
            $table->time('actual_end_time')->nullable();
            $table->integer('break_duration')->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['employee_id', 'shift_date']);
            $table->index('status');
        });

        // 25. Notifications tables
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'error', 'success']);
            $table->enum('target_type', ['all', 'role', 'user']);
            $table->unsignedBigInteger('target_id')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['target_type', 'target_id']);
            $table->index('is_read');
            $table->index('created_at');
        });

        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->unique(['notification_id', 'user_id']);
            $table->index(['user_id', 'is_read']);
        });

        // 26. Audit logs table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['model_type', 'model_id']);
            $table->index(['action', 'created_at']);
            $table->index('user_id');
        });

        // Insert default data
        $this->insertDefaultData();
    }

    /**
     * Insert default data
     */
    private function insertDefaultData(): void
    {
        // Insert default expense types
        DB::table('expense_types')->insert([
            ['name' => 'Malzeme', 'description' => 'Malzeme giderleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Elektrik', 'description' => 'Elektrik faturaları', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kira', 'description' => 'Kira giderleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personel', 'description' => 'Personel maaşları', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default expense categories
        DB::table('expense_categories')->insert([
            ['name' => 'Operasyonel', 'description' => 'Operasyonel giderler', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Yatırım', 'description' => 'Yatırım giderleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personel', 'description' => 'Personel giderleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default income categories
        DB::table('income_categories')->insert([
            ['name' => 'Satış', 'description' => 'Satış gelirleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hizmet', 'description' => 'Hizmet gelirleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Diğer', 'description' => 'Diğer gelirler', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Faiz', 'description' => 'Faiz gelirleri', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default income types
        DB::table('income_types')->insert([
            ['name' => 'Nakit', 'description' => 'Nakit gelir', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kart', 'description' => 'Kredi kartı geliri', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Havale', 'description' => 'Havale geliri', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Çek', 'description' => 'Çek geliri', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'EFT', 'description' => 'EFT geliri', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default accounts
        DB::table('accounts')->insert([
            ['name' => 'Nakit Kasa', 'type' => 'cash', 'description' => 'Ana nakit kasa', 'balance' => 0.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Banka Hesabı', 'type' => 'bank', 'description' => 'Ana banka hesabı', 'balance' => 0.00, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default payment methods
        DB::table('payment_methods')->insert([
            ['name' => 'Nakit', 'type' => 'cash', 'description' => 'Nakit ödeme', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kredi Kartı', 'type' => 'card', 'description' => 'Kredi kartı ile ödeme', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Online', 'type' => 'online', 'description' => 'Online ödeme', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default units
        DB::table('units')->insert([
            ['name' => 'Adet', 'short_name' => 'ad', 'description' => 'Adet birimi', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kilogram', 'short_name' => 'kg', 'description' => 'Kilogram birimi', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Litre', 'short_name' => 'lt', 'description' => 'Litre birimi', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paket', 'short_name' => 'pkt', 'description' => 'Paket birimi', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default tax rates
        DB::table('tax_rates')->insert([
            ['name' => 'KDV %18', 'rate' => 18.00, 'description' => 'Katma Değer Vergisi', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'KDV %8', 'rate' => 8.00, 'description' => 'Katma Değer Vergisi', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insert default loyalty settings
        DB::table('loyalty_settings')->insert([
            ['points_per_tl' => 1.00, 'tl_per_point' => 0.01, 'min_redemption' => 100, 'max_redemption_percent' => 50, 'birthday_bonus' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all tables in reverse order
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('staff_shifts');
        Schema::dropIfExists('loyalty_settings');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('package_features');
        Schema::dropIfExists('user_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('employee_service_commissions');
        Schema::dropIfExists('employee_commissions');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('reservations_items');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('promissory_notes');
        Schema::dropIfExists('checks');
        Schema::dropIfExists('customers_account_transactions');
        Schema::dropIfExists('income_items');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('income_categories');
        Schema::dropIfExists('income_types');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('expense_items');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('expense_types');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('units');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('tables');
        Schema::dropIfExists('menu_item_addons');
        Schema::dropIfExists('menu_item_variants');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('users');
    }
};
