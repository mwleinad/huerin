<?php

class Util extends CustomError
{
    public $DB;
    private $DBSelect;
    private $DBRemote;
    private $DBProspect;

    public function DB($change_collate = true)
    {
        if ($this->DB == null) {
            $this->DB = new DB($change_collate);
        }
        return $this->DB;
    }

    public function DBRemote($id = "pascacio_general")
    {
        //$this->DBRemote = null;
        if ($this->DBRemote == null) {
            $this->DBRemote = new DBRemote($id);
        }
        $this->DBRemote->setSqlDatabase($id);
        return $this->DBRemote;
    }

    public function DBProspect($id = SQL_DATABASE_PROSPECT)
    {
        if ($this->DBProspect == null) {
            $this->DBProspect = new DBProspect($id);
        }
        $this->DBProspect->setSqlDatabase($id);
        return $this->DBProspect;
    }


    public function DBSelect($empresaId = 1)
    {
        if ($this->DBSelect == null) {
            $this->DBSelect = new DB();
        }
        //$this->DBSelect->setSqlDatabase("facturas_".$empresaId);
        return $this->DBSelect;
    }

    function RoundNumber($number)
    {
        return round($number, 6); // RECORDAR::cambio de precision de 6 a 2 decimales como en la clase XML
    }

    function FormatSeconds($sec, $padHours = false)
    {
        $hms = "";
        $hours = intval(intval($sec) / 3600);
        $hms .= ($padHours)
            ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ':'
            : $hours . ':';
        $minutes = intval(($sec / 60) % 60);
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ':';
        $seconds = intval($sec % 60);
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
        return $hms;
    }

    function FormatNumber($number, $dec = 2)
    {
        return number_format($number, $dec);
    }

    function FormatDateAndTime($time)
    {
        $time = date("Y-m-d H:i:s", $time);
        return $time;
    }

    function FormatDateAndTimeSat($date)
    {
        $time = strtotime($date);
        $date = date("d/m/Y H:i:s", $time);
        return $date;
    }

    function FormatDate($time)
    {
        $time = date("Y-m-d", $time);
        return $time;
    }

    function FormatDateMySql($date)
    {
        $date = explode("-", $date);
        return $date[2] . "-" . $date[1] . "-" . $date[0];
    }

    function FormatDateMySqlSlash($date)
    {
        $date = explode("/", $date);
        return $date[2] . "-" . $date[1] . "-" . $date[0];
    }

    function FormatDateMySqlBack($date)
    {
        $this->FormatDateMySql($date);
    }

    function ValidateInteger(&$number, $max = 0, $min = 0)
    {
        if (!preg_match("/^[0-9]+$/", $number)) $number = 0;

        if ($min != 0 && $number < $min) {
            $number = $min;
            return;
        }

        if ($max != 0 && $number > $max) {
            $number = $max;
            return;
        }

        if ($number > 9223372036854775807) {
            $number = 9223372036854775807;
        }
    }

    function ValidateNumericWhitRange($value, $min = -1, $max = 0, $field = "")
    {
        if (!is_numeric($value)) {
            $this->setError(10055, 'error', "Debe ingresar un valor numerico", $field);
            return false;
        }

        if ($min >= 0) {
            if ($value < $min) {
                $this->setError(10055, 'error', "El valor minimo permitido es $min", $field);
                return false;
            }

        }
        if ($max) {
            if ($value > $max) {
                $this->setError(10055, 'error', "El valor maximo permito es $max", $field);
                return false;
            }

        }
        return true;
    }

    function limpiaNumero($numero)
    {

        $numero = trim($numero);
        $numero = str_replace(' ', '', $numero);
        $numero = str_replace('$', '', $numero);
        $numero = str_replace(',', '', $numero);

        return $numero;

    }//limpiaNumero

    function ValidateFloat(&$number, $decimals = 2, $max = 0, $min = 0)
    {
        if (!is_numeric($number)) {
            $number = 0;
        }

        $number = round($number, $decimals);

        if ($max != 0 && $number > $max) {
            $number = $max;
            return;
        }

        if ($min != 0 && $number < $min) {
            $number = $min;
            return;
        }

        if ($number > 9223372036854775807) {
            $number = 9223372036854775807;
        }
    }

    function ValidateDecimal($number)
    {
        if (!is_numeric($number)) {
            $this->setError(10055, 'error', '', 'Monto de la Renta');
            return false;
        }

        return true;
    }

    function ValidateOnlyNumeric($value, $field)
    {
        if (!ctype_digit($value)) {
            $this->setError(10055, 'error', 'El valor debe ser una cadena numerica:', $field);
            return false;
        }

        return true;
    }

    function ValidateIsNumeric($value, $field)
    {
        if (!is_numeric($value)) {
            $this->setError(10055, 'error', 'El valor debe ser una cadena numerica:', $field);
            return false;
        }

        return true;
    }

    function ValidateOption($value, $field)
    {

        if ($value == '')
            return $this->setError(10037, "error", "", $field);

    }

    function CheckDomain($domain, $server, $findText)
    {
        // Open a socket connection to the whois server
        $con = fsockopen($server, 43);
        if (!$con) {
            return false;
        }
        // Send the requested doman name
        fputs($con, $domain . "\r\n");

        // Read and store the server response
        $response = ' :';
        while (!feof($con)) {
            $response .= fgets($con, 128);
        }

        fclose($con);

        // Check the response stream whether the domain is available
        if (strpos($response, $findText)) {
            return true;
        } else {
            return false;
        }
    }

    function ValidateString(&$string, $max_chars = 5000, $minChars = 1, $field = null)
    {
        if (strlen($string) < $minChars) {
            return $this->setError(10000, "error", "", $field);
        }

        if (strlen($string) > $max_chars) {
            $string = substr($string, 0, $max_chars);
        }
        $string = htmlspecialchars($string, ENT_QUOTES);

    }

