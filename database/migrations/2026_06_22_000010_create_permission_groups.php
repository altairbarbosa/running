<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('key', 100)->unique();
            $table->string('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });
        Schema::create('permission_group_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_group_id')->constrained()->cascadeOnDelete();
            $table->string('permission', 100);
            $table->unique(['permission_group_id', 'permission'], 'group_permission_unique');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('permission_group_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        $now = now();
        $groups = [
            'admin' => ['name' => 'Administradores', 'description' => 'Acesso completo e protegido ao sistema.'],
            'trainer' => ['name' => 'Professores', 'description' => 'Gestão de alunos, exercícios e treinos.'],
            'member' => ['name' => 'Alunos', 'description' => 'Acesso pessoal a treinos, mensalidades e loja.'],
        ];
        foreach ($groups as $key => $group) {
            $id = DB::table('permission_groups')->insertGetId([...$group, 'key' => $key, 'is_system' => true, 'created_at' => $now, 'updated_at' => $now]);
            $permissions = config("permissions.defaults.$key", []);
            if ($permissions !== ['*']) {
                DB::table('permission_group_permissions')->insert(array_map(fn ($permission) => ['permission_group_id' => $id, 'permission' => $permission], $permissions));
            }
            DB::table('users')->where('role', $key)->update(['permission_group_id' => $id]);
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropConstrainedForeignId('permission_group_id'));
        Schema::dropIfExists('permission_group_permissions');
        Schema::dropIfExists('permission_groups');
    }
};
