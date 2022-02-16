<?php

class Producto extends Sucursal
{
	private $noIdentificacion;

	private $cantidad;
	private $unidad;
	private $descripcion;
	private $valorUnitario;
	private $importe;
	private $excentoIva;
	private $id_producto;
	private $tasa;
	private $impuesto;
	private $tipo;
	private $tasaIva;
	private $importeImpuesto;
	private $setCategoriaConcepto;

	public function setTasaIva($value)
	{
		$this->Util()->ValidateFloat($value, 6);
		$this->tasaIva = $value;
	}

	public function getTasaIva()
	{
		return $this->tasaIva;
	}

	public function setImpuesto($value)
	{
		$this->Util()->ValidateString($value, $max_chars=255, $minChars = 1, "Impuesto");
		$this->impuesto = $value;
	}

	public function getImpuesto()
	{
		return $this->impuesto;
	}

	public function setCategoriaConcepto($value)
	{
		$this->Util()->ValidateString($value, $max_chars=255, $minChars = 0, "Categoria Concepto");
		$this->categoriaConcepto = $value;
	}

	public function getCategoriaConcepto()
	{
		return $this->categoriaConcepto;
	}

	public function setTipo($value)
	{
		$this->Util()->ValidateString($value, $max_chars=255, $minChars = 1, "Tipo");
		$this->tipo = $value;
	}

	public function getTipo()
	{
		return $this->tipo;
	}

	public function setTasa($value)
	{
		$this->Util()->ValidateFloat($value, 6);
		$this->tasa = $value;
	}

	public function getTasa()
	{
		return $this->tasa;
	}

