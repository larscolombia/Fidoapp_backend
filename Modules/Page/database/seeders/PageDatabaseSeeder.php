<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Page\Models\Page;

class PageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $pages = [
            [
                'name' => 'Política de privacidad',
                'sequence' => 1,
                'status' => 1,
                'type' => 'privacy-policy',
                'description' => '<p>Esta Política de privacidad ha sido elaborada por FidoApp. FidoApp es el único propietario de varios sitios web de demostración que contienen vistas previas de temas para sitios web de WordPress.  Esta Política de privacidad describe cómo FidoApp recoge, utiliza, comparte y asegura la información personal que usted proporciona. </p><p>FidoApp no comparte información personal de ningún tipo con nadie. No venderemos ni alquilaremos su nombre ni su información personal a terceros. No vendemos, alquilamos ni proporcionamos acceso externo a nuestra lista de correo ni a ningún dato que almacenemos. Cualquier dato que un usuario almacene a través de nuestras instalaciones es propiedad exclusiva de dicho usuario o empresa. En cualquier momento, un usuario o empresa es libre de tomar sus datos e irse, o simplemente borrarlos de nuestras instalaciones.</p><p>FidoApp sólo recoge la información personal específica que es necesaria para que usted pueda acceder y utilizar nuestros servicios. Estos datos personales incluyen, entre otros, nombre y apellidos, dirección de correo electrónico y país de residencia.</p><p>FidoApp puede revelar información personal si FidoApp está obligado por ley, orden de registro, citación, orden judicial o investigación de fraude. También podemos utilizar la información personal de forma que no le identifique específicamente ni permita que nos pongamos en contacto con usted, pero sí que identifique ciertos criterios sobre los usuarios de nuestro sitio en general (por ejemplo, podemos informar a terceros sobre el número de usuarios registrados, el número de visitantes únicos y las páginas por las que se navega con más frecuencia).</p><p>&nbsp;</p><h5>Uso de la información</h5><p><br></p><p>Utilizamos la información para permitirle utilizar el sitio y sus funciones y para garantizar la seguridad de su uso y evitar cualquier posible abuso. Podemos utilizar la información que recopilamos para diversos fines, entre ellos:</p><p><strong>Promoción</strong>&nbsp;— Con su consentimiento, le enviamos comunicaciones promocionales, como información sobre productos y servicios, funciones, encuestas, boletines, ofertas, promociones, concursos y eventos.;</p><p><strong>Seguridad y protección</strong>&nbsp;— Utilizamos la información de que disponemos para verificar cuentas y actividades, combatir conductas nocivas, detectar y prevenir el spam y otras malas experiencias, mantener la integridad de la Plataforma y promover la seguridad.</p><p><strong>Investigación y desarrollo de productos</strong>&nbsp;— Utilizamos la información de que disponemos para desarrollar, probar y mejorar nuestra Plataforma y Servicios, realizando encuestas e investigaciones, y probando y resolviendo problemas de nuevos productos y funciones.</p><p><strong>Comunicación con usted</strong>&nbsp;— Utilizamos la información de que disponemos para enviarle diversas comunicaciones, informarle sobre nuestros productos e informarle sobre nuestras políticas y condiciones. También utilizamos su información para responderle cuando se pone en contacto con nosotros.</p><p>&nbsp;</p><h5>Enmiendas</h5><p><br></p><p>Podemos modificar esta Política de privacidad de vez en cuando. Cuando modifiquemos esta Política de privacidad, actualizaremos esta página en consecuencia y le pediremos que acepte las modificaciones para poder seguir utilizando nuestros servicios.</p><h5><br></h5><h5>Póngase en contacto con nosotros</h5><p>Puede obtener más información sobre el funcionamiento de la privacidad en nuestro sitio poniéndose en contacto con nosotros. Si tiene alguna pregunta sobre esta Política, puede ponerse en contacto con nosotros a través de la dirección de correo electrónico facilitada. Además, también podemos resolver cualquier disputa que tenga con nosotros en relación con nuestras políticas y prácticas de privacidad a través del contacto directo. Escríbanos a<em>admin@fidoapp.com</em></p>',
            ],
            [
                'name' => 'Condiciones generales',
                'type' => 'terms-conditions',
                'sequence' => 2,
                'status' => 1,
                'description' => '<p>Al acceder a productos en este sitio y realizar un pedido desde nuestro sitio web, confirmas que estás de acuerdo y obligado por los términos y condiciones presentados y descritos aquí. Estos términos se aplican a todo el sitio web y cualquier correo electrónico u otro tipo de comunicación entre tú y FidoApp. El equipo de FidoApp no es responsable de ningún daño directo, indirecto, incidental o consecuente, incluyendo, pero no limitado a, la pérdida de datos o ganancias, que surjan del uso de los materiales en este sitio.</p><p>FidoApp no será responsable de ningún resultado que pueda ocurrir durante el uso de nuestros recursos. Nos reservamos el derecho de cambiar precios y revisar la política de uso de recursos en cualquier momento.</p><p>&nbsp;</p><h5><strong>Productos</strong></h5><p><br></p><p>Todos los productos y servicios ofrecidos en este sitio son producidos por FidoApp. Puedes acceder a tu descarga desde tu panel correspondiente. No proporcionamos soporte para software, complementos o bibliotecas de terceros que puedas haber utilizado con nuestros productos.</p><p>&nbsp;</p><h5>Seguridad</h5><p><br></p><p>FidoApp no procesa ningún pago de pedidos a través del sitio web. Todos los pagos se procesan de manera segura a través de RazorPay y Stripe, proveedores de pago en línea de terceros.</p><p>&nbsp;</p><h5>Política de Cookies</h5><p><br></p><p>Una cookie es un archivo que contiene un identificador (una cadena de letras y números) que es enviado por un servidor web a un navegador web y es almacenado por el navegador. El identificador se envía nuevamente al servidor cada vez que el navegador solicita una página del servidor. Nuestro sitio web utiliza cookies. Al utilizar nuestro sitio web y aceptar esta política, consientes nuestro uso de cookies de acuerdo con los términos de esta política.</p><p>Utilizamos cookies de sesión para personalizar el sitio web para cada usuario.</p><p>Utilizamos Google Analytics para analizar el uso de nuestro sitio web. Nuestro proveedor de servicios analíticos genera información estadística y otra información sobre el uso del sitio web mediante cookies. Eliminar cookies tendrá un impacto negativo en la usabilidad del sitio. Si bloqueas las cookies, no podrás utilizar todas las funciones en nuestro sitio web.</p><p>&nbsp;</p><h5>Reembolsos</h5><p><br></p><p>Puedes solicitar un reembolso por el artículo comprado bajo ciertas circunstancias enumeradas en nuestra Política de Reembolsos. En caso de que cumplas con los criterios aplicables para recibir un reembolso, FidoApp te emitirá un reembolso y te pedirá que especifiques cómo el producto no cumplió con tus expectativas de rendimiento.</p><p>&nbsp;</p><h5>Email</h5><p><br></p><p>Al registrarte en nuestro sitio web https://iqonic.design aceptas recibir correos electrónicos de nuestra parte – tanto transaccionales como promocionales (ocasionalmente).</p><p>&nbsp;</p><h5>Propiedad</h5><p><br></p><p>La propiedad del producto está regida por la licencia de uso.</p><p>&nbsp;</p><h5>Cambios sobre términos</h5><p><br></p><p>Podemos cambiar/actualizar nuestros términos de uso sin previo aviso. Si cambiamos nuestros términos y condiciones, publicaremos esos cambios en esta página. Los usuarios pueden consultar la última versión aquí.</p>',
            ],
        ];
        if (env('IS_DUMMY_DATA')) {
            foreach ($pages  as $key => $pages_data) {
                $pages = Page::create($pages_data);
            }
        }

    }
}
