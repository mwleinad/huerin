<?php
include_once('../init.php');
include_once('../config.php');
include_once(DOC_ROOT.'/libraries.php');

$base_path = DOC_ROOT.'/expedientes';
//almacenar el archivo en el servidor
if(!is_dir($base_path))
    mkdir($base_path,0777);

$dir_employe = $base_path.'/'.$_POST['idp'];
if(!is_dir($dir_employe))
    mkdir($dir_employe,0777);

$file_files = 'file_'.$_POST['idp'].$_POST['ide'];
$extension = end(explode('.',$_FILES[$file_files]['name']));
$name_file = 'employe_file'.$_POST['idp'].$_POST['ide'].".".$extension;
$temp = $_FILES[$file_files]['tmp_name'];

//comprobar si tiene un archivo actualmente y eliminarlo
$sqlc ="SELECT path FROM personalExpedientes  WHERE personalId='".$_POST['idp']."' AND expedienteId='".$_POST['ide']."'";
$db->setQuery($sqlc);
$antes = $db->GetSingle();
$actual_file = $dir_employe.'/'.$antes;
if(file_exists($actual_file)&&is_file($actual_file))
    unlink($actual_file);

if(move_uploaded_file($temp,$dir_employe.'/'.$name_file))
{
  //si se subio el archivo actualizar tablas
   $sql ="UPDATE personalExpedientes SET fecha='".date('Y-m-d')."', path='".$name_file."' WHERE personalId='".$_POST['idp']."' AND expedienteId='".$_POST['ide']."'";
    $db->setQuery($sql);
    $db->UpdateData();

    //eliminamos el anteior si existe

    echo "ok[#]";
    $personal->setPersonalId($_POST['idp']);
    $expedientes = $personal->GetExpedientes();
    $smarty->assign("DOC_ROOT", DOC_ROOT);
    $smarty->assign("expedientes", $expedientes);
    $smarty->display(DOC_ROOT.'/templates/forms/show-file-personal.tpl');
}
else
{
    echo "fail[#]";
    echo "Hubo un  error al subir archivo";
}