    function ValidateRequireField($string, $field = null)
    {
        if ($string == "") {
            $this->setError(10013, "error", "", $field);
            return false;
        }

        return true;
    }

    function ValidateFieldCero($string, $field = null)
    {
        if ($string == 0) {
            $this->setError(10013, "error", "", $field);
            return false;
        }

        return true;
    }

    function ValidateMail($mail, $field = "")
    {
        $mail = strtolower($mail);
        if (!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/', trim($mail))) {
            $this->setError(10002, "error", "", $field);
            return false;
        }
        return true;
    }

    function ValidateEmail($mail)
    {
        $mail = strtolower($mail);
        if (!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/', trim($mail))) {
            return false;
        }

        return true;
    }

    function ValidateUrl($url)
    {
        if (!preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) {
            return $this->error = 10001;
        }
    }

    function ValidateFile($pathToFile)
    {
        $handle = @fopen($pathToFile, "r");
        if ($handle === false) {
            return $this->error = 10003;
        }
        fclose($handle);
    }

    function wwwRedirect()
    {
        if (!preg_match("/^www./", $_SERVER['HTTP_HOST'])) {
            header("Location: " . WEB_ROOT);
        }
    }

    function MakeTime($day, $month, $year)
    {
        return mktime(0, 0, 0, $month, $day, $year);
    }

    function CreateDropDown($name, $from, $to, $selectedIndex)
    {
        $select = "<select name='" . $name . "' id='" . $name . "'>";
        for ($ii = $from; $ii <= $to; $ii++) {
            if ($selectedIndex == $ii)
                $select .= "<option value='" . $ii . "' selected='selected'>" . $ii . "</option>";
            else
                $select .= "<option value='" . $ii . "'>" . $ii . "</option>";
        }
        $select .= "</select>";
        return $select;
    }

    function GetCurrentYear()
    {
        return date("Y", time());
    }

    function GetCents($num)
    {
        $cents = ($num - floor($num)) * 100;
        $cents = ceil($cents);
        if ($cents < 10)
            $cents = "0" . $cents;
        return $cents;
    }

    function num2letras($num, $fem = true, $dec = true)
    {
        $matuni[2] = "dos";
        $matuni[3] = "tres";
        $matuni[4] = "cuatro";
        $matuni[5] = "cinco";
        $matuni[6] = "seis";
        $matuni[7] = "siete";
        $matuni[8] = "ocho";
        $matuni[9] = "nueve";
        $matuni[10] = "diez";
        $matuni[11] = "once";
        $matuni[12] = "doce";
        $matuni[13] = "trece";
        $matuni[14] = "catorce";
        $matuni[15] = "quince";
        $matuni[16] = "dieciseis";
        $matuni[17] = "diecisiete";
        $matuni[18] = "dieciocho";
        $matuni[19] = "diecinueve";
        $matuni[20] = "veinte";
        $matunisub[2] = "dos";
        $matunisub[3] = "tres";
        $matunisub[4] = "cuatro";
        $matunisub[5] = "quin";
        $matunisub[6] = "seis";
        $matunisub[7] = "sete";
        $matunisub[8] = "ocho";
        $matunisub[9] = "nove";

        $matdec[2] = "veint";
        $matdec[3] = "treinta";
        $matdec[4] = "cuarenta";
        $matdec[5] = "cincuenta";
        $matdec[6] = "sesenta";
        $matdec[7] = "setenta";
        $matdec[8] = "ochenta";
        $matdec[9] = "noventa";
        $matsub[3] = 'mill';
        $matsub[5] = 'bill';
        $matsub[7] = 'mill';
        $matsub[9] = 'trill';
        $matsub[11] = 'mill';
        $matsub[13] = 'bill';
        $matsub[15] = 'mill';
        $matmil[4] = 'millones';
        $matmil[6] = 'billones';
        $matmil[7] = 'de billones';
        $matmil[8] = 'millones de billones';
        $matmil[10] = 'trillones';
        $matmil[11] = 'de trillones';
        $matmil[12] = 'millones de trillones';
        $matmil[13] = 'de trillones';
        $matmil[14] = 'billones de trillones';
        $matmil[15] = 'de billones de trillones';
        $matmil[16] = 'millones de billones de trillones';

        $num = trim((string)@$num);
        if ($num[0] == '-') {
            $neg = 'menos ';
            $num = substr($num, 1);
        } else
            $neg = '';
        while ($num[0] == '0') $num = substr($num, 1);
        if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
        $zeros = true;
        $punt = false;
        $ent = '';
        $fra = '';
        for ($c = 0; $c < strlen($num); $c++) {
            $n = $num[$c];
            if (!(strpos(".,'''", $n) === false)) {
                if ($punt) break;
                else {
                    $punt = true;
                    continue;
                }

            } elseif (!(strpos('0123456789', $n) === false)) {
                if ($punt) {
                    if ($n != '0') $zeros = false;
                    $fra .= $n;
                } else

                    $ent .= $n;
            } else

                break;

        }
        $ent = '     ' . $ent;
        if ($dec and $fra and !$zeros) {
            $fin = ' coma';
            for ($n = 0; $n < strlen($fra); $n++) {
                if (($s = $fra[$n]) == '0')
                    $fin .= ' cero';
                elseif ($s == '1')
                    $fin .= $fem ? ' una' : ' un';
                else
                    $fin .= ' ' . $matuni[$s];
            }
        } else
            $fin = '';
        if ((int)$ent === 0) return 'Cero ' . $fin;
        $tex = '';
        $sub = 0;
        $mils = 0;
        $neutro = false;
        while (($num = substr($ent, -3)) != '   ') {
            $ent = substr($ent, 0, -3);
            if (++$sub < 3 and $fem) {
                $matuni[1] = 'una';
                $subcent = 'os'; /* MODIFICADO as */
            } else {
                $matuni[1] = $neutro ? 'un' : 'uno';
                $subcent = 'os';
            }
            $t = '';
            $n2 = substr($num, 1);
            if ($n2 == '00') {
            } elseif ($n2 < 21)
                $t = ' ' . $matuni[(int)$n2];
            elseif ($n2 < 30) {
                $n3 = $num[2];
                if ($n3 != 0) $t = 'i' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            } else {
                $n3 = $num[2];
                if ($n3 != 0) $t = ' y ' . $matuni[$n3];
                $n2 = $num[1];
                $t = ' ' . $matdec[$n2] . $t;
            }
            $n = $num[0];
            if ($n == 1) {
                $t = ' ciento' . $t;
            } elseif ($n == 5) {
                $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
            } elseif ($n != 0) {
                $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
            }
            if ($sub == 1) {
            } elseif (!isset($matsub[$sub])) {
                if ($num == 1) {
                    $t = ' mil';
                } elseif ($num > 1) {
                    $t .= ' mil';
                }
            } elseif ($num == 1) {
                $t .= ' ' . $matsub[$sub] . '?n';
            } elseif ($num > 1) {
                $t .= ' ' . $matsub[$sub] . 'ones';
            }
            if ($num == '000') $mils++;
            elseif ($mils != 0) {
                if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
                $mils = 0;
            }
            $neutro = true;
            $tex = $t . $tex;
        }
        $tex = $neg . substr($tex, 1) . $fin;
        return ucfirst($tex);
    }

