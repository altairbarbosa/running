<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::table('workouts',fn(Blueprint $table)=>$table->foreignId('source_workout_id')->nullable()->after('workout_template_id')->constrained('workouts')->nullOnDelete()); }
    public function down(): void { Schema::table('workouts',fn(Blueprint $table)=>$table->dropConstrainedForeignId('source_workout_id')); }
};
