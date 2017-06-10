<?php 

include_once('init.php');
include_once('config.php');
include_once(DOC_ROOT.'/libraries.php');

class Trazzos
{
}

class Module extends Trazzos
{
	public $module;
	public $table;
	public $fields;
	public $inheritesFrom;
	public $modulePlural;
	public $classe;
	
	function GenerateClass()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "<?php \r\n
class ".ucfirst($this->name)."";
		if($this->inheritesFrom)
		{
			$str.=" extends ".$this->inheritesFrom;
		}
		
		$str .= "\r\n";
		$str .= "{\r\n";
		foreach($fields as $field)
		{
			$str.= "\tprivate $".$field["Field"].";\r\n";
		}
		
		$str .= "\r\n";
		
		foreach($fields as $field)
		{
			$fieldName = $field["Field"];
			
			$str.= "\tpublic function set".ucfirst($field["Field"])."(\$value)\r\n";
			$str.= "\t{\r\n";
			//validation
			$type = explode("(", $field["Type"]);
			
			switch($type[0])
			{
				//validate integer
				case "tinyint":
				case "smallint":
				case "mediumint":
				case "int":
				case "bigint": 	$str .="\t\t\$this->Util()->ValidateInteger(\$value);\r\n";break;
				//validate float
				case "decimal":
				case "float":
				case "double":
				case "real": $str .="\t\t\$this->Util()->ValidateFloat(\$value, 6);\r\n";break;
				//no validation
				case "enum": break;
				//default string validation
				default: $str .="\t\t\$this->Util()->ValidateString(\$value, 10000, 1, '".$fieldName."');\r\n";
			}
			$str.= "\t\t\$this->".$fieldName." = \$value;\r\n";
			$str.= "\t}\r\n";
			$str.= "\r\n";
			$str.= "\tpublic function get".ucfirst($field["Field"])."()\r\n";
			$str.= "\t{\r\n";
			$str.= "\t\treturn \$this->".$fieldName.";\r\n";
			$str.= "\t}\r\n";
			$str.= "\r\n";
		}
		$str.= "\tpublic function Enumerate()\r\n";
		$str.= "\t{\r\n";
		$str.= "\t\t\$this->Util()->DB()->setQuery('SELECT COUNT(*) FROM ".$this->table."');\r\n";
		$str.= "\t\t\$total = \$this->Util()->DB()->GetSingle();\r\n\r\n";
		
		$str.= "\t\t\$pages = \$this->Util->HandleMultipages(\$this->page, \$total ,WEB_ROOT.\"/".$this->module."\");\r\n\r\n";
		$str.= "\t\t\$sql_add = \"LIMIT \".\$pages[\"start\"].\", \".\$pages[\"items_per_page\"];\r\n";
		
		$str.= "\t\t\$this->Util()->DB()->setQuery('SELECT * FROM ".$this->table." ORDER BY ".$primaryKey." ASC '.\$sql_add);\r\n";
		$str.= "\t\t\$result = \$this->Util()->DB()->GetResult();\r\n\r\n";
		$str.= "\t\tforeach(\$result as \$key => \$row)\r\n";
		$str.= "\t\t{\r\n";
		$str.= "\t\t}\r\n";
		
		$str.= "\t\t\$data[\"items\"] = \$result;\r\n";
		$str.= "\t\t\$data[\"pages\"] = \$pages;\r\n";
		
		$str.= "\t\treturn \$data;\r\n";
		$str.= "\t}\r\n";
		$str.= "\r\n";

		$str.= "\tpublic function Info()\r\n";
		$str.= "\t{\r\n";
		$str.= "\t\t\$this->Util()->DB()->setQuery(\"SELECT * FROM ".$this->table." WHERE ".$primaryKey." = '\".\$this->".$primaryKey.".\"'\");\r\n";
		$str.= "\t\t\$row = \$this->Util()->DB()->GetRow();\r\n";
		$str.= "\t\treturn \$row;\r\n";
		$str.= "\t}\r\n";
		$str.= "\r\n";

		$str.= "\tpublic function Edit()\r\n";
		$str.= "\t{\r\n";
		$str.= "\t\tif(\$this->Util()->PrintErrors()){ return false; }\r\n";
		$str.= "\r\n";
		$str.= "\t\t\$this->Util()->DB()->setQuery(\"\r\n";;
		$str.= "\t\t\tUPDATE\r\n"; 
		$str.= "\t\t\t\t".$this->table."\r\n"; 
		$str.= "\t\t\tSET\r\n"; 
		//fields
		$ii = 0;
		foreach($fields as $field)
		{
			if($ii < $totalFields-1)
			{
				$str.= "\t\t\t\t`".$field["Field"]."` = '\".\$this->".$field["Field"].".\"',\r\n";
			}
			else
			{
				$str.= "\t\t\t\t`".$field["Field"]."` = '\".\$this->".$field["Field"].".\"'\r\n";
			}
			$ii++;
		}
		$str.= "\t\t\tWHERE ".$primaryKey." = '\".\$this->".$primaryKey.".\"'\");\r\n";
		$str.= "\t\t\$this->Util()->DB()->UpdateData();\r\n";
		$str.= "\r\n";
		$str.= "\t\t\$this->Util()->setError(1, \"complete\");\r\n";
		$str.= "\t\t\$this->Util()->PrintErrors();\r\n";
		$str.= "\t\treturn true;\r\n";
		$str.= "\t}\r\n";
		$str.= "\r\n";

