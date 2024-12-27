<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.css')}}">
    <style>
        .card-success {
            border-color: #ff4931; /* Color del card */
        }
        .full-height {
            height: 100vh; /* Altura completa de la ventana */
        }
        .bg-white{
            color: #000;
        }
        .icon-checkmark {
            width: 24px; /* Ajusta el tamaño del SVG */
            height: 24px; /* Ajusta el tamaño del SVG */
            fill: #28a745; /* Color success de Bootstrap (verde) */
        }
        .fa-check {
            font-size: 24px; /* Tamaño del icono Font Awesome */
            color: #28a745; /* Color success de Bootstrap (verde) */
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center full-height">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <div class="card  text-center">
                    <div class="card-header">
                       <div class="d-flex justify-content-center">
                        <i class="fa-solid fa-check"></i>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="icon-checkmark">
                            <path d="M504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zM227.3 387.3l184-184c6.2-6.2 6.2-16.4 0-22.6l-22.6-22.6c-6.2-6.2-16.4-6.2-22.6 0L216 308.1l-70.1-70.1c-6.2-6.2-16.4-6.2-22.6 0l-22.6 22.6c-6.2 6.2-6.2 16.4 0 22.6l104 104c6.2 6.2 16.4 6.2 22.6 0z"/>
                        </svg>
                        <h5 class="card-title font-weight-bold ml-2">{{ $message }}</h5>
                       </div>
                    </div>
                    <div class="card-body bg-white ">
                        <h5 class="card-text">Detalles:</h5>
                       <div>
                        <p class="text-justify">Monto: ${{ $payment->amount }}</p>
                        <p class="text-justify">Descripción: {{ $payment->description }}</p>
                        <p class="text-justify">Fecha: {{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y') }}</p>
                       </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</body>
</html>
