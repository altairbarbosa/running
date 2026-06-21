<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrador Running',
            'email' => 'admin@running.test',
            'role' => 'admin',
        ]);

        $member = User::factory()->create([
            'name' => 'Aluno Demonstração',
            'email' => 'aluno@running.test',
            'role' => 'member',
            'phone' => '(53) 99999-0000',
            'birth_date' => '1997-06-09',
        ]);

        $exercises = collect([
            ['name' => 'Supino reto', 'muscle_group' => 'Peitoral'],
            ['name' => 'Remada baixa', 'muscle_group' => 'Costas'],
            ['name' => 'Agachamento livre', 'muscle_group' => 'Pernas'],
            ['name' => 'Desenvolvimento com halteres', 'muscle_group' => 'Ombros'],
        ])->map(fn ($exercise) => Exercise::create($exercise));

        $workout = Workout::create([
            'member_id' => $member->id,
            'created_by' => $admin->id,
            'name' => 'A — Peitoral e costas',
            'starts_at' => now()->toDateString(),
            'ends_at' => now()->addMonth()->toDateString(),
            'status' => 'active',
            'notes' => 'Priorizar execução controlada e amplitude confortável.',
        ]);

        $workout->items()->createMany([
            ['exercise_id' => $exercises[0]->id, 'position' => 1, 'sets' => 4, 'repetitions' => '8-12', 'weight' => 40, 'rest_seconds' => 90],
            ['exercise_id' => $exercises[1]->id, 'position' => 2, 'sets' => 4, 'repetitions' => '10-12', 'weight' => 35, 'rest_seconds' => 75],
        ]);

        $this->call(BillingSeeder::class);
    }
}
