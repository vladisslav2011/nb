<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
//$_SESSION['uid']='1000';
//if(preg_match('/^127\.0\..*/',$_SERVER['REMOTE_ADDR']))$_SESSION['uid']='0';

//phpinfo();
//$_SESSION['sql_design']=false;
//if($_SESSION['uid']==0)$_SESSION['sql_design']=true;
require_once('lib/auth.php');
require_once('lib/ddc_meta.php');
require_once('lib/commctrls.php');
$sql->logquerys=true;

if($_SESSION['sql_design']==true)
{
	ddc_gentable_n('%dev_list',
		Array(
		//requred by tree
		Array('id','bigint(20)',0,NULL,1,NULL),
		Array('name','text',0,NULL,NULL,NULL),
		Array('description','text',0,NULL,NULL,NULL),
		Array('isdone','tinyint(1)',0,0,NULL,NULL),
		Array('wip','tinyint(1)',0,0,NULL,NULL),
		Array('mtime','timestamp',1,NULL,NULL,NULL)//
	//	Array('','',1,NULL,NULL,NULL),
	)
	,
	Array(
		Array('PRIMARY','id',NULL)//,
	)
	,$sql);
	ddc_gentable_n('%dev_list_deps',
		Array(
		//requred by tree
		Array('id','bigint(20)',0,NULL,NULL,NULL),
		Array('depid','bigint(20)',0,NULL,NULL,NULL)//
	//	Array('','',1,NULL,NULL,NULL),
	)
	,
	Array(
		Array('PRIMARY','id',NULL),
		Array('PRIMARY','depid',NULL)//,
	)
	,$sql);
	
	
}



function gen_text($str)
{
	$r=new dom_statictext;
	$r->text=$str;
	return $r;
}



class progress_viewer extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype='progress_viewer';
		$this->top=new dom_div;
		$this->append_child($this->top);
		
		if($_SESSION['uid']==0)
		{
			editor_generic::addeditor('editmode',new editor_checkbox);
			$this->top->append_child($this->editors['editmode']);
			$addbtn=new editor_button;
		}
		if($_SESSION['progress_edit'])
		{
			editor_generic::addeditor('add',$addbtn);
			$this->top->append_child($addbtn);
			$addbtn->attributes['value']='+';
		}
		editor_generic::addeditor('cell',new progress_cell);
		$this->append_child($this->editors['cell']);
		$this->css_class='progress_viewer_top';
	}
	function html_inner()
	{
		global $sql;
		$this->top->html();
		//$sql->query("insert into  `%dev_list` SET name='test',description='test'");
		$sres=$sql->query("SELECT a.* FROM `%dev_list` as a ORDER BY a.mtime DESC");
		while($row=$sql->fetcha($sres))
		{
			foreach($this->editors as $a)
			{
				unset($a->keys);
				$a->keys['id']=$row['id'];
				$a->args= &$row;
				$a->id_alloc();
				$a->bootstrap();
			}
				$this->editors['cell']->html();
		}
		$sql->free($sres);
	
	}
	function after_build_before_children()
	{
		$this->rootnode->scripts['settings.js']='/settings/settings.js';
		$this->rootnode->scripts['core.js']='/js/core.js';

	}
	function bootstrap()
	{
		
		$this->long_name=$long_name=editor_generic::long_name();
		if($_SESSION['uid']==0)
			$this->args['editmode']=$_SESSION['progress_edit'];
		if($_SESSION['progress_edit'])
			$this->context[$long_name.'.editmode']['var']='editmode';
		foreach($this->editors as $a)
		{
			$a->context=&$this->context;
			$a->keys=&$this->keys;
			$a->args=&$this->args;
			$a->bootstrap();
		}
	}
	
	function handle_event($ev)
	{
		global $sql;
		switch($ev->rem_name)
		{
		case 'editmode':
			$_SESSION['progress_edit']=$_POST['val'];
			print 'window.location.reload(true);';
			exit;
		case 'add':
			if(!$_SESSION['progress_edit'])return;
			$sql->query("INSERT INTO `%dev_list` SET `name`=''");
			print "window.location.reload(true);";
			break;
		case 'cell.del':
			if(!$_SESSION['progress_edit'])return;
			$sql->query("DELETE FROM `%dev_list_deps` WHERE id='".$sql->esc($ev->val)."' OR depid='".$sql->esc($ev->val)."'");
			$sql->query("DELETE FROM `%dev_list` WHERE id='".$sql->esc($ev->val)."'");
			print "window.location.reload(true);";
			break;
		case 'cell.subtargets.a.del':
			if(!$_SESSION['progress_edit'])return;
			$sql->query("DELETE FROM `%dev_list_deps` WHERE id='".$sql->esc($ev->keys['id'])."' AND depid='".$sql->esc($ev->val)."'");
			print "window.location.reload(true);";
			break;
		
		case 'cell.add_sub.btn':
			if(!$_SESSION['progress_edit'])return;
		//	print "alert('".$ev->val."');";
			$sql->query("INSERT INTO `%dev_list_deps` SET id='".$sql->esc($ev->keys['id'])."',depid='".$sql->esc($ev->val)."'");
			print "window.location.reload(true);";
			break;
		default:
		}
		editor_generic::handle_event($ev);
	}
}

