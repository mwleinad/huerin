<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case "producto":
	$tipoServicio->setTipoServicioId($_POST["value"]);
	$result= $tipoServicio->Info();
	echo urldecode($result["nombreServicio"]);
	echo "{#}";
	echo $result["costo"];
	echo $result["valorUnitario"];
	echo "{#}";
	echo "NO APLICA";
	echo "{#}";
	break;
	case "impuesto":
	$impuesto->setImpuestoId($_POST["value"]);
	$result = $impuesto->Info();
	echo urldecode($result["nombre"]);
	echo "{#}";
	echo $result["tasa"];
	echo "{#}";
	echo $result["tipo"];
	echo "{#}";
	echo $result["iva"];
	echo "{#}";
	break;

	case "datosFacturacion":
		$userId = $_POST["value"];
		$contract->setContractId($userId, 1);
		$result = $contract->Info();
		if(!$result)
		{
			echo "{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}{#}";
			exit();
		}
		echo "{#}";
		echo "{#}";
		echo "{#}";
		echo $result["nameFacturacion"];
		echo "{#}";
		echo $result["address"];
		echo "{#}";
		echo $result["noExtAddress"];
		echo "{#}";
		echo $result["noIntAddress"];
		echo "{#}";
		echo $result["coloniaAddress"];
		echo "{#}";
		echo $result["municipioAddress"];
		echo "{#}";
		echo $result["cpFacturacion"];
		echo "{#}";
		echo $result["estadoAddress"];
		echo "{#}";
		echo $result["municipioAddress"];
		echo "{#}";
		echo $result["referencia"];
		echo "{#}";
		echo $result["paisAddress"];
		echo "{#}";
		// echo $result["emailContactoAdministrativo"];
		echo $result["email"];
		echo "{#}";
		echo $result["rfcFacturacion"];
		echo "{#}";
		echo $result["idFacturacion"];
		echo "{#}";

	break;
}

?>
