if(document.location.hostname == "braunhuerin.dyndns.org" || document.location.hostname ==  "192.168.1.12")
{
	if(document.location.port == "8080")
	{
		var webRoot = document.location.hostname+":8080";
	}
	else
	{
		var webRoot = document.location.hostname;
	}
}
else
{
	var webRoot = document.location.hostname + "/huerin_test";
}

var WEB_ROOT = "http://" + webRoot; 