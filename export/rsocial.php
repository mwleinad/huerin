<?php

if(!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Mexico_City');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
ini_set('memory_limit','3G');
$user->allowAccess(167);
$rfc = '';
$tipo = 'Activos';

if($_POST['deep'])
    $subor = 'subordinado';
else
    $subor = 'propio';



$encargados = $personal->GetIdResponsablesSubordinados($_POST);
$post = $_POST;
$post["encargados"] = $encargados;
if($_POST['responsableCuenta']>0)
    $post["selectedResp"] = true;
else
    $post["selectedResp"] = false;
$clientes =  $customer->SuggestCustomerRazon($post);
//$clientes = $customer->SuggestCustomerCatalog($rfc, $subor, $customerId = 0, $tipo);

$x .=
"<table border=\"1\">
	<thead>
        <tr>
            <th style=\"background:#E0E5E7;text-align:center\"><b>NO. CLIENTE</b></th>
             <th style=\"background:#E0E5E7;text-align:center\"><b>NO. CONTRATO</b></th>
            <th style=\"background:#E0E5E7;text-align:center\"><b>CLIENTE</b></th>
            <th style=\"background:#E0E5E7;text-align:center\"><b>TEL. CONTACTO</b></th>
            <th style=\"background:#E0E5E7;text-align:center\"><b>EMAIL CONTACTO</b></th>
            <th style=\"background:#E0E5E7;text-align:center\"><b>PASSWORD</b></th>
            <th style=\"background:#E0E5E7;text-align:center\"><b>RAZONES SOCIALES</b></th>
            <th style=\"background:#E0E5E7;text-align:center\"><b>FECHA ALTA</b></th>
            <th style=\"background:#E0E5E7;text-align:center\"><b>OBSERVACIONES</b></th>
            <th style=\"background:#E0E5E7;text-align:center\"><b>CLIENTE ACTIVO</b></th>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE RAZON SOCIAL</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>TOTAL DE IGUALA</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>TIPO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RFC</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>REGIMEN FISCAL</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RAZON ACTIVA</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE COMERCIAL</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>DIRECCION COMERCIAL</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>CALLE</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>No. EXTERIOR</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>No. INTERIOR</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>COLONIA</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>MUNICIPIO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>ESTADO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>PAIS</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>C.P.</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE CONTACTO ADMINISTRATIVO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>EMAIL CONTACTO ADMINISTRATIVO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>TELEFONO CONTACTO ADMINISTRATIVO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE CONTACTO CONTABILIDAD</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>EMAIL CONTACTO CONTABILIDAD</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>TELEFONO CONTACTO CONTABILIDAD</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE CONTACTO DIRECTIVO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>EMAIL CONTACTO DIRECTIVO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>TELEFONO CONTACTO DIRECTIVO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>CELULAR CONTACTO DIRECTIVO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>CLAVE CIEC</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>CLAVE FIEL</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>CLAVE IDSE</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>CLAVE ISN</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>FACTURADOR</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>METODO DE PAGO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>NUMERO DE CUENTA</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RESPONSABLE</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>SUPERVISOR</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RESP. CONTABILIDAD</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RESP. NOMINA</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RESP. ADMIN</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RESP. JURIDICO</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RESP. IMSS</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RESP. MENSAJERIA</b></td>
            <td style=\"background:#D7EBFF;text-align:center;\"><b>RESP. AUDITORIA</b></td>
            
        </tr>
	</thead>
	<tbody>";

/*foreach($clientes as $res){

    $activo = ($res['active'] == 1) ? 'Activo' : 'Inactivo';

    if(count($res['contracts']) > 0){

        foreach($res['contracts'] as $con){

            $regimen->setRegimenId($con['regimenId']);
            $nomRegimen = $regimen->GetNameById();

            $x .= "
				<tr>
    				<td style=\"text-align:center;mso-number-format:'@';\">".$res['customerId']."</td>
    				<td style=\"text-align:center;mso-number-format:'@';\">".$con['contractId']."</td>
        			<td style=\"text-align:left;mso-number-format:'@';\">".utf8_decode($res['nameContact'])."</td>
	        		<td style=\"text-align:center;mso-number-format:'@';\">".$res['phone']."</td>
		        	<td style=\"text-align:left;mso-number-format:'@';\">".$res['email']."</td>
    			    <td style=\"text-align:center;mso-number-format:'@';\">".$res['password']."</td>
	    		    <td style=\"text-align:center;mso-number-format:'@';\">".count($res['contracts'])."</td>
		    	    <td style=\"text-align:center;mso-number-format:'@';\">".date('d-m-Y',strtotime($res['fechaAlta']))."</td>
			        <td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($res['observaciones'])."</td>
			        <td style=\"text-align:center;mso-number-format:'@';\">".$activo."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($con['name'])."</td>
					<td style=\"text-align:left;mso-number-format:'@';\">$ ".$con['totalMensual']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($con['type'])."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['rfc']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($nomRegimen)."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['activo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nombreComercial']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['direccionComercial']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['address']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['noExtAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['noIntAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['coloniaAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['municipioAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['estadoAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['paisAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['cpAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameContactoAdministrativo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['emailContactoAdministrativo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['telefonoContactoAdministrativo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameContactoContabilidad']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['emailContactoContabilidad']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['telefonoContactoContabilidad']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameContactoDirectivo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['emailContactoDirectivo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['telefonoContactoDirectivo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['telefonoCelularDirectivo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['claveCiec']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['claveFiel']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['claveIdse']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['claveIsn']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['facturador']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['metodoDePago']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['noCuenta']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['responsable']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['supervisadoBy']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameContabilidad']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameNominas']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameAdministracion']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameJuridico']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameImss']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameMensajeria']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameAuditoria']."</td>
				</tr>";

        }//foreach

    }//if

}//foreach*/
foreach($clientes as $con){

            $regimen->setRegimenId($con['regimenId']);
            $nomRegimen = $regimen->GetNameById();
            $activo = ($con['active'] == 1) ? 'Activo' : 'Inactivo';
            $x .= "
				<tr> 
    				<td style=\"text-align:center;mso-number-format:'@';\">".$con['clienteId']."</td>
    				<td style=\"text-align:center;mso-number-format:'@';\">".$con['contractId']."</td>
        			<td style=\"text-align:left;mso-number-format:'@';\">".utf8_decode($con['nameContact'])."</td>
	        		<td style=\"text-align:center;mso-number-format:'@';\">".$con['phone']."</td>
		        	<td style=\"text-align:left;mso-number-format:'@';\">".$con['email']."</td>
    			    <td style=\"text-align:center;mso-number-format:'@';\">".$con['password']."</td>
	    		    <td style=\"text-align:center;mso-number-format:'@';\">".count($con['contracts'])."</td>
		    	    <td style=\"text-align:center;mso-number-format:'@';\">".date('d-m-Y',strtotime($con['fechaAlta']))."</td>
			        <td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($con['observaciones'])."</td>
			        <td style=\"text-align:center;mso-number-format:'@';\">".$activo."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($con['name'])."</td>
					<td style=\"text-align:left;mso-number-format:'@';\">$ ".$con['totalMensual']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($con['type'])."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['rfc']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($nomRegimen)."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['activo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nombreComercial']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['direccionComercial']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['address']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['noExtAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['noIntAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['coloniaAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['municipioAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['estadoAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['paisAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['cpAddress']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameContactoAdministrativo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['emailContactoAdministrativo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['telefonoContactoAdministrativo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameContactoContabilidad']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['emailContactoContabilidad']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['telefonoContactoContabilidad']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameContactoDirectivo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['emailContactoDirectivo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['telefonoContactoDirectivo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['telefonoCelularDirectivo']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['claveCiec']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['claveFiel']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['claveIdse']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['claveIsn']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['facturador']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['metodoDePago']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['noCuenta']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['responsable']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['supervisadoBy']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameContabilidad']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameNominas']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameAdministracion']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameJuridico']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameImss']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameMensajeria']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameAuditoria']."</td>
				</tr>";

        }//foreach


$x .= '</tbody>
	</table>';

$name = 'Razones_Sociales';
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=".$name.".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo $x;


exit;

?>