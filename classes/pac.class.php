<?php

class Pac extends Util
{
	function CancelaCfdi($user, $pw, $rfc, $uuid, $pfx, $pfxPassword)
	{
		//open zip and encode it
		$fh = fopen($pfx, 'r');
		$theData = fread($fh, filesize($pfx));
		$zipFileEncoded = base64_encode($theData);
		fclose($fh);

		require_once(DOC_ROOT.'/libs/nusoap.php');
		$client = new nusoap_client('https://cfdiws.sedeb2b.com/EdiwinWS/services/CFDi?wsdl', true);
		$client->useHTTPPersistentConnection();

		$params = array(
			'user' => $user,
			'password' => $pw,
			'rfc' => $rfc,
			'uuid' => $uuid,
			'pfx' => "$zipFileEncoded",
			'pfxPassword' => $pfxPassword
		);

        $response = $client->call('cancelaCFDi', $params, 'http://cfdi.service.ediwinws.edicom.com/');
		//errors
		if($response["faultcode"])
		{
			print_r($response);
			return "fault";
		}
		return $response;
	}
	/*
	 * funcion CancelaCfdi2018
	 * recibe:
	 * $user = usuario pac
	 * $pw =  contraseña pac
	 * $rfcE = Rfc emisor
	 * $rfcR = Rfc receptor
	 * $uuid = timbre a cancelar
	 * $total = total monto a cancelar viene de la bd
	 * $pfx = certificado del emisor
	 * $pfxpassword =  contraseña de la llave privada
	 * Devuelve:
	 * $data =  contiene el mensaje a mostrar al usuario y e informacion que servira para cambiar el status
	 * de la factura en la bd
	 */
	function getStatusCfdi($user, $pw,$rfcE,$rfcR, $uuid,$total,$pfx, $pfxPassword){
        $fh = fopen($pfx, 'r');
        $theData = fread($fh, filesize($pfx));
        $zipFileEncoded = base64_encode($theData);
        fclose($fh);
        require_once(DOC_ROOT.'/libs/nusoap.php');
        $client = new nusoap_client('https://cfdiws.sedeb2b.com/EdiwinWS/services/CFDi?wsdl', true);
        $client->useHTTPPersistentConnection();
        if(PROJECT_STATUS == "test")
            $isTest = true;
        else
            $isTest = false;

        $params = array(
            'user' => $user,
            'password' => $pw,
            'rfcE' => $rfcE,
            'rfcR' => $rfcR,
            'uuid' => $uuid,
            'total' => $total,
            'pfx' => $zipFileEncoded,
            'pfxPassword'=>$pfxPassword,
            'test'=>$isTest
        );
        $response = $client->call('getCFDiStatus', $params, 'http://cfdi.service.ediwinws.edicom.com/');
        return $response;
    }
    function CancelaCfdi2018($user, $pw,$rfcE,$rfcR, $uuid,$total,$pfx, $pfxPassword)
    {
        $fh = fopen($pfx, 'r');
        $theData = fread($fh, filesize($pfx));
        $zipFileEncoded = base64_encode($theData);
        fclose($fh);
        require_once(DOC_ROOT.'/libs/nusoap.php');
        $client = new nusoap_client('https://cfdiws.sedeb2b.com/EdiwinWS/services/CFDi?wsdl', true);
        $client->useHTTPPersistentConnection();
        if(PROJECT_STATUS == "test")
            $isTest = true;
        else
            $isTest = false;

        $params = array(
            'user' => $user,
            'password' => $pw,
            'rfcE' => $rfcE,
            'rfcR' => $rfcR,
            'uuid' => $uuid,
            'total' => $total,
            'pfx' => $zipFileEncoded,
            'pfxPassword'=>$pfxPassword,
            'test'=>$isTest
        );
        $data = [];
        $response = $client->call('cancelCFDiAsync', $params, 'http://cfdi.service.ediwinws.edicom.com/');
        /*if($rfcR=='XAXX010101000'){
            dd($response);
            $cancelado = $client->call('getCFDiStatus', $params, 'http://cfdi.service.ediwinws.edicom.com/');
            dd($cancelado);
        }*/
        if($response['cancelCFDiAsyncReturn']['status']==201||$response['detail']['fault']['cod']==201){
            $cancelado = $client->call('getCFDiStatus', $params, 'http://cfdi.service.ediwinws.edicom.com/');
            $data['cancelado'] = true;
            switch ($cancelado['getCFDiStatusReturn']['status']){
                case 'Vigente':
                    $data['conAceptacion'] = true;
                    $data['message'] = "La solicitud de cancelacion ha sido enviado correctamente. Este proceso puede tardar hasta 72 horas.";
                    if($cancelado['getCFDiStatusReturn']['isCancelable']=='No Cancelable'){
                        $data['cancelado'] = false;
                        $data['message'] = "Factura no cancelable, verificar documentos relacionados.";
                    }
                break;
                case 'Cancelado':
                    $data['conAceptacion'] = false;
                    $data['message'] = "Documento cancelado correctamente";
                break;
            }
        }else{
            $cancelado = $client->call('getCFDiStatus', $params, 'http://cfdi.service.ediwinws.edicom.com/');
            if($response['cancelCFDiAsyncReturn']['cancelQueryData']['status']!='No Encontrado'){
                switch($response['cancelCFDiAsyncReturn']['cancelQueryData']['status']){
                    case 'Cancelado':
                        $data['cancelado'] =  true;
                        $data['message'] =  "Documento cancelado correctamente.";
                    break;
                    default: //aqui se atrapa todo tipo de respuesta ,se da por echo que fue error,en caso de que el campo isCancelable diga no cancelable  se cambia el mensaje.
                        $data['cancelado'] =  false;
                        $data['message'] ="Error al cancelar: ".$cancelado['getCFDiStatusReturn']['status']." ".$cancelado['getCFDiStatusReturn']['statusCode'];
                        if($response['cancelCFDiAsyncReturn']['cancelQueryData']['isCancelable']=='No Cancelable'){
                            $data['message'] = "Factura no cancelable, verificar si cuenta con documentos relacionados e intentar nuevamente.";
                        }
                    break;
                }
            }else{
                if(strpos($response['cancelCFDiAsyncReturn']['cancelQueryData']['cancelStatus'],'Cancelado')!==false){
                    $data['cancelado'] =  true;
                    $data['message'] =  "Documento cancelado correctamente.";
                }else{
                    $data['cancelado'] =  false;
                    $data['message'] =  "Error al cancelar: ".$cancelado['getCFDiStatusReturn']['status']." ".$cancelado['getCFDiStatusReturn']['statusCode'];
                }
            }

        }
        //errors
        return $data;
    }
	function GetCfdi($user, $pw, $zipFile, $path, $newFile, $empresa)
	{
		//open zip and encode it
		$fh = fopen($zipFile, 'r');
		$theData = fread($fh, filesize($zipFile));
		$zipFileEncoded = base64_encode($theData);
		fclose($fh);

		require_once(DOC_ROOT.'/libs/nusoap.php');
		$client = new nusoap_client('https://cfdiws.sedeb2b.com/EdiwinWS/services/CFDi?wsdl', true);
		$client->useHTTPPersistentConnection();

		$params = array(
			'user' => $user,
			'password' => $pw,
			'file' => "$zipFileEncoded"
		);

		//demo
		if(PROJECT_STATUS == "test")
		{
			$response = $client->call('getCfdiTest', $params, 'http://cfdi.service.ediwinws.edicom.com/');
			if($response["faultcode"])
			{
				print_r($response);
				return "fault";
			}
			$data = base64_decode($response["getCfdiTestReturn"]);
		}
		else
		{
			$response = $client->call('getCfdi', $params, 'http://cfdi.service.ediwinws.edicom.com/');
			if($response["faultcode"])
			{
				print_r($response);
				return "fault";
			}
			$data = base64_decode($response["getCfdiReturn"]);
		}

		$fh = fopen($newFile, 'w') or die("can't open file");
		$fh = fwrite($fh, $data);

		$this->Unzip($path, $newFile);

		return $response;
	}

