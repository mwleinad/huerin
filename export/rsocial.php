<?php

if(!isset($_SESSION)){
    session_start();
}

date_default_timezone_set('America/Mexico_City');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

$user->allowAccess(167);

extract($_POST);
$rfc = '';
$tipo = 'Activos';
$_POST['deep'] = "on";

if($_POST['deep'])
    $subor = 'subordinado';
else
    $subor = 'propio';

$clientes = $customer->SuggestCustomerCatalog($rfc, $subor, $customerId = 0, $tipo);

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
            <td style=\"background:#D7EBFF;text-align:center;\"><b>DIRECCION FISCAL</b></td>
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

foreach($clientes as $res){

    $activo = ($res['active'] == 1) ? 'Activo' : 'Inactivo';

    if(count($res['contracts']) > 0){

        foreach($res['contracts'] as $con){

            $regimen->setRegimenId($con['regimenId']);
            $nomRegimen = $regimen->GetNameById();

            $x .= "
				<tr>
    				<td style=\"text-align:center;\">".$res['customerId']."</td>
    				<td style=\"text-align:center;\">".$con['contractId']."</td>
        			<td style=\"text-align:left;\">".utf8_decode($res['nameContact'])."</td>
	        		<td style=\"text-align:center;\">".$res['phone']."</td>
		        	<td style=\"text-align:left;\">".$res['email']."</td>
    			    <td style=\"text-align:center;\">".$res['password']."</td>
	    		    <td style=\"text-align:center;\">".count($res['contracts'])."</td>
		    	    <td style=\"text-align:center;\">".date('d-m-Y',strtotime($res['fechaAlta']))."</td>
			        <td style=\"text-align:center;\">".utf8_decode($res['observaciones'])."</td>
			        <td style=\"text-align:center;\">".$activo."</td>
					<td style=\"text-align:center;\">".utf8_decode($con['name'])."</td>
					<td style=\"text-align:left;\">$ ".$con['totalMensual']."</td>
					<td style=\"text-align:center;\">".utf8_decode($con['type'])."</td>
					<td style=\"text-align:center;\">".$con['rfc']."</td>
					<td style=\"text-align:center;\">".utf8_decode($nomRegimen)."</td>
					<td style=\"text-align:center;\">".$con['activo']."</td>
					<td style=\"text-align:center;\">".$con['nombreComercial']."</td>
					<td style=\"text-align:center;\">".$con['direccionComercial']."</td>
					<td style=\"text-align:center;\">".$con['address']." ".$con['noExtAddress']." ".$con['noIntAddress']." ".$con['coloniaAddress']."
					".$con['municipioAddress']." ".$con['estadoAddress']." ".$con['cpAddress']."</td>
					<td style=\"text-align:center;\">".$con['nameContactoAdministrativo']."</td>
					<td style=\"text-align:center;\">".$con['emailContactoAdministrativo']."</td>
					<td style=\"text-align:center;\">".$con['telefonoContactoAdministrativo']."</td>
					<td style=\"text-align:center;\">".$con['nameContactoContabilidad']."</td>
					<td style=\"text-align:center;\">".$con['emailContactoContabilidad']."</td>
					<td style=\"text-align:center;\">".$con['telefonoContactoContabilidad']."</td>
					<td style=\"text-align:center;\">".$con['nameContactoDirectivo']."</td>
					<td style=\"text-align:center;\">".$con['emailContactoDirectivo']."</td>
					<td style=\"text-align:center;\">".$con['telefonoContactoDirectivo']."</td>
					<td style=\"text-align:center;\">".$con['telefonoCelularDirectivo']."</td>
					<td style=\"text-align:center;\">".$con['claveCiec']."</td>
					<td style=\"text-align:center;\">".$con['claveFiel']."</td>
					<td style=\"text-align:center;\">".$con['claveIdse']."</td>
					<td style=\"text-align:center;\">".$con['claveIsn']."</td>
					<td style=\"text-align:center;\">".$con['facturador']."</td>
					<td style=\"text-align:center;\">".$con['metodoDePago']."</td>
					<td style=\"text-align:center;\">".$con['noCuenta']."</td>
					<td style=\"text-align:center;\">".$con['responsable']."</td>
					<td style=\"text-align:center;\">".$con['supervisadoBy']."</td>
					<td style=\"text-align:center;\">".$con['nameContabilidad']."</td>
					<td style=\"text-align:center;\">".$con['nameNominas']."</td>
					<td style=\"text-align:center;\">".$con['nameAdministracion']."</td>
					<td style=\"text-align:center;\">".$con['nameJuridico']."</td>
					<td style=\"text-align:center;\">".$con['nameImss']."</td>
					<td style=\"text-align:center;\">".$con['nameMensajeria']."</td>
					<td style=\"text-align:center;\">".$con['nameAuditoria']."</td>
				</tr>";

        }//foreach

    }//if

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