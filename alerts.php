<?php

if($_SERVER['DOCUMENT_ROOT'] != "/opt/lampp/htdocs")
{
	$docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin_test";
}
else
{
	$docRoot = $_SERVER['DOCUMENT_ROOT'];
}

	define('DOC_ROOT', $docRoot);


	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	
	$employees = $personal->Enumerate();
	
	$subject = "Servicios Pendientes de Entrega";
	foreach($employees as $employee)
	{
		$servicios = $servicio->EnumerateActive($employee["personalId"]);
		$message = "A continuacion se enlistan los servicios que no has completado<br>";
		
		foreach($servicios as $servicio)
		{
      $message .= "Servicio:<b>".$servicio["nombreServicio"]."</b><br>";
      $message .= "Nombre del Cliente:<b>".$servicio["clienteName"]."</b><br>";
      $message .= "Nombre de la Razon Social:<b>".$servicio["razonSocialName"]."</b><br>";

			foreach($servicio["instancias"] as $instanciaServicio)
			{

				if($instanciaServicio["status"] == "completa")
				{
					continue;
				}
        $message .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$instanciaServicio["date"].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank" href="http://avantikads.com/despacho/workflow/id/'.$instanciaServicio["instanciaServicioId"].'">Ver Servicio</a><br>';
				
			}
			$message .= "<br>";
		}
		$to = $employee["email"];
		$body = $message;
		
		$sendmail->Prepare($subject, $body, $to, $toName, "", "", "", "", $from = "admin@avantikdads.com", $fromName = "Administrador del Sistema");

	//mail($email, $subject, $message);
//		echo $message;
//		exit();
	}
	exit();
	