class progress_cell extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype='progress_cell';
		$this->css_class='progress_cell';
		editor_generic::addeditor('anchor',new progress_cell_a);
		$this->append_child($this->editors['anchor']);
		$name_div=new dom_div;
		$name_div->css_class='progress_cell_header';
		$this->append_child($name_div);
		if($_SESSION['progress_edit'])
		{
			editor_generic::addeditor('name_text',new editor_textarea);
			$this->editors['name_text']->attributes['rows']=2;
		}else
			editor_generic::addeditor('name_text',new editor_statictext);
		
		$name_div->append_child($this->editors['name_text']);
		
		$description_div=new dom_div;
		
		$description_div->css_style['background-color']='white';
		$this->append_child($description_div);
		
		if($_SESSION['progress_edit'])
			editor_generic::addeditor('description_text',new editor_textarea);
		else
			editor_generic::addeditor('description_text',new editor_statichtml);
		
		$description_div->append_child($this->editors['description_text']);
		
		editor_generic::addeditor('subtargets',new progress_cell_subtargets);
		$this->append_child($this->editors['subtargets']);
		
		if($_SESSION['progress_edit'])
		{
			editor_generic::addeditor('del',new editor_button);
			$this->append_child($this->editors['del']);
			$this->editors['del']->attributes['value']='-';
			
			editor_generic::addeditor('inprogress',new editor_checkbox);
			$this->append_child($this->editors['inprogress']);
			$this->append_child(gen_text('wip'));
			
			editor_generic::addeditor('isdone',new editor_checkbox);
			$this->append_child($this->editors['isdone']);
			$this->append_child(gen_text('done'));
			
			editor_generic::addeditor('add_sub',new editor_pick_button);
			$this->append_child($this->editors['add_sub']);
			$this->editors['add_sub']->list_class='progress_cell_editor_dep_list';
			
		}else{
			$this->inprogress=new dom_div;
			$this->inprogress->css_style['background-color']='yellow';
			$this->append_child($this->inprogress);
			$txt=new dom_statictext;
			$txt->text='В разработке';
			$this->inprogress->append_child($txt);
			$this->isdone=new dom_div;
			$this->isdone->css_style['background-color']='green';
			$this->append_child($this->isdone);
			$txt=new dom_statictext;
			$txt->text='Готово';
			$this->isdone->append_child($txt);

		}
	
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		$this->inprogress->css_style['display']=($this->args['wip']==1)?'block':'none';
		$this->isdone->css_style['display']=($this->args['isdone']==1)?'block':'none';
		//////
		$this->context[$long_name.'.name_text']['var']='name';
		//////
		$this->context[$long_name.'.description_text']['var']='description';
		//////
		$this->context[$long_name.'.inprogress']['var']='wip';
		$this->context[$long_name.'.isdone']['var']='isdone';
		$this->context[$long_name.'.del']['var']='id';
		foreach($this->editors as $i=>$a)
		{
			//unset($a->keys);
			//unset($a->args);
			$a->context=&$this->context;
			$a->keys=&$this->keys;
			$a->args=&$this->args;
			//print $i.';';
			$a->bootstrap();
		}
		if($_SESSION['progress_edit'])$this->editors['add_sub']->button->attributes['value']='s+';
	}
	function handle_event($ev)
	{
		global $sql;
		$var=$ev->context[$ev->long_name]['var'];
		//print 'alert(\''.$var.';'.$ev->rem_name.'\');';
		switch($ev->rem_name)
		{
		case 'name_text':
		case 'description_text':
		case 'isdone':
		case 'inprogress':
			$q="INSERT INTO `%dev_list` SET ".
			$sql->esc($var)."='".$sql->esc($ev->val).
			"' , id=".$sql->esc($ev->keys['id']).
			" ON DUPLICATE KEY UPDATE ".
			$sql->esc($var)."='".$sql->esc($ev->val).
			"'";
			$sql->query($q);
			break;
		}
		editor_generic::handle_event($ev);
	}
}

class progress_cell_a extends dom_any
{
	function __construct()
	{
		dom_any::__construct('a');
		$this->etype='progress_cell_a';
	}
	function bootstrap()
	{
		$this->attributes['name']="h".$this->args['id'];
	}
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}

