<?php
/**
 * Created by PhpStorm.
 * User: HECTOR CRUZ
 * Date: 01/03/2018
 * Time: 03:44 PM
 */

class Razon extends Contract
{
    public function __construct()
    {
    }

    public function findEmailsAscByRespId($responsableId,$untilLevel){
       global $personal;
       $correos = [];
       $subordinados =  $personal->Jefes($responsableId);
       array_push($subordinados,$responsableId);
       $subordinados = array_unique($subordinados);
       $strFilter = " and a.personalId NOT IN(".IDBRAUN.",".IDHUERIN.") ";
       if($untilLevel||is_array($untilLevel)>0)
       {
           if(is_array($untilLevel)){
               if(count($untilLevel)>0)
                   $strFilter .="  and b.nivel in (".implode(",",$untilLevel).")";
           }
           else
               $strFilter .= " and b.nivel <='".$untilLevel."' ";
       }
       $sql = "select a.departamentoId,a.name,a.email,a.roleId,b.nivel from personal a 
                    inner join roles b on a.roleId=b.rolId
                    where a.active='1' and a.personalId IN (".implode(",",$subordinados).") $strFilter
                    ";
       $this->Util()->DB()->setQuery($sql);
       $responsables = $this->Util()->DB()->GetResult();
       if(!is_array($responsables))
           return $correos;

       foreach ($responsables as $key=>$value){
           if($this->Util()->ValidateEmail($value["email"]))
               $correos[$value["email"]] = $value["name"];
       }
       return $correos;
   }
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
           return $emails;

       switch(strtolower($area))
       {
           case 'Administracion':
           case 'administracion':
                $emails = $this->Util()->ExplodeEmails($row['emailContactoAdministrativo']);
                if(!is_array($emails))
                    $emails = array();
                if($mainCustomer)
                    @array_push($emails,$row['email']);
           break;
           case 'contabilidad':
               $emails = $this->Util()->ExplodeEmails($row['emailContactoContabilidad']);
               if(!is_array($emails))
                   $emails = array();
               if($mainCustomer)
                   @array_push($emails,$row['email']);
           break;
           case 'directivo':
               $emails = $this->Util()->ExplodeEmails($row['emailContactoDirectivo']);
               if(!is_array($emails))
                   $emails = array();
               if($mainCustomer)
                   @array_push($emails,$row['email']);
               break;
           case 'all':
               $emails = array();
               $emailsAdmin = $this->Util()->ExplodeEmails($row['emailContactoAdministrativo']);
               $emailsDirectivo = $this->Util()->ExplodeEmails($row['emailContactoDirectivo']);
               $emailsContabilidad = $this->Util()->ExplodeEmails($row['emailContactoContabilidad']);
               $emails = array_merge($emails,$emailsAdmin,$emailsDirectivo,$emailsContabilidad);
               if($mainCustomer)
                   @array_push($emails,$row['email']);
           break;

       }

       if(count($emails)<=0)
           return $emails;

