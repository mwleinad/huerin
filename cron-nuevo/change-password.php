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

$sql =  "SELECT * FROM personal WHERE active='1' ORDER BY personalId ASC ";
$util->DB()->setQuery($sql);
$results =  $util->DB()->GetResult();
$mod=0;
$nomod=0;
foreach($results as $key =>$item){
    $fecha = strtotime('+3 month', strtotime($item['lastChangePassword']));
    $month3 = date('Y-m-d',$fecha);

    if(date('Y-m-d')<$month3){
        $nomod++;
        continue;
    }


    $cadena = $util->generateRandomString(6,true);

    $util->DB()->setQuery('UPDATE personal SET passwd="'.$cadena.'" WHERE personalId='.$item['personalId'].'');
    if($util->DB()->UpdateData()){
        $util->DB()->setQuery('UPDATE personal SET lastChangePassword="'.date('Y-m-d').'" WHERE personalId='.$item['personalId'].'');
        if($util->DB()->UpdateData()){
            $body="ESTIMADO USUARIO CON EL FIN DE MANTENER LA SEGURIDAD DE SUS DATOS Y DE LOS CLIENTES QUE SE ENCUENTRAN EN LA PLATAFORMA BAJO SU RESPONSABILIDAD 
                   SE HA REALIZADO EL CAMBIO DE CONTRASE&Ntilde;A DE SU CUENTA, CIERRE SU SESSION SI SE ENCUENTRA EN LA PLATAFORMAINGRESE ALA PLATAFORMA CON LOS SIGUIENTES DATOS <br>
                   USUARIO:".$item['username']." <br>
                   PASSWD:".$cadena." 
                   <br><br>
                   Este correo se creo automaticamente, favor de no responder.
                   ";
            $subject="CAMBIO DE CONTRASEÑA";
            $to = $item['email'];
            $toName= $item['name'];

            $sendmail = new SendMail;
            $sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileName.".xlsx", $attachment2, $fileName2,'noreply@braunhuerin.com.mx' , "ADMINISTRADOR DE PLATAFORMA") ;
            echo "Contraseña cambiada correctamente para ".$item['username']." y correo enviado a ".$item['email'];
            echo "<br>";
            $mod++;
        }
    }else{
        $nomod++;
    }
}
echo "total modificados  ".$mod;
echo "<br>";
echo "total no modificados  ".$nomod;