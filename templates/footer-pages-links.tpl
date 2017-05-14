{* se debe tener una variable typo arreglo en este caso llamado -arrPage- que es asignado en el modulo			*}
{* php (de la carpeta /modules) llamado cuando se carga la pagina de la opcion seleccionada						*}
{* este arreglo cuenta con los siguientes indices: 																*}
{*		rowBegin 		= el registro inicial que se esta mostrando en la pagina actual							*}
{*		countPageRows 	= el total de registros recuperados de la pagina actual 								*}
{*		rowEnd			= el registro final que se esta mostrando en la pagina actual 							*}
{*		totalTableRows	= el total de registros que existe en la tabla que se esta desplegando					*}
{*		rowsPerPages	= el total de registros que se debe desplegar por pagina								*}
{*		currentPage		= la pagina actual que se esta desplegando												*}
{*		totalPages		= el total de paginas que se pueden mostrar												*}
{*		startPage		= el enlace a la primera pagina si la pagina actual es mayor a 2 						*}
{*		previusPage		= el enlace a la pagina anterior si la pagina actual es mayor a 1						*}
{*		nextPage		= el enlace a la pagina siguiente si l pagina actual es menor al total de paginas		*}
{*		endPage			= el enlace a la ultima pagina si la pagina actual es menor a (total de paginas - 1)	*}
{*		refreshPage		= enlace a la misma pagina, por si se necesite actualizar los datos de la pagina		*}
<div id="footerPagesLinks">
    <div style="text-align:left; float:left">
        mostrando regs {$arrPage.rowBegin} al {$arrPage.rowEnd} de {$arrPage.totalTableRows} - <a href="{$arrPage.refreshPage}" title="Refrescar Datos">Refrescar Datos</a>
    </div>
    <div style="text-align:right; float:right">
		{if $arrPage.startPage|count_characters > 0}
			<a href="{$arrPage.startPage}" title="Pagina inicio">Inicio</a> |
		{else}
			Inicio |
		{/if}
		{if $arrPage.previusPage|count_characters > 0}
			<a href="{$arrPage.previusPage}" titulo="Pagina anterior">Anterior</a> |
		{else}
			Anterior |
		{/if}
		pagina {$arrPage.currentPage} de {$arrPage.totalPages} |
		{if $arrPage.nextPage|count_characters > 0}
			<a href="{$arrPage.nextPage}" title="Pagina siguiente">Siguiente</a> |
		{else}
			Siguiente |
		{/if}
		{if $arrPage.endPage|count_characters > 0}
			<a href="{$arrPage.endPage}" titulo="Pagina final">Final</a>
		{else}
			Final
		{/if}
    </div>
    <div style="clear:both"></div>
</div>