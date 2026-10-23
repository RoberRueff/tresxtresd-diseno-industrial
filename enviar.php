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

// --- DEBUG TEMPORAL: va en la propia respuesta JSON (se ve en la pestaña Network del navegador).
// No depende del error_log del servidor (acá está apagado: log_errors=0 en .user.ini). Sacar cuando se confirme el envío.
$debug = [];
function txt_log(&$debug, $msg) {
    $debug[] = $msg;
}

// --- Antispam ---
// Si parece un robot se responde "ok" igual (para no darle pistas), pero no se manda el mail.
function responder_ok_sin_enviar($debug) {
    echo json_encode(['ok' => true, 'debug' => $debug]);
    exit;
}

// 1) Campo trampa: está oculto en el formulario, una persona nunca lo completa.
if (campo('sitio_alt') !== '') {
    txt_log($debug, 'bloqueado por honeypot (sitio_alt="' . campo('sitio_alt') . '")');
    responder_ok_sin_enviar($debug);
}
// 2) Tiempo: segundos entre que se cargó la página y el envío (lo calcula el navegador).
//    Menos de 3 segundos, o sin dato (robot que no ejecuta JavaScript), no es una persona.
$tiempo = campo('tiempo');
if (!ctype_digit($tiempo) || (int) $tiempo < 3) {
    txt_log($debug, 'bloqueado por tiempo (tiempo="' . $tiempo . '")');
    responder_ok_sin_enviar($debug);
}
// 3) Límite: máximo 5 consultas por hora desde la misma IP.
$registro = sys_get_temp_dir() . '/txt_form_' . md5($_SERVER['REMOTE_ADDR'] ?? '') . '.json';
$envios = is_file($registro) ? (json_decode((string) file_get_contents($registro), true) ?: []) : [];
$envios = array_values(array_filter($envios, function ($t) { return $t > time() - 3600; }));
if (count($envios) >= 5) {
    txt_log($debug, 'bloqueado por límite de envíos por hora');
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => 'demasiados_envios', 'debug' => $debug]);
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
    txt_log($debug, 'bloqueado por filtro de contenido (links=' . $links . ', texto="' . $todo . '")');
    responder_ok_sin_enviar($debug);
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
$asunto_txt = 'Consulta desde la web — ' . sin_saltos($nombre);
// mb_encode_mimeheader necesita la extensión mbstring; si el hosting no la tiene, se codifica a mano (RFC 2047).
$asunto = function_exists('mb_encode_mimeheader')
    ? mb_encode_mimeheader($asunto_txt, 'UTF-8')
    : '=?UTF-8?B?' . base64_encode($asunto_txt) . '?=';

$cuerpo  = "Nombre: $nombre\n"
         . "Empresa: $empresa\n"
         . "Email: $email\n"
         . "WhatsApp: $whatsapp\n"
         . "Tipo de proyecto: $tipo\n"
         . "Cantidad: $cantidad\n"
         . "Mensaje:\n$mensaje\n";

// --- Adjunto ---
// Límite propio además del de PHP/hosting: muchos servidores de mail compartidos cortan o
// rebotan mensajes de más de ~10-15MB, y el base64 le suma ~37% de peso al archivo original.
const ADJUNTO_MAX_BYTES = 8 * 1024 * 1024;
const ADJUNTO_EXT_MIME = [
    'pdf'  => 'application/pdf',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'stl'  => 'model/stl',
    'obj'  => 'text/plain',
    'step' => 'application/step',
    'stp'  => 'application/step',
    '3mf'  => 'model/3mf',
];

