<!DOCTYPE html>
<html>
<head>
    <title>Su servicio está por caducar pronto</title>
    <style>
        /* General reset */
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
        }

        /* Wrapper for the centered content */
        .email-wrapper {
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        /* Main content box styling */
        .email-content {
            width: 100%;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Header styling */
        .email-header {
            background-color: #001689; /* Mindsoft blue */
            padding: 20px;
            color: #ffffff;
            text-align: center;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        /* Body content styling */
        .email-body {
            padding: 20px 30px;
            color: #333333;
        }

        .email-body h2 {
            color: #001689; /* Mindsoft blue */
            font-size: 20px;
            margin-bottom: 15px;
        }

        .urgent {
            color: #FF4438; /* Mindsoft red */
            font-weight: bold;
        }

        /* Contact info styling */
        .contact-info a {
            color: #001689;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        /* Footer */
        .email-footer {
            padding: 15px 30px;
            font-size: 12px;
            color: #666666;
            text-align: center;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                <h1>Mindsoft</h1>
                <p>Notificación Urgente</p>
            </div>
            <div class="email-body">
                <p>Fecha: {{ date('l, d F Y H:i') }}</p>
                <h2>Estimado(a) {{ $serviceData['company'] }},</h2>
                <p>Su servicio <strong>{{ $serviceData['serviceName'] }}</strong> caduca en una semana, el día <strong>{{ $serviceData['endDate'] }}</strong>.</p>
                <p class="urgent">Le recomendamos encarecidamente que renueve su servicio antes de esta fecha para evitar cualquier interrupción y posible pérdida de datos.</p>
                <p>Para renovar, contáctenos a la brevedad:</p>
                <div class="contact-info">
                    <p>WhatsApp: 
                        <a href="https://wa.me/593984258842" target="_blank">+593 98 425 8842</a>
                    </p>
                    <p>Email: 
                        <a href="mailto:dennis.ocana@mindsoft.biz">dennis.ocana@mindsoft.biz</a>
                    </p>
                </div>
            </div>
            <div class="email-footer">
                <p>Gracias por utilizar nuestros servicios.<br>Atentamente, el equipo de Mindsoft</p>
            </div>
        </div>
    </div>
</body>
</html>

