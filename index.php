<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
session_start();
//$_SESSION['uid']=1000;

//$_SESSION['uid']=0;
//$_SESSION['sql_design']=false;
$profiler=microtime(true);

require_once('lib/ddc_meta.php');
require_once('lib/dom.php');
require_once('lib/settings.php');
require_once('lib/commctrls.php');
require_once('lib/auth.php');
require_once('lib/uncommctrls.php');
require_once('lib/query_gen.php');
require_once('lib/component_tests.php');
require_once('lib/work_progress.php');
require_once('lib/dev_controls.php');
require_once('lib/test_controls.php');

require_once('lib/barcodes_tmp.php');
//require_once('lib/scanned.php');


class get_parser
{
	function __construct()
	{
		if(isset($_GET['p']))
		{
			$this->path=$path=$_GET['p'];
			if(preg_match('/^[0-9]+$/',$path))//path is oid
			{
				$this->oid=$path;
				$this->path_backend=new path_backend_tree;
				$this->path_backend->path=$path;
	//			die($path);
			}
			else //path is static
			{
				$this->oid=-1;
				$this->path_backend=new path_backend_static;
				$this->path_backend->path=$path;
			}
		}else{
				$this->oid=-1;
				$this->path_backend=new path_backend_static;
		}
		unset($this->keys);
		foreach($_GET as $i =>$v)
			if(preg_match('/^k.*/',$i))//keys
				$this->keys[preg_replace('/^k/','',$i)]=$v;
	}
}


class path_backend_tree
{
}


class path_backend_static
{
	function __construct()
	{
		global $tests_m_array;
		$this->path='home';//default path
		$obj->id='home';
		$obj->val='home';
		$this->add_child('',$obj);unset($obj);
		
		$obj->id='com_tests';
		$obj->val='component tests';
		$this->add_child('home',$obj);unset($obj);
		
		$obj->id='wip_group';
		$obj->val='Work progress';
		$this->add_child('home',$obj);unset($obj);
		
		$obj->id='Progress_viewer';
		$obj->class_n='progress_viewer';
		$obj->val='Progress viewer';
		$this->add_child('wip_group',$obj);unset($obj);
		
		/*
		$obj->id='Progress_viewer';
		$obj->class_n='progress_viewer';
		$obj->val='Progress viewer';
		$this->add_child('wip_group',$obj);unset($obj);*/
		
		$obj->id='test3';
		$obj->val='some moar test';
		$this->add_child('home',$obj);unset($obj);
		$obj->id='test3.0';
		$obj->val='some moar test0';
		$this->add_child('test3',$obj);unset($obj);
		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		if(isset($tests_m_array) && is_array($tests_m_array))
			foreach($tests_m_array as $e)$this->add_test_m($e);
		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			
		$this->add_test('editor_text_test');
		$this->add_test('editor_button_test');
		$this->add_test('editor_checkbox_test');
		$this->add_test('editor_text_submit_test');
		$this->add_test('editor_textarea_test');
		$this->add_test('editor_text_autosuggest_test');
		$this->add_test('editor_text_autosuggest_session_test');
		$this->add_test('editor_container_hidden_test');
		$this->add_test('editor_debugger_test');
		$this->add_test('workers_container_test');
		$this->add_test('workers_container_test1');
		$this->add_test('query_merge_test');
	}
	
	function add_test($n)
	{
		$obj->id=$n;
		$obj->val=$n;
		$obj->class_n=$n;
		$this->add_child('com_tests',$obj);unset($obj);
	}
	
	function add_test_m($n)
	{
		$obj->id=$n;
		$obj->val=$n;
		$obj->class_n=$n;
		$this->add_child('test3',$obj);unset($obj);
	}
	
