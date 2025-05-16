<?php

namespace Modules\Blog\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Blog\Models\Blog;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class BlogTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('IS_DUMMY_DATA')) {
            $data = [


                0 =>
                array(
                    'id' => 1,
                    'name' => 'Cómo dar pastillas a los perros',
                    'platform_id' => 1,
                    'description' => '<p>Dar medicamentos a los perros puede ser un desafío, pero con algunos consejos útiles, puedes hacer que el proceso sea más agradable tanto para ti como para tu amigo peludo.</p><p><strong>Bolsillos para pastillas</strong></p><p>Los bolsillos para pastillas son golosinas suaves con un hueco para ocultar el medicamento de tu mascota. Si tu perro puede tomar medicamentos con comida, prueba usar un bolsillo para pastillas o envolver la pastilla en comida blanda como queso. Esto facilita que la pastilla pase al sistema de tu perro. Ten en cuenta que esto funciona mejor con perros que devoran golosinas sin masticar. Si tu perro mastica las golosinas, podría descubrir el medicamento, dificultando su administración la próxima vez. Si tu perro tiene sensibilidades alimentarias o alergias, consulta a tu veterinario antes de usar bolsillos para pastillas.</p><p><strong>Medicamentos compuestos</strong></p><p>Algunos medicamentos vienen en forma de tabletas masticables o compuestos con sabor, lo que puede ser más atractivo para los perros que tienen dificultades para tragar pastillas. Sin embargo, estas opciones pueden ser más costosas y algunos medicamentos pueden no ser aptos para compuestos debido a problemas de eficacia. No todas las farmacias preparan medicamentos compuestos, por lo que debes pedir recomendaciones a tu veterinario.</p><p><strong>Dispositivo para pastillas</strong></p><p>Administrar pastillas a tu perro puede ser arriesgado, ya que podrías sufrir mordeduras accidentales si tus dedos se acercan a sus dientes. Los dispositivos para pastillas ofrecen una alternativa más segura al permitir colocar el medicamento en la boca del perro sin exponer tus dedos al peligro. Para asegurar el éxito, coloca la pastilla detrás de la protuberancia en la lengua de tu perro, cierra sus mandíbulas y acaricia suavemente su garganta hacia abajo para fomentar la deglución.</p><p><strong>Pide ayuda</strong></p><p>Intentar inmovilizar a tu perro mientras le das un medicamento puede ser complicado. Si es posible, pide a un amigo o familiar que lo sujete, permitiéndote concentrarte completamente en la tarea.</p><p>Recuerda que la salud y el bienestar de tu perro son lo más importante. Siempre sigue las instrucciones de tu veterinario y busca su consejo si tienes dudas o preguntas.</p>',
                    'tags' => 'Animal, Cuidado, ProblemasDeSalud',
                    'status' => 1,
                    'url' => 'https://www.youtube.com/watch?v=S5YPHdzRv4I',
                    'video' => 'https://www.youtube.com/watch?v=S5YPHdzRv4I',
                    'blog_image' => public_path('/dummy-images/blog-image/feedpillsdogs.png'),
                    'created_by' => NULL,
                    'updated_by' => 1,
                    'deleted_by' => NULL,
                    'created_at' => '2023-08-29 06:54:50',
                    'updated_at' => '2023-09-13 07:06:19',
                    'deleted_at' => NULL,
                ),
                1 =>
                array(
                    'id' => 2,
                    'name' => 'Cómo cuidar a los animales callejeros durante el monzón',
                    'platform_id' => 1,
                    'description' => '<p>Como personas responsables, es nuestro deber cuidar de estos animales. Exploremos algunas formas en las que podemos ayudarlos durante la temporada de monzones.</p><p><strong>Ofrecer refugio en casa o en tu comunidad</strong></p><p>Busca permiso de las autoridades relevantes en tu comunidad para permitir que los animales callejeros encuentren refugio en tu complejo de apartamentos. Si vives en una casa, considera asignar un área seca en tu garaje o porche para que se resguarden y estén a salvo de la lluvia.</p><p><strong>Proveer refugios temporales</strong></p><p>Crear refugios permanentes para animales en ciudades superpobladas puede ser complicado. Sin embargo, puedes instalar refugios temporales usando tiendas, madera o láminas de metal en jardines públicos, áreas abiertas o terrenos. Asegúrate de que el lugar elegido sea higiénico y seguro para los animales, y que no sea propenso a inundarse.</p><p><strong>Proporcionar comida, agua y suministros médicos</strong></p><p>Es crucial ofrecerles comida adecuada, agua limpia y atención médica. Anima a tu comunidad a turnarse para proporcionar comida fresca y agua limpia. Mantén un botiquín de primeros auxilios y una reserva de suministros médicos esenciales para tratar animales enfermos. Usa periódicos viejos, ropa o sábanas para que los animales puedan mantenerse calientes y secos. Esto ayuda a mantener su salud y prevenir enfermedades.</p><p><strong>Ayudar a animales heridos</strong></p><p>Durante las fuertes lluvias, los animales callejeros están en mayor riesgo de sufrir accidentes o contraer infecciones o enfermedades graves debido a desagües obstruidos y agua estancada. Si encuentras un animal herido, contacta al hospital veterinario o clínica más cercana para obtener ayuda.</p><p><strong>Apoyar refugios locales</strong></p><p>Los refugios de animales enfrentan desafíos adicionales durante el monzón. Requieren más comida, agua y suministros médicos para satisfacer la creciente demanda. Considera visitar refugios locales para entender sus necesidades y ofrecer apoyo en consecuencia. Si no puedes proporcionar asistencia financiera, puedes contribuir donando tu tiempo o artículos como ropa vieja, sábanas o muebles.</p><p><strong>Buscar ayuda de expertos y ONGs</strong></p><p>En situaciones donde no puedas aliviar el sufrimiento de un animal callejero, contacta a las autoridades adecuadas. Busca líneas de ayuda animal o contacta ONGs y refugios cercanos para obtener asistencia.</p><p>Recordemos que los animales callejeros merecen el mismo amor y cuidado que brindamos a nuestras mascotas. Esta temporada de monzones, extendamos una mano amiga y hagamos la diferencia en sus vidas.</p>',
                    'tags' => 'PerroCallejero, Cuidado, Animal',
                    'status' => 1,
                    'url' => 'https://www.youtube.com/watch?v=ls7m_w5NIRE',
                    'video' => 'https://www.youtube.com/watch?v=ls7m_w5NIRE',
                    'blog_image' => public_path('/dummy-images/blog-image/takecare-of-stray-animals-monsoon.png'),
                    'created_by' => NULL,
                    'updated_by' => 1,
                    'deleted_by' => NULL,
                    'created_at' => '2023-09-02 06:54:50',
                    'updated_at' => '2023-09-13 07:14:04',
                    'deleted_at' => NULL,
                ),


                2 =>
                array(
                    'id' => 3,
                    'name' => 'Extraviado vs. Salvaje: Qué hacer cuando te encuentras con un gato salvaje',
                    'platform_id' => 1,
                    'description' => '<p>Los gatos callejeros son gatos domésticos que se han perdido o han sido abandonados, diferenciándose de los gatos salvajes. Al no haber recibido la socialización vital, los gatos salvajes se comportan más como animales salvajes en comparación con los gatos domésticos dóciles. Aquí tienes una guía rápida para distinguir entre los diferentes tipos de gatos errantes que nos rodean en nuestras vidas urbanas.</p><p><strong>Gato callejero amistoso</strong></p><p>Fíjate en si tienen collar, ya que podrían pertenecer a un vecino cercano o estar perdidos o abandonados, anhelando cuidado y atención.</p><p><strong>Gato comunitario</strong></p><p>A diferencia de los gatos con dueños tradicionales, los gatos comunitarios son cuidados por la comunidad en general. Aunque pueden mostrar cierto grado de amistad, su nivel de socialización puede variar.</p><p><strong>Salvaje amistoso</strong></p><p>A través de una socialización mínima facilitada por la alimentación regular, los gatos salvajes amistosos pueden mostrar cierta confianza hacia la persona que les proporciona sustento. Sin embargo, no han alcanzado un nivel de socialización que permita el contacto físico, como las caricias.</p><p><strong>Salvaje</strong></p><p>Los gatos verdaderamente salvajes carecen de cualquier forma de socialización. Se mantienen siempre vigilantes y profundamente desconfiados de la presencia humana. Al menor indicio de personas, huyen rápidamente.</p><p><strong>Cómo ayudar a los gatos salvajes en tu área</strong></p><p>Es un hecho innegable que los gatos callejeros y salvajes enfrentan vidas difíciles mientras navegan por los peligros del exterior. Tristemente, muchos gatos salvajes no viven más de dos años, en contraste con la longevidad de los gatos domésticos, que pueden superar los 20 años. Como individuos compasivos, podemos tomar los siguientes pasos para aliviar su sufrimiento:</p><p><strong>Esterilización y castración</strong></p><p>El primer y más crucial paso para ayudar tanto a los gatos de interior como a los comunitarios es promover y priorizar la esterilización y castración. Esta medida esencial ayuda a controlar la población y prevenir más sufrimiento.</p><p><strong>Educación y defensa</strong></p><p>Educa a otros sobre la importancia de esterilizar, castrar y vacunar a los gatos. Al crear conciencia, podemos fomentar una comunidad que participe activamente en el cuidado responsable de las mascotas. Aboga por el patrocinio local de clínicas de esterilización a bajo costo para garantizar la accesibilidad para todos.</p><p><strong>Necesidades básicas</strong></p><p>Proporciona agua limpia y fresca a los gatos salvajes en tu área. Un pequeño acto como este puede marcar una gran diferencia en su bienestar. Al alimentarlos, ofrece porciones modestas en las mañanas.</p><p><strong>Iniciativas de refugio</strong></p><p>Considera construir una caseta para gatos o invertir en un refugio para gatos resistente a cualquier clima. Estos refugios ofrecen un lugar seguro tanto para los gatos callejeros como para los salvajes, protegiéndolos de las condiciones climáticas adversas.</p><p>Busca orientación en refugios locales: Si encuentras un gato o gatito errante y no sabes cómo ayudar, comunícate con un refugio local. Ellos pueden brindarte valiosos consejos y apoyo, guiándote en el proceso de ayudar eficazmente a estos felinos.</p>',
                    'tags' => 'GatoCallejero, GatoSalvaje',
                    'status' => 1,
                    'url' => 'https://www.youtube.com/watch?v=idywqpJsvas',
                    'video' => 'https://www.youtube.com/watch?v=idywqpJsvas',
                    'blog_image' => public_path('/dummy-images/blog-image/stray-feral-cat.png'),
                    'created_by' => NULL,
                    'updated_by' => 1,
                    'deleted_by' => NULL,
                    'created_at' => '2023-08-14 06:54:50',
                    'updated_at' => '2023-09-13 07:31:10',
                    'deleted_at' => NULL,
                ),
                3 =>
                array(
                    'id' => 4,
                    'name' => 'Qué hacer cuando encuentras un animal callejero herido',
                    'platform_id' => 1,
                    'description' => '<p>Encontrarse con un animal callejero herido puede ser una situación angustiante, pero hay pasos que puedes tomar para garantizar su seguridad y bienestar. Aquí te mostramos cómo evaluar la situación, proporcionar cuidados temporales y buscar ayuda adecuada, marcando una diferencia significativa en la vida del animal herido.</p><p><strong>Evalúa la situación</strong></p><p>Cuando te encuentres con un animal herido, es importante evaluar la situación antes de actuar. Esto garantizará tu seguridad y la del animal.</p><p><strong>Contacta con el refugio local</strong></p><p>Si crees que el animal herido podría representar una amenaza inmediata para las personas u otros animales, contacta de inmediato con el refugio local. Ellos tendrán la experiencia y recursos necesarios para manejar la situación.</p><p><strong>Acércate con precaución</strong></p><p>Acércate al animal herido de forma lenta y tranquila, evitando el contacto visual directo y los movimientos bruscos. Habla en un tono bajo, suave y tranquilizador para ayudar a calmar al animal.</p><p><strong>Evalúa las heridas</strong></p><p>Observa las heridas del animal desde una distancia segura. Si las heridas parecen graves o potencialmente mortales, es mejor esperar a que lleguen los profesionales. Toma nota de las heridas y proporciona esta información a las autoridades al llegar.</p><p><strong>Contacta con organizaciones de rescate locales</strong></p><p>Comunícate con organizaciones de rescate animal en tu área. Ellos te orientarán sobre cómo proceder y pueden contar con recursos para ayudar en el cuidado y rehabilitación del animal herido.</p><p><strong>Permanece con el animal</strong></p><p>Si es posible, permanece con el animal hasta que llegue la ayuda. Tu presencia puede ser reconfortante y tranquilizadora para el animal en un momento de angustia.</p><p>Encontrar un animal callejero herido requiere compasión y acción rápida. Siguiendo estos pasos, puedes ayudar a aliviar el miedo y el sufrimiento del animal mientras esperas la asistencia profesional. Tus esfuerzos pueden marcar la diferencia en las posibilidades de supervivencia y recuperación del animal.</p>',
                    'tags' => 'PerroCallejero, Herida, Cuidado, Animal',
                    'status' => 1,
                    'url' => 'https://www.youtube.com/watch?v=Mqiq5Wra_IU',
                    'video' => 'https://www.youtube.com/watch?v=Mqiq5Wra_IU',
                    'blog_image' => public_path('/dummy-images/blog-image/injured-stray-animal.png'),
                    'created_by' => NULL,
                    'updated_by' => 1,
                    'deleted_by' => NULL,
                    'created_at' => '2023-09-12 06:54:50',
                    'updated_at' => '2023-09-13 07:32:58',
                    'deleted_at' => NULL,
                ),
                4 =>
                array(
                    'id' => 5,
                    'name' => 'Cómo mantener a tu mascota libre de pulgas y garrapatas',
                    'platform_id' => 1,
                    'description' => '<p>Como dueños de mascotas, queremos que nuestros amigos peludos sean felices y saludables, y parte de eso es protegerlos de pulgas y garrapatas. Estos pequeños parásitos pueden causar muchas molestias y transmitir enfermedades. Por suerte, hay muchas formas de prevenir que las pulgas y garrapatas infesten a tu mascota. Aquí tienes algunos consejos para mantener a tu mascota libre de estos parásitos.</p><p><strong>Usa un medicamento preventivo</strong></p><p>Existen diferentes tipos de medicamentos en el mercado, incluyendo tratamientos tópicos, collares y medicamentos orales. Consulta con tu veterinario para determinar cuál es el más adecuado según la edad, peso y salud general de tu mascota.</p><p><strong>No uses medicamentos para perros en gatos</strong></p><p>No uses preventivos para pulgas y garrapatas diseñados para perros en gatos, y viceversa. Algunos productos contienen ingredientes dañinos que pueden causar reacciones graves. Asegúrate de seguir las indicaciones de las etiquetas.</p><p><strong>Grooming regular</strong></p><p>Dedicar tiempo a asear a tu mascota no solo refuerza el vínculo, sino que también es una oportunidad para detectar parásitos externos escondidos en su pelaje.</p><p><strong>Usa remedios naturales</strong></p><p>Existen remedios naturales, como aceites esenciales de cedro, lavanda y menta, que ayudan a repeler pulgas y garrapatas. Sin embargo, ten cuidado, ya que algunos aceites pueden ser tóxicos si no se diluyen adecuadamente. Siempre consulta a tu veterinario antes de usar estos remedios.</p>',
                    'tags' => 'Mascota, Cuidado, Animal, Protección',
                    'status' => 1,
                    'url' => 'https://www.youtube.com/watch?v=eYR4jwqnOBo',
                    'video' => 'https://www.youtube.com/watch?v=eYR4jwqnOBo',
                    'blog_image' => public_path('/dummy-images/blog-image/pet-flea-tick-free.png'),
                    'created_by' => NULL,
                    'updated_by' => 1,
                    'deleted_by' => NULL,
                    'created_at' => '2023-10-01 06:54:50',
                    'updated_at' => '2023-10-02 08:32:58',
                    'deleted_at' => NULL,
                ),

                5 =>
                array(
                    'id' => 6,
                    'name' => 'Cómo Mantener a tus Conejos Frescos en Verano',
                    'platform_id' => 1,
                    'description' => '<p>El verano ha llegado y, mientras muchos de nosotros disfrutamos del maravilloso clima cálido de la temporada, nuestras mascotas pueden no estar disfrutándolo tanto.</p><p>En particular, los conejos son vulnerables al golpe de calor y dependen de sus dueños para proporcionarles condiciones más frescas durante los meses de verano. Los conejos silvestres se refugian bajo tierra o se esconden bajo arbustos y arbustos para mantenerse frescos, así que aquí analizamos cómo, como dueños de mascotas, podemos ayudar a mantener a los conejos frescos en climas cálidos.</p><p><strong>Ofrece a tu Conejo Agua Fresca y Fría</strong></p><p>Una fuente fresca de agua siempre es esencial y debe reponerse a intervalos regulares durante el día en los meses de verano. Una combinación de tazones y botellas de agua le dará a tu conejo acceso a suficientes líquidos, y es posible que incluso disfruten acostándose en los tazones cuando hace mucho calor.</p><p>Añadir cubos de hielo al tazón de agua les brindará un alivio refrescante, al igual que ofrecer un suministro de verduras frescas. Estas contienen naturalmente una gran cantidad de agua y tu conejo disfrutará comiéndolas durante esos largos días calurosos mientras se mantiene hidratado al mismo tiempo.</p><p><strong>Mantén a las Moscas Lejos</strong></p><p>¡Las moscas son quizás lo más molesto del verano! Son criaturas persistentes que nos vuelven locos, y desafortunadamente, tienen el mismo efecto en nuestros conejos.</p><p>Las moscas pueden causar daño serio si ponen huevos en tu conejo, por lo que mantenerlas alejadas de la jaula de tu conejo es vital. La higiene meticulosa es esencial, y solo la limpieza regular de la cama y la arena de tu mascota ayudará a mantener a estas criaturas alejadas. Si ves moscas alrededor de la jaula de tu conejo, considera colgar papel atrapamoscas cerca (fuera del alcance de tu conejo) y revisa regularmente a tu mascota para detectar signos de infestación. Mantener a tu conejo bien arreglado y eliminar el exceso de pelo no solo ayudará a mantenerlo más fresco en el calor, sino que también les dará menos lugares a las moscas para poner sus huevos.</p><p><strong>Cómo Detectar los Síntomas del Golpe de Calor en los Conejos</strong></p><p>El golpe de calor en los conejos puede ser fatal, al igual que en otros animales pequeños. Si puedes detectar los síntomas del golpe de calor en las primeras etapas, tendrás tiempo para revertir los efectos. Los principales síntomas a los que debes prestar atención son:
        </p><ul><li>Respiración rápida y superficial
        </li><li>Humedad alrededor de la nariz
        </li><li>Respiración rápida por la boca abierta mientras echan la cabeza hacia atrás
        </li><li>Orejas calientes</li></ul><p>Si te preocupa que tu conejo tenga un golpe de calor, llévalo de inmediato al interior a una habitación fresca y bien ventilada. No lo sumerjas en agua fría, ya que esto puede enviarlo a un estado de shock, pero sí aplica una compresa fría en sus orejas. Ofrécele mucha agua fresca y fría y mantenlo tranquilo. Si no parece mejorar en poco tiempo, llévalo a tu veterinario local de inmediato.
        </p>',
                    'tags' => 'Conejos, Cuidado, ProtecciónVerano',
                    'status' => 1,
                    'url' => 'https://www.youtube.com/watch?v=YIAwciekAFI',
                    'video' => 'https://www.youtube.com/watch?v=YIAwciekAFI',
                    'blog_image' => public_path('/dummy-images/blog-image/rabbits-cool-summer.png'),
                    'created_by' => NULL,
                    'updated_by' => 1,
                    'deleted_by' => NULL,
                    'created_at' => '2023-09-04 06:54:50',
                    'updated_at' => '2023-09-13 07:38:52',
                    'deleted_at' => NULL,
                ),
                6 =>
                array(
                    'id' => 7,
                    'name' => 'Cómo Cuidar de tu Tortuga',
                    'platform_id' => 1,
                    'description' => '<p>Una tortuga es una mascota maravillosa, pero como todas las criaturas exóticas, tienen necesidades específicas y algunos requisitos básicos que deben cumplirse para mantenerlas felices y saludables.</p><p>Si se cuida adecuadamente, una tortuga tendrá una vida muy larga, con muchas viviendo más de 100 años. Por eso, antes de llevar a una de estas encantadoras mascotas a tu hogar, debes considerar que probablemente será un compromiso de por vida y que bien podrían sobrevivirte.</p><p>Al decidir si una tortuga es adecuada para ti, debes pensar en dónde vivirá, qué condiciones necesitará y cuánto tiempo y atención deberás proporcionarle.</p><p><strong>¿Qué condiciones necesita la tortuga?</strong></p><p>En la medida de lo posible, debes intentar imitar el entorno natural en el que viviría tu tortuga. También ten en cuenta que si el recinto de la tortuga está demasiado húmedo, puede fomentar el crecimiento de hongos, lo que puede afectar su salud. Debes incluir un área de descanso en el recinto, así como algo de sombra para que tu tortuga pueda moverse y regular su temperatura corporal según sea necesario.</p><p><strong>¿Qué debo alimentar a mi tortuga?</strong></p><p>Nuestro rango de tazones para alimentación tiene un efecto natural, son fáciles de limpiar y no son porosos. Están diseñados para integrarse en el entorno, de modo que tu tortuga se sienta como si estuviera buscando comida en la naturaleza. Asegúrate de que tu tortuga siempre tenga acceso a agua poco profunda para beber y para remojarse.</p><p>La mayor parte de la dieta de tu tortuga estará compuesta por vegetales frescos como hojas verdes, junto con algo de vegetación como dientes de león, por ejemplo. Requerirán proteínas de pequeños insectos vivos como gusanos de harina o grillos, y la fruta también puede constituir un pequeño porcentaje de su dieta. El calcio es particularmente importante para una tortuga, así que asegúrate de agregar un suplemento a su comida dos veces por semana.
        </p>',
                    'tags' => 'CuidadoAnimal, Tortugas',
                    'status' => 1,
                     'url' => 'https://www.youtube.com/watch?v=5bXRJA75YO8',
                    'video' => 'https://www.youtube.com/watch?v=5bXRJA75YO8',
                    'blog_image' => public_path('/dummy-images/blog-image/care-your-tortoise.png'),
                    'created_by' => NULL,
                    'updated_by' => 1,
                    'deleted_by' => NULL,
                    'created_at' => '2023-08-17 06:54:50',
                    'updated_at' => '2023-09-13 07:41:17',
                    'deleted_at' => NULL,
                ),



            ];
            foreach ($data as $key => $val) {
                // $subCategorys = $val['sub_category'];
                $blogImage = $val['blog_image'] ?? null;
                $blogData = Arr::except($val, ['blog_image']);
                $blog = Blog::create($blogData);

                if (isset($blogImage)) {
                    $this->attachFeatureImage($blog, $blogImage);
                }
            }
        }
    }
    private function attachFeatureImage($model, $publicPath)
    {
        if (!env('IS_DUMMY_DATA_IMAGE')) return false;

        $file = new \Illuminate\Http\File($publicPath);

        $media = $model->addMedia($file)->preservingOriginal()->toMediaCollection('blog_image');
        return $media;
    }
}
