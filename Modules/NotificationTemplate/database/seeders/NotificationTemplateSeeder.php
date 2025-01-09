<?php

namespace Modules\NotificationTemplate\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Constant\Models\Constant;
use Modules\NotificationTemplate\Models\NotificationTemplate;

class NotificationTemplateSeeder extends Seeder
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
        /*
         * NotificationTemplates Seed
         * ------------------
         */

        // DB::table('notificationtemplates')->truncate();
        // echo "Truncate: notificationtemplates \n";

        $types = [
            [
                'type' => 'notification_type',
                'value' => 'new_booking',
                'name' => 'New Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'accept_booking',
                'name' => 'Accept Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'reject_booking',
                'name' => 'Reject Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'complete_booking',
                'name' => 'Complete On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'accept_booking_request',
                'name' => 'Accept Booking Request',
            ],
            [
                'type' => 'notification_type',
                'value' => 'cancel_booking',
                'name' => 'Cancel On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'change_password',
                'name' => 'Chnage Password',
            ],
            [
                'type' => 'notification_type',
                'value' => 'forget_email_password',
                'name' => 'Forget Email/Password',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'id',
                'name' => 'ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_name',
                'name' => 'Customer Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'description',
                'name' => 'Description / Note',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_id',
                'name' => 'Booking ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_date',
                'name' => 'Booking Date',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_time',
                'name' => 'Booking Time',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_services_names',
                'name' => 'Booking Services Names',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_duration',
                'name' => 'Booking Duration',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'employee_name',
                'name' => 'Staff Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'venue_address',
                'name' => 'Venue / Address',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'logged_in_user_fullname',
                'name' => 'Your Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'logged_in_user_role',
                'name' => 'Your Position',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_name',
                'name' => 'Company Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_contact_info',
                'name' => 'Company Info',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_id',
                'name' => 'User\' ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_password',
                'name' => 'User Password',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'link',
                'name' => 'Link',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'site_url',
                'name' => 'Site URL',
            ],
            [
                'type' => 'notification_to',
                'value' => 'user',
                'name' => 'User',
            ],

            [
                'type' => 'notification_to',
                'value' => 'employee',
                'name' => 'Employee',
            ],

            [
                'type' => 'notification_to',
                'value' => 'demo_admin',
                'name' => 'Demo Admin',
            ],
            [
                'type' => 'notification_to',
                'value' => 'admin',
                'name' => 'Admin',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        //echo " Insert: notificationtempletes \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('notification_templates')->delete();
        DB::table('notification_template_content_mapping')->delete();

        $template = NotificationTemplate::create([
            'type' => 'new_booking',
            'name' => 'new_booking',
            'label' => 'Booking confirmation',
            'status' => 1,
            'to' => '["admin","demo_admin","employee", "user"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => '¡Gracias por elegir nuestros servicios! Su reserva ha sido confirmada exitosamente. Esperamos poder servirle y brindarle una experiencia excepcional. Estén atentos para más actualizaciones.',
            'status' => 1,
            'subject' => '¡Nueva Reserva!',
            'template_detail' => '
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Asunto: Confirmación de cita - ¡Gracias!</span></p>
            <p><strong id="docs-internal-guid-7d6bdcce-7fff-5035-731b-386f9021a5db" style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Estimado [[ user_name ]],</span></p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">¡Estamos encantados de informarle que su cita ha sido confirmada con éxito! Gracias por elegir nuestros servicios. Estamos entusiasmados de tenerlo como nuestro valioso cliente y estamos comprometidos a brindarle una experiencia maravillosa.</span></p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <h4>Appointment Details</h4>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">ID de cita: [[ id ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Fecha de cita: [[ booking_date ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Servicio/Evento: [[ booking_services_names ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Fecha: [[ booking_date ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Hora: [[ booking_time ]]</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Ubicación: [[ venue_address ]]</span></p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Queremos asegurarle que hemos recibido los detalles de su cita y que todo está en orden. Nuestro equipo se está preparando ansiosamente para hacer de esta una experiencia memorable para usted. Si tiene algún requisito específico o pregunta con respecto a su cita, no dude en comunicarse con nosotros.</span></p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Le recomendamos marcar su calendario y configurar un recordatorio de la fecha y hora del evento para asegurarse de no perder su cita. Si hay alguna actualización o cambio en su cita, se lo notificaremos de inmediato.</span></p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Una vez más, gracias por elegir nuestros servicios. Esperamos brindarle un servicio excepcional y crear recuerdos duraderos. Si tiene más consultas, no dude en ponerse en contacto con nuestro amable equipo de atención al cliente.</span></p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">Atentamente,</span></p>
            <p><strong style="font-weight: normal;">&nbsp;</strong></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_fullname ]],</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ logged_in_user_role ]],</span></p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_name ]],</span></p>
            <p>&nbsp;</p>
            <p dir="ltr" style="line-height: 1.38; margin-top: 0pt; margin-bottom: 0pt;"><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">[[ company_contact_info ]]</span></p>
            <p><span style="font-size: 11pt; font-family: Arial; color: #000000; background-color: transparent; font-weight: 400; font-style: normal; font-variant: normal; text-decoration: none; vertical-align: baseline; white-space: pre-wrap;">&nbsp;</span></p>
          ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'accept_booking',
            'name' => 'accept_booking',
            'label' => 'Accept Booking',
            'status' => 1,
            'to' => '["user"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => 'Bienvenido a su alojamiento reservado. ¡Esperamos que tengas una estancia agradable!',
            'status' => 1,
            'subject' => '¡Reserva aceptada!',
            'template_detail' => '<p>Bienvenido a su alojamiento reservado. ¡Esperamos que tengas una estancia agradable!</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'reject_booking',
            'name' => 'reject_booking',
            'label' => 'Reject Booking',
            'status' => 1,
            'to' => '["user"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => 'Gracias por elegir nuestros servicios. Por favor recuerde revisar por [check-out time]. ¡Esperamos que hayas tenido una experiencia maravillosa!',
            'status' => 1,
            'subject' => 'Reserva rechazada',
            'template_detail' => '<p>Gracias por elegir nuestros servicios. Por favor recuerde revisar por [check-out time]. ¡Esperamos que hayas tenido una experiencia maravillosa!</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'complete_booking',
            'name' => 'complete_booking',
            'label' => 'Complete On Booking',
            'status' => 1,
            'to' => '["user"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => '¡Felicidades! Su reserva se ha completado con éxito. Apreciamos su negocio y esperamos poder servirle nuevamente.',
            'status' => 1,
            'subject' => 'Cita completa por correo electrónico con factura',
            'template_detail' => '
            <p>Subject: Finalización y facturación de citas</p>
            <p>&nbsp;</p>
            <p>Dear [[ user_name ]],</p>
            <p>&nbsp;</p>
            <p>Le escribimos para informarle de que su reciente cita con nosotros ha concluido con éxito. Agradecemos sinceramente su confianza en nuestros servicios y la oportunidad de atenderle.</p>
            <p>&nbsp;</p>
            <h4>Detalles de la cita:</h4>
            <p>&nbsp;</p>
            <p>Fecha de cita: [[ booking_date ]]</p>
            <p>Hora de la cita: [[ booking_time ]]</p>
            <p>Servicio prestado: [[ booking_services_names ]]</p>
            <p>Duración del servicio: [[ booking_duration ]]</p>
            <p>Proveedor de servicios: [[ employee_name ]]</p>
            <p>&nbsp;</p>
            <p>Nos complace informarle de que la cita se llevó a cabo sin contratiempos, y esperamos que cumpliera o superara sus expectativas. Nuestro entregado equipo trabajó con diligencia para garantizar su satisfacción durante todo el proceso.</p>
            <p>&nbsp;</p>
            <p>Para garantizar la transparencia de nuestros procedimientos de facturación, adjuntamos la factura de los servicios prestados durante su cita. La factura incluye un desglose detallado de los servicios prestados, los impuestos aplicables y el importe total adeudado. Adjuntamos la factura a este correo electrónico (o le proporcionamos instrucciones sobre cómo acceder a la factura si está alojada en línea).</p>
            <p>&nbsp;</p>
            <p>Gracias una vez más por elegir nuestros servicios. Apreciamos su confianza y apoyo.</p>
            <p>&nbsp;</p>
            <p>Saludos cordiales,</p>
            <p>&nbsp;</p>
            <p>[[ logged_in_user_fullname ]]<br />[[ logged_in_user_role ]]<br />[[ company_name ]]</p>
            <p>[[ company_contact_info ]]</p>
          ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'cancel_booking',
            'name' => 'cancel_booking',
            'label' => 'Cancel On Booking',
            'status' => 1,
            'to' => '["user"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => 'Lamentamos informarle de que su reserva ha sido cancelada. Si tiene alguna pregunta o necesita más ayuda, póngase en contacto con nuestro equipo de asistencia.',
            'status' => 1,
            'subject' => 'Cancelación de reservas',
            'template_detail' => '<p><span id="docs-internal-guid-b1e18659-7fff-e334-ed58-8ced003b3621"><span style="font-size: 11pt; font-family: Arial; background-color: transparent; font-variant-numeric: normal; font-variant-east-asian: normal; font-variant-alternates: normal; vertical-align: baseline; white-space-collapse: preserve;">Lamentamos informarle de que su reserva ha sido cancelada. Si tiene alguna pregunta o necesita más ayuda, póngase en contacto con nuestro equipo de asistencia.</span></span></p>',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'accept_booking_request',
            'name' => 'accept_booking_request',
            'label' => 'Accept Booking Request',
            'status' => 1,
            'to' => '["admin","demo_admin","employee","user"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => 'Bienvenido a su alojamiento reservado. ¡Esperamos que tengas una estancia agradable!',
            'status' => 1,
            'subject' => 'Solicitud de reserva aceptada',
            'template_detail' => '<p>Bienvenido a su alojamiento reservado. ¡Esperamos que tengas una estancia agradable!</p>',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'change_password',
            'name' => 'change_password',
            'label' => 'Change Password',
            'status' => 1,
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Cambio de contraseña',
            'template_detail' => '
            <p>Asunto: Confirmación de cambio de contraseña</p>
            <p>Dear [[ user_name ]],</p>
            <p>&nbsp;</p>
            <p>Queríamos informarle de que recientemente se ha realizado un cambio de contraseña en su cuenta. Si usted no inició este cambio, por favor tome medidas inmediatas para asegurar su cuenta.</p>
            <p>&nbsp;</p>
            <p>Para recuperar el control y asegurar tu cuenta:</p>
            <p>&nbsp;</p>
            <p>Visita [[ link ]].</p>
            <p>Siga las instrucciones para verificar su identidad.</p>
            <p>Crea una contraseña fuerte y única.</p>
            <p>Actualice las contraseñas de cualquier otra cuenta que utilice credenciales similares.</p>
            <p>Si tiene alguna duda o necesita ayuda, póngase en contacto con nuestro equipo de atención al cliente.</p>
            <p>&nbsp;</p>
            <p>Gracias por prestar atención a este asunto.</p>
            <p>&nbsp;</p>
            <p>Saludos cordiales,</p>
            <p>[[ logged_in_user_fullname ]]<br />[[ logged_in_user_role ]]<br />[[ company_name ]]</p>
            <p>[[ company_contact_info ]]</p>
          ',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'forget_email_password',
            'name' => 'forget_email_password',
            'label' => 'Forget Email/Password',
            'status' => 1,
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => '',
            'status' => 1,
            'subject' => 'Olvido de correo electrónico/contraseña',
            'template_detail' => '
            <p>Asunto: Instrucciones para restablecer la contraseña</p>
            <p>&nbsp;</p>
            <p>Dear [[ user_name ]],</p>
            <p>Se ha iniciado una solicitud de restablecimiento de contraseña para su cuenta. Para restablecer su contraseña:</p>
            <p>&nbsp;</p>
            <p>Visitar [[ link ]].</p>
            <p>Introduzca su dirección de correo electrónico.</p>
            <p>Siga las instrucciones proporcionadas para completar el proceso de restablecimiento.</p>
            <p>Si no has solicitado este restablecimiento o necesitas ayuda, ponte en contacto con nuestro equipo de asistencia.</p>
            <p>&nbsp;</p>
            <p>Muchas gracias.</p>
            <p>&nbsp;</p>
            <p>Saludos Cordiales,</p>
            <p>[[ logged_in_user_fullname ]]<br />[[ logged_in_user_role ]]<br />[[ company_name ]]</p>
            <p>[[ company_contact_info ]]</p>
            <p>&nbsp;</p>
          ',
        ]);


        $template = NotificationTemplate::create([
            'type' => 'order_placed',
            'name' => 'order_placed',
            'label' => 'Order Placed',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => 'Gracias por elegirnos para su reciente pedido. Nos complace confirmarle que su pedido se ha realizado correctamente.',
            'status' => 1,
            'subject' => 'Pedido realizado',
            'template_detail' => '<p>Gracias por elegirnos para su reciente pedido. Nos complace confirmarle que su pedido se ha realizado correctamente.</p>',
        ]);



        $template = NotificationTemplate::create([
            'type' => 'order_proccessing',
            'name' => 'order_proccessing',
            'label' => 'Order Processing',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => "Nos complace informarle de que su pedido se está preparando y pronto estará en camino para satisfacer sus papilas gustativas.",
            'status' => 1,
            'subject' => 'Orden Procesada',
            'template_detail' => "<p>Nos complace informarle de que su pedido se está preparando y pronto estará en camino para satisfacer sus papilas gustativas.</p>",
        ]);


        $template = NotificationTemplate::create([
            'type' => 'order_delivered',
            'name' => 'order_delivered',
            'label' => 'Order Delivered',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => "Nos complace informarle de que su pedido ha sido entregado correctamente en la puerta de su casa.",
            'status' => 1,
            'subject' => 'Entrega del pedido',
            'template_detail' => "<p>Nos complace informarle de que su pedido ha sido entregado correctamente en la puerta de su casa.</p>",
        ]);

        $template = NotificationTemplate::create([
            'type' => 'order_cancelled',
            'name' => 'order_cancelled',
            'label' => 'Oreder Cancelled',
            'status' => 1,
            'to' => '["user","admin","demo_admin"]',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'es',
            'notification_link' => '',
            'notification_message' => "Lamentamos informarle de que su reciente pedido ha sido cancelado.",
            'status' => 1,
            'subject' => 'Pedido cancelado',
            'template_detail' => "<p>Lamentamos informarle de que su reciente pedido ha sido cancelado.</p>",
        ]);

    }
}
