<?php

class Sello extends Comprobante
{

    function generar($cadenaOriginal, $md5)
    {
        $root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/";

        if(!is_dir($root))
        {
            mkdir($root, 0777);
        }


        $data["certificado"] = $arr['noCertificado'];
        $rfcActivo = $this->getRfcActive();
        $root = DOC_ROOT."/empresas/".$_SESSION["empresaId"]."/certificados/".$rfcActivo."/";

        if(!is_dir($root))
        {
            mkdir($root, 0777);
        }

        if ($handle = opendir($root))
        {
            while (false !== ($file = readdir($handle)))
            {
                $ext = substr($file, -7);
                if($ext == "cer.pem")
                {
                    $cert = $file;
                }

                if($ext == "key.pem")
                {
                    $key = $file;
                }
            }
        }

        closedir($handle);

        $file=$root.$key;      // Ruta al archivo
        //write md5 to txt
        $fp = fopen ($root."md5.txt", "w+");
        fwrite($fp, $md5);
        fclose($fp);


        //sign the original md5 with private key
        exec("openssl dgst -sha256 -sign ".$file." -out ".$root."/md5sha1.txt ".$root."/md5.txt");

        $myFile = $root."/md5sha1.txt";
        $fh = fopen($myFile, 'r');
        $theData = fread($fh, filesize($myFile));
        fclose($fh);
        //generate public
        exec("openssl rsa -in ".$file." -pubout -out ".$root."/publickey.txt");

        //verify
        exec("openssl dgst -sha256 -verify ".$root."/publickey.txt -out ".$root."/verified.txt -signature ".$root."/md5sha1.txt ".$root."/md5.txt");

        //TODO recordar este que se le quito el utf8
        $cadenaOriginalDecoded = $cadenaOriginal;

        $file = $root.$cert;      // Ruta al archivo
        $datos = file($file);
        $data["certificado"] = "";
        $carga=false;
        for ($i=0; $i<sizeof($datos); $i++)
        {
            if (strstr($datos[$i],"END CERTIFICATE")) $carga=false;
            if ($carga)
            {
                $data["certificado"] .= trim($datos[$i]);

            }
            if (strstr($datos[$i],"BEGIN CERTIFICATE")) $carga=true;
        }
        $keyFile = $root.$key;
        $pkeyid = openssl_get_privatekey(file_get_contents($root.$key));
//		openssl_sign($cadenaOriginal, $crypttext, $pkeyid, OPENSSL_ALGO_SHA1);
        openssl_sign($cadenaOriginalDecoded, $crypttext, $pkeyid, OPENSSL_ALGO_SHA256);

        $data["sello"] = base64_encode($crypttext);

        return $data;
    }
}


?>