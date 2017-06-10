<?php /* Smarty version Smarty3-b7, created on 2015-03-25 09:39:07
         compiled from "/var/www/html/templates/lists/report-servicio-pdf.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8321555565512e4ab068b37-89125786%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9cc4ae2fb7ce1a7e97e8b9dccfc9c4df4c0f52ed' => 
    array (
      0 => '/var/www/html/templates/lists/report-servicio-pdf.tpl',
      1 => 1412284790,
    ),
  ),
  'nocache_hash' => '8321555565512e4ab068b37-89125786',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_modifier_wordwrap')) include '/var/www/html/libs/plugins/modifier.wordwrap.php';
?><style>
.stCompleto{
	background-color:#009900 !important;
}
.stCompletoTardio{
	background-color:#01FFFF !important;
}
.stPorCompletar{
	background-color:#FC0 !important;
}
.stIniciado{
	background-color:#ffff99 !important;
}
.stPorIniciar{
	background-color:#F00 !important;
}
.txtStCompleto,
.txtStPorIniciar,
.txtStPorCompletar
.txtStCompletoTardio{
	color:#FFFFFF !important;
}
.txtIniciado{
	color:#000000 !important;
}
body{
	font-family:"Courier New", Courier, monospace;
	font-size:8px;
}

/*******************************************************************************
  TABLE DESIGN 
*******************************************************************************/
#box-table-a {
	font-size: 12px;
	margin: 0px;
	text-align: left;
	border-collapse: separate;
	border-bottom:none;
}
#box-table-a th {
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
/*	background: #EFEFEF;
*/	border-top: 1px solid #FFF;
	color: #333;
	text-align: center;
}
#box-table-a td {
	padding: 8px;
	background: none; 
	border-top: 1px solid #CCC;
	color: #666;
	border-bottom: none !important;
}

#box-table-a tr:hover td {
	background: #FBFBFB;
	color: #333;
}
#box-table-a tr.footer { background: none !important; }
#box-table-a tr.footer:hover td { background: none !important;  }

/*******************************************************************************
  TABLE DESIGN 
*******************************************************************************/
.box-table-b {
	font-size: 12px;
	margin: 0px;
	text-align: left;
	border-collapse: separate;
	border-bottom:none;
}
.box-table-b th {
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
	background: #EFEFEF;
	border-top: 1px solid #FFF;
	color: #333;
	text-align: center;
}
.box-table-b td {
	padding: 8px;
	background: none; 
	border-top: 1px solid #CCC;
	color: #666;
	border-bottom: none !important;
}
.box-table-b tr.footer { background: none !important; }
.box-table-b tr.footer:hover td { background: none !important;  }

#box-table-b {
	font-size: 12px;
	margin: 0px;
	text-align: left;
	border-collapse: separate;
	border-bottom:none;
}
#box-table-b th {
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
	background: #EFEFEF;
	border-top: 1px solid #FFF;
	color: #333;
	text-align: center;
}
#box-table-b td {
	padding: 8px;
	background: none; 
	border-top: 1px solid #CCC;
	color: #666;
	border-bottom: none !important;
}

#box-table-b tr.footer { background: none !important; }
#box-table-b tr.footer:hover td { background: none !important;  }

/*******************************************************************************
  TABLE DESIGN 
*******************************************************************************/
.box-table-a {
	font-size: 12px;
	margin: 0px;
	text-align: left;
	border-collapse: separate;
	border-bottom:none;
}
.box-table-a th {
	font-size: 13px;
	font-weight: normal;
	padding: 8px;
	background: #EFEFEF;
	border-top: 1px solid #FFF;
	color: #333;
	text-align: center;
}
.box-table-a td {
	padding: 8px;
	background: none; 
	border-top: 1px solid #CCC;
	color: #666;
	border-bottom: none !important;
}
.box-table-a tr:hover td {
	background: #FBFBFB;
	color: #333;
}
.box-table-a tr.footer { background: none !important; }
.box-table-a tr.footer:hover td { background: none !important;  }

@page { margin: 5px; }
body { margin: 5px; }
html { margin: 5px; }
</style>
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b">
<thead>
	<tr>
		<th align="center" width="80">Cliente</th>
		<th align="center" width="80">Razon Social</th>
		<th align="center" width="50">Ene</th>
		<th align="center" width="50">Feb</th>
		<th align="center" width="50">Mar</th>
		<th align="center" width="50">Abr</th>
		<th align="center" width="50">May</th>
		<th align="center" width="50">Jun</th>
		<th align="center" width="50">Jul</th>
		<th align="center" width="50">Ago</th>
		<th align="center" width="50">Sep</th>
		<th align="center" width="50">Oct</th>
		<th align="center" width="50">Nov</th>
		<th align="center" width="50">Dic</th>
	</tr>
</thead>
<tbody>

<?php if ($_smarty_tpl->getVariable('clientes')->value){?>
<?php  $_smarty_tpl->tpl_vars['cliente'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('clientes')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['cliente']->key => $_smarty_tpl->tpl_vars['cliente']->value){
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['cliente']->key;
?>
	<?php  $_smarty_tpl->tpl_vars['contract'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['keyContract'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('cliente')->value['contracts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['contract']->key => $_smarty_tpl->tpl_vars['contract']->value){
 $_smarty_tpl->tpl_vars['keyContract']->value = $_smarty_tpl->tpl_vars['contract']->key;
?>
		<?php  $_smarty_tpl->tpl_vars['servicio'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['keyServicio'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('contract')->value['instanciasServicio']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['servicio']->key => $_smarty_tpl->tpl_vars['servicio']->value){
 $_smarty_tpl->tpl_vars['keyServicio']->value = $_smarty_tpl->tpl_vars['servicio']->key;
?>
  
<tr >
    <td align="center" class="" title="<?php echo $_smarty_tpl->getVariable('contract')->value['responsable']['name'];?>
"><?php echo smarty_modifier_wordwrap($_smarty_tpl->getVariable('cliente')->value['nameContact'],15,"<br />");?>
</td>
    <td align="center" class="" title="<?php echo $_smarty_tpl->getVariable('contract')->value['responsable']['name'];?>
"><?php echo smarty_modifier_wordwrap($_smarty_tpl->getVariable('contract')->value['name'],15,"<br />");?>
</td>
    <?php  $_smarty_tpl->tpl_vars['instanciaServicio'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('servicio')->value['instancias']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['instanciaServicio']->key => $_smarty_tpl->tpl_vars['instanciaServicio']->value){
?>
    <td align="center" class="st<?php echo $_smarty_tpl->getVariable('instanciaServicio')->value['class'];?>
 txtSt<?php echo $_smarty_tpl->getVariable('instanciaServicio')->value['class'];?>
 " title="<?php echo $_smarty_tpl->getVariable('instanciaServicio')->value['class'];?>
">
	
<div style="cursor:pointer" onclick="GoToWorkflow('report-servicios', '<?php echo $_smarty_tpl->getVariable('instanciaServicio')->value['instanciaServicioId'];?>
')"><?php echo smarty_modifier_wordwrap($_smarty_tpl->getVariable('servicio')->value['nombreServicio'],10,"<br />",true);?>
</div>
    </td>
    <?php }} ?>
</tr>
		<?php }} ?>
  <?php }} ?>
<?php }} ?>
<?php }?>

</tbody>
</table>