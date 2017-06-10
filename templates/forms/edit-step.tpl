<div id="divForm">
	<form id="editStepForm" name="editStepForm" method="post">
	<input type="hidden" id="type" name="type" value="saveEditStep"/>
	<input type="hidden" id="stepId" name="stepId" value="{$post.stepId}"/>
	<input type="hidden" id="servicioId" name="servicioId" value="{$post.servicioId}"/>
		<fieldset>

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre:</div>
         <div style="width:30%;float:left"><input  name="nombreStep" id="nombreStep" type="text" value="{$post.nombreStep}" size="27"/> </div>
				<hr />
        </div>		
            
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Descripcion:</div>
         <div style="width:30%;float:left"><input  name="descripcion" id="descripcion" type="text" value="{$post.descripcion}" size="27"/> </div>
				<hr />
			</div>

			<div style="clear:both"></div>
			* Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="btnEditStep"><span>Agregar</span></a>           
            </div>			
		</fieldset>
	</form>
</div>
