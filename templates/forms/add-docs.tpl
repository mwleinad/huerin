<div id="divForm">
	<form id="addDocsForm" name="addDocsForm" method="post">
		<fieldset>
			<div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">* Fecha Recibido Roque&ntilde;i <br />Straffon:</div>
                <table width="30%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                	<td>
                    <select name="day" id="day" class="smallInput">
                    <option value="01" {if $f.d == 1}selected{/if}>01</option>
                    <option value="02" {if $f.d == 2}selected{/if}>02</option>
                    <option value="03" {if $f.d == 3}selected{/if}>03</option>
                    <option value="04" {if $f.d == 4}selected{/if}>04</option>
                    <option value="05" {if $f.d == 5}selected{/if}>05</option>
                    <option value="06" {if $f.d == 6}selected{/if}>06</option>
                    <option value="07" {if $f.d == 8}selected{/if}>07</option>
                    <option value="08" {if $f.d == 8}selected{/if}>08</option>
                    <option value="09" {if $f.d == 9}selected{/if}>09</option>
                    <option value="10" {if $f.d == 10}selected{/if}>10</option>
                    <option value="11" {if $f.d == 11}selected{/if}>11</option>
                    <option value="12" {if $f.d == 12}selected{/if}>12</option>
                    <option value="13" {if $f.d == 13}selected{/if}>13</option>
                    <option value="14" {if $f.d == 14}selected{/if}>14</option>
                    <option value="15" {if $f.d == 15}selected{/if}>15</option>
                    <option value="16" {if $f.d == 16}selected{/if}>16</option>
                    <option value="17" {if $f.d == 17}selected{/if}>17</option>
                    <option value="18" {if $f.d == 18}selected{/if}>18</option>
                    <option value="19" {if $f.d == 19}selected{/if}>19</option>
                    <option value="20" {if $f.d == 20}selected{/if}>20</option>
                    <option value="21" {if $f.d == 21}selected{/if}>21</option>
                    <option value="22" {if $f.d == 22}selected{/if}>22</option>
                    <option value="23" {if $f.d == 23}selected{/if}>23</option>
                    <option value="24" {if $f.d == 24}selected{/if}>24</option>
                    <option value="25" {if $f.d == 25}selected{/if}>25</option>
                    <option value="26" {if $f.d == 26}selected{/if}>26</option>
                    <option value="27" {if $f.d == 27}selected{/if}>27</option>
                    <option value="28" {if $f.d == 28}selected{/if}>28</option>
                    <option value="29" {if $f.d == 29}selected{/if}>29</option>
                    <option value="30" {if $f.d == 30}selected{/if}>30</option>
                    <option value="31" {if $f.d == 31}selected{/if}>31</option>
                    </select>
                	</td>
                    <td>
                    <select name="month" id="month"class="smallInput">
                    <option value="01" {if $f.m == 1}selected{/if}>Enero</option>
                    <option value="02" {if $f.m == 2}selected{/if}>Febrero</option>
                    <option value="03" {if $f.m == 3}selected{/if}>Marzo</option>
                    <option value="04" {if $f.m == 4}selected{/if}>Abril</option>
                    <option value="05" {if $f.m == 5}selected{/if}>Mayo</option>
                    <option value="06" {if $f.m == 6}selected{/if}>Junio</option>
                    <option value="07" {if $f.m == 7}selected{/if}>Julio</option>
                    <option value="08" {if $f.m == 8}selected{/if}>Agosto</option>
                    <option value="09" {if $f.m == 9}selected{/if}>Septiembre</option>
                    <option value="10" {if $f.m == 10}selected{/if}>Octubre</option>
                    <option value="11" {if $f.m == 11}selected{/if}>Noviembre</option>
                    <option value="12" {if $f.m == 12}selected{/if}>Diciembre</option>
                    </select>
                	</td>
                    <td>
                	<input type="text" name="year" id="year" maxlength="4" value="{$f.y}" size="4" class="smallInput" />
                	</td>                   
                 </tr>
                 </table>                 
            </div>
            <div class="formLine" style="width:100%; text-align:left">
				<div style="width:30%;float:left">{$titInfo}</div>
                 <input type="text" name="des" id="des" class="smallInput" />
            </div>	
            
			<div style="clear:both"></div>
			
            <div class="formLine" style="text-align:center; margin-left:300px">            
                <a class="button_grey" id="btnAddDocs"><span>Agregar</span></a>           
            </div>	
            <br />&nbsp;            
            <hr />
           
           <div align="center" id="proContent">
           {include file="{$DOC_ROOT}/templates/lists/docs.tpl"}
           </div>
           	
            <input type="hidden" id="docBasicId" name="docBasicId" value="{$docBasicId}" />		
			<input type="hidden" id="action" name="action" value="saveAddDocs"/>
		</fieldset>
	</form>    
</div>
