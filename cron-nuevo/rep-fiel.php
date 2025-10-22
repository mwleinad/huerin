<?php
ini_set('memory_limit','3G');
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
} else {
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
}

define('DOC_ROOT', $docRoot);

include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');

echo 'Inicio ejecucion : '.date('Y-m-d H:i:s',time()).chr(10).chr(13);
$logFile = DOC_ROOT . "/sendFiles/log_envios_" . date('Y-m-d_H-i-s') . ".txt";
file_put_contents($logFile, "Registro de envios - " . date('Y-m-d H:i:s') . "\n\n");

$sql = "SELECT personal.personalId,
            personal.name,
            personal.email,
            personal.lastSendArchivo,
            personal.departamentoId,
            personal.lastSendArchivo,
            departamentos.departamento as departamento,
            roles.name as rol,
            roles.nivel 
        FROM personal
        INNER JOIN departamentos ON personal.departamentoId=departamentos.departamentoId
        INNER JOIN roles ON personal.roleId=roles.rolId 
        WHERE  personal.active='1'
        AND roles.name NOT IN ('Socio','Asociado')
        AND DATE_ADD(IF(personal.lastSendArchivo = '0000-00-00' OR personal.lastSendArchivo IS NULL, '1900-01-01', personal.lastSendArchivo), INTERVAL 1 WEEK) <= CURDATE()
        ORDER BY roles.nivel ASC LIMIT 50";

$db->setQuery($sql);
$employees = $db->GetResult();
foreach($employees as $key=>$itemEmploye){
    $persons = array();
    $personal->setPersonalId($itemEmploye['personalId']);
    $subordinados = $personal->Subordinados(true);
    $persons = $util->ConvertToLineal($subordinados, 'personalId');
    array_unshift($persons, $itemEmploye['personalId']);

    $sql  = "SELECT DISTINCT contract.contractId,
                customer.nameContact,
                contract.name
            FROM  contract
            INNER JOIN customer ON contract.customerId=customer.customerId
            INNER JOIN contractPermiso ON contract.contractId=contractPermiso.contractId
            WHERE contractPermiso.personalId IN (".implode(',', $persons).")
            AND contract.activo='Si' and customer.active='1' ";
    $db->setQuery($sql);
    $contracts = $db->GetResult();

    if (count($contracts)<=0)
        continue;

    foreach ($contracts as $kc=>$vc){
        $filesExp = $contractRep->CheckExpirationFiel($vc,$itemEmploye['departamentoId']);
        if(empty($filesExp))
        {
            unset($contracts[$kc]);
            continue;
        }

        $contracts[$kc]['filesExpirate'] = $filesExp;
    }
    $sortedArray = $util->orderMultiDimensionalArray($contracts,'nameContact');
    if(count($sortedArray)<=0)
        continue;

    $numContracts = count($sortedArray);
    $numFilesExp = 0;
    foreach($sortedArray as $contract){
        $numFilesExp += count($contract['filesExpirate']);
    }

    $html = '<html>
			<head>
				<title>Cupon</title>
				<style type="text/css">
					table,td {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 15px "Trebuchet MS";
						font-size:15px;
						border: 1px solid #C0C0C0;
						border-collapse: collapse;
					}
					.cabeceraTabla {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 14px "Trebuchet MS";
						font-size:14px;
						border: 1px solid #C0C0C0;
						background: gray;
						color: #FFFFFF;
						vertical-align: center;
						border-collapse: collapse;
					}
					.divInside {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #686DAB;
						background: #686DAB;
						color: #FFFFFF;
						vertical-align: center;
						text-align: center;
						height: 50px;
					}
				</style>
			</head>
			';
    $depto = $itemEmploye['departamento'];
    $smarty->assign("depto", $depto);
    $smarty->assign("namePersonal", $itemEmploye['name']);
    $smarty->assign("registros", $sortedArray);
    $smarty->assign("DOC_ROOT", DOC_ROOT);
    $html .= $smarty->fetch(DOC_ROOT.'/templates/lists/rep-fiel.tpl');
    $file = strtoupper(substr($depto,0,2))."-ARCHIVOS-".trim(strtoupper(substr($itemEmploye['name'],0,6)).$itemEmploye['personalId']);
    $excel->ConvertToExcel($html, 'xlsx', false, $file,true,100);

    $subject= $file;
    $body   = "ESTIMADO USUARIO : SE HACE LLEGAR EL REPORTE DE ARCHIVOS VENCIDOS O PROXIMOS A VENCER DE LAS EMPRESAS QUE SE ENCUENTRAN BAJO SU RESPONSABILIDAD
          <br><br>
          Este correo se genero automaticamente favor de no responder";
    $sendmail = new SendMail;

    $to =[];
    if(PROJECT_STATUS !== 'test')
        $to = array($itemEmploye["email"]=>$itemEmploye['name']);

    $toName = $itemEmploye['name'];
    $attachment = DOC_ROOT . "/sendFiles/".$file.".xlsx";

    $sendmail->PrepareMultiple($subject, $body, $to, $toName, $attachment, $file.".xlsx", '', '','noreply@braunhuerin.com.mx' , "Notificacion de archivos vencidos o por vencer"); ;

    $up = 'UPDATE personal SET lastSendArchivo=" '.date("Y-m-d").' " WHERE personalId='.$itemEmploye["personalId"].' ';
    $db->setQuery($up);
    $db->UpdateData();

    unlink($attachment);
    $logEntry = "Enviado a: {$itemEmploye['name']} ({$itemEmploye['email']}) - Contratos: $numContracts, Archivos expirados: $numFilesExp\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    echo "<br>";
}
$subjectLog = "Registro de envios - " . date('Y-m-d');
$bodyLog = "Adjunto el registro de envios realizados.";
$sendmailLog = new SendMail;
$toLog = array(EMAIL_DEV=>'Desarrollador');
$toNameLog = 'Desarrollador';
$attachmentLog = $logFile;
$sendmailLog->PrepareMultiple($subjectLog, $bodyLog, $toLog, $toNameLog, $attachmentLog, basename($logFile), '', '','noreply@braunhuerin.com.mx' , "Registro de envios de notificacion");
echo 'Final ejecucion : '.date('Y-m-d H:i:s',time());