    function ImprimeNoFolio($folio)
    {
        return sprintf("%05d", $folio);
    }

    function ConvertirMes($mes)
    {
        $mesArray = array("N/A", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        return $mesArray[$mes];
    }

    function LoadPage($page, $extendible = "")
    {
        header("location: " . WEB_ROOT . "/" . $page . $extendible);
    }

    function Today()
    {
        return date("Y-m-d");
    }

    function ExtractPeriod()
    {
        $today = $this->Today();
        $this->DB()->setQuery("SELECT periodoId FROM periodo WHERE status = 'activo'");

        $period = $this->DB()->GetSingle();

        if ($period == 0) {
            $period = 1;
        }

        return $period;
    }

    function GetReservationDays($in, $out)
    {
        $entrada = explode("-", $in);
        $entrada[2] = str_replace(" 00:00:00", "", $entrada[2]);

        $fechaEntrada = $this->MakeTime($entrada[2], $entrada[1], $entrada[0]);

        $salida = explode("-", $out);
        $salida[2] = str_replace(" 00:00:00", "", $salida[2]);
        $fechaSalida = $this->MakeTime($salida[2], $salida[1], $salida[0]);

        return $days = ($fechaSalida - $fechaEntrada) / (3600 * 24);
    }

    function LoadUrl($url)
    {
        header("location: " . $url);
    }


    function HandleMultipages($page, $total, $link, $items_per_page = 0, $pagevar = "p")
    {

        if (!$items_per_page)
            $items_per_page = ITEMS_PER_PAGE;

        $pages["items_per_page"] = $items_per_page;

        if (empty($page)) {
            $page = 0;
        }//if

        $pages["start"] = $page * $items_per_page;
        $pages["end"] = $pages["start"] + $items_per_page;
        if ($pages["end"] > $total) {
            $pages["end"] = $total;
        }//if

        if ($total % $items_per_page == 0) {
            $total_pages =(int)($total / $items_per_page - 1);
            if ($total_pages < 0) {
                $total_pages = 0;
            }//if
        }//if
        else {
            $total_pages = (int)($total / $items_per_page);
        }//else

        if ($page > 0) {
            if (!$this->hs_eregi("\|$pagevar\|", $link))
                $pages["prev"] = $link . "/" . $pagevar . "/" . ($page - 1);
            else
                $pages["prev"] = $this->hs_ereg_replace("\|$pagevar\|", (string)($page - 1), $link);
        }//if

        if ($total_pages > 0) {
            if ($total_pages > 15) {
                $start = $page - 7;
                if ($start < 0)
                    $start = 0;
                $end = $start + 15;
                if ($end > $total_pages) {
                    $end = $total_pages;
                    $start = $end - 15;
                }//if
            }//if
            else {
                $start = 0;
                $end = $total_pages;
            }//else
            for ($i = $start; $i <= $end; $i++) {
                if (!$this->hs_eregi("\|$pagevar\|", $link))
                    $pages["numbers"][$i + 1] = $link . "/" . $pagevar . "/" . $i;
                else
                    $pages["numbers"][$i + 1] = $this->hs_ereg_replace("\|$pagevar\|", (string)$i, $link);
            }//for
        }//if

        if ($page < $total_pages) {
            if (!$this->hs_eregi("\|$pagevar\|", $link))
                $pages["next"] = $link . "/" . $pagevar . "/" . ($page + 1);
            else
                $pages["next"] = hs_ereg_replace("\|$pagevar\|", (string)($page + 1), $link);
        }//if

        if ($start > 0) {
            if (!$this->hs_eregi("\|$pagevar\|", $link))
                $pages["first"] = $link . "/" . $pagevar . "/0";
            else
                $pages["first"] = hs_ereg_replace("\|$pagevar\|", "0", $link);
        }

        if ($end < $total_pages) {
            if (!$this->hs_eregi("\|$pagevar\|", $link))
                $pages["last"] = $link . "/" . $pagevar . "/" . $total_pages;
            else
                $pages["last"] = hs_ereg_replace("\|$pagevar\|", (string)($total_pages), $link);
        }

        $pages["current"] = $page + 1;

        return $pages;

    }//handle_multipages

    function hs_eregi($var1, $var2, $var3 = null)
    {

        if (function_exists("mb_eregi"))
            return @mb_eregi($var1, $var2, $var3);
        else
            return @preg_match("/$var1/i", $var2, $var3);

    }//hs_eregi


    function hs_ereg_replace($var1, $var2, $var3)
    {

        if (function_exists("mb_ereg_replace"))
            return mb_ereg_replace($var1, $var2, $var3);
        else
            return preg_replace("/$var1/", $var2, $var3);

    }//hs_ereg_replace

    function SexoString($sex)
    {
        switch ($sex) {
            case "m":
                $sexo = "Masculino";
                break;
            case "f":
                $sexo = "Femenino";
                break;
            default:
                $sexo = "Masculino";
        }
        return $sexo;
    }

    function CalculateIva($price)
    {
        return $price * (IVA / 100);
    }

    function ReturnLang()
    {
        if (!isset($_SESSION['lang'])) {
            $lang = "es";
        } elseif ($_SESSION['lang'] == "es") {
            $lang = "es";
        } else {
            $lang = "en";
        }

        return $lang;
    }

    function FormatOutputText(&$text)
    {
        $text = nl2br($text);
    }

    function SetIp()
    {
        if ($_SERVER) {
            if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif ($_SERVER["HTTP_CLIENT_IP"]) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        return $realip;

    }

    function validateDateFormat($date, $field, $format = 'd-m-Y')
    {
        //match the format of the date
        switch ($format) {
            case 'd-m-Y':
                if (preg_match("/^([0-9]{2})-([0-9]{2})-([0-9]{4})$/", $date, $parts)) {
                    //check weather the date is valid of not
                    if (checkdate($parts[2], $parts[1], $parts[3]))
                        return true;
                    else {
                        $this->setError(10011, "error", "", $field);
                        return false;
                    }
                } else {
                    $this->setError(10011, "error", "", $field);
                    return false;
                }

                break;
            case 'Y-m-d':
                if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
                    //check weather the date is valid of not
                    if (checkdate($parts[2], $parts[3], $parts[1]))
                        return true;
                    else {
                        $this->setError(10011, "error", "", $field);
                        return false;
                    }

                } else {
                    $this->setError(10011, "error", "", $field);
                    return false;
                }

                break;
        }

    }

    function isValidateDate($date, $format = 'd-m-Y')
    {
        //match the format of the date
        switch ($format) {
            case 'd-m-Y':
                if (preg_match("/^([0-9]{2})-([0-9]{2})-([0-9]{4})$/", $date, $parts)) {
                    //check weather the date is valid of not
                    if (checkdate($parts[2], $parts[1], $parts[3]))
                        return true;
                    else
                        return false;
                } else
                    return false;
                break;
            case 'Y-m-d':
                if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
                    //check weather the date is valid of not
                    if (checkdate($parts[2], $parts[3], $parts[1]))
                        return true;
                    else
                        return false;
                } else
                    return false;
                break;
            case 'd/m/Y':
                if (preg_match("/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/", $date, $parts)) {
                    //check weather the date is valid of not
                    if (checkdate($parts[2], $parts[1], $parts[3]))
                        return true;
                    else
                        return false;
                } else
                    return false;
                break;
        }
    }