	function GetTimbreCfdi($user, $pw, $zipFile, $path, $newFile, $empresa)
	{
		//open zip and encode it
		$fh = fopen($zipFile, 'r');
		$theData = fread($fh, filesize($zipFile));
		$zipFileEncoded = base64_encode($theData);
		fclose($fh);

		require_once(DOC_ROOT.'/libs/nusoap.php');
		$client = new nusoap_client('https://cfdiws.sedeb2b.com/EdiwinWS/services/CFDi?wsdl', true);
		$client->useHTTPPersistentConnection();

		$params = array(
			'user' => $user,
			'password' => $pw,
			'file' => "$zipFileEncoded"
		);

		if(PROJECT_STATUS == "test")
		{
			$response = $client->call('getTimbreCfdiTest', $params, 'http://cfdi.service.ediwinws.edicom.com/');
			$data = base64_decode($response["getTimbreCfdiTestReturn"]);
		}
		else
		{
			$response = $client->call('getTimbreCfdi', $params, 'http://cfdi.service.ediwinws.edicom.com/');
			$data = base64_decode($response["getTimbreCfdiReturn"]);
		}
		//save new zip
		$fh = fopen($newFile, 'w') or die("can't open file");
		$fh = fwrite($fh, $data);
//		fclose($fh);
		$this->Unzip($path."/timbres/", $newFile);

		return $newFile;
	}

	function ParseTimbre($file)
	{
		$fh = fopen($file, 'r');
		$theData = fread($fh, filesize($file));
		$pos = strrpos($theData, "<tfd:TimbreFiscalDigital");
		$theData = substr($theData, $pos);

		$pos = strrpos($theData, "</cfdi:Complemento>");
		$theData = substr($theData, 0, $pos);

		$xml = @simplexml_load_string($theData);

		$data = array();
		foreach($xml->attributes() as $key => $attribute)
		{
			$data[$key] = (string)$attribute;
		}
		return $data;
	}

	function GenerateCadenaOriginalTimbre($data)
	{
		$cadenaOriginal = "||";
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["version"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["UUID"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["FechaTimbrado"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["selloCFD"]);
		$cadenaOriginal .= $this->Util()->CadenaOriginalVariableFormat($data["noCertificadoSAT"]);
		$cadenaOriginal .= "|";

		$cadena = utf8_encode($cadenaOriginal);
		$data["original"] = $cadena;
		$data["sha1"] = sha1($cadena);
		return $data;
	}

}


?>
