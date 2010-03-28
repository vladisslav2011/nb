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


//$sql->query('drop table `*users`');

if($_SESSION['sql_design'])
{
	if($sql->fetch1($sql->query('select name from `*users` where name=\'root\''))!='root')
	{
	//	die($sql->fetch1($sql->query('select count(uid) where name=\'root\' group by name')));
		$sql->query('insert into `*users` set name=\'root\' , pass=\''.md5('root').'\', isgroup=0,isactive=1');
		$sql->query('update `*users` set uid=0 where name=\'root\'');
	}
	if($sql->fetch1($sql->query('select count(uid) `*users` where name=\'test\''))<=0)
	{
		$sql->query('insert into `*users` set uid=1000, name=\'test\' , pass=\''.md5('password').
		'\', isgroup=0,isactive=1');
	}
}

/*


	ddc_gentable_n(TABLE_META_USERS,
	Array(
		Array('uid','bigint(20)',0,NULL,'auto_increment',NULL),
		Array('name','varchar(64)',0,'dummy',NULL,NULL),
		Array('pass','varchar(64)',0,md5('dummy'),NULL,NULL),
		Array('reflink','bigint(20)',1,NULL,NULL,NULL),
		Array('isgroup','tinyint(1)',0,0,NULL,NULL),
		Array('isactive','tinyint(1)',0,0,NULL,NULL)
	),
	Array(
		Array('PRIMARY','uid',NULL)
		
	),$sql);
	
	settings: users.sort
	settings: users.group
	settings: users.filters
*/

class users_main_list extends dom_div
{
	/*
	<div>
		<div>
		
		</div>
		<div>
		<table>
		</div>
	
	</div>
	*/
	function __construct()
	{
		dom_div::__construct();
		$this->etype='users_main_list';
		editor_generic::addeditor('tabs',new container_tab_control);
		$controls=$this->editors['tabs'];
		
		$this->append_child($controls);
		$controls->add_tab('filters','filters');
		$controls->add_tab('sort','sort');
		editor_generic::addeditor('sort',new users_main_list_sort);
		editor_generic::addeditor('filters',new users_main_list_where);
		$controls->tabs['filters']->div->append_child($this->editors['filters']);
		$controls->tabs['sort']->div->append_child($this->editors['sort']);
		$div=new dom_div;
		editor_generic::addeditor('apply',new editor_button);
		$div->append_child($this->editors['apply']);
		$this->editors['apply']->attributes['value']='apply';
		$controls->append_child($div);
		$table=new dom_div;
		$this->append_child($table);
		editor_generic::addeditor('rows',new users_main_list_rows);
		
		$this->txt=new dom_statictext;
		$dbgdiv=new dom_div;
		$dbgdiv->css_style['border']='1px solid green';
		$dbgdiv->css_style['background-color']='#D0FFD0';
		$dbgdiv->append_child($this->txt);
		$table->append_child($dbgdiv);
		
		$table->append_child($this->editors['rows']);
		
	}
	
	function bootstrap()
	{
		
		$long_name=editor_generic::long_name();
		$controls->name=$long_name;
		$cols=Array('uid','name','pass','reflink','isgroup','isactive');
		$this->context[$long_name]['htmlid']=$this->id_gen();
		$this->context[$long_name]['oid']=$this->oid;
		$this->context[$long_name.'.sort']['picklist']=serialize($cols);
		$this->context[$long_name.'.sort']['settingid']=$long_name.'.sort';
		$this->context[$long_name.'.filters']['picklist']=serialize($cols);
		$this->context[$long_name.'.filters']['settingid']=$long_name.'.filters';
		foreach($this->editors as $e)
		{
			$e->oid=$this->oid;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->context=&$this->context;
		}
		$this->editors['rows']->cols=&$cols;
		
		
	}
	
