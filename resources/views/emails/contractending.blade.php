<!DOCTYPE html>
<html>
<head>
    <title>Su servicio está por caducar</title>
</head>
<body>
    <h1>Buen día estimados {{ $serviceData['company'] }},</h1>
    <p>De parte de Mindsoft deseamos recordarles que su servicio {{ $serviceData['serviceName'] }} caduca el día {{ $serviceData['endDate'] }}.</p>
    <p>Favor en caso de desear renovarlo contáctenos:</p>
    
    <!-- WhatsApp link -->
    <p> 
        Nuestro Whatsapp al 
        <a href="https://wa.me/593984258842" target="_blank">
            +593 98 425 8842
        </a>
    </p>
    
    <!-- Email link -->
    <p> 
        O a 
        <a href="mailto:dennis.ocana@mindsoft.biz">
            dennis.ocana@mindsoft.biz
        </a>
    </p>
</body>
</html>
