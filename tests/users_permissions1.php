<?php
session_start();
$_SESSION['sql_design']=false;
$profiler=microtime(true);
define('FILE_PLACEMENT', '..');
require_once(FILE_PLACEMENT.'/lib/ddc_meta.php');
require_once(FILE_PLACEMENT.'/lib/dom.php');
require_once(FILE_PLACEMENT.'/lib/settings.php');
require_once(FILE_PLACEMENT.'/lib/commctrls.php');
require_once(FILE_PLACEMENT.'/lib/auth.php');



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
		$controls=new dom_div;
		$this->append_child($controls);
		$table=new dom_div;
		$this->append_child($table);
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
		$repeating=new dom_tr;
		$this->append_child($repeating);
		$this->repeating=$repeating;
		
		$descr_div=new dom_td;
		$repeating->append_child($descr_div);
		
		$this->editors['descrtext']=new editor_statictext;
		$descr_div->append_child($this->editors['descrtext']);
		$this->editors['descrtext']->name='col';
		
		$cb=new dom_td;
		$repeating->append_child($cb);
		
		$this->editors['dir_checkbox']=new editor_checkbox;
		$cb->append_child($this->editors['dir_checkbox']);
		$this->editors['dir_checkbox']->name='dir';
		
		$txt=new dom_statictext;
		$txt->text='321';
		$cb->append_child($txt);
		
		$btns=new dom_td;
		$repeating->append_child($btns);
		$this->editors['delbtn']=new editor_button;
		$btns->append_child($this->editors['delbtn']);
		$this->editors['delbtn']->attributes['value']='-';
		$this->editors['delbtn']->name='del';
		
		$this->editors['addbtn']=new editor_pick_button;
		$btns->append_child($this->editors['addbtn']);
		$this->editors['addbtn']->attributes['value']='add';
		$this->editors['addbtn']->name='add';
		
		$this->en=new dom_tr;
		$this->append_child($this->en);
		$et=new dom_td;
		$this->en->append_child($et);
		$et->attributes['colspan']=3;
		
		$this->editors['add_end']=new editor_pick_button;
		$et->append_child($this->editors['add_end']);
		$this->editors['add_end']->name='add_end';
		$this->editors['add_end']->attributes['value']='add';
		
	
	}
	function bootstrap()
	{
		//in:Array(Object(col,dir))
		$this->context[$this->name]['htmlid']=$this->id_gen();
		reset($this->editors);
		foreach($this->editors as $e)
		{
			$e->context= & $this->context;
			$e->name=$this->name.'.'.$e->name;
			$this->context[$e->name]['control']='users_main_list_sort';
		}
		$this->for_each_set('oid',$this->oid);
		$this->context[$this->editors['descrtext']->name]['var']='users.sort.col';
		$this->context[$this->editors['dir_checkbox']->name]['var']='users.sort.dir';
		$picklist=$this->context[$this->name]['picklist'];
		$this->context[$this->editors['add_end']->name]['picklist']=	$picklist;
		$this->context[$this->editors['addbtn']->name]['picklist']=	$picklist;
		
	}
	
	function html_inner()
	{
		$this->in=unserialize($this->rootnode->setting_val($this->oid,$this->context[$this->name]['settingid'],serialize(Array())));
		foreach($this->in as $i => $r)
		{
			$this->repeating->id_alloc();
			$this->keys['col']=$r->col;
			$this->args['users.sort.col']=$r->col;
			$this->args['users.sort.dir']=$r->dir;
			foreach($this->editors as $e)
			{
				$e->args= &$this->args;
				$e->keys= &$this->keys;
				$e->bootstrap();
			}
			$this->repeating->html();
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
}






class editor_pick_button extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->button=new editor_button;
		$this->button->value='editor_pick_button';
		$this->resdiv=new dom_div;
		$this->resdiv->css_style['display']='none';
		$this->resdiv->css_style['position']='absolute';
		$this->resdiv->css_style['border']='1px solid gray';
		$this->resdiv->css_style['background']='#DDDDDD';
		$this->append_child($this->button);
		$this->append_child($this->resdiv);
		$this->context=Array();
	}
	function bootstrap()
	{
		$this->context[$this->name]['htmlid']=$this->id_gen();
		$this->button->name=$this->name.'.button';
		$this->context[$this->button->name]['res_div']=$this->resdiv->id_gen();
		$this->context[$this->button->name]['control']='editor_pick_button';
		$this->button->keys=&$this->keys;
		$this->button->args=&$this->args;
		$this->button->context=&$this->context;
		$this->button->attributes['value']='+';
		$this->button->bootstrap();
	}
	function html_inner()
	{
		$this->button->bootstrap();
		dom_any::html_inner();
	}
	function html()
	{
		$this->html_inner();
	}
}