	function html_inner()
	{
		$long_name=editor_generic::long_name();
		$cols=Array('uid','name','pass','reflink','isgroup','isactive');
		$sort_l=unserialize($this->rootnode->setting_val($this->oid,$long_name.'.sort',serialize(Array())));
		$filt_l=unserialize($this->rootnode->setting_val($this->oid,$long_name.'.filters',''));
		
		$qg=new query_gen;
		$qg->add('from','`'.TABLE_META_USERS.'`');
		foreach($cols as $c)$qg->add('what','`'.$c.'`');
		if(count($filt_l->exprs)>0)$qg->add('where',$filt_l);
		foreach($sort_l as $c)
		{
			if($c->dir==1)
			{
				$sl=new sql_logical;
				$sl->add($c->col);
				$sl->add('desc');
				$qg->add('order',$sl);
			}else
				$qg->add('order',$c->col);
		}
		$this->txt->text=$qg->result();
		$this->editors['rows']->sql_query=$qg->result();
		foreach($this->editors as $e)$e->bootstrap();
		parent::html_inner();
	}
	
	
	function handle_event($ev)
	{
		global $sql;
		//exit;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$settingid=$ev->context[$ev->parent_name]['settingid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		//foreach($ev->context[$ev->parent_name] as $a)print "chse.safe_alert(123,'".$a."');"; //debug
		if(! isset($oid))$oid=-1;
		switch($ev->rem_name)
		{
			//handle root object events here
		case 'apply':
			$r= new users_main_list;
			$r->context=$ev->context;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->oid=$oid;
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r,true).
									"}catch(e){ window.location.reload(true);};";
			print "chse.safe_alert(123,'".js_escape($js_escape_time)."');";
			return true;
		default:
			;
		}
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













class users_main_list_sort extends dom_table
{
	function __construct()
	{
		//div(span(text(descr))span(checkbox(dir)text('321'))button(del)button(add_after :unhides available)div(available :hidden))
		//button(add_end)
		dom_table::__construct();
		//$this->repeating=new users_main_list_sort_item;
		$this->etype='users_main_list_sort';
		
		$repeating=new dom_tr;
		$this->append_child($repeating);
		$this->repeating=$repeating;
		
		$descr_div=new dom_td;
		$repeating->append_child($descr_div);
		
		editor_generic::addeditor('descrtext',new editor_text);
		$descr_div->append_child($this->editors['descrtext']);
		
		$cb=new dom_td;
		$repeating->append_child($cb);
		
		editor_generic::addeditor('dir_checkbox',new editor_checkbox);
		$cb->append_child($this->editors['dir_checkbox']);
		
		$txt=new dom_statictext;
		$txt->text='321';
		$cb->append_child($txt);
		
		$btns=new dom_td;
		$repeating->append_child($btns);
		editor_generic::addeditor('delbtn',new editor_button);
		$btns->append_child($this->editors['delbtn']);
		$this->editors['delbtn']->attributes['value']='-';
		
//		editor_generic::addeditor('addbtn',new editor_pick_button_static);
		editor_generic::addeditor('addbtn',new editor_pick_button);
		$btns->append_child($this->editors['addbtn']);
		$this->editors['addbtn']->attributes['value']='add';
		
		$this->en=new dom_tr;
		$this->append_child($this->en);
		$et=new dom_td;
		$this->en->append_child($et);
		$et->attributes['colspan']=3;
		
//		editor_generic::addeditor('add_end',new editor_pick_button_static);
		editor_generic::addeditor('add_end',new editor_pick_button);
		$et->append_child($this->editors['add_end']);
		$this->editors['add_end']->name='add_end';
		
	
	}
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		//die($long_name);
		
		
		//in:Array(Object(col,dir))
		$this->context[$long_name]['htmlid']=$this->id_gen();
		reset($this->editors);
		foreach($this->editors as $e)
		{
			$e->context= & $this->context;
			$e->args= &$this->args;
			$e->keys= &$this->keys;
		}
		$this->for_each_set('oid',$this->oid);
		$this->context[$long_name.'.descrtext']['var']='users.sort.col';
		$this->context[$long_name.'.dir_checkbox']['var']='users.sort.dir';
		$this->context[$long_name.'.dir_checkbox']['event']='users_main_list_sort.dir';
		$picklist=$this->context[$long_name]['picklist'];
		$this->context[$long_name.'.add_end']['picklist']=	&$picklist;
		$this->context[$long_name.'.addbtn']['picklist']=	&$picklist;
		
		
		//$this->context[$long_name.'.add_end']['buttons']=	serialize(Array('btnforw'=>'123','btnrev'=>'321'));
		//$this->context[$long_name.'.addbtn']['buttons']=	serialize(Array('btnforw'=>'123','btnrev'=>'321'));
		
	}
	
	function html_inner()
	{
		$this->in=unserialize($this->rootnode->setting_val($this->oid,$this->context[editor_generic::long_name()]['settingid'],serialize(Array())));
		reset($this->in);
		$p=0;
		foreach($this->in as $r)
		{
			$this->repeating->id_alloc();
			$this->keys['col']=$p;
			$this->args['users.sort.col']=$r->col;
			$this->args['users.sort.dir']=$r->dir;
			foreach($this->editors as $e)
			{
				$e->bootstrap();
			}
			$this->repeating->html();
			$p++;
		}
		unset($this->keys);
		foreach($this->editors as $e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->bootstrap();
		}
		$this->en->html();
	}
	
