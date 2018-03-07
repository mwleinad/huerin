<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
session_start();

switch ($_POST['type']) {
    case 'search':
       $filter['modulo']=$_POST['modulo'];
       $filter['finicial'] = $_POST['finicial'];
       $filter['ffinal'] = $_POST['ffinal'];
       $logs = $log->SearchLog($filter);
       $bitacoras=array();
       foreach($logs as $key=>$value){
           if($value['action']=="")
               continue;
           $tipo = "";
           $showChanges= false;
           switch ($value['action']) {
               case 'Insert':
                   $tipo = 'Alta';
                   if($value['tabla']=='contract'||$value['tabla']=='customer')
                       $descripcion = 'Alta de una razon social o contrato';
                   elseif($value['tabla']=='servicio')
                       $descripcion = 'Se agrega servicio al cliente';
                   $showChanges= false;
                   break;
               case 'Update':
                   $tipo = 'Modificacion';
                   if($value['tabla']=='contract'||$value['tabla']=='customer')
                       $descripcion = 'Actualizacion de informacion del cliente';
                   elseif($value['tabla']=='servicio')
                       $descripcion = 'Actualizacion de servicio al cliente';
                   $showChanges = true;
                   break;
               case 'Delete':
                   $tipo = 'Eliminacion';
                   if($value['tabla']=='contract'||$value['tabla']=='customer')
                       $descripcion = 'Eliminacion de una razon social';
                   elseif($value['tabla']=='servicio')
                       $descripcion = 'Eliminacion de servicio al cliente';
                   $showChanges  = false;
                   break;
               case 'Reactivacion':
                   $tipo ='Reactivacion';
                   if($value['tabla']=='contract'||$value['tabla']=='customer')
                       $descripcion = 'Reactivacion de razon social al cliente';
                   elseif($value['tabla']=='servicio')
                       $descripcion = 'Reactivacion de servicio al cliente';
                   $showChanges=false;
                   break;
               case 'Baja':

                   $tipo ='Baja';
                   switch($value['tabla']){
                       case 'contract':
                           $descripcion = 'Baja de razon social al cliente';
                       break;
                       case 'customer':
                           $descripcion = 'Baja del cliente';
                       break;
                       case 'servicio':
                          $descripcion = 'Baja de servicio asignado al cliente';
                       break;
                   }
                   $showChanges = false;
                   break;
               default:
                   $tipo =$value['action'];
                   $showChanges = false;
                   break;
           }

           $card =  $value;
           $card['tipo'] = $tipo;
           $newValue = unserialize($value['newValue']);
           $oldValue = unserialize($value['oldValue']);
           if(empty($newValue))
               $newValue =  array();
           if(empty($oldValue))
               $oldValue =  array();
           switch($value['tabla']){
               case 'customer':
                   $sql =" SELECT * FROM customer WHERE customerId='".$value['tablaId']."' ";
                   $db->setQuery($sql);
                   $customer = $db->GetRow();
                   $card['nameContact'] = $customer['nameContact'];
                   $card['servicio'] = 'N/A';
                   $old=array();
                   $new = array();
                   if($showChanges){
                       foreach($oldValue as $keyo =>$valueo)
                       {
                           if($valueo!=$newValue[$keyo])
                           {
                               $old[$keyo]=$valueo;
                               $new[$keyo]=$newValue[$keyo];
                           }
                       }
                       if(empty($old))
                           $descripcion = 'Edicion de registro sin modificar informacion';

                       $card['oldValue']=$old;
                       $card['newValue']=$new;

                   }else{
                       $card['oldValue']=array();
                       $card['newValue']=array();
                   }
                   break;
               case 'contract':
                   $sql = 'SELECT contract.permisos,customer.nameContact,contract.name FROM contract
                    LEFT JOIN customer ON contract.customerId=customer.customerId
                    WHERE contract.contractId='.$value["tablaId"].' ';
                   $db->setQuery($sql);
                   $contrato = $db->GetRow();
                   $permisos = preg_split("/-/",$contrato['permisos']);
                   foreach($permisos as $pm){
                       $split = explode(',',$pm);
                       if($split[0] == 1) {
                           $personal->setPersonalId($split[1]);
                           $resposable = $personal->Info();
                       }
                   }

                   $card['name'] = $contrato['name'];
                   $card['nameContact'] = $contrato['nameContact'];
                   $card['respContabilidad'] = $resposable['name'];
                   $card['servicio'] = 'NA';

                   $old=array();
                   $new = array();
                   if($showChanges){
                       foreach($oldValue as $keyo =>$valueo)
                       {
                           if($valueo!=$newValue[$keyo])
                           {
                               $old[$keyo]=$valueo;
                               $new[$keyo]=$newValue[$keyo];
                           }
                       }
                       if(empty($old))
                           $descripcion = 'Edicion de registro sin modificar informacion';

                       $card['oldValue']=$old;
                       $card['newValue']=$new;

                   }else{
                       $card['oldValue']=array();
                       $card['newValue']=array();
                   }

                   break;
               case 'servicio':
                   $sql =  'SELECT contract.permisos,customer.nameContact,contract.name FROM  servicio
                     LEFT JOIN contract ON servicio.contractId=contract.contractId
                     LEFT JOIN customer ON contract.customerId=customer.customerId
                     WHERE servicioId='.$value["tablaId"].'
                    ';
                   $db->setQuery($sql);
                   $contrato = $db->GetRow();
                   $permisos = preg_split("/-/",$contrato['permisos']);
                   foreach($permisos as $pm){
                       $split = explode(',',$pm);
                       if($split[0] == 1) {
                           $personal->setPersonalId($split[1]);
                           $resposable = $personal->Info();
                       }
                   }
                   $servicio->setServicioId($value['tablaId']);
                   $serv = $servicio->Info();
                   $card['name'] = $contrato['name'];
                   $card['nameContact'] = $contrato['nameContact'];
                   $card['respContabilidad'] = $resposable['name'];
                   $card['servicio'] = $serv['nombreServicio'];

                   $old=array();
                   $new = array();
                   if($showChanges){
                       foreach($oldValue as $keyo =>$valueo)
                       {
                           if($valueo!=$newValue[$keyo])
                           {
                               $old[$keyo]=$valueo;
                               $new[$keyo]=$newValue[$keyo];
                           }
                       }
                       if(empty($old))
                           $descripcion = 'Edicion de registro sin modificar informacion';
                       $card['oldValue']=$old;
                       $card['newValue']=$new;
                   }else{
                       $card['oldValue']=array();
                       $card['newValue']=array();
                   }


                   break;

           }
           $card['descripcion'] = $descripcion;
           $bitacoras[] = $card;
       }

       echo "ok[#]";
       $smarty->assign('registros',$bitacoras);
       $smarty->display(DOC_ROOT . '/templates/lists/bitacora.tpl');
       $html = $smarty->fetch(DOC_ROOT . '/templates/lists/bitacora.tpl');
       $html = str_replace('$', '', $html);
       $html = str_replace(',', '', $html);
       $excel->ConvertToExcel($html, 'xlsx', false, "list-bitacora",500);
       echo "[#]";
       echo "<a href='".WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/list-bitacora.xlsx'> 
              <img src='".WEB_ROOT."/images/excel.PNG' width='16' />Exportar resultado
             </a>";

    break;
}
?>