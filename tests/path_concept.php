<?php
$profiler=microtime(true);
ini_set('memory_limit', '16M');
set_include_path($_SERVER['DOCUMENT_ROOT']);
session_start();
//$_SESSION['uid']=0;
//$_SESSION['sql_design']=false;
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
require_once('lib/component_sandbox.php');
require_once('lib/meta_editor.php');
require_once('lib/table_xml_load.php');
require_once('lib/keyboard-test.php');
require_once('lib/samples_db.php');


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
	function __construct()
	{
		global $sql;
		$this->sql=$sql;
	}
	
	/* core functions */
	function getpath($id)
	{
		$ni=$id;
		$out=Array();
		while($ni != '')
		{
			$out[]=$ni;
			$ni='';//parent
			$ni=$this->sql->fetch1($this->sql->query("SELECT `parentid` FROM `".TABLE_META_TREE."` WHERE `id`='".$ni."'"));
			if($ni==0)break;
		}
		return $out;
	}
	function getchildren($id)
	{
		$res=$this->sql->qv("SELECT `id` FROM `".TABLE_META_TREE."` WHERE `parentid`='".$id."' AND `sql_type`=''");
		if(is_array($res))return $res;
		return null;
	}
	function getnear($id)
	{
		//return array of id's
		$res=$this->sql->qv("SELECT `id` FROM `".TABLE_META_TREE."` WHERE `parentid`=(SELECT `parentid` FROM `".TABLE_META_TREE."` WHERE `id`='".$id."')");
		if(is_array($res))return $res;
		return Array();
	}
	function getclass($id)
	{
		global $sql;
		
		$res=$this->sql->qv("SELECT `editor` FROM `".TABLE_META_TREE."` WHERE `id`='".$id."'");
		if($res[0] != '')return $res[0];
		return 'm_list';
		
	}
	function getval($id)
	{
		$res=$this->sql->qv("SELECT `name` FROM `".TABLE_META_TREE."` WHERE `id`='".$id."'");
		return $res[0];
	}
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
		
		$this->add_r('home',$tests_m_array);
	}
	
	
	function add_r($to,$arr)
	{
		if(is_array($arr))
			foreach($arr as $name => $val)
			{
				unset($obj);
				$obj->id=$name;
				$obj->val=$name;
				if(!is_array($val))
					$obj->class_n=$val;
				$this->add_child($to,$obj);
				if(is_array($val))
					$this->add_r($name,$val);
			}
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
	
	function getval($id)
	{
		return $this->nodes[$id]->val;
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
				$this->txt->text=$paths->getval($p);//nodes[$p]->val;
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
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
		}
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
				$this->txt->text=$paths->getval($nn[$k]);//nodes[$nn[$k]]->val;
				$this->a->attributes['href']='?p='.$nn[$k];
				$this->id_alloc();
				$this->context[$this->long_name.'.dd']['pid']=$nn[$k];
				foreach($this->editors as $e)$e->bootstrap();
				$this->editors['dd']->button->attributes['value']='▼';
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
		$this->editors['dd']->button->attributes['value']='▶';
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
				$this->txt->text=$paths->getval($p);//nodes[$p]->val;
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
				$this->txt->text=$paths->getval($p);//nodes[$p]->val;
				//$this->txt->text=$pt->getpath($p);
				$this->a->attributes['href']='?p='.$p;
				$this->id_alloc();
				dom_table::html_inner();
			}
	}
}



class dom_async_mon extends dom_div
{
	function __construct($text)
	{
		parent::__construct();
		$this->css_style['position']='fixed';
		$this->css_style['left']='50%';
		$this->css_style['top']='20px';
		$this->css_style['background-color']='#FFFF7E';
		$this->css_style['border']='3px solid #666633';
		$this->css_style['visibility']='hidden';
		$this->css_style['z-index']='10000';
		$this->custom_id='async_monitor';
		$this->append_child(new dom_statictext($text));
		$acync_count=new dom_span;
		$this->append_child($acync_count);
		$acync_count->custom_id='async_monitor_count';
	}
}