	function handle_event($ev)
	{
		global $sql;
		print "chse.safe_alert(123,'".$ev->rem_name."');";
		//exit;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$settingid=$ev->context[$ev->parent_name]['settingid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		if(! isset($oid))$oid=-1;
		switch($ev->rem_name)
		{
			//handle root object events here
		
		
		
		case 'addbtn.btnforw':
		case 'add_end.btnforw':
			if(!isset($item->dir))$item->dir=0;
		case 'addbtn.btnrev':
		case 'add_end.btnrev':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			if($items)$arr=unserialize($items);
			else $arr=Array();
			if(!isset($item->dir))$item->dir=1;
			$item->col=$_POST['val'];
			//unset($arr[$item->col]);
			foreach($arr as $i => $v)
				if($v->col==$_POST['val'])unset($arr[$i]);
			$new=Array();
			$p=0;
			if(is_array($ev->keys))
				foreach($arr as $i => $v)
				{
					if($i==(int)$ev->keys['col'])
					{
						$new[]=$item;
						$new[]=$v;
					}else $new[]=$v;
					$p++;
				}
			if(!is_array($ev->keys))
			{
				$arr[]=$item;
				$new=$arr;
			}
			$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($new)));
			$r= new users_main_list_sort;
			$r->context=$ev->context;
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->custom_id=$customid;
			$r->bootstrap();
			//$res=reload_object($r);
			//print 'chse.safe_alert(123,\''.js_escape($customid).'\');';
			//print 'var a=$i(\'debug\');a.value=\''.js_escape($customid).'\');';
			//print "\$i('".js_escape($customid)."').innerHTML=".reload_object($r,true);
			//print 'chse.safe_alert(123,\''.js_escape($res).'\');';
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r,true).
									"}catch(e){ window.location.reload(true);};";
			return true;
		case 'delbtn':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			if($items)$arr=unserialize($items);
			else exit;
			$new=Array();
			foreach($arr as $i => $v)
				if($i!=(int)$ev->keys['col'])$new[]=$v;
			$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($new)));
			$r= new users_main_list_sort;
			$r->context=$ev->context;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->oid=$oid;
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r,true).
									"}catch(e){ window.location.reload(true);};";
			print "chse.safe_alert(123,'".js_escape($js_escape_time)."');";
			return true;
		case 'dir_checkbox':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			if($items)$arr=unserialize($items);
			else exit;
			if(!is_array($ev->keys))return true;
			$arr[(int)$ev->keys['col']]->dir=$_POST['val'];
			$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($arr)));
			return true;
		case 'descrtext':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			if($items)$arr=unserialize($items);
			else $arr=Array();
			if(is_array($ev->keys))
			{
				foreach($arr as $i => $v)
					if($v->col==$_POST['val'])
					{
						print '$i(\''.$ev->context[$ev->long_name]['htmlid'].'\').style.backgroundColor=\'red\';';
						return;
					}
				$arr[(int)$ev->keys['col']]->col=$_POST['val'];
			}
			if(!is_array($ev->keys))
			{
				print 'alert(\'error! Ask developers.\');';
			}
			$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($arr)));
			break;
			$r= new users_main_list_sort;
			$r->context=$ev->context;
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r,true).
									"}catch(e){ window.location.reload(true);};";
			return true;
		
			
		default:
			;
		}
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















/*
class sql_logical
{
	function __construct($op='AND')
	{
		$this->op=$op;
	}
	
	function __clone()
	{
		if(is_array($this->exprs))foreach($this->exprs as $k => $e)if(is_object($e))$this->exprs[$k]=clone $e;
	}
	
	function add($expr)
	{
		$this->exprs[]=$expr;
	}
	function result()
	{
		$res='';
		if(! is_array($this->exprs))return $res;
		foreach($this->exprs as $e)
		{
			if($res != '')$res .= " ".$this->op." ";
			if(is_object($e))
				$res .= "(".$e->result().")";
			else
				$res .= $e;
		}
		return $res;
	}
}
*/

class sql_logical_editor
{
	function change_op($obj,$path,$op)
	{
		if($path=='')
		{
			$obj->op=$op;
			return;
		}
		if(preg_match('/.*\/.*/',$path))
		{
			$thisid=preg_replace('/\/.*/','',$path);
			$rempath=preg_replace('/^[0-9]\//','',$path);
		}else{
			$obj->exprs[$path]->op=$op;
			return;
		}
		sql_logical_editor::change_op($obj->exprs[$thisid],$rempath,$op);
	}
	function add(&$obj,$path,$new)
	{
		if($path=='')
		{
			$obj->exprs[]=$new;
			return;
		}
		if(preg_match('/.*\/.*/',$path))
		{
			$thisid=preg_replace('/\/.*/','',$path);
			$rempath=preg_replace('/^[0-9]\//','',$path);
		}else{
			$thisid=$path;
			$rempath='';
		}
		sql_logical_editor::add($obj->exprs[$thisid],$rempath,$new);
	}
	
	function change(&$obj,$path,$new)
	{
		if($path==='')return;
		if(preg_match('/.*\/.*/',$path))
		{
			$thisid=preg_replace('/\/.*/','',$path);
			$rempath=preg_replace('/^[0-9]\//','',$path);
		}else{
			$obj->exprs[$path]=$new;
			return;
		}
		sql_logical_editor::change($obj->exprs[$thisid],$rempath,$new);
	}
	