		$str.= "\tpublic function Save()\r\n";
		$str.= "\t{\r\n";
		$str.= "\t\tif(\$this->Util()->PrintErrors()){ return false; }\r\n";
		$str.= "\r\n";
		$str.= "\t\t\$this->Util()->DB()->setQuery(\"\r\n";;
		$str.= "\t\t\tINSERT INTO\r\n"; 
		$str.= "\t\t\t\t".$this->table."\r\n"; 
		$str.= "\t\t\t(\r\n"; 

		$ii = 0;
		foreach($fields as $field)
		{
			if($ii < $totalFields-1)
			{
				$str.= "\t\t\t\t`".$field["Field"]."`,\r\n";
			}
			else
			{
				$str.= "\t\t\t\t`".$field["Field"]."`\r\n";
			}
			$ii++;
		}
		$str.= "\t\t)\r\n"; 
		$str.= "\t\tVALUES\r\n"; 
		$str.= "\t\t(\r\n"; 

		$ii = 0;
		foreach($fields as $field)
		{
			if($ii < $totalFields-1)
			{
				$str.= "\t\t\t\t'\".\$this->".$field["Field"].".\"',\r\n";
			}
			else
			{
				$str.= "\t\t\t\t'\".\$this->".$field["Field"].".\"'\r\n";
			}
			$ii++;
		}
		$str.= "\t\t);\");\r\n"; 
		$str.= "\t\t\$this->Util()->DB()->InsertData();\r\n";

		$str.= "\t\t\$this->Util()->setError(1, \"complete\");\r\n";
		$str.= "\t\t\$this->Util()->PrintErrors();\r\n";
		$str.= "\t\treturn true;\r\n";
		$str.= "\t}\r\n";
		$str.= "\r\n";

		$str.= "\tpublic function Delete()\r\n";
		$str.= "\t{\r\n";
		$str.= "\t\tif(\$this->Util()->PrintErrors()){ return false; }\r\n";
		$str.= "\r\n";
		$str.= "\t\t\$this->Util()->DB()->setQuery(\"\r\n";;
		$str.= "\t\t\tDELETE FROM\r\n"; 
		$str.= "\t\t\t\t".$this->table."\r\n"; 
		$str.= "\t\t\tWHERE\r\n"; 
		$str.= "\t\t\t\t".$primaryKey." = '\".\$this->".$primaryKey.".\"'\");\r\n";
		$str.= "\t\t\$this->Util()->DB()->DeleteData();\r\n";

		$str.= "\t\t\$this->Util()->setError(1, \"complete\");\r\n";
		$str.= "\t\t\$this->Util()->PrintErrors();\r\n";
		$str.= "\t\treturn true;\r\n";
		$str.= "\t}\r\n";
		$str.= "\r\n";
		$str.= "}\r\n";
		$str.= "\r\n";

		$str .= "?>";
		
