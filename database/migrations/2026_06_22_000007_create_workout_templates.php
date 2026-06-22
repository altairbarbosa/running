<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('workout_templates', function(Blueprint $table){$table->id();$table->foreignId('created_by')->constrained('users')->restrictOnDelete();$table->string('name');$table->text('description')->nullable();$table->boolean('active')->default(true)->index();$table->timestamps();});
        Schema::create('workout_template_items', function(Blueprint $table){$table->id();$table->foreignId('workout_template_id')->constrained()->cascadeOnDelete();$table->foreignId('exercise_id')->constrained()->restrictOnDelete();$table->unsignedSmallInteger('position');$table->unsignedSmallInteger('sets')->default(3);$table->string('repetitions',30);$table->unsignedSmallInteger('rest_seconds')->nullable();$table->timestamps();$table->unique(['workout_template_id','position']);});
        Schema::table('workouts',function(Blueprint $table){$table->foreignId('workout_template_id')->nullable()->after('created_by')->constrained()->nullOnDelete();});
    }
    public function down(): void {Schema::table('workouts',fn(Blueprint $table)=>$table->dropConstrainedForeignId('workout_template_id'));Schema::dropIfExists('workout_template_items');Schema::dropIfExists('workout_templates');}
};
