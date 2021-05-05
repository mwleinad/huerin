Event.observe(window, 'load', function() {
	
	//Event.observe($('addCategoryDiv'), "click", AddCategoryDiv);
	
	AddEditCategoryListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		/*
		if(del == true)
		{
			DeleteCategoryPopup(id);
			return;
		}
		*/

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditCategoryPopup(id);
		}
	}

	$('contenido').observe("click", AddEditCategoryListeners);																	 

});

function AddCategoryDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}	
	
	new Ajax.Request(WEB_ROOT+'/ajax/contract-category.php', 
	{
		method:'post',
		parameters: {type: "addCategory"},
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			
			FViewOffSet(response);
			Event.observe($('addCategory'), "click", AddCategory);
			Event.observe($('fviewclose'), "click", function(){ AddCategoryDiv(0); });

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function AddCategory()
{
	
	new Ajax.Request(WEB_ROOT+'/ajax/contract-category.php', 
	{
		method:'post',
		parameters: $('addCategoryForm').serialize(true),
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
				AddCategoryDiv(0);
			}

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function EditCategoryPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	
	new Ajax.Request(WEB_ROOT+'/ajax/contract-category.php', 
	{
		method:'post',
		parameters: {type: "editCategory", id:id},
    onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditCategoryPopup(0); });
			Event.observe($('editCategory'), "click", EditCategory);

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function EditCategory()
{
			
	new Ajax.Request(WEB_ROOT+'/ajax/contract-category.php', 
	{
		method:'post',
		parameters: $('editCategoryForm').serialize(true),
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
				AddCategoryDiv(0);
			}

		},
    onFailure: function(){ alert('Something went wrong...') }
  });
}

function DeleteCategoryPopup(id)
{
	
	var message = "Realmente deseas eliminar este tipo de contrato?";
	if(!confirm(message))
  	{
		return;
	}	
	
	new Ajax.Request(WEB_ROOT+'/ajax/contract-category.php',{
			method:'post',
			parameters: {type: "deleteCategory", id: id},
			onSuccess: function(transport){
				var response = transport.responseText || "no response text";
			
				var splitResponse = response.split("[#]");
				ShowStatusPopUp(splitResponse[1])
				$('contenido').innerHTML = splitResponse[2];
				AddCategoryDiv(0);
			},
		onFailure: function(){ alert('Something went wrong...') }
	  });
	
}