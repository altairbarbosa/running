<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muscle_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('slug', 100)->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->foreignId('muscle_group_id')->nullable()->after('name')->constrained()->nullOnDelete();
        });

        $defaults = [
            'Peitoral', 'Costas', 'Ombros', 'Bíceps', 'Tríceps', 'Antebraços',
            'Quadríceps', 'Posteriores de coxa', 'Glúteos', 'Panturrilhas',
            'Abdômen', 'Corpo inteiro', 'Cardiorrespiratório',
        ];

        $legacyGroups = DB::table('exercises')->whereNotNull('muscle_group')->distinct()->pluck('muscle_group');

        collect($defaults)->merge($legacyGroups)->filter()->unique(fn ($name) => Str::slug($name))
            ->values()->each(function ($name, $index) {
                DB::table('muscle_groups')->insert([
                    'name' => trim($name),
                    'slug' => Str::slug($name),
                    'sort_order' => ($index + 1) * 10,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        foreach ($legacyGroups as $legacyGroup) {
            $groupId = DB::table('muscle_groups')->where('slug', Str::slug($legacyGroup))->value('id');
            DB::table('exercises')->where('muscle_group', $legacyGroup)->update(['muscle_group_id' => $groupId]);
        }

        Schema::table('exercises', function (Blueprint $table) {
            $table->dropIndex(['muscle_group']);
            $table->dropColumn('muscle_group');
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->string('muscle_group', 80)->nullable()->index();
        });

        DB::table('exercises')->orderBy('id')->eachById(function ($exercise) {
            $name = DB::table('muscle_groups')->where('id', $exercise->muscle_group_id)->value('name');
            DB::table('exercises')->where('id', $exercise->id)->update(['muscle_group' => $name]);
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->dropConstrainedForeignId('muscle_group_id');
        });

        Schema::dropIfExists('muscle_groups');
    }
};
