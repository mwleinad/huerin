<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 07/06/2018
 * Time: 05:02 PM
 */

class Validar extends Main
{
    public function validateLayout($FILES, $table="", $action="") {
        $departamentos = new Departamentos();

        $file_temp = $FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $list_departamentos = $departamentos->GetListDepartamentos();
        $string = file_get_contents(DOC_ROOT."/properties/config_layout_".$action."_".$table.".json");
        $columnas = json_decode($string, true);

        //add columnas
        if($table === 'contract') {
            foreach ($list_departamentos as $key => $dep) {
                $new_head['name'] = "Resp. " . ucfirst($dep['departamento']);
                $new_head['required'] = false;
                $new_head['comment'] = "Seleccione un responsable de la lista";
                $keyField = "name" . ucfirst(mb_strtolower(str_replace(" ", "", $dep['departamento'])));
                $new_head['field_excel'] = $keyField;
                $new_head['fillable'] = true;
                $new_head['constraint'] = true;
                $new_head['reference_table'] = "personal";
                $new_head['field_comparison_foreign'] = "name";
                $new_head['field_return_foreign'] = "personalId";
                $new_head['foreign_key'] = "personalId";
                $new_head['is_responsable'] = true;
                $new_head['dep_id'] = $dep['departamentoId'];
                array_push($columnas, $new_head);
            }
        }
        $fila=1;
        $items = [];
        while(($row=fgetcsv($fp,4096,","))==true) {
           if($fila === 1) {
               $fila++;
               continue;
           }
            $card_main['table'] = $table;
            $fields = [];
            $col_primary_key = "";
            $responsables = [];
            foreach($columnas as $col => $columna) {
                $col_name = $columna['name'];
                if($columna['is_primary_key'])
                    $col_primary_key =  $col;

                if($columna['check_in_db'] === true) {
                    $sql = "select ".$columna['field_bd']." from  ".$columna['check_table']."  
                            where ".$columna['check_field']." = '".($row[$col])."'";
                    $this->Util()->DB(false)->setQuery($sql);
                    $exist  = $this->Util()->DB(false)->GetRow();
                    if(!$exist) {
                        $this->Util()->setError(0,"error","No se encontro algun registro con el dato proporcionado en la columna $col_name y fila $fila");
                        break 2;
                    }
                }
                if(!$columna['fillable'])
                    continue;

                if($columna['required'] === true) {
                    if($row[$col] === "") {
                        $this->Util()->setError(0,"error","Campo requerido de la columna $col_name y fila $fila");
                        break 2;
                    }
                }

                if($columna['constraint'] === true && $row[$col] !== "") {
                    $valorComparar = "";
                    $valorComparar = str_replace(chr(10), '', $row[$col]);
                    $valorComparar = str_replace(chr(13), '', $valorComparar);
                    $valorComparar = trim($valorComparar);
                    $sql = "select ".$columna['field_return_foreign']." from  ".$columna['reference_table']."  
                            where ".$columna['field_comparison_foreign']." = '".$valorComparar."'";
                    $this->Util()->DB(false)->setQuery($sql);
                    $find  = $this->Util()->DB(false)->GetRow();
                    if(!$find) {
                        $this->Util()->setError(0,"error","El valor referenciado no se encuentra en el sistema, ver columna $col_name y fila $fila");
                        break 2;
                    }

                    if($columna['is_responsable']) {
                       $resp['dep_id'] = $columna['dep_id'];
                       $resp['personal_id'] = $find[$columna['field_return_foreign']];
                       array_push($responsables, $resp);
                       continue;
                    }
                }
                if($columna['input_validate'] === true) {
                    if(!in_array($row[$col], $columna['accepted_values'])) {
                        $this->Util()->setError(0,"error","Valor no permitido, ver columna $col_name y fila $fila");
                        break 2;
                    }
                }
                if($columna['validate_date_format'] === true) {
                    if($row[$col] !== ""){
                        if(!$this->Util()->isValidateDate($row[$col],$columna['date_format'])) {
                            $this->Util()->setError(0,"error","Fecha no valida, ver columna $col_name y fila $fila");
                            break 2;
                        }
                    }
                }
                if($columna['is_responsable'])
                    continue;

                $card['field'] =  $columna['field_bd'];
                $card['value'] =  $columna['constraint'] ?
                                  isset($find[$columna['field_return_foreign']]) ?  $find[$columna['field_return_foreign']] : '0' : htmlspecialchars_decode($row[$col]);
                $card['value']  = ($columna['type_column'] ?? '') == 'integer' ? intval($card['value']) : $card['value'];
                $card['columna'] = $columna;
                array_push($fields, $card);
            }
            $card_main['primary_key'] = $columnas[$col_primary_key]['field_bd'];
            $card_main['value_primary_key'] = $row[$col_primary_key];
            $card_main['fields_update'] =  $fields;
            $card_main['responsables'] =  $responsables;
            array_push($items, $card_main);
            $fila++;
        }
        if($this->Util()->PrintErrors())
            return false;

        return $items;
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
               $this->Util()->setError(0,'error','Falta cel. directivo en la fila '.$fila);
               break;
           }
           if($row[28]==""){
               $this->Util()->setError(0,'error','Falta clave FIEL en la fila '.$fila);
               break;
           }
           if($row[29]==""){
               $this->Util()->setError(0,'error','Falta clave CIEC en la fila '.$fila);
               break;
           }
           if($row[30]==""){
               $this->Util()->setError(0,'error','Falta clave IDSE en la fila '.$fila);
               break;
           }
           if($row[31]==""){
               $this->Util()->setError(0,'error','Falta clave ISN en la fila '.$fila);
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
               echo $this->Util()->DB()->getQuery();
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
                  inner join customer b on a.customerId=b.customerId and replace(trim(lower(replace(replace(b.nameContact,' ',''), '&amp;', '&'))), char(9), '')='".mb_strtolower(str_replace(' ','', $row[0]))."' and b.active='1' 
                  where replace(trim(lower(replace(replace(a.name,' ',''), '&amp;', '&'))), char(9), '')='".mb_strtolower(str_replace(' ','',$row[1]))."' and a.activo='Si'
                  ";
            $this->Util()->DB()->setQuery($sql);
            $conId =  $this->Util()->DB()->GetSingle();
            if($conId<=0) {
                $this->Util()->setError(0, 'error', "Razon social de la fila " . $fila . " no encontrado,existe conflicto con algun otro cliente, esta inactivo o no se cuentra en el sistema");
                $this->Util()->setError(0,'error',$sql );
                break;
            }
            //encontrar el servicio
            $sql= "select tipoServicioId from tipoServicio where replace(trim(lower(replace(replace(nombreServicio,' ',''), '&amp;', '&'))), char(9), '')='".mb_strtolower(str_replace(' ','',$row[2]))."' ";
            $this->Util()->DB()->setQuery($sql);
            $tipoServicioId = $this->Util()->DB()->GetSingle();
            if($tipoServicioId<=0) {
                $this->Util()->setError(0, 'error', "Servicio de la fila " . $fila . " no encontrado");
                break;
            }
            if($row[3]==""){
                $this->Util()->setError(0, 'error', "Falta inicio de facturacion en la fila " . $fila . " de no tenerlo usar 0000-00-00");
                break;
            } elseif($row[3]!='0000-00-00') {
                if(!$this->Util()->isValidateDate($row[3], 'd/m/Y')) {
                    $this->Util()->setError(0, 'error', "Formato de fecha inicio de facturacion en la fila " . $fila . " es invalido.  Usar dia/mes/año ");
                    break;
                }
            }
            if($row[4]==""){
                $this->Util()->setError(0, 'error', "Falta inicio de operaciones en la fila " . $fila . " de no tenerlo usar 0000-00-00");
                break;
            } elseif($row[4]!='0000-00-00') {
                if(!$this->Util()->isValidateDate($row[4], 'd/m/Y')) {
                    $this->Util()->setError(0, 'error', "Formato de fecha inicio de operacion en la fila " . $fila . " es invalido.  Usar dia/mes/año ");
                    break;
                }
            }
            if($isNew){
                $fechaFacturacion = $row[3] == '0000-00-00' ? $row[3] : $this->Util()->FormatDateMySqlSlash($row[3]);
                $fechaInicioOperacion = $row[4] == '0000-00-00' ? $row[4] : $this->Util()->FormatDateMySqlSlash($row[4]);

                $sqlServ = "SELECT servicioId from servicio where contractId='".$conId."' and tipoServicioId='".$tipoServicioId."' and inicioOperaciones='".$fechaInicioOperacion."' and inicioFactura='". $fechaFacturacion."' and status in('activo','bajaParcial') ";
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
    public function ValidateLayoutUpdateDireccionFiscal($FILES)
    {
        $file_temp = $FILES['file']['tmp_name'];
        $fp = fopen($file_temp, 'r');
        $fila = 1;
        while(($row=fgetcsv($fp,4096,","))==true){
            if(count($row)!=3){
                $this->Util()->setError(0, 'error', "Archivo no valido");
                break;
            }
            if ($fila == 1) {
                $fila++;
                continue;
            }
            $dir_explode = explode("|",$row[2]);
            if(count($dir_explode)!=8)
            {
                dd($dir_explode);
                $this->Util()->setError(0, 'error', "Direccion fiscal no valida en la fila " . $fila);
                break;
            }
            $fila++;
        }
        if($this->Util()->PrintErrors())
            return false;

        return true;
    }
    public function ValidateLayoutNewCustomer($FILES){
        $file_temp = $FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila=1;
        while(($row=fgetcsv($fp,4096,","))==true){
            if (count($row)!=6) {
                $this->Util()->setError(0, 'error', "Archivo no valido" );
                break;
            }
            if ($fila == 1) {
                $fila++;
                continue;
            }
            if(trim($row[0])==""){
                $this->Util()->setError(0, 'error', "Falta NOMBRE en la fila " .$fila );
                break;
            }
            //comprobar que no este registrado por el nombre
            $sql= "select customerId from customer where lower(replace(nameContact,' ',''))='".strtolower(str_replace(' ','',$row[0]))."' ";
            $this->Util()->DB()->setQuery($sql);
            $customerId = $this->Util()->DB()->GetSingle();
            if($customerId) {
                $this->Util()->setError(0, 'error', "Ya existe un cliente registrado con el nombre de la fila " . $fila);
                break;
            }

            if(trim($row[1])==""){
                $this->Util()->setError(0, 'error', "Falta TELEFONO en la fila " . $fila );
                break;
            }
            if(trim($row[2])==""){
                $this->Util()->setError(0, 'error', "Falta EMAIL en la fila " . $fila );
                break;
            }
            if(trim($row[3])==""){
                $this->Util()->setError(0, 'error', "Falta PASSWORD en la fila " . $fila );
                break;
            }
            if(trim($row[5])==""){
                $this->Util()->setError(0, 'error', "Falta FECHA ALTA en la fila " . $fila );
                break;
            }else{
                if(!$this->Util()->isValidateDate(trim($row[5]),"d/m/Y")){
                    $this->Util()->setError(0, 'error', "FECHA ALTA no valida en la fila " . $fila );
                    break;
                }
            }
            $fila++;
        }
        if($this->Util()->PrintErrors())
            return false;

        return true;
    }
    public function ValidateLayoutUpdateServicios($FILES){
        $file_temp = $FILES['file']['tmp_name'];
        $fp = fopen($file_temp,'r');
        $fila=1;
        while(($row=fgetcsv($fp,4096,","))==true){
            if (count($row)!=11) {
                $this->Util()->setError(0, 'error', "Archivo no valido" );
                break;
            }
            if ($fila == 1) {
                $fila++;
                continue;
            }
            if(trim($row[0])=="" || !is_numeric(trim($row[0]))){
                $this->Util()->setError(0, 'error', "Error ID CONTRATO en la fila " .$fila );
                break;
            }
            if(trim($row[2])=="" || !is_numeric(trim($row[2]))){
                $this->Util()->setError(0, 'error', "Error ID SERVICIO en la fila " . $fila );
                break;
            }
            if(!is_numeric(trim($row[4]))){
                $this->Util()->setError(0, 'error', "Eror  COSTO en la fila " . $fila );
                break;
            }
            if(trim($row[5])=='0000-00-00' || !$this->Util()->isValidateDate(trim($row[5]),"d/m/Y")){
                $this->Util()->setError(0, 'error', "Error INICIO OPERACION en la fila " . $fila );
                break;
            }
            if(trim($row[6])!="0000-00-00")
            {
                if(!$this->Util()->isValidateDate(trim($row[6]),"d/m/Y")){
                    $this->Util()->setError(0, 'error', "Error INICIO FACTURA en la fila " . $fila );
                    break;
                }
                if((double)(trim($row[2]))<=0){
                    $this->Util()->setError(0, 'error', "COSTO debe ser mayor a cero si cuenta con fecha valida en INICIO FACTURA, comprobar fila  " . $fila );
                    break;
                }
            }
            if(trim($row[10])=="")
            {
                $this->Util()->setError(0, 'error', "Error en STATUS en fila " .$fila);
                break;
            }else{
                $fields = $this->Util()->getEnumValues('servicio','status');
                if(!in_array(trim($row[10]),$fields)){
                    $this->Util()->setError(0, 'error', "Error en STATUS en la fila " .$fila.", solo se aceptan las descritas en el encabezado");
                    break;
                }
                if(trim($row[10])=="bajaParcial"){
                    if(trim($row[7])=='' || trim($row[7])=='0000-00-00' || !$this->Util()->isValidateDate(trim($row[7]),"d/m/Y")){
                        $this->Util()->setError(0, 'error', "Si el servicio se encuentra como baja temporal o parcial es necesario una fecha valida en FECHA ULTIMO WORKFLOW en la fila " . $fila );
                        break;
                    }
                }
            }

            $fila++;
        }
        if($this->Util()->PrintErrors())
            return false;

        return true;
    }


}
