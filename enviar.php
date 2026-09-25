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

// --- Antispam ---
// Si parece un robot se responde "ok" igual (para no darle pistas), pero no se manda el mail.
function responder_ok_sin_enviar() {
    echo json_encode(['ok' => true]);
    exit;
}
// 1) Campo trampa: está oculto en el formulario, una persona nunca lo completa.
if (campo('sitio_alt') !== '') {
    responder_ok_sin_enviar();
}
// 2) Tiempo: segundos entre que se cargó la página y el envío (lo calcula el navegador).
//    Menos de 3 segundos, o sin dato (robot que no ejecuta JavaScript), no es una persona.
$tiempo = campo('tiempo');
if (!ctype_digit($tiempo) || (int) $tiempo < 3) {
    responder_ok_sin_enviar();
}
// 3) Límite: máximo 5 consultas por hora desde la misma IP.
$registro = sys_get_temp_dir() . '/txt_form_' . md5($_SERVER['REMOTE_ADDR'] ?? '') . '.json';
$envios = is_file($registro) ? (json_decode((string) file_get_contents($registro), true) ?: []) : [];
$envios = array_values(array_filter($envios, function ($t) { return $t > time() - 3600; }));
if (count($envios) >= 5) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'demasiados_envios']);
    exit;
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

// 4) Contenido típico de spam (se descarta en silencio, igual que los robots de arriba).
$todo = "$nombre $empresa $mensaje";
$links = preg_match_all('~https?://|www\.~i', $todo);
if ($links > 2
    || preg_match('~https?://|www\.~i', "$nombre $empresa")   // una persona no pone links en su nombre
    || preg_match('~\b(casino|crypto|bitcoin|forex|viagra|cialis|porn|backlinks?|seo services?|loan)\b~iu', $todo)
    || preg_match('~[\p{Cyrillic}\p{Han}\p{Hiragana}\p{Katakana}\p{Hangul}\p{Arabic}\p{Thai}]~u', $todo)) {
    responder_ok_sin_enviar();
}

// 5) El dominio del correo tiene que existir y poder recibir mails (frena direcciones inventadas).
//    Acá sí se avisa al visitante, porque puede ser un error de tipeo de una persona real.
$dominio = substr(strrchr($email, '@'), 1);
if (function_exists('checkdnsrr') && !checkdnsrr($dominio, 'MX') && !checkdnsrr($dominio, 'A')) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'email_dominio']);
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
    $envios[] = time();
    @file_put_contents($registro, json_encode($envios), LOCK_EX);
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'mail_fallo']);
}
