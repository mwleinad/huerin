<?php
/** BASE DE DATOS **/
define("USER_PAC", "STI070725SAA");
define("PW_PAC", "oobrotcfl");

/** SMTP **/
define('SMTP_HOST','');
define('SMTP_PORT','');
define('SMTP_USER','');
define('SMTP_PASS','');

/** PAGINACION **/
define('ITEMS_PER_PAGE', '15');

//instancias
define("SERVICIOS_NOMINA", "5, 6, 7, 13, 14, 15");

//start balance
define("BALANCE_MONTH", "7");
define("BALANCE_YEAR", "2013");

switch($_SERVER['HTTP_HOST'])
{
    case 'localhost':
        define("SEND_TO", "administracion@emfrich.com.mx");
        define("SEND_TO2", "isc061990@gmail.com");
        define("SEND_TO3", "leasib_666@hotmail.com");
        define("FROM_MAIL", "noreply@braunhuerin.com.mx");
        define("FROM_MAILAlERTA", "noreply@noreply.com");
    break;
    default:
        define("SEND_TO", "asanchez@braunhuerin.com.mx");
        define("SEND_TO2", "cobranza2@braunhuerin.com.mx ");
        define("SEND_TO3", "cobranza3@braunhuerin.com.mx");
        define("FROM_MAIL", "facturacion@braunhuerin.com.mx");
        define("FROM_MAILAlERTA", "noreply@noreply.com");
    break;
}

$CC_EMAILS =  array(SEND_TO=>'ARACELI SANCHEZ GALVAN',SEND_TO2=>'COBRANZA1',SEND_TO3=>'COBRANZA2');
define("CC_EMAILS", serialize($CC_EMAILS));

define("SERVICIO_CONTABILIDAD", $servicioContabilidad);
define("RIF", 8);
define("ANUAL", 11);
define("DIM", 19);
define("IDBRAUN", 56);
define("IDHUERIN", 13);
?>