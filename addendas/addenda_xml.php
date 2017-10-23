<?php
//echo $data["fromAddenda"];
				$nufa = "SIGN_".$empresa["empresaId"]."_".$serie["serie"]."_".$data["folio"];
				$realSignedXml = $root.$nufa.".xml";

switch($data["fromAddenda"])
{
	case "Continental":
				include_once(DOC_ROOT."/addendas/generate_xml_continental.php");
				break;		
	case "Pepsico":
				include_once(DOC_ROOT."/addendas/generate_xml_pepsico.php");
				break;		
	case "Zepto":
				include_once(DOC_ROOT."/addendas/generate_xml_zepto.php");
				break;		
}


				$fh = fopen($realSignedXml, 'r');
				$theData = fread($fh, filesize($realSignedXml));
				fclose($fh);
				$theData = str_replace("</cfdi:Complemento>", "</cfdi:Complemento>".$strAddenda, $theData);
				
		
				$fh = fopen($realSignedXml, 'w') or die("can't open file");
				fwrite($fh, $theData);
				fclose($fh);

?>