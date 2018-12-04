Event.observe(window, 'load', function() {
    if(document.getElementById("addSteps"))
		Event.observe($('addSteps'), "click", AddStepsDiv);

	AddEditCustomerListeners = function(e) {
		var el = e.element();
		var del = el.hasClassName('spanDelete');
		var id = el.identify();
		if(del == true)
		{
			DeleteCustomerPopup(id);
			return;
		}

		del = el.hasClassName('spanEdit');
		if(del == true)
		{
			EditStepPopup(id);
		}

		del = el.hasClassName('spanAddTask');
		if(del == true)
		{
			AddTaskPopup(id);
		}

		del = el.hasClassName('spanTaskDelete');
		if(del == true)
		{
			DeleteTaskPopup(id);
		}

		del = el.hasClassName('spanTaskEdit');
		if(del == true)
		{
			EditTaskPopup(id);
		}

	}

	$('contenido').observe("click", AddEditCustomerListeners);

});

function EditTaskPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tasks.php',
	{
		method:'post',
		parameters: {type: "editTask", taskId:id, servicioId: $('servicioId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditTaskPopup(0); });
			Event.observe($('btnEditTask'), "click", EditTask);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditTask()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tasks.php',
	{
		method:'post',
		parameters: $('editTaskForm').serialize(true),
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
				EditTaskPopup(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}



function DeleteTaskPopup(id)
{
	var message = "Realmente deseas eliminar esta tarea?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/tasks.php',
	{
		method:'post',
		parameters: {type: "deleteTask", taskId: id, servicioId: $('servicioId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1]);
			$('contenido').innerHTML = splitResponse[2];
				AddCustomerDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTaskPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/tasks.php',
	{
		method:'post',
		parameters: {type: "addTask", stepId:id},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ AddTaskPopup(0); });
			Event.observe($('btnAddTask'), "click", AddTask);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddTask()
{
	new Ajax.Request(WEB_ROOT+'/ajax/tasks.php',
	{
		method:'post',
		parameters: $('addTaskForm').serialize(true),
		onLoading:function(){
			$('loading-img').style.display='block';
            $('btnAddTask').style.display='none';
		},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1]);
                $('btnAddTask').style.display='block';
                $('loading-img').style.display='none';
			}
			else
			{
				ShowStatusPopUp(splitResponse[1]);
                $('btnAddTask').style.display='block';
                $('loading-img').style.display='none';
				$('contenido').innerHTML = splitResponse[2];
				AddTaskPopup(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}
function EditStepPopup(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/steps.php',
	{
		method:'post',
		parameters: {type: "editStep", stepId:id, servicioId: $('servicioId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('closePopUpDiv'), "click", function(){ EditStepPopup(0); });
			Event.observe($('btnEditStep'), "click", EditStep);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function EditStep()
{
	new Ajax.Request(WEB_ROOT+'/ajax/steps.php',
	{
		method:'post',
		parameters: $('editStepForm').serialize(true),
        onLoading:function(){
            $('loading-img').style.display='block';
            $('btnEditTask').style.display='none';
        },
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			if(splitResponse[0] == "fail")
			{
				ShowStatusPopUp(splitResponse[1]);
                $('btnEditTask').style.display='block';
                $('loading-img').style.display='none';
			}
			else
			{
				ShowStatusPopUp(splitResponse[1])
                $('btnAddTask').style.display='block';
                $('loading-img').style.display='none';
				$('contenido').innerHTML = splitResponse[2];
				EditStepPopup(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function DeleteCustomerPopup(id)
{
	var message = "Realmente deseas eliminar este paso?";
	if(!confirm(message))
	{
		return;
	}
	new Ajax.Request(WEB_ROOT+'/ajax/steps.php',
	{
		method:'post',
		parameters: {type: "deleteStep", stepId: id, servicioId: $('servicioId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			var splitResponse = response.split("[#]");
			ShowStatus(splitResponse[1]);
			$('contenido').innerHTML = splitResponse[2];
				AddCustomerDiv(0);
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddStepsDiv(id)
{
	grayOut(true);
	$('fview').show();
	if(id == 0)
	{
		$('fview').hide();
		grayOut(false);
		return;
	}

	new Ajax.Request(WEB_ROOT+'/ajax/steps.php',
	{
		method:'post',
		parameters: {type: "addStep", id: $('servicioId').value},
		onSuccess: function(transport){
			var response = transport.responseText || "no response text";
			FViewOffSet(response);
			Event.observe($('btnAddStep'), "click", AddStep);
			Event.observe($('fviewclose'), "click", function(){ AddStepsDiv(0); });
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function AddStep()
{
	new Ajax.Request(WEB_ROOT+'/ajax/steps.php',
	{
		method:'post',
		parameters: $('addStepForm').serialize(true),
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
				AddStepsDiv(0);
			}
		},
		onFailure: function(){ alert('Something went wrong...') }
	});
}

function ToogleTasks(id)
{
	$('tasks-'+id).toggle();
	if($('tasks-'+id).visible())
	{
		$('spanStepId-'+id).innerHTML = "[-]";
	}
	else
	{
		$('spanStepId-'+id).innerHTML = "[+]";
	}
}
jQ(document).on('click','#check_all',function () {
	if(!jQ(this).is(':checked')){
        jQ('form input[type=checkbox]#extensiones').prop('checked',false);
	}else{
        jQ('form input[type=checkbox]#extensiones').prop('checked',true);
	}

});
