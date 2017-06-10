<?php
		
//	$empresa->AuthUser();
	
	$info = $empresa->Info();
	$smarty->assign('info', $info);
	
//	$empresa->Util()->DB()->setQuery('SELECT * FROM usuario');
//	$result = $empresa->Util()->DB->GetResult();
	
	$rfc->setEmpresaId($_SESSION['empresaId'], 1);
	$rfcs = $rfc->GetRfcsByEmpresa();
	$smarty->assign('empresaRfcs', $rfcs);
	
	//Obtenemos el rfc activo
	$id_rfc = $rfc->getRfcActive();
	$rfc->setRfcId($id_rfc);
	
	$certAnterior = $rfc->GetCertificadoByRfc();

//print_r($_FILES);
	if($_FILES['qr'])
	{
		$continuar = true;

		$ruta_dir = DOC_ROOT.'/empresas/'.$_SESSION['empresaId'].'/qrs';
		$file = '/'.$_POST["serieId"].'_'.basename( $_FILES['qr']['name']);
		$file_new = '/'.$_POST["serieId"];

		//Checamos si existe el directorio
		if(!is_dir($ruta_dir)){
			
			//Sino existe creamos el directorio			
			if(!mkdir($ruta_dir, 0755, true)){
				$errMsg = 'No se pudo crear el directorio.';
				$smarty->assign('errMsg', $errMsg);
				$continuar = false;
			}//else
			
		}//if

		if($continuar)
		{
			$ext = substr($file, strrpos($file, '.') + 1);
  		if (($ext != "png") || ($_FILES["qr"]["type"] != "image/png"))
			{
				$errMsg = 'El archivo debe de tener una extension .PNG.';
				$smarty->assign('errMsg', $errMsg);
				$continuar = false;
			}
		}
		if($continuar)
		{
			if(!move_uploaded_file($_FILES['qr']['tmp_name'], $ruta_dir.$file_new.".".$ext)) 
			{
				$errMsg = 'No se pudo subir el archivo';
				$smarty->assign('errMsg', $errMsg);
				$continuar = false;
			}
			else
			{
				//echo $ruta_dir.$new_file;
				chmod($ruta_dir.$new_file, 0777);
				$cmpMsg = 'Codigo de Barras Bidimensional Asignado';
				$smarty->assign('cmpMsg', $cmpMsg);
				$continuar = true;
			}
		}
	}

	if($_FILES['cedula'])
	{
		$continuar = true;
		print_r($_FILES);
		$ruta_dir = DOC_ROOT.'/empresas/'.$_SESSION['empresaId'].'/qrs';
		$file = '/'.$_POST["serieId"].'_cedula_'.basename( $_FILES['cedula']['name']);

		//Checamos si existe el directorio
		if(!is_dir($ruta_dir)){
			//Sino existe creamos el directorio			
			if(!mkdir($ruta_dir, 0755, true)){
				$errMsg = 'No se pudo crear el directorio.';
				$smarty->assign('errMsg', $errMsg);
				$continuar = false;
			}//else
			
		}//if

		if($continuar)
		{
			$ext = strtolower(substr($file, strrpos($file, '.') + 1));
			echo $ext;
  		if (($ext != "jpg") || ($_FILES["cedula"]["type"] != "image/jpeg"))
			{
				$errMsg = 'El archivo debe de tener una extension .JPG.';
				$smarty->assign('errMsg', $errMsg);
				$continuar = false;
			}
		}

		$file = '/'.$_POST["serieId"].".".$ext;

		if($continuar)
		{
			if(!move_uploaded_file($_FILES['cedula']['tmp_name'], $ruta_dir.$file)) 
			{
				$errMsg = 'No se pudo subir el archivo';
				$smarty->assign('errMsg', $errMsg);
				$continuar = false;
			}
			else
			{
				$cmpMsg = 'Logo agregado';
				$smarty->assign('cmpMsg', $cmpMsg);
				
				//convertir en jepg
				$continuar = true;
			}
		}
	}

	switch($_GET['section']){
		case 'nuevos-folios':
		
			  $folios->setIdRfc($id_rfc);
			  $listFolios = $folios->GetFoliosByRfc();
			  $smarty->assign('folios', $listFolios);				
			  
			  break;	
	}//switch
	
