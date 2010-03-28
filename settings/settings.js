



function save_setting_value(oid,setting,val)
{
	async_post('/settings/backend.php','oid=' + encodeURIComponent(oid) + '&setting=' + encodeURIComponent(setting) + '&val=' + encodeURIComponent(val),function()
	{
	//$i('prof').innerHTML += '<br>' + xmlHttp.readyState + ';' + xmlHttp.status + ';' + xmlHttp.responseText;
	
	
	
	
	});

}


function save_setting_value_reload(oid,setting,val)
{
	async_post('/settings/backend.php','oid=' + encodeURIComponent(oid) + '&setting=' + encodeURIComponent(setting) + '&val=' + encodeURIComponent(val),function(){window.location.reload(true);});

}