// DEBUG TEMPORAL: todo este bloque va en un try/catch para que, si algo explota (ej. error fatal
// de PHP), se vea en el JSON de respuesta en vez de morir en silencio (acá display_errors=0).
try {
    $adjunto = null;
    if (!empty($_FILES['archivo']['name']) && $_FILES['archivo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error_subida = $_FILES['archivo']['error'];
        $nombre_archivo = sin_saltos(basename($_FILES['archivo']['name']));
        $ext = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

        if ($error_subida === UPLOAD_ERR_INI_SIZE || $error_subida === UPLOAD_ERR_FORM_SIZE) {
            $cuerpo .= "\n(El visitante intentó adjuntar \"$nombre_archivo\" pero supera el tamaño máximo que acepta el hosting — pedirlo por WhatsApp o email de respuesta.)\n";
        } elseif ($error_subida !== UPLOAD_ERR_OK) {
            $cuerpo .= "\n(El visitante intentó adjuntar \"$nombre_archivo\" pero falló la subida — pedirlo por WhatsApp o email de respuesta.)\n";
        } elseif (!isset(ADJUNTO_EXT_MIME[$ext])) {
            $cuerpo .= "\n(El visitante adjuntó \"$nombre_archivo\", un tipo de archivo no permitido — pedirlo por WhatsApp o email de respuesta.)\n";
        } elseif ($_FILES['archivo']['size'] > ADJUNTO_MAX_BYTES) {
            $cuerpo .= "\n(El visitante adjuntó \"$nombre_archivo\" (" . round($_FILES['archivo']['size'] / 1024 / 1024, 1) . " MB), demasiado pesado para mandar por mail — pedirlo por WhatsApp o email de respuesta.)\n";
        } else {
            $contenido = file_get_contents($_FILES['archivo']['tmp_name']);
            if ($contenido === false) {
                $cuerpo .= "\n(El visitante adjuntó \"$nombre_archivo\" pero no se pudo leer el archivo subido — pedirlo por WhatsApp o email de respuesta.)\n";
            } else {
                $adjunto = [
                    'nombre' => str_replace('"', "'", $nombre_archivo),
                    'mime'   => ADJUNTO_EXT_MIME[$ext],
                    'datos'  => $contenido,
                ];
            }
        }
        txt_log($debug, 'archivo recibido: name=' . $_FILES['archivo']['name'] . ', error=' . $error_subida . ', size=' . $_FILES['archivo']['size'] . ', ext=' . $ext . ', adjunto_armado=' . ($adjunto ? 'si' : 'no'));
    } else {
        txt_log($debug, 'sin archivo adjunto en el POST');
    }

    $headers   = [];
    $headers[] = 'From: Formulario web <no-reply@tresxtresd.com.ar>';
    $headers[] = 'Reply-To: ' . sin_saltos($email);
    $headers[] = 'MIME-Version: 1.0';

    if ($adjunto) {
        $boundary = md5(uniqid('', true));
        $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

        $cuerpo_mime = "--$boundary\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: 8bit\r\n\r\n"
            . $cuerpo . "\r\n"
            . "--$boundary\r\n"
            . 'Content-Type: ' . $adjunto['mime'] . '; name="' . $adjunto['nombre'] . "\"\r\n"
            . "Content-Transfer-Encoding: base64\r\n"
            . 'Content-Disposition: attachment; filename="' . $adjunto['nombre'] . "\"\r\n\r\n"
            . chunk_split(base64_encode($adjunto['datos'])) . "\r\n"
            . "--$boundary--";
    } else {
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $cuerpo_mime = $cuerpo;
    }

    txt_log($debug, 'llamando a mail() — adjunto=' . ($adjunto ? $adjunto['nombre'] . ' (' . strlen($adjunto['datos']) . ' bytes)' : 'ninguno') . ', destino=' . $destino);
    $enviado = mail($destino, $asunto, $cuerpo_mime, implode("\r\n", $headers));
    txt_log($debug, 'mail() devolvió ' . ($enviado ? 'true' : 'false'));

    if ($enviado) {
        $envios[] = time();
        @file_put_contents($registro, json_encode($envios), LOCK_EX);
        echo json_encode(['ok' => true, 'debug' => $debug]);
    } else {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'mail_fallo', 'debug' => $debug]);
    }
} catch (\Throwable $e) {
    txt_log($debug, 'EXCEPCIÓN: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'excepcion', 'debug' => $debug]);
}
