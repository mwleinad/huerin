<?php
header('Content-Disposition: attachment; filename="test.zip"');
header('Content-type:application/zip');
readfile("/opt/lampp/htdocs/archivos/921_MADERASFINAS.zip");
?>
