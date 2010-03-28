<html>
<body>
<script type='text/javascript'>





</script>


<input type=text id=t1><!-- onkeydown='return false;' onkeyup='return false;' onkeypress='return false;'> -->
<div id=res style='display:inline;'></div>

<script type='text/javascript'>

function pr_d(e)
{
	document.getElementById('res').innerHTML += 'd' + e.which;
	window.event.cancelBubble=true;
	window.event.returnValue=false;
//	e.stopPropagation();
//	return false;
}
function pr_u(e)
{
	document.getElementById('res').innerHTML += 'u' + e.which;
	window.event.cancelBubble=true;
	window.event.returnValue=false;
//	e.stopPropagation();
//	return false;
}
function pr_p(e)
{
	document.getElementById('res').innerHTML += 'p' + e.which;
	window.event.cancelBubble=true;
	window.event.returnValue=false;
//	e.stopPropagation();
//	return false;
}

function ka()
{
	window.event.cancelBubble=true;
	window.event.returnValue=false;
}
	
	
	
	var t1=document.getElementById('t1');
	document.body.addEventListener('keydown',pr_d,true);
	document.body.addEventListener('keyup',pr_u,true);
	document.body.addEventListener('keypress',pr_p,true);
	t1.setAttribute("autocomplete","off");
	t1.addEventListener('beforedeactivate',ka,true);




</script>












</body>
</html>