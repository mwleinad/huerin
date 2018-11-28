<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 20/11/2018
 * Time: 06:24 PM
 */
use Dompdf\Dompdf;
use Dompdf\Options;
class Coffe extends main
{
    private $elements;
    private $menuId;
    public function setElements($value)
    {
        $this->elements=$value;

    }
    public function setId($value){
        $this->menuId = $value;
    }
    public function GenerateFile($type='download',$namefile="menu")
    {
        global $smarty;
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dom = new Dompdf();
        $smarty->assign('WEB_ROOT',WEB_ROOT);
        $smarty->assign('elements',$this->elements);
        $html = $smarty->fetch(DOC_ROOT.'/templates/molds/coffe.tpl');
        $dom->loadHtml($html);
        $dom->setPaper('letter', 'portrait');
        $dom->render();
         switch ($type){
             case 'download':
                 $dom->stream('menu.pdf');
             break;
             case 'view':
                 $dom->stream('menu.pdf', array("Attachment" => false));
             break;
             case 'save':
                 $output = $dom->output();

                 file_put_contents(DOC_ROOT."/sendFiles/$namefile.pdf", $output);
             break;
             default:
                 $dom->stream('menu.pdf', array("Attachment" => false));
              break;
         }
    }
    public function CleanMenu(){
        unset($_SESSION['platillos']);
    }
    public function SaveMenu($send=false){
       $mail =  new SendMail();

       $platillosImplode =  implode(',',$_SESSION['platillos']);
       $this->Util()->DB()->setQuery("INSERT INTO peppermint_menus(fecha,platillos)VALUES('".date("Y-m-d")."','".$platillosImplode."')");
       $id = $this->Util()->DB()->InsertData();
       if($send){
          $this->setElements($_SESSION['platillos']);
          $this->GenerateFile('save','menu'.$id);
          $file =  DOC_ROOT."/sendFiles/menu".$id.".pdf";
          if(file_exists($file)){
              $body ="<pre>Buen dia, le hacemos llegar el menu del dia.";
              $this->Util()->DB()->setQuery("select email,name from personal where active='1' and personalId NOT IN(13,56,32) ");
              $correos =  $this->Util()->DB()->GetResult();
              if(!is_array($correos))
                  $correos = [];
              $mails = [];
              foreach ($correos as $correo){
                  if($this->Util()->ValidateEmail($correo['email']))
                      $mails[$correo['email']] =  $correo['name'];
              }
              if(!empty($mails))
              {
                  $mails = [];
                  $mails['alizasevilla@gmail.com']= 'Aliza Sevilla';
                  $mails['isc061990@gmail.com']= 'Hector cruz';

                  $mail->PrepareMultipleNotice('MENU DEL DIA PEPPERMINT', $body, $mails, '', $file,'menu'.$id.".pdf" , "", "",'noreply@braunhuerin.com.mx','PEPPERMINT',true);
              }
              unlink($file);
          }


       }
       $this->Util()->setError(0,'complete','Se ha guardado y enviado correctamente el menu del dia.');
       $this->Util()->PrintErrors();
       $this->CleanMenu();
       return true;
    }
    public function Enumerate(){
        global $User,$rol;
        $this->Util()->DB()->setQuery('SELECT COUNT(*) FROM peppermint_menus');
        $total = $this->Util()->DB()->GetSingle();

        $pages = $this->Util->HandleMultipages($this->page, $total ,WEB_ROOT."/coffe");

        $sql_add = "LIMIT ".$pages["start"].", ".$pages["items_per_page"];
        $this->Util()->DB()->setQuery('SELECT * FROM peppermint_menus WHERE 1 ORDER BY fecha DESC '.$sql_add);
        $result = $this->Util()->DB()->GetResult();
        $data["items"] = $result;
        $data["pages"] = $pages;
        return $data;
    }
    public function Info(){
        $this->Util()->DB()->setQuery("SELECT platillos  from peppermint_menus WHERE menuId='".$this->menuId."' ");
        return $this->Util()->DB()->GetSingle();
    }
    public function DeleteMenu()
    {
        $this->Util()->DB()->setQuery("DELETE FROM peppermint_menus WHERE menuId='".$this->menuId."' ");
        $this->Util()->DB()->DeleteData();
        $this->Util()->setError(0,'complete','Menu eliminado correctamente.');
        $this->Util()->PrintErrors();
        return true;
    }

}