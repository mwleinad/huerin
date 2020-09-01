<?php

$archivo->SetPage($_GET["p"]);
$resArchivo = $archivo->Enumerate();
$smarty->assign("resArchivo", $resArchivo);
?>
