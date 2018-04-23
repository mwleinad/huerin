<div id="divForm">
	<form id="editPersonalForm" name="editPersonalForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre Completo:</div>
                <input class="smallInput medium" name="name" id="name" type="text" value="{$post.name}" size="50"/>
				<hr />
            </div>		
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Tel&eacute;fono:</div>
                <input class="smallInput medium" name="phone" id="phone" type="text" value="{$post.phone}" size="50"/>            	<hr />
			</div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Extension:</div>
                <input class="smallInput medium" name="ext" id="ext" type="text" value="{$post.ext}" size="50"/>            	<hr />
			</div>

      <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Telefono Celular:</div>
                <input class="smallInput medium" name="celphone" id="celphone" type="text" value="{$post.celphone}" size="50"/>            	<hr />
			</div>      
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Correo electr&oacute;nico:</div>
                <input class="smallInput medium" name="email" id="email" type="text" value="{$post.email}" size="50"/>
                <hr />
			</div>

      <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left"># de Equipo:</div>
                <input class="smallInput medium" name="skype" id="skype" type="text" value="{$post.skype}" size="50"/>            	<hr />
			</div>

      <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Clave Aspel:</div>
                <input class="smallInput medium" name="aspel" id="aspel" type="text" value="{$post.aspel}" size="50"/>            	<hr />
			</div>

      <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Puesto de Trabajo:</div>
                <input class="smallInput medium" name="puesto" id="puesto" type="text" value="{$post.puesto}" size="50"/>            	<hr />
			</div>

      <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Horario de Trabajo:</div>
                <input class="smallInput medium" name="horario" id="horario" type="text" value="{$post.horario}" size="50"/>            	<hr />
			</div>

      <div class="formLine" style="width:100%; text-align:left">
      	<div style="width:30%;float:left">Fecha Ingreso:</div>
                <input style="width: 20%!important;" class="smallInput medium" name="fechaIngreso" id="fechaIngreso" onclick="return false" type="text" value="{$post.fechaIngreso}" maxlength="10" size="40" readonly="readonly"/>
                <hr />
	  </div>
      <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Grupo de Trabajo:</div>
                <input class="smallInput medium" name="grupo" id="grupo" type="text" value="{$post.grupo}" size="50"/>            	<hr />
			</div>

      <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Clave Computadora</div>
                <input class="smallInput medium" name="computadora" id="computadora" type="text" value="{$post.computadora}" size="50"/>            	<hr />
			</div>      
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Usuario:</div>
                <input class="smallInput medium" name="username" id="username" type="text" value="{$post.username}" size="50"/>            	<hr />
			</div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Contrase&ntilde;a:</div>
                <input class="smallInput medium" name="passwd" id="passwd" type="text" value="{$post.passwd}" size="50"/>            	<hr />
			</div>   

          <div class="formLine" style="width:100%; text-align:left">
             <div style="width:30%;float:left">Tipo de Usuario:</div>
              <select name="tipoPersonal" id="tipoPersonal" class="smallInput medium" onchange="ToggleReporta()">
                  <option value="" >Seleccionar...</option>
                  {foreach from=$roles item=item key=key}
                      <option value="{$item.name}" {if $post.tipoPersonal eq $item.name || $post.roleId eq $item.rolId} selected="selected" {/if}>{$item.name}</option>
                  {/foreach}
              </select>
             <hr />       
          </div>
      
          <div class="formLine" style="width:100%; text-align:left"  id="departamentoDiv">
             <div style="width:30%;float:left">Departamento:</div>
             <select name="departamentoId" id="departamentoId"  class="smallInput medium">
              	<option value="0">Seleccione...</option>
             	{foreach from=$departamentos item=departamento}
              	<option value="{$departamento.departamentoId}" {if $departamento.departamentoId == $post.departamentoId} selected="selected"{/if}>{$departamento.departamento}</option>
              {/foreach}
             </select>
             <hr />       
          </div>	  

          <div class="formLine" style="width:100%; text-align:left;"  id="">
             <div style="width:30%;float:left">Jefe Inmediato:</div>
             <select name="jefeInmediato" id="jefeInmediato"  class="smallInput medium">
              	<option value="0">Seleccione...</option>
             	{foreach from=$personal item=contador}
              	<option value="{$contador.personalId}" {if $contador.personalId == $post.jefeInmediato} selected="selected"{/if}>{$contador.name}</option>
              {/foreach}
             </select>
             <hr />       
          </div>
         <div class="formLine" style="width:100%; text-align:left">
            <div style="width:30%;float:left">Activo:</div><input name="active" id="active" type="checkbox" {if $post.active}checked{/if} value="1"/>
            <hr />
          </div>
          <div class="formLine" style="width:100%; text-align:left;" >
                <div style="display: inline-block">Expedientes a subir</div>
                <br/> <br/>
                <div style="display: inline-block; ">
                    <table>
                        {assign var='ncol' value=3}
                        {foreach from=$expedientes key=kexp item=exp}
                            {if $ncol eq 3}
                                <tr><td><input type="checkbox" name="expe[]" id="" value="{$exp.expedienteId}" {if $exp.find}checked onclick="return false;"{/if}></td><td>{$exp.name}</td>
                                {assign var='ncol' value=$ncol-1}
                            {else}
                                <td> <input type="checkbox" name="expe[]" id="{$exp.expedienteId}" value="{$exp.expedienteId}" {if $exp.find}checked onclick="return false;"{/if}></td> <td>{$exp.name}</td>
                                {assign var='ncol' value=$ncol-1}
                                {if $ncol eq 0}
                                    {assign var='ncol' value=3}
                                    </tr>
                                {/if}
                            {/if}
                        {/foreach}
                    </table>
                </div><br>
                 <span style="color: #990000;">{$msgExp}</span>
                <hr />
          </div>

			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="editPersonal"><span>Actualizar</span></a>           
            </div>			
			<input type="hidden" id="type" name="type" value="saveEditPersonal"/>
			<input type="hidden" id="personalId" name="personalId" value="{$post.personalId}"/>
		</fieldset>
	</form>
</div>