class locationbar extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='locationbar';
		$this->css_style['width']='97%';
		$this->css_style['margin']='auto';
		$this->css_style['background']='#a0a5b0';
		$this->css_style['border']='2px solid #504560';
		$this->css_style['padding-bottom']='1px';
		
		
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
		$pinbtn->attributes['value']='▲';
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
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
		}
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
			$na=$_GET;
			if($_POST['val']==1)
				$na['init']='init';
			else
				unset($na['init']);
			$u='';
			foreach($na as $k =>$v)
			{
				if($u !='')$u.='&';
				$u.=urlencode($k).'='.urlencode($v);
			}
			print "window.location.href='".js_escape('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$u)."';";
			//print 'window.location.reload(true);';
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
			"try{res.innerHTML=";
			reload_object($r);
			print "}catch(e){window.location.reload(true);};};";
			return true;
		default:
			;
		}
		
		editor_generic::handle_event($ev);
	}
}



























$path_keys=new get_parser;

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
	$event->context=(editor_generic::em?unserialize($_POST['context']):unserialize(gzuncompress(base64_decode($_POST['context']))));
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

function prepare_keyboard($dom_root)
{
	$a=$_SERVER['HTTP_USER_AGENT'];
	//very very BAD
	if(preg_match('/Opera/',$a))
	{
		$dom_root->inlinescripts['keyboard-fix']='window.keyboard_fix=0;';
	}
	if(preg_match('/Firefox/',$a))
	{
		$dom_root->inlinescripts['keyboard-fix']='window.keyboard_fix=1;';
	}
	if(preg_match('/Konqueror/',$a))
	{
		$dom_root->inlinescripts['keyboard-fix']='window.keyboard_fix=2;';
	}
	if(preg_match('/AppleWebKit/',$a))
	{
		$dom_root->inlinescripts['keyboard-fix']='window.keyboard_fix=3;';
	}
	if(preg_match('/MSIE/',$a))
	{
		$dom_root->inlinescripts['keyboard-fix']='window.keyboard_fix=4;';
	}
	
}




class page_developer extends dom_root_print
{
	function __construct()
	{
		global $path_keys,$sql;
		parent::__construct();
		$this->context=Array();
		$this->title='path concept tests';
		$this->endscripts=Array();
		
		
		$locker=new dom_table_x(1,1);
		$txt_div=new dom_div;
		$txt_div->css_style['margin']='auto';
		#$txt_div->css_style['text-align']='center';
		$txt_div->css_style['position']='relative';
		$txt_div->css_style['background-color']='red';
		$txt_div->css_style['opacity']='1';
		$locker->cells[0][0]->append_child($txt_div);
		$txt_div->append_child(new dom_statictext('LOADING'));
		$unlock=new dom_div;
		$locker->cells[0][0]->append_child($unlock);
		$unlock->append_child(new dom_statictext('UNLOCK'));
		$unlock->attributes['onclick']="\$i('".js_escape($locker->id_gen())."').style.display='none';";
		
		$locker->css_style['position']='fixed';
		$locker->css_style['width']='100%';
		$locker->css_style['height']='100%';
		$locker->cells[0][0]->css_style['width']='100%';
		$locker->cells[0][0]->css_style['height']='100%';
		$locker->css_style['opacity']='0.5';
		$locker->css_style['background-color']='white';
		$locker->css_style['z-index']='100000';
		
		$locker->css_style['text-align']='center';
		$this->append_child($locker);
		$lockerid=$locker->id_gen();
		
		$this->append_child(new dom_async_mon('sending:'));
		$this->endscripts[]="\$i('$lockerid').style.display='none';";
		
		
		
		
		//$pk=new path_view_control;
		$pk=new locationbar;
		$pk->name='pk';
		
		$pc=new path_view_children;
		$this->append_child($pk);
		$this->append_child($pc);
		
		$pk->context=&$this->context;
		if($_SESSION['settings_preset']=='')$_SESSION['settings_preset']=0;
		$settings_tool=new settings_tool;
		
		
		$this->for_each_set('oid',-1);
		
		$pk->bootstrap();
		
		$class=$path_keys->path_backend->getclass($path_keys->path);
		if(isset($class))
		{
			$c=new $class;
			$c->name=$class;
			$c->oid=$path_keys->oid;
			$this->append_child($c);
			$c->context=&$this->context;
			$c->bootstrap();
		}
		
		$fill=new dom_div;
		unset($fill->id);
		$fill->css_class='bottom_fill';
		$this->append_child($fill);
		$dbg=new dom_div;
		$dbg->custom_id='debug';
		$this->append_child($dbg);
		
		$this->collect_oids($settings_tool);
		$this->settings_array=$settings_tool->read_oids($sql);
	}
	
}