//	print_r($servicios);
	
	$day = date("d");

	foreach($servicios as $servicio)
	{
		
		foreach($servicio["instancias"] as $instanciaServicio)
		{
			$servicio["completo"] = "no";
			if($instanciaServicio["status"] == "completa")
			{
				$servicio["completo"] = "si";
				continue;
			}
			if($instanciaServicio["instanciaServicioId"] != 83)
			{
//				continue;
			}
			
			$workflow->setInstanciaServicioId($instanciaServicio["instanciaServicioId"]);
			$myWorkflow = $workflow->Info();
			$messages = array();
			foreach($myWorkflow["steps"] as $step)
			{
				if($step["stepCompleted"] == 1)
				{
					continue;
				}

				$messageTarea = array();
				foreach($step["tasks"] as $task)
				{
					if($task["taskCompleted"] == 1)
					{
						continue;
					}
					//preventive email
					if($day > $task["diaVencimiento"] + $task["prorroga"])
					//if(1 == 2)
					{
						$messageTarea[] = "La tarea '".$task["nombreTask"]."' ha finalizado su tiempo de prorroga\nCliente: ".$servicio["clienteName"]."\nRazon Social: ".$servicio["razonSocialName"].".\nFecha de Servicio: ".$instanciaServicio["monthShow"]."\nDia Vencimiento: ".$task["diaVencimiento"]."\nProrroga: ".$task["prorroga"]." Dias\n* - - - - - - - *";
						
						$messages[] = "\nLA SIGUIENTE TAREA HA AGOTADO SU TIEMPO DE PRORROGA\nCliente: ".$servicio["clienteName"]."\nCuenta: ".$servicio["razonSocialName"]."\nTarea Pendiente: ".$task["nombreTask"]."\nPaso: ".$step["nombreStep"]."\nFecha de Servicio: ".$instanciaServicio["monthShow"]."\nDia Vencimiento: ".$task["diaVencimiento"]."\nProrroga: ".$task["prorroga"]." Dias";

						$tipoDeMail = "prorroga";
					}
					elseif($day > $task["diaVencimiento"])
//					elseif(1 == 2)
					{
						$messageTarea[] = "La tarea '".$task["nombreTask"]."' ha finalizado su tiempo de Vencimiento\nCliente: ".$servicio["clienteName"]."\nRazon Social: ".$servicio["razonSocialName"].".\nFecha de Servicio: ".$instanciaServicio["monthShow"]."\nDia Vencimiento: ".$task["diaVencimiento"]."\nProrroga: ".$task["prorroga"]." Dias\n* - - - - - - - *";
						
						$messages[] = "\nLA SIGUIENTE TAREA HA VENCIDO\nCliente: ".$servicio["clienteName"]."\nCuenta: ".$servicio["razonSocialName"]."\nTarea Pendiente: ".$task["nombreTask"]."\nPaso: ".$step["nombreStep"]."\nFecha de Servicio: ".$instanciaServicio["monthShow"]."\nDia Vencimiento: ".$task["diaVencimiento"]."\nProrroga: ".$task["prorroga"]." Dias";
						
						$tipoDeMail = "vencimiento";
					}
					elseif($day > $task["diaVencimiento"] - 3)
//					elseif (1 == 1)
					{
						$messageTarea[] = "La tarea '".$task["nombreTask"]."' esta proxima a su tiempo de vencimiento\nCliente: ".$servicio["clienteName"]."\nRazon Social: ".$servicio["razonSocialName"].".\nFecha de Servicio: ".$instanciaServicio["monthShow"]."\nDia Vencimiento: ".$task["diaVencimiento"]."\nProrroga: ".$task["prorroga"]." Dias\n* - - - - - - - *";

						$messages[] = "\nLA SIGUIENTE TAREA ESTA PROXIMA A VENCER\nCliente: ".$servicio["clienteName"]."\nCuenta: ".$servicio["razonSocialName"]."\nTarea Pendiente: ".$task["nombreTask"]."\nPaso: ".$step["nombreStep"]."\nFecha de Servicio: ".$instanciaServicio["monthShow"]."\nDia Vencimiento: ".$task["diaVencimiento"]."\nProrroga: ".$task["prorroga"]." Dias";
						$tipoDeMail = "preventivo";
					}
				}//foreach tasks
				
				//enviar mail al responsable de las tareas faltantes
				$subject = "ALERTA DE PASOS: Las siguientes tareas del paso '".$step["nombreStep"]."' del Servicio ".$servicio["razonSocialName"]." '".$servicio["nombreServicio"]."' requieren de su atencion.";
				foreach($messageTarea as $mess)
				{
					$messTarea.="\n".$mess."\n";
				}
			//	echo $messTarea;
				$email = $servicio["responsableCuentaEmail"];
				//mail($email, $subject, $messTarea);
				
			}//foreach steps

				$subject = "ALERTA: El Servicio de ".$servicio["razonSocialName"]." '".$servicio["nombreServicio"]."' requiere de su atencion";
				$message = "Cliente: ".$servicio["clienteName"]."\nRazon Social: ".$servicio["razonSocialName"].".\nFecha de Servicio: ".$instanciaServicio["monthShow"]."\n";
				$message.="El responsable encargado de este servicio es: ".$servicio["responsableCuentaName"]." ".$servicio["responsableCuentaEmail"]."\n * = = = = = = = = *";
				
				foreach($messages as $mess)
				{
					$message.="\n".$mess;
				}
				//enviar mail a los superiores
				$email = $servicio["responsableCuentaEmail"];
//				$email = "dlopez@trazzos.com";
				//mail($email, $subject, $message);

				if($contadorInfo)
				{
					$email = $contadorInfo["email"];
//					$email = "dlopez@trazzos.com";
					//mail($email, $subject, $message);
				}

				if($supervisorInfo)
				{
					$email = $supervisorInfo["email"];
//				$email = "dlopez@trazzos.com";
					//mail($email, $subject, $message);
				}

				if($gerenteInfo)
				{
					$email = $gerenteInfo["email"];
//				$email = "dlopez@trazzos.com";
					//mail($email, $subject, $message);
				}

				if($socioInfo)
				{
					$email = $gerenteInfo["email"];
			//	$email = "dlopez@trazzos.com";
					//mail($email, $subject, $message);
				}

		}//foreach instance
		
/*		if($servicio["completo"] == "no")
		{
			$personal->setPersonalId($servicio["encargadoCuenta"]);
			$personalInfo = $personal->Info();
			
			$personal->setPersonalId($personalInfo["jefeGerente"]);
			$gerenteInfo = $personal->Info();

			$personal->setPersonalId($personalInfo["jefeSocio"]);
			$socioInfo = $personal->Info();

			$subject = "El tiempo de Prorroga para uno de los servicios ha terminado";
			$message = "El servicio '".$servicio["nombreServicio"]."' ha superado su tiempo de prorroga:\n\nEl Auxiliar encargado de esta cuenta es: ".$servicio["responsableCuentaEmail"].".\n\nEl Responsable de esta cuenta es: ".$servicio["responsableCuentaName"]."\n\nEl Encargado de esta cuenta es: ".$servicio["encargadoCuentaName"]."\n\nCliente: ".$servicio["clienteName"]."\nCuenta: ".$servicio["razonSocialName"];			
				//enviar mail al contador
			$email = $gerenteInfo["email"];
			//mail($email, $subject, $message);

			$message = "El servicio '".$servicio["nombreServicio"]."' ha superado su tiempo de prorroga:\n\nEl Auxiliar encargado de esta cuenta es: ".$servicio["responsableCuentaEmail"].".\n\nEl Responsable de esta cuenta es: ".$servicio["responsableCuentaName"]."\n\nEl Encargado de esta cuenta es: ".$servicio["encargadoCuentaName"]."\n\nEl Gerente de esta cuenta es: ".$gerenteInfo["name"]."\n\nCliente: ".$servicio["clienteName"]."\nCuenta: ".$servicio["razonSocialName"];			

			$email = $socioInfo["email"];
			//mail($email, $subject, $message);

		}
*/	}//foreach servicio
?>