    function DP($variable)
    {
        echo "<pre>";
        print_r($variable);
        echo "</pre>";
    }

    function CadenaOriginalFormat($cadena, $decimals = 2, $addPipe = true)
    {
        //change tabs, returns and newlines into spaces
        $cadena = utf8_encode(urldecode($cadena));
        $cadena = str_replace("|", "/", $cadena);
        $remove = array("\t", "\n", "\r\n", "\r");
        $cadena = str_replace($remove, ' ', $cadena);
        $cadena = str_replace("+", "MAS", $cadena);
        $cadena = str_replace("&amp;", "&", $cadena);
        $pat[0] = "/^\s+/";
        $pat[1] = "/\s{2,}/";
        $pat[2] = "/\s+\$/";
        $rep[0] = "";
        $rep[1] = "";
        $rep[2] = "";
        $cadena = preg_replace($pat, $rep, $cadena);
        $cadena = @number_format($cadena, $decimals, ".", "");
        if (strlen($cadena) > 0 && $addPipe) {
            $cadena .= "|";
        }
        $cadena = urldecode(utf8_decode($cadena));
        return $cadena = trim($cadena);
    }

    function CadenaOriginalVariableFormat($cadena, $formatNumber = false, $addPipe = true, $dec = 2)
    {
        //change tabs, returns and newlines into spaces
        $cadena = $this->isUtf8Aplly($cadena) ? $cadena : utf8_encode(urldecode($cadena));
        $cadena = str_replace("|", "/", $cadena);
        $remove = array("\t", "\n", "\r\n", "\r");
        $cadena = str_replace($remove, ' ', $cadena);

        $pat[0] = "/^\s+/";
        $pat[1] = "/\s{2,}/";
        $pat[2] = "/\s+\$/";
        $rep[0] = "";
        $rep[1] = " ";
        $rep[2] = "";
        $cadena = preg_replace($pat, $rep, $cadena);

        if ($formatNumber) {
            $cadena = @number_format($cadena, $dec, ".", "");
        }

        if (strlen($cadena) > 0 && $addPipe) {
            $cadena .= "|";
        }
        return $cadena = trim($cadena);
    }