class page_samples_db extends dom_root_print
{
	function __construct()
	{
		global $path_keys,$sql,$top_fixed_div;
		parent::__construct();
		$this->context=Array();
		$this->title='Образцы';
		$this->endscripts=Array();
		
		
		$locker=new dom_table_x(1,1);
		$txt_div=new dom_div;
		$txt_div->css_style['margin']='auto';
		#$txt_div->css_style['text-align']='center';
		$txt_div->css_style['position']='relative';
		$txt_div->css_style['background-color']='red';
		$txt_div->css_style['opacity']='1';
		$locker->cells[0][0]->append_child($txt_div);
		$txt_div->append_child(new dom_statictext('Загрузка'));
		$unlock=new dom_div;
		$locker->cells[0][0]->append_child($unlock);
		$unlock->append_child(new dom_statictext('разблокировать'));
		$unlock->attributes['onclick']="\$i('".js_escape($locker->id_gen())."').style.display='none';";
		
		$locker->css_style['position']='fixed';
		$locker->css_style['width']='100%';
		$locker->css_style['height']='100%';
		$locker->cells[0][0]->css_style['width']='100%';
		$locker->cells[0][0]->css_style['height']='100%';
		$locker->css_style['opacity']='0.5';
		$locker->css_style['background-color']='white';
		$locker->css_style['z-index']='100000';
		
		$locker->css_style['text-align']='center';
		$this->append_child($locker);
		$lockerid=$locker->id_gen();
		
		$this->append_child(new dom_async_mon('Выполняется запросов:'));
		$this->endscripts[]="\$i('$lockerid').style.display='none';";
		
		
		
		
		//$pk=new path_view_control;
		$pk=new container_autotable;
		$pk->css_style['position']='fixed';
		$pk->css_style['left']='10px';
		$pk->css_style['top']='5px';
		$pk->css_style['height']='2em';
		$pk->css_style['background']='#bbbbbb';
		$pk->css_style['border']='groove black 2px';
		
		$a=new dom_any('a');$a->attributes['href']='?p=samples_db_list';
		$a->append_child(new dom_statictext('Образцы'));
		$pk->append_child($a);

		$a=new dom_any('a');$a->attributes['href']='?p=samples_db_list_1';
		$a->append_child(new dom_statictext('Образцы(вариант)'));
		$pk->append_child($a);
		
		$a=new dom_any('a');$a->attributes['href']='?p=samples_db_list_2';
		$a->append_child(new dom_statictext('Галерея'));
		$pk->append_child($a);
		
		if($_SESSION['interface']=='samples_admin')
		{
			$a=new dom_any('a');$a->attributes['href']='?p=samples_db_users';
			$a->append_child(new dom_statictext('Пользователи'));
			$pk->append_child($a);
		}
		
		$logout=new dom_textbutton;
		$logout->attributes['value']='Выход';
		$logout->attributes['onclick']='chse.send_or_push({static:\'auth=logout&val=\',val:\'\'});';
		
		$top_fixed_div=new dom_div;
		$pk->append_child($top_fixed_div);
		
		$pk->append_child($logout);
		
		$this->append_child($pk);
		
		$pk_ph=new dom_div;
		$this->append_child($pk_ph);
		$pk_ph->css_style['height']='4em';
		
		if($_SESSION['settings_preset']=='')$_SESSION['settings_preset']=0;
		$settings_tool=new settings_tool;
		
		
		$this->for_each_set('oid',-1);
		
		if($path_keys->path=='')$path_keys->path='samples_db_list';
		
		$class=$path_keys->path_backend->getclass($path_keys->path);
		if(isset($class))
		{
			$c=new $class;
			$c->name='samples_db';
			$c->oid=$path_keys->oid;
			$this->append_child($c);
			$c->context=&$this->context;
			$c->bootstrap();
		}
		
		$fill=new dom_div;
		unset($fill->id);
		$fill->css_class='bottom_fill';
		$this->append_child($fill);
		$dbg=new dom_div;
		$dbg->custom_id='debug';
		$this->append_child($dbg);
		
		$this->collect_oids($settings_tool);
		$this->settings_array=$settings_tool->read_oids($sql);
	}
	
}






























