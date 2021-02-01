<?php
/**
 * Created by PhpStorm.
 * User: HECTOR CRUZ
 * Date: 01/03/2018
 * Time: 03:44 PM
 */

class Razon extends Contract
{
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
   public function sendComprobante33($id_comprobante,$showErrors=false,$from33=false, $reenvio = false){
       global $comprobante,$sendmail,$personal;
       $compInfo = $comprobante->GetInfoComprobante($id_comprobante);
       if(SEND_FACT_CUSTOMER=='SI'){
           $this->setContractId($compInfo['userId']);
           $contratoEmails =  $this->getEmailContractByArea('administracion',false);

           if(empty($contratoEmails))
               return false;
           $correos = array();
           foreach($contratoEmails['allEmails'] as $val){
               $correos[$val] = $from33==true?utf8_decode($contratoEmails["name"]):$contratoEmails["name"];
           }
       }else{
           $correos = array();
       }
       //encontrar el encargado de administracion
       $encargados = [];
       $contractRep = new ContractRep();
       $encargadosIds = $contractRep->encargadosCustomKey('departamentoId','personalId',$compInfo['userId']);
       if(key_exists(21,$encargadosIds))
            $encargados = $this->findEmailsAscByRespId($encargadosIds[21],[4,5,6]);

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
           $pdf = $pdfService->generate($id_empresa, $compInfo, 'email');
           if(!is_dir(DOC_ROOT."/empresas/$id_empresa/certificados/$id_rfc/facturas/pdf"))
               mkdir(DOC_ROOT."/empresas/$id_empresa/certificados/$id_rfc/facturas/pdf",0777,true);

           $enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo;
           file_put_contents($enlace, $pdf);
       } else {
           $archivo = $id_empresa.'_'.$serie.'_'.$folio.'.pdf';
           $enlace = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/pdf/'.$archivo;
       }

       $archivo_xml = "SIGN_".$id_empresa.'_'.$serie.'_'.$folio.'.xml';
       $enlace_xml  = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc.'/facturas/xml/'.$archivo_xml;

       /*** End Archivo PDF ***/

       if($compInfo['tiposComprobanteId']==10)
       {
           $fromName = "COBRANZA B&H";
           $subjectPrefix  = FROM_FACTURA === 'test'
               ? "COMPLEMENTO DE PAGO EN TEST CON FOLIO No. "
               : "COMPLEMENTO DE PAGO CON FOLIO No. ";
           $subject  = $subjectPrefix.$serie.$folio;


       }
       else{
           $fromName = "FACTURACION B&H";
           $subjectPrefix  = FROM_FACTURA === 'test'
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

       if($compInfo['tiposComprobanteId']==10)
       {
          $body .= "<br><br>Estimado Cliente: ".$contratoEmails["name"]."<br><br>";
          $body .= "Anexo encontrara su factura complemento emitida por BRAUN HUERIN SC , la cual contiene informacion adicional especifica en la que se detalla la cantidad que se paga e identifica la factura que se liquida.<br><br>";

       }else{
          $this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("select a.*,b.razonSocial as empresa from bankAccount a inner join rfc b ON a.rfcId=b.rfcId where a.rfcId = '".$compInfo["rfcId"]."' ");
          $bankData = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();
          $body .= "<br><br>Estimado Cliente: ".$contratoEmails["name"]."<br><br>";
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
           //$body .= "...::: NOTIFICACION AUTOMATICA --- NO RESPONDER :::...<br><br>";
       }else{
           $body .= "Gracias.<br>";
           $body .= "Favor de revisar el archivo adjunto para ver factura.\r\n";
           $body .= "<br><br>";
          // $body .= "...::: NOTIFICACION AUTOMATICA --- NO RESPONDER :::...<br><br>";
       }

        if($from33)
            $body = utf8_decode($body);

       if(!SEND_LOG_MOD){
           $correos = [];
           $encargados=[];
       }

       if($sendmail->PrepareMultiple(strtoupper($subject),$body,$correos,'',$attachment1,$file1,$attachment2,$file2,FROM_MAIL,$fromName,$encargados))
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
}
