<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT . '/libraries33.php');

$smarty->assign("permisos", $_SESSION['permisos2']);
$smarty->assign("nuevosPermisos", $_SESSION['nuevosPermisos2']);

switch ($_POST["type"]) {
    case "agregarImpuesto":
        $values = explode("&", $_POST["form"]);
        foreach ($values as $key => $val) {
            $values[$key] = explode("=", $values[$key]);
        }
        $producto->setTasa($values[0][1]);
        $producto->setImpuesto($values[1][1]);
        $producto->setTipo($values[4][1]);

        $producto->setTasaIva($values[2][1]);
        $producto->setImporteImpuesto($values[3][1]);

        if (!$producto->AgregarImpuesto()) {
            echo "fail|";
            $smarty->display(DOC_ROOT . '/templates/boxes/status.tpl');
        } else {
            echo "ok|ok";
        }
        echo "|";
        $smarty->assign("impuestos", $_SESSION["impuestos"]);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/lists/impuestos.tpl');
        break;

    case "sendEmail":
        $util->ValidateString($_POST['nombre'], 10000, 1, 'nombre');
        $util->ValidateString($_POST['correo'], 10000, 1, 'Correo');
        $util->ValidateString($_POST['telefono'], 10000, 1, 'telefono');
        $util->ValidateString($_POST['mensaje'], 10000, 1, 'telefono');
        if ($util->EnvEmail($_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_POST['mensaje']))
            echo "ok";
        else
            echo "fail";
        break;

    case "agregarConcepto":
        $producto->setCantidad($_POST["cantidad"]);
        $producto->setNoIdentificacion($_POST["noIdentificacion"]);
        $producto->setUnidad($_POST["unidad"]);
        $producto->setDescripcion($_POST["descripcion"]);
        $producto->setValorUnitario($_POST["valorUnitario"]);
        $producto->setExcentoIva($_POST["excentoIva"]);
        $producto->setExcentoIsh($_POST["excentoIsh"]);
        $producto->setIepsTasaOCuota($_POST["iepsTasaOCouta"]);
        $producto->setPorcentajeIeps($_POST["iepsConcepto"]);
        $producto->setPorcentajeIsh($_POST["ishConcepto"]);
        $producto->setCuentaPredial($_POST["cuentaPredial"]);

        $producto->setClaveProdServ($_POST["c_ClaveProdServ"]);
        $producto->setClaveUnidad($_POST["c_ClaveUnidad"]);
        switch ($_POST["fromAddenda"]) {
            case "Pepsico":
                $producto->setIdRecepcion($_POST["idRecepcion"]);
                break;
            case "Continental":
                $producto->setIdRecepcion($_POST["idRecepcion"]);
                $producto->setItem($_POST["item"]);
                break;

            case "Zepto":
                $producto->setOrdenCompra($_POST["idRecepcion"]);
                $producto->setItem($_POST["item"]);
                break;
        }

        if ($_POST["fromAgrario"] == "Si") {
            $producto->setTipoGanado($_POST["tipoGanado"]);
            $producto->setPeso($_POST["peso"]);
        }
        $producto->setCategoriaConcepto($_POST["categoriaConcepto"]);
        $producto->setImporte();
        if (!$producto->AgregarConcepto()) {
            echo "fail|";
            $smarty->display(DOC_ROOT . '/templates/boxes/status.tpl');
        } else {
            echo "ok|ok";
        }
        echo "|";
        //print_r($_SESSION["conceptos"]);
        $smarty->assign("conceptos", $_SESSION["conceptos"]);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/lists/conceptos.tpl');

        break;

    case "borrarConcepto":
        $producto->BorrarConcepto($_POST["id"]);
        $smarty->assign("conceptos", $_SESSION["conceptos"]);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/lists/conceptos.tpl');

        break;

    case "borrarImpuesto":
        $producto->BorrarImpuesto($_POST["id"]);
        $smarty->assign("impuestos", $_SESSION["impuestos"]);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/lists/impuestos.tpl');

        break;

    case "updateTotalesDesglosados":
        $totalDesglosado = $producto->GetTotalDesglosado();
        $smarty->assign("impuestos", $totalDesglosado["impuestos"]);
        unset($totalDesglosado["impuestos"]);
        if ($totalDesglosado) {
            foreach ($totalDesglosado as $key => $total) {
                $totalDesglosado[$key] = number_format($totalDesglosado[$key], 2);
            }
        }
        $smarty->assign("totalDesglosado", $totalDesglosado);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/boxes/total-desglosado.tpl');

        break;

    case "generarComprobante":
        $data["datosFacturacion"] = $_POST["nuevaFactura"];
        $data["observaciones"] = $_POST["observaciones"];

        $data["reviso"] = $_POST["reviso"];
        $data["autorizo"] = $_POST["autorizo"];
        $data["recibio"] = $_POST["recibio"];
        $data["vobo"] = $_POST["vobo"];
        $data["pago"] = $_POST["pago"];
        $data["tiempoLimite"] = $_POST["tiempoLimite"];
        $data["cuentaPorPagar"] = $_POST["cuentaPorPagar"];
        $data["formatoNormal"] = $_POST["formatoNormal"];

        $data["spf"] = $_POST["spf"];
        $data["isn"] = $_POST["isn"];

        $data["banco"] = $_POST["banco"];
        $data["fechaDeposito"] = $_POST["fechaDeposito"];
        $data["referencia"] = $_POST["referencia"];

        $data["format"] = $_POST["format"];

        if ($_POST["fechaSobreDia"] && $_POST["fechaSobreMes"] && $_POST["fechaSobreAnio"]) {
            $data["fechaSobre"] = $_POST["fechaSobreDia"] . "-" . $_POST["fechaSobreMes"] . "-" . $_POST["fechaSobreAnio"];
        } else {
            $data["fechaSobre"] = "";
        }

        $values = explode("&", $data["datosFacturacion"]);
        foreach ($values as $key => $val) {
            $array = explode("=", $values[$key]);
            $data[$array[0]] = $array[1];
        }

        $data["folioSobre"] = $_POST["folioSobre"];

        switch ($data["fromAddenda"]) {
            case "Continental":
            case "Pepsico":
                $data["idPedido"] = $_POST["idPedido"];
                $data["idSolicitudDePago"] = $_POST["idSolicitudDePago"];
                $data["referencia"] = $_POST["referencia"];
                $data["idProveedor"] = $_POST["idProveedor"];
                $data["idRecepcion"] = $_POST["idRecepcion"];
                break;
            case "Zepto":
                $data["referencia"] = $_POST["referencia"];
                break;
        }

        if ($data["fromAgrario"] == "Si") {
            $data["noRegistro"] = $_POST["idPedido"];
            $data["noGuiaTraslado"] = $_POST["idSolicitudDePago"];
            $data["uppOrigen"] = $_POST["referencia"];
            $data["patenteVendedor"] = $_POST["idProveedor"];
            $data["finalidad"] = $_POST["finalidad"];
            $data["uppDestino"] = $_POST["uppDestino"];
            $data["firmaVendedor"] = $_POST["firmaVendedor"];
        }

        if (!$response = $cfdi->Generar($data)) {
            echo "fail|";
            $smarty->display(DOC_ROOT . '/templates/boxes/status.tpl');
        } else {
            if ($data['format'] == 'vistaPrevia') {
                echo "ok|";
                $smarty->assign("response", $response);
                $smarty->display(DOC_ROOT . '/templates/boxes/cfdi33-vista-previa.tpl');
                echo "|isVistaPrevia";
                exit;
            }

            echo "ok|";
            $info = $user->Info();
            $smarty->assign("info", $info);
            $comprobante = $comprobante->GetLastComprobante();
            $smarty->assign("comprobante", $comprobante);
            $smarty->display(DOC_ROOT . '/templates/boxes/export-factura.tpl');
            echo "|real";
        }
        break;

    case "cambiarRfcActivo":
        $rfc->setRfcId($_POST["rfcId"]);
        $rfc->SetAsActive();
        break;

    case 'loadConceptoFromService':
        $id = $_POST['value'];
        $contract->setContractId($id);
        $contrato = $contract->Info();
        $invoice = new InvoiceService();
        $invoice->setCurrentContract($contrato);
        $invoice->GetFilterServicesByContract(false);
        $_SESSION['conceptos'] = $invoice->GenerateConceptos(true);
        echo "ok|ok|";
        $smarty->assign("conceptos", $_SESSION["conceptos"]);
        $smarty->assign("DOC_ROOT", DOC_ROOT);
        $smarty->display(DOC_ROOT . '/templates/lists/conceptos.tpl');
    break;
    case 'loadConceptoAndDataCompany':
            $invoice = new InvoiceService();
            $data = $invoice->getFullDataInvoiceByFolio($_POST['serie'], $_POST['folio']);
            if(!$data) {
                echo "fail[#]";
                $smarty->display(DOC_ROOT."/templates/boxes/status_on_popup.tpl");
            } else {
                $_SESSION['conceptos'] = $data['conceptos'];
                echo "ok[#]";
                $smarty->assign("conceptos", $_SESSION["conceptos"]);
                $smarty->assign("DOC_ROOT", DOC_ROOT);
                $smarty->display(DOC_ROOT . '/templates/lists/conceptos.tpl');
                echo "[#]".$data['comprobanteId']."[#]".$data['razonSocial']."[#]".$data['rfc'];
                echo "[#]".$data['userId']."[#]".$data['calle']."[#]".$data['pais'];
                echo "[#]".$data['setTipoComprobante'];
            }
    break;
}

?>