if(false){
$page=new dom_root_print;
prepare_keyboard($page);
$page->context=Array();
$page->title='path concept tests';



$locker=new dom_table_x(1,1);
$txt_div=new dom_div;
$txt_div->css_style['margin']='auto';
#$txt_div->css_style['text-align']='center';
$txt_div->css_style['position']='relative';
$txt_div->css_style['background-color']='red';
$txt_div->css_style['opacity']='1';
$locker->cells[0][0]->append_child($txt_div);
$txt_div->append_child(new dom_statictext('LOADING'));
$unlock=new dom_div;
$locker->cells[0][0]->append_child($unlock);
$unlock->append_child(new dom_statictext('UNLOCK'));
$unlock->attributes['onclick']="\$i('".js_escape($locker->id_gen())."').style.display='none';";

$locker->css_style['position']='fixed';
$locker->css_style['width']='100%';
$locker->css_style['height']='100%';
$locker->cells[0][0]->css_style['width']='100%';
$locker->cells[0][0]->css_style['height']='100%';
$locker->css_style['opacity']='0.5';
$locker->css_style['background-color']='white';
$locker->css_style['z-index']='100000';

$locker->css_style['text-align']='center';
$page->append_child($locker);
$lockerid=$locker->id_gen();

$page->endscripts[0]=<<<aaa
		\$i('$lockerid').style.display='none';
aaa;




//$pk=new path_view_control;
$pk=new locationbar;
$pk->name='pk';

$pc=new path_view_children;
$page->append_child($pk);
$page->append_child($pc);

$pk->context=&$page->context;
if($_SESSION['settings_preset']=='')$_SESSION['settings_preset']=0;
$settings_tool=new settings_tool;


$page->for_each_set('oid',-1);

$pk->bootstrap();

$class=$path_keys->path_backend->getclass($path_keys->path);
if(isset($class))
{
	$c=new $class;
	$c->name='tester';
	$c->oid=$path_keys->oid;
	$page->append_child($c);
	$c->context=&$page->context;
	$c->bootstrap();
}

$fill=new dom_div;
unset($fill->id);
$fill->css_class='bottom_fill';
$page->append_child($fill);
$dbg=new dom_div;
$dbg->custom_id='debug';
$page->append_child($dbg);

$page->collect_oids($settings_tool);
$page->settings_array=$settings_tool->read_oids($sql);
}

$sr=$sql->qv("SELECT `interface` FROM `*users` where uid='".$_SESSION['uid']."'");
$_SESSION['interface']=$sr[0];

if($_SESSION['interface']!='')
	$page=new page_samples_db;
else
	$page=new page_developer;
prepare_keyboard($page);
$page->styles[]='/css/default.css';
$page->after_build();
print $page->html();

	echo microtime(true)-$profiler;echo ":".$sql->querytime.':('.$sql->qcnt.")";
	print "\n";



?>