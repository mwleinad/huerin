			{*include file="{$DOC_ROOT}/templates/boxes/status_open.tpl"*} 
			{if !empty($errors)}
      	<h3>
    			{*if $errors.complete}
			    	<img src="{$WEB_ROOT}/images/ok.png" style="cursor:pointer" />
    			{else}
	       		<img src="{$WEB_ROOT}/images/error.png" style="cursor:pointer" />
    			{/if*}  
       	</h3>
        <div style="position:relative;top:-20px;left:50px; font-size:14px">
        {foreach from=$errors.value item="error" key="key"}
    			<b>{$error}.
    			{if $errors.field.$key}
       			Campo: <u>{$errors.field.$key}</u>
    			{/if}
                </b>
     			<br />
  			{/foreach}

        </div>
      {/if}  
			{*include file="{$DOC_ROOT}/templates/boxes/status_close.tpl"*}
