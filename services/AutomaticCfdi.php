<?php

class AutomaticCfdi extends Comprobante
{
    function CreateServiceInvoices()
    {
        global $months;
        $month = date("m");
        $year = date("Y");
        echo "<pre>";
        $init = microtime();

        $this->Util()->DB()->setQuery("SELECT * FROM rfc
			WHERE empresaId = '21' ORDER BY rfcId ASC LIMIT 1");
        $emisorHuerin = $this->Util()->DB()->GetRow();

        $this->Util()->DB()->setQuery("SELECT * FROM rfc
			WHERE empresaId = '20' ORDER BY rfcId ASC LIMIT 1");
        $emisorBraun = $this->Util()->DB()->GetRow();

        $this->Util()->DB()->setQuery("SELECT * FROM customer WHERE customerId = 1320");
        $clientes = $this->Util()->DB()->GetResult();
        $data = array();
        foreach($clientes as $key => $cliente)
        {
            if($cliente["active"] == "1")
            {
                $data["clientes"]["activo"][] = $cliente;
            }
            else
            {
                $data["clientes"]["inactivo"][] = $cliente;
            }
        }
        ?>
        <table border="1" width="600">
            <tr>
                <td>Concepto</td>
                <td>Braun Huerin SC</td>
                <td>Jacobo Braun</td>
                <td>Efectivo</td>
                <td>Total</td>
            </tr>
            <tr>
                <td>Clientes Totales</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
                <td><?php echo count($clientes) ?></td>
            </tr>

            <tr>
                <td>Clientes Activos</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
                <td><b><?php echo count($data["clientes"]["activo"]) ?></b></td>
            </tr>

            <tr>
                <td>Clientes Inactivos</td>
                <td>N/A</td>
                <td>N/A</td>
                <td>N/A</td>
                <td><?php echo count($data["clientes"]["inactivo"]) ?></td>
            </tr>
            <?php
            //ya no necesitamos los clientes inactivos
            unset($data["clientes"]["inactivo"]);

            $data["clientesSinContrato"] = 0;
            $data["contratosInactivos"] = 0;

            foreach($data["clientes"]["activo"] as $key => $cliente)
            {
                $this->Util()->DB()->setQuery("SELECT * FROM contract WHERE
			customerId = '".$cliente["customerId"]."'
			AND ( contractId != 18
				AND contractId != 24
				AND contractId != 677
				AND contractId != 651
				AND contractId != 622
				AND contractId != 581
				AND contractId != 872
				AND contractId != 875
				AND contractId != 881
				AND contractId != 936
				AND contractId != 1236
				AND contractId != 1315
				AND contractId != 1407
				AND contractId != 1440
				AND contractId != 1441
				AND contractId != 1562
				AND contractId != 1702
				AND contractId != 1731
				AND contractId != 1858
				AND contractId != 1941
				AND contractId != 2058)"
                );
                $contratos = $this->Util()->DB()->GetResult();

                if(count($contratos) == 0)
                {
                    $data["clientesSinContrato"]++;
                    unset($data["clientes"]["activo"][$key]);
                    continue;
                }

                //contratos inactivos


                $data["totalContratos"] += count($contratos);

                foreach($contratos as $keyContrato => $contrato)
                {

                    /*if($contrato["activo"] == "No")
                    {
                        $data["contratosInactivos"]++;
                        unset($contratos[$keyContrato]);
                        continue;
                    }*/
                    if($contrato["activo"] == "Si")
                    {
                        $data["totalContratosActivos"]++;

                        if($contrato["facturador"] == "Braun")
                        {
                            $data["contratosBraun"][] = $contrato;
                        }
                        elseif($contrato["facturador"] == "BHSC")
                        {
                            $data["contratosHuerin"][] = $contrato;
                        }
                        else
                        {
                            $data["contratosEfectivo"][] = $contrato;
                        }
                    }
                    else
                    {
                        $data["totalContratosInactivos"]++;
                    }
                }
            }

            ?>
            <tr>
                <td>Clientes sin Razones Sociales</td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $data["clientesSinContrato"]?></td>
            </tr>

            <tr>
                <td>Contratos Inactivos</td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $data["contratosInactivos"]?></td>
            </tr>

            <?php $clientesActivosMenosSinContrato = count($data["clientes"]["activo"]) - $data["clientesSinContrato"];?>
            <tr>
                <td>Clientes Activos MENOS Clientes ACTIVOS sin Razones Sociales</td>
                <td></td>
                <td></td>
                <td></td>
                <td><b><?php echo count($data["clientes"]["activo"]); ?></b></td>
            </tr>

            <tr>
                <td>Razones Sociales Totales</td>
                <td></td>
                <td></td>
                <td></td>
                <td><b><?php echo $data["totalContratos"]; ?></b></td>
            </tr>

            <tr>
                <td>Razones Sociales Activas</td>
                <td><?php echo count($data["contratosHuerin"])?></td>
                <td><?php echo count($data["contratosBraun"])?></td>
                <td><?php echo count($data["contratosEfectivo"])?></td>
                <td><b><?php echo $data["totalContratosActivos"]; ?></b></td>
            </tr>

            <tr>
                <td>Razones Sociales Inactivas</td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $data["totalContratosInactivos"]; ?></td>
            </tr>

            <?php
            //ya no necesitamos efectivo
            unset($data["contratosEfectivo"]);
            //obtener servicios
            foreach($data["contratosHuerin"] as $key => $contratoHuerin)
            {
                //unset($data["contratosHuerin"][$key]);
                //continue;
                $this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId = '".$contratoHuerin["contractId"]."'");
                $servicios = $this->Util()->DB()->GetResult();

                if(count($servicios) == 0)
                {
                    $data["clientesSinServicios"]++;
                    unset($data["contratosHuerin"][$key]);
                    continue;
                }

                $data["totalServicios"] += count($servicios);
                $data["totalServiciosHuerin"] += count($servicios);

                foreach($servicios as $keyServicio => $servicio)
                {
                    if($servicio["status"] == "activo")
                    {
                        $data["totalServiciosActivos"]++;

                        $data["servicios"][] = $servicio;
                        $data["serviciosHuerin"][] = $servicio;
                    }
                    else
                    {
                        $data["totalServiciosInactivos"]++;
                    }
                }
            }


            foreach($data["contratosBraun"] as $key => $contratoHuerin)
            {
                $this->Util()->DB()->setQuery("SELECT * FROM servicio
		WHERE contractId = '".$contratoHuerin["contractId"]."'");
                $servicios = $this->Util()->DB()->GetResult();

                if(count($servicios) == 0)
                {
                    $data["clientesSinServicios"]++;
                    unset($data["contratosBraun"][$key]);
                    continue;
                }

                $data["totalServicios"] += count($servicios);
                $data["totalServiciosBraun"] += count($servicios);

                foreach($servicios as $keyServicio => $servicio)
                {
                    if($servicio["status"] == "activo")
                    {
                        $data["totalServiciosActivos"]++;

                        $data["servicios"][] = $servicio;
                        $data["serviciosBraun"][] = $servicio;
                    }
                    else
                    {
                        $data["totalServiciosInactivos"]++;
                    }
                }
            }

            ?>
            <tr>
                <td>Servicios Totales de Razones Sociales Activos</td>
                <td><b><?php echo $data["totalServiciosHuerin"]; ?></b></td>
                <td><b><?php echo $data["totalServiciosBraun"]; ?></b></td>
                <td></td>
                <td><b><?php echo $data["totalServicios"]; ?></b></td>
            </tr>

            <tr>
                <td>Servicios Activos</td>
                <td><?php echo count($data["serviciosHuerin"])?></td>
                <td><?php echo count($data["serviciosBraun"])?></td>
                <td></td>
                <td><b><?php echo $data["totalServiciosActivos"]; ?></b></td>
            </tr>

            <tr>
                <td>Servicios Inactivos</td>
                <td></td>
                <td></td>
                <td></td>
                <td><?php echo $data["totalServiciosInactivos"]; ?></td>
            </tr>

            <?php
            //remover costo 0
            foreach($data["serviciosHuerin"] as $key => $servicio)
            {
                if($servicio["costo"] <= 0)
                {
                    $data["costo0Huerin"]++;
                    $data["totalCosto0"]++;
                    unset($data["serviciosHuerin"][$key]);
                    continue;
                }
            }

            foreach($data["serviciosBraun"] as $key => $servicio)
            {
                if($servicio["costo"] <= 0)
                {
                    $data["costo0Braun"]++;
                    $data["totalCosto0"]++;
                    unset($data["serviciosBraun"][$key]);
                    continue;
                }
            }
            $data["totalCostoMayor0"] = count($data["serviciosHuerin"]) + count($data["serviciosBraun"]);

            ?>
            <tr>
                <td>Servicios con Costo == 0</td>
                <td><?php //echo $data["costo0Huerin"]?></td>
                <td><?php //echo $data["costo0Braun"]?></td>
                <td></td>
                <td><?php echo $data["totalCosto0"]; ?></td>
            </tr>
            <tr>
                <td>Servicios con Costo > 0</td>
                <td><?php echo count($data["serviciosHuerin"])?></td>
                <td><?php echo count($data["serviciosBraun"])?></td>
                <td></td>
                <td><b><?php echo $data["totalCostoMayor0"]; ?></b></td>
            </tr>

            <?php

            ///quitar los que la fecha de facturacion no es ha iniciado
            foreach($data["serviciosHuerin"] as $key => $servicio)
            {
                if($servicio["inicioFactura"] == "0000-00-00")
                {
                    unset($data["serviciosHuerin"][$key]);
                    continue;
                }

                $fecha = explode("-", $servicio["inicioFactura"]);
                if($fecha[0] > $year)
                {
                    $data["fechaPosteriorHuerin"]++;
                    $data["fechaPosterior"]++;
                    unset($data["serviciosHuerin"][$key]);
                    continue;
                }

                if($fecha[1] > $month && $fecha[0] == $year)
                {
                    $data["fechaPosteriorHuerin"]++;
                    $data["fechaPosterior"]++;
                    unset($data["serviciosHuerin"][$key]);
                    continue;
                }
            }

            foreach($data["serviciosBraun"] as $key => $servicio)
            {

                if($servicio["inicioFactura"] == "0000-00-00")
                {
                    unset($data["serviciosBraun"][$key]);
                    continue;
                }

                $fecha = explode("-", $servicio["inicioFactura"]);
                if($fecha[0] > $year)
                {
                    $data["fechaPosteriorBraun"]++;
                    $data["fechaPosterior"]++;
                    unset($data["serviciosBraun"][$key]);
                    continue;
                }

                if($fecha[1] > $month && $fecha[0] == $year)
                {
                    $data["fechaPosteriorBraun"]++;
                    $data["fechaPosterior"]++;
                    unset($data["serviciosBraun"][$key]);
                    continue;
                }
            }

            $data["totalFechaPosterior"] = count($data["serviciosHuerin"]) + count($data["serviciosBraun"]);


            ?>

            <tr>
                <td>Inicio Factura Posterior a Fecha</td>
                <td><?php //echo $data["costo0Huerin"]?></td>
                <td><?php //echo $data["costo0Braun"]?></td>
                <td></td>
                <td><?php echo $data["fechaPosterior"]; ?></td>
            </tr>
            <tr>
                <td>Inicio de Factura Correcto</td>
                <td><?php echo count($data["serviciosHuerin"])?></td>
                <td><?php echo count($data["serviciosBraun"])?></td>
                <td></td>
                <td><b><?php echo $data["totalFechaPosterior"]; ?></b></td>
            </tr>
            <?php

            //quitar instancia de servicio
            ///quitar los que la fecha de facturacion no es ha iniciado

            foreach($data["serviciosHuerin"] as $key => $servicio)
            {
                $this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM instanciaServicio
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		LEFT JOIN contract ON contract.contractId = servicio.contractId
		LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
		LEFT JOIN customer ON customer.customerId = contract.customerId
		WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'");
                $row = $this->Util()->DB()->GetRow();

                if(!$row)
                {
                    $data["noInstanciaHuerin"]++;
                    $data["noInstancia"]++;
                    unset($data["serviciosHuerin"][$key]);
                    continue;
                }

                $data["serviciosHuerin"][$key] = $row;
            }

            foreach($data["serviciosBraun"] as $key => $servicio)
            {
                $this->Util()->DB()->setQuery("SELECT *, servicio.costo AS costoServicio, contract.name AS name FROM instanciaServicio
		LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
		LEFT JOIN contract ON contract.contractId = servicio.contractId
		LEFT JOIN tipoServicio ON servicio.tipoServicioId = tipoServicio.tipoServicioId
		LEFT JOIN customer ON customer.customerId = contract.customerId
		WHERE servicio.servicioId = '".$servicio["servicioId"]."' AND MONTH(date) = '".$month."' AND YEAR(date) = '".$year."'");
                $row = $this->Util()->DB()->GetRow();

                if(!$row)
                {
                    $data["noInstanciaBraun"]++;
                    $data["noInstancia"]++;
                    unset($data["serviciosBraun"][$key]);
                    continue;
                }

                $data["serviciosBraun"][$key] = $row;
            }
            $data["totalInstancias"] = count($data["serviciosHuerin"]) + count($data["serviciosBraun"]);

            ?>
            <tr>
                <td>Sin Instancia de Servicio</td>
                <td><?php //echo $data["costo0Huerin"]?></td>
                <td><?php //echo $data["costo0Braun"]?></td>
                <td></td>
                <td><?php echo $data["noInstancia"]; ?></td>
            </tr>
            <tr>
                <td>Con Instancia de Servicio (Facturas teoricas)</td>
                <td><b><?php echo count($data["serviciosHuerin"])?></b></td>
                <td><b><?php echo count($data["serviciosBraun"])?></b></td>
                <td></td>
                <td><b><?php echo $data["totalInstancias"]; ?></b></td>
            </tr>
            <?php
            //facturadas
            foreach($data["serviciosBraun"] as $key => $servicio)
            {
                if($servicio["comprobanteId"] != 0)
                {
                    $data["facturadaBraun"]++;
                    $data["facturada"]++;
                    unset($data["serviciosBraun"][$key]);
                    continue;
                }

                if(!$data["facturadaBraun"])
                {
                    $data["facturadaBraun"] = 0;
                }
            }

            foreach($data["serviciosHuerin"] as $key => $servicio)
            {
                if($servicio["comprobanteId"] != 0)
                {
                    $data["facturadaHuerin"]++;
                    $data["facturada"]++;
                    unset($data["serviciosHuerin"][$key]);
                    continue;
                }

                if(!$data["facturadaHuerin"])
                {
                    $data["facturadaHuerin"] = 0;
                }

            }
            //	exit();

            if(!$data["facturada"])
            {
                $data["facturada"] = 0;
            }

            $data["totalFacturadas"] = count($data["serviciosHuerin"]) + count($data["serviciosBraun"]);

            ?>
            <tr>
                <td>Facturadas</td>
                <td><?php echo $data["facturadaHuerin"]?></td>
                <td><?php echo $data["facturadaBraun"]?></td>
                <td></td>
                <td><?php echo $data["facturada"]; ?></td>
            </tr>
            <tr>
                <td>Por Facturar</td>
                <td><b><?php echo count($data["serviciosHuerin"])?></b></td>
                <td><b><?php echo count($data["serviciosBraun"])?></b></td>
                <td></td>
                <td><b><?php echo $data["totalFacturadas"]; ?></b></td>
            </tr>
        </table>
        <?php

        if(!is_array($data["serviciosHuerin"]))
        {
            $data["serviciosHuerin"] = array();
        }

        if(!is_array($data["serviciosBraun"]))
        {
            $data["serviciosBraun"] = array();
        }

        $servicio = array_merge($data["serviciosHuerin"], $data["serviciosBraun"]);
        //$servicio = $data["serviciosBraun"];
        //Agrupamos por Contratos (Razones Sociales)
        //print_r($data["serviciosBraun"]);

        $idContracts = array();
        $contratos = array();
        foreach($servicio as $res){

            $contractId = $res['contractId'];
            $contratos[$contractId][] = $res;

        }//foreach
        //exit;
        //Contratos
        unset($data);
        foreach($contratos as $contractId => $servicios){
            //	echo "jere";
            $this->Util()->DB()->setQuery("SELECT facturador FROM contract WHERE contractId = '".$contractId."'");
            $value['facturador'] = $this->Util()->DB()->GetSingle();

            if($value["facturador"] == "BHSC")
            {
                $empresaIdFacturador = 21;
                $_SESSION['empresaId'] = 21;
                $emisor = $emisorHuerin;
                $nombreFactura = "Factura";
            }
            if($value["facturador"] == "Huerin")
            {
                $empresaIdFacturador = 15;
                $_SESSION['empresaId'] = 15;
                $emisor = $emisorHuerin;
                $nombreFactura = "Factura";
            }
            elseif($value["facturador"] == "Braun")
            {
                $empresaIdFacturador = 20;
                $_SESSION['empresaId'] = 20;
                $emisor = $emisorBraun;
                $nombreFactura = "Recibo Honorarios";
            }

            echo '*****************';
            echo '<br>';
            echo $contractId.' :: '.$value["facturador"];
            echo "<br>";

            $subtotal = $res["costoServicio"];
            $idInstServ = array();
            $_SESSION["conceptos"] = array();
            $tasaIva = $emisor["iva"];

            $iva = $subtotal * ($emisor["iva"] / 100);
            $total = $subtotal + $iva;
            foreach($servicios as $res){

                $subtotal += $res["costoServicio"];

                $fecha = explode("-", $res["date"]);
                $fechaText = $months[$fecha[1]]." del ".$fecha["0"];
                $concepto = $res["nombreServicio"]." CORRESPONDIENTE AL MES DE ".$fechaText;

                $_SESSION["conceptos"][] = array(
                    "noIdentificacion" => "",
                    "cantidad" => 1,
                    "unidad" => "No Aplica",
                    "valorUnitario" => $res["costoServicio"],
                    "importe" => $res["costoServicio"],
                    "excentoIva" => "no",
                    "descripcion" => $concepto,
                    "tasaIva" => $tasaIva,
                    "claveProdServ" => '01010101',
                    "claveUnidad" => 'EA',
                    'importeTotal' => $res["costoServicio"],
                    'totalIva' => $iva,
                );

                echo  $res["nombreServicio"]." ".$res["instanciaServicioId"]." ".$res["name"]." ".$res["rfc"]." ".$res["costoServicio"];
                echo "<br>";

                $idInstServ[] = $res['instanciaServicioId'];

            }//foreach

            $data["idFactura"] = $res["instanciaServicioId"]; //Duda


            $data["formaDePago"] = "99";
            $data["condicionesDePago"] = "";
            $data["tasaIva"] = $tasaIva;
            $data["tiposDeMoneda"] = "MXN";
            $data["porcentajeRetIva"] = 0;
            $data["porcentajeDescuento"] = 0;
            $data["tipoDeCambio"] = 1.00;
            $data["porcentajeRetIsr"] = 0;
            $data["porcentajeIEPS"] = 0 ;

            //get serie
            $this->Util()->DB()->setQuery("SELECT * FROM serie WHERE empresaId = '".$empresaIdFacturador."'
				ORDER BY serieId ASC LIMIT 1");
            $serie = $this->Util()->DB()->GetRow();
            //agregar serie
            $data["serie"] = array
            (
                "serieId" => $serie["serieId"],
                "serie" => $serie["serie"],
                "empresaId" => $serie["empresaId"],
                "tiposComprobanteId" => $serie["tiposComprobanteId"],
                "lugarDeExpedicion" => $serie["lugarDeExpedicion"],
                "noCertificado" => $serie["noCertificado"],
                "email" => $serie["email"],
                "consecutivo" => $serie["consecutivo"],
                "rfcId" => $serie["rfcId"]
            );

            $data["comprobante"] = array
            (
                "tiposComprobanteId" => 1,
                "nombre" => $nombreFactura,
                "tipoDeComprobante" => "ingreso"
            );

            //nodo emisor
            $emisor["rfc"] = trim(str_replace("-", "", $emisor["rfc"]));
            $emisor["rfc"] = str_replace(" ", "", $emisor["rfc"]);

            $data["nodoEmisor"]["rfc"] = array
            (

                "rfcId" => $emisor["rfcId"],
                "empresaId" => $empresaIdFacturador,
                "regimenFiscal" => $emisor["regimenFiscal"],
                "rfc" => $emisor["rfc"],
                "razonSocial" => $emisor["razonSocial"],
                "pais" => $emisor["pais"],
                "calle" => $emisor["calle"],
                "noExt" => $emisor["noExt"],
                "noInt" => $emisor["noInt"],
                "colonia" => $emisor["colonia"],
                "localidad" => $emisor["localidad"],
                "municipio" => $emisor["municipio"],
                "ciudad" => $emisor["ciudad"],
                "referencia" => $emisor["referencia"],
                "estado" => $emisor["estado"],
                "cp" => $emisor["cp"],
                "activo" => $emisor["activo"],
                "main" => $emisor["main"]
            );

            if($value["facturador"] == "BHSC")
            {
                $data["nodoEmisor"]["sucursal"] = array
                (
                    "identificador" => "Matriz",
                    "rfcId" => $emisor["rfcId"],
                    "empresaId" => $empresaIdFacturador,
                    "regimenFiscal" => $emisor["regimenFiscal"],
                    "rfc" => $emisor["rfc"],
                    "razonSocial" => $emisor["razonSocial"],
                    "pais" => $emisor["pais"],
                    "calle" => "NAVARRA",
                    "noExt" => "210",
                    "noInt" => "PB",
                    "colonia" => "Alamos",
                    "localidad" => "BENITO JUAREZ",
                    "municipio" => "BENITO JUAREZ",
                    "ciudad" => "BENITO JUAREZ",
                    "referencia" => "",
                    "estado" => "DF",
                    "cp" => "03400",
                    "activo" => $emisor["activo"],
                    "main" => $emisor["main"]
                );
            }
            else
            {
                $data["nodoEmisor"]["sucursal"] = array(
                    "identificador" => "Matriz",
                    "rfcId" => $emisor["rfcId"],
                    "empresaId" => $empresaIdFacturador,
                    "regimenFiscal" => $emisor["regimenFiscal"],
                    "rfc" => $emisor["rfc"],
                    "razonSocial" => $emisor["razonSocial"],
                    "pais" => $emisor["pais"],
                    "calle" => $emisor["calle"],
                    "noExt" => $emisor["noExt"],
                    "noInt" => $emisor["noInt"],
                    "colonia" => $emisor["colonia"],
                    "localidad" => $emisor["localidad"],
                    "municipio" => $emisor["municipio"],
                    "ciudad" => $emisor["ciudad"],
                    "referencia" => $emisor["referencia"],
                    "estado" => $emisor["estado"],
                    "cp" => $emisor["cp"],
                    "activo" => $emisor["activo"],
                    "main" => $emisor["main"]
                );
            }

            //$data["nodoEmisor"]["sucursal"]["identificador"] = "Matriz";
            $res["rfc"] = trim(str_replace("-", "", $res["rfc"]));
            $res["rfc"] = str_replace(" ", "", $res["rfc"]);

            if($res["rfc"] == "123123123123")
            {
                continue;
            }
            if(!$res["rfc"])
            {
                continue;
                $res["rfc"] = "XAXX010101000";
            }

            if(strlen($res["rfc"]) < 12)
            {
                continue;
                $res["rfc"] = "XAXX010101000";
            }

            $data["nodoReceptor"] = array
            (
                "userId" => $res["contractId"],
                "empresaId" => $empresaIdFacturador,
                "rfcId" => $emisor["rfcId"],
                "rfc" => $res["rfc"],
                "nombre" => $res["name"],
                "calle" => $res["address"],
                "noExt" => $res["noExtAddress"],
                "noInt" => $res["noIntAddress"],
                "colonia" => $res["coloniaAddress"],
                "municipio" => $res["municipioAddress"],
                "cp" => $res["cpAddress"],
                "estado" => $res["estadoAddress"],
                "localidad" => $res["municipioAddress"],
                "referencia" => "",
                "pais" => $res["paisAddress"],
                "email" => $res["emailContactoAdministrativo"],
                "telefono" => $res["telefonoContactoAdministrativo"],
                "password" => ""
            );

            $metodoDePago = $res["metodoDePago"];
            $data["metodoDePago"] = $metodoDePago;
            $data["NumCtaPago"] = $res["noCuenta"];

            if(strlen($data["NumCtaPago"]) != 4){
                $data["NumCtaPago"] = '';
            }

            //print_r($_SESSION["conceptos"]);

            $data['userId'] = $res["contractId"];
            $data['format'] = 'generar';
            $data['metodoDePago'] = 'PUE';
            $data['cfdiRelacionadoSerie'] = null;
            $data['cfdiRelacionadoFolio'] = null;
            $data['tipoRelacion'] = '04';
            $data['usoCfdi'] = 'G03';
            $data["tiposComprobanteId"] = $serie["tiposComprobanteId"]."-".$serie['serieId'];

            $cfdi = new Cfdi();

            $result = $cfdi->Generar($data);


            if(!$result){
                echo "\nError al generar la factura para ".$res["rfc"]."\n\n";
                print_r($_SESSION['errorPac']);
            } else {
                $last = $this->GetLastComprobante();

                $sql = "UPDATE instanciaServicio SET comprobanteId = '".$last["comprobanteId"]."'
						WHERE instanciaServicioId IN (0, ".implode(',',$idInstServ).")";
                $this->Util()->DB()->setQuery($sql);
                $this->Util()->DB()->UpdateData();
                echo "\n\nFactura para ".$res["rfc"]." Lista\n";

            }
            break;

        }//foreach

        //FIN AGRUPADO POR CONTRATOS

        $end = microtime();
        $tiempo = $end-$init;
        echo "<br>Script ejecutado en ".$tiempo." Milisegundos";

    }
}


?>