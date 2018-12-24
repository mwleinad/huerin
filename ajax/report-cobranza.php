<?php

include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

switch($_POST["type"])
{
	case 'doSearch':
			$sql = '';
			
			if($_POST['name'])
				$sql .= ' AND name LIKE "%'.$_POST['name'].'%"';
			if($_POST['folio'])
				$sql .= ' AND folio = "'.$_POST['folio'].'"';
			if($_POST['contCatId'])
				$sql .= ' AND contCatId = "'.$_POST['contCatId'].'"';
			if($_POST['status'])
				$sql .= ' AND status = "'.$_POST['status'].'"';

			$stOblig = $_POST['stOblig'];
			$resContracts = $contract->Search($sql);
			$contracts = array();
			foreach($resContracts as $key => $val){
				
				$card = $val;
				
				$card['name'] = utf8_encode($val['name']);
				
				$contCat->setContCatId($val['contCatId']);
				$card['tipo'] = $contCat->GetNameById();
				
				$contract->setContractId($val['contractId']);
				$card['stOblig'] = $contract->GetStatusOblig();
								
				$card['status'] = ucfirst($card['status']);
				
				if($stOblig){
					
					if($stOblig == $card['stOblig'])
						$contracts[$key] = $card;	
						
				}else
					$contracts[$key] = $card;
				
			}
			
			$totalRegs = count($contracts);
			
			echo 'ok[#]';
			
			$smarty->assign("contracts", $contracts);
			$smarty->assign("DOC_ROOT", DOC_ROOT);
			$smarty->display(DOC_ROOT.'/templates/lists/report-cobranza.tpl');
			
			echo '[#]';
			
			echo 'Total de Registros: <b>'.$totalRegs.'</b>';
			
	break;
	case 'searchCobranzaMensual':


		$mes = (int)$_POST['mes'];
		$anio = (int)$_POST['year'];
        $empresaId = (int)$_POST['empresaId'];
        $firstDate = date("Y-m-d",mktime(0,0,0,$mes,1,$anio));
		$dia = date("d",mktime(0,0,0,$mes+1,0,$anio));
		$lastDay = date("Y-m-d",mktime(0,0,0,$mes,$dia,$anio));

        $sql = "SELECT a.paymentDate,concat_ws('',b.serie,b.folio) as factura,a.amount as importe,a.deposito,d.nameContact,c.name,e.name as responsable,
        b.xml,b.empresaId ,b.serie as serief,b.folio as foliof,b.rfcId,a.comprobantePagoId FROM payment a 
        LEFT JOIN comprobante b ON a.comprobanteId=b.comprobanteId
        LEFT JOIN contract c ON b.userId=c.contractId 
        LEFT JOIN customer d ON c.customerId=d.customerId
        LEFT JOIN personal e ON c.responsableCuenta=e.personalId
        WHERE b.empresaId='".$empresaId."' AND date(a.paymentDate)>='".$firstDate."'
        AND date(a.paymentDate) <='".$lastDay."'ORDER BY  a.paymentDate DESC ";
        $db->setQuery($sql);
        $payments = $db->GetResult($sql);

		//crear el zip que enviara todos los xmls
        $zip =  new ZipArchive();
        $file_name = 'XMLS2-'.strtoupper($util->GetMonthByKey($mes)).'.ZIP';
        $file_zip =DOC_ROOT.'/sendFiles/'.$file_name;
        if($zip->open($file_zip,ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)===true) {
            foreach ($payments as $kp => $vp) {
                $archivo_xml = "SIGN_" . $vp['empresaId'] . '_' . $vp['serief'] . '_' . $vp['foliof'] . '.xml';
                $enlace_xml = DOC_ROOT . '/empresas/' . $vp['empresaId'] . '/certificados/' . $vp['rfcId'] . '/facturas/xml/' . $archivo_xml;
                if (file_exists($enlace_xml)) {
                    $zip->addFile($enlace_xml, 'XML-CFDI2/'.$archivo_xml);
                }
                //comprobar si hay complementos, tambien se adjunta en el zip
                if($vp['comprobantePagoId']>0){
                    $db->setQuery("SELECT folio,serie,empresaId,rfcId FROM comprobante WHERE comprobanteId='".$vp['comprobantePagoId']."'");
                    $complemento = $db->GetRow();
                    if(!empty($complemento)){
                        $archivo_xml_comp = "SIGN_" . $complemento['empresaId'] . '_' . $complemento['serie'] . '_' . $complemento['folio'] . '.xml';
                        $enlace_xml_comp = DOC_ROOT . '/empresas/' . $complemento['empresaId'] . '/certificados/' . $complemento['rfcId'] . '/facturas/xml/' . $archivo_xml_comp;
                        if (file_exists($enlace_xml_comp))
                            $zip->addFile($enlace_xml_comp, 'XML-COMP2/'.$archivo_xml_comp);
                    }
                }
            }
            if(!$zip->numFiles)
                $zip->addFromString("README.txt",'EMPTY');

           	$zip->close();
        }
        else {
            echo 'Error creando en'.$file_zip.'\n';
        }
        $html = '<html>
			<head>
				<title>Cupon</title>
				<style type="text/css">
					table,td {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #C0C0C0;
						border-collapse: collapse;
					}
					.cabeceraTabla {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #C0C0C0;
						background: gray;
						color: #FFFFFF;
						vertical-align: center;
						border-collapse: collapse;
					}
					.divInside {
						font-family:verdana;
						text-transform: uppercase;
						font:bold 12px "Trebuchet MS";
						font-size:12px;
						border: 1px solid #686DAB;
						background: #686DAB;
						color: #FFFFFF;
						vertical-align: center;
						text-align: center;
						height: 50px;
					}
				</style>
			</head>
			';
        $smarty->assign("registros", $payments);
        $smarty->assign('mes',$util->GetMonthByKey($mes));
        $contents = $smarty->fetch(DOC_ROOT . '/templates/lists/rep-cobranza.tpl');
        $html .= $contents;
        //$html = str_replace('$', '', $html);
        $html = str_replace(',', '', $html);
        $excel->ConvertToExcel($html,'xlsx',false,'rep-cobranza-mensual',true,500);
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/rep-cobranza-mensual.xlsx";
        echo "[#]";
        echo WEB_ROOT."/download.php?file=".WEB_ROOT."/sendFiles/".$file_name;
	break;
    case 'searchAcumulada':
        $year = $_POST['year'];
        $formValues['subordinados'] = $_POST['subordinados'];
        $formValues['respCuenta'] = $_POST['responsableCuenta'];
        $formValues['cliente'] = $_POST["rfc"];
        $formValues['year'] = $year;
        $formValues['activos'] = true;

        if($_POST['monthInicial']>$_POST['monthFinal'])
        {
            $util->setError(0,'error','Mes inicial debe ser inferior al mes final');
            $util->PrintErrors();
            echo "fail[#]";
            $smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');
            exit;
        }
        include_once(DOC_ROOT.'/ajax/filterOnlyContract.php');

        $meses = [];
        $base = [];
        for($m=$_POST['monthInicial'];$m<=$_POST['monthFinal'];$m++)
        {
            $meses[$m]= $monthsInt[$m];
            $base[$m]= [];
        }
        $contratos = [];

        //hacer la busqueda y la estructura del array segun el tipo de detalle
        switch($_POST['groupBy']){
            case 'contrato':
                //la busqueda se realiza por medio de los comprobantes emitidos del mes que se esta pasando
                //se suman lo abonos sin iva.
                foreach($contracts as $key => $contrato) {
                    $cad = [];
                    $cad['customerId'] = $contrato['contractId'];
                    $cad['customer'] = $contrato['nameContact'];
                    $cad['razon'] = $contrato['name'];
                    $sql ="select sum(a.total) as total,sum(b.amount) as amount,month(a.fecha) as mes
                           from comprobante a 
                           left join (select comprobanteId,sum(amount) as amount from payment group by comprobanteId) b on a.comprobanteId=b.comprobanteId
                           inner join contract c on a.userId=c.contractId and c.activo='Si'
                           where month(a.fecha) >='".$_POST['monthInicial']."' and month(a.fecha)<='".$_POST['monthFinal']."' and year(a.fecha)='".$formValues['year']."' 
                           and a.userId='".$contrato['contractId']."' and a.tiposComprobanteId in(1)  and a.status ='1' 
                           group by month(a.fecha) order by month(a.fecha) desc";

                    $util->DB()->setQuery($sql);
                    $pagos = $util->DB()->GetResult();
                    $totalXcontrato = 0;
                    if(!empty($pagos)){
                        foreach($pagos as $pago){
                                if(!$_POST['whitiva'])
                                    $pago['amount'] = $pago['amount']/1.16;

                            $totalXcontrato = $totalXcontrato+$pago['amount'];
                            $base[$pago['mes']] = $pago;
                        }
                        $totales = [];
                        $totales['isColTotal'] = true;
                        $totales['total'] = $totalXcontrato;
                        $base[13] = $totales;
                        $cad['pagos'] = $base;
                        $contratos[]=$cad;
                    }
                }
                echo "ok[#]";
                $smarty->assign("meses", $meses);
                $smarty->assign("contratos", $contratos);
                $smarty->display(DOC_ROOT.'/templates/lists/report-cobranza-acumulada.tpl');
            break;
            default:
                echo "ok[#]";
                echo "ds";
            break;
        }

    break;
		
}
?>