    function CadenaOriginalPDFFormat($cadena, $formatNumber = false, $addPipe = true, $dec = 2)
    {
        //change tabs, returns and newlines into spaces
        $cadena = utf8_encode(urldecode($cadena));
        $cadena = str_replace("|", "/", $cadena);
//		$remove = array("\t", "\n", "\r\n", "\r");
//    $cadena = str_replace($remove, ' ', $cadena);

        $pat[0] = "/^\s+/";
        $pat[1] = "/\s{2,}/";
        $pat[2] = "/\s+\$/";
        $rep[0] = "";
        $rep[1] = " ";
        $rep[2] = "";
        $cadena = preg_replace($pat, $rep, $cadena);

        if ($formatNumber) {
            $cadena = number_format($cadena, $dec, ".", ",");
        }

        if (strlen($cadena) > 0 && $addPipe) {
            $cadena .= "|";
        }
        return $cadena = trim($cadena);
    }

    function DecodeVal($value)
    {

        return urldecode($value);

    }//decodeVal

    //new
    function Wrap($string, $lenght)
    {
        $wrapped = wordwrap($string, $lenght);
        return $wrapped;
    }

    function ShortString($string, $lenght)
    {
        $string = html_entity_decode($string);
        $string = strip_tags($string);
        $wrapped = substr($string, 0, $lenght);
        return $wrapped;
    }

    function DecodeString($string)
    {
        $string = html_entity_decode($string);
        $string = strip_tags($string);
        $string = utf8_encode($string);
        return $string;
    }

    function EncodeRow($row)
    {

        foreach ($row as $key => $val) {
            $info[$key] = utf8_encode($val);
        }

        return $info;

    }

    function EncodeResult($result)
    {

        foreach ($result as $k => $row) {

            foreach ($row as $key => $val) {
                $info[$key] = utf8_encode($val);
            }

            $card[$k] = $info;

        }

        return $card;

    }

    function DecodeRow($row)
    {

        foreach ($row as $key => $val) {
            $info[$key] = utf8_decode($val);
        }

        return $info;

    }

    function DecodeResult($result)
    {

        foreach ($result as $k => $row) {

            foreach ($row as $key => $val) {
                $info[$key] = $this->isUtf8Aplly($val) ? $val : utf8_decode($val);
            }

            $card[$k] = $info;

        }

        return $card;

    }

    function GetHours()
    {

        for ($k = 0; $k <= 23; $k++) {
            if ($k <= 9)
                $val = '0' . $k;
            else
                $val = $k;

            $card['value'] = $k;
            $card['name'] = $val;

            $hours[$k] = $card;
        }

        return $hours;

    }

    function GetMinutes()
    {

        for ($k = 0; $k <= 59; $k++) {
            if ($k <= 9)
                $val = '0' . $k;
            else
                $val = $k;

            $card['value'] = $k;
            $card['name'] = $val;

            $minutes[$k] = $card;
        }

        return $minutes;

    }

    function EnumerateStates()
    {

        $sql = "SELECT
					*
				FROM
					state
				ORDER BY
					name ASC";

        $this->Util()->DB()->setQuery($sql);
        $result = $this->Util()->DB()->GetResult();

        return $result;

    }

    function GetClaveState($stateId)
    {

        $sql = "SELECT
					clave
				FROM
					state
				WHERE
					stateId = " . $stateId;

        $this->Util()->DB()->setQuery($sql);
        $clave = $this->Util()->DB()->GetSingle();

        return $clave;

    }

    function GetDayByKey($key)
    {

        $days = array('', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');

        return $days[$key];

    }

    function GetMonthByKey($key)
    {

        $months = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio',
            'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

        return $months[$key];

    }

    function getMonthsList()
    {

        for ($k = 1; $k <= 12; $k++) {
            $months[$k] = $this->getMonthByKey($k);
        }//for

        return $months;

    }//getMonthsList

    function getDaysList()
    {

        for ($k = 1; $k <= 31; $k++)
            $days[$k] = $k;

        return $days;

    }//getDaysList

    function ValidateDate($year, $month, $day)
    {
        //validamos que sean numericos
        if (!is_numeric($year) || !is_numeric($month) || !is_numeric($day))
            return false;
        //validamos el dia
        if ($day < 1 || $day > 31)
            return false;
        //validamos el mes
        if ($month < 1 || $month > 12)
            return false;

        //echo '<br/>*** dia: ' . $day . ', mes: ' . $month . ' ***<br/>';

        //todos los meses tienen 28 dias, si es asi es una fecha valida
        if ($day <= 28)
            return true;

        //todos los meses excepto febrero (si no es bisiesto) tienen 29 dias, es una fecha valida
        if ($day == 29 && $month != 2)
            return true;

        //si el dia es 29 y el mes es febrero, validar solo si es a�o bisiesto
        if ($day == 29 && $month == 2 &&
            (($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0)))) {
            //dia 29, mes Feb, a�o es bisiesto, fecha valida
            return true;
        }
        if ($day == 29 && $month == 2 &&
            !(($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0)))) {
            //dia 29, mes Feb, A�o no es bisiesto, fecha no valida
            return false;
        }
        // si el dia es mayor a 29 y es febrero, es fecha no valida
        if (($day > 29) && ($month == 2))
            return false;

        //si el dia es 31, y el mes es Abr, Jun, Sep y Nov, la fecha no es valida (solo tienen 30 dias)
        if (($day > 30) && ($month == 4 || $month == 6 || $month == 9 || $month == 11))
            return false;