//	$DOC_ROOT = $_SERVER['DOCUMENT_ROOT'];
	$id_empresa = $_SESSION['empresaId'];
		
	$ruta_dir = DOC_ROOT.'/empresas/'.$id_empresa.'/certificados/'.$id_rfc;
		
	if($_POST['accion'] == 'guardar_certificado'){
									
		$continuar = true;
						
		//Checamos si existe el directorio
		if(!is_dir($ruta_dir)){
			
			//Sino existe creamos el directorio			
			if(!mkdir($ruta_dir, 0755, true)){
				$errMsg = 'Hubo un error al actualizar el certificado. No se pudo crear el directorio.';
				$smarty->assign('errMsg', $errMsg);
				$continuar = false;
			}//else
			
		}//if
						
		if($continuar){
			
			//Certificado .key
			$no_certificado = $_POST['certificado'];
			$pass = $_POST['pass_llave'];
			$generar_sello = true;
			
			if(empty($pass)){
				$smarty->assign('errPass', 'Ingrese la contrasena de la llave privada.');
				$generar_sello = false;
			}//if
			
			$tmp_archivo = $_FILES['file_certificado']['tmp_name']; 
			$nombre_archivo = $_FILES['file_certificado']['name']; 
			$file = pathinfo($nombre_archivo);
			$ext = $file['extension'];
			$ruta_destino = $ruta_dir.'/'.$nombre_archivo;			
			
			if(strlen($file['filename']) != 20){				
				$smarty->assign('errCertificado', 'Nombre del certificado invalido. Debe tener 20 caracteres.');
				$generar_sello = false;	
				@unlink($ruta_destino);		
			}elseif($ext != 'cer'){
				$smarty->assign('errCertificado', 'Certificado invalido. Debe ser extension .cer');
				$generar_sello = false;
				@unlink($ruta_destino);
			}else{ 				
				if(!move_uploaded_file($tmp_archivo, $ruta_destino)){
					$errMsg = 'Hubo un error al actualizar el certificado. No se pudo guardar el certificado.';
					$smarty->assign('errMsg', $errMsg);
					$generar_sello = false;
				}//if
			}//else
			
			//Llave privada	.cer				
			$tmp_archivo2 = $_FILES['file_llave']['tmp_name']; 
			$nombre_archivo2 = $_FILES['file_llave']['name']; 
			$file2 = pathinfo($nombre_archivo2);
			$ext2 = $file2['extension'];
			$ruta_destino2 = $ruta_dir.'/'.$nombre_archivo2;			
			
			if($ext2 != 'key'){
				$smarty->assign('errLlave', 'Llave privada invalida. Debe ser extension .key');
				@unlink($ruta_destino);
				@unlink($ruta_destino2);
				$generar_sello = false;				
			}else{ 				
				if(!move_uploaded_file($tmp_archivo2, $ruta_destino2)){
					$errMsg = 'Hubo un error al actualizar el certificado. No se pudo guardar la llave privada.';
					$smarty->assign('errMsg', $errMsg);
					$generar_sello = false;				
				}//if
			}//else
			
			if($generar_sello){
			
				$cadenaOriginal = 'abcdefghijklmnopqrstuvwxyz';
				$md5 = md5($cadenaOriginal);
				$certificado = $nombre_archivo2;
				$llave = $nombre_archivo;
				
				//Borramos los archivos del certificado anterior
				
				if(is_dir($ruta_dir)){
					if($gd = opendir($ruta_dir)){
						while($archivo = readdir($gd)){				
							$info = pathinfo($ruta_dir.'/'.$archivo);
							$archivoActual = $info['basename'];
							if($archivoActual != $certificado && $archivoActual != $llave){								
								@unlink($ruta_dir.'/'.$archivoActual);
							}//if
						}//while
						closedir($gd);
					}//if
				}//if
				
				$comprobante->GenerarSelloGral($cadenaOriginal, $md5, $certificado, $llave, $pass, $id_rfc);
				
				//Checamos si se genero el archivo
				$status = '';
				$ruta_verified = $ruta_dir.'/verified.txt';
				
				if(file_exists($ruta_verified))
					$status = file_get_contents($ruta_verified);
				
				if(trim($status) == 'Verified OK'){
					
					//guardamos el password
					$myFile = $ruta_dir."/password.txt";
					$fh = fopen($myFile, 'w');
					$stringData = $pass;
					fwrite($fh, $stringData);
					
					$smarty->assign('cmpMsg', 'Certificado actualizado correctamente.');				
				}else{
					$errMsg = 'Error al actualizar el certificado. Los datos no coinciden.';
					$smarty->assign('errMsg', $errMsg);					
					
					//Borramos los archivos
					if(is_dir($ruta_dir)){
						if($gd = opendir($ruta_dir)){
							while($archivo = readdir($gd)){
								@unlink($ruta_dir.'/'.$archivo);
							}//while
							closedir($gd);
						}//if
					}//if
					
				}//else				
				
			}else{
				@unlink($ruta_destino);
				@unlink($ruta_destino2);
			}//else	
			
		}//if			
					
	}//if Guardar Certificado
		
	$certNuevo = $rfc->GetCertificadoByRfc();
	
	if($certNuevo == '')
		$nom_certificado = 'No hay certificados.';
	else{
	
		$sqlQuery = 'UPDATE serie SET noCertificado = "'.$certNuevo.'" WHERE noCertificado = "'.$certAnterior.'"';
		$folios->Util()->DBSelect($_SESSION["empresaId"])->setQuery($sqlQuery);
		$folios->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();
		
		$existe_certificado = true;
		$nom_certificado = $certNuevo;
	}//else
	$smarty->assign('nom_certificado', $nom_certificado);
	
	//Expiration Date
	if($existe_certificado){	
		$expire = exec('openssl x509 -noout -in '.$ruta_dir.'/'.$nom_certificado.'.cer.pem -dates');
		$exp = explode('=',$expire);
		$fecha_expiracion = $exp[1];
		$fecha_exp = date('d-m-Y g:i:s a',strtotime($fecha_expiracion));
		$smarty->assign('fecha_expiracion', $fecha_exp);
	}//if
	$smarty->assign('mainMnu','admin-folios');  
	
?>