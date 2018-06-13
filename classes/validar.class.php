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
            if($fila==1)
            {
                $fila++;
                continue;
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
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[38]))."'");
                $idCont=$this->Util()->DB()->GetSingle();
                if(!$idCont)
                {
                    $this->Util()->setError(0,'error','Responsable de contabilidad de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[39]!="" and $row[39]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[39]))."'");
                $idNom=$this->Util()->DB()->GetSingle();
                if(!$idNom)
                {
                    $this->Util()->setError(0,'error','Responsable de nomina de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[40]!="" and $row[40]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[40]))."'");
                $idAdmin=$this->Util()->DB()->GetSingle();
                if(!$idAdmin)
                {
                    $this->Util()->setError(0,'error','Responsable de administracion de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[41]!="" and $row[41]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[41]))."'");
                $idJur=$this->Util()->DB()->GetSingle();
                if(!$idJur)
                {
                    $this->Util()->setError(0,'error','Responsable de juridico de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[42]!="" and $row[42]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[42]))."'");
                $idImss=$this->Util()->DB()->GetSingle();
                if(!$idImss)
                {
                    $this->Util()->setError(0,'error','Responsable de imss de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[43]!="" and $row[43]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[43]))."'");
                $idMen=$this->Util()->DB()->GetSingle();
                if(!$idMen)
                {
                    $this->Util()->setError(0,'error','Responsable de mensajeria de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            if($row[44]!="" and $row[44]!="--" ){
                $this->Util()->DB()->setQuery("SELECT personalId FROM  personal WHERE lower(name)='".strtolower(trim($row[44]))."'");
                $idAud=$this->Util()->DB()->GetSingle();
                if(!$idAud)
                {
                    $this->Util()->setError(0,'error','Responsable de auditoria de la fila '.$fila.' no se encuentra dado de alta ');
                    break;
                }
            }
            //comprobar que el regimen existe y sea exclusivamente del tipo de persona seleccionado
            $this->Util()->DB()->setQuery("SELECT regimenId FROM  regimen WHERE lower(replace(nombreRegimen,' ',''))='".strtolower(str_replace(' ','',$row[14]))."' and lower(replace(tipoDePersona,' ',''))='".strtolower(str_replace(' ','',$row[12]))."' ");
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


}