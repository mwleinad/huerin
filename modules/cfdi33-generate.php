<?php
$user->allowAccess(5);//level1
$user->allowAccess(131);//level2

$info = $empresa->Info();
$smarty->assign("info", $info);

/*$empresa->Util()->DB()->setQuery("SELECT * FROM usuario");
$result = $empresa->Util()->DB->GetResult();*/
echo $_SESSION['empresaId'];
$rfc->setEmpresaId($_SESSION["empresaId"], 1);
$smarty->assign("empresaRfcs", $rfc->GetRfcsByEmpresa());
//$empresa->hasPermission($_GET['section']);
$producto->CleanConceptos();
$producto->CleanImpuestos();


if(isset($_GET['id']))
{
    $ticketChain = $_GET['id'];
    $smarty->assign("ticketChain", $ticketChain);
    $ticketId = $_GET["id"];
    $smarty->assign("ticketId", $ticketId);
    unset($_SESSION['ticketsId']);
    $venta->setVentaId($_GET['id']);
    $productos = $venta->GetProductos();
    /*				echo "metodo producto<pre><h1>";
                        print_r($productos);
                    echo "</h1></pre>";
    Andres*/

    $prodIds = array();
    foreach($productos as $res){
//					$productoId = $res['productoId'];
        $productoId = $res['noIdentificacion'];
//Andres	Ultimoif(!in_array($productoId, $prodIds))
        if(!in_array($noIdentificacion, $prodIds))
            $prodIds[] = $productoId;
        /*						echo "<h1>ProIDs";
                                print_r($prodIds);
                                echo "</h1>";		Llenado del arrego ProIDs	Andres*/
    }//foreach

    $products = array();
    foreach($prodIds as $productoId){
        $promocionId = 0;
        $total2 = 0;
        $precio = 0;
        $cantidad = 0;
        $totalDesc = 0;
        $card = array();
        foreach($productos as $res){
//Andres 			if($res['productoId'] == $productoId){
            if($res['noIdentificacion'] == $productoId){
                $card = $res;

                if($res['promocionId'])
                    $promocionId = $res['promocionId'];

                if($res['valorUnitario'] > $precio)
                    $precio = $res['valorUnitario'];

                $cantidad += $res['cantidad'];
                $totalDesc += $res['totalDesc'];

                $total2 += $res['importe'];
            }

        }//foreach

        $card['precioUnitario'] = $precio;
        $card['cantidad'] = $cantidad;
        $card['total'] = $total2;
        $card['totalDesc'] = $totalDesc;
        $card['promocionId'] = $promocionId;

        $products[] = $card;

    }//foreach
    foreach($products as $key => $resProducto)
    {
        $producto->setCantidad($resProducto["cantidad"]);
        $producto->setNoIdentificacion($resProducto["noIdentificacion"]);
        $producto->setUnidad($resProducto["unidad"]);
        $producto->setDescripcion($resProducto["descripcion"]);
        $producto->setValorUnitario($resProducto["valorUnitario"]);
        $producto->setExcentoIva($resProducto["excentoIva"]);
        $producto->setCategoriaConcepto($resProducto["categoriaConcepto"]);
        $producto->setImporte();
        $producto->AgregarConcepto();
    }
    $totalDesglosado = $producto->GetTotalDesglosado2(16);
    $smarty->assign("impuestos", $totalDesglosado["impuestos"]);
    unset($totalDesglosado["impuestos"]);
    if($totalDesglosado){
        foreach($totalDesglosado as $key => $total)
        {
            $totalDesglosado[$key] = number_format($totalDesglosado[$key], 2);
        }
    }

    $smarty->assign("totalDesglosado", $totalDesglosado);
    $smarty->assign("conceptos", $_SESSION["conceptos"]);
    $smarty->assign("notaVentaId", $_GET["id"]);
}else
{
    if(isset($_SESSION['ticketsId']))
    {
        $producto->CleanConceptos();
        $producto->CleanImpuestos();

        $subtotal = $iva = $total = 0;
        $concepto = "Factura con base a los tickets: ";
        $products = array();
        foreach($_SESSION['ticketsId'] as $key => $resId)
        {
            $ticketChain .= $resId.",";
            $smarty->assign("ticketChain", $ticketChain);

            $venta->setVentaId($resId);
            $infoVenta = $venta->Info();
            $subtotal += $infoVenta['subtotal'];
            $iva += $infoVenta['iva'];
            $total += $infoVenta['total'];
            $concepto .= $resId.",";
        }
        $concepto = trim($concepto, ",");
        $producto->setCantidad(1);
        $producto->setNoIdentificacion(" ");
        $producto->setUnidad("Pieza");
        $producto->setDescripcion($concepto);
        $producto->setValorUnitario($subtotal);
        $producto->setExcentoIva("no");
        $producto->setCategoriaConcepto("");
        $producto->setImporte();
        $producto->AgregarConcepto();

        $totalDesglosado = $producto->GetTotalDesglosado2(16);
        $smarty->assign("impuestos", $totalDesglosado["impuestos"]);
        unset($totalDesglosado["impuestos"]);
        if($totalDesglosado){
            foreach($totalDesglosado as $key => $total)
            {
                $totalDesglosado[$key] = number_format($totalDesglosado[$key], 2);
            }
        }

        $smarty->assign("totalDesglosado", $totalDesglosado);
        $smarty->assign("conceptos", $_SESSION["conceptos"]);
    }
}
$ivas = $main->ListIvas();
$smarty->assign("ivas", $ivas);
$retIsrs = $main->ListRetIsr();
$smarty->assign("retIsrs", $retIsrs);
$retIvas = $main->ListRetIva();
$smarty->assign("retIvas", $retIvas);
$tiposDeMoneda = $main->ListTipoDeMoneda33();
$smarty->assign("tiposDeMoneda", $tiposDeMoneda);
$comprobantes = $main->ListTiposDeComprobantesValidos();
$smarty->assign("comprobantes", $comprobantes);
$sucursal->setRfcId($rfc->getRfcActive());
$sucursal->setEmpresaId($_SESSION["empresaId"], 1);

