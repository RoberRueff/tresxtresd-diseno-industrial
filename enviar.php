<?php
// Recibe el formulario de contacto de index.html y lo manda por mail a info@tresxtresd.com.ar
// Requiere que el hosting tenga habilitada la función mail() de PHP (estándar en hosting compartido tipo DonWeb).

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    exit;
}

function campo($nombre) {
    return isset($_POST[$nombre]) ? trim($_POST[$nombre]) : '';
}
// Evita inyección de headers de mail (ej. alguien poniendo "\r\nBcc: ..." en el nombre o el email).
function sin_saltos($texto) {
    return str_replace(["\r", "\n"], ' ', $texto);
}

$nombre   = campo('nombre');
$empresa  = campo('empresa');
$email    = campo('email');
$whatsapp = campo('whatsapp');
$tipo     = campo('tipo');
$cantidad = campo('cantidad');
$mensaje  = campo('mensaje');

if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'datos_invalidos']);
    exit;
}

$destino = 'info@tresxtresd.com.ar';
$asunto  = mb_encode_mimeheader('Consulta desde la web — ' . sin_saltos($nombre), 'UTF-8');

$cuerpo  = "Nombre: $nombre\n"
         . "Empresa: $empresa\n"
         . "Email: $email\n"
         . "WhatsApp: $whatsapp\n"
         . "Tipo de proyecto: $tipo\n"
         . "Cantidad: $cantidad\n"
         . "Mensaje:\n$mensaje\n";

if (!empty($_FILES['archivo']['name'])) {
    // No se reenvía el archivo en sí (requeriría armar el mail como multipart), solo se avisa que lo adjuntaron.
    $cuerpo .= "\n(El visitante adjuntó un archivo: " . $_FILES['archivo']['name'] . " — no se reenvía por mail, pedirlo por WhatsApp o email de respuesta.)\n";
}

$headers   = [];
$headers[] = 'From: Formulario web <no-reply@tresxtresd.com.ar>';
$headers[] = 'Reply-To: ' . sin_saltos($email);
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

$enviado = mail($destino, $asunto, $cuerpo, implode("\r\n", $headers));

if ($enviado) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'mail_fallo']);
}
