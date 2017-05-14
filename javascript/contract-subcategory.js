Event.observe(window, 'load', function() {
	
	Event.observe($('addSubcategoryDiv'), "click", AddSubcategoryDiv);
	
	AddEditSubcategoryListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteSubcategoryPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditSubcategoryPopup(id);
		}
	}

	$('contenido').observe("click", AddEditSubcategoryListeners);																	 

});

function AddSubcategoryDiv(id)
{
	grayOut(true);
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	$('fview').show();
	
	new Ajax.Request(WEB_ROOT+'/ajax/contract-subcategory.php', 
	{
		method:'post',
		parameters: {type: "addSubcategory"},
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('addSubcategory'), "click", AddSubcategory);
			Event.observe($('fviewclose'), "click", function(){ AddSubcategoryDiv(0); });

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function AddSubcategory()
{
		
	new Ajax.Request(WEB_ROOT+'/ajax/contract-subcategory.php', 
	{
		method:'post',
		parameters: $('addSubcategoryForm').serialize(true),
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];				
				AddSubcategoryDiv(0);
			}

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function EditSubcategoryPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	
	new Ajax.Request(WEB_ROOT+'/ajax/contract-subcategory.php', 
	{
		method:'post',
		parameters: {type: "editSubcategory", id:id},
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditSubcategoryPopup(0); });
			Event.observe($('editSubcategory'), "click", EditSubcategory);

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function EditSubcategory()
{
			
	new Ajax.Request(WEB_ROOT+'/ajax/contract-subcategory.php', 
	{
		method:'post',
		parameters: $('editSubcategoryForm').serialize(true),
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1])
			}
			else
			{
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
				AddSubcategoryDiv(0);
			}

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function DeleteSubcategoryPopup(id)
{
	
	var message = "Realmente deseas eliminar esta subcategoria?";
	if(!confirm(message))
  	{
		return;
	}	
	
	new Ajax.Request(WEB_ROOT+'/ajax/contract-subcategory.php',{
			method:'post',
			parameters: {type: "deleteSubcategory", id: id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
				
				var splitResponse = response.split("[#]");
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
				AddSubcategoryDiv(0);
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
	
}