class editor_pick_button_list extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$div=new dom_tr;
		$this->append_child($div);
		$this->editors['text']=new editor_statictext;
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['text']);
		
		$this->editors['btnforw']=new editor_button;
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['btnforw']);
		$this->editors['btnforw']->attributes['value']='123';
		
		$this->editors['btnrev']=new editor_button;
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['btnrev']);
		$this->editors['btnrev']->attributes['value']='321';
		
	}
	
	function bootstrap()
	{
		//$this->picklist=$this->context
		$this->editors['text']->name=$this->name.'.text';
		$this->editors['btnforw']->name=$this->name.'.btnforw';
		$this->editors['btnrev']->name=$this->name.'.btnrev';
		$this->context[$this->name.'.text']['var']='c';
		$this->context[$this->editors['btnforw']->name]['var']='col';
		$this->context[$this->editors['btnrev']->name]['var']='col';
		$this->context[$this->editors['btnforw']->name]['control']='editor_pick_button_list';
		$this->context[$this->editors['btnrev']->name]['control']='editor_pick_button_list';
		
	}
	
	
	function html_inner()
	{
		reset($this->picklist);
		foreach($this->picklist as $i => $r)
		{
			//$this->keys['col']=$r;
			$this->context[$this->editors['btnforw']->name]['dir']=0;
			$this->context[$this->editors['btnrev']->name]['dir']=1;
			$this->editors['btnforw']->value=$r;
			$this->editors['btnrev']->value=$r;
			$this->args['c']=$r;
			//$this->args['users.sort.dir']=$r->dir;
			$this->id_alloc();
			reset($this->editors);
			foreach($this->editors as $e)
			{
				$e->args= $this->args;
				$e->keys= &$this->keys;
				$e->context= &$this->context;
				$e->bootstrap();
			}
			dom_div::html_inner();
		}
	}
}














// ##########################################################################################
// ##########################################################################################
// ####################     CORE COMPONENTS        ##########################################
// ##########################################################################################
// ##########################################################################################
class post_tool
{
	function array_urlencode($arr,$pfix)
	{
		$ret='';
		if(! is_array($arr))return $ret;
		foreach($arr as $i => $v)
		{
			$key=$pfix.urlencode("[".$i."]");
			if($ret != '') $ret.='&';
			if(is_array($v))
				$ret.=post_tool::array_urlencode($v,$key);
			else{
				$ret.=($key."=".urlencode($v));
			}
		}
		return $ret;
	}
	
	function array_to_post($arr)
	{
		$postdata='';
		foreach($arr as $k => $v)
		{
			if($postdata != '') $postdata.='&';
			if(is_array($v))$postdata.=post_tool::array_urlencode($v,urlencode($k));
			elseif(is_object($v))die('urlencoded postdata : object serialization not supported');
			else $postdata.=urlencode($k).'='.urlencode($v);
		}
		return $postdata;
	}
	
	function array_to_post_js($arr)
	{
		$postdata='';
		foreach($arr as $k => $v)
		{
			if($postdata != '') $postdata.='&';
			$postdata.=urlencode($k)."=' + ".$v." + '";
		}
		return $postdata;
	}
}




class editor_checkbox extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->attributes['onchange']='chse.timerch(true);';
		$this->attributes['type']='checkbox';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$postargs['keys']=$this->keys;
		$postargs['context']=$this->context;
		$postargs['context']['this']=$this->name;
		$postargs['context'][$this->name]['htmlid']=$this->id_gen();
		$postargs['context'][$this->name]['type']='checkbox';
		if(isset($this->oid))$postargs['context'][$this->name]['oid']=$this->oid;
		$cnt=0;
