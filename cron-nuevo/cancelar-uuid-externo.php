<?php
if (!$_SERVER["DOCUMENT_ROOT"]) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/..');
}

if ($_SERVER['DOCUMENT_ROOT'] != "/var/www/mainplatform/public_html" && $_SERVER['DOCUMENT_ROOT'] != "/var/www/qplatform/public_html") {
    $docRoot = $_SERVER['DOCUMENT_ROOT'] . "";
    session_save_path("C:/laragon/tmp");
} else {
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
    session_save_path("/tmp");
}
define('DOC_ROOT', $docRoot);
include_once(DOC_ROOT . '/init_cron.php');
include_once(DOC_ROOT . '/config.php');
include_once(DOC_ROOT . '/constants.php');
include_once(DOC_ROOT . '/libraries33.php');

if (!isset($_SESSION)) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['User'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado. Inicia sesión para continuar.']);
    exit;
}

function jsonExit(bool $success, string $message, array $extra = []): void
{
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

// --- Validar parámetros requeridos ---
foreach (['rfcEmisor', 'rfcReceptor', 'uuid', 'total', 'rfcId'] as $param) {
    if (empty($_GET[$param])) {
        jsonExit(false, "Parámetro requerido faltante: $param");
    }
}

$rfcEmisor        = strtoupper(trim($_GET['rfcEmisor']));
$rfcReceptor      = strtoupper(trim($_GET['rfcReceptor']));
$uuid             = strtoupper(trim($_GET['uuid']));
$total            = trim($_GET['total']);
$rfcId            = (int) $_GET['rfcId'];
$motivo           = isset($_GET['motivo']) ? trim($_GET['motivo']) : '02';
$folioSustitucion = isset($_GET['folioSustitucion']) ? trim($_GET['folioSustitucion']) : '';

if (!preg_match('/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $rfcEmisor)) {
    jsonExit(false, "Formato de rfcEmisor inválido.");
}
if (!preg_match('/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $rfcReceptor)) {
    jsonExit(false, "Formato de rfcReceptor inválido.");
}
if (!preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i', $uuid)) {
    jsonExit(false, "Formato de UUID inválido.");
}
if (!is_numeric($total) || (float)$total <= 0) {
    jsonExit(false, "El total debe ser un número mayor a cero.");
}
if ($rfcId <= 0) {
    jsonExit(false, "rfcId inválido.");
}

// --- Obtener registro de la tabla rfc y noCertificado de serie ---
$db->setQuery("
    SELECT r.rfcId, r.rfc, r.empresaId, s.noCertificado
    FROM rfc r
    LEFT JOIN serie s ON s.rfcId = r.rfcId
    WHERE r.rfcId = " . $rfcId . "
    LIMIT 1
");
$rfcRow = $db->GetRow();

if (!$rfcRow) {
    jsonExit(false, "No se encontró registro RFC con rfcId: $rfcId");
}

$empresaId     = $rfcRow['empresaId'];
$noCertificado = $rfcRow['noCertificado'];

$_SESSION['empresaId'] = $empresaId;

// --- Consultar estatus en el SAT antes de solicitar cancelación ---
$statusResponse = $cancelation->getStatus($rfcEmisor, $rfcReceptor, $uuid, $total);

if ($statusResponse === false) {
    jsonExit(false, "No se pudo consultar el estatus del CFDI en el SAT. Intente más tarde.");
}

$estado       = $statusResponse->ConsultaResult->Estado ?? '';
$esCancelable = $statusResponse->ConsultaResult->EsCancelable ?? '';

if (strpos($estado, 'Cancelado') !== false) {
    jsonExit(true, "El CFDI ya se encuentra cancelado. Estado: $estado.", [
        'uuid'   => $uuid,
        'estado' => $estado,
    ]);
}

if ($esCancelable === 'No Cancelable') {
    jsonExit(false, "El CFDI no es cancelable. Verifique si cuenta con documentos relacionados.", [
        'uuid'         => $uuid,
        'estado'       => $estado,
        'esCancelable' => $esCancelable,
    ]);
}

// --- Cargar certificados desde el sistema de archivos ---
$basePath = DOC_ROOT . "/empresas/" . $empresaId . "/certificados/" . $rfcId . "/";
$cerPath  = $basePath . $noCertificado . ".cer.pem";
$keyPath  = $basePath . $noCertificado . ".key.pem";
$encPath  = $basePath . $noCertificado . ".enc";

if (!is_file($cerPath)) {
    jsonExit(false, "Certificado (.cer.pem) no encontrado para rfcId: $rfcId");
}
if (!is_file($keyPath)) {
    jsonExit(false, "Llave privada (.key.pem) no encontrada para rfcId: $rfcId");
}

$passwordPath = $basePath . "password.txt";
if (!is_file($passwordPath)) {
    jsonExit(false, "Archivo password.txt no encontrado para rfcId: $rfcId");
}

$fh = fopen($cerPath, 'r');
$contentCer = fread($fh, filesize($cerPath));
fclose($fh);

$fh = fopen($passwordPath, 'r');
$keyPassword = trim(fread($fh, filesize($passwordPath)));
fclose($fh);

exec(
    "openssl rsa -in " . escapeshellarg($keyPath) .
    " -des3 -out " . escapeshellarg($encPath) .
    " -passout pass:" .FINKOK_PASS
);

if (!is_file($encPath)) {
    jsonExit(false, "No se pudo generar la llave cifrada (.enc) para rfcId: $rfcId");
}

$fh = fopen($encPath, 'r');
$contentKey = fread($fh, filesize($encPath));
fclose($fh);

// --- Construir payload y solicitar cancelación a Finkok ---
$uuidItem = [
    "UUID"             => $uuid,
    "Motivo"           => $motivo,
    "FolioSustitucion" => $folioSustitucion,
];

$data = [
    "UUIDS"        => ['UUID' => $uuidItem],
    "username"     => FINKOK_USER,
    "password"     => FINKOK_PASS,
    "taxpayer_id"  => $rfcEmisor,
    "cer"          => $contentCer,
    "key"          => $contentKey,
    "store_pending" => false,
];

$response = $pac->Cancelar($data, 'cancel');

if ($response === false) {
    jsonExit(false, "Error de comunicación con Finkok. Intente más tarde.");
}

$responseCancel = $response->cancelResult ?? null;
if (!$responseCancel) {
    jsonExit(false, "Respuesta inesperada de Finkok.");
}

$statusCode = $responseCancel->Folios->Folio->EstatusUUID ?? null;

$mensajes = [
    201 => "La cancelación se ha realizado correctamente.",
    202 => "El documento ha sido cancelado anteriormente.",
    203 => "El documento no fue encontrado o no corresponde al emisor.",
    205 => "El SAT aún no ha registrado el CFDI. Se recomienda esperar e intentar nuevamente.",
    207 => "Motivo de cancelación inválido o relación de CFDI incorrecta.",
    208 => "Folio de sustitución inválido.",
    'no_cancelable' => "La factura contiene CFDI relacionados. Revise las relaciones antes de cancelar.",
    708 => "No se pudo conectar con el SAT. Recuerde que solo tiene 3 intentos para cancelar.",
    798 => "Ya existe una solicitud previa. Espere 72 horas antes de volver a intentar.",
    799 => "Se ha excedido el límite máximo de intentos para cancelar el comprobante.",
];

$success = ($statusCode == 201 || $statusCode == 202);
$message = $mensajes[(int)$statusCode] ?? ($mensajes[$statusCode] ?? "Código de respuesta desconocido: $statusCode");

jsonExit($success, $message, [
    'uuid'       => $uuid,
    'statusCode' => $statusCode,
    'estado'     => $estado,
]);
