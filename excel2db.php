<?php
	
	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	$lineSeparator = "\n";
	$fieldSeparator = ",";
	$csvFile = 'clientes_vanessa.csv';
	
	if(!file_exists($csvFile)){
		echo 'No se encuentra el archivo '.$csvFile;
		exit;	
	}//if
	
	$file = fopen($csvFile, 'r');
	
	if(!$file){
		echo 'Error al abrir el archivo';
		exit;	
	}//if
	
	$size = filesize($csvFile);
	
	if(!$size){
		echo 'El archivo esta vacio, verifique';
		exit;
	}//if
	
	$lines = 0;
	$regs = 0;
	$promotores = array();
	$empresaId = 15;
	echo "<pre>";
	
	$row = 0;
	$cards = array();
	while (( $field = fgetcsv($file,19192,",")) !== false ) 
	{
		echo $row;
//		print_r($field);
		if($row == 0 || $row == 1)
		{
			$row++;
			continue;
		}

		foreach($field as $key => $value)
		{
			$cards[$key][$row] = $value;
		}
		$row++;
					
	}//while
	
//print_r($cards[1]);	
	foreach($cards as $key => $card)
	{
		if($key == 0 || $key == 1)
		{
			continue;
		}
		if($card[2] == "")
		{
			$card[2] = $card[15];
		}
		
		//insertar cliente
		$db->setQuery("
			INSERT INTO
				customer
			(
				`name`,				
				phone,
				email,				
				street,				
				numExt,				
				numInt,				
				colony,				
				city,				
				state,				
				fechaAlta,				
				active
		)
		VALUES
		(
				'".utf8_decode($card[2])."',				
				'".utf8_decode($card[3])."',
				'".utf8_decode($card[4])."',				
				'".utf8_decode($card[5])."',				
				'".utf8_decode($card[6])."',				
				'".utf8_decode($card[7])."',				
				'".utf8_decode($card[8])."',				
				'".utf8_decode($card[9])."',				
				'".utf8_decode($card[10])."',				
				'".date("Y-m-d")."',				
				'1'
		);");
		//echo $db->query;
		$id = $db->InsertData();

		$card["14"] = trim($card["14"]);
		$card["14"] = ucwords(strtolower($card["14"]));

		$card["17"] =  trim($card["17"]);
		switch($card["17"])
		{
			case "SOCIEDAD ANONIMA": echo $card["17"] = 2; break;
			default : $card["17"] = 0;
		}

		$card["18"] =  trim($card["18"]);
		switch($card["18"])
		{
			case "Titulo II regimen general de ley": $card["18"] = 4; break;
			case "Titulo IV PF Capitulo II Seccion II actividades empresariales": $card["18"] = 4; break;
			default : $card["18"] = 4;
		}
		
		$card["34"] = trim($card["34"]);
		switch($card["34"])
		{
			case "ISAAC ZETINA": $card["34"] = 32; break;
			case "MIGUEL ANGEL BRITO": $card["34"] = 37; break;
			case "MIGUEL ANGEL CASTILLO SAMPEDRO": $card["34"] = 33; break;
			case "ALEJANDRO FUENTES LICONA": $card["34"] = 34; break;
			case "ARACELI": $card["34"] = 38; break;
			case "ELVIRA": $card["34"] = 46; break;
			case "RICARDO GONZALEZ": $card["34"] = 55; break;
			case "GERARDO": $card["34"] = 48; break;
			case "IVON": $card["34"] = 41; break;
			case "GRISELDA": $card["34"] = 42; break;
			case "ALICIA": $card["34"] = 39; break;
			case "DELIA": $card["34"] = 40; break;
			case "VANESSA": $card["34"] = 31; break;
			case "JAVIER": $card["34"] = 47; break;
			case "CESAR": $card["34"] = 45; break;
			case "GONZALO": $card["34"] = 50; break;
			case "ALBERTO": $card["34"] = 51; break;
			case "RICARDO ALEJANDRO": $card["34"] = 54; break;
			case "LUIS": $card["34"] = 52; break;
			case "NORA": $card["34"] = 53; break;
			case "ARTURO": $card["34"] = 44; break;
			case "ALAN": $card["34"] = 43; break;
			default : $card["34"] = 0;
		}
		//print_r($card);
		$card["16"] = str_replace("-", "", $card["16"]);
		$card["16"] = str_replace(" ", "", $card["16"]);

		$db->setQuery("
			INSERT INTO
				contract
			(
				customerId,
				address,
				type,
				sociedadId,
				`name`,
				regimenId,
				nombreComercial,
				direccionComercial,
				nameContactoAdministrativo,
				emailContactoAdministrativo,
				telefonoContactoAdministrativo,
				nameContactoContabilidad,
				emailContactoContabilidad,
				telefonoContactoContabilidad,
				nameContactoDirectivo,
				emailContactoDirectivo,
				telefonoContactoDirectivo,
				telefonoCelularDirectivo,
				claveCiec,
				claveFiel,
				claveIdse,
				rfc,
				noExtComercial,
				noIntComercial,
				coloniaComercial,
				municipioComercial,
				estadoComercial,
				noExtAddress,
				noIntAddress,
				coloniaAddress,
				municipioAddress,
				estadoAddress,
				responsableCuenta,
				claveIsn
			)
			VALUES
			(
				'".$id."',
				'".utf8_decode($card["21"])."',
				'".$card["14"]."',
				'".$card["17"]."',
				'".utf8_decode($card["15"])."',
				'".$card["18"]."',
				'".utf8_decode($card["19"])."',
				'".utf8_decode($card["28"])."',
				'".utf8_decode($card["36"])."',
				'".utf8_decode($card["37"])."',
				'".utf8_decode($card["38"])."',
				'".utf8_decode($card["39"])."',
				'".utf8_decode($card["40"])."',
				'".utf8_decode($card["41"])."',
				'".utf8_decode($card["42"])."',
				'".utf8_decode($card["43"])."',
				'".utf8_decode($card["44"])."',
				'".utf8_decode($card["45"])."',
				'".utf8_decode($card["46"])."',
				'".utf8_decode($card["47"])."',
				'".utf8_decode($card["48"])."',
				'".utf8_decode($card["16"])."',
				'".utf8_decode($card["29"])."',
				'".utf8_decode($card["30"])."',
				'".utf8_decode($card["31"])."',
				'".utf8_decode($card["32"])."',
				'".utf8_decode($card["33"])."',
				'".utf8_decode($card["22"])."',
				'".utf8_decode($card["23"])."',
				'".utf8_decode($card["24"])."',
				'".utf8_decode($card["25"])."',
				'".utf8_decode($card["26"])."',
				'".$card["34"]."',
				'".utf8_decode($card["49"])."'
			)"
		);		
		$db->InsertData();
		//echo $db->query;
		
		echo "Cliente Importado: ";
		echo $card[2];
		echo "<br>";
//		echo $db->query;
	}
	fclose($file);
		
	echo '<br><br>Done!';
		
	echo $lines.' registers imported.';
			
	exit;

?>