<div id="divForm">
    <form id="editComentarioForm" name="editComentarioForm" method="post">
        <input type="hidden" id="type" name="type" value="saveEditComentario"/>
        <input type="hidden" id="contractId" name="contractId" value="{$post.contractId}"/>
        <fieldset>
            <div class="formLine" style="width:100%; text-align:left">
                <div style="width:30%;float:left">Comentario:</div>
                <input class="smallInput medium" name="comentario" id="comentario" type="text" value="{$post.comentario}" size="50"/>
                <hr />
            </div>
            <div style="clear:both"></div>
            * Campos requeridos
            <div class="formLine" style="text-align:center; margin-left:300px">
                <a class="button_grey" id="editCustomer" onclick="SaveEditComentario({$post.contractId})"><span>Actualizar</span></a>
            </div>
        </fieldset>
    </form>
</div>