	/* core functions */
	function getpath($id)
	{
		$ni=$id;
		$out=Array();
		while($ni != '')
		{
			$out[]=$ni;
			$ni=$this->nodes[$ni]->parent;
		}
		return $out;
	}
	function getchildren($id)
	{
		if( ! is_array($this->nodes))return null;
		foreach($this->nodes as $n)if($n->parent==$id)$res[]=$n->id;
		return $res;
	}
	function getnear($id)
	{
		if( ! is_array($this->nodes))return Array();
		if( ! isset($this->nodes[$id]))return Array();
		$nid=$this->nodes[$id]->parent;
		foreach($this->nodes as $n)if(($n->parent==$nid)&&($n->id!=$id))$res[]=$n->id;
		return $res;
	}
	function getclass($id)
	{
		if( ! is_array($this->nodes))return null;
		return $this->nodes[$id]->class_n;
	}
	/*manipulation functions*/
	function add_child($id,$node)
	{
		if($id=='')
		{
			unset($node->parent);
			$this->nodes[$node->id]=$node;
		}else{
			if(! isset($this->nodes[$id]))die('path_backend_static: trying to add child to nonexisting node:'.$id.'. Operation order needs review');
			$node->parent=$id;
			unset($this->nodes[$node->id]);
			$this->nodes[$node->id]=$node;
		}
	}
}










class path_view extends dom_table
{
	function __construct()
	{
		global $path_keys;
		dom_table::__construct();
		if(!isset($path_keys))$path_keys=new get_parser;
		
		$this->tr=new dom_tr;
		$this->append_child($this->tr);
		$this->td=new dom_td;
		$this->tr->append_child($this->td);
		$this->txt=new dom_statictext;
		$this->td->append_child($this->txt);
		
		
	}
	function bootstrap()
	{
	}
	function html_inner()
	{
		global $path_keys;
		$paths=&$path_keys->path_backend;
		foreach($paths->nodes as $p)
		{
			$this->txt->text='';
			$tp=$paths->getpath($p->id);
			for($k=count($tp)-1;$k>=0;$k--)$this->txt->text.='.'.$tp[$k];
			$this->id_alloc();
			if($p->id==$path)
				$this->tr->css_style['background-color']='yellow';
			else
				unset($this->tr->css_style['background-color']);
			dom_table::html_inner();
		}
	}
	

}

class path_view_children extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->tr=new dom_tr;
		$this->append_child($this->tr);
		$this->td=new dom_td;
		$this->tr->append_child($this->td);
		$this->a=new dom_any('a');
		$this->td->append_child($this->a);
		$this->txt=new dom_statictext;
		$this->a->append_child($this->txt);
		
		
	}
	function bootstrap()
	{
	}
	function html_inner()
	{
		global $path_keys;
		$paths=&$path_keys->path_backend;
		$id=&$paths->path;
		$nn=$paths->getchildren($id);
		if(is_array($nn))
			foreach($nn as $p)
			{
				//$this->txt->text=$paths->nodes[$p]->val;
				$this->txt->text=$paths->nodes[$p]->val;
				$this->a->attributes['href']='?p='.$p;
				$this->id_alloc();
				dom_table::html_inner();
			}
	}
	

}