	public function setNoIdentificacion($value)
	{
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "No. Identificacion");
		$this->noIdentificacion = $value;
	}

	public function getNoIdentificacion()
	{
		return $this->noIdentificacion;
	}

	public function setExcentoIva($value)
	{
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "No. Identificacion");
		$this->excentoIva = $value;
	}

	public function getExcentoIva()
	{
		return $this->excentoIva;
	}

	private $cuentaPredial;
	public function setCuentaPredial($value)
	{
		$this->Util()->ValidateString($value, $max_chars=200, $minChars = 0, "Cuenta Predial");
		$this->cuentaPredial = $value;
	}

	private $claveProdServ;
	public function setClaveProdServ($value)
	{
		$this->Util()->ValidateString($value, $max_chars=200, $minChars = 0, "Clave Prod Serv");
		$this->claveProdServ = $value;
	}
	private $claveUnidad;
	public function setClaveUnidad($value)
	{
		$this->Util()->ValidateString($value, $max_chars=200, $minChars = 0, "Clave Unidad");
		$this->claveUnidad = $value;
	}
	private $fechaCorrespondiente;
	public function setFechaCorrespondiente($value)
	{
		$this->fechaCorrespondiente = $value;
	}
	private $iepsTasaOCuota;
	public function setIepsTasaOCuota($value)
	{
		$this->Util()->ValidateString($value, $max_chars=200, $minChars = 0, "IEPS Tasa o Cuota");
		$this->iepsTasaOCuota = $value;
	}

	public function setUnidad($value)
	{
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 1, "Unidad");
		$this->unidad = $value;
	}

	public function getUnidad()
	{
		return $this->unidad;
	}

	public function setCantidad($value)
	{
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 1, "Cantidad");
		$this->Util()->ValidateFloat($value, 6);
		$this->cantidad = $value;
	}

	public function getCantidad()
	{
		return $this->cantidad;
	}

	public function setValorUnitario($value)
	{
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 1, "Valor Unitario");
		$this->Util()->ValidateFloat($value, 6);
		$this->valorUnitario = $value;
	}

	public function getValorUnitario()
	{
		return $this->valorUnitario;
	}

	public function setImporteImpuesto($value)
	{
		$this->Util()->ValidateFloat($value, 6);
		$this->importeImpuesto = $value;
	}

	public function getImporteImpuesto()
	{
		return $this->importeImpuesto;
	}

	public function setImporte()
	{
		$value = $this->valorUnitario * $this->cantidad;
		$this->Util()->ValidateFloat($value, 6);
		$this->importe = $value;
	}

	public function setExcentoIsh($value)
	{
		$this->Util()->ValidateString($value, $max_chars=50, $minChars = 0, "Excento Ish");
		$this->excentoIsh = $value;
	}

	public function setPorcentajeIsh($value)
	{
		$this->Util()->ValidateFloat($value, 6, 100, 0);
		$this->porcentajeIsh = $value;
	}

	public function getPorcentajeIsh()
	{
		return $this->porcentajeIsh;
	}

	public function setPorcentajeIeps($value)
	{
		$this->Util()->ValidateFloat($value, 6, 100, 0);
		$this->porcentajeIeps = $value;
	}

	public function getPorcentajeIeps()
	{
		return $this->porcentajeIeps;
	}

	public function getImporte()
	{
		return $this->importe;
	}

	public function setDescripcion($value)
	{
		$this->Util()->ValidateString($value, $max_chars=10000, $minChars = 1, "Descripcion");
		/*$this->Util()->DB()->setQuery("select nombreServicio from tipoServicio where tipoServicioId='".$this->noIdentificacion."' ");
		$service = $this->Util()->DB()->GetSingle();
		$serviceConcat = $service." CORRESPONDIENTE";
		$serviceConcat = strtoupper($serviceConcat);
		if(stripos($value,$serviceConcat,0)===false)
		    $this->Util()->setError(0,"error","Descripcion  no valida");*/
		$this->descripcion = $value;
	}

	public function getDescripcion()
	{
		return $this->descripcion;
	}

	function Suggest($value)
	{
		$this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("SELECT * FROM producto WHERE noIdentificacion LIKE '%".$value."%' AND empresaId = ".$_SESSION['empresaId']." ORDER BY noIdentificacion");

		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();

		return $result;
	}

	function GetProductosByRfc()
	{
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT COUNT(*) FROM producto WHERE rfcId = ".$this->getRfcActive()." ORDER BY noIdentificacion");
		$total = $this->Util()->DBSelect($_SESSION["empresaId"])->GetSingle();

		$pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/admin-productos/nuevos-productos");

		$sqlAdd = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];

		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM producto WHERE rfcId = ".$this->getRfcActive()." ORDER BY noIdentificacion ".$sqlAdd);

		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetResult();

		$data["items"] = $result;
		$data["pages"] = $pages;

		return $data;
	}

	function GetProductoInfo()
	{
		$empresa = $this->Info();
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM producto WHERE noIdentificacion = '".$this->noIdentificacion."'  AND empresaId = ".$empresa["empresaId"]);

		$result = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

		return $result;

	}

	function AgregarConcepto($key = -1)
	{
		global $months;
		if($this->Util()->PrintErrors())
			return false;
		end($_SESSION["conceptos"]);
		$conceptos = $key >= 0 ? $key : key($_SESSION["conceptos"]) + 1;
		$_SESSION["conceptos"][$conceptos]["noIdentificacion"] = $this->noIdentificacion;
		$_SESSION["conceptos"][$conceptos]["cantidad"] = $this->cantidad;
		$_SESSION["conceptos"][$conceptos]["unidad"] = $this->unidad;
		$_SESSION["conceptos"][$conceptos]["valorUnitario"] = $this->valorUnitario;
		$_SESSION["conceptos"][$conceptos]["importe"] = $this->importe;
		$_SESSION["conceptos"][$conceptos]["excentoIva"] = $this->excentoIva;
		if($this->Util()->isValidateDate($this->fechaCorrespondiente, 'Y-m-d')) {
			$fecha = explode("-", $this->fechaCorrespondiente);
			$fechaText = strtoupper($months[$fecha[1]]." DEL ".$fecha["0"]);
			$descripcion = $_POST["nombreServicioOculto"]." CORRESPONDIENTE AL MES ".$fechaText;
		} else {
			$descripcion = urldecode($this->descripcion);
		}
		$_SESSION["conceptos"][$conceptos]["descripcion"] = $descripcion;
		$_SESSION["conceptos"][$conceptos]["categoriaConcepto"] = urldecode($this->categoriaConcepto);
		$_SESSION["conceptos"][$conceptos]["claveProdServ"] = $this->claveProdServ;
		$_SESSION["conceptos"][$conceptos]["claveUnidad"] = $this->claveUnidad;
		$_SESSION["conceptos"][$conceptos]["fechaCorrespondiente"] = $this->fechaCorrespondiente;
		return true;
	}

	function AgregarImpuesto()
	{
		if($this->Util()->PrintErrors())
		{
			return false;
		}

		end($_SESSION["impuestos"]);
		$impuestos = key($_SESSION["impuestos"]) + 1;
		$_SESSION["impuestos"][$impuestos]["tasa"] = $this->tasa;
		$_SESSION["impuestos"][$impuestos]["impuesto"] = urldecode($this->impuesto);
		$_SESSION["impuestos"][$impuestos]["tipo"] = $this->tipo;
		$_SESSION["impuestos"][$impuestos]["importe"] = $this->importeImpuesto;
		$_SESSION["impuestos"][$impuestos]["parent"] = 0;
		$_SESSION["impuestos"][$impuestos]["tasaIva"] = $this->tasaIva;

		//desglosar otro para el iva
		if($this->tasaIva)
		{
			end($_SESSION["impuestos"]);
			$impuestos = key($_SESSION["impuestos"]) + 1;
			$_SESSION["impuestos"][$impuestos]["tasa"] = $this->tasa * ($this->tasaIva / 100);
			$_SESSION["impuestos"][$impuestos]["impuesto"] = urldecode($this->tasaIva."% IVA ".$this->impuesto);
			$_SESSION["impuestos"][$impuestos]["tipo"] = $this->tipo;
			$_SESSION["impuestos"][$impuestos]["importe"] = $this->importeImpuesto * ($this->tasaIva / 100);
//			$_SESSION["impuestos"][$impuestos]["parent"] = $impuestos - 1;
			$_SESSION["impuestos"][$impuestos]["tasaIva"] = $this->tasaIva;

		}
		//	print_r($_SESSION);
		return true;
	}

	function BorrarImpuesto($key)
	{
		unset($_SESSION["impuestos"][$key]);
		return true;
	}

	function BorrarConcepto($key)
	{
		unset($_SESSION["conceptos"][$key]);
		return true;
	}

	function CleanImpuestos()
	{
		unset($_SESSION["impuestos"]);
	}

	function CleanConceptos()
	{
		unset($_SESSION["conceptos"]);
	}

	function GetTotalDesglosado( $data = [])
	{
		$values = explode("&", $_POST["form"]);
		foreach($values as $key => $val)
		{
			$array = explode("=", $values[$key]);
			$data[$array[0]] = $array[1];
		}

		if(!$_SESSION["conceptos"])
		{
			return false;
		}

		$data["subtotal"] = 0;
		$data["descuento"] = 0;
		$data["iva"] = 0;
		$data["ieps"] = 0;
		$data["retIva"] = 0;
		$data["retIsr"] = 0;
		$data["total"] = 0;

		foreach($data as $key => $value)
		{
			$data[$key] = $this->Util()->RoundNumber($data[$key]);
		}

echo "<pre>";
		foreach($_SESSION["conceptos"] as $key => $concepto)
		{
			//cada concepto correrle los impuestos extra.
			if($_SESSION["impuestos"])

			{
				$importe = $concepto["importe"];
				foreach($_SESSION["impuestos"] as $keyImpuesto => $impuesto)
				{

			//		print_r($impuesto);
					//impuesto extra, suma
					if($_SESSION["impuestos"][$keyImpuesto]["importe"] != 0)
					{
//						echo $_SESSION["impuestos"][$keyImpuesto]["importe"];
						if($impuesto["tipo"] == "impuesto")
						{
							$concepto["importe"] = $concepto["importe"] + $_SESSION["impuestos"][$keyImpuesto]["importe"];
						}
						elseif($impuesto["tipo"] == "retencion")
						{
							$concepto["importe"] = $concepto["importe"] - $_SESSION["impuestos"][$keyImpuesto]["importe"];
						}
						elseif($impuesto["tipo"] == "deduccion")
						{
							$concepto["importe"] = $concepto["importe"] - $_SESSION["impuestos"][$keyImpuesto]["importe"];
						}
						elseif($impuesto["tipo"] == "amortizacion")
						{
							$concepto["importe"] = $concepto["importe"] - $_SESSION["impuestos"][$keyImpuesto]["importe"];
						}
						continue;
					}

					if($impuesto["tipo"] == "impuesto")
					{
						$concepto["importe"] = $concepto["importe"] + ($importe * ($impuesto["tasa"] / 100));
						$_SESSION["impuestos"][$keyImpuesto]["importe"] = $importe * ($impuesto["tasa"] / 100);
					}
					elseif($impuesto["tipo"] == "retencion")
					{
						$concepto["importe"] = $concepto["importe"] - ($importe * ($impuesto["tasa"] / 100));
						$_SESSION["impuestos"][$keyImpuesto]["importe"] = $importe * ($impuesto["tasa"] / 100);
					}
					elseif($impuesto["tipo"] == "deduccion")
					{
						$concepto["importe"] = $concepto["importe"] - ($importe * ($impuesto["tasa"] / 100));
						$_SESSION["impuestos"][$keyImpuesto]["importe"] = $importe * ($impuesto["tasa"] / 100);
					}

				}//foreach
			}
			$data["subtotalOriginal"] = $this->Util()->RoundNumber($data["subtotalOriginal"] + $importe);
			$data["subtotal"] = $this->Util()->RoundNumber($data["subtotal"] + $concepto["importe"]);
			if($concepto["excentoIva"] == "si")
			{
				$_SESSION["conceptos"][$key]["tasaIva"] = 0;
			}
			else
			{
				$_SESSION["conceptos"][$key]["tasaIva"] = $data["tasaIva"];
			}
			//porcentaje de descuento
			if($data["porcentajeDescuento"])
			{
				$data["porcentajeDescuento"];
			}

			$data["descuentoThis"] = $this->Util()->RoundNumber($_SESSION["conceptos"][$key]["importe"] * ($data["porcentajeDescuento"] / 100));
			$data["descuento"] += $data["descuentoThis"];

			$afterDescuento = $_SESSION["conceptos"][$key]["importe"] - $data["descuentoThis"];
			if($concepto["excentoIva"] == "si")
			{
				$_SESSION["conceptos"][$key]["tasaIva"] = 0;
			}
			else
			{
				$_SESSION["conceptos"][$key]["tasaIva"] = $data["tasaIva"];
			}



			$data["ivaThis"] = $this->Util()->RoundNumber($afterDescuento * ($_SESSION["conceptos"][$key]["tasaIva"] / 100));
			$data["iva"] += $data["ivaThis"];

			$_SESSION["conceptos"][$key]["descuento"] = $data["descuentoThis"];
			$_SESSION["conceptos"][$key]["importeTotal"] = $concepto["importe"] - $data["descuentoThis"];
			$_SESSION["conceptos"][$key]["totalIva"] = $_SESSION["conceptos"][$key]["importeTotal"] * (round($_SESSION["conceptos"][$key]["tasaIva"] / 100, 6));
			//$_SESSION["conceptos"][$key]["totalIeps"] = $_SESSION["conceptos"][$key]["importeTotal"] * (round($_SESSION["conceptos"][$key]["porcentajeIeps"] / 100, 6));
			$_SESSION["conceptos"][$key]["totalRetencionIva"] = $_SESSION["conceptos"][$key]["importeTotal"] * (round($data["porcentajeRetIva"] / 100, 6));
			$_SESSION["conceptos"][$key]["totalRetencionIsr"] = $_SESSION["conceptos"][$key]["importeTotal"] * (round($data["porcentajeRetIsr"] / 100, 6));
		}
		$data["impuestos"] = $_SESSION["impuestos"];
		$afterDescuento = $data["subtotal"] - $data["descuento"];
		$data["afterDescuento"] = $afterDescuento;

//		echo $data["afterDescuento"];
		//aplicar otros impuestos y retenciones aplican despues del descuento
//		$afterDescuento = $data["afterDescuento"];

		$data["afterIva"] = $afterDescuento + $data["iva"];
		//ieps de descuento
		if(!$data["porcentajeIEPS"])
		{
			$data["porcentajeIEPS"] = 0;
		}
		$data["ieps"] = $this->Util()->RoundNumber($data["afterDescuento"] * ($data["porcentajeIEPS"] / 100));
		$afterImpuestos = $afterDescuento + $data["iva"] + $data["ieps"];
		$data["afterImpuestos"] = $afterImpuestos;

		$data["retIva"] = $this->Util()->RoundNumber($data["afterDescuento"] * ($data["porcentajeRetIva"] / 100));
		$data["retIsr"] = $this->Util()->RoundNumber($data["afterDescuento"] * ($data["porcentajeRetIsr"] / 100));
		$data["total"] = $this->Util()->RoundNumber($data["subtotal"] - $data["descuento"] + $data["iva"] + $data["ieps"] - $data["retIva"] - $data["retIsr"]);

echo "</pre>";

//		echo "<pre>";		print_r($data);		echo "</pre>";
		return $data;
	}

	function AddProducto(){

		if($this->Util()->PrintErrors()){
			return false;
		}

		$this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("
			INSERT INTO `producto` (
				`empresaId`,
				`noIdentificacion`,
				`unidad`, 
				`valorUnitario`, 
				`descripcion`, 
				`rfcId`		
				) 
			VALUES (
				'".$this->getEmpresaId()."',
				'".$this->getNoIdentificacion()."',				
				'".$this->getUnidad()."',
				'".$this->getValorUnitario()."',
				'".$this->getDescripcion()."',
				'".$this->getRfcId()."')"
			);

		$id_producto = $this->Util()->DBSelect($_SESSION['empresaId'])->InsertData();

		$this->Util()->setError(20014, 'complete');

		$this->Util()->PrintErrors();

		return true;

	}//AddProducto

	function EditProducto(){

		if($this->Util()->PrintErrors()){
			return false;
		}
		$this->Util()->DBSelect($_SESSION['empresaId'])->setQuery("
			UPDATE `producto` SET 
				`empresaId` = '".$this->getEmpresaId()."',
				`noIdentificacion` = '".$this->getNoIdentificacion()."', 
				`unidad` = '".$this->getUnidad()."', 
				`valorUnitario` = '".$this->getValorUnitario()."', 
				`descripcion` = '".$this->getDescripcion()."', 
				`rfcId` = '".$this->getRfcId()."'				
			WHERE productoId = '".$this->id_producto."'"
			);
		$this->Util()->DBSelect($_SESSION["empresaId"])->UpdateData();

		$this->Util()->setError(20016, "complete");
		$this->Util()->PrintErrors();

		return true;

	}//EditProducto

	public function setProductoDelete($value){
		$this->Util()->ValidateString($value, $max_chars=13, $minChars = 1, "ID Producto");
		$this->id_producto = $value;
	}

	function DeleteProducto(){
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("DELETE FROM producto WHERE productoId = '".$this->id_producto."'");
		$this->Util()->DBSelect($_SESSION["empresaId"])->DeleteData();
		$this->Util()->setError(20015, "complete");
		$this->Util()->PrintErrors();
		return true;
	}

	function getInfoProducto(){
		$this->Util()->DBSelect($_SESSION["empresaId"])->setQuery("SELECT * FROM producto WHERE productoId ='".$this->id_producto."'");
		$info = $this->Util()->DBSelect($_SESSION["empresaId"])->GetRow();

		return $info;
	}

}//Producto


?>
