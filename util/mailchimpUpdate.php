<?php
	include_once('../init_files.php');
	include_once('../config.php');
	include_once(DOC_ROOT."/libraries.php");
	include_once(DOC_ROOT."/properties/errors.es.php");

	$util->DB()->setQuery("SELECT customer.nameContact, customer.email, contract.name, nameContactoAdministrativo, emailContactoAdministrativo, nameContactoContabilidad, emailContactoContabilidad, nameContactoDirectivo, emailContactoDirectivo    FROM contract
	LEFT JOIN customer ON customer.customerId = contract.customerId;
	");
	
	$contratos = $util->DB()->GetResult();
//	echo count($servicios);

	$contactos = array();
	foreach($contratos as $key => $contrato)
	{
		{
			$correosSeparados = explode(" ", $contrato["email"]);
			
			foreach($correosSeparados as $key => $value)
			{
				$contacto["empresa"] = $contrato["name"];
				$contacto["correo"] = trim($value);
				$contacto["nombre"] = $contrato["nameContact"];
				$contactos[] = $contacto;
			}
			$correosSeparados = explode("/", $contrato["email"]);
	
			foreach($correosSeparados as $key => $value)
			{
				$contacto["empresa"] = $contrato["name"];
				$contacto["correo"] = trim($value);
				$contacto["nombre"] = $contrato["nameContact"];
				$contactos[] = $contacto;
			}
		}

		{
			$correosSeparados = explode(" ", $contrato["emailContactoAdministrativo"]);
			foreach($correosSeparados as $key => $value)
			{
				$contacto["empresa"] = $contrato["name"];
				$contacto["correo"] = trim($value);
				$contacto["nombre"] = $contrato["nameContactoAdministrativo"];
				$contactos[] = $contacto;
			}
			$correosSeparados = explode("/", $contrato["emailContactoAdministrativo"]);
	
			foreach($correosSeparados as $key => $value)
			{
				$contacto["empresa"] = $contrato["name"];
				$contacto["correo"] = trim($value);
				$contacto["nombre"] = $contrato["nameContactoAdministrativo"];
				$contactos[] = $contacto;
			}
		}

		{
			$correosSeparados = explode(" ", $contrato["emailContactoContabilidad"]);
			
			foreach($correosSeparados as $key => $value)
			{
				$contacto["empresa"] = $contrato["name"];
				$contacto["correo"] = trim($value);
				$contacto["nombre"] = $contrato["nameContactoContabilidad"];
				$contactos[] = $contacto;
			}
			$correosSeparados = explode("/", $contrato["emailContactoContabilidad"]);
	
			foreach($correosSeparados as $key => $value)
			{
				$contacto["empresa"] = $contrato["name"];
				$contacto["correo"] = trim($value);
				$contacto["nombre"] = $contrato["nameContactoContabilidad"];
				$contactos[] = $contacto;
			}
		}
		
		{
			$correosSeparados = explode(" ", $contrato["emailContactoDirectivo"]);
			
			foreach($correosSeparados as $key => $value)
			{
				$contacto["empresa"] = $contrato["name"];
				$contacto["correo"] = trim($value);
				$contacto["nombre"] = $contrato["nameContactoDirectivo"];
				$contactos[] = $contacto;
			}
			$correosSeparados = explode("/", $contrato["emailContactoDirectivo"]);
	
			foreach($correosSeparados as $key => $value)
			{
				$contacto["empresa"] = $contrato["name"];
				$contacto["correo"] = trim($value);
				$contacto["nombre"] = $contrato["nameContactoDirectivo"];
				$contactos[] = $contacto;
			}
		}		
	}
	
	$cleanedUp = array();
	$correos = array();
	foreach($contactos as $contacto)
	{
		if(in_array($contacto["correo"], $correos))
		{
			continue;
		}
		$correos[] = $contacto["correo"];
		
		$contacto["empresa"] = str_replace(",", " ", $contacto["empresa"]);
		$contacto["nombre"] = str_replace(",", " ", $contacto["nombre"]);
		$contacto["correo"] = str_replace(",", " ", $contacto["correo"]);
		
		$cleanedUp[] = $contacto;
		
	}
	
	foreach($cleanedUp as $correo)
	{
		echo $correo["empresa"].",".$correo["correo"].",".$correo["nombre"]."<br>";
	}
	?>