		$myFile = DOC_ROOT."/classes/".$this->classe.".class.php";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		
	}

	function GenerateModule()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "<?php\r\n";
		$str .= "\r\n";
		$str .= "\$".$this->classe."->SetPage(\$_GET[\"p\"]);\r\n";
		$str .= "\$".$this->modulePlural." = \$".$this->classe."->Enumerate();\r\n";
		$str .= "\$smarty->assign(\"".$this->modulePlural."\", $".$this->modulePlural.");\r\n";
		
		$str .= "?>";
		
		$myFile = DOC_ROOT."/modules/".$this->module.".php";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		
	}

	function GenerateMainTemplate()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "<div align=\"center\">\r\n";
		$str .= "\t<span id=\"add".$this->name."\" style=\"cursor:pointer\"><img src=\"{\$WEB_ROOT}/images/addn.png\" border=\"0\" />Agregar ".$this->name."</span>\r\n";
		$str .= "</div>\r\n";
		$str .= "<div id=\"content\">\r\n";
		$str .= "\t{include file=\"lists/".$this->module.".tpl\"}\r\n";		
		$str .= "</div>\r\n";

		$myFile = DOC_ROOT."/templates/".$this->module.".tpl";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		
	}
	
	function GenerateLists()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "<table id=\"taskSheet\" cellpadding=\"2\" cellspacing=\"1\" border=\"0\" class=\"sheet\" width=\"100%\">\r\n";
		$str .= "\t{include file=\"{\$DOC_ROOT}/templates/items/".$this->module."-header.tpl\"}\r\n";
		$str .= "<tbody>\r\n";
		$str .= "\t{include file=\"{\$DOC_ROOT}/templates/items/".$this->module."-base.tpl\"}\r\n";
		$str .= "</tbody>\r\n";
		$str .= "</table>\r\n";
		$str .= "\t{if count(\$".$this->modulePlural.".pages)}\r\n";
		$str .= "\t{include file=\"{\$DOC_ROOT}/templates/lists/pages.tpl\" pages=\$".$this->modulePlural.".pages}\r\n";
		$str .= "\t{/if}\r\n";
		
		$myFile = DOC_ROOT."/templates/lists/".$this->module.".tpl";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		
		
	}

	function Generateitems()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "{foreach from=\$".$this->modulePlural.".items item=item key=key}\r\n";
		$str .= "\t<tr id=\"1\">\r\n";
		foreach($fields as $field)
		{
			$str.= "\t\t<td align=\"center\"";
			if($field["Field"] == $primaryKey)
			{
				$str.=" class=\"id\"";
			}
			$str.= ">{\$item.".$field["Field"]."}</td>\r\n";
		}
		$str.= "\t\t<td class=\"act\">\r\n";
		$str.= "\t\t\t<img src=\"{\$WEB_ROOT}/images/b_dele.png\" class=\"spanDelete\" id=\"{\$item.".$primaryKey."}\"/></span> <img src=\"{\$WEB_ROOT}/images/b_edit.png\" class=\"spanEdit\" id=\"{\$item.".$primaryKey."}\"/></a>\r\n";
		$str.= "\t\t</td>\r\n";
		$str .= "\t</tr>\r\n";
		$str .= "{/foreach}\r\n";

		$myFile = DOC_ROOT."/templates/items/".$this->module."-base.tpl";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		

		$str="";
		$str .= "<thead>\r\n";
		$str .= "\t<tr>\r\n";
		foreach($fields as $field)
		{
			$str.= "\t\t<th width=\"100\"><div id=\"ord".ucfirst($field["Field"])."\" style=\"cursor:pointer\">".ucfirst($field["Field"])."</div></th>\r\n";
		}
		$str.= "\t\t<th></th>\r\n";
		$str .= "\t</tr>\r\n";
		$str .= "</thead>\r\n";

		$myFile = DOC_ROOT."/templates/items/".$this->module."-header.tpl";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		

	}

	function GenerateJavascript()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "Event.observe(window, 'load', function() {\r\n";
		$str .= "\tEvent.observe(\$('add".$this->name."'), \"click\", Add".$this->name."Div);\r\n";
		$str.= "\r\n";
		$str .= "\tAddEdit".$this->name."Listeners = function(e) {\r\n";
		$str .= "\t\tvar el = e.element();\r\n";
		$str .= "\t\tvar del = el.hasClassName('spanDelete');\r\n";
		$str .= "\t\tvar id = el.identify();\r\n";
		$str .= "\t\tif(del == true)\r\n";
		$str .= "\t\t{\r\n";
		$str .= "\t\t\tDelete".$this->name."Popup(id);\r\n";
		$str .= "\t\t\treturn;\r\n";
		$str .= "\t\t}\r\n";
		$str.= "\r\n";
		$str .= "\t\tdel = el.hasClassName('spanEdit');\r\n";
		$str .= "\t\tif(del == true)\r\n";
		$str .= "\t\t{\r\n";
		$str .= "\t\t\tEdit".$this->name."Popup(id);\r\n";
		$str .= "\t\t}\r\n";
		$str .= "\t}\r\n";
		$str .= "\r\n";
		$str .= "\t\$('content').observe(\"click\", AddEdit".$this->name."Listeners);\r\n";
		$str .= "\r\n";
		$str .= "});\r\n";
		$str .= "\r\n";
		
		$str .= "function Edit".$this->name."Popup(id)\r\n";
		$str .= "{\r\n";
		$str .= "\tgrayOut(true);\r\n";
		$str .= "\t\$('fview').show();\r\n";
		$str .= "\tif(id == 0)\r\n";
		$str .= "\t{\r\n";
		$str .= "\t\t\$('fview').hide();\r\n";
		$str .= "\t\tgrayOut(false);\r\n";
		$str .= "\t\treturn;\r\n";
		$str .= "\t}\r\n";
		$str .= "\r\n";
		$str .= "\tnew Ajax.Request(WEB_ROOT+'/ajax/".$this->module.".php',\r\n";
		$str .= "\t{\r\n";
		$str .= "\t\tmethod:'post',\r\n";
		$str .= "\t\tparameters: {type: \"edit".$this->name."\", ".$primaryKey.":id},\r\n";
		$str .= "\t\tonSuccess: function(transport){\r\n";
		$str .= "\t\t\tvar response = transport.responseText || \"no response text\";\r\n";
		$str .= "\t\t\tFViewOffSet(response);\r\n";
		$str .= "\t\t\tEvent.observe(\$('closePopUpDiv'), \"click\", function(){ Edit".$this->name."Popup(0); });\r\n";
		$str .= "\t\t\tEvent.observe(\$('edit".$this->name."'), \"click\", Edit".$this->name.");\r\n";
		$str .= "\t\t},\r\n";
		$str .= "\t\tonFailure: function(){ alert('Something went wrong...') }\r\n";
		$str .= "\t});\r\n";
		$str .= "}\r\n";
		$str .= "\r\n";

		$str .= "function Edit".$this->name."()\r\n";
		$str .= "{\r\n";
		$str .= "\tnew Ajax.Request(WEB_ROOT+'/ajax/".$this->module.".php',\r\n";
		$str .= "\t{\r\n";
		$str .= "\t\tmethod:'post',\r\n";
		$str .= "\t\tparameters: \$('edit".$this->name."Form').serialize(true),\r\n";
		$str .= "\t\tonSuccess: function(transport){\r\n";
		$str .= "\t\t\tvar response = transport.responseText || \"no response text\";\r\n";
		$str .= "\t\t\tvar splitResponse = response.split(\"[#]\");\r\n";
		$str .= "\t\t\tif(splitResponse[0] == \"fail\")\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\tShowStatusPopUp(splitResponse[1])\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\t\telse\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\tShowStatusPopUp(splitResponse[1])\r\n";
		$str .= "\t\t\t\t\$('content').innerHTML = splitResponse[2];\r\n";
		$str .= "\t\t\t\tAdd".$this->name."Div(0);\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\t},\r\n";
		$str .= "\t\tonFailure: function(){ alert('Something went wrong...') }\r\n";
		$str .= "\t});\r\n";
		$str .= "}\r\n";
		$str .= "\r\n";

		$str .= "function Delete".$this->name."Popup(id)\r\n";
		$str .= "{\r\n";
		$str .= "\tvar message = \"Realmente deseas eliminar este registro?\";\r\n";
		$str .= "\tif(!confirm(message))\r\n";
		$str .= "\t{\r\n";
		$str .= "\t\treturn;\r\n";
		$str .= "\t}\r\n";
		$str .= "\tnew Ajax.Request(WEB_ROOT+'/ajax/".$this->module.".php',\r\n";
		$str .= "\t{\r\n";
		$str .= "\t\tmethod:'post',\r\n";
		$str .= "\t\tparameters: {type: \"delete".$this->name."\", ".$primaryKey.": id},\r\n";
		$str .= "\t\tonSuccess: function(transport){\r\n";
		$str .= "\t\t\tvar response = transport.responseText || \"no response text\";\r\n";
		$str .= "\t\t\tvar splitResponse = response.split(\"[#]\");\r\n";
		$str .= "\t\t\tShowStatus(splitResponse[1])\r\n";
		$str .= "\t\t\t$('content').innerHTML = splitResponse[2];\r\n";
		$str .= "\t\t\t\tAdd".$this->name."Div(0);\r\n";
		$str .= "\t\t},\r\n";
		$str .= "\t\tonFailure: function(){ alert('Something went wrong...') }\r\n";
		$str .= "\t});\r\n";
		$str .= "}\r\n";
		$str .= "\r\n";

		$str .= "function Add".$this->name."Div(id)\r\n";
		$str .= "{\r\n";
		$str .= "\tgrayOut(true);\r\n";
		$str .= "\t\$('fview').show();\r\n";
		$str .= "\tif(id == 0)\r\n";
		$str .= "\t{\r\n";
		$str .= "\t\t\$('fview').hide();\r\n";
		$str .= "\t\tgrayOut(false);\r\n";
		$str .= "\t\treturn;\r\n";
		$str .= "\t}\r\n";
		$str .= "\r\n";
		$str .= "\tnew Ajax.Request(WEB_ROOT+'/ajax/".$this->module.".php',\r\n";
		$str .= "\t{\r\n";
		$str .= "\t\tmethod:'post',\r\n";
		$str .= "\t\tparameters: {type: \"add".$this->name."\"},\r\n";
		$str .= "\t\tonSuccess: function(transport){\r\n";
		$str .= "\t\t\tvar response = transport.responseText || \"no response text\";\r\n";
		$str .= "\t\t\tFViewOffSet(response);\r\n";
		$str .= "\t\t\tEvent.observe(\$('add".$this->name."'), \"click\", Add".$this->name.");\r\n";
		$str .= "\t\t\tEvent.observe(\$('fviewclose'), \"click\", function(){ Add".$this->name."Div(0); });\r\n";
		$str .= "\t\t},\r\n";
		$str .= "\t\tonFailure: function(){ alert('Something went wrong...') }\r\n";
		$str .= "\t});\r\n";
		$str .= "}\r\n";
		$str .= "\r\n";


		$str .= "function Add".$this->name."()\r\n";
		$str .= "{\r\n";
		$str .= "\tnew Ajax.Request(WEB_ROOT+'/ajax/".$this->module.".php',\r\n";
		$str .= "\t{\r\n";
		$str .= "\t\tmethod:'post',\r\n";
		$str .= "\t\tparameters: $('add".$this->name."Form').serialize(true),\r\n";
		$str .= "\t\tonSuccess: function(transport){\r\n";
		$str .= "\t\t\tvar response = transport.responseText || \"no response text\";\r\n";
		$str .= "\t\t\tvar splitResponse = response.split(\"[#]\");\r\n";
		$str .= "\t\t\tif(splitResponse[0] == \"fail\")\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\tShowStatusPopUp(splitResponse[1])\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\t\telse\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\tShowStatusPopUp(splitResponse[1])\r\n";
		$str .= "\t\t\t\t\$('content').innerHTML = splitResponse[2];\r\n";
		$str .= "\t\t\t\tAdd".$this->name."Div(0);\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\t},\r\n";
		$str .= "\t\tonFailure: function(){ alert('Something went wrong...') }\r\n";
		$str .= "\t});\r\n";
		$str .= "}\r\n";
		$str .= "\r\n";

		$myFile = DOC_ROOT."/javascript/".$this->module.".js";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		
	}

	function GenerateAjax()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "<?php\r\n";
		$str .= "include_once('../init.php');\r\n";
		$str .= "include_once('../config.php');\r\n";
		$str .= "include_once(DOC_ROOT.'/libraries.php');\r\n";
		$str .= "switch(\$_POST[\"type\"])\r\n";
		$str .= "{\r\n";

		$str .= "\tcase \"add".$this->name."\": \r\n";
		$str .= "\t\t\t\$smarty->assign(\"DOC_ROOT\", DOC_ROOT);\r\n";
		$str .= "\t\t\t\$smarty->display(DOC_ROOT.'/templates/boxes/add-".$this->module."-popup.tpl');\r\n";
		$str .= "\t\tbreak;	\r\n";
		$str .= "\tcase \"saveAdd".$this->name."\":\r\n";

		//fields
		foreach($fields as $field)
		{
			if($field["Field"] != $primaryKey)
			{
				$str .= "\t\t\t\$".$this->classe."->set".ucfirst($field["Field"])."(\$_POST['".$field["Field"]."']);\r\n";			
			}
		}
		
		$str .= "\t\t\tif(!\$".$this->classe."->Save())\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\techo \"fail[#]\";\r\n";
		$str .= "\t\t\t\t\$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\t\telse\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\techo \"ok[#]\";\r\n";
		$str .= "\t\t\t\t\$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');\r\n";
		$str .= "\t\t\t\techo \"[#]\";\r\n";
		$str .= "\t\t\t\t\$".$this->modulePlural." = \$".$this->classe."->Enumerate();\r\n";
		$str .= "\t\t\t\t\$smarty->assign(\"".$this->modulePlural."\", \$".$this->modulePlural.");\r\n";
		$str .= "\t\t\t\t\$smarty->assign(\"DOC_ROOT\", DOC_ROOT);\r\n";
		$str .= "\t\t\t\t\$smarty->display(DOC_ROOT.'/templates/lists/".$this->module.".tpl');\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\tbreak;\r\n";

		$str .= "\tcase \"delete".$this->name."\":\r\n";
		$str .= "\t\t\t\$".$this->classe."->set".ucfirst($primaryKey)."(\$_POST['".$primaryKey."']);\r\n";
		$str .= "\t\t\tif($".$this->classe."->Delete())\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\techo \"ok[#]\";\r\n";
		$str .= "\t\t\t\t\$smarty->display(DOC_ROOT.'/templates/boxes/status.tpl');\r\n";
		$str .= "\t\t\t\techo \"[#]\";\r\n";
		$str .= "\t\t\t\t\$".$this->modulePlural." = \$".$this->classe."->Enumerate();\r\n";
		$str .= "\t\t\t\t\$smarty->assign(\"".$this->modulePlural."\", \$".$this->modulePlural.");\r\n";
		$str .= "\t\t\t\t\$smarty->assign(\"DOC_ROOT\", DOC_ROOT);\r\n";
		$str .= "\t\t\t\t\$smarty->display(DOC_ROOT.'/templates/lists/".$this->module.".tpl');\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\tbreak;\r\n";


		$str .= "\tcase \"edit".$this->name."\": \r\n";
		$str .= "\t\t\t\$smarty->assign(\"DOC_ROOT\", DOC_ROOT);\r\n";
		$str .= "\t\t\t\$".$this->classe."->set".ucfirst($primaryKey)."(\$_POST['".$primaryKey."']);\r\n";
		$str .= "\t\t\t\$my".$this->name." = \$".$this->classe."->Info();\r\n";
		$str .= "\t\t\t\$smarty->assign(\"post\", \$my".$this->name.");\r\n";
		$str .= "\t\t\t\$smarty->display(DOC_ROOT.'/templates/boxes/edit-".$this->module."-popup.tpl');\r\n";
		$str .= "\t\tbreak;\r\n";

		$str .= "\tcase \"saveEdit".$this->name."\":\r\n";

		foreach($fields as $field)
		{
			$str .= "\t\t\t\$".$this->classe."->set".ucfirst($field["Field"])."(\$_POST['".$field["Field"]."']);\r\n";			
		}

		$str .= "\t\t\tif(!\$".$this->classe."->Edit())\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\techo \"fail[#]\";\r\n";
		$str .= "\t\t\t\t\$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\t\telse\r\n";
		$str .= "\t\t\t{\r\n";
		$str .= "\t\t\t\techo \"ok[#]\";\r\n";
		$str .= "\t\t\t\t\$smarty->display(DOC_ROOT.'/templates/boxes/status_on_popup.tpl');\r\n";
		$str .= "\t\t\t\techo \"[#]\";\r\n";
		$str .= "\t\t\t\t\$".$this->modulePlural." = \$".$this->classe."->Enumerate();\r\n";
		$str .= "\t\t\t\t\$smarty->assign(\"".$this->modulePlural."\", \$".$this->modulePlural.");\r\n";
		$str .= "\t\t\t\t\$smarty->assign(\"DOC_ROOT\", DOC_ROOT);\r\n";
		$str .= "\t\t\t\t\$smarty->display(DOC_ROOT.'/templates/lists/".$this->module.".tpl');\r\n";
		$str .= "\t\t\t}\r\n";
		$str .= "\t\tbreak;\r\n";
		$str .= "}\r\n";
		$str .= "?>\r\n";

		$myFile = DOC_ROOT."/ajax/".$this->module.".php";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		
		
	}

	function GenerateBoxes()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "<div class=\"popupheader\" style=\"z-index:70\">\r\n";
		$str .= "\t<div id=\"fviewmenu\" style=\"z-index:70\">\r\n";
		$str .= "\t\t<div id=\"fviewclose\"><span style=\"color:#CCC\" id=\"closePopUpDiv\">Close<img src=\"{\$WEB_ROOT}/images/b_disn.png\" border=\"0\" alt=\"close\" /></span>\r\n";
		$str .= "\t\t</div>\r\n";
		$str .= "\t</div>\r\n";

		$str .= "\t<div id=\"ftitl\">\r\n";
		$str .= "\t\t<div class=\"flabel\">Agregar ".$this->name."</div>\r\n";
		$str .= "\t\t<div id=\"vtitl\"><span title=\"Titulo\">Agregar ".$this->name."</span></div>\r\n";
		$str .= "\t</div>\r\n";
		$str .= "\t<div id=\"draganddrop\" style=\"position:absolute;top:45px;left:640px\">\r\n";
		$str .= "\t\t<img src=\"{\$WEB_ROOT}/images/draganddrop.png\" border=\"0\" alt=\"mueve\" />\r\n";
		$str .= "\t</div>\r\n";
		$str .= "</div>\r\n";

		$str .= "<div class=\"wrapper\">\r\n";
		$str .= "\t{include file=\"{\$DOC_ROOT}/templates/forms/add-".$this->module.".tpl\"}\r\n";
		$str .= "</div>\r\n";

		$myFile = DOC_ROOT."/templates/boxes/add-".$this->module."-popup.tpl";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		

		$str = "";
		$str .= "<div class=\"popupheader\" style=\"z-index:70\">\r\n";
		$str .= "\t<div id=\"fviewmenu\" style=\"z-index:70\">\r\n";
		$str .= "\t\t<div id=\"fviewclose\"><span style=\"color:#CCC\" id=\"closePopUpDiv\">Close<img src=\"{\$WEB_ROOT}/images/b_disn.png\" border=\"0\" alt=\"close\" /></span>\r\n";
		$str .= "\t\t</div>\r\n";
		$str .= "\t</div>\r\n";

		$str .= "\t<div id=\"ftitl\">\r\n";
		$str .= "\t\t<div class=\"flabel\">Editar ".$this->name."</div>\r\n";
		$str .= "\t\t<div id=\"vtitl\"><span title=\"Titulo\">Editar ".$this->name."</span></div>\r\n";
		$str .= "\t</div>\r\n";
		$str .= "\t<div id=\"draganddrop\" style=\"position:absolute;top:45px;left:640px\">\r\n";
		$str .= "\t\t<img src=\"{\$WEB_ROOT}/images/draganddrop.png\" border=\"0\" alt=\"mueve\" />\r\n";
		$str .= "\t</div>\r\n";
		$str .= "</div>\r\n";

		$str .= "<div class=\"wrapper\">\r\n";
		$str .= "\t{include file=\"{\$DOC_ROOT}/templates/forms/edit-".$this->module.".tpl\"}\r\n";
		$str .= "</div>\r\n";

		$myFile = DOC_ROOT."/templates/boxes/edit-".$this->module."-popup.tpl";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		

	}

	function GenerateForms()
	{
		$fields = $this->GetFields();
		$primaryKey = $this->GetPrimaryKey($fields);
		$totalFields = count($fields);
		
		$str .= "<div id=\"divForm\">\r\n";
		$str .= "\t<form id=\"add".$this->name."Form\" name=\"add".$this->name."Form\" method=\"post\">\r\n";
		$str .= "\t\t<fieldset>\r\n";

		foreach($fields as $field)
		{
			if($field["Field"] != $primaryKey)
			{
				$str .= "\t\t\t<div class=\"formLine\" style=\"width:100%; text-align:left\">\r\n";
				$str .= "\t\t\t\t<div style=\"width:30%;float:left\">".ucfirst($field["Field"]).":</div><input name=\"".$field["Field"]."\" id=\"".$field["Field"]."\" type=\"text\" value=\"{\$post.".$field["Field"]."}\" size=\"50\"/>\r\n";
				$str .= "\t\t\t</div>\r\n";
			}
		}

		$str .= "\t\t\t<div style=\"clear:both\"></div>\r\n";
		$str .= "\t\t\t<hr />\r\n";
		$str .= "\t\t\t<div class=\"formLine\" style=\"text-align:center\">\r\n";
		$str .= "\t\t\t\t<input type=\"button\" id=\"add".$this->name."\" name=\"add".$this->name."\" class=\"buttonForm\" value=\"Agregar\" />\r\n";
		$str .= "\t\t\t</div>\r\n";

		$str .= "\t\t\t<input type=\"hidden\" id=\"type\" name=\"type\" value=\"saveAdd".$this->name."\"/>\r\n";
		$str .= "\t\t\t<input type=\"hidden\" id=\"".$primaryKey."\" name=\"".$primaryKey."\" value=\"{\$post.".$primaryKey."}\"/>\r\n";
		$str .= "\t\t</fieldset>\r\n";
		$str .= "\t</form>\r\n";
		$str .= "</div>\r\n";


		$myFile = DOC_ROOT."/templates/forms/add-".$this->module.".tpl";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		
		
		$str = "";
		$str .= "<div id=\"divForm\">\r\n";
		$str .= "\t<form id=\"edit".$this->name."Form\" name=\"edit".$this->name."Form\" method=\"post\">\r\n";
		$str .= "\t\t<fieldset>\r\n";

		foreach($fields as $field)
		{
			if($field["Field"] != $primaryKey)
			{
				$str .= "\t\t\t<div class=\"formLine\" style=\"width:100%; text-align:left\">\r\n";
				$str .= "\t\t\t\t<div style=\"width:30%;float:left\">".ucfirst($field["Field"]).":</div><input name=\"".$field["Field"]."\" id=\"".$field["Field"]."\" type=\"text\" value=\"{\$post.".$field["Field"]."}\" size=\"50\"/>\r\n";
				$str .= "\t\t\t</div>\r\n";
			}
		}

		$str .= "\t\t\t<div style=\"clear:both\"></div>\r\n";
		$str .= "\t\t\t<hr />\r\n";
		$str .= "\t\t\t<div class=\"formLine\" style=\"text-align:center\">\r\n";
		$str .= "\t\t\t\t<input type=\"button\" id=\"edit".$this->name."\" name=\"edit".$this->name."\" class=\"buttonForm\" value=\"Actualizar\" />\r\n";
		$str .= "\t\t\t</div>\r\n";

		$str .= "\t\t\t<input type=\"hidden\" id=\"type\" name=\"type\" value=\"saveEdit".$this->name."\"/>\r\n";
		$str .= "\t\t\t<input type=\"hidden\" id=\"".$primaryKey."\" name=\"".$primaryKey."\" value=\"{\$post.".$primaryKey."}\"/>\r\n";
		$str .= "\t\t</fieldset>\r\n";
		$str .= "\t</form>\r\n";
		$str .= "</div>\r\n";


		$myFile = DOC_ROOT."/templates/forms/edit-".$this->module.".tpl";
		$fh = fopen($myFile, 'w') or die("can't open file");
		fwrite($fh, $str);		
		fclose($fh);		

		
	}

	function GetFields()
	{
		$this->Util()->DB()->setQuery("SHOW COLUMNS FROM ".$this->table);
		$result = $this->Util()->DB()->GetResult();
		
		$fields = explode(",", $this->fields);
		
		if($this->fields)
		{
			//check if we spelled all the right columns
			foreach($fields as $field)
			{
				$exists = false;
				foreach($result as $res)
				{
					if($res["Field"] == $field)
					{
						$exists = true;
						break;
					}
				}
				if($exists == false)
				{
					echo "Field doesn't exist in table, please correct before continuing: ". $field;
					echo "<br>";
					exit();
				}
			}
			
			foreach($result as $key => $res)
			{
				if(!in_array($res["Field"], $fields))
				{
					unset($result[$key]);
				}
			}
		}
		else
		{
			foreach($result as $key => $res)
			{
				if($res["Key"] == "PRI")
				{
//					unset($result[$key]);
				}
			}
			
		}

		return $result;
	}
	
	function GetPrimaryKey($fields)
	{
		foreach($fields as $field)
		{
			if($field["Key"] == "PRI")
			{
				return $field["Field"];
			}
		}
	}

	public function Util() 
	{
		if($this->Util == null ) 
		{
			$this->Util = new Util();
		}
		return $this->Util;
	}
	
	function Destroy(){
		
		$destroy = $this->module;
		
		unlink(DOC_ROOT."/classes/".$this->classe.".class.php");
		unlink(DOC_ROOT."/modules/".$destroy.".php");
		unlink(DOC_ROOT."/templates/".$destroy.".tpl");
		unlink(DOC_ROOT."/templates/lists/".$destroy.".tpl");
		unlink(DOC_ROOT."/templates/items/".$destroy."-header.tpl");
		unlink(DOC_ROOT."/templates/items/".$destroy."-base.tpl");
		unlink(DOC_ROOT."/javascript/".$destroy.".js");
		unlink(DOC_ROOT."/ajax/".$destroy.".php");
		unlink(DOC_ROOT."/templates/boxes/edit-".$destroy."-popup.tpl");
		unlink(DOC_ROOT."/templates/boxes/add-".$destroy."-popup.tpl");
		unlink(DOC_ROOT."/templates/forms/edit-".$destroy.".tpl");
		unlink(DOC_ROOT."/templates/forms/add-".$destroy.".tpl");
		echo "<br>";
		echo "Destroyed Module ".$destroy;
		echo "<br>";
		
	}
	
}

