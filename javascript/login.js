Event.observe(window, 'load', function() {
	Event.observe($('doLogin'), "click", DoLogin);
	Event.observe($('username'), 'keypress', function(event){ 
		var key = event.which || event.keyCode;
		if(key == 13) DoLogin();
	});
	Event.observe($('passwd'), 'keypress', function(event){ 
		var key = event.which || event.keyCode;
		if(key == 13) DoLogin();
	});

});

function DoLogin()
{	
	new Ajax.Request(WEB_ROOT+'/ajax/usuario.php', 
	{
		method:'post',
		parameters: $('frmLogin').serialize(true),
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			console.log(response);

			var splitResponse = response.split("[#]");
			if(splitResponse[0].trim() == "fail")
			{
				ShowStatusPopUp(splitResponse[1]);
				Event.observe($('close_icon'), "click", function(){ ClosePopUp(); });
			}
			else
			{
				location.href = WEB_ROOT;
			}

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function ClosePopUp(){
	
	$('fview').hide();
	grayOut(false);
		
}