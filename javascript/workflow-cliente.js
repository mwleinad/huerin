function ToggleTask(id)
{
	$$('.tasks').each(
		 function (e) {
				e.setStyle({display:'none'}); 
		 } 
	);
	$('step-'+id).show();
}