	function del(&$obj,$path)
	{
		if($path=='')
		{
			unset($obj->exprs);
			//$obj->exprs[]=Array();
			return;
		}
		if(preg_match('/.*\/.*/',$path))
		{
			$thisid=preg_replace('/\/.*/','',$path);
			$rempath=preg_replace('/^[0-9]\//','',$path);
		}else{
			$nexprs=Array();
			foreach($obj->exprs as $i => $k)
				if($i != $path) $nexprs[]=$k;
			$obj->exprs=$nexprs;
			return;
		}
		sql_logical_editor::del($obj->exprs[$thisid],$rempath);
	}
	function get($obj,$path)
	{
		if($path==='')return $obj;
		if(preg_match('/.*\/.*/',$path))
		{
			$thisid=preg_replace('/\/.*/','',$path);
			$rempath=preg_replace('/^[0-9]\//','',$path);
		}else{
			return $obj->exprs[$path];
		}
		return sql_logical_editor::get($obj->exprs[$thisid],$rempath);
	}
	
}



class users_main_list_where extends dom_div
{
	function __construct()
	{
		//div(span(text(descr))span(checkbox(dir)text('321'))button(del)button(add_after :unhides available)div(available :hidden))
		//button(add_end)
		dom_div::__construct();
		$this->etype='users_main_list_where';
		//$this->repeating=new users_main_list_sort_item;
		editor_generic::addeditor('node',new users_main_list_where_node);
		$this->append_child($this->editors['node']);
		
	
	}
	function bootstrap()
	{
		//in:Array(Object(col,dir))
		$long_name=editor_generic::long_name();
		//$this->context[$long_name.'.node']['settingid']=$this->context[$long_name]['settingid'];
		$this->editors['node']->context=&$this->context;
		$this->context[$long_name]['htmlid']=$this->id_gen();
		//$this->editors['node']->in=unserialize($this->rootnode->setting_val($this->oid,$this->context[$long_name]['settingid'],serialize(new sql_logical)));
//		$this->editors['node']->bootstrap();
		
		
	}
	function html_inner()
	{
		$long_name=editor_generic::long_name();
		$this->editors['node']->in=unserialize($this->rootnode->setting_val($this->oid,$this->context[$long_name]['settingid'],serialize(new sql_logical)));
//		die($this->context[$long_name]['settingid']);
		$this->editors['node']->bootstrap();
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		global $sql;
		print "chse.safe_alert(123,'".$ev->parent_name."');";
		//exit;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$settingid=$ev->context[$ev->parent_name]['settingid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		if(! isset($oid))$oid=-1;
		switch($ev->rem_name)
		{
			//handle root object events here
		
		
		
		case 'node.add_new':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			if($items)$arr=unserialize($items);
			else $arr=new sql_logical;
			
			
			sql_logical_editor::add($arr,$ev->keys['path'],new sql_logical);
			
			
			$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($arr)));
			
			
			$r= new users_main_list_where;
			$r->context=$ev->context;
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r,true).
									"}catch(e){ window.location.reload(true);};";
			print "chse.safe_alert(111,'".$ev->parent_name."');";
			return true;
		case 'node.del_this':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			if($items)$arr=unserialize($items);
			else $arr=new sql_logical;
			
			//if($ev->keys['path']==='')return;
			sql_logical_editor::del($arr,$ev->keys['path']);
			
			
			$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($arr)));
			
			
			$r= new users_main_list_where;
			$r->context=$ev->context;
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r,true).
									"}catch(e){ window.location.reload(true);};";
			return true;
		case 'node.change':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			if($items)$arr=unserialize($items);
			else $arr=new sql_logical;
			
			//if($ev->keys['path']==='')return;
			if(is_object(sql_logical_editor::get($arr,$ev->keys['path'])))
				$new='';
			else
				$new=new sql_logical;
			sql_logical_editor::change($arr,$ev->keys['path'],$new);
			
			
			$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($arr)));
			
			
			$r= new users_main_list_where;
			$r->context=$ev->context;
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r,true).
									"}catch(e){ window.location.reload(true);};";
			return true;
		case 'node.text':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			unset($arr);
			if($items)$arr=unserialize($items);
			if(isset($arr))
			{
				$new=$_POST['val'];
				sql_logical_editor::change($arr,$ev->keys['path'],$new);
				$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($arr)));
			}
			break;
			//return true;
		case 'node.typemark.pl':
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			unset($arr);
			if($items!='')$arr=unserialize($items);
			if(isset($arr))
			{
				$new=$_POST['val'];
				switch($new)
				{
				case '||':
					$new='OR';break;
				case '&&':
					$new='AND';break;
				}
				sql_logical_editor::change_op($arr,$ev->keys['path'],$new);
				$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($arr)));
			}
			return true;
		default:
			;
		}
		$ev->etype=$etype=preg_replace('/\..*/','',$ev->rem_type);
		
		$ev->parent_type=$ev->parent_type.'.'.$etype;
		$ev->rem_type=preg_replace('/^[^.]*\./','',$ev->rem_type);
		$ev->name=preg_replace('/\..*/','',$ev->rem_name);
		$ev->parent_name.='.'.$ev->name;
		$ev->rem_name=preg_replace('/^[^.]*\./','',$ev->rem_name);
		
		if(class_exists($ev->etype))
		{
			$obj=new $etype;
			if(method_exists($obj,'handle_event'))
				$obj->handle_event($ev);
		}
	}
	
}

