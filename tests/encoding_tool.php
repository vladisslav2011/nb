<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
session_start();
$_SESSION['uid']=0;
//$_SESSION['sql_design']=false;
$profiler=microtime(true);
require_once('lib/ddc_meta.php');
require_once('lib/dom.php');
require_once('lib/settings.php');
require_once('lib/commctrls.php');
require_once('lib/auth.php');
require_once('lib/query_gen.php');





class container_resize_postback extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype='container_resize_postback';
		$this->after_id='_resizeable';
		$this->css_style['border']='3px solid black';
	}
	function bootstrap()
	{
		editor_generic::bootstrap_part();
		$this->attributes['onmouseup']="if(resizer && resizer.obj && resizer.obj==this)chse.send_or_push({static:'".$this->send."',val:this.clientWidth+'x'+this.clientHeight});";
	}
	
}

class enclosing_table extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->css_style['width']='100%';
		$this->css_style['height']='100%';
		$this->css_style['border-collapse']='collapse';
		$this->css_style['padding']='0px';
	}
	function html_inner()
	{
		$this->rootnode->out(
		"<tr><td></td><td style='height:1px;'></td><td></td></tr>".
		"<tr><td style='width:1px;'></td>".
		"<td style='height:100%'>"
		);
		dom_table::html_inner();
		$this->rootnode->out(
		"</td>".
		"<td style='width:1px;'></td></tr>".
		"<tr><td></td><td style='height:3px;'></td><td></td></tr>"
		);
	}
}

class enclosing_table1 extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->css_style['width']='100%';
		$this->css_style['height']='100%';
	}
	function html_inner()
	{
		$this->rootnode->out(
		"<tr><td></td><td style='height:1px;'><div style='height:1px'> </div></td><td></td></tr>".
		"<tr><td style='width:1px;'><div style='width:1px'> </div></td>".
		"<td style='height:100%'>"
		);
		dom_table::html_inner();
		$this->rootnode->out(
		"</td>".
		"<td style='width:1px;'><div style='width:1px'> </div></td></tr>".
		"<tr><td></td><td style='height:1px;'><div style='height:1px'> </div></td><td></td></tr>"
		);
	}
}





