<?php

$documento->SetPage($_GET["p"]);
$resDocumento = $documento->Enumerate();
$smarty->assign("resDocumento", $resDocumento);
?>