{if $comments|count>0}
    <table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
        <thead>
            <tr>
                <th>Comentario</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$comments key=key item=item}
                <tr>
                    <td>{$item.comment}</td>
                    <td>
                        <a href="javascript:;"  id="{$item.commentId}" class="spanAll spanDeleteComment">
                            <img src="{$WEB_ROOT}/images/icons/delete.png" />
                        </a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}