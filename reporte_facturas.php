<?php

	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');

			$comprobantes = array();
			$values['status_activo'] = 1;
				
			$comprobantes = $comprobante->SearchComprobantesByRfc($values);

				$data .= "Serie,Folio,Concepto,Razon Social,Fecha,Monto sin IVA,Iva,Total\n";
				foreach($comprobantes["items"] as $comprobante)
				{
					foreach($comprobante as $key => $value)
					{
						$comprobante[$key] = str_replace(",", " ", $value);

						if($key == "rfc"
							|| $key == "porcentajeDescuento"
							|| $key == "descuento"
							|| $key == "total_formato"
							|| $key == "subtotal_formato"
							|| $key == "iva_formato"
							|| $key == "tipoDeMoneda"
							|| $key == "tipoDeCambio"
							|| $key == "porcentajeRetIva"
							|| $key == "porcentajeRetIsr"
							|| $key == "porcentajeIEPS"
							|| $key == "comprobanteId"
							|| $key == "status"
							|| $key == "tipoDeComprobante"
							|| $key == "instanciaServicioId"
							|| $key == "uuid"
						)
						{
							unset($comprobante[$key]);
						}
						
						if($key == "subTotal" || $key == "ivaTotal" || $key == 'total')
						{
							$comprobante[$key] = "$".number_format($value, 2, ".", "");
						}
						
					}

					//print_r($comprobante);
					$data .= implode(",", $comprobante);
					$data .= "\n";
				}
				
				$data = utf8_decode($data);
				$data = html_entity_decode($data);
		
		header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="Reporte_de_facturas.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
				echo $data;
				//$data = urldecode($data);
			/*	$myFile = DOC_ROOT."/reporte_comprobantes.csv";
				$fh = fopen($myFile, 'w') or die("can't open file");
				fwrite($fh, $data);
				fclose($fh);*/
				
?>				