        //si el dia es 30 todos los meses tienen 30, excepto febreo que ya se evaluo antes...
        return true;

    }

    function GetYearsOld($date)
    {

        list($dia, $mes, $anio) = explode("-", $date);

        $anio_dif = date("Y") - $anio;
        $mes_dif = date("m") - $mes;
        $dia_dif = date("d") - $dia;

        if ($dia_dif < 0 || $mes_dif < 0)
            $anio_dif--;

        return $anio_dif;

    }

    function orderMultiDimensionalArray(array $toOrderArray, $field, $inverse = false, $sameKey = false)
    {

        $position = array();
        $newRow = array();
        foreach ($toOrderArray as $key => $row) {
            $position[$key] = $row[$field];
            $newRow[$key] = $row;
        }
        if ($inverse) {
            arsort($position);
        } else {
            asort($position);
        }
        $returnArray = array();
        foreach ($position as $key => $pos) {
            if ($sameKey)
                $returnArray[$key] = $newRow[$key];
            else
                $returnArray[] = $newRow[$key];
        }
        return $returnArray;

    }

    function GetNextAutoIncrement($table)
    {

        $sql = 'SHOW TABLE STATUS LIKE "' . $table . '"';
        $this->Util()->DB()->setQuery($sql);
        $row = $this->Util()->DB()->GetRow();

        return $row['Auto_increment'];

    }

    function EnumTiposComprobante()
    {

        $sql = 'SELECT * FROM tiposComprobante WHERE tiposComprobanteId = 1 ORDER BY nombre ASC';
        $this->Util()->DB()->setQuery($sql);
        $tipos = $this->Util()->DB()->GetResult();

        return $tipos;
    }

    function PadStringLeft($input, $chars, $char)
    {
        return str_pad($input, $chars, $char, STR_PAD_LEFT);
    }

    function CalculateEndDateUp($startDate, $periodo)
    {

        $startDate = date('Y-m-d', strtotime($startDate));

        $timeStartDate = strtotime($startDate);

        if ($periodo == 'mensual') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate) + 1, date('d', $timeStartDate), date('Y', $timeStartDate));
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'bimestral') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate) + 2, date('d', $timeStartDate), date('Y', $timeStartDate));
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'trimestral') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate) + 3, date('d', $timeStartDate), date('Y', $timeStartDate));
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'semestral') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate) + 6, date('d', $timeStartDate), date('Y', $timeStartDate));
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'anual') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate), date('d', $timeStartDate), date('Y', $timeStartDate) + 1);
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'bianual') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate), date('d', $timeStartDate), date('Y', $timeStartDate) + 2);
            $endDate = date('Y-m-d', $endDate);
        }

        return $endDate;

    }//CalculateEndDateUp

    function CalculateEndDateDown($startDate, $periodo)
    {

        $startDate = date('Y-m-d', strtotime($startDate));

        $timeStartDate = strtotime($startDate);

        if ($periodo == 'mensual') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate) - 1, date('d', $timeStartDate), date('Y', $timeStartDate));
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'bimestral') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate) - 2, date('d', $timeStartDate), date('Y', $timeStartDate));
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'trimestral') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate) - 3, date('d', $timeStartDate), date('Y', $timeStartDate));
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'semestral') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate) - 6, date('d', $timeStartDate), date('Y', $timeStartDate));
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'anual') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate), date('d', $timeStartDate), date('Y', $timeStartDate) - 1);
            $endDate = date('Y-m-d', $endDate);
        } elseif ($periodo == 'bianual') {
            $endDate = mktime(0, 0, 0, date('m', $timeStartDate), date('d', $timeStartDate), date('Y', $timeStartDate) - 2);
            $endDate = date('Y-m-d', $endDate);
        }

        return $endDate;

    }//CalculateEndDateDown

    function ChangeDateFormat($fecha)
    {

        $sD = explode('-', $fecha);
        $mes = $this->GetMonthByKey(intval($sD[1]));
        $fecha = $sD[2] . ' ' . substr($mes, 0, 3) . ' ' . $sD[0];

        return $fecha;

    }//ChangeDateFormat

    function GetStatusByDate($fecha, $recibido = 0)
    {

        $status = 'Futuro';
        if ($recibido)
            $status = 'Entregado';
        elseif ($fecha == '')
            $status = '';
        elseif ($fecha < date('Y-m-d'))
            $status = 'Retrasado';

        //Si faltan 2 semanas para la entrega
        elseif ($fecha <= date('Y-m-d', strtotime('+2 week')))
            $status = 'Proximo';

        return $status;

    }//GetStatusByDate

    function GetContCatName($contCatId)
    {

        $sql = 'SELECT name FROM contract_category WHERE contCatId = ' . $contCatId;
        $this->Util()->DB()->setQuery($sql);
        $category = $this->Util()->DB()->GetSingle();

        return $category;

    }//GetContCatName

    function GetContSubCatName($contSubcatId)
    {

        $sql = 'SELECT name FROM contract_subcategory WHERE contSubcatId = ' . $contSubcatId;
        $this->Util()->DB()->setQuery($sql);
        $subcategory = $this->Util()->DB()->GetSingle();

        return $subcategory;

    }//GetContSubCatName

    function setFormatDate($fecha)
    {

        $mesEnt = $this->GetMonthByKey(date('n', strtotime($fecha)));
        //$mesEnt = substr($mesEnt,0,3);
        $fecha = date('d', strtotime($fecha)) . ' ' . strtoupper($mesEnt) . ' ' . date('Y', strtotime($fecha));

        return $fecha;

    }//setFormatDate


    function DecodeUrlRow($row)
    {

        foreach ($row as $key => $val) {
            $info[$key] = urldecode($val);
        }

        return $info;

    }


    function DecodeUrlResult($result)
    {

        if (!$result)
            return;

        foreach ($result as $k => $row) {

            foreach ($row as $key => $val) {
                $info[$key] = urldecode($val);
            }

            $card[$k] = $info;

        }

        return $card;

    }


    function Unzip($path, $file)
    {
        $zip = new ZipArchive;
        $res = $zip->open($file);
        if ($res === true) {
            $zip->extractTo($path);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    function Zip($path, $file)
    {
        $zip = new ZipArchive();
        $res = $zip->open($path . $file . ".zip", ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
        if ($res === TRUE) {
            $zip->addFile($path . $file . ".xml", $file . ".xml");
            $zip->close();
        }

        return file_exists($path . $file);
    }

    function ZipTasks($zipPath, $files)
    {
        $zip = new ZipArchive();
        if (is_file($zipPath))
            unlink($zipPath);
        $res = $zip->open($zipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
        if ($res === TRUE) {
            foreach ($files as $key => $file) {
                $this->InsertFileIntoZip($zip, $file);
            }
            $zip->close();
        }

        return file_exists($zipPath);
    }

    function InsertFileIntoZip($objZip, $item)
    {
        global $monthsComplete;
        $dateExploded = explode("-", $item["date"]);
        $anio = $dateExploded[0];
        $month = strtolower($monthsComplete[$dateExploded[1]]);
        $nombreServicio = strtolower(str_replace(" ", "", trim($item["nombreServicio"])));
        $this->Util()->DB()->setQuery("select nombreStep from step where stepId='" . $item["stepId"] . "' ");
        $nombreStep = $this->Util()->DB()->GetSingle();
        $nombreStep = strtolower(str_replace(" ", "", trim($nombreStep)));
        $rutaEnzip = $anio . "/" . $nombreServicio . "/" . $month . "/" . $nombreStep . "/";

        if (is_file(DOC_ROOT . "/tasks" . $item["ruta"]) && file_exists(DOC_ROOT . "/tasks" . $item["ruta"])) {
            $attachment = DOC_ROOT . "/tasks" . $item["ruta"];
            $name = end(explode("/", $item["ruta"]));
        } else {
            $name = $item["servicioId"] . "_" . $item["stepId"] . "_" . $item["taskId"] . "_" . $item["control"] . "_" . $item["version"] . "." . $item["ext"];
            $attachment = DOC_ROOT . "/tasks/" . $name;
        }
        if (file_exists($attachment)) {
            $objZip->addFile($attachment, $rutaEnzip . $name);
        }

    }

    function ExplodeEmails($email)
    {
        $email = trim($email);
        $email = str_replace("/", ",", $email);
        $email = str_replace(" ", ",", $email);
        $email = str_replace("|", ",", $email);
        $email = str_replace(";", ",", $email);
        $email = explode(",", $email);

        foreach ($email as $key => $value) {
            if (!$value || !$this->ValidateEmail($value) || (stripos(trim($value), 'braunhuerin.com.mx') && $value != 'vane@braunhuerin.com.mx')) {
                unset($email[$key]);
            }
        }
        return $email;
    }

    function GetMesDiagonal($fecha)
    {
        if (!empty($fecha)) //	2013/12/31----->31/Dic/2013
        {
            $date = explode("/", $fecha);
            $mesArray = array("00", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
            $fechaMes = $date[2] . "/" . $mesArray[(int)$date[1]] . "/" . $date[0];
            //return $mesArray[$mes];
            return $fechaMes;
        }
    }

    function GetMesGuion($fecha)
    {
        if (!empty($fecha)) //	2013/12/31----->31/Dic/2013
        {
            $date = explode("-", $fecha);
            $mesArray = array("00", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
            $fechaMes = $date[2] . "-" . $mesArray[(int)$date[1]] . "-" . $date[0];
            //return $mesArray[$mes];
            return $fechaMes;
        }
    }

    function MesCorto($numMes)
    {
        $mesArray = array("00", "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
        return $mesArray[($numMes * 1)];
    }

    function ConvertToLineal($array = array(), $field)
    {
        $newArray = array();
        if (!is_array($array))
            $array = [];

        foreach ($array as $k => $val)
            if ($field == 'email') {
                if ($this->Util()->ValidateEmail($val[$field]))
                    $newArray[] = $val[$field];
            } else {
                $newArray[] = $val[$field];
            }


        return $newArray;
    }

    function getLastDayMonth($anio, $mes)
    {
        return date("d", (mktime(0, 0, 0, $mes + 1, 1, $anio) - 1));
    }

    function getFirstDate($date)
    {
        $fecha = explode('-', $date);
        $month = $fecha[1];
        $year = $fecha[0];
        $day = date("d", mktime(0, 0, 0, $month, 1, $year));
        return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
    }

    public function inicio_fin_semana($fecha)
    {

        $diaInicio = "Monday";
        $diaFin = "Sunday";

        $strFecha = strtotime($fecha);

        $fechaInicio = date('Y-m-d', strtotime('last ' . $diaInicio, $strFecha));
        $fechaFin = date('Y-m-d', strtotime('next ' . $diaFin, $strFecha));

        if (date("l", $strFecha) == $diaInicio) {
            $fechaInicio = date("Y-m-d", $strFecha);
        }
        if (date("l", $strFecha) == $diaFin) {
            $fechaFin = date("Y-m-d", $strFecha);
        }
        return array("fechaInicio" => $fechaInicio, "fechaFin" => $fechaFin);
    }

    function generateRandomString($length = 10, $upper = false)
    {
        if ($upper)
            return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
        else
            return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    function delete_files($target, $tipo = "*")
    {
        if (is_dir($target)) {
            $files = glob($target . '' . $tipo, GLOB_MARK); //
            foreach ($files as $file) {
                $file = str_replace("/", "/", $file);
                $this->delete_files($file, "*");
            }
            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }

    public function Smarty()
    {
        if ($this->smarty == null) {
            $this->smarty = new Smarty();
        }
        return $this->smarty;
    }

    function erase_val(&$myarr)
    {
        $myarr = array_map(function () {
        }, $myarr);
    }

    function getEnumValues($table, $field)
    {
        $this->Util()->DB()->setQuery("SHOW COLUMNS FROM $table WHERE Field ='$field'");
        $columns = $this->Util()->DB()->GetRow();
        preg_match("/^enum\(\'(.*)\'\)$/", $columns['Type'], $fields);
        $fields = str_replace("'", "", $fields[1]);
        $fields = explode(",", $fields);
        return $fields;

    }

    function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    function is_cli()
    {
        if (defined('STDIN')) {
            return true;
        }
        if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
            return true;
        }
        return false;
    }

    function validateRfc($var)
    {
        if ($var == "123123123123")
            return false;
        if (!$var)
            return false;
        if (strlen($var) < 12)
            return false;

        return true;
    }

    function ConvertSerial($serial)
    {
        $arreglo = str_split($serial, 2);
        foreach ($arreglo as $key => $value) {
            $str .= $value - 30;
        }
        return $str;
    }

    function GetNoCertificado($ruta_dir, $nom_certificado)
    {
        $serial = exec('openssl x509 -noout -in ' . $ruta_dir . '/' . $nom_certificado . '.cer.pem -serial');
        $ser = explode('=', $serial);
        $serial = $ser[1];

        $noCertificado = $this->ConvertSerial($serial);

        return $noCertificado;
    }

    function changeKeyArray(array $elements, $key)
    {
        $data = [];
        if (!is_array($elements) || count($elements) <= 0)
            return $data;

        foreach ($elements as $var)
            $data[$var[$key]] = $var;

        return $data;
    }

    function isJson($str)
    {
        $json = json_decode($str);
        return $json && $str != $json;
    }

    function rollbackTable($table, $field, $ids)
    {
        if (!$table || !$field || !$ids)
            return false;
        $operator = is_array($ids) ? "in (" . implode(',', $ids) . ")" : "='" . $ids . "'";
        $sql = "delete from " . $table . " where  " . $field . $operator;
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->DeleteData();
    }

    function createOrReplaceView($nameView, $select, $custom_fields = [])
    {
        if (!$nameView || !$select)
            return false;
        $sql = "DROP VIEW IF EXISTS ".$nameView;
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->ExecuteQuery();
        $this->Util()->DB()->CleanQuery();

        $sql = "DROP TABLE IF EXISTS ".$nameView;
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->ExecuteQuery();
        $this->Util()->DB()->CleanQuery();

        $custom_name_fields = count($custom_fields) ? "(" . implode(',', $custom_fields) . ")" : "";
        $sql = "create or replace view " . $nameView . $custom_name_fields . " as " . $select;
        $this->Util()->DB()->setQuery($sql);
        $this->Util()->DB()->ExecuteQuery();
        $this->Util()->DB()->CleanQuery();
    }

    function generateMonthUntil($max_month, $array = true)
    {
        $base = [];
        $until = $max_month > 12 || $max_month <=0 ? 12 : (int) $max_month;
        for ($ii = 1; $ii <= $until; $ii++) {
            $base[$ii] = $array ? [] : $ii;
        }
        return $base;
    }
    function generateMonthByPeriod($period, $array = true)
    {
        $base = [];
        switch ($period) {
            case 'efm':
                $init = 1;
                $until = 3;
                break;
            case 'amj':
                $init = 4;
                $until = 6;
                break;
            case 'jas':
                $init = 7;
                $until = 9;
                break;
            case 'ond':
                $init = 10;
                $until = 12;
                break;
            default:
                $init = 1;
                $until = 12;
                break;
        }
        for ($ii = $init; $ii <= $until; $ii++) {
            $base[$ii] = $array ? [] : $ii;
        }

        return $base;
    }

    function getNumMesPorPeriodicidad($periodicidad) {

        switch ($periodicidad) {
            case "Mensual":
                return 1;
            case "Bimestral":
                return 2;
            case "Trimestral":
               return 3;
            case "Cuatrimestral":
              return 4;
            case "Semestral":
               return 6;
            case "Anual":
               return 7;
            default: return 0;
        }
    }

    function listMonthHeaderForReport($max_month)
    {
        global $monthsInt;
        $base = [];
        $until = $max_month > 12 || $max_month <=0 ? 12 : (int) $max_month;
        for ($ii = 1; $ii <= $until; $ii++) {
            $base[] = $monthsInt[$ii];
        }

        return $base;
    }

    function listMonthCompleteHeaderForReport($max_month)
    {
        global $monthsIntComplete;
        $base = [];
        $until = $max_month > 12 || $max_month <=0 ? 12 : (int) $max_month;
        for ($ii = 1; $ii <= $until; $ii++) {
            $base[] = $monthsIntComplete[$ii];
        }

        return $base;
    }

    function cleanString($string)
    {

        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        $string = str_replace(
            array("\\", "¨", "º", "~",
                "#", "@", "|", "!", "\"",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", "¡",
                "¿", "[", "^", "`", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":",
                ".", "'", '"', '“', '”'),
            '',
            $string
        );
        return $string;
    }
    function normalizeString($string) {
        return addslashes($string);
    }

    function csvToJson($fname, $customKey = [], $ignoreKey = []) {
        if (!($fp = fopen($fname, 'r'))) {
            die("Can't open file...");
        }

        $key = is_array($customKey) && count($customKey)
            ? $customKey
            : fgetcsv($fp,"1024",",");

        $json = array();
        while ($row = fgetcsv($fp,"1024",",")) {
            $current = array_combine($key, $row);
            foreach($ignoreKey as $ignore)
                unset($current[$ignore]);

            $json[] = $current;
        }
        fclose($fp);
        return json_encode($json,JSON_UNESCAPED_SLASHES );
    }

    function checkUtf8($str){
        if( mb_detect_encoding($str,"UTF-8, ISO-8859-1")!="UTF-8" ){
            return  utf8_encode($str);
        }
        else{
            return $str;
        }

    }

    function isUtf8Aplly($str) {
        return mb_detect_encoding($str,"UTF-8, ISO-8859-1") == "UTF-8";
    }
}

?>
