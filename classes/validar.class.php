<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 07/06/2018
 * Time: 05:02 PM
 */

class Validar extends Main
{
    public function ValidateLayoutCustomerRazon($FILES){
        $file_temp = $FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila=1;
        while(($row=fgetcsv($fp,4096,","))==true) {
            if(count($row)!=45)
            {
                $this->Util()->setError(0,'error','Archivo no valido');
                break;
            }
            if($fila==1)
            {
                $fila++;
                continue;
            }
            //comprobar que el cliente se encuentra en el sistema
            $sqlc = "SELECT customerId FROM customer  WHERE customerId='".$row[0]."' ";
            $this->Util()->DB()->setQuery($sqlc);
            $findCustomer = $this->Util()->DB()->GetRow();
            if(empty($findCustomer))
            {
                $this->Util()->setError(0,'error','El cliente de la fila '.$fila." no se encuentra registrado");
                break;
            }
            //campos que no deberan estar vacio
            //nombre de cliente
            if($row[2]=="")
            {
                $this->Util()->setError(0,'error','Falta nombre del cliente  en la fila '.$fila);
                break;
            }
            //fecha de alta del cliente
            if($row[7]==""){
                $this->Util()->setError(0,'error','Falta fecha de alta del cliente en la fila '.$fila);
                break;
            }

            //nombre de razon social
            if($row[10]==""){
                $this->Util()->setError(0,'error','Falta razon social en la fila '.$fila);
                break;
            }
            //tipo de persona
            if($row[12]==""){
                $this->Util()->setError(0,'error','Falta tipo de persona en la fila '.$fila);
                break;
            }
            if($row[13]==""){
                $this->Util()->setError(0,'error','Falta RFC en la fila '.$fila);
                break;
            }
            if($row[14]==""){
                $this->Util()->setError(0,'error','Falta regimen fiscal en la fila '.$fila);
                break;
            }
            if($row[16]==""){
                $this->Util()->setError(0,'error','Falta nombre comercial en la fila '.$fila);
                break;
            }
            if($row[17]==""){
                $this->Util()->setError(0,'error','Falta direccion comercial  en la fila '.$fila);
                break;
            }
            /* datos de contacto no es obligatorio
            if($row[19]==""){
                $this->Util()->setError(0,'error','Falta nombre contacto administrativo '.$fila);
                break;
            }
            if($row[20]==""){
                $this->Util()->setError(0,'error','Falta email contacto administrativo '.$fila);
                break;
            }
            if($row[21]==""){
                $this->Util()->setError(0,'error','Falta telefono contacto administrativo '.$fila);
                break;
            }
            if($row[22]==""){
                $this->Util()->setError(0,'error','Falta nombre contacto contabilidad '.$fila);
                break;
            }
            if($row[23]==""){
                $this->Util()->setError(0,'error','Falta email contacto contabilidad '.$fila);
                break;
            }
            if($row[24]==""){
                $this->Util()->setError(0,'error','Falta telefono contacto contabilidad '.$fila);
                break;
            }
            if($row[25]==""){
                $this->Util()->setError(0,'error','Falta nombre contacto directivo '.$fila);
                break;
            }
            if($row[26]==""){
                $this->Util()->setError(0,'error','Falta email contacto directivo '.$fila);
                break;
            }
            if($row[27]==""){
                $this->Util()->setError(0,'error','Falta telefono contacto directivo '.$fila);
                break;
            }
            if($row[28]==""){
                $this->Util()->setError(0,'error','Falta celular contacto directivo '.$fila);
                break;
            }
            */
            if($row[29]==""){
                $this->Util()->setError(0,'error','Falta clave CIEC en fila(Usar NO APLICA en caso de no existir) '.$fila);
                break;
            }
            if($row[30]==""){
                $this->Util()->setError(0,'error','Falta clave FIEL en fila(Usar NO APLICA en caso de no existir) '.$fila);
                break;
            }
            if($row[31]==""){
                $this->Util()->setError(0,'error','Falta clave IDSE en fila(Usar NO APLICA en caso de no existir) '.$fila);
                break;
            }
            if($row[32]==""){
                $this->Util()->setError(0,'error','Falta clave ISN en fila(Usar NO APLICA en caso de no existir) '.$fila);
                break;
            }
            if($row[33]==""){
                $this->Util()->setError(0,'error','Fatla facturador en fila(Usar NO APLICA en caso de no existir) '.$fila);
                break;
            }
            if($row[34]==""){
                $this->Util()->setError(0,'error','Falta metodo de pago  en fila(Usar NO APLICA en caso de no existir) '.$fila);
                break;
            }
            //comprobar que los encargados esten dados de alta siempre y cuando no este vacio
            if($row[38]!="" and $row[38]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower($row[38])."'");
                $idCont=$this->Util()->DB()->GetSingle();
                if(!$idCont)
                {
                    $this->Util()->setError(0,'error','Responsable de contabilidad de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[39]!="" and $row[39]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[39]))."' ");
                $idNom=$this->Util()->DB()->GetSingle();
                if(!$idNom)
                {
                    $this->Util()->setError(0,'error','Responsable de nomina de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[40]!="" and $row[40]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[40]))."'");
                $idAdmin=$this->Util()->DB()->GetSingle();
                if(!$idAdmin)
                {
                    $this->Util()->setError(0,'error','Responsable de administracion de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[41]!="" and $row[41]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[41]))."'");
                $idJur=$this->Util()->DB()->GetSingle();
                if(!$idJur)
                {
                    $this->Util()->setError(0,'error','Responsable de juridico de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[42]!="" and $row[42]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[42]))."'");
                $idImss=$this->Util()->DB()->GetSingle();
                if(!$idImss)
                {
                    $this->Util()->setError(0,'error','Responsable de imss de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[43]!="" and $row[43]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[43]))."'");
                $idMen=$this->Util()->DB()->GetSingle();
                if(!$idMen)
                {
                    $this->Util()->setError(0,'error','Responsable de mensajeria de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[44]!="" and $row[44]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[44]))."'");
                $idAud=$this->Util()->DB()->GetSingle();
                if(!$idAud)
                {
                    $this->Util()->setError(0,'error','Responsable de auditoria de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            //comprobar que el regimen existe y sea exclusivamente del tipo de persona seleccionado
            $this->Util()->DB()->setQuery("SELECT regimenId FROM  regimen WHERE lower(replace(nombreRegimen,' ',''))='".mb_strtolower(str_replace(' ','',$row[14]))."' and lower(replace(tipoDePersona,' ',''))='".mb_strtolower(str_replace(' ','',$row[12]))."' ");
            $regimenId=$this->Util()->DB()->GetSingle();
            if(!$regimenId)
            {
                $this->Util()->setError(0,'error','Tipo de regimen en la fila '.$fila.' no existe o no pertenece al tipo de persona seleccionado ');
                break;
            }
            //comprobar fecha de alta del cliente si es valido
            if(!$this->Util()->isValidateDate($row[7],'d/m/Y')){
                $this->Util()->setError(0,'error','La fecha de alta del cliente en la fila '.$fila.' no es valido ');
                break;
            }
            //comprobar que id del contrato pertenezca al cliente y que el cliente le pertenezca el contrato
            $sql1 = "SELECT a.contractId,b.customerId FROM contract a INNER JOIN customer b ON a.customerId=b.customerId AND b.customerId='".$row[0]."' WHERE a.contractId='".$row[1]."' ";
            $this->Util()->DB()->setQuery($sql1);
            $findData = $this->Util()->DB()->GetRow();
            if(empty($findData))
            {
                $this->Util()->setError(0,'error','No se encontro razon social del cliente o no coincide la informacion en la fila '.$fila);
                break;
            }
            //si cambia nombre de razon  comprobar que no exista en otro registro(existen duplicados de razones por cliente por lo que se quita la validacion del contrato que sea solo por cliente)
            //(hay duplicados de razones sociales, ignorar esta condicion se hara depues de haber hecho la correccion de eso)
            /*$sql2 = "SELECT contractId FROM contract WHERE name='".$row[10]."' AND customerId!='".$row[0]."' AND contractId!='".$row[1]."' ";
            $db->setQuery($sql2);
            $findRazon = $db->GetRow();
            if(!empty($findRazon))
            {
                $this->Util()->setError(0,'error',"La razon social ".trim($row[10])." de la fila ".$fila." ya se encuentra en uso con el cliente con ID=".$findRazon['contractId']);
                $cad .="La razon social ".trim($row[10])." de la fila ".$fila." ya se encuentra en uso con el cliente con ID=".$findRazon['contractId'].chr(13).chr(10);
            }*/
            $fila++;
        }
        fclose($fp);
    if($this->Util()->PrintErrors())
     return false;

    return true;
   }
   public function ValidateLayoutNewContract($FILES){
       $file_temp = $FILES['file']['tmp_name'];
       $fp = fopen($file_temp,'r');
       $fila=1;
       while(($row=fgetcsv($fp,4096,","))==true) {

           if(count($row)!=41)
           {
               $this->Util()->setError(0,'error','Archivo no valido');
               break;
           }
           if ($fila == 1) {
               $fila++;
               continue;
           }
           //nombre de cliente
           if($row[0]=="")
           {
               $this->Util()->setError(0,'error','Falta nombre del cliente  en la fila '.$fila);
               break;
           }
           $sql = "SELECT customerId FROM customer WHERE nameContact='".$row[0]."' ";
           $this->Util()->DB()->setQuery($sql);
           $customerId = $this->Util()->DB()->GetSingle();
           if(!$customerId)
           {
               $this->Util()->setError(0,'error','El cliente de la fila '.$fila." no se encuentra registrado verificar nombre del cliente");
               break;
           }
           //nombre de razon social
           if($row[1]==""){
               $this->Util()->setError(0,'error','Falta razon social en la fila '.$fila);
               break;
           }
           //validar si el nombre de la razon social no este en uso
           $sql = "SELECT contractId FROM contract WHERE name='".utf8_encode(trim($row[1]))."' ";
           $this->Util()->DB()->setQuery($sql);
           $findContractId = $this->Util()->DB()->GetSingle();
           if($findContractId)
           {
               $this->Util()->setError(0,'error','Nombre de razon social de la fila  '.$fila." ya se encuentra en uso");
               break;
           }
           //tipo persona
           if($row[2]==""){
               $this->Util()->setError(0,'error','Falta tipo persona en la fila '.$fila);
               break;
           }
           //facturador
           if($row[3]==""){
               $this->Util()->setError(0,'error','Falta facturador en la fila '.$fila);
               break;
           }
           //RFC
           if($row[4]==""){
               $this->Util()->setError(0,'error','Falta RFC en la fila '.$fila);
               break;
           }
           //validar si el rfc no se encuntra en uso
           $sql = "SELECT contractId FROM contract WHERE rfc='".trim($row[4])."' ";
           $this->Util()->DB()->setQuery($sql);
           $findRfcId = $this->Util()->DB()->GetSingle();
           if($findRfcId)
           {
               $this->Util()->setError(0,'error','El RFC de la fila  '.$fila." ya se encuentra en uso");
               break;
           }
           //nombre comercial
           if($row[5]==""){
               $this->Util()->setError(0,'error','Falta nombre comercial en la fila '.$fila);
               break;
           }
           if($row[6]==""){
               $this->Util()->setError(0,'error','Falta calle en la fila '.$fila);
               break;
           }
           if($row[9]==""){
               $this->Util()->setError(0,'error','Falta colonia en la fila '.$fila);
               break;
           }
           if($row[10]==""){
               $this->Util()->setError(0,'error','Falta municipio en la fila '.$fila);
               break;
           }
           if($row[11]==""){
               $this->Util()->setError(0,'error','Falta estado en la fila '.$fila);
               break;
           }
           if($row[12]==""){
               $this->Util()->setError(0,'error','Falta pais en la fila '.$fila);
               break;
           }
           if($row[13]==""){
               $this->Util()->setError(0,'error','Falta codigo postal en la fila '.$fila);
               break;
           }
           if($row[14]==""){
               $this->Util()->setError(0,'error','Falta metodo de pago en la fila '.$fila);
               break;
           }
           if($row[18]==""){
               $this->Util()->setError(0,'error','Falta contacto administrativo en la fila '.$fila);
               break;
           }
           if($row[19]==""){
               $this->Util()->setError(0,'error','Falta email administrativo en la fila '.$fila);
               break;
           }
           if($row[20]==""){
               $this->Util()->setError(0,'error','Falta tel. administrativo en la fila '.$fila);
               break;
           }
           if($row[21]==""){
               $this->Util()->setError(0,'error','Falta contacto contabilidad en la fila '.$fila);
               break;
           }
           if($row[22]==""){
               $this->Util()->setError(0,'error','Falta email contabilidad en la fila '.$fila);
               break;
           }
           if($row[23]==""){
               $this->Util()->setError(0,'error','Falta tel. contabilidad en la fila '.$fila);
               break;
           }
           if($row[24]==""){
               $this->Util()->setError(0,'error','Falta contacto directivo en la fila '.$fila);
               break;
           }
           if($row[25]==""){
               $this->Util()->setError(0,'error','Falta email directivo en la fila '.$fila);
               break;
           }
           if($row[26]==""){
               $this->Util()->setError(0,'error','Falta tel. directivo en la fila '.$fila);
               break;
           }
           if($row[27]==""){
               $this->Util()->setError(0,'error','Falta clave FIEL en la fila '.$fila);
               break;
           }
           if($row[28]==""){
               $this->Util()->setError(0,'error','Falta clave CIEC en la fila '.$fila);
               break;
           }
           if($row[29]==""){
               $this->Util()->setError(0,'error','Falta clave IDSE en la fila '.$fila);
               break;
           }
           if($row[30]==""){
               $this->Util()->setError(0,'error','Falta clave ISN en la fila '.$fila);
               break;
           }
           if($row[31]==""){
               $this->Util()->setError(0,'error','Falta cel. directivo en la fila '.$fila);
               break;
           }
           if($row[39]==""){
               $this->Util()->setError(0,'error','Falta tipo de regimen en la fila '.$fila);
               break;
           }
           if(mb_strtolower($row[2])=="persona moral"){
               if($row[40]==""){
                   $this->Util()->setError(0,'error','Falta tipo de sociedad en la fila '.$fila);
                   break;
               }
               $this->Util()->DB()->setQuery("SELECT sociedadId FROM  sociedad WHERE lower(replace(nombreSociedad,' ',''))='".mb_strtolower(str_replace(' ','',$row[40]))."' ");
               $sociedadId=$this->Util()->DB()->GetSingle();
               if(!$sociedadId)
               {
                   $this->Util()->setError(0,'error','Tipo de sociedad en la fila '.$fila.' no existe ');
                   break;
               }
           }
           //comprobar que el regimen existe y sea exclusivamente del tipo de persona seleccionado
           $this->Util()->DB()->setQuery("SELECT regimenId FROM  regimen WHERE lower(replace(nombreRegimen,' ',''))='".mb_strtolower(str_replace(' ','',$row[39]))."' and lower(replace(tipoDePersona,' ',''))='".mb_strtolower(str_replace(' ','',$row[2]))."' ");
           $regimenId=$this->Util()->DB()->GetSingle();
           if(!$regimenId)
           {
               $this->Util()->setError(0,'error','Tipo de regimen en la fila '.$fila.' no existe o no pertenece al tipo de persona');
               break;
           }
           //comprobar que los encargados esten dados de alta siempre y cuando no este vacio
           if($row[32]!="" and $row[32]!="--" ){
               $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[32]))."'");
               $idCont=$this->Util()->DB()->GetSingle();
               if(!$idCont)
               {
                   $this->Util()->setError(0,'error','Responsable de contabilidad de la fila '.$fila.' no se encuentra dado de alta ');
                   break;
               }
           }
           if($row[33]!="" and $row[33]!="--" ){
               $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[33]))."'");
               $idNom=$this->Util()->DB()->GetSingle();
               if(!$idNom)
               {
                   $this->Util()->setError(0,'error','Responsable de nomina de la fila '.$fila.' no se encuentra dado de alta ');
                   break;
               }
           }
           if($row[34]!="" and $row[34]!="--" ){
               $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[34]))."'");
               $idAdmin=$this->Util()->DB()->GetSingle();
               if(!$idAdmin)
               {
                   $this->Util()->setError(0,'error','Responsable de administracion de la fila '.$fila.' no se encuentra dado de alta ');
                   break;
               }
           }
           if($row[35]!="" and $row[35]!="--" ){
               $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[35]))."'");
               $idJur=$this->Util()->DB()->GetSingle();
               if(!$idJur)
               {
                   $this->Util()->setError(0,'error','Responsable de juridico de la fila '.$fila.' no se encuentra dado de alta ');
                   break;
               }
           }
           if($row[36]!="" and $row[36]!="--" ){
               $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[36]))."'");
               $idImss=$this->Util()->DB()->GetSingle();
               if(!$idImss)
               {
                   $this->Util()->setError(0,'error','Responsable de imss de la fila '.$fila.' no se encuentra dado de alta ');
                   break;
               }
           }
           if($row[37]!="" and $row[37]!="--" ){
               $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[37]))."'");
               $idMen=$this->Util()->DB()->GetSingle();
               if(!$idMen)
               {
                   $this->Util()->setError(0,'error','Responsable de mensajeria de la fila '.$fila.' no se encuentra dado de alta ');
                   break;
               }
           }
           if($row[38]!="" and $row[38]!="--" ){
               $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[38]))."'");
               $idAud=$this->Util()->DB()->GetSingle();
               if(!$idAud)
               {
                   $this->Util()->setError(0,'error','Responsable de auditoria de la fila '.$fila.' no se encuentra dado de alta ');
                   break;
               }
           }

           $fila++;
       }
       fclose($fp);
       if($this->Util()->PrintErrors())
           return false;

       return true;

   }
    public function ValidateLayoutOnlyEncargado($FILES){
        $file_temp = $FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila=1;
        while(($row=fgetcsv($fp,4096,","))==true) {
            if(count($row)!=11)
            {
                $this->Util()->setError(0,'error','Archivo no valido');
                break;
            }
            if($fila==1)
            {
                $fila++;
                continue;
            }
            //comprobar que el cliente se encuentra en el sistema
            $sqlc = "SELECT customerId FROM customer  WHERE customerId='".$row[0]."' ";
            $this->Util()->DB()->setQuery($sqlc);
            $findCustomer = $this->Util()->DB()->GetRow();
            if(empty($findCustomer))
            {
                $this->Util()->setError(0,'error','El cliente de la fila '.$fila." no se encuentra registrado");
                break;
            }
            //campos que no deberan estar vacio
            //nombre de cliente
            if($row[2]=="")
            {
                $this->Util()->setError(0,'error','Falta nombre del cliente  en la fila '.$fila);
                break;
            }
            //nombre de razon social
            if($row[3]==""){
                $this->Util()->setError(0,'error','Falta razon social en la fila '.$fila);
                break;
            }
            //comprobar que los encargados esten dados de alta siempre y cuando no este vacio
            if($row[4]!="" and $row[4]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(trim(char(9) from trim(name)))='".mb_strtolower($row[4])."'");
                $idCont=$this->Util()->DB()->GetSingle();
                if(!$idCont)
                {
                    $this->Util()->setError(0,'error','Responsable de contabilidad de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[5]!="" and $row[5]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(trim(char(9) from trim(name)))='".mb_strtolower(trim($row[5]))."' ");
                $idNom=$this->Util()->DB()->GetSingle();
                if(!$idNom)
                {
                    $this->Util()->setError(0,'error','Responsable de nomina de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[6]!="" and $row[6]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(trim(char(9) from trim(name)))='".mb_strtolower(trim($row[6]))."'");
                $idAdmin=$this->Util()->DB()->GetSingle();
                if(!$idAdmin)
                {
                    $this->Util()->setError(0,'error','Responsable de administracion de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[7]!="" and $row[7]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(trim(char(9) from trim(name)))='".mb_strtolower(trim($row[7]))."'");
                $idJur=$this->Util()->DB()->GetSingle();
                if(!$idJur)
                {
                    $this->Util()->setError(0,'error','Responsable de juridico de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[8]!="" and $row[8]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(trim(char(9) from trim(name)))='".mb_strtolower(trim($row[8]))."'");
                $idImss=$this->Util()->DB()->GetSingle();
                if(!$idImss)
                {
                    $this->Util()->setError(0,'error','Responsable de imss de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[9]!="" and $row[9]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(trim(char(9) from trim(name)))='".mb_strtolower(trim($row[9]))."'");
                $idMen=$this->Util()->DB()->GetSingle();
                if(!$idMen)
                {
                    $this->Util()->setError(0,'error','Responsable de mensajeria de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[10]!="" and $row[10]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(trim(char(9) from trim(name)))='".mb_strtolower(trim($row[10]))."'");
                $idAud=$this->Util()->DB()->GetSingle();
                if(!$idAud)
                {
                    $this->Util()->setError(0,'error','Responsable de auditoria de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            //comprobar que id del contrato pertenezca al cliente y que el cliente le pertenezca el contrato
            $sql1 = "SELECT a.contractId,b.customerId FROM contract a INNER JOIN customer b ON a.customerId=b.customerId AND b.customerId='".$row[0]."' WHERE a.contractId='".$row[1]."' ";
            $this->Util()->DB()->setQuery($sql1);
            $findData = $this->Util()->DB()->GetRow();
            if(empty($findData))
            {
                $this->Util()->setError(0,'error','No se encontro razon social del cliente o no coincide la informacion en la fila '.$fila);
                break;
            }
            //si cambia nombre de razon  comprobar que no exista en otro registro(existen duplicados de razones por cliente por lo que se quita la validacion del contrato que sea solo por cliente)
            //(hay duplicados de razones sociales, ignorar esta condicion se hara depues de haber hecho la correccion de eso)
            /*$sql2 = "SELECT contractId FROM contract WHERE name='".$row[10]."' AND customerId!='".$row[0]."' AND contractId!='".$row[1]."' ";
            $db->setQuery($sql2);
            $findRazon = $db->GetRow();
            if(!empty($findRazon))
            {
                $this->Util()->setError(0,'error',"La razon social ".trim($row[10])." de la fila ".$fila." ya se encuentra en uso con el cliente con ID=".$findRazon['contractId']);
                $cad .="La razon social ".trim($row[10])." de la fila ".$fila." ya se encuentra en uso con el cliente con ID=".$findRazon['contractId'].chr(13).chr(10);
            }*/
            $fila++;
        }
        fclose($fp);
        if($this->Util()->PrintErrors())
            return false;

        return true;
    }
    /*
     * Comprobar solo los campos obligatorioa y encargados
     */
    public function ValidateLayoutContractRebuild($FILES){
        $file_temp = $FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila=1;
        while(($row=fgetcsv($fp,4096,","))==true) {
            if(count($row)!=45)
            {
                $this->Util()->setError(0,'error','Archivo no valido');
                break;
            }
            if($fila==1)
            {
                $fila++;
                continue;
            }
            //comprobar que el cliente se encuentra en el sistema
            $sqlc = "SELECT customerId FROM customer  WHERE customerId='".$row[0]."' ";
            $this->Util()->DB()->setQuery($sqlc);
            $findCustomer = $this->Util()->DB()->GetRow();
            if(empty($findCustomer))
            {
                $this->Util()->setError(0,'error','El cliente de la fila '.$fila." no se encuentra registrado");
               // break;
            }
            //campos que no deberan estar vacio
            //nombre de cliente
            if($row[2]=="")
            {
                $this->Util()->setError(0,'error','Falta nombre del cliente  en la fila '.$fila);
               // break;
            }
            //fecha de alta del cliente
            if($row[7]==""){
                $this->Util()->setError(0,'error','Falta fecha de alta del cliente en la fila '.$fila);
               // break;
            }

            //nombre de razon social
            if($row[10]==""){
                $this->Util()->setError(0,'error','Falta razon social en la fila '.$fila);
                break;
            }
            //tipo de persona
            if($row[12]==""){
                $this->Util()->setError(0,'error','Falta tipo de persona en la fila '.$fila);
              //  break;
            }
            if($row[13]==""){
                $this->Util()->setError(0,'error','Falta RFC en la fila '.$fila);
                //break;
            }
            if($row[14]==""){
                $this->Util()->setError(0,'error','Falta regimen fiscal en la fila '.$fila);
               // break;
            }
            if($row[16]==""){
                $this->Util()->setError(0,'error','Falta nombre comercial en la fila '.$fila);
                //break;
            }
            if($row[17]==""){
                $this->Util()->setError(0,'error','Falta direccion comercial  en la fila '.$fila);
               // break;
            }
            /* datos de contacto no es obligatorio
            if($row[19]==""){
                $this->Util()->setError(0,'error','Falta nombre contacto administrativo '.$fila);
                break;
            }
            if($row[20]==""){
                $this->Util()->setError(0,'error','Falta email contacto administrativo '.$fila);
                break;
            }
            if($row[21]==""){
                $this->Util()->setError(0,'error','Falta telefono contacto administrativo '.$fila);
                break;
            }
            if($row[22]==""){
                $this->Util()->setError(0,'error','Falta nombre contacto contabilidad '.$fila);
                break;
            }
            if($row[23]==""){
                $this->Util()->setError(0,'error','Falta email contacto contabilidad '.$fila);
                break;
            }
            if($row[24]==""){
                $this->Util()->setError(0,'error','Falta telefono contacto contabilidad '.$fila);
                break;
            }
            if($row[25]==""){
                $this->Util()->setError(0,'error','Falta nombre contacto directivo '.$fila);
                break;
            }
            if($row[26]==""){
                $this->Util()->setError(0,'error','Falta email contacto directivo '.$fila);
                break;
            }
            if($row[27]==""){
                $this->Util()->setError(0,'error','Falta telefono contacto directivo '.$fila);
                break;
            }
            if($row[28]==""){
                $this->Util()->setError(0,'error','Falta celular contacto directivo '.$fila);
                break;
            }
            */
            if($row[29]==""){
                $this->Util()->setError(0,'error','Falta clave CIEC en fila(Usar NO APLICA en caso de no existir) '.$fila);
                //break;
            }
            if($row[30]==""){
                $this->Util()->setError(0,'error','Falta clave FIEL en fila(Usar NO APLICA en caso de no existir) '.$fila);
                //break;
            }
            if($row[31]==""){
                $this->Util()->setError(0,'error','Falta clave IDSE en fila(Usar NO APLICA en caso de no existir) '.$fila);
               // break;
            }
            if($row[32]==""){
                $this->Util()->setError(0,'error','Falta clave ISN en fila(Usar NO APLICA en caso de no existir) '.$fila);
               // break;
            }
            if($row[33]==""){
                $this->Util()->setError(0,'error','Fatla facturador en fila(Usar NO APLICA en caso de no existir) '.$fila);
                //break;
            }
            if($row[34]==""){
                $this->Util()->setError(0,'error','Falta metodo de pago  en fila(Usar NO APLICA en caso de no existir) '.$fila);
               // break;
            }
            //comprobar que los encargados esten dados de alta siempre y cuando no este vacio
            if($row[38]!="" and $row[38]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower($row[38])."'");
                $idCont=$this->Util()->DB()->GetSingle();
                if(!$idCont)
                {
                    $this->Util()->setError(0,'error','Responsable de contabilidad de la fila '.$fila.' no se encuentra dado de alta ');
                   // break;
                }
            }
            if($row[39]!="" and $row[39]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[39]))."' ");
                $idNom=$this->Util()->DB()->GetSingle();
                if(!$idNom)
                {
                    $this->Util()->setError(0,'error','Responsable de nomina de la fila '.$fila.' no se encuentra dado de alta ');
                   // break;
                }
            }
            if($row[40]!="" and $row[40]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[40]))."'");
                $idAdmin=$this->Util()->DB()->GetSingle();
                if(!$idAdmin)
                {
                    $this->Util()->setError(0,'error','Responsable de administracion de la fila '.$fila.' no se encuentra dado de alta ');
                   // break;
                }
            }
            if($row[41]!="" and $row[41]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[41]))."'");
                $idJur=$this->Util()->DB()->GetSingle();
                if(!$idJur)
                {
                    $this->Util()->setError(0,'error','Responsable de juridico de la fila '.$fila.' no se encuentra dado de alta ');
                   // break;
                }
            }
            if($row[42]!="" and $row[42]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[42]))."'");
                $idImss=$this->Util()->DB()->GetSingle();
                if(!$idImss)
                {
                    $this->Util()->setError(0,'error','Responsable de imss de la fila '.$fila.' no se encuentra dado de alta ');
                   // break;
                }
            }
            if($row[44]!="" and $row[44]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".mb_strtolower(trim($row[44]))."'");
                $idAud=$this->Util()->DB()->GetSingle();
                if(!$idAud)
                {
                    $this->Util()->setError(0,'error','Responsable de auditoria de la fila '.$fila.' no se encuentra dado de alta ');
                   //break;
                }
            }
            //comprobar que el regimen existe y sea exclusivamente del tipo de persona seleccionado
            $this->Util()->DB()->setQuery("SELECT regimenId FROM  regimen WHERE lower(replace(nombreRegimen,' ',''))='".mb_strtolower(str_replace(' ','',$row[14]))."' and lower(replace(tipoDePersona,' ',''))='".mb_strtolower(str_replace(' ','',$row[12]))."' ");
           // echo  $this->Util()->DB()->getQuery();
            $regimenId=$this->Util()->DB()->GetSingle();
            if(!$regimenId)
            {
                $this->Util()->setError(0,'error','Tipo de regimen en la fila '.$fila.' no existe o no pertenece al tipo de persona seleccionado ');
               // break;
            }
            //comprobar fecha de alta del cliente si es valido
            if(!$this->Util()->isValidateDate($row[7],'d/m/Y')){
                $this->Util()->setError(0,'error','La fecha de alta del cliente en la fila '.$fila.' no es valido ');
                //break;
            }
            //comprobar que el id del contrato no esta siendo usado en otro cliente
            $sql1 = "SELECT contractId,customerId FROM contract where customerId!='".$row[0]."' and contractId='".$row[1]."' ";
            $this->Util()->DB()->setQuery($sql1);
            $findData = $this->Util()->DB()->GetRow();
            if(!empty($findData))
            {
                $this->Util()->setError(0,'error','La razon social con de la fila '.$fila.' no se encuentra registrado para el cliente pero esta siendo usado por otro');
               // break;
            }
            $fila++;
        }
        fclose($fp);
        if($this->Util()->PrintErrors())
            return false;

        return true;
    }
    public function ValidateLayoutImportServicio($FILES,$isNew=false){
        $file_temp = $FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila=1;
        while(($row=fgetcsv($fp,4096,","))==true){
            if ($fila == 1) {
                $fila++;
                continue;
            }
            //encontrar el contrato
            $sql ="select max(a.contractId) from contract a 
                  inner join customer b on a.customerId=b.customerId and lower(replace(b.nameContact,' ',''))='".mb_strtolower(str_replace(' ','',utf8_encode($row[0])))."' and b.active='1' 
                  where lower(replace(a.name,' ',''))='".mb_strtolower(str_replace(' ','',utf8_encode($row[1])))."' and a.activo='Si'
                  ";
            $this->Util()->DB()->setQuery($sql);
            $conId =  $this->Util()->DB()->GetSingle();
            if($conId<=0) {
                $this->Util()->setError(0, 'error', "Razon social de la fila " . $fila . " no encontrado,existe conflicto con algun otro cliente, esta inactivo o no se cuentra en el sistema");
                $this->Util()->setError(0,'error',$sql );
                break;
            }
            //encontrar el servicio
            $sql= "select tipoServicioId from tipoServicio where lower(replace(nombreServicio,' ',''))='".mb_strtolower(str_replace(' ','',utf8_encode($row[2])))."' ";
            $this->Util()->DB()->setQuery($sql);
            $tipoServicioId = $this->Util()->DB()->GetSingle();
            if($tipoServicioId<=0) {
                $this->Util()->setError(0, 'error', "Servicio de la fila " . $fila . " no encontrado");
                $this->Util()->setError(0,'error',$sql );
                break;
            }
            if($row[4]==""){
                $this->Util()->setError(0, 'error', "Falta inicio de facturacion en la fila " . $fila . " de no tenerlo usar 0000-00-00");
                break;
            }
            if($row[5]==""){
                $this->Util()->setError(0, 'error', "Falta inicio de operaciones en la fila " . $fila . " de no tenerlo usar 0000-00-00");
                break;
            }
            if($isNew){
                if($row[4]=='0000-00-00')
                    $fechaFacturacion = $row[4];
                else
                    $fechaFacturacion = $this->Util()->FormatDateMySqlSlash($row[4]);

                if($row[5]=='0000-00-00')
                    $fechaInicioOperacion = $row[5];
                else
                    $fechaInicioOperacion = $this->Util()->FormatDateMySqlSlash($row[5]);

                echo $sqlServ = "SELECT servicioId from servicio where contractId='".$conId."' and tipoServicioId='".$tipoServicioId."' and inicioOperaciones='".$fechaInicioOperacion."' and inicioFactura='". $fechaFacturacion."' and status in('activo','bajaParcial') ";
                $this->Util()->DB()->setQuery($sqlServ);
                $servicesFind= $this->Util()->DB()->GetResult();
                if(count($servicesFind)>0){
                    $this->Util()->setError(0, 'error', "Ya se encuentra un servicio registrado con las mismas caracteristicas de la fila " . $fila . ", revisar informacion");
                    break;
                }
            }
            $fila++;
        }
        if($this->Util()->PrintErrors())
           return false;

        return true;
    }


}