       $row['allEmails'] = $emails;
       return $row;
   }

   public function enviarComprobante($comprobanteId, $tipo='Responsable CxC', $showErrors=true){
       global $comprobante, $sendmail;

       $correos = [];

       $cfdi =  $comprobante->GetInfoComprobante($comprobanteId);

       $contractRep = new ContractRep();
       $encargados = $contractRep->encargadosArea($cfdi['userId']);
       $encargadosFiltrados = [];

       foreach($encargados ?? [] as $encargado) {
           $respon = explode("@",$encargado['email']);
           $dominio = $respon[1] ?? '';
           if($encargado['departamentoId'] == 21 && $this->Util()->ValidateEmail($encargado['email']) && $dominio =='braunhuerin.com.mx')
               $encargadosFiltrados[] = $encargado;
       }
       $responsableCxc = $encargadosFiltrados[0] ?? [];

       switch ($tipo) {
           case 'Cliente':
               $this->setContractId($cfdi['userId']);
               $correosReceptor =  $this->getEmailContractByArea('administracion');
               foreach($correosReceptor['allEmails'] ?? [] as $val){
                   $correos[$val] = $correosReceptor["name"];
               }
           break;
           case 'Responsable CxC':
               if ($responsableCxc)
                    $correos[$responsableCxc["email"]] = $responsableCxc["name"];
           break;
       }

       $query = "select a.*,b.razonSocial as empresa 
                 from bankAccount a 
                 inner join rfc b ON a.rfcId=b.rfcId 
                 where a.rfcId = '".$cfdi["rfcId"]."'";

       $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery($query);
       $cuentaBancaria = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

       if (!$correos) {
           $this->Util()->setError('', 'error', "No se encontro a ningun remitente.");
           $this->Util()->PrintErrors();
           if ($tipo === 'Cliente' && $responsableCxc) {
               $texto  = "Ha ocurrido un error al intentar enviar la factura ".$cfdi['serie'].$cfdi['folio']." de la empresa ".$cfdi['razon_social'].".<br>";
               $texto .= count($correosReceptor['allEmails']) > 0
                   ? "Los siguientes correos fueron detectados: ". implode(",", $correosReceptor['allEmails'])."<br>"
                   : "No se encontraron correos configurados.<br>";
               $texto .= "Verifique y asegure que los correos ingresados en el formulario de la empresa sean validos o intente manualmente desde el modulo de Facturacion >> Consultar comprobantes.";
               $texto .= "<br><br>Este correo se genero de manera automatica, favor de no responder.";
               $sendmail->Prepare("Comprobante ".$cfdi['serie'].$cfdi['folio']." no enviado al cliente", $texto,$responsableCxc['email'],$responsableCxc['name']);
           }
           return false;
       }
       if(!$this->sendComprobante($cfdi, $correos, $cuentaBancaria, $showErrors, true,false,$tipo)) {

           if ($tipo === 'Cliente' && $responsableCxc) {
               $texto  = "Ha ocurrido un error al intentar enviar la factura ".$cfdi['serie'].$cfdi['folio']." de la empresa ".$cfdi['razon_social']." <br>";
               $texto .= count($correosReceptor['allEmails']) > 0
                   ? "Los siguientes correos fueron detectados: ". implode(",", $correosReceptor['allEmails'])."<br>"
                   : "No se encontraron correos configurados.<br>";
               $texto .= "Verifique y asegure que los correos ingresados en el formulario de la empresa sean validos o intente manualmente desde el modulo de Facturacion >> Consultar comprobantes.";
               $texto .= "<br><br>Este correo se genero de manera automatica, favor de no responder.";
               $sendmail->Prepare("Comprobante ".$cfdi['serie'].$cfdi['folio']." no enviado al cliente", $texto,$responsableCxc['email'],$responsableCxc['name']);
           }
           if($showErrors){
               $this->Util()->PrintErrors();
           }
           return false;
       }else {

           if($showErrors){
               $this->Util()->PrintErrors();
           }
           return true;
       }
   }

   public function sendComprobante33($id_comprobante, $showErrors=false, $from33=false, $reenvio = false) {

       global $comprobante,$sendmail;
       $compInfo = $comprobante->GetInfoComprobante($id_comprobante);

       $correos = [];
       if(SEND_FACT_CUSTOMER=='SI'){

           $this->setContractId($compInfo['userId']);
           $contrato =  $this->getEmailContractByArea('administracion');

           $correos = array();
           foreach($contrato['allEmails'] ?? [] as $val){
               $correos[$val] = $from33 ? utf8_decode($contrato["name"]):$contrato["name"];
           }
       }

       $contractRep = new ContractRep();
       $encargadosIds = $contractRep->encargadosCustomKey('departamentoId','personalId',$compInfo['userId']);
       if(key_exists(21,$encargadosIds)) {
           //$encargados = $this->findEmailsAscByRespId($encargadosIds[21], [4, 5, 6]);
           if($encargadosIds[21]) {

               $sql = "select name,email from personal where active='1' and personalId ='".$encargadosIds[21]."'";
               $this->Util()->DB()->setQuery($sql);
               $responsable = $this->Util()->DB()->GetRow();

               if(isset($responsable['email'])) {
                   $respon = explode("@",$responsable['email']);
                   $dominio = $respon[1] ?? '';
                   if ($this->Util()->ValidateEmail($responsable["email"]) && $dominio === 'braunhuerin.com.mx')
                       $correos[$responsable["email"]] = $responsable["name"];
               }
           }
       }

       $id_rfc = $compInfo['rfcId'];
       $id_empresa = $compInfo['empresaId'];
       $serie = $compInfo['serie'];
       $folio = $compInfo['folio'];
       if(in_array($compInfo['version'], ['3.3','4.0'])) {
           include_once(DOC_ROOT."/services/PdfService.php");
           include_once(DOC_ROOT."/services/QrService.php");
           include_once(DOC_ROOT."/services/XmlReaderService.php");

           $pdfService = new PdfService();
           $fileName = 'SIGN_'.$id_empresa.'_'.$serie.'_'.$folio;
           $archivo = $id_empresa.'_'.$serie.'_'.$folio.'.pdf';
           $pdf = $pdfService->generate($id_empresa, $compInfo, 'email');
           if(!is_dir(DOC_ROOT."/empresas/$id_empresa/certificados/$id_rfc/facturas/pdf"))
               mkdir(DOC_ROOT."/empresas/$id_empresa/certificados/$id_rfc/facturas/pdf",0777,true);

           if (!$pdf) {
               if($showErrors){
                   $this->Util()->setError('', 'error', "El documento no existe ");
                   $this->Util()->PrintErrors();
               }
               return false;
           }
           $enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo;
           file_put_contents($enlace, $pdf);
       } else {
           $archivo = $id_empresa.'_'.$serie.'_'.$folio.'.pdf';
           $enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo;
       }

       $archivo_xml = "SIGN_".$id_empresa.'_'.$serie.'_'.$folio.'.xml';
       $enlace_xml  = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/xml/'.$archivo_xml;

       /*** End Archivo PDF ***/

       if((int)$compInfo['tiposComprobanteId'] === 10)
       {
           $fromName = "COBRANZA B&H";
           $subjectPrefix  = PROJECT_STATUS === 'test'
               ? "COMPLEMENTO DE PAGO EN TEST CON FOLIO No. "
               : "COMPLEMENTO DE PAGO CON FOLIO No. ";
           $subject  = $subjectPrefix.$serie.$folio;


       } else {
           $fromName = "FACTURACION B&H";
           $subjectPrefix  = PROJECT_STATUS === 'test'
               ? "ENVIO DE FACTURA EN TEST CON FOLIO No. "
               : "ENVIO DE FACTURA CON FOLIO No. ";
           $subject  = $subjectPrefix.$serie.$folio;
       }
       $subject = $reenvio ? 'REENVIO_'.$subject : $subject;

       if (file_exists($enlace)) {
           $attachment1 = $enlace;
           $file1 = $archivo;
       } else {
           if($showErrors){
               $this->Util()->setError('', 'error', "El documento no existe ");
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

       if($compInfo['tiposComprobanteId']==10)
       {
          $body .= "<br><br>Estimado Cliente: ".$compInfo["razon_social"]."<br><br>";
          $body .= "Anexo encontrara su factura complemento emitida por BRAUN HUERIN SC , la cual contiene informacion adicional especifica en la que se detalla la cantidad que se paga e identifica la factura que se liquida.<br><br>";

       }else{
          $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("select a.*,b.razonSocial as empresa from bankAccount a inner join rfc b ON a.rfcId=b.rfcId where a.rfcId = '".$compInfo["rfcId"]."' ");
          $bankData = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
          $body .= "<br><br>Estimado Cliente: ".$compInfo["razon_social"]."<br><br>";
          $body .= "Anexo encontrara su factura emitida por ".$bankData["empresa"].", la cual se solicita sea cubierta dentro de los primeros 15 dias del mes, esto para evitar molestias de cobro.<br><br>";
          $body .= "DATOS DE PAGO:<br><br>";
          $body .= "Nombre    :".$bankData["empresa"]."<br><br>";
          $body .= "Banco     :".$bankData["name"]."<br>";
          $body .= "Cuenta    :".$bankData["account"]."<br>";
          $body .= "Clabe     :".$bankData["clabe"]."<br>";
          $body .= "REALIZADO EL DEPÓSITO FAVOR DE ENVIAR EL COMPROBANTE, PARA PODER APLICARLO A SU CUENTA.<br><br>Quedo de usted.<br><br>Saludos cordiales!<br><br>FAVOR DE CONFIRMA LA RECEPCIÓN DE ESTE CORREO.<br><br>";
       }
       if($compInfo['tiposComprobanteId']==10){
           $body .= "Quedo de usted. Saludos cordiales! Gracias.<br><br><br>";
           $body .= "Favor de revisar el archivo adjunto para ver comprobante.\r\n";
           $body .= "<br><br>";
       }else{
           $body .= "Gracias.<br>";
           $body .= "Favor de revisar el archivo adjunto para ver factura.\r\n";
           $body .= "<br><br>";
       }

        if($from33)
            $body = utf8_decode($body);

       if(!SEND_LOG_MOD){
           return false;
       }

       if($sendmail->PrepareMultiple(strtoupper($subject),$body,$correos,'',$attachment1,$file1,$attachment2,$file2,FROM_MAIL,$fromName))
       {
           if(is_file($attachment1))
               unlink($attachment1);

           $this->Util()->DB()->setQuery("UPDATE comprobante SET sent = 'si' WHERE comprobanteId='".$id_comprobante."' ");
           $this->Util()->DB()->UpdateData();
           if($showErrors){
               $this->Util()->setError(20023, 'complete', "Has enviado el comprobante correctamente");
               $this->Util()->PrintErrors();
           }
           return true;
       }
       else
       {
           if($showErrors){
               $this->Util()->setError(20023, 'error', 'Hubo un error al enviar el comprobante');
               $this->Util()->PrintErrors();
           }
           return false;
       }
   }

   public function sendComprobante($cfdi, $destinatarios, $cuentaBancaria, $showErrors=false, $from33=false, $reenvio=false, $tipo='Responsable CxC')
   {
       global $sendmail;

       $id_rfc = $cfdi['rfcId'];
       $id_empresa = $cfdi['empresaId'];
       $serie = $cfdi['serie'];
       $folio = $cfdi['folio'];

       if(in_array($cfdi['version'], ['3.3','4.0'])) {
           include_once(DOC_ROOT."/services/PdfService.php");
           include_once(DOC_ROOT."/services/QrService.php");
           include_once(DOC_ROOT."/services/XmlReaderService.php");

           $pdfService = new PdfService();
           $archivo = $id_empresa.'_'.$serie.'_'.$folio.'.pdf';
           $pdf = $pdfService->generate($id_empresa, $cfdi, 'email');
           if(!is_dir(DOC_ROOT."/empresas/$id_empresa/certificados/$id_rfc/facturas/pdf"))
               mkdir(DOC_ROOT."/empresas/$id_empresa/certificados/$id_rfc/facturas/pdf",0777,true);

           if (!$pdf) {
               if($showErrors){
                   $this->Util()->setError('', 'error', "XMl del comprobante no encontrado.");
               }
               return false;
           }
           $enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo;
           file_put_contents($enlace, $pdf);
       } else {
           $archivo = $id_empresa.'_'.$serie.'_'.$folio.'.pdf';
           $enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo;
       }

       $archivo_xml = "SIGN_".$id_empresa.'_'.$serie.'_'.$folio.'.xml';
       $enlace_xml  = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/xml/'.$archivo_xml;

       if((int)$cfdi['tiposComprobanteId'] === 10)
       {
           $fromName = "COBRANZA B&H";
           $subjectPrefix  = PROJECT_STATUS === 'test'
               ? "COMPLEMENTO DE PAGO EN TEST CON FOLIO No. "
               : "COMPLEMENTO DE PAGO CON FOLIO No. ";
           $subject  = $subjectPrefix.$serie.$folio;


       } else {
           $fromName = "FACTURACION B&H";
           $subjectPrefix  = PROJECT_STATUS === 'test'
               ? "ENVIO DE FACTURA EN TEST CON FOLIO No. "
               : "ENVIO DE FACTURA CON FOLIO No. ";
           $subject  = $subjectPrefix.$serie.$folio;
       }
       $subject = $reenvio ? 'REENVIO_'.$subject : $subject;

       if (file_exists($enlace)) {
           $attachment1 = $enlace;
           $file1 = $archivo;
       } else {
           if($showErrors){
               $this->Util()->setError('', 'error', "PDF del comprobante no encontrado.");
           }
           return false;
       }
       if (file_exists($enlace_xml)) {
           $attachment2 = $enlace_xml;
           $file2 = $archivo_xml;
       } else {
           if($showErrors) {
               $this->Util()->setError('', 'complete', "XML del comprobante no encontrado.");
           }
           return false;
       }
       $body = " <pre>";

       if($cfdi['tiposComprobanteId']==10)
       {
           $body .= "<br><br>Estimado Cliente: ".mb_strtoupper($cfdi["razon_social"])."<br><br>";
           $body .= "Anexo encontrara su factura complemento emitida por BRAUN HUERIN SC , la cual contiene informacion adicional especifica en la que se detalla la cantidad que se paga e identifica la factura que se liquida.<br><br>";

       }else{

           $body .= "<br><br>Estimado Cliente: ".$cfdi["razon_social"]."<br><br>";
           $body .= "Anexo encontrara su factura emitida por ".$cuentaBancaria["empresa"].", la cual se solicita sea cubierta dentro de los primeros 15 dias del mes, esto para evitar molestias de cobro.<br><br>";
           $body .= "DATOS DE PAGO:<br><br>";
           $body .= "Nombre    :".$cuentaBancaria["empresa"]."<br><br>";
           $body .= "Banco     :".$cuentaBancaria["name"]."<br>";
           $body .= "Cuenta    :".$cuentaBancaria["account"]."<br>";
           $body .= "Clabe     :".$cuentaBancaria["clabe"]."<br>";
           $body .= "REALIZADO EL DEPÓSITO FAVOR DE ENVIAR EL COMPROBANTE, PARA PODER APLICARLO A SU CUENTA.<br><br>Quedo de usted.<br><br>Saludos cordiales!<br><br>FAVOR DE CONFIRMA LA RECEPCIÓN DE ESTE CORREO.<br><br>";
       }
       if($cfdi['tiposComprobanteId']==10){
           $body .= "Quedo de usted. Saludos cordiales! Gracias.<br><br><br>";
           $body .= "Favor de revisar el archivo adjunto para ver comprobante.\r\n";
           $body .= "<br><br>";
       }else{
           $body .= "<br>";

           $body .= "Si tiene alguna duda o problema en visualizar los archivos adjuntos, no dude en contactarnos.\r\n";
       }

       if($from33)
           $body = utf8_decode($body);

       if($sendmail->PrepareMultiple(strtoupper($subject),$body,$destinatarios,'',$attachment1,$file1,$attachment2,$file2,FROM_MAIL,$fromName, [], $tipo === 'Cliente'))
       {
           if(is_file($attachment1))
               unlink($attachment1);

           $query = $tipo === 'Cliente'
               ? "UPDATE comprobante SET sentCliente = 'Si' WHERE comprobanteId='".$cfdi['comprobanteId']."'"
               : "UPDATE comprobante SET sent = 'si' WHERE comprobanteId='".$cfdi['comprobanteId']."'";

           $this->Util()->DB()->setQuery($query);
           $this->Util()->DB()->UpdateData();

           if($showErrors){
               $this->Util()->setError(20023, 'complete', "Has enviado el comprobante correctamente");
           }
           return true;
       }
       else
       {
           if($showErrors){
               $this->Util()->setError(20023, 'error', 'Ha ocurrido un error al enviar el comprobante, intente nuevamente.');
           }
           return false;
       }
   }
}
