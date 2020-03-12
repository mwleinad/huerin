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
            <th style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE RAZON SOCIAL</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>TOTAL DE IGUALA</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>TIPO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RFC</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>REGIMEN FISCAL</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RAZON ACTIVA</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>ACTIVIDAD COMERCIAL</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>DIRECCION COMERCIAL</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>CALLE</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>No. EXTERIOR</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>No. INTERIOR</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>COLONIA</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>MUNICIPIO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>ESTADO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>PAIS</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>C.P.</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE CONTACTO ADMINISTRATIVO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>EMAIL CONTACTO ADMINISTRATIVO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>TELEFONO CONTACTO ADMINISTRATIVO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE CONTACTO CONTABILIDAD</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>EMAIL CONTACTO CONTABILIDAD</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>TELEFONO CONTACTO CONTABILIDAD</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE CONTACTO DIRECTIVO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>EMAIL CONTACTO DIRECTIVO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>TELEFONO CONTACTO DIRECTIVO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>CELULAR CONTACTO DIRECTIVO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>REPRESENTANTE LEGAL</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>CLAVE CIEC</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>CLAVE FIEL</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>CLAVE IDSE</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>CLAVE ISN</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>FACTURADOR</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>METODO DE PAGO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>NUMERO DE CUENTA</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RESPONSABLE</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>SUPERVISOR</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RESP. CONTABILIDAD</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RESP. NOMINA</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RESP. ADMIN</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RESP. JURIDICO</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RESP. IMSS</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RESP. AUDITORIA</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>RESP. DH</b></th>
            <th style=\"background:#D7EBFF;text-align:center;\"><b>GENERAR FACTURA DE MES 13</b></th>
        </tr>
	</thead>
	<tbody>";
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
		    	    <td style=\"text-align:center;mso-number-format:'@';\">".date('d/m/Y',strtotime($con['dateAlta']))."</td>
			        <td style=\"text-align:center;mso-number-format:'@';\">".utf8_decode($con['observacion'])."</td>
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
					<td style=\"text-align:center;\">".$con['nameRepresentanteLegal']."</td>
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
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameAuditoria']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['nameDesarrollohumano']."</td>
					<td style=\"text-align:center;mso-number-format:'@';\">".$con['generaFactura13']."</td>
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