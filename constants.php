<?php
/** BASE DE DATOS **/
define("USER_PAC", "STI070725SAA");
define("PW_PAC", "oobrotcfl");

/** SMTP **/
define('SMTP_HOST','mail.avantika.com.mx');
define('SMTP_PORT','587');
define('SMTP_USER','smtp@avantika.com.mx');
define('SMTP_PASS','smtp12345');

define('SMTP_HOST2','54.148.210.219');
define('SMTP_PORT2','587');
define('SMTP_USER2','smtpsis');
define('SMTP_PASS2','Strong47-');

/** PAGINACION **/
define('ITEMS_PER_PAGE', '15');

//instancias
define("SERVICIOS_NOMINA", "5, 6, 7, 13, 14, 15");
define("CONTRACTS_EXECPTION", "2507, 2508, 2782, 2767, 2768");

//start balance
define("BALANCE_MONTH", "7");
define("BALANCE_YEAR", "2013");
define("RFC_DEFAULT", 30);

switch($_SERVER['HTTP_HOST'])
{
    case 'localhost':
    case 'huerin.test':
        define("SEND_TO", "bissael.cruz@gmail.com");
        define("FROM_MAIL", "noreply@braunhuerin.com.mx");
        define("EMAILCOORDINADOR", "isc061990@gmail.com");
        define("FROM_MAILAlERTA", "noreply@noreply.com");
        define("PATHWKHTML",DOC_ROOT.'/util/wkhtmltox/bin/wkhtmltopdf');
        define("FROM_FACTURA", "test");
    break;
    case 'bhtest.ddns.net':
        define("SEND_TO", "bissael.cruz@hotmail.com");
        define("FROM_MAIL", "noreply@braunhuerin.com.mx");
        define("EMAILCOORDINADOR", "rzetina@braunhuerin.com.mx");
        define("FROM_MAILAlERTA", "noreply@noreply.com");
        define("PATHWKHTML",'/usr/bin/wkhtmltopdf');
        define("FROM_FACTURA", "test");
    break;
    default:
        define("SEND_TO", "asanchez@braunhuerin.com.mx");
        define("FROM_MAIL", "facturacionbh@braunhuerin.com.mx");
        define("EMAILCOORDINADOR", "rzetina@braunhuerin.com.mx");
        define("FROM_MAILAlERTA", "noreply@noreply.com");
        define("PATHWKHTML",'/usr/bin/wkhtmltopdf');
        define("FROM_FACTURA", "produccion");
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
define("PRECIERREREVMENSUAL", 57);
define("INICIO_ADEUDO", '2018-01-01');
define("IDSUP", 201);
define("ITER_LIMIT", 5);

define('KB', 1024);
define('MB', 1048576);
define('GB', 1073741824);
define('TB', 1099511627776);

?>
