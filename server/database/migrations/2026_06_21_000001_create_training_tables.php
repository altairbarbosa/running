<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('muscle_group')->nullable()->index();
            $table->text('instructions')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('name');
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->string('status')->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('workout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('position');
            $table->unsignedSmallInteger('sets')->default(3);
            $table->string('repetitions', 30);
            $table->decimal('weight', 7, 2)->nullable();
            $table->unsignedSmallInteger('rest_seconds')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['workout_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_items');
        Schema::dropIfExists('workouts');
        Schema::dropIfExists('exercises');
    }
};
