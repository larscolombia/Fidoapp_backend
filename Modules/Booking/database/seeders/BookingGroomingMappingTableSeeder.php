<?php

namespace Modules\Booking\database\seeders;

use Illuminate\Database\Seeder;

class BookingGroomingMappingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('booking_grooming_mapping')->delete();

        \DB::table('booking_grooming_mapping')->insert(array(
            0 =>
            array(
                'id' => 1,
                'date_time' => '2023-08-01 09:30:00',
                'booking_id' => 19,
                'service_id' => 24,
                'service_name' => 'Baño Medicado', // Traducción de "Medicated Bath"
                'price' => 10.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-01 13:35:53',
                'updated_at' => '2023-08-01 13:35:53',
                'deleted_at' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'date_time' => '2023-08-02 18:30:00',
                'booking_id' => 20,
                'service_id' => 61,
                'service_name' => 'Limpieza de Almohadillas', // Traducción de "Paw Pad Cleaning"
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-01 13:37:59',
                'updated_at' => '2023-08-01 13:37:59',
                'deleted_at' => NULL,
            ),
            2 =>
            array(
                'id' => 3,
                'date_time' => '2023-08-16 18:00:00',
                'booking_id' => 21,
                'service_id' => 48,
                'service_name' => 'Remoción de Cerumen', // Traducción de "Ear Wax Removal"
                'price' => 50.0,
                'duration' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-01 13:38:50',
                'updated_at' => '2023-08-01 13:38:50',
                'deleted_at' => NULL,
            ),
            3 =>
            array(
                'id' => 4,
                'date_time' => '2023-08-03 19:30:00',
                'booking_id' => 22,
                'service_id' => 36,
                'service_name' => 'Plumaje y Mezcla', // Traducción de "Feathering and Blending"
                'price' => 35.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-01 13:39:34',
                'updated_at' => '2023-08-01 13:39:34',
                'deleted_at' => NULL,
            ),
            4 =>
            array(
                'id' => 6,
                'date_time' => '2023-08-23 16:30:00',
                'booking_id' => 24,
                'service_id' => 59,
                'service_name' => 'Acondicionamiento de Pelaje', // Traducción de "Coat Conditioning"
                'price' => 35.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-01 13:40:46',
                'updated_at' => '2023-08-01 13:40:46',
                'deleted_at' => NULL
            ),



            5 =>
            array(
                'id' => 9,
                'date_time' => '2023-08-01 19:20:00',
                'booking_id' => 27,
                'service_id' => 26,
                'service_name' => 'Baño para Pulgas y Garrapatas', // Traducción de "Flea and Tick Bath"
                'price' => 30.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-01 13:52:31',
                'updated_at' => '2023-08-01 13:52:31',
                'deleted_at' => NULL,
            ),
            6 =>
            array(
                'id' => 10,
                'date_time' => '2023-08-09 09:55:00',
                'booking_id' => 28,
                'service_id' => 47,
                'service_name' => 'Lijado de Uñas', // Traducción de "Nail Grinding"
                'price' => 15.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-01 13:53:22',
                'updated_at' => '2023-08-01 13:53:22',
                'deleted_at' => NULL,
            ),
            7 =>
            array(
                'id' => 11,
                'date_time' => '2023-08-07 20:00:00',
                'booking_id' => 29,
                'service_id' => 66,
                'service_name' => 'Exfoliación de Almohadillas', // Traducción de "Paw Pad Exfoliation"
                'price' => 30.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-01 13:55:56',
                'updated_at' => '2023-08-01 13:55:56',
                'deleted_at' => NULL,
            ),
            8 =>
            array(
                'id' => 12,
                'date_time' => '2023-08-03 10:30:00',
                'booking_id' => 56,
                'service_id' => 76,
                'service_name' => 'Masaje de Relajación', // Traducción de "Relaxation Massage"
                'price' => 16.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-02 05:33:07',
                'updated_at' => '2023-08-02 05:33:07',
                'deleted_at' => NULL,
            ),


            9 =>
            array(
                'id' => 13,
                'date_time' => '2023-08-22 11:25:00',
                'booking_id' => 57,
                'service_id' => 81,
                'service_name' => 'Soporte de Rehabilitación', // Traducción de "Rehabilitation Support"
                'price' => 30.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-02 05:34:03',
                'updated_at' => '2023-08-02 05:34:03',
                'deleted_at' => NULL,
            ),
            10 =>
            array(
                'id' => 14,
                'date_time' => '2023-08-26 10:20:00',
                'booking_id' => 61,
                'service_id' => 41,
                'service_name' => 'Groomers Profesionales', // Traducción de "Professional Groomers"
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 05:47:54',
                'updated_at' => '2023-08-26 05:47:54',
                'deleted_at' => NULL,
            ),
            11 =>
            array(
                'id' => 15,
                'date_time' => '2023-08-26 13:20:00',
                'booking_id' => 63,
                'service_id' => 38,
                'service_name' => 'Estilo para Ocasiones Especiales', // Traducción de "Special Occasion Styling"
                'price' => 25.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 06:34:35',
                'updated_at' => '2023-08-26 06:34:35',
                'deleted_at' => NULL,
            ),
            12 =>
            array(
                'id' => 16,
                'date_time' => '2023-08-26 19:40:00',
                'booking_id' => 64,
                'service_id' => 45,
                'service_name' => 'Lijado de Uñas', // Traducción de "Nail Filing"
                'price' => 30.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 06:35:56',
                'updated_at' => '2023-08-26 06:35:56',
                'deleted_at' => NULL,
            ),


            13 =>
            array(
                'id' => 17,
                'date_time' => '2023-08-30 18:40:00',
                'booking_id' => 75,
                'service_id' => 25,
                'service_name' => 'Baño Sin Agua',
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 07:20:56',
                'updated_at' => '2023-08-26 07:20:56',
                'deleted_at' => NULL,
            ),
            14 =>
            array(
                'id' => 18,
                'date_time' => '2023-09-02 16:20:00',
                'booking_id' => 76,
                'service_id' => 53,
                'service_name' => 'Recorte de Pelo de Oído',
                'price' => 25.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 07:22:02',
                'updated_at' => '2023-08-26 07:22:02',
                'deleted_at' => NULL,
            ),


            15 =>
            array(
                'id' => 19,
                'date_time' => '2023-08-29 12:45:00',
                'booking_id' => 91,
                'service_id' => 25,
                'service_name' => 'Baño Sin Agua', // Traducción de "Waterless Bath"
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 07:42:58',
                'updated_at' => '2023-08-26 07:42:58',
                'deleted_at' => NULL,
            ),
            16 =>
            array(
                'id' => 20,
                'date_time' => '2023-08-26 19:30:00',
                'booking_id' => 92,
                'service_id' => 26,
                'service_name' => 'Baño para Pulgas y Garrapatas', // Traducción de "Flea and Tick Bath"
                'price' => 30.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 07:43:49',
                'updated_at' => '2023-08-26 07:43:49',
                'deleted_at' => NULL,
            ),
            17 =>
            array(
                'id' => 21,
                'date_time' => '2023-08-26 14:35:00',
                'booking_id' => 99,
                'service_id' => 25,
                'service_name' => 'Baño Sin Agua', // Traducción de "Waterless Bath"
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 08:14:20',
                'updated_at' => '2023-08-26 08:14:20',
                'deleted_at' => NULL,
            ),
            18 =>
            array(
                'id' => 22,
                'date_time' => '2023-08-30 17:40:00',
                'booking_id' => 103,
                'service_id' => 24,
                'service_name' => 'Baño Medicado', // Traducción de "Medicated Bath"
                'price' => 10.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 09:05:07',
                'updated_at' => '2023-08-26 09:05:07',
                'deleted_at' => NULL,
            ),
            19 =>
            array(
                'id' => 23,
                'date_time' => '2023-09-13 20:10:00',
                'booking_id' => 106,
                'service_id' => 27,
                'service_name' => 'Baño Hidratante', // Traducción de "Moisturizing Bath"
                'price' => 20.0,
                'duration' => 1,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 09:15:37',
                'updated_at' => '2023-08-26 09:15:37',
                'deleted_at' => NULL,
            ),

            20 =>
            array(
                'id' => 24,
                'date_time' => '2023-08-26 19:30:00',
                'booking_id' => 107,
                'service_id' => 25,
                'service_name' => 'Baño Sin Agua',
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 09:18:35',
                'updated_at' => '2023-08-26 09:18:35',
                'deleted_at' => NULL,
            ),


            21 =>
            array(
                'id' => 25,
                'date_time' => '2023-08-27 19:40:00',
                'booking_id' => 113,
                'service_id' => 31,
                'service_name' => 'Baño Hipoalergénico', // Traducción de "Hypoallergenic Bath"
                'price' => 18.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 09:40:27',
                'updated_at' => '2023-08-26 09:40:27',
                'deleted_at' => NULL,
            ),
            22 =>
            array(
                'id' => 26,
                'date_time' => '2023-08-27 21:35:00',
                'booking_id' => 120,
                'service_id' => 25,
                'service_name' => 'Baño Sin Agua', // Traducción de "Waterless Bath"
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 10:01:24',
                'updated_at' => '2023-08-26 10:01:24',
                'deleted_at' => NULL,
            ),
            23 =>
            array(
                'id' => 27,
                'date_time' => '2023-09-11 09:35:00',
                'booking_id' => 128,
                'service_id' => 26,
                'service_name' => 'Baño para Pulgas y Garrapatas', // Traducción de "Flea and Tick Bath"
                'price' => 30.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 10:20:16',
                'updated_at' => '2023-08-26 10:20:16',
                'deleted_at' => NULL,
            ),
            24 =>
            array(
                'id' => 28,
                'date_time' => '2023-08-27 14:25:00',
                'booking_id' => 131,
                'service_id' => 25,
                'service_name' => 'Baño Sin Agua', // Traducción de "Waterless Bath"
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 10:28:58',
                'updated_at' => '2023-08-26 10:28:58',
                'deleted_at' => NULL,
            ),
            25 =>
            array(
                'id' => 29,
                'date_time' => '2023-08-26 20:35:00',
                'booking_id' => 137,
                'service_id' => 24,
                'service_name' => 'Baño Medicado', // Traducción de "Medicated Bath"
                'price' => 10.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 10:41:41',
                'updated_at' => '2023-08-26 10:41:41',
                'deleted_at' => NULL,
            ),
            26 =>
            array(
                'id' => 30,
                'date_time' => '2023-08-26 09:35:00',
                'booking_id' => 151,
                'service_id' => 25,
                'service_name' => 'Baño Sin Agua',
                'price' => 20.0,
                'duration' => 0,
                'created_by' => NULL,
                'updated_by' => NULL,
                'deleted_by' => NULL,
                'created_at' => '2023-08-26 11:12:16',
                'updated_at' => '2023-08-26 11:12:16',
                'deleted_at' => NULL,
            ),
        ));
    }
}
