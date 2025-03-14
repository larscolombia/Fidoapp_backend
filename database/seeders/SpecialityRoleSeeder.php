<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SpecialityRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private array $roleMap = [
        'trainer' => [
            'Consulta.',
            'Evaluación.'
        ],
        'vet' => [
            'Consultas.',
            'Actualización de data del perro.',
            'Actualización de vacuna'
        ]
    ];

    public function run()
    {
        try {
            DB::transaction(function () {
                $roles = $this->validateRoles();

                foreach ($this->roleMap as $roleName => $descriptions) {
                    $this->updateSpecialities(
                        descriptions: $descriptions,
                        roleId: $roles[$roleName]
                    );
                }
            });

            Log::info('Relación especialidades-roles actualizada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error en SpecialityRoleSeeder: ' . $e->getMessage());
            $this->command->error($e->getMessage());
        }
    }

    private function validateRoles(): array
    {
        $roles = Role::whereIn('name', array_keys($this->roleMap))
            ->pluck('id', 'name')
            ->toArray();

        if (count($roles) !== count($this->roleMap)) {
            $missingRoles = array_diff(array_keys($this->roleMap), array_keys($roles));
            throw new \RuntimeException(
                'Roles requeridos no encontrados: ' . implode(', ', $missingRoles)
            );
        }

        return $roles;
    }

    private function updateSpecialities(array $descriptions, int $roleId): void
    {
        $updated = DB::table('specialities')
            ->whereIn('description', $descriptions)
            ->update(['rol_id' => $roleId]);

        $this->command->info(sprintf(
            'Actualizadas %d especialidades para el rol ID %d',
            $updated,
            $roleId
        ));
    }
}
