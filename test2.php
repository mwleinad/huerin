<?php 
if (!isset($_SESSION)) 
{
  session_start();
}
$_SESSION["test2"] = "creada2";
print_r($_SESSION);
?>