//		$postargs_js['last_generated_id']='last_generated_id';
		$send=post_tool::array_to_post($postargs);
		$send.="&last_generated_id=' + last_generated_id + '";
		$send.="&val";
		$this->attributes['onfocus']="chse.activatemon({obj:this,objtype:'checkbox',static:'$send'});";
		$this->attributes['onchange']="chse.timerch(true);";
		$this->attributes['onblur']='chse.latedeactivate(this);';
		unset($this->attributes['checked']);
		if($this->args[$this->context[$this->name]['var']]==1)$this->attributes['checked']='checked';
		
	}
}

class editor_statictext extends dom_statictext
{
	function __construct()
	{
		dom_statictext::__construct();
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$this->text=$this->args[$this->context[$this->name]['var']];
	}
}



class editor_button extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->attributes['type']='submit';
		$this->css_style['margin']='1px';
		$this->css_style['padding']='1px';
		$this->css_style['font-size']='12px';
		//$this->keys;
		//$this->args
	}
	
	
	
	
	function bootstrap()
	{
		$postargs['keys']=$this->keys;
		$postargs['context']=$this->context;
		$postargs['context']['this']=$this->name;
		$postargs['context'][$this->name]['htmlid']=$this->id_gen();
		$postargs['context'][$this->name]['type']='button';
		if(isset($this->oid))$postargs['context'][$this->name]['oid']=$this->oid;
		$send=post_tool::array_to_post($postargs);
		$send.="&last_generated_id=' + last_generated_id + '";
		$send.="&val";
		$value=js_escape($this->value);
		$this->attributes['onclick']="chse.send_or_push({static:'$send',val:'$value'});";
	}
	
	function after_build_before_children()
	{
		$this->rootnode->scripts['settings.js']='../settings/settings.js';
		$this->rootnode->scripts['core.js']='../js/core.js';

	}
}

class editor_text extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->bindval='';
		$this->callback_prefix='';
		$this->callback_uri='';
		$this->attributes['onchange']='chse.timerch(true);';
		$this->attributes['type']='text';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$postargs['keys']=$this->keys;
		$postargs['context']=$this->context;
		$postargs['context']['this']=$this->name;
		$postargs['context'][$this->name]['htmlid']=$this->id_gen();
		$postargs['context'][$this->name]['type']='text';
		if(isset($this->oid))$postargs['context'][$this->name]['oid']=$this->oid;
		$send=post_tool::array_to_post($postargs);
		$send.="&last_generated_id=' + last_generated_id + '";
		$send.="&val";
		$this->attributes['onfocus']="chse.activatemon({obj:this,objtype:'text',static:'$send'});";
		$this->attributes['onblur']='chse.latedeactivate(this);';
		$this->attributes['value']=$this->args[$this->context[$this->name]['var']];
		
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
		$thisid=preg_replace('/\/.*/','',$path);
		$rempath=preg_replace('/^[0-9]\//','',$path);
		$cnt=0;
		reset($obj->exprs);
		foreach($obj->exprs as $e)
		{
			if($cnt==$thisid)$this->change_op($e,$rempath,$op);
			$cnt++;
		}
	}
	function add($obj,$path,$new)
	{
		if($path=='')
		{
			$obj->exprs[]=$new;
			return;
		}
		$thisid=preg_replace('/\/.*/','',$path);
		$rempath=preg_replace('/^[0-9]\//','',$path);
		$cnt=0;
		reset($obj->exprs);
		foreach($obj->exprs as $e)
		{
			if($cnt==$thisid)$this->add($e,$rempath,$new);
			$cnt++;
		}
	}
	
	function change($obj,$path,$new)
	{
		$thisid=preg_replace('/\/.*/','',$path);
		$rempath=preg_replace('/^[0-9]\//','',$path);
		$cnt=0;
		reset($obj->exprs);
		foreach($obj->exprs as $k => $e)
		{
			if($rempath=='')
			{
				if($cnt==$thisid)$obj->exprs[$k]=$new;
				exit;
			}else{
				if($cnt==$thisid)$this->change($e,$rempath,$new);
			}
			$cnt++;
		}
	}
	
	function del($obj,$path)
	{
		$thisid=preg_replace('/\/.*/','',$path);
		$rempath=preg_replace('/^[0-9]\//','',$path);
		$cnt=0;
		reset($obj->exprs);
		foreach($obj->exprs as $k => $e)
		{
			if($rempath=='')
			{
				if($cnt==$thisid)unset($obj->exprs[$k]);
				exit;
			}else{
				if($cnt==$thisid)$this->del($e,$rempath);
			}
			$cnt++;
		}
	}
	
}