class progress_cell_subtargets extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype='progress_cell_subtargets';
		$this->css_style['overflow']='hidden';
		$div=new dom_div;
		$div->css_style['float']='left';
		$this->append_child($div);
		if($_SESSION['progress_edit'])
		{
			$this->a=new progress_cell_editor_dep;
		}else{
			$this->a=new progress_cell_viewer_dep;
		}
		$div->append_child($this->a);
		editor_generic::addeditor('a',$this->a);
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		$this->css_class='subtar';
		$this->context[$long_name.'.a']['var']='editor_dep_callback';
		foreach($this->editors as $a)
		{
			$a->keys=&$this->keys;
			$a->context=&$this->context;
		}
		
	}
	
	function html_inner()
	{
		
		global $sql;
		$sres=$sql->query("SELECT a.name,a.id as depid, '".$sql->esc($this->keys['id'])."' as id FROM `%dev_list` as a, `%dev_list_deps` as b WHERE a.id=b.depid AND b.id='".$sql->esc($this->keys['id'])."' ORDER BY a.name");
		while($row=$sql->fetcha($sres))
		{
			foreach($this->editors as $a)
			{
				$a->keys['depid']=$row['dep'];
				$a->args= &$row;
				$a->id_alloc();
				$a->bootstrap();
			}
			dom_div::html_inner();
		}
		if($sres)$sql->free($sres);
	}
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}


class progress_cell_viewer_dep extends dom_any
{
	function __construct()
	{
		dom_any::__construct('a');
		$this->etype='progress_cell_viewer_dep';
		$this->node_name='a';
		$this->text=new dom_statictext;
		$this->append_child($this->text);
		
	}
	function bootstrap()
	{
		$this->attributes['href']='#h'.$this->args['depid'];
		$this->text->text=" [".$this->args['name']."] ";
	}
	function handle_event($ev)
	{
	}
}





class query_log extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->custom_id='this';
//		$this->css_style['position']='absolute';
//		$this->css_style['top']='1000px';
		$this->css_style['width']='80%';
		$this->css_style['height']='50%';
		$this->css_style['background-color']='white';
		
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
	}
	function add_row($val)
	{
		$tr=new dom_tr;
		$this->tbl->append_child($tr);
		$td=new dom_td;
		$tr->append_child($td);
		if(!isset($this->cnt))$this->cnt=0;
		$tn=new dom_statictext;
		$td->append_child($tn);
		$tn->text=$this->cnt;
		$td=new dom_td;
		$tr->append_child($td);
		$d1=new dom_div;
		$td->append_child($d1);
		
		$tn=new dom_statictext;
		$d1->append_child($tn);
		$tn->text=$val->q;
		
		$tn=new dom_statictext;
		$d1->append_child($tn);
		$tn->text=$val->e;
		
		$this->cnt++;
	}
	function html_head()
	{
		global $sql;
		foreach($sql->querylog as $a)
			$this->add_row($a);
		
		dom_div::html_head();
	}
}


class progress_cell_editor_dep extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype='progress_cell_editor_dep';
		editor_generic::addeditor('view',new editor_statictext);
		$this->append_child($this->editors['view']);
		editor_generic::addeditor('del',new editor_button);
		$this->editors['del']->attributes['value']='s-';
		$this->append_child($this->editors['del']);
		/*editor_generic::addeditor('picker',new editor_pick_button);
		$this->editors['picker']->list_class='progress_cell_editor_dep_list';
		$this->append_child($this->editors['picker']);*/
		
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		$this->context[$long_name.'.view']['var']='name';
		$this->context[$long_name.'.del']['var']='depid';
		foreach($this->editors as $e)
		{
				$e->keys=&$this->keys;
				$e->args= &$this->args;
				$e->context=&$this->context;
				$e->bootstrap();
		}
	}
	
	function handle_event($ev)
	{
	editor_generic::handle_event($ev);
	}
}





class progress_cell_editor_dep_list extends  dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->etype='editor_pick_button';
		$div=new dom_tr;
		$this->append_child($div);
		editor_generic::addeditor('text',new editor_statictext);
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['text']);
		
		editor_generic::addeditor('btn',new editor_button);
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['btn']);
		$this->editors['btn']->attributes['value']='+';
		
	}
	
	function bootstrap()
	{
		//$this->picklist=$this->context
		$this->long_name=$long_name=editor_generic::long_name();
		$this->context[$long_name.'.text']['var']='name';
		$this->context[$long_name.'.btn']['var']='id';
		
	}
	
	
	function html_inner()
	{
		global $sql;
		//$this->long_name=$long_name=editor_generic::long_name();
		//if(! isset($this->picklist))$this->picklist=unserialize($this->context[$this->long_name]['picklist']);
		unset($this->picklist);
		$res=$sql->query("SELECT x.name,x.id FROM `%dev_list` as x WHERE x.id <> '".$sql->esc($this->keys['id'])."' AND (SELECT b.depid as dep FROM `%dev_list_deps` as b WHERE b.depid=x.id AND b.id='".$sql->esc($this->keys['id'])."' ) IS NULL ORDER BY x.name");
		while($row=$sql->fetcha($res))
		{
			$this->picklist[]=$row;
		}
		
		foreach($this->editors as $e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
		}
		reset($this->picklist);
		foreach($this->picklist as $i => $r)
		{
			//$this->keys['col']=$r;
			//$this->editors['btnforw']->value=$r;
			//$this->editors['btnrev']->value=$r;
			$this->args=$r;
			//$this->args['users.sort.dir']=$r->dir;
			$this->id_alloc();
			reset($this->editors);
			foreach($this->editors as $e)
				$e->bootstrap();
			dom_table::html_inner();
		}
	}
}
















?>