<?php

include_once('init.php');
include_once('config.php');
include_once('constants.php');
include_once(DOC_ROOT.'/libraries.php');
$db->setQuery("SELECT fecha, timbreFiscal, folio, serie, comprobanteId, empresaId FROM comprobante
	WHERE fecha > '2016-12-05 12:57:00' AND fecha < '2016-12-05 14:50:00' AND status = '1'");
$result = $db->GetResult();
?>
    <table style="font-size:10px">
        <tr>
            <td>Serie</td>
            <td>Folio</td>
            <td>Fecha</td>
            <td>comprobanteId</td>
            <td>timbreFiscal</td>
        </tr>
        <?php
        	foreach($result as $res)
            {
                $timbre = unserialize(urldecode($res["timbreFiscal"]));
            ?>
              <tr>
                  <td><?php echo $res["serie"]?></td>
                  <td><?php echo $res["folio"]?></td>
                <td><?php echo $res["fecha"]?></td>
                <td><?php echo $res["comprobanteId"]?></td>
                <td><?php echo $timbre["UUID"]?></td>
                </tr>
            <?php
            }
        ?>
    </table>
    <br /><br />
    Timbres a Cancelar
    <br /><br />
<?php
foreach($result as $key => $res)
{
    $timbre = unserialize(urldecode($res["timbreFiscal"]));
    echo $timbre["UUID"];
    echo "<br>";

    if($res["empresaId"] == 15)
    {
        $rfc = "BHU120320CQ1";
        $pfx = DOC_ROOT."/empresas/15/certificados/1/00001000000404247361.cer.pfx";
        $pfxPassword = "BHU120320";
    }
    if($res["empresaId"] == 21)
    {
        $rfc = "BCO160224ECA";
        $pfx = DOC_ROOT."/empresas/21/certificados/30/00001000000402946663.cer.pfx";
        $pfxPassword = "BCO160224";
    }
    else
    {
        $rfc = "BABJ701019LD7";
        $pfx = DOC_ROOT."/empresas/20/certificados/29/00001000000402502066.cer.pfx";
        $pfxPassword = "BABJ701019";
    }
    $response = $pac->CancelaCfdi(USER_PAC, PW_PAC, $rfc, $timbre["UUID"], $pfx, $pfxPassword);
    if(is_array($response["cancelaCFDiReturn"]))
    {
        $db->setQuery("UPDATE comprobante SET status = '0'
			WHERE comprobanteId = '".$res["comprobanteId"]."'");
        $nuevo = $db->UpdateData();
    }
    else
    {
        echo "falla";
    }
    //exit;
}
?>