if($_GET['cb']!='')
{
	$event->long_name=$long_name=$_POST['name'];
	$event->long_type=$long_type=$_POST['type'];
	$event->etype=$etype=preg_replace('/\..*/','',$long_type);
	
	$event->parent_type=$etype;
	$event->rem_type=preg_replace('/^[^.]*\./','',$long_type);
	$event->name=preg_replace('/\..*/','',$long_name);
	$event->parent_name=$event->name;
	$event->rem_name=preg_replace('/^[^.]*\./','',$long_name);
	$event->context=unserialize($_POST['context']);
	//$this_cont=$context[$long_name];
	$event->keys=unserialize($_POST['keys']);
	$event->val=&$_POST['val'];
	if(isset($_POST['last_generated_id']))$idcounter=$_POST['last_generated_id'];
	switch($long_name)
	{
	//handle root object events here
	case 'main':
		$_SESSION['main']=&$event->val;
		break;
	case 'urlENcode':
		$_SESSION['main']=urlencode($_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'urlDEcode':
		$_SESSION['main']=urldecode($_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'htmlspecialchars_decode':
		$_SESSION['main']=htmlspecialchars_decode($_SESSION['main'],ENT_QUOTES);
		print 'window.location.reload(true)';
		exit;
	case 'htmlspecialchars':
		$_SESSION['main']=htmlspecialchars($_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'htmlspecialchars+quotes':
		$_SESSION['main']=htmlspecialchars($_SESSION['main'],ENT_QUOTES);
		print 'window.location.reload(true)';
		exit;
	case 'js_escape':
		$_SESSION['main']=js_escape($_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'preg_quote':
		$_SESSION['main']=preg_quote($_SESSION['main'],'/');
		print 'window.location.reload(true)';
		exit;
	case 'sql::esc':
		$_SESSION['main']=$sql->esc($_SESSION['main']);
		print 'window.location.reload(true)';
		exit;

	
	case 'stripcslashes':
		$_SESSION['main']=stripcslashes($_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'cp1251->utf8':
		$_SESSION['main']=iconv('cp1251','utf-8',$_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'utf8->cp1251':
		$_SESSION['main']=iconv('utf-8','cp1251',$_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'base64_encode':
		$_SESSION['main']=base64_encode($_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'base64_decode':
		$_SESSION['main']=base64_decode($_SESSION['main']);
		print 'window.location.reload(true)';
		exit;
	case 'encoding_tool_container_resize_postback1':
		$_SESSION['encoding_tool_container_resize_postback1']=$_POST['val'];
		exit;
/*	case 'test_editor':
		$_SESSION['eeee']=$_POST['val'];print 'alert(\''.js_escape($_POST['val']).'\');';
		exit;
	case 'ast1':
			
			$customid=$event->context[$long_name]['retid'];
			$oid=$event->context[$long_name]['oid'];
			$htmlid=$event->context[$long_name]['htmlid'];
			$r= new editor_text_autosuggest_list1;
			$r->context=$ev->context;
			$r->input_part=$_POST['val'];
			$r->list_items=Array('blah','bleh','bluh','noo','poo','woo');
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->text_inp=$htmlid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r).
									"}catch(e){ window.location.reload(true);};";
			print 'a.style.display=\'block\';';
			$js='';
			foreach($r->result_array as $v)
			{
				if($js!='')$js.=',';
				$js.='{id:\''.js_escape($v->id).'\',val:\''.js_escape($v->val).'\'}';
			}
			print '$i(\''.js_escape($htmlid).'\').as_objects=['.$js.'];';
		
		exit;*/
	default:
		;
	}
//	$obj=new $classes[$etype];
	if(class_exists($etype))
	{
		$obj=new $etype;
		if(method_exists($obj,'handle_event'))
			$obj->handle_event($event);
	}
	
	exit;
}




function add_new_button($n)
{
	global $page;
	$test=new editor_button;
	$test->name=$n;
	$test->attributes['value']=$n;
	$page->append_child($test);
	$test->bootstrap();
}










$wh=explode('x',isset($_SESSION['encoding_tool_container_resize_postback1'])?$_SESSION['encoding_tool_container_resize_postback1']:'200x30');



$page=new dom_root_print;
$page->title='encoding tool';

$test= new editor_textarea;
$test->name='main';
$test->oid=-1;
$test->args['main']=&$_SESSION['main'];
$test->context['main']['var']='main';
$test->css_style['width']='100%';
$test->css_style['height']='100%';
$test->css_style['border']='1px solid red';
//$test->css_style['margin']='-2px';
//$test->css_style['border']='1px solid green';
$div=new container_resize_postback;
$div->name='encoding_tool_container_resize_postback1';
$div->css_style['width']=$wh[0].'px';
$div->css_style['height']=$wh[1].'px';
$page->append_child($div);

$et= new enclosing_table;
$div->append_child($et);
$et->append_child($test);


//$div->append_child($test);
$test->bootstrap();
$div->bootstrap();

add_new_button('urlENcode');
add_new_button('urlDEcode');
add_new_button('htmlspecialchars');
add_new_button('htmlspecialchars+quotes');
add_new_button('htmlspecialchars_decode');
add_new_button('js_escape');
add_new_button('preg_quote');
add_new_button('sql::esc');
//add_new_button();
add_new_button('stripcslashes');

add_new_button('cp1251->utf8');
add_new_button('utf8->cp1251');
add_new_button('base64_encode');
add_new_button('base64_decode');



$page->inlinescripts[]=<<<aaaa


function init()
{
	chse.callback_uri='?cb=1';
	setInterval('chse.timerch(false);',1000);
}

init();



chse.fetchfuncs.push(function (o)
{
	if(o.objtype=='editor_text')
	return function()
	{
		this.obj.style.backgroundColor='#d0d0ff';
		return encodeURIComponent(this.obj.value);
	};
	if(o.objtype=='editor_checkbox')
		return function()
		{
			return this.obj.checked?1:0;
		}
	return null;
}
);

chse.checkerfuncs.push(function (o)
{
	if(o.objtype=='editor_text')
	{
		if((! o.obj.oldval)&&(o.obj.oldval != '') )
		{
			o.obj['oldval']=o.obj.value;
		}
		return function()
		{
			if(this.obj.oldval==this.obj.value) return false;
			this.obj.oldval=this.obj.value;
			return true;
		}
	}
	if(o.objtype=='editor_checkbox')
		return function()
		{
			return true;
		}
	
	return null;
}
);


//fix middle click paste in opera
function opera_fix(o)
{
		if((! o.oldval)&&(o.oldval != '') )
			o['oldval']=o.value;
}

chse.safe_alert=function(a,b){\$i('debug').value += (b + '\\n ');};





aaaa;

//$page->endscripts[]='setTimeout(\'window.location.reload(true)\',100);';






if($_SESSION['settings_preset']=='')$_SESSION['settings_preset']=0;
$settings_tool=new settings_tool;


$page->for_each_set('oid',-1);
$page->collect_oids($settings_tool);
$page->settings_array=$settings_tool->read_oids($sql);



//$tree_width=$page->setting_val(-1,'tree_width','');
//$tree_height=$page->setting_val(-1,'tree_height','');

$page->styles[]='/css/default.css';
/*
$page->inlinestyles[]=<<<aaaa
body{
font-family:arial;
font-size:16px;
}

input{
border: 1px solid blue;
}



.left{
border:4px solid black;
position:fixed;
overflow:hidden;
top:40px;
}
.left:hover{
border:4px solid red;
position:fixed;
overflow:hidden;
top:40px;
}



aaaa;

*/

//$leftdiv->oid=-1;
$page->after_build();
print $page->html();

	echo microtime(true)-$profiler;echo ":".$sql->querytime;
	print "\n";



?>