<?php
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
{
    $docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin";
}
else
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
}
define('DOC_ROOT', $docRoot);
include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');

include(DOC_ROOT.'/libs/excel/PHPExcel.php');
$sql = "select count(*) from modification_per_day where date_format(modification_date, '%Y-%d-%m') = date_format(now(), '%Y-%d-%m') ";
$db->setQuery($sql);
$changes =  $db->GetSingle();
if(!$changes)
    exit;

$layout = new Layout();
$opts['tipo'] = 'add_contract';
$opts['type'] =  'generate_layout';
$layout->generateLayout($opts);
$file =  DOC_ROOT.$layout->getUrlResult();

if(file_exists($file)) {
    $send =  new SendMail();
    $query = "select personal.email, personal.name from personal 
              inner join roles on personal.roleId = roles.rolId where roles.nivel IN (2,3)";
    $db->setQuery($query);
    $result = $db->GetResult();
    $emails = [];
    foreach($result as $var) {
        $emails[$var['email']] = $var['name'];
    }
    $emails = [];
    $emails['isc061990@outlook.com'] =  'hector';
    $subject =  "Actualizacion de layout razones sociales";
    $body = "Se envia layout actualizado para importar nuevas razones sociales";
    $send->PrepareMultipleNotice($subject, $body, $emails, '', $file, 'layout_razones_sociales.xlsx', '','','sistema@braunhuerin.com.mx','Admnistrador de sistema', true);
    echo "layout enviado";
}


