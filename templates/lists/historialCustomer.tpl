<table width="100%" cellpadding="0" cellspacing="0" id="box-table-a">
    {include file="{$DOC_ROOT}/templates/items/historialCustomer-header.tpl"}
    <tbody>
    {include file="{$DOC_ROOT}/templates/items/historialCustomer-base.tpl"}
    </tbody>
</table>
{if count($resDocumento.pages)}
    {include file="{$DOC_ROOT}/templates/lists/pages.tpl" pages=$resDocumento.pages}
{/if}