<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EmployeeRatingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('employee_rating')->delete();

        \DB::table('employee_rating')->insert(array(
            0 =>
            array(
                'id' => 1,
                'employee_id' => 12,
                'user_id' => 2,
                'review_msg' => '¡Excelente servicio de alimentación y agua! Mi mascota estuvo bien nutrida e hidratada durante toda la estancia. ¡Lo recomiendo encarecidamente! ❤️🤩',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            1 =>
            array(
                'id' => 2,
                'employee_id' => 13,
                'user_id' => 5,
                'review_msg' => 'Agradezco las actualizaciones y fotos que enviaste mientras mi mascota estaba hospedada contigo. Me tranquilizó saber que estaba en buenas manos. 😍💥',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            2 =>
            array(
                'id' => 3,
                'employee_id' => 14,
                'user_id' => 4,
                'review_msg' => 'Gracias por cuidar tan maravillosamente a mi bebé peludo durante su hospedaje. ¡Tu amor y atención marcaron la diferencia! 🎊💥',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            3 =>
            array(
                'id' => 4,
                'employee_id' => 15,
                'user_id' => 9,
                'review_msg' => '¡No podría haber pedido un mejor cuidador! Mi mascota estuvo feliz, saludable y claramente bien querida durante toda su estancia. 💕😊',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            4 =>
            array(
                'id' => 5,
                'employee_id' => 16,
                'user_id' => 8,
                'review_msg' => 'Tu pasión por los animales se refleja en tu trabajo. Estoy agradecido de que mi mascota te haya tenido como cuidador durante su hospedaje. ❤️🥰',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            5 =>
            array(
                'id' => 6,
                'employee_id' => 17,
                'user_id' => 10,
                'review_msg' => 'Eres un verdadero profesional con un corazón de oro. Mi mascota regresó a casa feliz y contenta después de su tiempo contigo. 🤩😊',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            6 =>
            array(
                'id' => 7,
                'employee_id' => 18,
                'user_id' => 5,
                'review_msg' => '¡Excelente servicio de atención veterinaria general! Altamente recomendado para un cuidado integral y compasivo de mascotas 🥰😊',
                'rating' => 3.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            7 =>
            array(
                'id' => 8,
                'employee_id' => 24,
                'user_id' => 4,
                'review_msg' => '¡Gracias, Dra. Erica, por cuidar tan bien a mi bebé peludo durante su hospedaje! Tu experiencia y compasión me tranquilizaron. 💥🎊',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            8 =>
            array(
                'id' => 9,
                'employee_id' => 23,
                'user_id' => 3,
                'review_msg' => '¡Excelente y atento cuidado para mi amigo peludo - altamente recomendado! 🎉🎊',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            9 =>
            array(
                'id' => 10,
                'employee_id' => 20,
                'user_id' => 7,
                'review_msg' => '¡Los servicios de hospedaje del Dr. Daniel fueron sobresalientes! Mi mascota recibió la mejor atención médica y amor, haciendo que la experiencia fuera sin preocupaciones. 😊',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            10 =>
            array(
                'id' => 11,
                'employee_id' => 19,
                'user_id' => 8,
                'review_msg' => 'Recomiendo encarecidamente los servicios de hospedaje del Dr. Jorge - mi mascota estuvo en buenas manos, y el cuidado personalizado fue excepcional. ❤️🎊',
                'rating' => 3.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            11 =>
            array(
                'id' => 12,
                'employee_id' => 22,
                'user_id' => 9,
                'review_msg' => 'El nivel de atención proporcionado por el Dr. Erik es inigualable. Mi mascota recibe el mejor tratamiento y atención bajo su experiencia. 🥰 ❤️',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            12 =>
            array(
                'id' => 13,
                'employee_id' => 21,
                'user_id' => 11,
                'review_msg' => 'El enfoque gentil y el cálido comportamiento del Dr. José crean una atmósfera cómoda para las visitas de mi mascota. Agradecido de tener un veterinario tan cariñoso. 💥😍',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            13 =>
            array(
                'id' => 14,
                'employee_id' => 25,
                'user_id' => 2,
                'review_msg' => '¡Servicios de peluquería excepcionales! El peluquero prestó meticulosa atención a los detalles, dejando a mi mascota con un aspecto y sensación fantásticos. 😊😍',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            14 =>
            array(
                'id' => 15,
                'employee_id' => 26,
                'user_id' => 2,
                'review_msg' => 'Peluquero altamente capacitado que hizo que mi mascota se sintiera cómoda y mimada durante toda la sesión de peluquería. 💥💕🤩',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),

            15 =>
            array(
                'id' => 16,
                'employee_id' => 30,
                'user_id' => 3,
                'review_msg' => 'Impresionado por el talento del peluquero; el pelaje de mi mascota nunca se había visto tan suave y bien arreglado. 🥳🤩',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            16 =>
            array(
                'id' => 17,
                'employee_id' => 29,
                'user_id' => 4,
                'review_msg' => 'El peluquero fue paciente y comprensivo con las necesidades específicas de peluquería de mi mascota, proporcionando una experiencia maravillosa. 🥳🎊',
                'rating' => 3.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            17 =>
            array(
                'id' => 18,
                'employee_id' => 27,
                'user_id' => 5,
                'review_msg' => 'Un gran agradecimiento al peluquero por su profesionalismo y cuidado al hacer que mi mascota se vea adorable. 😊 🤩',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            18 =>
            array(
                'id' => 19,
                'employee_id' => 28,
                'user_id' => 6,
                'review_msg' => '¡Servicio de peluquería excepcional! Mi mascota disfrutó la sesión, y definitivamente volveré para más sesiones de peluquería. 🥳🎉',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            19 =>
            array(
                'id' => 20,
                'employee_id' => 34,
                'user_id' => 6,
                'review_msg' => '¡Servicios de entrenamiento sobresalientes! La experiencia y paciencia del entrenador ayudaron a mi mascota a aprender nuevas habilidades de manera efectiva. ❤️🎉',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            20 =>
            array(
                'id' => 21,
                'employee_id' => 35,
                'user_id' => 7,
                'review_msg' => '¡Entrenador altamente recomendado! Su enfoque personalizado hizo que las sesiones de entrenamiento fueran agradables y productivas. 🎊🥰',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            21 =>
            array(
                'id' => 22,
                'employee_id' => 36,
                'user_id' => 8,
                'review_msg' => 'Impresionado por el profesionalismo del entrenador y su capacidad para abordar las necesidades específicas de mi mascota. ❤️💥',
                'rating' => 3.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            22 =>
            array(
                'id' => 23,
                'employee_id' => 33,
                'user_id' => 10,
                'review_msg' => 'Excelentes resultados logrados con la guía del entrenador; el comportamiento de mi mascota mejoró significativamente. 🤩😊',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 12:20:51',
            ),
            23 =>
            array(
                'id' => 24,
                'employee_id' => 37,
                'user_id' => 2,
                'review_msg' => 'Las técnicas de refuerzo positivo del entrenador crearon un fuerte vínculo entre mi mascota y yo. 💕🥰',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),

            24 =>
            array(
                'id' => 25,
                'employee_id' => 39,
                'user_id' => 3,
                'review_msg' => '¡Absolutamente encantado con los servicios de paseo! El paseador fue atento y mi mascota regresó feliz y llena de energía. 💕😍',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 08:00:36',
            ),
            25 =>
            array(
                'id' => 26,
                'employee_id' => 43,
                'user_id' => 6,
                'review_msg' => 'El paseador mostró un cuidado y paciencia genuinos, haciendo que cada paseo fuera agradable para mi compañero peludo. 🥰',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            26 =>
            array(
                'id' => 27,
                'employee_id' => 44,
                'user_id' => 9,
                'review_msg' => 'Recomiendo altamente los servicios de paseo; la confiabilidad del paseador y su relación con mi mascota fueron impresionantes. 💥❤️',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            27 =>
            array(
                'id' => 28,
                'employee_id' => 40,
                'user_id' => 7,
                'review_msg' => 'Mi mascota y yo adoramos al paseador; siempre va más allá para asegurar una gran experiencia de paseo. 🥳💕',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            28 =>
            array(
                'id' => 29,
                'employee_id' => 41,
                'user_id' => 10,
                'review_msg' => 'Paseador confiable y responsable, me siento tranquilo sabiendo que mi mascota está en buenas manos durante los paseos. 🥰',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 06:20:30',
            ),
            29 =>
            array(
                'id' => 30,
                'employee_id' => 45,
                'user_id' => 8,
                'review_msg' => '¡Servicio de guardería excepcional! El cuidador de la guardería cuidó muy bien de mi mascota, proporcionando un ambiente seguro y divertido. 🥰 ❤️',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            30 =>
            array(
                'id' => 31,
                'employee_id' => 47,
                'user_id' => 9,
                'review_msg' => 'Estoy encantado con los servicios de guardería y la atención del cuidador a las necesidades de mi mascota. 💥💕',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            31 =>
            array(
                'id' => 32,
                'employee_id' => 50,
                'user_id' => 11,
                'review_msg' => '¡Cuidador de guardería altamente recomendado! Mi mascota siempre vuelve a casa feliz y bien cuidada. 😎💕',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 07:04:04',
            ),
            32 =>
            array(
                'id' => 33,
                'employee_id' => 49,
                'user_id' => 2,
                'review_msg' => 'El amor genuino del cuidador de la guardería por los animales se nota, haciendo que la experiencia de la guardería sea verdaderamente especial. 💕😍',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            33 =>
            array(
                'id' => 34,
                'employee_id' => 46,
                'user_id' => 3,
                'review_msg' => 'Servicio de guardería confiable y cariñoso; confío completamente en el cuidador de la guardería con mi amigo peludo. 😍💥',
                'rating' => 4.5,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),
            34 =>
            array(
                'id' => 35,
                'employee_id' => 48,
                'user_id' => 6,
                'review_msg' => 'Gracias al cuidador de la guardería, mi mascota espera con ansias cada visita - ¡una señal de excelentes servicios de guardería! 🥳🥰',
                'rating' => 3.0,
                'created_at' => '2023-08-26 05:15:31',
                'updated_at' => '2023-08-26 05:15:31',
            ),

            35 =>
            array(
                'id' => 36,
                'employee_id' => 17,
                'user_id' => 9,
                'review_msg' => 'Experiencia de hospedaje de primera clase - ¡mi mascota estuvo en buenas manos y regresó feliz! 😍',
                'rating' => 5.0,
                'created_at' => '2023-08-26 05:57:00',
                'updated_at' => '2023-08-26 05:57:00',
            ),
            36 =>
            array(
                'id' => 37,
                'employee_id' => 28,
                'user_id' => 8,
                'review_msg' => 'Tratamiento de pulgas y garrapatas efectivo y minucioso – ¡mi mascota está aliviada y refrescada! 🥰🥳',
                'rating' => 4.0,
                'created_at' => '2023-08-26 06:11:45',
                'updated_at' => '2023-08-26 06:11:52',
            ),
            37 =>
            array(
                'id' => 38,
                'employee_id' => 31,
                'user_id' => 10,
                'review_msg' => 'Una experiencia de mimos que dejó a mi mascota relajada y rejuvenecida. ¡Altamente recomendado! 💕😍',
                'rating' => 5.0,
                'created_at' => '2023-08-26 06:16:24',
                'updated_at' => '2023-08-26 06:16:24',
            ),
            38 =>
            array(
                'id' => 39,
                'employee_id' => 19,
                'user_id' => 6,
                'review_msg' => 'Equipo altamente capacitado que proporciona un cuidado reproductivo excepcional para mi mascota. Un verdadero salvavidas ❤️💥',
                'rating' => 4.0,
                'created_at' => '2023-08-26 06:29:56',
                'updated_at' => '2023-08-26 06:30:53',
            ),
            39 =>
            array(
                'id' => 40,
                'employee_id' => 20,
                'user_id' => 6,
                'review_msg' => 'Cuidado veterinario excepcional que realmente entiende y se preocupa por las necesidades de mi mascota. 😍🧑‍⚕️',
                'rating' => 5.0,
                'created_at' => '2023-08-26 06:50:31',
                'updated_at' => '2023-08-26 06:50:31',
            ),
            40 =>
            array(
                'id' => 41,
                'employee_id' => 13,
                'user_id' => 3,
                'review_msg' => '¡Cuidado y amor excepcionales para mi amigo peludo durante su estancia. No podría estar más feliz! 🎉🥳❤️',
                'rating' => 4.0,
                'created_at' => '2023-08-26 07:57:40',
                'updated_at' => '2023-08-26 07:57:40',
            ),
            41 =>
            array(
                'id' => 42,
                'employee_id' => 35,
                'user_id' => 3,
                'review_msg' => 'Entrenamiento transformador que sacó lo mejor de mi querida mascota. 💕😎',
                'rating' => 4.0,
                'created_at' => '2023-08-26 07:59:09',
                'updated_at' => '2023-08-26 07:59:09',
            ),
            42 =>
            array(
                'id' => 43,
                'employee_id' => 44,
                'user_id' => 3,
                'review_msg' => 'Servicio excepcional de paseo de perros - ¡mi mascota espera con entusiasmo sus paseos! 🥰🤩',
                'rating' => 5.0,
                'created_at' => '2023-08-26 07:59:43',
                'updated_at' => '2023-08-26 07:59:43',
            ),
            43 =>
            array(
                'id' => 44,
                'employee_id' => 25,
                'user_id' => 3,
                'review_msg' => 'Servicio de peluquería impecable que deja a mi mascota con un aspecto y sensación fantásticos. 😎🤓💯',
                'rating' => 4.0,
                'created_at' => '2023-08-26 08:01:59',
                'updated_at' => '2023-08-26 08:01:59',
            ),
            44 =>
            array(
                'id' => 45,
                'employee_id' => 13,
                'user_id' => 11,
                'review_msg' => 'Un fantástico hogar lejos de casa para mi mascota. Servicio profesional y cariñoso. 💕😍',
                'rating' => 5.0,
                'created_at' => '2023-08-26 08:06:08',
                'updated_at' => '2023-08-26 08:06:08',
            ),
            45 =>
            array(
                'id' => 46,
                'employee_id' => 33,
                'user_id' => 11,
                'review_msg' => 'Servicio de entrenamiento altamente efectivo que forjó un fuerte vínculo con mi mascota. 🤓😎🤗',
                'rating' => 5.0,
                'created_at' => '2023-08-26 08:09:05',
                'updated_at' => '2023-08-26 12:19:27',
            ),

            46 =>
            array(
                'id' => 47,
                'employee_id' => 45,
                'user_id' => 11,
                'review_msg' => 'Proveedor de cuidado de mascotas excepcional, ¡mi amigo peludo siempre vuelve a casa feliz! 😍💕🤗',
                'rating' => 5.0,
                'created_at' => '2023-08-26 08:12:50',
                'updated_at' => '2023-08-26 12:55:16',
            ),
            47 =>
            array(
                'id' => 48,
                'employee_id' => 26,
                'user_id' => 11,
                'review_msg' => 'Peluqueros altamente capacitados que miman a mi mascota con cuidado y estilo. 🤓🤩',
                'rating' => 5.0,
                'created_at' => '2023-08-26 08:15:14',
                'updated_at' => '2023-08-26 08:15:14',
            ),
            48 =>
            array(
                'id' => 49,
                'employee_id' => 40,
                'user_id' => 11,
                'review_msg' => '¡Un salvavidas! Estos paseadores han transformado positivamente la energía y el comportamiento de mi mascota. 🧑‍💼🥰',
                'rating' => 5.0,
                'created_at' => '2023-08-26 08:18:50',
                'updated_at' => '2023-08-26 08:18:50',
            ),
            49 =>
            array(
                'id' => 50,
                'employee_id' => 38,
                'user_id' => 10,
                'review_msg' => 'Entrenadores excepcionales que cambiaron el comportamiento de mi mascota con cuidado. 😎😍🥰',
                'rating' => 4.0,
                'created_at' => '2023-08-26 09:10:14',
                'updated_at' => '2023-08-26 12:54:14',
            ),
            50 =>
            array(
                'id' => 51,
                'employee_id' => 41,
                'user_id' => 9,
                'review_msg' => 'Paseadores profesionales y dedicados que realmente entienden y conectan con las necesidades de mi mascota. 🤩🎉',
                'rating' => 5.0,
                'created_at' => '2023-08-26 09:11:35',
                'updated_at' => '2023-08-26 09:11:35',
            ),
            51 =>
            array(
                'id' => 52,
                'employee_id' => 27,
                'user_id' => 9,
                'review_msg' => 'Experiencia de peluquería excepcional que mantiene la cola de mi mascota moviéndose de alegría. 😊🤗',
                'rating' => 5.0,
                'created_at' => '2023-08-26 09:19:22',
                'updated_at' => '2023-08-26 09:19:22',
            ),
            52 =>
            array(
                'id' => 53,
                'employee_id' => 40,
                'user_id' => 9,
                'review_msg' => 'Un excelente paseador que trata a mi mascota con cuidado y entusiasmo. ❤️🥳',
                'rating' => 4.0,
                'created_at' => '2023-08-26 09:26:36',
                'updated_at' => '2023-08-26 09:26:36',
            ),
            53 =>
            array(
                'id' => 54,
                'employee_id' => 24,
                'user_id' => 9,
                'review_msg' => 'Un servicio veterinario de primera clase que prioriza el bienestar y la salud de mi mascota. 💕💯',
                'rating' => 4.0,
                'created_at' => '2023-08-26 09:28:10',
                'updated_at' => '2023-08-26 09:28:10',
            ),
            54 =>
            array(
                'id' => 55,
                'employee_id' => 27,
                'user_id' => 8,
                'review_msg' => 'Un servicio de peluquería que constantemente ofrece perfección, adaptado a mi mascota. 😊😍',
                'rating' => 5.0,
                'created_at' => '2023-08-26 09:42:41',
                'updated_at' => '2023-08-26 12:52:54',
            ),
            55 =>
            array(
                'id' => 56,
                'employee_id' => 18,
                'user_id' => 7,
                'review_msg' => 'Atención veterinaria confiable y compasiva en la que siempre puedo confiar. 🧑‍⚕️🤗',
                'rating' => 4.0,
                'created_at' => '2023-08-26 09:49:17',
                'updated_at' => '2023-08-26 09:49:17',
            ),
            56 =>
            array(
                'id' => 57,
                'employee_id' => 14,
                'user_id' => 6,
                'review_msg' => 'Instalaciones impecables y personal atento. ¡El nuevo lugar favorito de mi mascota para quedarse! 😊💯',
                'rating' => 4.0,
                'created_at' => '2023-08-26 10:14:47',
                'updated_at' => '2023-08-26 10:14:47',
            ),
            57 =>
            array(
                'id' => 58,
                'employee_id' => 25,
                'user_id' => 5,
                'review_msg' => 'Un servicio de peluquería que constantemente ofrece perfección. 🐈🥰',
                'rating' => 4.0,
                'created_at' => '2023-08-26 10:34:26',
                'updated_at' => '2023-08-26 10:34:34',
            ),

            58 =>
            array(
                'id' => 59,
                'employee_id' => 47,
                'user_id' => 4,
                'review_msg' => 'Confiable y atento; a mi mascota le encanta su tiempo en esta guardería. 😊🤗',
                'rating' => 5.0,
                'created_at' => '2023-08-26 10:41:14',
                'updated_at' => '2023-08-26 10:41:14',
            ),
            59 =>
            array(
                'id' => 60,
                'employee_id' => 21,
                'user_id' => 3,
                'review_msg' => 'Atención de cinco estrellas de veterinarios conocedores que tratan a mi mascota como familia. 🧑‍⚕️💯',
                'rating' => 4.0,
                'created_at' => '2023-08-26 11:04:16',
                'updated_at' => '2023-08-26 11:04:16',
            ),
            60 =>
            array(
                'id' => 61,
                'employee_id' => 23,
                'user_id' => 10,
                'review_msg' => 'Atención veterinaria confiable y compasiva en la que siempre puedo confiar. 💯',
                'rating' => 4.0,
                'created_at' => '2023-08-26 11:45:20',
                'updated_at' => '2023-08-26 11:45:20',
            ),
            61 =>
            array(
                'id' => 62,
                'employee_id' => 48,
                'user_id' => 11,
                'review_msg' => 'Una guardería fantástica con personal cariñoso, el segundo hogar de mi mascota. 🤗',
                'rating' => 5.0,
                'created_at' => '2023-08-26 11:49:40',
                'updated_at' => '2023-08-26 11:49:40',
            ),
            62 =>
            array(
                'id' => 63,
                'employee_id' => 25,
                'user_id' => 7,
                'review_msg' => 'Mimos de primera clase que transforman a mi amigo peludo en una obra de arte. 😊☺️',
                'rating' => 4.0,
                'created_at' => '2023-08-26 11:51:56',
                'updated_at' => '2023-08-26 11:51:56',
            ),
            63 =>
            array(
                'id' => 64,
                'employee_id' => 47,
                'user_id' => 5,
                'review_msg' => '¡Cuidado confiable y amoroso que hace que mi mascota mueva la cola de alegría! 😊🥰👏',
                'rating' => 5.0,
                'created_at' => '2023-08-26 11:55:01',
                'updated_at' => '2023-08-26 12:47:48',
            ),
            64 =>
            array(
                'id' => 65,
                'employee_id' => 34,
                'user_id' => 8,
                'review_msg' => 'Entrenamiento profesional que marcó una diferencia notable en la obediencia de mi mascota. 😍🤩',
                'rating' => 5.0,
                'created_at' => '2023-08-26 11:59:24',
                'updated_at' => '2023-08-26 11:59:24',
            ),
            65 =>
            array(
                'id' => 66,
                'employee_id' => 27,
                'user_id' => 4,
                'review_msg' => 'Mimos de primera clase que transforman a mi amigo peludo en una obra de arte. 🤗😊',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:01:08',
                'updated_at' => '2023-08-26 12:01:08',
            ),
            66 =>
            array(
                'id' => 67,
                'employee_id' => 14,
                'user_id' => 9,
                'review_msg' => 'Servicio de hospedaje confiable y maravilloso. Mi mascota fue tratada como de la familia. 😍🤗👏',
                'rating' => 5.0,
                'created_at' => '2023-08-26 12:05:16',
                'updated_at' => '2023-08-26 12:53:31',
            ),
            67 =>
            array(
                'id' => 68,
                'employee_id' => 23,
                'user_id' => 7,
                'review_msg' => 'Servicio de cuidado de mascotas sobresaliente que va más allá en todos los aspectos. 🧑‍⚕️😍',
                'rating' => 5.0,
                'created_at' => '2023-08-26 12:06:29',
                'updated_at' => '2023-08-26 12:06:29',
            ),
            68 =>
            array(
                'id' => 69,
                'employee_id' => 35,
                'user_id' => 6,
                'review_msg' => 'Increíble experiencia de entrenamiento que nos hizo más felices tanto a mí como a mi mascota. 🤗😊',
                'rating' => 5.0,
                'created_at' => '2023-08-26 12:07:27',
                'updated_at' => '2023-08-26 12:07:27',
            ),
            69 =>
            array(
                'id' => 70,
                'employee_id' => 14,
                'user_id' => 5,
                'review_msg' => 'Servicio de hospedaje excepcional que le dio a mi mascota un hogar cómodo lejos de casa. 😍',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:10:52',
                'updated_at' => '2023-08-26 12:10:52',
            ),
            70 =>
            array(
                'id' => 71,
                'employee_id' => 42,
                'user_id' => 4,
                'review_msg' => 'Confiable y seguro - los paseos de mi mascota son siempre una alegría. ☺️',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:12:09',
                'updated_at' => '2023-08-26 12:12:09',
            ),

            71 =>
            array(
                'id' => 72,
                'employee_id' => 15,
                'user_id' => 3,
                'review_msg' => 'Confiable y atento: mi mascota estuvo en excelentes manos durante su estancia. 🥰🤩',
                'rating' => 5.0,
                'created_at' => '2023-08-26 12:13:04',
                'updated_at' => '2023-08-26 12:13:04',
            ),
            72 =>
            array(
                'id' => 73,
                'employee_id' => 35,
                'user_id' => 11,
                'review_msg' => 'Un entrenador excepcional que entiende y conecta perfectamente con mi mascota. 🥰',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:15:50',
                'updated_at' => '2023-08-26 12:15:50',
            ),
            73 =>
            array(
                'id' => 74,
                'employee_id' => 33,
                'user_id' => 9,
                'review_msg' => 'Servicio de entrenamiento sobresaliente que ha transformado positivamente el comportamiento de mi mascota. 🤩😍',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:30:40',
                'updated_at' => '2023-08-26 12:30:40',
            ),
            74 =>
            array(
                'id' => 75,
                'employee_id' => 33,
                'user_id' => 8,
                'review_msg' => 'Entrenador altamente capacitado que saca lo mejor de mi amigo peludo. 😊🥰',
                'rating' => 5.0,
                'created_at' => '2023-08-26 12:33:12',
                'updated_at' => '2023-08-26 12:33:12',
            ),
            75 =>
            array(
                'id' => 76,
                'employee_id' => 33,
                'user_id' => 7,
                'review_msg' => 'Experiencia de entrenamiento de cinco estrellas que ha marcado una diferencia notable para nosotros. 🤗👏',
                'rating' => 5.0,
                'created_at' => '2023-08-26 12:35:21',
                'updated_at' => '2023-08-26 12:50:52',
            ),
            76 =>
            array(
                'id' => 77,
                'employee_id' => 33,
                'user_id' => 6,
                'review_msg' => 'Confiable, paciente y efectivo - el progreso de mi mascota con este entrenador es fenomenal. 💕😍',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:39:22',
                'updated_at' => '2023-08-26 12:39:22',
            ),
            77 =>
            array(
                'id' => 78,
                'employee_id' => 33,
                'user_id' => 2,
                'review_msg' => 'Un entrenador dedicado que ha traído una disciplina y alegría notables a mi mascota. 👏🥰🤩🤗',
                'rating' => 5.0,
                'created_at' => '2023-08-26 12:41:08',
                'updated_at' => '2023-08-26 12:41:08',
            ),
            78 =>
            array(
                'id' => 79,
                'employee_id' => 27,
                'user_id' => 3,
                'review_msg' => 'Experiencia de peluquería de cinco estrellas que deja a las mascotas luciendo y sintiéndose lo mejor posible. 👏🥰',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:43:49',
                'updated_at' => '2023-08-26 12:43:49',
            ),
            79 =>
            array(
                'id' => 80,
                'employee_id' => 13,
                'user_id' => 4,
                'review_msg' => 'Cuidado confiable y compasivo que proporcionó una experiencia de hospedaje sin estrés. 🤗🤩',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:45:08',
                'updated_at' => '2023-08-26 12:45:08',
            ),
            80 =>
            array(
                'id' => 81,
                'employee_id' => 42,
                'user_id' => 6,
                'review_msg' => 'Confiable, atento y hábil para hacer de cada paseo una aventura. 🥰🤗',
                'rating' => 4.0,
                'created_at' => '2023-08-26 12:49:18',
                'updated_at' => '2023-08-26 12:49:18',
            ),

        ));
    }
}
