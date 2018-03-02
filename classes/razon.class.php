<?php
/**
 * Created by PhpStorm.
 * User: HECTOR CRUZ
 * Date: 01/03/2018
 * Time: 03:44 PM
 */

class Razon extends Contract
{

   public function getEmailContractByArea($area=false,$mainCustomer=false){
       $emails=array();
       $this->Util()->DB()->setQuery(
           "SELECT
              *,
              contract.name AS name,
              contract.encargadoCuenta AS encargadoCuenta,
              contract.responsableCuenta AS responsableCuenta,
              contract.auxiliarCuenta AS auxiliarCuenta,
              customer.email as email
            FROM
              contract
            LEFT JOIN
              customer ON customer.customerId = contract.customerId
            LEFT JOIN
              regimen ON regimen.regimenId = contract.regimenId
            LEFT JOIN
              sociedad ON sociedad.sociedadId = contract.sociedadId
            WHERE
              contractId = '".$this->getContractId()."'"
       );

       $row = $this->Util()->DB()->GetRow();

       if(!$area)
           return false;

       switch($area)
       {
           case 'Administracion':
           case 'administracion':
                $emails = $this->Util()->ExplodeEmails($row['emailContactoAdministrativo']);
                if($mainCustomer)
                    @array_push($emails,$row['email']);
           break;

       }

       if(count($emails)<=0)
           return false;

       $row['allEmails'] = $emails;
       return $row;
   }
   public function sendComprobante33($id_comprobante,$showErrors=false,$from33=false){
       global $comprobante;
       global $sendmail;
       $compInfo = $comprobante->GetInfoComprobante($id_comprobante);
       $this->setContractId($compInfo['userId']);
       $contratoEmails =  $this->getEmailContractByArea('administracion',true);

       if(empty($contratoEmails['allEmails'])|| !$contratoEmails)
           return false;

       $correos = array();
       foreach($contratoEmails['allEmails'] as $val){

              $correos[$val] = $from33==true?utf8_decode($contratoEmails["name"]):$contratoEmails["name"];
       }
       $id_rfc = $compInfo['rfcId'];
       $id_empresa = $compInfo['empresaId'];
       $serie = $compInfo['serie'];
       $folio = $compInfo['folio'];

       if($compInfo['version'] == '3.3') {
           include_once(DOC_ROOT."/services/PdfService.php");
           include_once(DOC_ROOT."/services/QrService.php");
           include_once(DOC_ROOT."/services/XmlReaderService.php");

           $pdfService = new PdfService();
           $fileName = 'SIGN_'.$id_empresa.'_'.$serie.'_'.$folio;
           $archivo = $id_empresa.'_'.$serie.'_'.$folio.'.pdf';
           $pdf = $pdfService->generate($id_empresa, $fileName, 'email');
           $enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo;
           file_put_contents($enlace, $pdf);
       } else {
           $archivo = $id_empresa.'_'.$serie.'_'.$folio.'.pdf';
           $enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo;
       }

       $archivo_xml = "SIGN_".$id_empresa.'_'.$serie.'_'.$folio.'.xml';
       $enlace_xml  = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/xml/'.$archivo_xml;

       /*** End Archivo PDF ***/
       $fromName = "FACTURACION B&H";
       $subject  = 'Envio de Factura con Folio No. '.$serie.$folio;

       if (file_exists($enlace)) {
           $attachment1 = $enlace;
           $file1 = $archivo;
       } else {
           if($showErrors){
               $this->Util()->setError('', 'complete', "El documento no existe ");
               $this->Util()->PrintErrors();
           }
           return false;
       }
       if (file_exists($enlace_xml)) {
           $attachment2 = $enlace_xml;
           $file2 = $archivo_xml;
       } else {
           if($showErrors) {
               $this->Util()->setError('', 'complete', "El documento xml no existe ");
               $this->Util()->PrintErrors();
           }
           return false;
       }
       $body = " <pre>";
       if($compInfo["empresaId"] == 15)
       {
           $body .= "<br><br>Estimado Cliente: ".$contratoEmails["name"]."<br><br>";

           $body .= "Anexo encontrara su factura emitida por BRAUN HUERIN SC , la cual se solicita sea cubierta antes del día 22 del mes en curso, esto para evitar molestias de cobro.<br><br>";

           $body .= "DATOS DE PAGO:<br><br>";
           $body .= "Nombre    Braun Huerin S.C.<br><br>";
           $body .= "Banco     BBV Bancomer<br>";
           $body .= "Cuenta    0189768785<br>";
           $body .= "Clabe     012180-001897-687857<br><br>";
           $body .= "REALIZADO EL DEPÓSITO FAVOR DE ENVIAR EL COMPROBANTE, PARA PODER APLICARLO A SU CUENTA.<br><br>Quedo de usted.<br><br>Saludos cordiales!<br><br>FAVOR DE CONFIRMA LA RECEPCIÓN DE ESTE CORREO.<br><br>";
       }
       elseif($compInfo["empresaId"] == 20)
       {
           $body .= "Estimado Cliente: ".$contratoEmails["name"]."<br><br>";

           $body .= "Anexo encontrara su factura emitida por JACOBO BRAUN BRUCKMAN, la cual se solicita sea cubierta antes del día 27 del mes en curso, esto para evitar molestias de cobro.<br><br>";

           $body .= "DATOS DE PAGO:<br><br>";
           $body .= "Nombre    Jacobo  Braun Bruckman<br><br>";
           $body .= "Banco     Scotiabank<br>";
           $body .= "Cuenta    00105313691<br>";
           $body .= "Clabe      044180-001053-136916<br><br>";
           $body .= "REALIZADO EL DEPÓSITO FAVOR DE ENVIAR EL COMPROBANTE, PARA PODER APLICARLO A SU CUENTA.<br><br>Quedo de usted.<br><br>Saludos cordiales!<br><br>FAVOR DE CONFIRMA LA RECEPCIÓN DE ESTE CORREO.<br><br>";
       }
       elseif($compInfo["empresaId"] == 21)
       {
           $body .= "Estimado Cliente: ".$contratoEmails["name"]."<br><br>";

           $body .= "Anexo encontrara su factura emitida por BHSC CONTADORES SC , la cual se solicita sea cubierta antes del día 22 del mes en curso, esto para evitar molestias de cobro.<br><br>";

           $body .= "DATOS DE PAGO:<br><br>";

           $body .= "Nombre    BHSC CONTADORES S.C<br><br>";
           $body .= "Banco     INBURSA<br>";
           $body .= "Cuenta    5003-6325-646<br>";
           $body .= "Clabe     036-1805-0036-3256-464<br><br>";
           $body .= "REALIZADO EL DEPÓSITO FAVOR DE ENVIAR EL COMPROBANTE, PARA PODER APLICARLO A SU CUENTA.<br><br>Quedo de usted.<br><br>Saludos cordiales!<br><br>FAVOR DE CONFIRMA LA RECEPCIÓN DE ESTE CORREO.<br><br>";
       }
       $body .= "Gracias.<br>";
       $body .= "Favor de revisar el archivo adjunto para ver factura.\r\n";
       $body .= "<br><br>";
       $body .= "...::: NOTIFICACION AUTOMATICA --- NO RESPONDER :::...<br><br>";
        if($from33)
            $body = utf8_decode($body);

       if($sendmail->PrepareMultiple(strtoupper($subject),$body,$correos,'',$attachment1,$file1,$attachment2,$file2,FROM_MAIL,$fromName))
       {
           if($showErrors){
               $this->Util()->setError(20023, 'complete', "Has enviado el comprobante correctamente");
               $this->Util()->PrintErrors();
           }
           return true;
       }
       else
       {
           if($showErrors){
               $this->Util()->setError(20023, 'complete', 'Hubo un error al enviar el comprobante, el correo de la cuenta es correcto?');
               $this->Util()->PrintErrors();
           }
           return false;
       }
   }
}