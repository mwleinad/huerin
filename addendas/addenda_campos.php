<?php
//echo $data["fromAddenda"];
switch($data["fromAddenda"])
{
	case "Continental":
/*			if(strlen($data["idPedido"]) < 10)
			{
				$vs->Util()->setError(10041, "error", "El campo Id Pedido debe de tener una longitud de al menos 10 caracteres");
			}

			if(strlen($data["idProveedor"]) < 10)
			{
				$vs->Util()->setError(10041, "error", "El campo Id Proveedor debe tener una longitud de al menos 10 caracteres");
			}
*/	break;		

	case "Pepsico":
			if(strlen($data["idPedido"]) < 10)
			{
				$vs->Util()->setError(10041, "error", "El campo Id Pedido debe de tener una longitud de al menos 10 caracteres");
			}

			if(strlen($data["idProveedor"]) < 10)
			{
				$vs->Util()->setError(10041, "error", "El campo Id Proveedor debe tener una longitud de al menos 10 caracteres");
			}
	break;		
	case "Zepto":
	break;		
}
?>