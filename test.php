<?php 
print_r($_SERVER);
echo "jere";
exit;
if (!isset($_SESSION)) 
{
  session_start();
}
$_SESSION["test"] = "creada";
print_r($_SESSION);
echo session_id();

?>