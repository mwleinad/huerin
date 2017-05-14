<div id="divForm">
	<form id="addTaskForm" name="addTaskForm" method="post">
	<input type="hidden" id="type" name="type" value="saveAddTask"/>
	<input type="hidden" id="stepId" name="stepId" value="{$stepId}"/>
		<fieldset>

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre:</div>
         <div style="width:30%;float:left"><input  name="nombreTask" id="nombreTask" type="text" value="{$post.nombreTask}" size="27"/> </div>
				<hr />
        </div>		
            
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">*Dia de Vencimiento:</div>
         <div style="width:30%;float:left"><input  name="diaVencimiento" id="diaVencimiento" type="text" value="{$post.diaVencimiento}" size="27"/> </div>
				<hr />
			</div>

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">*Dias de Prorroga:</div>
         <div style="width:30%;float:left"><input  name="prorroga" id="prorroga" type="text" value="{$post.prorroga}" size="27"/> </div>
				<hr />
			</div>

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">*Control Uno (Obligatorio):</div>
         <div style="width:30%;float:left"><input  name="control" id="control" type="text" value="{$post.control}" size="27"/> </div>
				<hr />
			</div>

{*}            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Control Dos (Opcional):</div>
         <div style="width:30%;float:left"><input  name="control2" id="control2" type="text" value="{$post.control2}" size="27"/> </div>
				<hr />
			</div>

            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">Control Tres (Opcional):</div>
         <div style="width:30%;float:left"><input  name="control3" id="control3" type="text" value="{$post.control3}" size="27"/> </div>
				<hr />
			</div>
{*}      

			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="btnAddTask"><span>Agregar</span></a>           
            </div>			
		</fieldset>
	</form>
</div>
