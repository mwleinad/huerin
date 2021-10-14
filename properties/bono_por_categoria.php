<?php
$categorias = $rol->EnumeratePorcentajes();
$global_bonos = [];
foreach($categorias as $cat)
    $global_bonos[$cat['categoria']] = $cat['monto'];