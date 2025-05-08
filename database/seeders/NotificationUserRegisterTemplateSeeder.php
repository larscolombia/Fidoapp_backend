<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplate;

class NotificationUserRegisterTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         // Disable foreign key checks!
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         $types = [
            [
                'type' => 'notification_type',
                'value' => 'new_user',
                'name' => 'New User',
            ],
        ];
        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $template = NotificationTemplate::create([
            'type' => 'new_user',
            'name' => 'new_user',
            'label' => 'New User',
            'status' => 1,
            'to' => '["admin","demo_admin","employee", "user"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language'             => 'en',
            'notification_link'    => '',
            'notification_message' => '¡Un nuevo usuario se ha registrado en la aplicación!',
            'status'               => 1,
            'subject'              => '¡Nuevo usuario registrado!',
            'template_detail'      => '
                <p>Hola [[ admin_name ]],</p>
                <p>Le informamos que un nuevo usuario se ha registrado en la plataforma.</p>
                <p><strong>Detalles del usuario:</strong></p>
                <ul>
                    <li><strong>Nombre:</strong> [[ user_name ]]</li>
                    <li><strong>Email:</strong> [[ user_email ]]</li>
                    <li><strong>Fecha de registro:</strong> [[ registration_date ]]</li>
                </ul>
                <p>Por favor, revise la información del nuevo usuario en el panel de administración si es necesario realizar alguna acción adicional.</p>
                <p>Saludos cordiales,<br>El equipo de [[ company_name ]]</p>
            ',
        ]);
    }
}