class users_main_list_selection extends dom_div
{
	function __construct()
	{
		//div(span(text(descr))span(checkbox(dir)text('321'))button(del)button(add_after :unhides available)div(available :hidden))
		//button(add_end)
		dom_div::__construct();
		//$this->repeating=new users_main_list_sort_item;
		
	
	}
	function bootstrap()
	{
		//in:Array(Object(col,dir))
		
		
	}
	
	function html_inner()
	{
		$this->in=unserialize($this->rootnode->setting_val($this->oid,$this->bindval,serialize(Array())));
	}
}

class users_main_list_selection_node extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		
		//string view
		$this->string_view=new editor_text;
		$this->string_view->bindval='users.selection.raw_expr';
		$this->editors['text']=$this->string_view;
		$this->append_child($this->string_view);
		//tree view
		$this->firstrow=new dom_tr;
		$this->append_child($this->firstrow);
		
		$this->typemark=new dom_td;
		$this->firstrow->append_child($this->typemark);
		
		$this->tree_type=new editor_checkbox;
		$this->tree_type->bindval='users.selection.tree_type';
		$this->typemark->append_child($this->tree_type);
		
		$this->col2=new dom_td;
		//$img=new dom_img;
		//$img->src=FILE_PLACEMENT.'/i/vbar.png';
		//$this->col2->append_child($img);
		$this->firstrow->append_child($this->col2);
		
		$this->tree_editors=new dom_td;
		$this->editors['add_btn']=new editor_button;
		$this->editors['add_btn']->value='users.selection.add_btn';
		$this->editors['add_btn']->attributes['value']='[+]';
		$this->editors['del_this']=new editor_button;
		$this->editors['del_this']->value='users.selection.del_this';
		$this->editors['del_this']->attributes['value']='[-]';
		$this->firstrow->append_child($this->tree_editors);
		
		
		
		$this->nextrow=new dom_tr;
		$this->append_child($this->nextrow);
		//expression view
		
	}
	
	function html_inner()
	{
	
	}
}













