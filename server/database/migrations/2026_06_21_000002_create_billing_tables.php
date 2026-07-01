<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('enrollment_fee', 10, 2)->default(0);
            $table->unsignedTinyInteger('billing_interval_months')->default(1);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->string('status')->default('active')->index();
            $table->unsignedTinyInteger('billing_day');
            $table->decimal('price', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'status']);
        });

        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('monthly');
            $table->string('description');
            $table->date('due_date')->index();
            $table->decimal('amount', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('status')->default('pending')->index();
            $table->timestamps();

            $table->unique(['membership_id', 'type', 'due_date']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charge_id')->constrained()->restrictOnDelete();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->dateTime('paid_at');
            $table->string('method');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('charges');
        Schema::dropIfExists('memberships');
        Schema::dropIfExists('plans');
    }
};
