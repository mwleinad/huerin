<?php
$user->allowAccess(213);
$user->allowAccess(214);
echo phpinfo();exit;
 $coffe =  new Coffe();
 if($_GET['menu'])
 {
     $coffe->setId($_GET['menu']);
     $eles =  $coffe->Info();
     $elesExplode = explode(',',$eles);
     $coffe->setElements($elesExplode);
     $coffe->GenerateFile('view');
     exit(0);

 }
 $coffe->setElements($_SESSION['platillos']);
 $coffe->GenerateFile('view');
 exit(0);

?>