class path_view_control extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		
		global $path_keys;
		dom_table::__construct();
		if(!isset($path_keys))$path_keys=new get_parser;
		
		$this->etype='path_view_control';
		$this->tr=new dom_tr;
		$this->append_child($this->tr);
		$this->td=new dom_td;
		$this->td->css_style['font-size']='smaller';
		$this->tr->append_child($this->td);
		$this->a=new dom_any('a');
		$this->td->append_child($this->a);
		$this->txt=new dom_statictext;
		$this->a->append_child($this->txt);
		editor_generic::addeditor('dd',new editor_pick_button);
		$this->editors['dd']->list_class='path_view_control_dd';
		$this->td->append_child($this->editors['dd']);
		$this->editors['dd']->button->css_style['width']='1.5em';
		$this->editors['dd']->button->css_style['height']='1.5em';
		$this->editors['dd']->button->css_style['font-size']='1em';
		
		
	}
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
	}
	function html_inner()
	{
		global $path_keys;
		$paths=&$path_keys->path_backend;
		$id=&$paths->path;
		$nn=$paths->getpath($id);
		$this->tr->html_head();
		$this->editors['dd']->context=&$this->context;
		if(is_array($nn))
			for($k=count($nn)-1;$k>=0;$k--)
			{
				//$this->txt->text=$paths->nodes[$nn[$k]]->val;
				$this->txt->text=$paths->nodes[$nn[$k]]->val;
				$this->a->attributes['href']='?p='.$nn[$k];
				$this->id_alloc();
				$this->context[$this->long_name.'.dd']['pid']=$nn[$k];
				foreach($this->editors as $e)$e->bootstrap();
				$this->editors['dd']->button->attributes['value']='⇵';
				$this->td->html();
			}
		////
		$c=$paths->getchildren($id);
		if(count($c)>0)
		{
		$this->txt->text='';
		$this->a->attributes['href']='';
		$this->context[$this->long_name.'.dd']['pid']=$id;
		$this->editors['dd']->list_class='path_view_control_cd';
		$this->id_alloc();
		foreach($this->editors as $e)$e->bootstrap();
		$this->editors['dd']->button->attributes['value']='>';
		$this->td->html();
 		}
		
		$this->tr->html_tail();
	}
	
	function handle_event($ev)
	{
		$ev->etype=$etype=preg_replace('/\..*/','',$ev->rem_type);
		
		$ev->parent_type=$ev->parent_type.'.'.$etype;
		$ev->rem_type=preg_replace('/^[^.]*\./','',$ev->rem_type);
		$ev->name=preg_replace('/\..*/','',$ev->rem_name);
		$ev->parent_name.='.'.$ev->name;
		$ev->rem_name=preg_replace('/^[^.]*\./','',$ev->rem_name);
		
		if(class_exists($etype))
		{
			$obj=new $etype;
			if(method_exists($obj,'handle_event'))
				$obj->handle_event($ev);
		}
	}

}

class path_view_control_dd extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		
		global $path_keys;
		dom_table::__construct();
		if(!isset($path_keys))$path_keys=new get_parser;
		
		$this->etype='editor_pick_button';
		$div=new dom_tr;
		$this->append_child($div);
		$td=new dom_td;
		$div->append_child($td);
		$this->a=new dom_any('a');
		$td->append_child($this->a);
		$this->txt=new dom_statictext;
		$this->a->append_child($this->txt);
		
	}
	
	function bootstrap()
	{
		global $path_keys;
		//$this->picklist=$this->context
		$this->path_backend=&$path_keys->path_backend;
		$this->long_name=$long_name=editor_generic::long_name();
		
	}
	
	
	function html_inner()
	{
		
		global $path_keys;
		$paths=&$path_keys->path_backend;
		$id=&$paths->path;
		$n=$this->context[$this->long_name]['pid'];
		$nn=$paths->getnear($n);
		if(is_array($nn))
			foreach($nn as $p)
			{
				$this->txt->text=$paths->nodes[$p]->val;
				//$this->txt->text=$pt->getpath($p);
				$this->a->attributes['href']='?p='.$p;
				$this->id_alloc();
				dom_table::html_inner();
			}
	}
}

class path_view_control_cd extends path_view_control_dd
{
	function html_inner()
	{
		
		global $path_keys;
		$paths=&$path_keys->path_backend;
		$id=&$paths->path;
		$n=$this->context[$this->long_name]['pid'];
		$nn=$paths->getchildren($n);
		if(is_array($nn))
			foreach($nn as $p)
			{
				$this->txt->text=$paths->nodes[$p]->val;
				//$this->txt->text=$pt->getpath($p);
				$this->a->attributes['href']='?p='.$p;
				$this->id_alloc();
				dom_table::html_inner();
			}
	}
}









