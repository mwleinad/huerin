<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 16/02/2018
 * Time: 10:48 AM
 */
ini_set('memory_limit','3G');
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

if(!$util->is_cli()){
    echo "Execution, Not Permitted!!!";
    exit;
}
$timeStart = date("d-m-Y").' a las '.date('H:i:s');
$sql =  "SELECT * FROM personal WHERE active='1' ORDER BY personalId ASC ";
$util->DB()->setQuery($sql);
$results =  $util->DB()->GetResult();
$mod=0;
$nomod=0;
$entry = "Inicio ".$timeStart." Hrs.".chr(13).chr(10).chr(13).chr(10);
foreach($results as $key =>$item){
    $currentPassword = $item["passwd"];
    $cadena = $util->generateRandomString(6,true);

    $util->DB()->setQuery('UPDATE personal SET passwd="'.$cadena.'" WHERE personalId='.$item['personalId'].'');
    if($util->DB()->UpdateData()){
        $util->DB()->setQuery('UPDATE personal SET lastChangePassword="'.date('Y-m-d').'" WHERE personalId='.$item['personalId'].'');
        $util->DB()->UpdateData();
           $body="ESTIMADO USUARIO CON EL FIN DE MANTENER LA SEGURIDAD DE SUS DATOS Y DE LOS CLIENTES QUE SE ENCUENTRAN EN LA PLATAFORMA BAJO SU RESPONSABILIDAD 
                   SE HA REALIZADO EL CAMBIO DE CONTRASE&Ntilde;A DE SU CUENTA, CIERRE SU SESSION SI SE ENCUENTRA ACTUALMENTE EN LA PLATAFORMA E INGRESE NUEVAMENTE CON LOS SIGUIENTES DATOS:  <br>
                   USUARIO:".$item['username']." <br>
                   PASSWD:".$cadena." 
                   <br><br>
                   Este correo se creo automaticamente, favor de no responder.
                   ";
            $subject="CAMBIO DE CONTRASEÑA ".$item['name'];
            $to = $item['email'];
            $toName= $item['name'];

            $sendmail = new SendMail;
            if($sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileName.".xlsx","", "",'noreply@braunhuerin.com.mx' , "ADMINISTRADOR DE PLATAFORMA")){
                $entry .= "Contraseña cambiada correctamente para ".$item["name"]."(".$item['username'].") y correo enviado a ".$item['email'].'\n'.chr(13).chr(10);
                $mod++;
            }else{
                $util->DB()->setQuery("UPDATE personal SET passwd='$currentPassword' WHERE personalId='".$item['personalId']."'");
                $util->DB()->UpdateData();
                $entry .= "Hubo un error al cambiar contraseña para ".$item["name"]."(".$item['username']."), no se realizaron cambios.".chr(13).chr(10);
            }
    }else{
        $nomod++;
    }
    $mod++;
}
$time = date("d-m-Y").' a las '.date('H:i:s');
$entry .= chr(13).chr(10).chr(13).chr(10);
$entry .= "Fin ".$time." Hrs.".chr(13).chr(10);
$entry .="Total modificados ".$mod.chr(13).chr(10);
$file = DOC_ROOT."/sendFiles/log_change_password.txt";
$open = fopen($file,"w");
if ( $open ) {
    fwrite($open,$entry);
    fclose($open);
}
if($mod>0){
    $to = [];
    $send = new SendMail;
    $subject  = utf8_decode("LOG CAMBIOS DE CONTRASEÑA");
    $body ="<p>Se han realizado cambios de contraseña del catalogo de colaboradores, revisar archivo adjunto para mas detalles.</p>";
    $send->PrepareMultipleNotice($subject,$body,$to,"",$file,"log_change_password.txt","","","noreply@braunhuerin.com.mx","ADMINISTRADOR DE PLATAFORMA",true);
}
$end = date("d-m-Y").' a las '.date('H:i:s').chr(13).chr(10);;
echo "Script ejecutado de  ".$timeStart." HASTA ".$end.'\n'.chr(13).chr(10);