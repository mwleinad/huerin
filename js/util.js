function grayOut(vis, options) {
  // Pass true to gray out screen, false to ungray
  // options are optional.  This is a JSON object with the following (optional) properties
  // opacity:0-100         // Lower number = less grayout higher = more of a blackout
  // zindex: #             // HTML elements with a higher zindex appear on top of the gray out
  // bgcolor: (#xxxxxx)    // Standard RGB Hex color code
  // grayOut(true, {'zindex':'50', 'bgcolor':'#0000FF', 'opacity':'70'});
  // Because options is JSON opacity/zindex/bgcolor are all optional and can appear
  // in any order.  Pass only the properties you need to set.
  var options = options || {};
  var zindex = options.zindex || 50;
  var opacity = options.opacity || 70;
  var opaque = (opacity / 100);
  var bgcolor = options.bgcolor || '#000000';
  var dark=document.getElementById('darkenScreenObject');
  if (!dark) {
    // The dark layer doesn't exist, it's never been created.  So we'll
    // create it here and apply some basic styles.
    // If you are getting errors in IE see: http://support.microsoft.com/default.aspx/kb/927917
    var tbody = document.getElementsByTagName("body")[0];
    var tnode = document.createElement('div');           // Create the layer.
        tnode.style.position='absolute';                 // Position absolutely
        tnode.style.top='0px';                           // In the top
        tnode.style.left='0px';                          // Left corner of the page
        tnode.style.overflow='hidden';                   // Try to avoid making scroll bars
        tnode.style.display='none';                      // Start out Hidden
        tnode.id='darkenScreenObject';                   // Name it so we can find it later
    tbody.appendChild(tnode);                            // Add it to the web page
    dark=document.getElementById('darkenScreenObject');  // Get the object.
  }
  if (vis) {
    // Calculate the page width and height
    if( document.body && ( document.body.scrollWidth || document.body.scrollHeight ) ) {
        var pageWidth = document.body.scrollWidth+'px';
        var pageHeight = document.body.scrollHeight+'px';
    } else if( document.body.offsetWidth ) {
      var pageWidth = document.body.offsetWidth+'px';
      var pageHeight = document.body.offsetHeight+'px';
    } else {
       var pageWidth='100%';
       var pageHeight='100%';
    }
    //set the shader to cover the entire page and make it visible.
    dark.style.opacity=opaque;
    dark.style.MozOpacity=opaque;
    dark.style.filter='alpha(opacity='+opacity+')';
    dark.style.zIndex=zindex;
    dark.style.backgroundColor=bgcolor;
    dark.style.width= pageWidth;
    dark.style.height= pageHeight;
    dark.style.display='block';
  } else {
     dark.style.display='none';
  }
}

function ShowStatus(status)
{
	$('divStatus').innerHTML = status;
	$('centeredDiv').show();
	grayOut(true);
}

function ShowStatusPopUp(status)
{
	$('divStatus').innerHTML = status;
	$('centeredDivOnPopup').show();
	grayOut(true);
}

function FViewOffSet(response, modal = 'fview')
{
	var offset = Element.cumulativeScrollOffset($(modal));
	var top = window.scrollY + 50;
	$(modal).style.position = "absolute";
	$(modal).style.top = top+"px";
	$(modal).innerHTML = response;
	jQ('#fview').draggable({
        handle:"#draganddrop"
    });
}
function ShowErrorOnPopup(message, error) {
  const popupContent = `
    <div id="centeredDivOnPopup" style="margin:auto; position:fixed; top:50%; left:50%; margin-top:-150px;margin-left:-275px;z-index:3000">
      <div style="width:548px; border:solid; border-color:#999;border-width:1px; background-color:#ccc; padding-left:5px; padding-top:5px; padding-bottom:5px">
        <div style="width:500px; border:solid; border-color:#999;border-width:1px; background-color:#FFF; padding:20px">
          <div id="close_icon" style="position:absolute;top: 10px; left: 500px">
            <img src="${WEB_ROOT}/images/close_icon.gif" style="cursor: pointer" onclick="ToogleStatusDivOnPopup()" />
          </div>
          <h3>
            <img src="${WEB_ROOT}/images/${error ? 'error' : 'ok'}.gif" />
          </h3>
          <div style="position:relative;top:-40px;left:50px; font-size:16px;text-align:justify;width:400px;overflow-wrap:break-word;">
            ${message}
          </div>
        </div>
      </div>
    </div>`;

  $('divStatus').innerHTML = popupContent;
  $('centeredDivOnPopup').show();
  grayOut(true);
}

