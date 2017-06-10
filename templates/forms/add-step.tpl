<div id="divForm">
	<form id="addStepForm" name="addStepForm" method="post">
	<input type="hidden" id="type" name="type" value="saveAddStep"/>
	<input type="hidden" id="servicioId" name="servicioId" value="{$servicioId}"/>
		<fieldset>

			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Nombre:</div>
         <div style="width:30%;float:left"><input  name="nombreStep" id="nombreStep" type="text" value="{$post.inicioOperacionesMysql}" size="27"/> </div>
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
                <a class="button_grey" id="btnAddStep"><span>Agregar</span></a>           
            </div>			
		</fieldset>
	</form>
</div>