class users_main_list_where_node extends dom_any
{
	function __construct()
	{
		dom_any::__construct('table');
		$this->etype='users_main_list_where_node';
		$this->path='';
		$this->attributes['border']=1;
		$this->css_style['border-collapse']='collapse';
		//string view
		$this->string_view=new editor_text;
		editor_generic::addeditor('text',$this->string_view);
		$this->append_child($this->string_view);
		//tree view
		$this->firstrow=new dom_tr;
		$this->append_child($this->firstrow);
		
		$this->typemark=new dom_td;
		$this->firstrow->append_child($this->typemark);
		
		$this->tree_type=new editor_pick_button_static;
		$this->tree_type->picklist=Array('&&','||','=','!=','<','>','');
		$this->tree_type->buttons=Array('pl'=>'>>');
		
		editor_generic::addeditor('typemark',$this->tree_type);
		$this->typemark->append_child($this->tree_type);
		
		$this->col2=new dom_td;
		//$img=new dom_img;
		//$img->src=FILE_PLACEMENT.'/i/vbar.png';
		//$this->col2->append_child($img);
		$this->firstrow->append_child($this->col2);
		
		$this->tree_editors=new dom_td;
		editor_generic::addeditor('add_new',new editor_button);
		$this->editors['add_new']->attributes['value']='[+]';
		$this->tree_editors->append_child($this->editors['add_new']);
		
		editor_generic::addeditor('del_this',new editor_button);
		$this->editors['del_this']->attributes['value']='[-]';
		$this->tree_editors->append_child($this->editors['del_this']);
		
		$this->firstrow->append_child($this->tree_editors);
		$this->tree_editors->attributes['colspan']=2;
		//$td=new dom_td;
		//$this->firstrow->append_child($td);
		
		
		$this->nextrow=new dom_tr;
		$this->append_child($this->nextrow);
		
		$this->child_node=new dom_td;
		$this->nextrow->append_child($this->child_node);
		
		$this->loop=new users_main_list_where_loopback;
		$this->loop->loop=&$this;
		$this->child_node->append_child($this->loop);
		
		$this->rowbuttons=new dom_td;
		$this->nextrow->append_child($this->rowbuttons);
		
		editor_generic::addeditor('change',new editor_button);
		$this->editors['change']->attributes['value']='ab/|&';
		$this->rowbuttons->append_child($this->editors['change']);
		
		//expression view
		
	}
	
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		reset($this->editors);
		foreach($this->editors as $e)
		{
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->context=&$this->context;
		}
		$this->context[$long_name.'.text']['var']='text';

	}
	
	function html()
	{
		$this->r($this->in,$this->path);
		//$this->r($this->in,$this->path);
	}
	
	function r($in,$path)
	{
		if(is_object($in))
		{
			$this->keys['path']=$path;
			//$path=$this->path;
			$npath=$path;
			if($npath!='')$npath.='/';
			
			$this->typemark->attributes['rowspan']=	count($in->exprs)+1;
			$this->col2->attributes['rowspan']=	count($in->exprs)+1;
			$this->html_head();
			switch($in->op)
			{
			case 'AND':
				$mark='&&';
				break;
			case 'OR':
				$mark='||';
				break;
			case '=':
				$mark='=';
				break;
			default:
				$mark=$in->op;
				break;
			}
			foreach($this->editors as $e)
			{
				$e->keys=&$this->keys;
				$e->bootstrap();
			}
			$this->tree_type->button->attributes['value']=$mark;
			$this->firstrow->html();
			if(is_array($in->exprs) && count($in->exprs)>0)
				foreach($in->exprs as $i => $n)
				{
					$this->loop->path=$npath.$i;
					$this->loop->in=$n;
					$this->id_alloc();
					$this->keys['path']=$path;
					$this->nextrow->html();
				}
			$this->html_tail();
			
		}else{
		//text
		$this->args['text']=$in;
		$this->keys['path']=$path;
		foreach($this->editors as $e)$e->bootstrap();
		$this->string_view->html();
		$this->editors['del_this']->html();
		
		}
	}
	
	function handle_event($ev)
	{
		global $sql;
		$ev->etype=$etype=preg_replace('/\..*/','',$ev->rem_type);
		
		$ev->parent_type=$ev->parent_type.'.'.$etype;
		$ev->rem_type=preg_replace('/^[^.]*\./','',$ev->rem_type);
		$ev->name=preg_replace('/\..*/','',$ev->rem_name);
		$ev->parent_name.='.'.$ev->name;
		$ev->rem_name=preg_replace('/^[^.]*\./','',$ev->rem_name);
		
		if(class_exists($ev->etype))
		{
			$obj=new $etype;
			if(method_exists($obj,'handle_event'))
				$obj->handle_event($ev);
		}
	}
	
}

