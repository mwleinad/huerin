<?php
if(!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Mexico_City');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');
ini_set('memory_limit','3G');

$values['nombre'] = $_POST['rfc'];
$values['facturador'] = $_POST['facturador'];
$values['respCuenta'] = $_POST['responsableCuenta'];
$values['subordinados'] = $_POST['deep'];
$values['cliente'] = $_POST['cliente'];
$values['year'] = $_POST['year'];
//si subordinados esta activo se busca todos los subordinados
$encargados = array();
$empleados = array();
if($values['respCuenta'] == 0) {
    $personal->setActive(1);
    $empleados = $personal->ListAll();
    $empleados = $util->ConvertToLineal($empleados, 'personalId');
}else{
    array_push($encargados,$values['respCuenta']);
    if($values['subordinados']){
        $personal->setPersonalId($values['respCuenta']);
        $empleados = $personal->Subordinados();
        if(!empty($empleados))
            $empleados = $util->ConvertToLineal($empleados,'personalId');

    }
}
$encargados = array_merge($encargados,$empleados);
$values['respCuenta'] =  array_unique($encargados);

$listCxc = $cxc->searchCxC($values);
$totales =  array();
$contratos= [];
foreach($listCxc['items'] as $key => $value)
{

    $totales[$value['nombre']]['total']=$totales[$value['nombre']]['total']+$value['total'];
    $totales[$value['nombre']]['payment']=$totales[$value['nombre']]['payment']+$value['payment'];
    $totales[$value['nombre']]['saldo']=$totales[$value['nombre']]['saldo']+$value['saldo'];
    $totales[$value['nombre']]['nameContact']=$value['nameContact'];
    $totales[$value['nombre']]['rfc']=$value['rfc'];
    $totales[$value['nombre']]['facturador']=$value['facturador'];
    $totales[$value['nombre']]['facturas'][]=$value;
    if(!in_array($value['contractId'],$contratos)){
        array_push($contratos,$value['contractId']);
        $totales[$value['nombre']]['saldoAnterior']=$cxc->getSaldo((int)$values['year']-1,$value['contractId']);
        $totales[$value['nombre']]['saldo']=$totales[$value['nombre']]['saldo']+$totales[$value['nombre']]['saldoAnterior'];
    }
}
$x .=
    "<table border=\"1\">
	<thead>
        <tr>
            <th style=\"background:#E0E5E7;text-align:left\"><b></b></th>
            <th style=\"background:#E0E5E7;text-align:left\"><b>RFC</b></th>
            <th style=\"background:#E0E5E7;text-align:left\"><b>CLIENTE</b></th>
            <th style=\"background:#E0E5E7;text-align:left\"><b>NOMBRE</b></th>
            <th style=\"background:#E0E5E7;text-align:left\"><b>FACTURADOR</b></th>
            <th style=\"background:#E0E5E7;text-align:left\"><b>SALDO INICIAL</b></th>
            <th style=\"background:#E0E5E7;text-align:left\"><b>IMPORTE</b></th>
            <th style=\"background:#E0E5E7;text-align:left\"><b>PAGOS</b></th>
            <th style=\"background:#E0E5E7;text-align:left\"><b>SALDO ACTUAL</b></th>
            
        </tr>
	</thead>
	<tbody>";
foreach($totales as $key=>$cxc){
    $x .="
        <tr>
            <td style=\"text-align:center\">
                <div>
                    <span style=\"color:blue\">[+]</span>
                </div>
            </td>
            <td style='background-color: #9fd39c;color: #0f0f0f'>".$cxc['rfc']."</td>
             <td style='background-color: #9fd39c;color: #0f0f0f'>".$cxc['nameContact']."</td>
             <td style='background-color: #9fd39c;color: #0f0f0f'>".$key."</td>
             <td style='background-color: #9fd39c;color: #0f0f0f'>".$cxc['facturador']."</td>
             <td style='background-color: #9fd39c;color: #0f0f0f'>".number_format($cxc['saldoAnterior'],2,'.',',')."</td>
             <td style='background-color: #9fd39c;color: #0f0f0f'>".number_format($cxc['total'],2,'.',',')."</td>
             <td style='background-color: #9fd39c;color: #0f0f0f'>".number_format($cxc['payment'],2,'.',',')."</td>
             <td style='background-color: #9fd39c;color: #0f0f0f'>".number_format($cxc['saldo'],2,'.',',')."</td>
        </tr>
    ";
    $x .="<tr>
            <td colspan=\"2\"></td>
            <td style='background-color: #06b8f6;color: #0f0f0f;text-align: left;font-weight: bold'>Folio</td>
            <td style='background-color: #06b8f6;color: #0f0f0f;text-align: left;font-weight: bold'>Fecha</td>
            <td style='background-color: #06b8f6;color: #0f0f0f;text-align: left;font-weight: bold'>Importe</td>
            <td style='background-color: #eae527;color: #0f0f0f;text-align: left;font-weight: bold'>Pagos</td>
            <td style='background-color: #06b8f6;color: #0f0f0f;text-align: left;font-weight: bold'>saldo</td>
            <td></td>
        </tr>";
    foreach($cxc['facturas'] as $kf=>$factura){
        $x .="<tr>
                <td colspan=\"2\">
                    <div>
                        <span style=\"color:blue;\">[+]</span>
                    </div>
                </td>
                <td  style='background-color:#3df5ff'>".$factura['serie'].$factura['folio']."</td>
                <td  style='background-color:#3df5ff'>".$factura['fecha']."</td>
                <td  style='background-color:#3df5ff'>".number_format($factura['total'],2,'.',',')."</td>
                <td  style='background-color:#eae527'>".number_format($factura['payment'],2,'.',',')."</td>
                <td  style='background-color:#3df5ff'>".number_format($factura['saldo'],2,'.',',')."</td>
                <td></td>
              </tr> 
        ";
        $x .="<tr>
            <td colspan=\"2\"></td>
            <td style='background-color: #eae527;color: #0f0f0f;text-align: left;font-weight: bold'>Folio</td>
            <td style='background-color: #eae527;color: #0f0f0f;text-align: left;font-weight: bold'>Fecha</td>
            <td style='background-color: #eae527;color: #0f0f0f;text-align: left;font-weight: bold'>Metodo de pago</td>
            <td style='background-color: #eae527;color: #0f0f0f;text-align: left;font-weight: bold'>Importe</td>
            <td style='background-color: #eae527;color: #0f0f0f;text-align: left;font-weight: bold'>Deposito</td>
            <td></td>
        </tr>";
       if(!empty($factura['payments'])){
           foreach($factura['payments']  as $payment){
               $x .="<tr>
                <td colspan=\"2\"></td>
                <td>".$payment['folioPago']."</td>
                <td>".$payment['paymentDate']."</td>
                <td>".$payment['mpago']."</td>
                <td>".number_format($payment['amount'],2,'.',',')."</td>
                <td>".number_format($payment['deposito'],2,'.',',')."</td>
                <td></td>
              </tr> ";
           }
       } else{
           $x .="<tr>
                    <td colspan=\"2\"></td>
                    <td colspan=\"6\" align='center'>No existe movimientos de pagos para esta factura.</td>
                </tr>";
       }

    }
}
$x .= '</tbody>
	</table>';
$name = 'reporte de cxc';
header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-type:   application/x-msexcel; charset=utf-8");
header("Content-Disposition: attachment; filename=".$name.".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo $x;


exit;

?>