//handlers
/*
if($_GET['cb']==2)
{
if(isset($_POST['last_generated_id']))$idcounter=$_POST['last_generated_id'];
switch ($_POST['button'])
{
	case 'users.sort.add_end':
	case 'users.sort.add':
		$r= new users_main_list_sort_add_items;
		$r->picklist=unserialize($_POST['picklist']);
		$r->extravars['picklist']=$_POST['picklist'];
		$r->extravars['reload_bindval']=$_POST['reload_bindval'];
		$r->extravars['reload_id']=$_POST['reload_id'];
		$r->keys=$_POST['keys'];
		
		print "\$i('".js_escape($_POST['dest_div_id'])."').style.display='block';";
		print "\$i('".js_escape($_POST['dest_div_id'])."').innerHTML=".reload_object($r);
		exit;
	case 'users.sort.add_rev':
		$settings_tool=new settings_tool;
		$items=$sql->fetch1($sql->query($settings_tool->single_query(-1,$_POST['reload_bindval'],$_SESSION['uid'],0)));
		if($items)$arr=unserialize($items);
		else $arr=Array();
		$item->dir=1;
		$item->col=$_POST['val'];
		$new=Array();
		if(is_array($_POST['keys']))
			foreach($arr as $i)
				if($i->col==$_POST['keys']['col'])
				{
					$new[$item->col]=$item;
					$new[$i->col]=$i;
				}else $new[$i->col]=$i;
		if(!is_array($_POST['keys']))
		{
			$arr[$_POST['val']]=$item;
			$new=$arr;
		}
		$sql->query($settings_tool->set_query(-1,$_POST['reload_bindval'],$_SESSION['uid'],0,serialize($new)));
		$r= new users_main_list_sort;
		$r->picklist=unserialize($_POST['picklist']);
		$r->bindval=$_POST['reload_bindval'];
		$r->custom_id=$_POST['reload_id'];
		print "\$i('".js_escape($_POST['reload_id'])."').innerHTML=".reload_object($r,true);
		exit;
	
	case 'users.sort.add_forw':
		$settings_tool=new settings_tool;
		$items=$sql->fetch1($sql->query($settings_tool->single_query(-1,$_POST['reload_bindval'],$_SESSION['uid'],0)));
		if($items)$arr=unserialize($items);
		else $arr=Array();
		$item->dir=0;
		$item->col=$_POST['val'];
		$new=Array();
		if(is_array($_POST['keys']))
			foreach($arr as $i)
				if($i->col==$_POST['keys']['col'])
				{
					$new[$item->col]=$item;
					$new[$i->col]=$i;
				}else $new[$i->col]=$i;
		if(!is_array($_POST['keys']))
		{
			$arr[$_POST['val']]=$item;
			$new=$arr;
		}
		$sql->query($settings_tool->set_query(-1,$_POST['reload_bindval'],$_SESSION['uid'],0,serialize($new)));
		$r= new users_main_list_sort;
		$r->picklist=unserialize($_POST['picklist']);
		$r->bindval=$_POST['reload_bindval'];
		$r->custom_id=$_POST['reload_id'];
		print "\$i('".js_escape($_POST['reload_id'])."').innerHTML=".reload_object($r,true);
		exit;
	
	case 'users.sort.del':
		$settings_tool=new settings_tool;
		$items=$sql->fetch1($sql->query($settings_tool->single_query(-1,$_POST['reload_bindval'],$_SESSION['uid'],0)));
		if($items)$arr=unserialize($items);
		else exit;
		$new=Array();
		foreach($arr as $item)if($item->col!=$_POST['keys']['col'])$new[$item->col]=$item;
		$sql->query($settings_tool->set_query(-1,$_POST['reload_bindval'],$_SESSION['uid'],0,serialize($new)));
		$r= new users_main_list_sort;
		$r->picklist=unserialize($_POST['picklist']);
		$r->bindval=$_POST['reload_bindval'];
		$r->custom_id=$_POST['reload_id'];
		print "\$i('".js_escape($_POST['reload_id'])."').innerHTML=".reload_object($r,true);
		exit;
	
	
	
	
	
	default:
}
}






















if($_GET['cb']==1)
{
switch($_POST['var'])
{
case 'users.sort.dir':
		$settings_tool=new settings_tool;
		$items=$sql->fetch1($sql->query($settings_tool->single_query(-1,$_POST['reload_bindval'],$_SESSION['uid'],0)));
		if($items)$arr=unserialize($items);
		else exit;
		$arr[$_POST['keys']['col']]->dir=$_POST['val'];
		$sql->query($settings_tool->set_query(-1,$_POST['reload_bindval'],$_SESSION['uid'],0,serialize($arr)));
		exit;
	
	;
default:
	;
}


}
*/








if($_GET['cb']!='')
{
	$context=$_POST['context'];
	$long_name=$context['this'];
	$sender_type=$context[$long_name]['type'];
	$sender_control=$context[$long_name]['control'];
	$sender_name=preg_replace('/^.*\./','',$long_name);
	$sender_parent=preg_replace("/^(.*)\..*/",'$1',$long_name);
	$this_cont=$context[$long_name];
	switch($sender_control)
	{
		case 'editor_pick_button':
			if($sender_name=='button')
			print "chse.safe_alert(123,'".js_escape($sender_parent)."');";
			//exit;
			$r= new editor_pick_button_list;
			$r->picklist=unserialize($context[$sender_parent]['picklist']);
			$r->context=$context;
			$r->for_each_set('oid',$context[$long_name]['oid']);
			$r->name=$sender_parent.".list";
			$r->keys=$_POST['keys'];
			$r->bootstrap();
			print "var res=\$i('".js_escape($context[$long_name]['res_div'])."');".
			"if(res.style.display=='block')".
			"{".
			"res.style.display='none';".
			"}else{".
			"res.style.display='block';".
			"res.innerHTML=".reload_object($r).
			"};";
			print "chse.safe_alert(123,'".$js_escape_time."');";
			exit;
		case 'editor_pick_button_list':
			//if($sender_parent
			$oid=$this_cont['oid'];
			$tparent=$sender_parent;
			while(!isset($context[$tparent]['settingid']))$tparent=preg_replace("/^(.*)\..*/",'$1',$tparent);
			$settingid=$context[$tparent]['settingid'];
			$customid=$context[$tparent]['htmlid'];
			if(! isset($oid))$oid=-1;
			$settings_tool=new settings_tool;
			$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
			if($items)$arr=unserialize($items);
			else $arr=Array();
			$item->dir=$this_cont['dir'];
			$item->col=$_POST['val'];
			$new=Array();
			if(is_array($_POST['keys']))
				foreach($arr as $i)
					if($i->col==$_POST['keys']['col'])
					{
						$new[$item->col]=$item;
						$new[$i->col]=$i;
					}else $new[$i->col]=$i;
			if(!is_array($_POST['keys']))
			{
				$arr[$_POST['val']]=$item;
				$new=$arr;
			}
			$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($new)));
			$r= new users_main_list_sort;
			$r->context=$context;
			$r->oid=$this_cont['oid'];
			$r->name=$tparent;
			$r->custom_id=$customid;
			$r->bootstrap();