class locationbar extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='locationbar';
		$this->css_style['width']='97%';
		$this->css_style['margins']='auto';
		$this->css_style['background']='#a0a5b0';
		$this->css_style['border']='2px solid #504560';
		
		
		$logout_div=new dom_div;
		$logout_div->css_style['float']='right';
		//$logout_btn=new editor_button;
		//editor_generic::addeditor('logout',$logout_btn);
		$this->logout_btn=new dom_textbutton;
		$logout_div->append_child($this->logout_btn);
		$this->append_child($logout_div);
		$this->logout_btn->attributes['value']='logout';
		
		if($_SESSION['uid']==0)
		{
			$design_div=new dom_div;
			$design_div->css_style['float']='right';
			$design_sw=new editor_checkbox;
			editor_generic::addeditor('design',$design_sw);
			$design_div->append_child($design_sw);
			$this->append_child($design_div);
			$st=new dom_statictext;
			$st->text='DM';
			$design_div->append_child($st);
		}
		
		$this->pinbtn=$pinbtn=new dom_textbutton;
		$pinbtn->attributes['value']='⇕';
		$pinbtn->css_style['float']='left';
		$this->append_child($pinbtn);
		
		$pk=new path_view_control;
		editor_generic::addeditor('pk',$pk);
		$this->append_child($pk);
	}
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		//$this->editors['logout']->attributes['onclick']='chse.send_or_push({static:\'auth=logout&val=\',val:\'\'});';
		$this->logout_btn->attributes['onclick']='chse.send_or_push({static:\'auth=logout&val=\',val:\'\'});';
		if(isset($this->editors['design']))
		{
			$this->editors['design']->context[$long_name.'.design']['var']='a';
			$this->editors['design']->args['a']=$_SESSION['sqldesign'];
		}
		$this->pinbtn->attributes['onclick']=
		'var a=$i(\''.js_escape($this->id_gen()).'\');'.
		'if(! a)a=$i(\''.js_escape($this->id_gen()).'_movable\');'.
		'if(a.style.position != \'fixed\')'.
		'{'.
			'a.style.position=\'fixed\';'.
			'a.id+=\'_movable\';'.
		'}'.
		'else '.
		'{'.
			'a.style.position=\'\';'.
			'a.id=a.id.replace(/_movable$/,\'\');'.
		'}'.
		''.
		''.
		'';
		
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
		
	}
	function handle_event($ev)
	{
		switch($ev->rem_name)
		{
			//handle root object events here
		case 'design':
			
			$_SESSION['sqldesign']=$_POST['val'];
			print 'window.location.reload(true);';
			break;
			
			$r= new $this->list_class;
			//$r->picklist=unserialize($ev->context[$ev->parent_name]['picklist']);
			//print 'chse.safe_alert(123,\''.$ev->parent_type.'\');';
			//exit;
			$r->etype=$ev->parent_type;
			$r->context=&$ev->context;
			$r->for_each_set('oid',$ev->context[$ev->parent_name]['oid']);
			$r->name=&$ev->parent_name;
			$r->keys=&$ev->keys;
			$r->bootstrap();
			print "var res=\$i('".js_escape($ev->context[$ev->long_name]['res_div'])."');".
			"chse.safe_alert(123,res.style.display);".
			"if(res.style.display!='none')".
			"{".
			"res.style.display='none';".
			"}else{".
			"res.style.display='block';".
			"try{res.innerHTML=".reload_object($r).
			"}catch(e){window.location.reload(true);};};";
			return true;
		default:
			;
		}
		
		editor_generic::handle_event($ev);
	}
}




























if(count($_POST)>0)
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





































$page=new dom_root_print;
$page->title='Коды';





$editor_name=$_GET['p'];
if(!class_exists($editor_name))
	$editor_name='query_result_viewer_codessel';
//$pk=new path_view_control;
$pk=new $editor_name;
$pk->name='pk';

$page->append_child($pk);
$pk->bootstrap();



/*
$b=new dom_any_noterm('input');
$b->attributes['type']='submit';
$b->attributes['onclick']='window.xxx=1;if(window.driven==1){window.close();}else{var w=window.open(window.location.href,\'selector\');w.driven=1;w=window.open(window.location.href,\'selector\');w.driven=1;}';
$page->append_child($b);
*/





$page->inlinescripts[]=<<<aaaa



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




?>
