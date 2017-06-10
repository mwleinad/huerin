<?php
	
	if(!isset($_SESSION)){
		session_start();
	}

	date_default_timezone_set('America/Mexico_City');	
	include_once('../config.php');
	include_once(DOC_ROOT.'/libraries.php'); 
	
	$user->allowAccess('customer');	
				
	extract($_POST);	

	$rfc = $_POST['rfc'];
	$tipo = $_POST["type"];
	$responsableCuenta = $_POST['responsableCuenta'];
	
	if($_POST['deep'])
		$subor = 'subordinado';
	else
		$subor = 'propio';
	
	$clientes = $customer->SuggestCustomerCatalog($rfc, $subor, $customerId = 0, $tipo);
	
	$x .= "<table border=\"1\">
	<thead>
	<tr>
		<th style=\"background:#E0E5E7;text-align:center\"><b>NO. CLIENTE</b></th>
		<th style=\"background:#E0E5E7;text-align:center\"><b>NOMBRE DIRECTIVO</b></th>
		<th style=\"background:#E0E5E7;text-align:center\"><b>TEL. CONTACTO</b></th>
		<th style=\"background:#E0E5E7;text-align:center\"><b>EMAIL CONTACTO</b></th>
		<th style=\"background:#E0E5E7;text-align:center\"><b>PASSWORD</b></th>
		<th style=\"background:#E0E5E7;text-align:center\"><b>RAZONES SOCIALES</b></th>
		<th style=\"background:#E0E5E7;text-align:center\"><b>FECHA ALTA</b></th>
		<th style=\"background:#E0E5E7;text-align:center\"><b>OBSERVACIONES</b></th>
		<th style=\"background:#E0E5E7;text-align:center\"><b>ACTIVO</b></th>
	</tr>
	</thead>
	<tbody>";
	 
	foreach($clientes as $res){
		
		$activo = ($res['active'] == 1) ? 'Activo' : 'Inactivo';
		
		$x .= "
		<tr>
			<td style=\"text-align:center;\">".$res['customerId']."</td>
			<td style=\"text-align:left;\">".utf8_decode($res['nameContact'])."</td>
			<td style=\"text-align:center;\">".$res['phone']."</td>
			<td style=\"text-align:left;\">".$res['email']."</td>
			<td style=\"text-align:center;\">".$res['password']."</td>
			<td style=\"text-align:center;\">".count($res['contracts'])."</td>
			<td style=\"text-align:center;\">".date('d-m-Y',strtotime($res['fechaAlta']))."</td>
			<td style=\"text-align:center;\">".utf8_decode($res['observaciones'])."</td>
			<td style=\"text-align:center;\">".$activo."</td>
		</tr>";
		
		if(count($res['contracts']) > 0){
		
			$x .= "		
			<tr>
				<td></td>
				<td style=\"background:#D7EBFF;text-align:center;\"><b>NOMBRE RAZON SOCIAL</b></td>
				<td style=\"background:#D7EBFF;text-align:center;\"><b>TIPO</b></td>
				<td style=\"background:#D7EBFF;text-align:center;\"><b>RFC</b></td>
				<td style=\"background:#D7EBFF;text-align:center;\"><b>REGIMEN FISCAL</b></td>
				<td style=\"background:#D7EBFF;text-align:center;\"><b>RESPONSABLE</b></td>
				<td style=\"background:#D7EBFF;text-align:center;\"><b>ACTIVO</b></td>
				<td></td>
				<td></td>
			</tr>";
			
			
			foreach($res['contracts'] as $con){
				
				$regimen->setRegimenId($con['regimenId']);
				$nomRegimen = $regimen->GetNameById();
				
				$x .= "
				<tr>
					<td></td>
					<td style=\"text-align:center;\">".utf8_decode($con['name'])."</td>
					<td style=\"text-align:center;\">".utf8_decode($con['type'])."</td>
					<td style=\"text-align:center;\">".$con['rfc']."</td>
					<td style=\"text-align:center;\">".utf8_decode($nomRegimen)."</td>
					<td style=\"text-align:center;\"></td>
					<td style=\"text-align:center;\">".$con['activo']."</td>
					<td></td>
					<td></td>
				</tr>";
				
			}//foreach
			
			$x .= "
			<tr>
				<td colspan=\"7\">&nbsp;</td>
			</tr>";
		
		}//if
		
	}//foreach
	
	$x .= '</tbody>
	</table>';
	
	$name = 'Clientes_con_Razones_Sociales';
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-type:   application/x-msexcel; charset=utf-8");
	header("Content-Disposition: attachment; filename=".$name.".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	
	echo $x;
	
	
	exit;
		
?>