//nuevos catalogos
$formasDePago = $catalogo->formasDePago();
$smarty->assign("formasDePago", $formasDePago);

$metodosDePago = $catalogo->metodosDePago();
$smarty->assign("metodosDePago", $metodosDePago);

$usoCfdi = $catalogo->usoCfdi();
$smarty->assign("usoCfdi", $usoCfdi);

$tipoRelacion = $catalogo->tipoRelacion();
$smarty->assign("tipoRelacion", $tipoRelacion);

$resSucursales = $sucursal->GetSucursalesByRfc();

foreach($resSucursales as $key => $res)
{
    if($_SESSION["sucursalId"] != $res["sucursalId"] && $_SESSION["sucursalId"] > 0)
    {
        unset($resSucursales[$key]);
    }
}

$resSuc = $util->DecodeUrlResult($resSucursales);
$sucursales = $resSuc;
$smarty->assign("sucursales", $sucursales);
$excentoIva = $main->ListExcentoIva();
$smarty->assign("excentoIva", $excentoIva);
$smarty->assign("DOC_ROOT", DOC_ROOT);

$id_rfc = $rfc->getRfcActive();
$rfc->setRfcId($id_rfc);
$certNuevo = $rfc->GetCertificadoByRfc();
$smarty->assign("certNuevo", $certNuevo);

$folios->setIdRfc($id_rfc);
$noFolios  = count($listFolios = $folios->GetFoliosByRfc());
$smarty->assign('noFolios', $noFolios);

$qrs = 0;
foreach($listFolios as $key => $value)
{
    if($value["qr"] != "")
    {
        $qrs++;
    }
}
$smarty->assign('qrs', $qrs);

//$cliente->GetCountClientesByActiveRfc();
//$smarty->assign('countClientes', $cliente->GetCountClientesByActiveRfc());

//total costo
if($info["costo"] == 0)
{
    $info["costo"] = COSTO_SISTEMA;
}

if($info["costoModuloNomina"] == 0)
{
    $info["costoModuloNomina"] = COSTO_NOMINA;
}

if($info["costoModuloImpuestos"] == 0)
{
    $info["costoModuloImpuestos"] = COSTO_IMPUESTOS;
}

$subtotalSistema = $info["costo"];

if($info["moduloImpuestos"] == "Si")
{
    $subtotal += $info["costoModuloImpuestos"];
}

if($info["moduloNomina"] == "Si")
{
    $subtotalSistema += $info["costoModuloNomina"];
}

$ivaSistema = $subtotalSistema * IVA;
$totalSistema = $subtotalSistema + $ivaSistema;
$smarty->assign('subtotalSistema', $subtotalSistema);
$smarty->assign('ivaSistema', $ivaSistema);
$smarty->assign('totalSistema', $totalSistema);

?>