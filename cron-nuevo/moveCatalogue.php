<?php
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

$docRoot = $_SERVER['DOCUMENT_ROOT'];
define('DOC_ROOT', $docRoot);
include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');
/*
// mover departamentos
$sql = "TRUNCATE departament";
$util->DBProspect()->setQuery($sql);
$util->DBProspect()->UpdateData();
$sql = "select departamentoId, departamento from departamentos order by departamentoId asc";
$util->DB(true)->setQuery($sql);
$deps =$util->DB(true)->GetResult();
foreach($deps as $dep) {
    $sql = "INSERT INTO departament(
                    id,
                    name,
                    created_at,
                    updated_at
                    ) VALUES (
                     '".$dep['departamentoId']."',
                     '".$dep['departamento']."',
                     now(),
                     now()
                    )";
    $util->DBProspect()->setQuery($sql);
    $util->DBProspect()->InsertData();
}


// mover servicios
$sql = "TRUNCATE service;";
$util->DBProspect()->setQuery($sql);
$util->DBProspect()->UpdateData();
$sql = "select tipoServicioId, nombreServicio,departamentoId from tipoServicio where status = '1' order by tipoServicioId asc";
$util->DB(true)->setQuery($sql);
$services =$util->DB(true)->GetResult();

foreach($services as $key => $val) {
    $sql = "INSERT INTO service(
                    id,
                    name,
                    departament_id,
                    created_at,
                    updated_at
                    ) VALUES (
                     '".$val['tipoServicioId']."',
                     '".$val['nombreServicio']."',
                     '".$val['departamentoId']."',
                     now(),
                     now()
                    )";
    $util->DBProspect()->setQuery($sql);
    $util->DBProspect()->InsertData();
}
*/
// mover regimenes
$sql = "TRUNCATE regimen";
$util->DBProspect()->setQuery($sql);
$util->DBProspect()->UpdateData();
$sql = "select tipoRegimenId, claveRegimen, nombreRegimen from tipoRegimen order by tipoRegimenId asc";
$util->DB(true)->setQuery($sql);
$regimenes =$util->DB(true)->GetResult();
foreach($regimenes as $val) {
    $sql = "INSERT INTO regimen(
                    id,
                    name,
                    tax_key,
                    tax_purpose,
                    created_at,
                    updated_at
                    ) VALUES (
                     '".$val['tipoRegimenId']."',
                     '".$val['nombreRegimen']."',
                     '".$val['claveRegimen']."',
                     '".$val['tax_purpose']."',
                      now(),
                      now()
                    )";
    $util->DBProspect()->setQuery($sql);
    $util->DBProspect()->InsertData();
}
exit;
// mover regimenes
$sql = "TRUNCATE activity";
$util->DBProspect()->setQuery($sql);
$util->DBProspect()->UpdateData();
$sql = "select id, name from actividad_comercial order by id asc";
$util->DB(true)->setQuery($sql);
$actividades =$util->DB(true)->GetResult();
foreach($actividades as $val) {
    $sql = "INSERT INTO activity(
                    id,
                    name,
                    created_at,
                    updated_at
                    ) VALUES (
                     '".$val['id']."',
                     '".$val['name']."',
                      now(),
                      now()
                    )";
    $util->DBProspect()->setQuery($sql);
    $util->DBProspect()->InsertData();
}

echo "Finalizado";