//			$r->picklist=unserialize($_POST['picklist']);
//			$r->bindval=$_POST['reload_bindval'];
//			$r->custom_id=$_POST['reload_id'];
			print "\$i('".js_escape($customid)."').innerHTML=".reload_object($r,true);
			exit;
		case 'users_main_list_sort':
			$oid=$this_cont['oid'];
			$tparent=$sender_parent;
			while(!isset($context[$tparent]['settingid']))$tparent=preg_replace("/^(.*)\..*/",'$1',$tparent);
			$settingid=$context[$tparent]['settingid'];
			$customid=$context[$tparent]['htmlid'];
			if(! isset($oid))$oid=-1;
			if($sender_name=='del')
			{
				$settings_tool=new settings_tool;
				$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
				if($items)$arr=unserialize($items);
				else exit;
				$new=Array();
				foreach($arr as $item)if($item->col!=$_POST['keys']['col'])$new[$item->col]=$item;
				$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($new)));
				$r= new users_main_list_sort;
				$r->context=$context;
				$r->name=$tparent;
				$r->oid=$this_cont['oid'];
				$r->custom_id=$customid;
				$r->bootstrap();
				print "\$i('".js_escape($customid)."').innerHTML=".reload_object($r,true);
				print "chse.safe_alert(123,'".js_escape($js_escape_time)."');";
				exit;
			}
			if($sender_name=='dir')
			{
				$settings_tool=new settings_tool;
				$items=$sql->fetch1($sql->query($settings_tool->single_query($oid,$settingid,$_SESSION['uid'],0)));
				if($items)$arr=unserialize($items);
				else exit;
				$arr[$_POST['keys']['col']]->dir=$_POST['val'];
				$sql->query($settings_tool->set_query($oid,$settingid,$_SESSION['uid'],0,serialize($arr)));
				exit;
			}
			exit;
		default:
	}
	
	
	
	
	exit;
}




function reload_object($obj,$inneronly=false)
{
	global $sql,$idcounter;
	$settings_tool=new settings_tool;
	$tr=new dom_root_reload;
	$tr->append_child($obj);
	//$tr->for_each_set('oid',-1);
	$tr->collect_oids($settings_tool);
	$tr->settings_array=$settings_tool->read_oids($sql);
	$tr->after_build();
	//print "\$i('dom_meta_treeview_resize_style').innerHTML=".$tr->html();
	if($inneronly)return $tr->firstinner();
	return $tr->html();
}
















$page=new dom_root_print;
$page->title='dbmanage tests';




//test order

$obj='users_main_list_sort';
//$order_test= new users_main_list_sort;
$order_test= new $obj;
$order_test->name='umtest';
$order_test->context[$order_test->name]['picklist']=serialize(Array('uid','name','pass','reflink','isgroup','isactive'));
$order_test->context[$order_test->name]['settingid']=$order_test->name;
$page->append_child($order_test);
$order_test->bootstrap();



$pb=new editor_pick_button;
$page->append_child($pb);
$pb->name='test';
$pb->context['test']['picklist']=serialize(Array('uid','name','pass','reflink','isgroup','isactive'));
$pb->bootstrap();

$pb=new editor_pick_button;
$page->append_child($pb);
$pb->name='test1';
$pb->context['test1']['picklist']=serialize(Array('uuid','xname','epass','8reflink','8isgroup','8isactive','preved','medved','krossafcheg','ЖЖомммм'));
$pb->bootstrap();













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
	if(o.objtype=='text')
	return function()
	{
		return encodeURIComponent(this.obj.value);
	};
	if(o.objtype=='checkbox')
		return function()
		{
			return this.obj.checked?1:0;
		}
	return null;
}
);

chse.checkerfuncs.push(function (o)
{
	if(o.objtype=='text')
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
	if(o.objtype=='checkbox')
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


?>