class users_main_list_where_loopback extends dom_div
{
	function html()
	{
		if(is_object ($this->loop))
		{
			//$this->loop->r->id_alloc();
			$this->loop->r($this->in,$this->path);
			//$this->loop->rootnode->out($this->path);
			return;
			$in=$this->loop->in;
			$path=$this->loop->path;
			$this->loop->path=$this->path;
			$this->loop->in=$this->in;
			$this->loop->id_alloc();
			$this->loop->bootstrap();
			$this->loop->html();
			$this->loop->path=$path;
			$this->loop->in=$in;
			$this->loop->bootstrap();
		}else die('users_main_list_where_loopback with empty loop');
	}
}




class users_main_list_rows extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->etype='users_main_list_rows';
		$this->css_style['border-collapse']='collapse';
		$this->tr=new dom_tr;
		$this->append_child($this->tr);
		$this->captions=new dom_tr;
		$this->append_child($this->tr);
		/*$this->editor_types['uid']='editor_text_submit';
		$this->editor_types['name']='editor_text';
		$this->editor_types['pass']='editor_pass';
		$this->editor_types['isgroup']='editor_checkbox';
		$this->editor_types['isactive']='editor_checkbox';
		*/
	}
	
	
	function bootstrap()
	{
		
		$long_name=editor_generic::long_name();
		$this->context[$long_name]['htmlid']=$this->id_gen();
		$this->context[$long_name]['oid']=$this->oid;
		unset($this->editors);
		unset($this->td->nodes);
		foreach($this->cols as $c)
		{
			$this->td=new dom_td;
			$this->tr->append_child($this->td);
			$this->td->css_style['border']='1px solid red';
			if(isset($this->editor_types[$c]))
				$e_type=$this->editor_types[$c];
			else
				$e_type='editor_statictext';
			$e=new $e_type;
			$e->css_style['width']='55px';
			editor_generic::addeditor($c,$e);
			$this->td->append_child($e);
			$e->oid=$this->oid;
			$e->keys=$this->keys;
			$e->context=&$this->context;
			$e->args=&$this->args;
			$this->context[$long_name.'.'.$c]['var']=$c;
			
			$capt=new dom_td;
			$capt_text=new dom_statictext;
			$capt_text->text=$c;
			$this->captions->append_child($capt);
			$capt->append_child($capt_text);
			$this->append_child($this->captions);
		}
		
		
	}
	
	function html_inner()
	{
		global $sql;
		$long_name=editor_generic::long_name();
/*		foreach($this->cols as $c)
		{
			$this->args[$c]=$c;
		}
		foreach($this->editors as $e)$e->bootstrap();
		$this->td->id_alloc();
		$this->tr->html();*/
		$this->captions->html();
		$res=$sql->query($this->sql_query);
		foreach($this->editors as $e)$e->bootstrap();
		while($this->args=$sql->fetcha($res))
		{
			$this->tr->id_alloc();
			foreach($this->editors as $e)$e->bootstrap();
			$this->tr->html();
		}
		
		
		return;
		$cols=Array('uid','name','pass','reflink','isgroup','isactive');
		$sort_l=unserialize($this->rootnode->setting_val($this->oid,$long_name.'.sort',serialize(Array())));
		$filt_l=unserialize($this->rootnode->setting_val($this->oid,$long_name.'.filters',''));
		
		$qg=new query_gen;
		$qg->add('from','`'.TABLE_META_USERS.'`');
		foreach($cols as $c)$qg->add('what','`'.$c.'`');
		$qg->add('where',$filt_l);
		foreach($sort_l as $c)
		{
			if($c->dir==1)
			{
				$sl=new sql_logical;
				$sl->add('`'.$c->col.'`');
				$sl->add('desc');
				$qg->add('order',$sl);
			}else
				$qg->add('order','`'.$c->col.'`');
		}
		$this->txt->text=$qg->result();
		foreach($this->editors as $e)$e->bootstrap();
		parent::html_inner();
	}
	
	
	function handle_event($ev)
	{
		global $sql;
		//exit;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$settingid=$ev->context[$ev->parent_name]['settingid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		//foreach($ev->context[$ev->parent_name] as $a)print "chse.safe_alert(123,'".$a."');"; //debug
		switch($ev->rem_name)
		{
			//handle root object events here
/*		case 'apply':
			$r= new users_main_list;
			$r->context=$ev->context;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->oid=$oid;
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r,true).
									"}catch(e){ window.location.reload(true);};";
			return true;*/
		default:
			;
		}
		$ev->etype=$etype=preg_replace('/\..*/','',$ev->rem_type);
		
		$ev->parent_type=$ev->parent_type.'.'.$etype;
		$ev->rem_type=preg_replace('/^[^.]*\./','',$ev->rem_type);
		$ev->name=preg_replace('/\..*/','',$ev->rem_name);
		$ev->parent_name.='.'.$ev->name;
		$ev->rem_name=preg_replace('/^[^.]*\./','',$ev->rem_name);
		
//		print "chse.safe_alert(123,'".js_escape('dammn')."');";
		if(class_exists($etype))
		{
			$obj=new $etype;
			if(method_exists($obj,'handle_event'))
				$obj->handle_event($ev);
		}
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
	$event->val=$_POST['val'];
	if(isset($_POST['last_generated_id']))$idcounter=$_POST['last_generated_id'];
	switch($long_name)
	{
	//handle root object events here
	case 'add500':
	case 'add100':
		$cnt=500;
		if($long_name=='add100')$cnt=100;
		for($k=0;$k<$cnt;$k++)
		{
			$n=md5(rand(0,3453523453));
			$sql->query('insert into `*users` set name=\''.$sql->esc($n).'\',pass=\''.$sql->esc(md5($n)).
			'\',isgroup='.rand(0,1).',isactive='.rand(0,1));
		}
		print 'window.location.reload(true)';
		exit;
	case 'toggle_sql_design':
		$_SESSION['sql_design']=$_POST['val'];
		exit;
	case 'reinit_table':
		$sql->query('drop table `*users`');
		$_SESSION['sql_design']=1;
		print 'window.location.reload(true)';
		exit;
	case 'test_editor':
		$_SESSION['eeee']=$_POST['val'];print 'alert(\''.js_escape($_POST['val']).'\');';
		exit;
	case 'ast1':
			
			break;
			$customid=$event->context[$long_name]['retid'];
			$oid=$event->context[$long_name]['oid'];
			$htmlid=$event->context[$long_name]['htmlid'];
			//print chse
			$list_class=$event->context[$event->long_name]['list_class'];
			$r= new $list_class;
			$r->context=$ev->context;
			$r->input_part=$_POST['val'];
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->text_inp=$htmlid;
			//$r->list_items=Array('fdve','egferf','wefer','fewrgf','qewfdsd','qwefrwqgf','wrfgwqrg','fg32',1234,435,1234234,6345745,75476,34,4352136,734785784,5,724,63,465,237,34);
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
			print '$i(\''.js_escape($htmlid).'\').as_id=null;';
			
		
		exit;
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
$page->title='dbmanage tests';

$obj='users_main_list';
//$order_test= new users_main_list_sort;
$test= new $obj;
$test->name='users_main_list';
$test->oid=-1;
//$test->css_style['border']='1px solid green';
$div=new dom_div;
$page->append_child($div);
$div->append_child($test);
//$page->append_child($order_test);
$test->bootstrap();


///////// debug buttons
$test=new editor_button;
$test->name='add500';
$test->attributes['value']='add500';
$page->append_child($test);
$test->bootstrap();

$test=new editor_button;
$test->name='add100';
$test->attributes['value']='add100';
$page->append_child($test);
$test->bootstrap();

$test=new editor_checkbox;
$test->name='toggle_sql_design';
$test->context['toggle_sql_design']['var']='sd';
$test->args['sd']=$_SESSION['sql_design'];
$page->append_child($test);
$test->bootstrap();
$test=new dom_statictext;
$test->text='toggle_sql_design ';
$page->append_child($test);

$test=new editor_button;
$test->name='reinit_table';
$test->attributes['value']='reinit_table';
$page->append_child($test);
$test->bootstrap();
///////// debug buttons















$ast=new editor_text_autosuggest;
$div=new dom_div;
$div->append_child($ast);
$page->append_child($div);
$ast->name='ast1';
$ast->oid=-1;
//$ast->
$ast->bootstrap();







$sh=new dom_div;
$sh->custom_id='div_resize_style';
$sh->css_class='ftt';
//$sh->css_style['width']='400px';
//$sh->css_style['height']='40px';
$page->append_child($sh);
$tb=new dom_table;
$tb->css_class='mzf';
$sh->append_child($tb);
$tr=new dom_tr;
$tb->append_child($tr);
$td1=new dom_td;
$tr->append_child($td1);
$td2=new dom_td;
$tr->append_child($td2);
$td3=new dom_td;
$tr->append_child($td3);
//$td2->css_style['width']='22px';
//$td3->css_style['width']='22px';
$td2->css_class='w22px';
$td3->css_class='w22px';
$btn=new dom_any('textarea');
$td1->append_child($btn);
//$btn->css_style['width']='100%';
//$btn->css_style['margin-right']='-22px';
//$btn->css_style['height']='100%';
$btn=new dom_textbutton;
$td2->append_child($btn);
//$btn->css_style['width']='20px';
//$btn->css_style['height']='100%';
$btn=new dom_div;
$td3->append_child($btn);
$btn->css_style['border']='1px solid black';
$btn->css_style['background']='yellow';
$btn->css_style['width']='20px';
$btn->css_style['height']='100%';
$btn->css_style['overflow']='hidden';
//$btn->attributes['onmouseover']='this.style.height=\'121%\';';
//$btn->attributes['onmouseout']='this.style.height=\'100%\';';
$txt=new dom_statictext;
$txt->text=' ';
$btn->append_child($txt);


$page->inlinestyles[]=<<<aaaa

.ftt
{
width:400px;
height:30px;
padding:1px;
border:3px solid black;
}

table.mzf{
width:100%;
height:100%;
border:0px;
border-collapse:collapse;
}

table.mzf tr{
height:100%;
}

table.mzf tr td{
padding:2px;
margin:0px;
height:99%;
}

td.w22px{
padding:2px;
height:25px;
width:22px;
background:#FFF0FF url(/2.png);
}


table.mzf tr td input{
width:80%;
height:25px;
margin:auto;
display:block;
background-image: url(/3.png);
background-repeat:no-repeat;
background-color:rgb(200,200,200);
background-position:top right;
min-height:1px;
}

table.mzf tr td textarea{
width:98%;
height:98%;
border:1px solid blue;
margin:auto;
min-height:1px;
display:block;
overflow:auto;
}
aaaa;








$test=new editor_text_submit;
$test->name='test_editor';
//$test->attributes['value']='reinit_table';
$page->append_child($test);
$test->context['test_editor']['var']='eeee';
$test->args['eeee']=$_SESSION['eeee'];
$test->bootstrap();









//test order

$obj='users_main_list_sort';
//$order_test= new users_main_list_sort;
$order_test= new $obj;
$order_test->name='umtest';
$order_test->css_style['border']='1px solid green';
$order_test->context[$order_test->name]['picklist']=serialize(Array('uid','name','pass','reflink','isgroup','isactive'));
$order_test->context[$order_test->name]['settingid']=$order_test->name;
$div=new dom_div;
$page->append_child($div);
$div->append_child($order_test);
//$page->append_child($order_test);
$order_test->bootstrap();

$where_test=new users_main_list_where;
$where_test->name='whtest';
$where_test->oid=-1;
$where_test->context[$where_test->name]['settingid']=$where_test->name;
$page->append_child($where_test);
$where_test->bootstrap();

$where_test=new users_main_list_where;
$where_test->name='whtest1';
$where_test->oid=-1;
$where_test->context[$where_test->name]['settingid']='where_part_2';
$page->append_child($where_test);
$where_test->bootstrap();





$div=new dom_div;
$page->append_child($div);

$pb=new editor_pick_button;
$div->append_child($pb);
$pb->oid=-1;
$pb->name='test';
$pb->context['test']['picklist']=serialize(Array('uid','name','pass','reflink','isgroup','isactive'));
$pb->bootstrap();

for($k=0;$k<0;$k++)
{
$pb=new editor_pick_button_static;
$div->append_child($pb);
$pb->oid=-1;
$pb->name='test'.$k;
//$pb->context['test'.$k]['picklist']=serialize(Array('uuid','xname','epass','8reflink','8isgroup','8isactive','preved','medved','krossafcheg','ЖЖомммм','xname','epass','8reflink','8isgroup','8isactive','preved','medved','krossafcheg','ЖЖомммм'));
//$pb->context['test'.$k]['buttons']=serialize(Array('b1'=>'1','b2'=>'2'));
$pb->picklist=Array('uuid','xname','epass','8reflink','8isgroup','8isactive','preved','medved','krossafcheg','ЖЖомммм','xname','epass','8reflink','8isgroup','8isactive','preved','medved','krossafcheg','ЖЖомммм');
$pb->buttons=Array('b1'=>'1','b2'=>'2');
$pb->bootstrap();
}













$dbg=new dom_any('textarea');
$dbg->attributes['cols']=80;
$dbg->attributes['rows']=20;
$dbg->custom_id='debug';
$page->append_child($dbg);

$txt=new dom_statictext;
$dbg->append_child($txt);
//$txt->text=urldecode(post_tool::array_urlencode($pb->button->context,'yyy'));


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



//$leftdiv->oid=-1;
$page->after_build();
print $page->html();

	echo microtime(true)-$profiler;echo ":".$sql->querytime;
	print "\n";


?>