$module = $_GET["module"];
$fields = $_GET["fields"];
$inheritesFrom = $_GET["inheritesFrom"];

$generate = new Module();

if(!$module)
{
	echo "You must specify a module.";
	exit();
}

$generate->table = $module;

$module = str_replace('_','-',$module);
$generate->module = $module;

$names = explode('-',$module);
$name = ucfirst($names[0]).ucfirst($names[1]);
$classe = $names[0].ucfirst($names[1]);
$generate->name = $name;
$generate->classe = $classe;

switch($_GET['action']){
	
	case 'create':
		
		$generate->modulePlural = "res".$name;
		$generate->fields = $fields;
		$generate->inheritesFrom = $inheritesFrom;
		$generate->GenerateClass();
		echo "Class generated at ".DOC_ROOT."/classes/".$classe.".class.php";
		echo "<br>";
		
		$generate->GenerateModule();
		echo "Module generated at ".DOC_ROOT."/modules/".$module.".php";
		echo "<br>";
		
		$generate->GenerateMainTemplate();
		echo "Template generated at ".DOC_ROOT."/templates/".$module.".tpl";
		echo "<br>";
		
		$generate->GenerateLists();
		echo "Template generated at ".DOC_ROOT."/templates/lists/".$module.".tpl";
		echo "<br>";
		
		$generate->GenerateItems();
		echo "Items generated at ".DOC_ROOT."/templates/items/".$module."-header.tpl";
		echo "<br>";
		echo "Items generated at ".DOC_ROOT."/templates/items/".$module."-base.tpl";
		echo "<br>";
		
		$generate->GenerateJavascript();
		echo "Javascript generated at ".DOC_ROOT."/javascript/".$module.".js";
		echo "<br>";
		
		$generate->GenerateAjax();
		echo "AJAX generated at ".DOC_ROOT."/ajax/".$module.".php";
		echo "<br>";
		
		$generate->GenerateBoxes();
		echo "Boxes generated at ".DOC_ROOT."/templates/boxes/edit-".$module."-popup.php";
		echo "<br>";
		echo "Boxes generated at ".DOC_ROOT."/templates/boxes/add-".$module."-popup.php";
		echo "<br>";
		
		$generate->GenerateForms();
		echo "Form generated at ".DOC_ROOT."/templates/forms/edit-".$module."-popup.php";
		echo "<br>";
		echo "Boxes generated at ".DOC_ROOT."/templates/forms/add-".$module."-popup.php";
		echo "<br>";
		
		break;
	case 'destroy':		
		$generate->destroy();				
		break;
	
	default:
		echo "You must specify an action.";

}

?>