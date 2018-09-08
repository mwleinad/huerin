<?php
/** BASE DE DATOS **/
define("USER_PAC", "STI070725SAA");
define("PW_PAC", "oobrotcfl");

/** SMTP **/
define('SMTP_HOST','mail.avantika.com.mx');
define('SMTP_PORT','587');
define('SMTP_USER','smtp@avantika.com.mx');
define('SMTP_PASS','smtp12345');

define('SMTP_HOST2','mail.braunhuerin.com.mx');
define('SMTP_PORT2','587');
define('SMTP_USER2','avisos@braunhuerin.com.mx');
define('SMTP_PASS2','Av1s0s');

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
        define("SEND_TO", "leasib_666@hotmail.com");
        //define("SEND_TO1", "jeje@braunhuerin.com.mx ");
        //define("SEND_TO2", "de@gmail.com");
        //define("SEND_TO3", "desarrollo@hotmail.com");
        define("FROM_MAIL", "noreply@braunhuerin.com.mx");
        define("FROM_MAILAlERTA", "noreply@noreply.com");
        define("PATHWKHTML",DOC_ROOT.'/util/wkhtmltox/bin/wkhtmltopdf');
    break;
    default:
        define("SEND_TO", "asanchez@braunhuerin.com.mx");
        //define("SEND_TO1", "cobranzabh1@braunhuerin.com.mx ");
        //define("SEND_TO2", "cobranzabh2@braunhuerin.com.mx ");
        //define("SEND_TO3", "cobranzabh3@braunhuerin.com.mx");
        define("FROM_MAIL", "facturacionbh@braunhuerin.com.mx");
        define("FROM_MAILAlERTA", "noreply@noreply.com");
        define("PATHWKHTML",'/usr/bin/wkhtmltopdf');
    break;
}
$facturadores =  ["'BHSC'","'Huerin'","'Braun'","'Efectivo'"];
$CC_EMAILS =  array(SEND_TO=>'ARACELI SANCHEZ GALVAN');
define("CC_EMAILS", serialize($CC_EMAILS));
define("FACTURADOR",serialize($facturadores));

define("SERVICIO_CONTABILIDAD", $servicioContabilidad);
define("RIF", 8);
define("RIFAUDITADO", 55);
define("ANUAL", 11);
define("DIM", 19);
define("IDBRAUN", 56);
define("IDHUERIN", 13);
define("PRECIERRE", 36);
define("PRECIERREAUDITADO", 52);
define("INICIO_ADEUDO", '2018-01-01');
define("IDSUP", 201);
define("ITER_LIMIT", 5);

?>