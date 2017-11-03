<?php /* Smarty version Smarty3-b7, created on 2017-10-23 23:06:18
         compiled from "/var/www/html/templates/lists/report-servicio-bono.tpl" */ ?>
<?php /*%%SmartyHeaderCode:22105808656ffef08a5ee82-27409023%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0aca4f29b65b524b11913d350fc6f1001873d0fa' => 
    array (
      0 => '/var/www/html/templates/lists/report-servicio-bono.tpl',
      1 => 1497132628,
    ),
  ),
  'nocache_hash' => '22105808656ffef08a5ee82-27409023',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<table width="100%" cellpadding="0" cellspacing="0" id="box-table-b" style="font-size:10px">
	<thead>
		<tr>
			<th class="cabeceraTabla" align="center" >Cliente</th>
			<th class="cabeceraTabla" align="center" >C. Asignado</th>
			<th class="cabeceraTabla" align="center" >Razon Social</th>
			<th class="cabeceraTabla" align="center" >Servicio</th>
			<?php  $_smarty_tpl->tpl_vars['mes'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('nombreMeses')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['mes']->key => $_smarty_tpl->tpl_vars['mes']->value){
?>
			<th class="cabeceraTabla" align="center" width="50px" ><?php echo $_smarty_tpl->getVariable('mes')->value;?>
</th>
			<?php }} ?>
			<!-- <th align="center" width="50">Ene</th>
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
			<th align="center" width="50">Dic</th> -->
			<th class="cabeceraTabla" align="center" >Total</th>
		</tr>
	</thead>
	<tbody>
		<?php  $_smarty_tpl->tpl_vars['Letra'] = new Smarty_Variable;
 $_smarty_tpl->tpl_vars['keyLetra'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('abcdario')->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['Letra']->key => $_smarty_tpl->tpl_vars['Letra']->value){
 $_smarty_tpl->tpl_vars['keyLetra']->value = $_smarty_tpl->tpl_vars['Letra']->key;
?>
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
						<?php if ($_smarty_tpl->getVariable('Letra')->value==$_smarty_tpl->getVariable('servicio')->value['LETRA']&&$_smarty_tpl->getVariable('keyLetra')->value==$_smarty_tpl->getVariable('servicio')->value['POCICION']){?>
						<tr>
							<td align="left" class="" title="<?php echo $_smarty_tpl->getVariable('contract')->value['responsable']['name'];?>
"><?php echo $_smarty_tpl->getVariable('cliente')->value['nameContact'];?>
</td>
							<td align="left" class="" title="<?php echo $_smarty_tpl->getVariable('contract')->value['responsable']['name'];?>
"><?php echo $_smarty_tpl->getVariable('servicio')->value['responsable'];?>
</td>
							<td align="left" class="" title="<?php echo $_smarty_tpl->getVariable('contract')->value['responsable']['name'];?>
"><?php echo $_smarty_tpl->getVariable('contract')->value['name'];?>
</td>
							<td align="center" class="" title="<?php echo $_smarty_tpl->getVariable('contract')->value['responsable']['name'];?>
"><?php echo $_smarty_tpl->getVariable('servicio')->value['nombreServicio'];?>
</td>
							<?php  $_smarty_tpl->tpl_vars['instanciaServicio'] = new Smarty_Variable;
 $_from = $_smarty_tpl->getVariable('servicio')->value['instancias']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
if (count($_from) > 0){
    foreach ($_from as $_smarty_tpl->tpl_vars['instanciaServicio']->key => $_smarty_tpl->tpl_vars['instanciaServicio']->value){
?>

								<td align="center"
								<?php if ($_smarty_tpl->getVariable('EXEL')->value=="SI"){?>
									<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['status']!='inactiva'){?>
										<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['class']=='CompletoTardio'||$_smarty_tpl->getVariable('instanciaServicio')->value['class']=='Completo'){?>
											style="background-color: #009900 !important;color:#FFF"
										<?php }else{ ?>
											<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['class']=='Iniciado'||$_smarty_tpl->getVariable('instanciaServicio')->value['class']=='PorCompletar'){?>
												style="background-color: #FC0 !important;color:#FFF"
											<?php }else{ ?>
												<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['class']=="PorIniciar"){?>
													style="background-color: #F00 !important;color:#FFF"
												<?php }?>
											<?php }?>
										<?php }?>
									<?php }?>

								<?php }else{ ?>
									class="
									<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['status']!='inactiva'){?>
										<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['class']=='CompletoTardio'){?>
											st<?php echo 'Completo';?>
 txtSt<?php echo 'Completo';?>

										<?php }else{ ?>
											<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['class']=='Iniciado'){?>
												st<?php echo 'PorCompletar';?>
 txtSt<?php echo 'PorCompletar';?>

											<?php }else{ ?>
												st<?php echo $_smarty_tpl->getVariable('instanciaServicio')->value['class'];?>
 txtSt<?php echo $_smarty_tpl->getVariable('instanciaServicio')->value['class'];?>

											<?php }?>
										<?php }?>
									<?php }?>"
								<?php }?>

								title="<?php echo $_smarty_tpl->getVariable('servicio')->value['nombreServicio'];?>
 <?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['status']!='inactiva'){?><?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['class']=='CompletoTardio'){?><?php echo 'Completo';?>
<?php }else{ ?><?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['class']=='Iniciado'){?><?php echo 'PorCompletar';?>
<?php }else{ ?><?php echo $_smarty_tpl->getVariable('instanciaServicio')->value['class'];?>
<?php }?><?php }?><?php }?>">
									<div style="cursor:pointer" onclick="GoToWorkflow('report-servicios', '<?php echo $_smarty_tpl->getVariable('instanciaServicio')->value['instanciaServicioId'];?>
')">
										<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['class']=='Completo'||$_smarty_tpl->getVariable('instanciaServicio')->value['class']=='CompletoTardio'){?>
											$<?php echo number_format($_smarty_tpl->getVariable('servicio')->value['costo'],2,".",",");?>

										<?php }else{ ?>
											-
										<?php }?>
										<?php if ($_smarty_tpl->getVariable('instanciaServicio')->value['status']=='inactiva'){?>
											<span style="color:#DA9696">(Inactivo)</span>
										<?php }?>
									</div>
								</td>
							<?php }} ?>
							<td align="center" mso-number-format>$<?php echo number_format($_smarty_tpl->getVariable('servicio')->value['sumatotal'],2);?>
</td>
						</tr>
						<?php }?>
					<?php }} ?>
				<?php }} ?>
			<?php }} else { ?>
				<?php if ($_smarty_tpl->getVariable('keyLetra')->value==0){?>
				<tr>
					<td colspan="15" align="center">Ning&uacute;n registro encontrado.</td>
				</tr>
				<?php }?>
			<?php } ?>
		<?php }} ?>

	</tbody>
</table>