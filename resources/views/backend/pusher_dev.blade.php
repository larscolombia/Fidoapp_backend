<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>pusher dev</title>
</head>

<body>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        var pusher = new Pusher("2a16d5e0c49e750492e7", {
            cluster: 'us2'
        });
        const channel = pusher.subscribe('user-event-notification');
        channel.bind('user-event-notification', function(data) {
            console.log('Received notification:', data);
            // Manejar la notificación recibida
        });
    </script>
</body>

</html>
