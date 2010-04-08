<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once('lib/ddc_meta.php');
require_once('lib/settings.php');
require_once('lib/commctrls.php');
require_once('lib/meta_query.php');


/*
//statically defined selector/editor
//keys:autodetect using 'SHOW KEYS/INDEXES' query
//column list:predefined/allow add-delete-redefine,store changes,load/save/delete preset

query:list of results
table:list of columns;each column has a cell;cell has fields, that catch values from query results
table head is detachable (as i think it will be easy to create):has captions/fields from head-query,updates indeppendent from main table????
layout:
<'setup div'
 <'hide/show button>
 <'hidden div'
  <'tab container'
   <tab name='query list' admin
    <'query editor' name='heading query'>
    <'query editor' name='row query'>
   >
   <tab name='column list' admin
    <table (add/paste/clear)
     <row left/right/delete/copy/paste>
      <'layout editor' (add/paste/clear)
       <'field editor' (add child/add after/paste child/paste after/copy/delete/clear fields/clear children)
        <option>
        <option>
        ....
        <field (name,default,rowid)>
        <field>
        ...
        <'layout editor' child>
        <child>
        ....
       >
      >
      <'layout editor' caption
       ....
      >
      <'query editor' parts for this editor fields>
     >
    >
   >
   <tab name='predefined filters' admin
   >
   <tab name=columns user
   >
   <tab name=filters user
   >
   <tab name=sort user
   >
  >
 >
>
<'caption div' detachable left:synced/top:fixed
 <'column captions table'
  <cell
   <div width/height-control-class>
  >
 >
<'caption div' detachable left:fixed/top:synced
 <'row captions table'
  <div>
>
<'results div'
 <table
  <cell
   <div class
    <field>
   >
  >
 >
>
*/
class simple_report_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='simple_report_test';
		
		$this->setup_cont=new dom_div;
		$this->append_child($this->setup_cont);
		$this->hidesettings=new dom_any('button');
		$this->setup_cont->append_child($this->hidesettings);
		$this->hidesettings->txt=new dom_statictext;
		$this->hidesettings->append_child($this->hidesettings->txt);
		$this->setup=new container_tab_control;
		$this->setup_cont->append_child($this->setup);
		
		$this->context=Array();
	}
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		$this->context[$long_name]['oid']=$this->oid;
		$this->context[$long_name]['htmlid']=$this->id_gen();
		
	}
	function html_inner()
	{
	}
	function html()
	{
		$this->html_inner();
	}
	function handle_event($ev)
	{
	}
}



class editor_object_list extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->row=new dom_div;
		$this->href=new dom_any('a');
		$this->text=new dom_statictext;
		
		$this->append_child($this->row);
		$this->row->append_child($this->href);
		$this->href->append_child($this->text);
		unset($this->row->id);
		unset($this->href->id);
		
	}
	
	function bootstrap()
	{
	}
	
	function html_inner()
	{
		global $sql;
		$qg=new query_gen_ext('select');
		$qg->from->exprs[]=new sql_column(NULL,TABLE_META_TREE,NULL,'mt');
		$join->type='LEFT OUTER JOIN';
		$join->what=new sql_column(NULL,TABLE_META_I18N,NULL,'mi');
		$join->on=new sql_expression('AND',Array(
			new sql_expression('=',Array(
				new sql_column(NULL,'mi','object'),
				new sql_column(NULL,'mt','id')
			)),
			new sql_expression('=',Array(
				new sql_column(NULL,'mi','var'),
				new sql_immed('name')
			)),
			new sql_expression('=',Array(
				new sql_column(NULL,'mi','loc'),
				new sql_immed($_SESSION['lang'])
			)),
		));
		$qg->joins->exprs[]=$join;
		$qg->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,'mt','sql_type'),
			new sql_immed('')
		));
		$qg->where->exprs[]=new sql_expression('!=',Array(
			new sql_column(NULL,'mt','xobject'),
			new sql_immed('sys')
		));
		$qg->what->exprs[]=new sql_column(NULL,'mt','id');
		$qg->what->exprs[]=new sql_column(NULL,'mt','name');
		$qg->what->exprs[]=new sql_list('coalesce',Array(
			new sql_column(NULL,'mi','val'),
			new sql_column(NULL,'mt','name')
		),'hr_name');
		
		$this->rootnode->out(htmlspecialchars($qg->result()));
		$res=$sql->query($qg->result());
		while($row=$sql->fetcha($res))
		{
			$this->href->attributes['href']="?p=".$row['id'];
			$this->text->text=$row['hr_name'];
			$this->row->attributes['title']=$row['name'];
			$this->row->css_style['background']=string_to_color($row['name'],2);
			$this->row->html();
		}
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}

$tests_m_array[]='editor_object_list';


















function meta_to_cache($oid,$children=false,$table=false)
{
	global $meta_cache,$table_cache,$sql;
	$query="SELECT * FROM `".TABLE_META_TREE."` WHERE id=".intval($oid);
	if($children)$query.=" OR parentid=".intval($oid);
	$res=$sql->query();
	while($row=$sql->fetcha($res))
		$meta_cache[$row['id']]=$row;
	$sql->free($res);
	if($table)
	
		foreach($meta_cache as $m)
			if($m['is_stored']==1 && !isset($table_cache[$m['sql_table']]))
			{
				$query="SELECT * FROM `".TABLE_META_TREE."` WHERE sql_table='".$sql->esc($m['sql_table'])."' AND sql_table=name AND is_stored=1 AND sql_type='table'";
				$res=$sql->query();
				while($row=$sql->fetcha($res))
				{
					$meta_cache[$row['id']]=$row;
					$table_cache[$row['name']]=$row;
				}
				$sql->free($res);
			}
	
}

function fetch_object(&$obj)
{
	global $sql;
	$query="SELECT a.*, coalesce(b.val,a.name) as hr_name FROM `".TABLE_META_TREE."` as a LEFT OUTER JOIN `".TABLE_META_I18N."` as b ON a.id=b.object AND b.var='name' AND b.loc='".$_SESSION['lang']."' WHERE a.parentid=".intval($obj->oid);
	$res=$sql->query($query);
	while($row=$sql->fetcha($res))
	{
		if($row['sql_keyname']=='PRIMARY')
		{
			$obj->key_by_id[$row['id']]=$row;
			if(!isset($obj->sql_table))$obj->sql_table=$row['sql_table'];
			$obj->key_by_name[$row['name']]=$row;
		}
		$obj->col_by_id[$row['id']]=$row;
		$obj->col_by_name[$row['name']]=$row;
	}
	
}


#######################################################################################
############## class m_list
#######################################################################################



class m_list extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('tabcontrol',new container_tab_control);
		$this->append_child($this->editors['tabcontrol']);
		$this->editors['tabcontrol']->add_tab('main',loc_get_val('m_list','tab_main','main'));
		$this->editors['tabcontrol']->add_tab('settings',loc_get_val('m_list','tab_settings','settings'));
		editor_generic::addeditor('pager',new util_small_pager);
		$this->editors['tabcontrol']->tabs['main']->div->append_child($this->editors['pager']);
		
		
		editor_generic::addeditor('list',new m_list_list);
		$this->editors['tabcontrol']->tabs['main']->div->append_child($this->editors['list']);
		
		
	}
	
	function html_head()
	{
		$this->args['ed_count']=$this->rootnode->setting_val($this->oid,$this->long_name.'._count',20);
		$this->args['ed_offset']=$this->rootnode->setting_val($this->oid,$this->long_name.'._offset',0);

		parent::html_head();
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->args))$this->args=Array();
		foreach($_GET as $i => $v)
			if(preg_match('/[0-9]+/',$i))
				$keys[$i]=$v;
		$this->context[$this->long_name.".pager.ed_count"]['var']='ed_count';
		$this->context[$this->long_name.".pager.ed_offset"]['var']='ed_offset';
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['id_list']=$this->editors['list']->id_gen();
		foreach($this->editors as $i=>$e)
		{
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=$keys;
			$e->args=&$this->args;
		}
		foreach($this->editors as $e)
			$e->bootstrap();
	}
	
	function handle_event($ev)
	{
		global $sql;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$customid=$ev->context[$ev->parent_name]['id_list'];
		$setting_tool=new settings_tool;
		$this->args['ed_count']=$sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.'._count',$_SESSION['uid'],0)));
		if($this->args['ed_count']=='')$this->args['ed_count']=20;
		$this->args['ed_offset']=$sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.'._offset',$_SESSION['uid'],0)));
		if($this->args['ed_offset']=='')$this->args['ed_offset']=0;
		switch($ev->rem_name)
		{
			case 'pager.ed_count':
				$this->args['ed_count']=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'._count',$_SESSION['uid'],0,$this->args['ed_count']));
				$reload_list=true;
				break;
			case 'pager.ed_offset':
				$this->args['ed_offset']=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'._offset',$_SESSION['uid'],0,$this->args['ed_offset']));
				$reload_list=true;
				break;
				
		}
		if($reload_list)
		{
			
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$r=new m_list_list;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".list";
			$r->etype=$ev->parent_type.".m_list_list";

			#$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){/* window.location.reload(true);*/};";
			//common part
		}
		editor_generic::handle_event($ev);
	}
}

class auto_tr extends dom_any
{
	function __construct()
	{
		parent::__construct('tr');
		unset($this->id);
		$this->td=new dom_td;
		unset($this->td->id);
		dom_any::append_child($this->td);
	}
	
	function append_child($node)
	{
		$this->td->append_child($node);
	}
	
	function html_inner()
	{
		if(is_array($this->td->nodes))foreach($this->td->nodes as $n)
		{
			$this->td->html_head();
			$n->html();
			$this->td->html_tail();
		}
	}

}



class m_list_list extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->style=new dom_style;
		$this->style->attributes['type']='text/css';
		$this->style->exstyle['.m_list_list td']['margin']='0px';
		$this->style->exstyle['.m_list_list td']['padding']='0px';
		$this->style->exstyle['.m_list_list td']['border']='1px solid black';
		$this->style->exstyle['.m_list_list']['border-collapse']='collapse';
		$this->append_child($this->style);
		$this->wc=new dom_table;
		$this->wc->css_class='m_list_list';
		$this->wr=new dom_tr;
		$this->append_child($this->wc);
		$this->wc->append_child($this->wr);
		
		$this->tbl=new dom_table;
		$this->tbl->css_class='m_list_list';
		$this->tr=new auto_tr;
		$this->append_child($this->tbl);
		$this->tbl->append_child($this->tr);
		
		$this->single=new dom_table;
		$this->single->css_class='m_list_list';
		$this->append_child($this->single);
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		unset($this->key_by_id);
		unset($this->key_by_name);
		unset($this->col_by_id);
		unset($this->col_by_name);
		unset($this->fetchq);
		unset($this->captions);
		unset($this->resizers);
		$this->editors=Array();
		$this->tr->td->nodes=Array();
		$this->wr->nodes=Array();
		$this->use_single=false;
		fetch_object($this);
		//print htmlspecialchars(serialize($this));

		
		//editor_generic::addeditor('m_text',new m_text);
		//$this->td->append_child($this->editors['m_text']);
		$this->fetchq=new query_gen_ext('SELECT');
		$this->fetchq->from->exprs[]=new sql_column(NULL,$this->sql_table);
		$keys_found=0;
		foreach($this->key_by_id as $c)
		{
			if(isset($this->keys[$c['id']]))
			{
				$wh=new sql_expression('=',Array(
					new sql_column(NULL,NULL,$c['name']),
					new sql_immed($this->keys[$c['id']])
					));
				$this->fetchq->where->exprs[]=$wh;
				$keys_found++;
			}
			$wh=new sql_column(NULL,NULL,$c['name'],'c'.$c['id']);
			$this->fetchq->what->exprs[]=$wh;
			//add to key list
			if(!isset($this->keys[$c['id']]))$this->keys[$c['id']]='';
		}
		foreach($this->col_by_id as $c)
		{
			if($keys_found==count($this->key_by_id))
			{
				$tr=new dom_tr;
				unset($tr->id);
				$this->single->append_child($tr);
				$th=new dom_td;
				unset($th->id);
				$tr->append_child($th);
				$txt=new dom_statictext($c['hr_name']);
				$th->append_child($txt);
				$th->attributes['title']=$e['name'];
				
				$td=new dom_td;
				$tr->append_child($td);

				if($c['editor']!='')
					$ed=$c['editor'];
				else
					$ed='m_text';
				editor_generic::addeditor('c'.$c['id'],new $ed($c));
				$this->editors_by_id[$c['id']]=$this->editors['c'.$c['id']];
				$this->editors_by_id[$c['id']]->css_class='c'.$c['id'];
				$this->editors_by_id[$c['id']]->oid=$c['id'];
				$td->append_child($this->editors['c'.$c['id']]);
				$this->use_single=true;
			}else{
				if($c['editor']!='')
					$ed=$c['editor'];
				else
	//				$ed='m_text';
					$ed='v_text_keysel';
				editor_generic::addeditor('c'.$c['id'],new $ed($c));
				$this->editors_by_id[$c['id']]=$this->editors['c'.$c['id']];
				$this->editors_by_id[$c['id']]->css_class='c'.$c['id'];
				$this->editors_by_id[$c['id']]->oid=$c['id'];
				$this->tr->append_child($this->editors['c'.$c['id']]);
				//caption
				$wd=new dom_td;
				$this->resizers[$c['id']]=new dom_div;
				$this->resizers[$c['id']]->css_class='c'.$c['id'];
				$this->resizers[$c['id']]->before_id='r';
				$this->resizers[$c['id']]->after_id='resize_stylex';
				$this->captions[$c['id']]=new dom_statictext;
				$this->wr->append_child($wd);
				$wd->append_child($this->resizers[$c['id']]);
				$this->resizers[$c['id']]->append_child($this->captions[$c['id']]);
			}
			//add to fetch query
			$wh=new sql_column(NULL,NULL,$c['name'],'c'.$c['id']);
			$this->fetchq->what->exprs[]=$wh;
			//add style
			$this->style->exstyle['.c'.$c['id']]['width']='50px';
			#$this->style->exstyle['.c'.$c['id']]['overflow']='hidden';
			#$this->style->exstyle['.c'.$c['id']]['padding-right']='1px';
			$this->style->exstyle['.c'.$c['id']]['margin']='0px';
			$this->style->exstyle['.c'.$c['id']]['padding']='0px';
//			$this->style->exstyle['.c'.$c['id']]['margin']='2px';
			$this->style->exstyle['.c'.$c['id']." input"]['width']='100%';
			$this->style->exstyle['.c'.$c['id']." input"]['margin']='0px';
			$this->style->exstyle['.c'.$c['id']." input"]['padding']='0px';
			$this->style->exstyle['.c'.$c['id']." input"]['border']='0px';
			$this->context[$this->long_name.'.c'.$c['id']]['var']='c'.$c['id'];
		}
		if(!is_array($this->args))$this->args=Array();
		if(!is_array($this->keys))$this->keys=Array();
		foreach($this->editors as $i=>$e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
		}
		
	}
	
	
	function html_inner()
	{
	global $sql;
	//output styles
	foreach($this->col_by_id as $id => $e)
	{
		$this->style->exstyle['.c'.$id]['width']=$this->rootnode->setting_val($id,$this->long_name.'._width',50).'px';
		
		$this->resizers[$id]->attributes['onmouseup']="if(resizer.obj){ save_setting_value('".$id."','".$this->long_name."._width',resizer.obj.clientWidth);";
//		$this->resizers[$id]->attributes['onmouseup'].="save_setting_value('".$id."','".$this->long_name."._height',resizer.obj.clientHeight);";
		$this->resizers[$id]->attributes['onmouseup'].="}";
		$this->resizers[$id]->css_class='c'.$id;
	}
	$this->style->html();
	$this->resizer->before_id='r';
	$this->resizer->after_id='_resize_stylex';
	if(!$this->use_single)
	{
		//output captions using hr_names
		
		foreach($this->col_by_id as $id => $e)
		{
			$this->resizers[$id]->css_style['background-color']=string_to_color($e['name'],1);
			$this->resizers[$id]->css_style['color']=($e['sql_keyname']=='PRIMARY')?'red':'black';
			$this->captions[$id]->text=$e['hr_name'];
			$this->resizers[$id]->attributes['title']=$e['name'];
		}
		$this->wc->html();
		//output data (set keys and oids coorectly
		$this->tbl->html_head();
		$this->fetchq->lim_count=$this->args['ed_count'];
		$this->fetchq->lim_offset=$this->args['ed_offset'];
	}
	
	$res=$sql->query($this->fetchq->result());
	while($row=$sql->fetcha($res))
	{
		foreach($this->keys as $i => $k)
			$this->keys[$i]=$row['c'.$i];
		foreach($row as $k=>$v)
			$this->args[$k]=$v;
		if(!$this->use_single)
		{
			$this->tr->id_alloc();
		}else{
			$this->single->id_alloc();
		}
		foreach($this->editors_by_id as $i => $c)
		{
			$c->bootstrap();
		}
		if(!$this->use_single)
		{
			$this->tr->html();
		}else{
			$this->single->html();
		}
	}
	if(!$this->use_single)
	{
		$this->tbl->html_tail();
	}
	//$this->rootnode->out(htmlspecialchars($this->fetchq->result()));
	$this->rootnode->out(htmlspecialchars($this->oid));
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}

class v_text extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->main=$this;
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
	}
	
	function html_inner()
	{
		$this->rootnode->out(htmlspecialchars($this->args[$this->context[$this->long_name]['var']]));
	}
	
	function handle_event($ev)
	{
	}
}

class v_text_keysel extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->main=$this;
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
	}
	
	function html_head()
	{
		$link="?p=".$this->com_parent->oid;
		if(is_array($this->keys))foreach($this->keys as $i =>$k)
		{
			$link.="&".urlencode($i)."=".urlencode($k);
		}
		$this->attributes['onclick']="window.location.assign('".js_escape($link)."');";
		parent::html_head();
	}
	
	function html_inner()
	{
		$this->rootnode->out(htmlspecialchars($this->args[$this->context[$this->long_name]['var']]));
	}
	
	function handle_event($ev)
	{
	}
}

class m_text extends editor_text
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
	
	function update_bound($ev)
	{
		global $sql,$ddc_key_special_types,$ddc_suffixes;
		$res=$sql->query("SELECT name,sql_table,parentid,metatables FROM `".TABLE_META_TREE."` WHERE id=".$this->oid);
		$row=$sql->fetcha($res);
		$table=$row['sql_table'];
		$col=$row['name'];
		$obj=$row['parentid'];
		$has_shadow=preg_match('/shadow/',$row['metatables']);
		$knl='';
		
		foreach($ddc_key_special_types as $k)
		{
			if($knl != '')$knl.=",";
			$knl.="'".$sql->esc($k)."'";
		}
		$res=$sql->query("SELECT id,name FROM `".TABLE_META_TREE."` WHERE sql_keyname='PRIMARY' AND isstored=1 AND sql_type NOT IN (".$knl.") AND sql_table='".$sql->esc($table)."' AND parentid=".$obj." GROUP BY id,name");
		
		while($row=$sql->fetcha($res))
			$key_list[$row['id']]=$row;
		$sql->free($res);
		$upd='';
		$keys_defined=0;
		if(is_array($key_list))foreach($key_list as $k => $v)
		{
			if($upd!='')$upd.=" AND";
			if(!isset($ev->keys[$k]))
				break;
			$upd.=" `".$sql->esc($v['name'])."`='".$sql->esc($ev->keys[$k])."'";
			$keys_defined++;
		}
		//all keys has to be defined and not try to store key column
		if(count($key_list) == $keys_defined && !isset($ev->keys['oid']) && $keys_defined!=0)
		{
			if($has_shadow)
				$q="UPDATE `".$sql->esc($table.$ddc_suffixes['shadow'])."` SET `".$sql->esc($col)."`='".$sql->esc($_POST['val'])."' WHERE".$upd;
			else
				$q="UPDATE `".$sql->esc($table)."` SET `".$sql->esc($col)."`='".$sql->esc($_POST['val'])."' WHERE".$upd;
			//print "alert('".js_escape($q)."');";
			$res=$sql->query($q);
			if($res===FALSE)$ev->failure=$sql->err();
		}else{
			#$ev->failure=count($key_list) ."==". $keys_defined ."&& !".isset($ev->keys['oid']);
		}
	}
	
	function handle_event($ev)
	{
		#global $sql,$ddc_key_special_types,$ddc_suffixes;
		$this->oid=$ev->context[$ev->long_name]['oid'];
		$this->update_bound($ev);
		parent::handle_event($ev);
		return;
	}
}

class m_number extends m_text
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
	
	function bootstrap()
	{
		parent::bootstrap();
		$this->main->attributes['onkeypress']="return mn_keypress(event,this);";
	}
	
}

class m_datetime extends m_text
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->date_selector=new dom_div;
		$this->append_child($this->date_selector);
	}
	
	function bootstrap()
	{
		parent::bootstrap();
		//$this->main->attributes['onkeypress']="return mn_keypress(event,this);";
	}
	
}

class date_selector extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('main',new editor_text);
		$this->append_child($this->editors['main']);
		$this->editors['main']->etype=$this->etype;
		editor_generic::addeditor('show_update',new editor_hidden);
		$this->append_child($this->editors['show_update']);
		
		#editor_generic::addeditor('dd',new date_selector_month);
		#$this->append_child($this->editors['dd']);
		$this->dd=new dom_div;
		$this->dd->css_class='date_selector_dd';
		$this->append_child($this->dd);
		$this->default_editor='main';
		$this->main=$this->editors['main']->main;

		
	}
	
	function html_head()
	{
		$dt=explode(' ',$this->args[$this->context[$this->long_name]['var']]);
		$ymd=explode('-',$dt[0]);
		$this->editors['main']->args['main']=$ymd[2].".".$ymd[1].".".$ymd[0];
		parent::html_head();
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		#$this->context[$this->long_name]['dd_id']=$this->editors['dd']->id_gen();
		$this->context[$this->long_name]['dd_id']=$this->dd->id_gen();
		
		$this->context[$this->long_name]['main_id']=$this->editors['main']->main_id();
		$this->context[$this->long_name.".main"]['var']='main';
		foreach($this->editors as $i=>$e)
		{
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=Array();
		}
		
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
#		$this->editors['main']->main->attributes['onfocus'].="var dd=\$i('".$this->editors['dd']->id_gen()."');dd.className='date_selector_v';";
		$this->editors['main']->main->attributes['onfocus'].="
		var ed=\$i('".$this->editors['main']->main_id()."');".
		"if(this.hide_timeout)clearTimeout(this.hide_timeout);".
		#"if(\$i('".$this->editors['dd']->id_gen()."').className=='date_selector_dd')chse.send_or_push({static:'".$this->editors['show_update']->send."',val:ed.value,c_id:this.id});";
		"if(\$i('".$this->dd->id_gen()."').className=='date_selector_dd')chse.send_or_push({static:'".$this->editors['show_update']->send."',val:ed.value,c_id:this.id});";
		#$this->editors['main']->main->attributes['onblur'].="this.hide_timeout=setTimeout(\"\$i('".$this->editors['dd']->id_gen()."').className='date_selector_dd';\",100);";
		$this->editors['main']->main->attributes['onblur'].="this.hide_timeout=setTimeout(\"\$i('".$this->dd->id_gen()."').className='date_selector_dd';\",100);";
		
	}
	
	
	function handle_event($ev)
	{
		$this->oid=$ev->context[$ev->long_name]['oid'];
		$oev=clone $ev;
		switch($ev->rem_name)
		{
		case 'main':
			$oev->parent_name=preg_replace('/\\.[^.]+$/','',$oev->parent_name);
			$oev->parent_type=preg_replace('/\\.[^.]+$/','',$oev->parent_type);
			m_text::update_bound($ev);
			if(isset($ev->failure))
			{
				//print 'var x=$i(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\');x.style.backgroundColor=\'pink\';'.
				print 'var x=chse.bgifc(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\',\'pink\');'.
				'if(x){'.
				'$i(x.failure_viewer).style.display=\'\';'.
				'$i(x.failure_viewer_text).innerHTML=\''.js_escape(htmlspecialchars($ev->failure,ENT_QUOTES)).'\';'.
				'}'
				;
			}else
				print 'var x=chse.bgifc(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\',\'\');'.
				'if(x){'.
				//print 'var x=$i(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\');x.style.backgroundColor=\'\';'.
				'$i(x.failure_viewer).style.display=\'none\';'.
				'}';
			#editor_generic::handle_event($ev);
			#m_text::handle_event($ev);
		case 'show_update':
			$this->args['full']=$_POST['val'];
			$dd_id=$ev->context[$oev->parent_name]['dd_id'];
			$r=new date_selector_month;
			
			$r->custom_id=$dd_id;
			$r->context=&$oev->context;
			$r->keys=&$oev->keys;
			$r->oid=$this->oid;
			$r->args=$this->args;
			$r->name=$oev->parent_name.".dd";
			$r->etype=$oev->parent_type.".date_selector_month";

			#$r->bootstrap();
			print "var nya=\$i('".js_escape($dd_id)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){/* window.location.reload(true);*/};";
			print "nya.className='date_selector_v';";
			if($oev->rem_name=='main')return;
			break;
		}
		editor_generic::handle_event($ev);
	}
}

class date_selector_month extends dom_div
{
	function __construct()
	{
		global $sql;
		parent::__construct();
		$this->etype=get_class($this);
		$this->css_class='date_selector_dd';
		#year selector
		$this->ys_cont=new container_autotable;
		$this->append_child($this->ys_cont);
		editor_generic::addeditor('pys',new editor_divbutton);
		$this->editors['pys']->attributes['value']='<';
		$this->editors['pys']->css_class='divbutton';
		$this->ys_cont->append_child($this->editors['pys']);
		
		$this->cy=new dom_statictext;
		$md=new dom_div;unset($md->id);$md->css_class='date_selector_exte';
		$md->append_child($this->cy);
		$this->ys_cont->append_child($md);
		
		editor_generic::addeditor('cy',new editor_hidden);
		$md->append_child($this->editors['cy']);
		
		editor_generic::addeditor('nys',new editor_divbutton);
		$this->editors['nys']->attributes['value']='>';
		$this->editors['nys']->css_class='divbutton';
		$this->ys_cont->append_child($this->editors['nys']);
		#month selector
		$this->ms_cont=new container_autotable;
		$this->append_child($this->ms_cont);
		editor_generic::addeditor('pms',new editor_divbutton);
		$this->editors['pms']->attributes['value']='<';
		$this->editors['pms']->css_class='divbutton';
		$this->ms_cont->append_child($this->editors['pms']);
		
		$md=new dom_div;unset($md->id);$md->css_class='date_selector_exte';
		$this->cm=new dom_statictext;
		$md->append_child($this->cm);
		$this->ms_cont->append_child($md);
		
		editor_generic::addeditor('cm',new editor_hidden);
		$md->append_child($this->editors['cm']);
		
		editor_generic::addeditor('nms',new editor_divbutton);
		$this->editors['nms']->attributes['value']='>';
		$this->editors['nms']->css_class='divbutton';
		$this->ms_cont->append_child($this->editors['nms']);
		
		$this->month=new dom_table;
		$this->month->css_class='month';
		
		$this->tr=new dom_tr;
		$this->td=new dom_td;
		unset($this->td->id);
		unset($this->tr->id);
		//editor_generic::addeditor('d',new editor_statictext_af);
		$this->d=new dom_div;
		unset($this->d->id);
		$this->d_text=new dom_statictext;
		$this->d->append_child($this->d_text);
		$this->append_child($this->month);
		$this->month->append_child($this->tr);
		$this->tr->append_child($this->td);
		$this->td->append_child($this->d);
		
		#$this->month->css_style['float']='left';
		/*$this->month->css_style['border']='1px solid gray';
		$this->month->css_style['border-collapse']='collapse';
		$this->month->css_style['margin']='5px';
		$this->td->css_style['border']='1px solid gray';*/
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->parent_name=preg_replace('/\\.[^.]+$/','',$this->long_name);
		$main_id=$this->context[$this->parent_name]['main_id'];
		$this->context[$this->long_name]['cy_id']=$this->editors['cy']->id_gen();
		$this->context[$this->long_name]['cm_id']=$this->editors['cm']->id_gen();
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->main_id=$main_id;
		$refocus="\$i('".js_escape($main_id)."').focus();return false;";
		$this->d->attributes['onmousedown']=$refocus;
		$ujs="(function(){
			var cm=\$i('".js_escape($this->editors['cm']->id_gen())."');
			var cy=\$i('".js_escape($this->editors['cy']->id_gen())."');
			m=parseInt(cm.value);
			y=parseInt(cy.value);
			{op};
			return m+','+y;
		})()";
		$this->editors['nys']->attributes['onmousedown']=$refocus;
		$this->editors['nys']->val_js=str_replace('{op}','y++',$ujs);
		$this->editors['pys']->attributes['onmousedown']=$refocus;
		$this->editors['pys']->val_js=str_replace('{op}','y--',$ujs);
		$this->editors['nms']->attributes['onmousedown']=$refocus;
		$this->editors['nms']->val_js=str_replace('{op}','m++;if(m==13){m=1;y++}',$ujs);
		$this->editors['pms']->attributes['onmousedown']=$refocus;
		$this->editors['pms']->val_js=str_replace('{op}','m--;if(m==0){m=12;y--}',$ujs);
		
		foreach($this->editors as $i=>$e)
		{
			$this->context[$this->long_name.".".$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
	}
	
	function ds_parse_gen($d)
	{
		if(!preg_match('/\\d+/',$d[0]))return false;
		if(!preg_match('/\\d+/',$d[1]))return false;
		if(!preg_match('/\\d+/',$d[2]))return false;
		$r->day=intval($d[2]);
		$r->month=intval($d[1]);
		$r->year=intval($d[0]);
		if(!isset($r->day))return false;
		if(!isset($r->month))return false;
		if(!isset($r->year))return false;
		if($r->year<0)return false;
		#if(r.year<100)r.year+=date.getYear() - date.getYear()%100 + ((r.year>(date.getYear()%100))?100:0);
		if($r->year<100)$r->year+=date("Y")-date("Y")%100 - (($r->year > date("Y")%100)?100:0);
		#$r->year+=date("Y")-date("Y")%100;
		if($r->month>12)return false;
		if($r->month<1)return false;
		if($r->month==12)
		{
			$day=date("j",mktime(0,0,0,1,0,$r->year+1));
		}else{
			$day=date("j",mktime(0,0,0,$r->month+1,0,$r->year));
		}
		if($r->day>$day)return false;
		if($r->day<0)return false;
		return $r;
	}

	function ds_parse_eu($v)
	{
		/*31.12.2009*/
		if(preg_match('/^\d\d?.\d\d?.\d\d(?:\d\d)?$/',$v))
		{
			$d=split(' ',preg_replace('/^(\d\d?).(\d\d?).(\d\d(?:\d\d)?)$/','$3 $2 $1',$v));
			return $this->ds_parse_gen($d);
		}else{
			return false;
		}
	}
	
	function ds_parse_iso($v)
	{
		if(preg_match('/^[0-9][0-9](?:[0-9][0-9])?.[0-9][0-9]?.[0-9][0-9]?$/',$v))
		{
			$d=split(' ',preg_replace('/^([0-9][0-9](?:[0-9][0-9])?).([0-9][0-9]?).([0-9][0-9]?)$/','$1 $2 $3',$v));
			return $this->ds_parse_gen($d);
		}else{
			return false;
		}
	}
	
	function ds_parse_us($v)
	{
		if(preg_match('/^\d\d?.\d\d?.\d\d(?:\d\d)?$/',$v))
		{
			$d=split(' ',preg_replace('/^(\d\d?).(\d\d?).(\d\d(?:\d\d)?)$/','$3 $1 $2',$v));
			return $this->ds_parse_gen($d);
		}else{
			return false;
		}
	}
	function ds_parse($v)
	{
		if(preg_match('/.*-.*-.*/',$v))
		{
			$res=$this->ds_parse_iso($v);
			if(is_object($res))return $res;
		}
		if(preg_match('/.*\\..*\\..*/',$v))
		{
			$res=$this->ds_parse_eu($v);
			if(is_object($res))return $res;
		}
		if(preg_match('/.*\\/.*\\/.*/',$v))
		{
			$res=$this->ds_parse_us($v);
			if(is_object($res))return $res;
		}
		$res=$this->ds_parse_eu($v);
		if(is_object($res))return $res;
		$res=$this->ds_parse_iso($v);
		if(is_object($res))return $res;
		$res=$this->ds_parse_us($v);
		if(is_object($res))return $res;
		return false;
	}
	
	
	function html_inner()
	{
		global $sql;
		$full=$this->ds_parse($this->args['full']);
		$now->year=date("Y");
		$now->month=date("n");
		$now->day=date("j");
		if(is_object($full))
		{
			$sel=$full;
		}else{
			$sel->year=intval($this->args['cy']);
			if($sel->year<=0 || $sel->year =='')$sel->year=date("Y");
			$sel->month=intval($this->args['cm']);
			if($sel->month<=0 || $sel->month =='' || $sel->month>12)$sel->month=date("m");
			$now->year=-1;
			$now->month=-1;
			$now->day=-1;
		}
		$this->args['cy']=$sel->year;
		$this->args['cm']=$sel->month;
		$this->cm->text=loc_get_val(-1,'month_name.'.$sel->month,date("F",mktime(0,0,0,$sel->month,1,2009)));
		$this->cy->text=$sel->year;
		$res=$sql->query("SELECT `working`,`isshort`,`day` FROM `calendar` WHERE `year`=".$sel->year." AND `month`=".$sel->month);
		while($row=$sql->fetcha($res))
		{
			$dc[$row['day']]=Array($row['working'],$row['isshort']);
		}
		$this->ys_cont->html();
		$this->ms_cont->html();
		$this->month->html_head();
		
		$mm=$sel->month;
		unset($this->td->attributes['colspan']);
		$dd=1;
		$dd_lim=($mm==12)?date("j",mktime(0,0,0,1,0,$sel->year+1)):date("j",mktime(0,0,0,$mm+1,0,$sel->year));
		#while($dd<=$dd_lim)
		for($rr=0;$rr<6;$rr++)
		{
			$this->tr->id_alloc();
			$this->tr->html_head();
			for($k=0;$k<7;$k++)
			{
				$wd=$k+1;if($wd==7)$wd=0;
				if($dd==1)
				{
					if(date("w",mktime(0,0,0,$mm,$dd,$sel->year))==$wd)
					{
						$valid=true;
					}else{
						$valid=false;
						$this->d_text->text='';
						unset($this->d->attributes['onclick']);
						$this->td->html();
					}
				}else{
					if($dd>$dd_lim)
					{
						$valid=false;
						$this->d_text->text='';
						unset($this->d->attributes['onclick']);
						$this->td->html();
					}else{
						$valid=true;
					}
				}
				if($valid)
				{
					#$res=$sql->fetchn($sql->query("SELECT `working`,`isshort` FROM `calendar` WHERE `day`=".$dd." AND `month`=".$mm." AND `year`=".$year));
					$res=$dc[$dd];
					$class='';
					if($res[0]==='0')
						$class.='n';
					#	$this->d->css_style['background-color']='#FF9999';
					if($res[1]==='1')
						$class.="h";
					if($full->year==$sel->year && $full->month==$mm && $full->day==$dd)
						$class.='s';
					if($now->year==$sel->year && $now->month==$mm && $now->day==$dd)
						$class.='c';
					#	$this->d->css_style['color']='red';
					$this->d_text->text=$dd;
#					$this->keys[$this->editors['d']->oid]=$sel->year."-".$mm."-".$dd;
					$this->d->attributes['onclick']="var m=\$i('".js_escape($this->main_id)."');m.focus();m.value='".$dd.".".$sel->month.".".$sel->year."';chse.timerch(false);";
					$dd+=1;
					if($class !='')$this->d->css_class='dsc_'.$class;
					$this->td->html();
					unset($this->d->css_class);
					#unset($this->d->css_style['background-color']);
					#unset($this->d->css_style['color']);
				}
				$this->td->id_alloc();
			}
			if($dd>$dd_lim)break;
			$this->tr->html_tail();
		}
		
		
		
		
		$this->month->html_tail();
		$this->month->id_alloc();
	}
	
	function handle_event($ev)
	{
		switch($ev->rem_name)
		{
		case 'nys':
		case 'pys':
		case 'nms':
		case 'pms':
			$r=new date_selector_month;
			$r->custom_id=$ev->context[$ev->parent_name]['htmlid'];
			$my=split(',',$_POST['val']);
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			
			$r->args=Array('cm'=>$my[0],'cy'=>$my[1]);
			#print '$i("debug").textContent="'.js_escape($ev->context[$ev->parent_name]['htmlid']).'";';
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;

			#$r->bootstrap();
			print "var nya=\$i('".js_escape($ev->context[$ev->parent_name]['htmlid'])."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){/* window.location.reload(true);*/};";
			print "nya.className='date_selector_v';";
		}
		editor_generic::handle_event($ev);
	}
}
	





##################################################################################
################## m_int_ud - integer increment/decrement#########################
##################################################################################
class m_int_ud extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->cont=new container_autotable;
		$this->append_child($this->cont);
		$this->dec=new dom_any_noterm('input');
		$this->dec->attributes['type']='button';
		$this->dec->attributes['value']="<";
		$this->inc=new dom_any_noterm('input');
		$this->inc->attributes['type']='button';
		$this->inc->attributes['value']=">";
		editor_generic::addeditor('main',new m_text);
		$this->cont->append_child($this->dec);
		$this->cont->append_child($this->editors['main']);
		$this->cont->append_child($this->inc);
		$this->default_editor='main';
		$this->main=$this->editors['main']->main;
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$i_id=$this->editors['main']->main_id();
		$this->editors['main']->oid=$this->oid;
		$this->editors['main']->context=&$this->context;
		$this->editors['main']->keys=&$this->keys;
		$this->editors['main']->args=&$this->args;
		$this->context[$this->long_name.'.main']['var']=$this->context[$this->long_name]['var'];
		
		$this->dec->attributes['onclick']="var a=\$i('".$i_id."');a.focus();try{var i=parseInt(a.value);a.value=i-1;}finally{if(a.value=='')a.value=0;};";
		$this->inc->attributes['onclick']="var a=\$i('".$i_id."');a.focus();try{var i=parseInt(a.value);a.value=i+1;}finally{if(a.value=='')a.value=0;};";
		$this->editors['main']->bootstrap();
		
	}
	
	function handle_event($ev)
	{
		//$this->editors['main']->handle_event($ev);
		editor_generic::handle_event($ev);
	}
}




function calendar_fill($year=NULL)
{
	global $sql;
	if(!isset($year))$year=date("Y");
	$q_ok=0;
	$q_failed=0;
	$year=intval($year);
	$days_in_year=$sql->fetch1($sql->query("select datediff(makedate(".($year+1).",1),makedate(".$year.",1))"));
	for($k=1;$k<=$days_in_year;$k++)
	{
		$set="`date`=makedate(".$year.",".$k."),`year`=".$year.",`month`=month(makedate(".$year.",".$k.")),`day`=dayofmonth(makedate(".$year.",".$k.")),`working`=if(dayofweek(makedate(".$year.",".$k.")) in (1,7),0,1), `weekday`=if(dayofweek(makedate(".$year.",".$k."))=1,7,dayofweek(makedate(".$year.",".$k."))-1)";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);
		if($re)$q_ok++;else $q_failed++;
	}
	//russian
	
		$set="`date`='".$year."-12-31',`isshort`=1";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
#	- 1, 2, 3, 4 и 5 января - Новогодние каникулы; 
		$set="`date`='".$year."-01-01',`year`=".$year.",`month`=1,`day`=1,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;

		$set="`date`='".$year."-01-02',`year`=".$year.",`month`=1,`day`=2,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-01-03',`year`=".$year.",`month`=1,`day`=3,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-01-04',`year`=".$year.",`month`=1,`day`=4,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-01-05',`year`=".$year.",`month`=1,`day`=5,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
#	- 7 января - Рождество Христово; 
		$set="`date`='".$year."-01-07',`year`=".$year.",`month`=1,`day`=7,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
#- 23 февраля - День защитника Отечества; 
		$set="`date`='".$year."-02-22',`year`=".$year.",`month`=2,`day`=22,`isshort`=1";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-02-23',`year`=".$year.",`month`=2,`day`=23,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
#- 8 марта - Международный женский день; 
		$set="`date`='".$year."-03-07',`isshort`=1";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-03-08',`year`=".$year.",`month`=3,`day`=8,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
#- 1 мая - Праздник Весны и Труда; 
		$set="`date`='".$year."-04-30',`isshort`=1";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-05-01',`year`=".$year.",`month`=5,`day`=1,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
#- 9 мая - День Победы; 
		$set="`date`='".$year."-05-08',`isshort`=1";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-05-09',`year`=".$year.",`month`=5,`day`=9,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
#- 12 июня - День России; 
		$set="`date`='".$year."-06-11',`isshort`=1";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-06-12',`year`=".$year.",`month`=6,`day`=12,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
#- 4 ноября - День народного единства.
		$set="`date`='".$year."-11-03',`isshort`=1";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		$set="`date`='".$year."-11-04',`year`=".$year.",`month`=11,`day`=4,`working`=0";
		$re=$sql->query("insert into `calendar` set ".$set." on duplicate key update ".$set);	if($re)$q_ok++;else $q_failed++;
		return Array("failed"=>$q_failed,"ok"=>$q_ok);
}



class calendar_viewer extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('year_selector',new m_int_ud);
		$this->append_child($this->editors['year_selector']);
		editor_generic::addeditor('year_fill',new editor_button);
		$this->append_child($this->editors['year_fill']);
		editor_generic::addeditor('click_mode',new editor_select);
		$this->append_child($this->editors['click_mode']);
		
		
		editor_generic::addeditor('months_cont',new calendar_viewer_months);
		$this->append_child($this->editors['months_cont']);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->keys=Array();
		if(!is_array($this->args))$this->args=Array();
		if(isset($_SESSION['calendar_viewer']['year']))$this->args['year_selector']=$_SESSION['calendar_viewer']['year'];
		else $this->args['year_selector']=date("Y");$_SESSION['calendar_viewer']['year']=date("Y");
		if(isset($_SESSION['calendar_viewer']['c']))$this->args['click_mode']=$_SESSION['calendar_viewer']['c'];
		else{$this->args['click_mode']=0;$_SESSION['calendar_viewer']['c']=0;};
		$this->editors['year_fill']->attributes['value']=loc_get_val($this->oid,'calendar_viewer.year_fill','Fill year with default data');
		$this->editors['click_mode']->options[0]=loc_get_val($this->oid,'calendar_viewer.click_opt_w','Toggle working');
		$this->editors['click_mode']->options[1]=loc_get_val($this->oid,'calendar_viewer.click_opt_s','Toggle short');
		$this->context[$this->long_name]['months_cont_id']=$this->editors['months_cont']->id_gen();
		foreach($this->editors as $i=>$e)
		{
			$this->context[$this->long_name.".".$i]['var']=$i;
			$e->oid=$this->oid;
			$e->context=&$this->context;
			//$e->keys=$keys;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
		}
		foreach($this->editors as $e)
			$e->bootstrap();
		
	}
	
	function handle_event($ev)
	{
		$reload=false;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$this->args=Array();
		switch($ev->rem_name)
		{
		case 'year_selector':
				$_SESSION['calendar_viewer']['year']=$_POST['val'];
				$reload=true;
				break;
		case 'year_fill':
				$res=calendar_fill($_SESSION['calendar_viewer']['year']);
				print "alert('ok:".$res['ok'].",failed:".$res['failed']."');";
				$reload=true;
				break;
		case 'click_mode':
				$_SESSION['calendar_viewer']['c']=$_POST['val'];
				break;
		}
		if($reload)
		{
			
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$this->args['year_selector']=$_SESSION['calendar_viewer']['year'];
			$r=new calendar_viewer_months;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".months_cont";
			$r->etype=$ev->parent_type.".calendar_viewer_months";

			#$r->bootstrap();
			print "var nya=\$i('".js_escape($ev->context[$ev->parent_name]['months_cont_id'])."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){/* window.location.reload(true);*/};";
			//common part
		}
		
		editor_generic::handle_event($ev);
	}
}

class calendar_viewer_months extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->year=new dom_div;
		editor_generic::addeditor('y',new editor_statictext);
		$this->append_child($this->year);
		$this->year->append_child($this->editors['y']);
		
		$this->month=new dom_table;
		
		$this->tr=new dom_tr;
		$this->td=new dom_td;
		//editor_generic::addeditor('d',new editor_statictext_af);
		editor_generic::addeditor('d',new calendar_viewer_day);
		$this->append_child($this->month);
		$this->month->append_child($this->tr);
		$this->tr->append_child($this->td);
		$this->td->append_child($this->editors['d']);
		
		$this->month->css_style['float']='left';
		$this->month->css_style['border']='1px solid gray';
		$this->month->css_style['border-collapse']='collapse';
		$this->month->css_style['margin']='5px';
		$this->td->css_style['border']='1px solid gray';
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		foreach($this->editors as $i=>$e)
		{
			$this->context[$this->long_name.".".$i]['var']=$i;
			$e->oid=$this->oid;
			$e->context=&$this->context;
			//$e->keys=$keys;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
		}
		foreach($this->editors as $e)
			$e->bootstrap();
		
	}
	
	function html_inner()
	{
		global $sql;
		$year=intval($this->args['year_selector']);
		if($year<=0 || $year =='')$year=date("Y");
		$this->args['y']=$year;
		#$this->year->html();
		$res=$sql->fetch1($sql->query("SELECT `id` FROM `".TABLE_META_TREE."` WHERE `name`='date' AND `sql_table`='calendar' AND `isstored`=1 AND `parentid`=".$this->oid));
		$this->editors['d']->oid=$res;
		$res=$sql->query("SELECT `working`,`isshort`,`day`,`month` FROM `calendar` WHERE `year`=".$year);
		while($row=$sql->fetcha($res))
		{
			$dc[$row['month']][$row['day']]=Array($row['working'],$row['isshort']);
		}
		
		for($mm=1;$mm<=12;$mm++)
		{
			$this->month->html_head();
			
			$this->tr->html_head();
			$this->td->attributes['colspan']=7;
			$this->args['d']=loc_get_val(-1,"month_name.".$mm,date("F", mktime (0,0,0,$mm,1,$year)));
			$this->td->html();
			$this->tr->html_tail();
			unset($this->td->attributes['colspan']);
			$dd=1;
			$dd_lim=($mm==12)?date("j",mktime(0,0,0,1,0,$year+1)):date("j",mktime(0,0,0,$mm+1,0,$year));
			#while($dd<=$dd_lim)
			for($rr=0;$rr<6;$rr++)
			{
				$this->tr->id_alloc();
				$this->tr->html_head();
				for($k=0;$k<7;$k++)
				{
					$wd=$k+1;if($wd==7)$wd=0;
					if($dd==1)
					{
						if(date("w",mktime(0,0,0,$mm,$dd,$year))==$wd)
						{
							$valid=true;
						}else{
							$valid=false;
							$this->args['d']='';
							$this->editors['d']->bootstrap();
							$this->td->html();
						}
					}else{
						if($dd>$dd_lim)
						{
							$valid=false;
							$this->editors['d']->bootstrap();
							$this->args['d']='';
							$this->td->html();
						}else{
							$valid=true;
						}
					}
					if($valid)
					{
						#$res=$sql->fetchn($sql->query("SELECT `working`,`isshort` FROM `calendar` WHERE `day`=".$dd." AND `month`=".$mm." AND `year`=".$year));
						$res=$dc[$mm][$dd];
						if($res[0]==='0')
							#$this->td->css_style['background-color']='#FF9999';
							$this->editors['d']->css_style['background-color']='#FF9999';
						if($res[1]==='1')
							#$this->td->css_style['color']='red';
							$this->editors['d']->css_style['color']='red';
						$this->args['d']=$dd;
						$this->keys[$this->editors['d']->oid]=$year."-".$mm."-".$dd;
						$dd+=1;
						$this->editors['d']->bootstrap();
						$this->td->html();
						unset($this->editors['d']->css_style['background-color']);
						unset($this->editors['d']->css_style['color']);
						$this->keys[$this->editors['d']->oid]='';
						#unset($this->td->css_style['background-color']);
						#unset($this->td->css_style['color']);
					}
					$this->td->id_alloc();
				}
				$this->tr->html_tail();
			}
			
			
			
			
			$this->month->html_tail();
			$this->month->id_alloc();
		}
	}
	
	function handle_event($ev)
	{
		global $sql;
		switch($ev->rem_name)
		{
		case 'd':
			$oid=$ev->context[$ev->long_name]['oid'];
			$id=$ev->context[$ev->long_name]['htmlid'];
			$mode=$_SESSION['calendar_viewer']['c'];
			if($ev->keys[$oid]=='')return;
			$res=$sql->fetchn($sql->query("SELECT `working`,`isshort` FROM `calendar` WHERE `date`='".$sql->esc($ev->keys[$oid])."'"));
			if($res[0]=='1')
				$nbgc="#FF9999";
			else
				$nbgc="";
			if($res[1]=='0')
				$nc='red';
			else
				$nc='';
			if($mode==0)
			{
				$res=$sql->query("UPDATE `calendar` SET `working`=IF(`working`=1,0,1) WHERE `date`='".$sql->esc($ev->keys[$oid])."'");
				if($res===true)print "\$i('".js_escape($id)."').style.backgroundColor='".$nbgc."';";
			}else{
				$res=$sql->query("UPDATE `calendar` SET `isshort`=IF(`isshort`=1,0,1) WHERE `date`='".$sql->esc($ev->keys[$oid])."'");
				if($res===true)print "\$i('".js_escape($id)."').style.color='".$nc."';";
			}
			
			
			break;
		}
		editor_generic::handle_event($ev);
	}
	
}

class calendar_viewer_day extends dom_any
{
	function __construct()
	{
		parent::__construct('div');
		$this->etype=get_class($this);
		$this->txt=new dom_statichtml;
		$this->append_child($this->txt);
		$this->css_style['text-align']='center';
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap_part();
		$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:1,c_id:this.id});";
		
	}
	
	function html()
	{
		//print $this->args[$this->context[$this->long_name]['var']];
		if(is_array($this->args))
			$this->txt->text=htmlspecialchars($this->args[$this->context[$this->long_name]['var']]);
		if($this->txt->text=='')$this->txt->text='&nbsp;';
		parent::html();
	}
	
	function handle_event($ev)
	{
		
	}
	
}


////
////////////											editor_m_object
////													- select object from meta, test


class editor_m_object extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		$this->current_selection=new dom_span;
		$this->append_child($this->current_selection);
		$this->current_selection_text=new dom_statictext;
		$this->current_selection_text->text=' - ';
		$this->current_selection->append_child($this->current_selection_text);
		
		editor_generic::addeditor('pick',new editor_button);$this->editors['pick']->attributes['value']='...';
		$this->append_child($this->editors['pick']);
		
		editor_generic::addeditor('self',new editor_hidden);
		$this->append_child($this->editors['self']);
		
		$this->variants=new dom_div;
		$this->append_child($this->variants);
		$this->variants->css_style['display']='none';
		$this->variants->css_style['position']='absolute';
		$this->variants->css_style['border']='1px solid red';
		$this->variants->css_style['background']='white';
		
		$this->main=$this->editors['pick']->main;
		
	}
	
	function bootstrap()
	{
		#chse.send_or_push({static:'".$this->editors['show_update']->send."',val:ed.value,c_id:this.id});
		$this->long_name=editor_generic::long_name();
		#$this->oid=132;#DEBUG
		#editor_generic::bootstrap_part();
		$this->context[$this->long_name]['rdiv']=$this->variants->id_gen();
		$this->context[$this->long_name]['pick_id']=$this->editors['pick']->main_id();
		$this->context[$this->long_name]['self_id']=$this->editors['self']->main_id();
		$this->context[$this->long_name]['current_selection_id']=$this->current_selection->id_gen();
		$this->context[$this->long_name.'.self']['var']=$this->context[$this->long_name]['var'];
		$this->context[$this->long_name]['v_next']='';
		$this->context[$this->long_name]['i_next']='';
		$this->context[$this->long_name]['rl']=0;
		foreach($this->editors as $i => $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		$this->editors['pick']->attributes['onfocus']='clearTimeout(this.hide_timeout);'.
			'if(typeof(this.sel_path)==\'undefined\')'.
			'{'.
				'this.sel_path=new Array();'.
				'this.sel_path[0]={selection:-1,sub:0};'.
			'};'
			;
		$this->editors['pick']->attributes['onblur']="setTimeout('".
			js_escape(
				"\$i('".$this->variants->id_gen()."').style.display='none';delete \$i('".$this->editors['pick']->id_gen()."').sel_path;"
				)."',110);";
		$this->editors['pick']->attributes['onkeypress']="return emo_keypress(event,this.id);";
	}
	
	function html_inner()
	{
		global $sql;
		#$this->current_selection->attributes['onclick']=$this->editors['pick']->attributes['onclick'];
		$hrarr=$sql->qkv("SELECT a.id,coalesce(b.val,a.name) as hr_name FROM `".TABLE_META_TREE."` as a LEFT OUTER JOIN `".TABLE_META_I18N."` as b ON a.id=b.object AND b.var='name' AND b.loc='".$_SESSION['lang']."' WHERE a.id IN (".str_replace('.',',',$this->args[$this->context[$this->long_name]['var']]).")");
		$ev=explode('.',$this->args[$this->context[$this->long_name]['var']]);
		foreach($ev as $eve)$evh[]=$hrarr[$eve];
		if(is_array($hrarr))
			$hrtext=implode('.',$evh);
		else
			$hrtext='';
		#$this->current_selection_text->text=$this->args[$this->context[$this->long_name]['var']];
		$this->current_selection_text->text=$hrtext;
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		global $sql;
		$oid=$ev->context[$ev->long_name]['oid'];
		switch($ev->rem_name)
		{
		case "pick":
			if($oid==-1)
			{
				$q="SELECT a.id, coalesce(b.val,a.name) as hr_name,(SELECT COUNT(d.`id`) FROM `".TABLE_META_TREE."` as d WHERE d.`parentid`=a.`id` AND d.`sql_type` != '') as num_children FROM `".TABLE_META_TREE."` as a LEFT OUTER JOIN `".TABLE_META_I18N."` as b ON a.id=b.object AND b.var='name' AND b.loc='".$_SESSION['lang']."' WHERE a.sql_type='' AND (a.xobject!='sys' OR a.xobject IS NULL)";
				$variants=$sql->qa($q);
				
			}
			if($oid!=-1)
			{
				$isrow=$sql->qa("SELECT a.sql_type as sql_type, a.rel as rel FROM `".TABLE_META_TREE."` as a WHERE id=".$sql->esc($oid));
				if($isrow[0]['sql_type']=='')
				{
					$q="SELECT a.id, coalesce(b.val,a.name) as hr_name, a.`rel` as num_children FROM `".TABLE_META_TREE."` as a LEFT OUTER JOIN `".TABLE_META_I18N."` as b ON a.id=b.object AND b.var='name' AND b.loc='".$_SESSION['lang']."' WHERE a.parentid='".$sql->esc($oid)."' AND (a.xobject!='sys' OR a.xobject IS NULL)";
					$variants=$sql->qa($q);
					
				}else{
					if($isrow[0]['rel']==0)
						unset($variants);
					else{
						$q="SELECT a.id, coalesce(b.val,a.name) as hr_name, a.`rel` as num_children FROM `".TABLE_META_TREE."` as a LEFT OUTER JOIN `".TABLE_META_I18N."` as b ON a.id=b.object AND b.var='name' AND b.loc='".$_SESSION['lang']."' WHERE a.parentid=(SELECT d.parentid FROM `".TABLE_META_TREE."` as d WHERE d.id=".$sql->esc($isrow[0]['rel'])." ) AND (a.xobject!='sys' OR a.xobject IS NULL)";
						$variants=$sql->qa($q);
						#return;
					}
				}
				
			}
			$r=new editor_m_object_variants;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			if(is_array($variants))
				foreach($variants as $v)
					$r->variants[]=Array('v'=>$v['id'],'n'=>$v['hr_name'],'+'=>(($v['num_children']>0) ? true : NULL));
			else $r->variants=NULL;

			#$r->bootstrap();
			print "var m=\$i('".js_escape($ev->context[$ev->parent_name]['pick_id'])."');";
			print "var d;try{d=\$i(m.sel_path[".$ev->context[$ev->parent_name]['rl']."].id);}catch(e){d=null;};";
			print "if(d!=null)d.style.display='none';";
			print "if(m.sel_path[".$ev->context[$ev->parent_name]['rl']."]==null) m.sel_path[".$ev->context[$ev->parent_name]['rl']."]=new Object();";
			print "m.sel_path[".$ev->context[$ev->parent_name]['rl']."].id='".js_escape($ev->context[$ev->parent_name]['rdiv'])."';";
			print "delete m ; ";
			print "var nya=\$i('".js_escape($ev->context[$ev->parent_name]['rdiv'])."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){/* window.location.reload(true);*/};";
			print "nya.style.display='block';";
			//common part
		}
	}
}

class editor_m_object_variants extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->table=new dom_table;
		$this->tr=new dom_tr;
		$this->td0=new dom_td;
		$this->td1=new dom_td;
		$this->t0=new dom_statictext;
		$this->t1=new dom_statictext;
		$this->d1=new dom_div;
		$this->d2=new dom_div;
		$this->td0->append_child($this->t0);$this->td1->append_child($this->d1);$this->td1->append_child($this->d2);
		$this->d1->append_child($this->t1);
		$this->tr->append_child($this->td0);$this->tr->append_child($this->td1);
		$this->table->append_child($this->tr);
		$this->append_child($this->table);
		editor_generic::addeditor('pick',new editor_button);$this->editors['pick']->attributes['value']='...';
		$this->append_child($this->editors['pick']);
		$this->d2->css_style['display']='none';
		$this->d2->css_style['position']='absolute';
		$this->d2->css_style['background']='white';
		$this->d2->css_style['border']='1px solid red';
		$this->d2->css_style['margin-left']='1em';
		$this->d2->css_style['cursor']='default';
		$this->d1->css_style['cursor']='default';
	}
	
	function html_inner()
	{
		$this->table->html_head();
#		$this->td0->attributes['onmousedown']="\$i('".$this->context[$this->long_name]['pick_id']."').focus();return false;";
#		$this->td1->attributes['onmousedown']="\$i('".$this->context[$this->long_name]['pick_id']."').focus();return false;";
		$this->td0->attributes['onmousedown']="return false;";
		$this->td1->attributes['onmousedown']="return false;";
		$rlist='';
		$rnum=0;
		$this_n=$this->context[$this->long_name]['rn'];
		if(is_array($this->variants))
		foreach($this->variants as $v)
		{
			$this->tr->id_alloc();
			$this->t0->text=$v['n'];
			$this->context[$this->long_name]['rn']=$rnum;
			$this->context[$this->long_name]['rl']++;
			$sel_i=$this->context[$this->long_name]['i_next'];if($sel_i!='')$sel_i.='.';
			$sel_v=$this->context[$this->long_name]['v_next'];if($sel_v!='')$sel_v.='.';
			$this->td0->attributes['onclick']="\$i('".js_escape($this->context[$this->long_name]['pick_id'])."').focus();".
				"\$i('".js_escape($this->context[$this->long_name]['current_selection_id'])."').textContent='".js_escape($sel_v.$v['n'])."';".
				"\$i('".js_escape($this->context[$this->long_name]['self_id'])."').value='".js_escape($sel_i.$v['v'])."';chse.send_or_push({static:'".$this->send."',val:'".js_escape($sel_i.$v['v'])."',c_id:this.id});".
				""
			;
			$this->t1->text='';
			unset($this->td1->attributes['onclick']);
			$i_next=$this->context[$this->long_name]['i_next'];
			$v_next=$this->context[$this->long_name]['v_next'];
			if(isset($v['+']))
			{
				$this->t1->text='+';
				#$oldrdiv=$this->context[$this->long_name]['rdiv'];
				if($this->context[$this->long_name]['i_next']!='')$this->context[$this->long_name]['i_next'].='.';
				$this->context[$this->long_name]['i_next'].=$v['v'];
				
				if($this->context[$this->long_name]['v_next']!='')$this->context[$this->long_name]['v_next'].='.';
				$this->context[$this->long_name]['v_next'].=$v['n'];
				
				$this->context[$this->long_name]['rdiv']=$this->d2->id_gen();
				$this->editors['pick']->oid=$v['v'];
				$this->editors['pick']->bootstrap();
				$this->d1->attributes['onclick']=$this->editors['pick']->attributes['onclick'];
				#$this->context[$this->long_name]['rdiv']=$oldrdiv;
			}else unset($this->d1->attributes['onclick']);
			$this->table->html_inner();
			$this->context[$this->long_name]['v_next']=$v_next;
			$this->context[$this->long_name]['i_next']=$i_next;
			
			if($rlist!='')$rlist.=',';
			$rlist.="{id_r:'".js_escape($this->tr->id_gen())."',id_0:'".js_escape($this->td0->id_gen())."',id_1:'".js_escape($this->td1->id_gen())."',id_btn:'".js_escape($this->d1->id_gen())."',id_c:'".js_escape($this->d2->id_gen())."'}";
			
			$rnum++;
			$this->context[$this->long_name]['rl']--;
		}
		$this->table->html_tail();
		$prev_lvl=$this->context[$this->long_name]['rl']-1;
		$this->rootnode->endscripts[]=
			"var m=\$i('".js_escape($this->context[$this->long_name]['pick_id'])."');".
			//unhighlite
			
			//update sel_path
			//highlite
			//update sel_tree
			//"m.sel_path[".
			"emo_highlight('".js_escape($this->context[$this->long_name]['pick_id'])."',0);".
			"m.sel_path[".$this->context[$this->long_name]['rl']."].items=[".$rlist."];".
			(($prev_lvl>=0)?("try{var f=m.sel_path[".$prev_lvl."];f.old_n=".$this_n.";}catch(e){};"):"").
			"m.sel_level=".$this->context[$this->long_name]['rl'].";".
			"m.sel_n=-1;".
			"while(m.sel_path.length>".$this->context[$this->long_name]['rl']."+1)m.sel_path.pop();".
			"emo_highlight('".js_escape($this->context[$this->long_name]['pick_id'])."',2);".
			"";
	}
	
	function bootstrap()
	{
		
		editor_generic::bootstrap_part();
		foreach($this->editors as $i => $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		
	}
	
	function handle_event($ev)
	{
	}
}

$tests_m_array[]='editor_m_object';






















#				
#				configurable_viewer
#				-test, simple 1-query customisable, oid-chain expansion, join caching, query merge

class configurable_viewer extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('configurator',new configurable_viewer_conf);
		$this->append_child($this->editors['configurator']);
		editor_generic::addeditor('p',new util_small_pager);
		$this->append_child($this->editors['p']);
		editor_generic::addeditor('rows',new configurable_viewer_rows);
		$this->append_child($this->editors['rows']);
		
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	
	function def_conf()
	{
		global $sql;
		//all columns, no joins
		$ret->columns=$sql->qv("SELECT a.id FROM `".TABLE_META_TREE."` as a WHERE a.parentid='".$sql->esc($this->oid)."'");
		$ret->order=Array();
		$ret->filter=Array();
		return $ret;
	}
	
	function html_inner()
	{
		$this->args['configurator']=$this->rootnode->setting_val($this->oid,'configurable_viewer_conf',serialize($this->def_conf()));
		$this->args['rows']=$this->args['configurator'];
		
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
	}
}

class dom_table_iter extends dom_table
{
	function __construct()
	{
		parent::__construct();
		$this->tr=new dom_tr;
		$this->append_child($this->tr);
		$this->cellcount=0;
		$this->cells=Array();
	}
	
	function add_cell($dom_node=NULL)
	{
		$td=new dom_td;
		$this->append_child($td);
		if(is_array($dom_node))
			foreach($dom_node as $n)$td->append_child($n);
		if(is_object($dom_node))$td->append_child($n);
		$this->cells[$this->cellcount++]=$td;
		return $td;
	}
}



class configurable_viewer_conf extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		$this->t=new dom_table_iter;
		$this->append_child($this->t);
		
		$this->ed_cols=new dom_table_iter;
		$this->t->add_cell($this->ed_cols);
		
		$this->ed_filter=new dom_table_iter;
		$this->t->add_cell($this->ed_filter);
		
		$this->ed_order=new dom_table_iter;
		$this->t->add_cell($this->ed_order);
	
		editor_generic::addeditor('col',new editor_m_object);
		$this->ed_cols->add_cell($this->editors['col']);
	
		editor_generic::addeditor('col+',new editor_m_object);
		$this->ed_cols->add_cell($this->editors['col+']);
	
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	
	function handle_event($ev)
	{
	}
}

class configurable_viewer_rows extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	
	function handle_event($ev)
	{
	}
}

#############################
#############################
##IN:arg=serialize(object)
##$this->subfields[field_name]=>class_name - fields, that may be of list type or any static type(string,int,float,bool)
// !WARNING :may be faulty by design
##$this->editor_types[class_name]=>class_name |NULL:self - subfield editors, if unset, use self ($this->etype)
##$this->children_field=>field_name - contains array of subobjects
##$this->known_classes[]=>class_name - new child class names
#############################



class filters_m extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->editor_names['fm_text_constant']='efm_text_constant';
		$this->editor_names['fm_list']='efm_list';
		$this->editor_names['fm_undefined']='efm_undefined';
		$this->editor_names['fm_logical_expression']='efm_logical_expression';
		$this->editor_names['fm_logical_group']='efm_logical_group';
		$this->editor_names['fm_meta_object']='efm_meta_object';
		$this->editor_names['fm_limit']='efm_limit';
		$this->editor_names['meta_query_gen']='emeta_query_gen';
		$this->editor_names['fm_set_expression']='efm_set_expression';
		
		$this->js_en=new dom_js("chse.debug=true;");$this->append_child($this->js_en);
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->context[$this->long_name]['oid']=$this->oid;
		/*foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
			*/
	}
	
	
	function create_editor_for($current,$ref)
	{
		//try this bad way :-)
		//find exactly what we have to do with
		$noremove=0;$noup=0;$nodown=0;
		
		if(preg_match('/[0-9]+/',$ref))
		{
		//index into main array
			$obj=$current->children[$ref];
			if(is_object($obj))
			{
			//got object, ok.
			//get a working editor
				$class_name=get_class($obj);
				if($ref==0)$noup=1;
				if($ref==count($current->children)-1)$nodown=1;
			}else{
				if(isset($obj))
				{
					//Text field. Impossible. Objects should handle them themselves. Nothing to do except a debug message.
					$this->rootnode->out('Damn. Got raw field instead of object at ['.$this->keys['path'].']+'.$ref.', "'.htmlspecialchars($obj).'"');
					return;
				}else{
					$class_name='fm_undefined';
					$noremove=1;
				}
			}
		}else{
		//field name
			$noremove=1;
			if($ref==='')
				$obj=$current;
			else
				$obj=$current->$ref;
			if(is_object($obj))
			{
				$class_name=get_class($obj);
			}else{
			//Text field. Impossible. Objects should handle them themselves. Nothing to do except a debug message.
				$this->rootnode->out('Damn. Got raw field instead of object at ['.$this->keys['path'].']+'.$ref.', "'.htmlspecialchars($obj).'"');
				return;
			}
		}
		if(isset($this->editors[$class_name]))
		{
			$saved_editor=$this->editors[$class_name]->to_var();
			$saved_editor_saved=1;
		}
		if(get_class($current)=='meta_query_gen')$noremove=true;
		
		$this->make_editor($class_name);
		$saved_editor['cid']=$this->context[$this->long_name]['children_id'];
		$oldpath=$this->keys['path'];
		if($ref !=='')$this->keys['path'].='/'.$ref;
		$this->editors[$class_name]->var=$obj;
		$this->editors[$class_name]->id_alloc();
		if($noremove){$noup=1;$nodown=1;};
		if($noremove)$this->editors[$class_name]->editors['-']->css_style['display']='none';
		else unset($this->editors[$class_name]->editors['-']->css_style['display']);
		if($noup)$this->editors[$class_name]->editors['u']->css_style['display']='none';
		else unset($this->editors[$class_name]->editors['u']->css_style['display']);
		if($nodown)$this->editors[$class_name]->editors['d']->css_style['display']='none';
		else unset($this->editors[$class_name]->editors['d']->css_style['display']);
		$this->editors[$class_name]->bootstrap();
		$this->editors[$class_name]->html();
		$this->keys['path']=$oldpath;
		$this->context[$this->long_name]['children_id']=$saved_editor['cid'];
		if($saved_editor_saved)$this->editors[$class_name]->from_var($saved_editor);
	}
	
	function make_editor($ed)
	{
		if(!isset($this->editors[$ed]))
		{
			$editor_name=$this->editor_names[$ed];
			editor_generic::addeditor($ed,new $editor_name);
			$this->append_child($this->editors[$ed]);
			$this->editors[$ed]->keys=&$this->keys;
			$this->editors[$ed]->args=&$this->args;
			$this->editors[$ed]->context=&$this->context;
			$this->editors[$ed]->oid=$this->oid;
			$this->editors[$ed]->callback_method='create_editor_for';
			$this->editors[$ed]->callback_object=$this;
			foreach($this->editor_names as $k => $v)if($v==$editor_name)$this->editors[$k]=$this->editors[$ed];
		}
	}
	
	function fetch($obj)
	{
		print "needs override!";
		return $obj->args[$obj->context[$obj->long_name]['var']];
	}
	
	function store($obj,$new)
	{
		print "needs override!";
		//do something!
	}
	
	function html_inner()
	{
		$object=$this->fetch($this);
		$this->keys=Array('path'=>'');
		$this->create_editor_for($object,'');
		$this->js_en->html();
		
	}
	
	
	function find($obj,$path)
	{
		$found=$obj;
		$path_e=explode('/',$path);
		$c=count($path_e);
		for($k=0;$k<$c;$k++)
			if($path_e[$k]!=='')
			{
				if(preg_match('/[0-9]+/',$path_e[$k]))
					$found=$found->children[$path_e[$k]];
				else
					$found=$found->{$path_e[$k]};
			}
		return $found;
	}
	
	function output_children()
	{
		$this->editors[$this->class_name]->output_children();
	}
	
	function reload_children($ev,$obj,$path_e)
	{
		$tgtid=$ev->context[$ev->parent_name]['children_id'];
		$this->name=$ev->parent_name;
		$this->etype=$ev->parent_type;
		$this->oid=$ev->context[$ev->long_name]['oid'];
		$this->context=$ev->context;
		$this->keys=$ev->keys;
		$class_name=get_class($obj);
		$this->make_editor($class_name);
		$this->keys['path']=implode('/',$path_e);
		$this->editors[$class_name]->var=$obj;
		$this->editors[$class_name]->id_alloc();
		$this->class_name=$class_name;
		$reloader=reload_object_create($this);
		$this->context[$ev->parent_name]['children_id']=$tgtid;
		print "var nya=\$i('".js_escape($tgtid)."');";
		print "try{nya.innerHTML='";
		$reloader->firstinner('output_children');
		print "}catch(e){window.location.reload(true);};\n";
	}
	
	function del_node($obj,$path)
	{
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(preg_match('/[0-9]+/',$last))
		{
			if($obk->static_children)
			{
				$obk->children[$last]=new fm_undefined;
				return true;
			}
			$a=Array();
			foreach($obk->children as $k => $v)
				if($k !=$last)$a[]=$v;
			$obk->children=$a;
		}else{
			
			#$obk->$last=new fm_undefined;
			return false;
		}
		return true;
	}
	
	function add_node($obj,$path,$new)
	{
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(preg_match('/[0-9]+/',$last))
		{
			if($obk->static_children)
			{
				$obk->children[$last]=$new;
				return;
			}
			$a=Array();
			foreach($obk->children as $k => $v)
			{
				if($k ==$last)$a[]=$new;
				$a[]=$v;
			}
			if($last>=count($obk->children))$a[]=$new;
			$obk->children=$a;
		}else{
			
			if($last != '')$obk->$last=$new;
		}
	}
	
	function handle_event($ev)
	{
		$this->context=&$ev->context;
		$this->long_name=$ev->parent_name;
		$this->oid=$this->context[$this->long_name]['oid'];
		$obj=$this->fetch($this);
		$path=$ev->keys['path'];
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(preg_match('/\\.-$/',$ev->rem_name))
		{
			if($this->del_node($obj,$ev->keys['path']))
			{
				$this->store($this,$obj);
				$this->reload_children($ev,$obk,$path_e);
			}
			return;
		}
		if(preg_match('/\\.\\+$/',$ev->rem_name))
		{
			$this->add_node($obj,$ev->keys['path'],new $_POST['val']);
			$this->store($this,$obj);
			$this->reload_children($ev,$obk,$path_e);
			return;
		}
		if(preg_match('/\\.u$/',$ev->rem_name))
		{
			$path_e=explode('/',$ev->keys['path']);
			$last=array_pop($path_e);
			$obk=$this->find($obj,implode('/',$path_e));
			if(preg_match('/[0-9]+/',$last))
			{
				$a=Array();
				foreach($obk->children as $k => $v)
				{
					if($last!=0)
					{
						if($k ==($last-1))$a[]=$obk->children[$last];
						elseif($k ==$last)$a[]=$obk->children[$last-1];
						else $a[]=$v;
					}else $a[]=$v;
				}
				if($last>=count($obk->children))$a[]=new $_POST['val'];
				$obk->children=$a;
			}else{
				
				return;
			}
			$this->store($this,$obj);
			$this->reload_children($ev,$obk,$path_e);
			return;
		}
		if(preg_match('/\\.d$/',$ev->rem_name))
		{
			$path_e=explode('/',$ev->keys['path']);
			$last=array_pop($path_e);
			$obk=$this->find($obj,implode('/',$path_e));
			if(preg_match('/[0-9]+/',$last))
			{
				$a=Array();
				foreach($obk->children as $k => $v)
				{
					if($last!=count($obk->children)-1)
					{
						if($k ==($last+1))$a[]=$obk->children[$last];
						elseif($k ==$last)$a[]=$obk->children[$last+1];
						else $a[]=$v;
					}else $a[]=$v;
				}
				if($last>=count($obk->children))$a[]=new $_POST['val'];
				$obk->children=$a;
			}else{
				
				return;
			}
			$this->store($this,$obj);
			$this->reload_children($ev,$obk,$path_e);
			return;
		}
		
		switch($ev->rem_name)
		{
			case 'fm_list.invert':
			case 'fm_text_constant.invert':
			case 'fm_logical_expression.invert':
			case 'fm_logical_group.invert':
			case 'fm_meta_object.invert':
			case 'fm_list.function':
			case 'fm_text_constant.value':
			case 'fm_logical_expression.operator':
			case 'fm_logical_group.operator':
			case 'fm_meta_object.path':
				$r_e=explode('.',$ev->rem_name);
				$field=$r_e[1];
				$obk=$this->find($obj,$ev->keys['path']);
				$obk->$field=$_POST['val'];
				$this->store($this,$obj);
			break;
			case 'fm_limit.sp.ed_count':
				$obk=$this->find($obj,$ev->keys['path']);
				$obk->count=$_POST['val'];
				$this->store($this,$obj);
			break;
			case 'fm_limit.sp.ed_offset':
				$obk=$this->find($obj,$ev->keys['path']);
				$obk->offset=$_POST['val'];
				$this->store($this,$obj);
			break;
		}
		
		editor_generic::handle_event($ev);
	}
}

class filters_m_s extends filters_m
{
/*	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
	*/
	function fetch($obj)
	{
		return unserialize($_SESSION['filters_m_test']);
	}
	
	function store($obj,$new)
	{
		if(! isset($new->rev))$new->rev=0;
		else $new->rev++;
		$_SESSION['filters_m_test']=serialize($new);
	}
	
}


class filters_m_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('r',new editor_button);
		$this->append_child($this->editors['r']);
		$this->editors['r']->attributes['value']='x';
		editor_generic::addeditor('filters_m',new filters_m_s);
		$this->append_child($this->editors['filters_m']);
		$this->result=new dom_div;
		$this->result_text=new dom_statictext;
		$this->append_child($this->result);
		$this->result->append_child($this->result_text);
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		$this->args=Array();
		$this->context=Array();
		$this->keys=Array();
		$this->oid=96;
		$this->context[$this->long_name]['result_div_id']=$this->result->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
			
			
	}
	
	function set_new()
	{
		$a=new meta_query_gen;
		$a->oid=$this->oid;
		$_SESSION['filters_m_test']=serialize($a);
	}
	
	function html_inner()
	{
		if(!isset($_SESSION['filters_m_test']))
			$this->set_new();
		#$this->args['filters_m']=unserialize($_SESSION['filters_m_test']);
		$prev=unserialize($_SESSION['filters_m_test']);
		$qg=$prev->to_show();
		if(is_object($qg))$this->result_text->text=$qg->result();
		else $this->result_text->text='undef';
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		$result_div_id=$ev->context[$ev->parent_name]['result_div_id'];
		if($ev->rem_name=='r')
		{
			$this->oid=$ev->context[$ev->parent_name]['oid'];
			$this->set_new();
			print "window.location.reload(true);";
		}
		$prev=unserialize($_SESSION['filters_m_test']);
		editor_generic::handle_event($ev);
		$after=unserialize($_SESSION['filters_m_test']);
		if($prev->rev != $after->rev)
		{
			$qg=$after->to_show();
			
			if(is_object($qg))print "\$i('".$result_div_id."').textContent='".js_escape($qg->result())."';";
			else print "\$i('".$result_div_id."').textContent='undef';";
		}
	}
}

$tests_m_array[]='filters_m_test';






class efm_common
{
	function controls($tgt)
	{
		if(isset($this->no_efm_common))return;
		
		editor_generic::addeditor('invert',new editor_checkbox);
		$tgt->append_child($this->editors['invert']);
		
		editor_generic::addeditor('-',new editor_button);
		$tgt->append_child($this->editors['-']);
		#$this->editors['-']->append_child(new dom_statictext(' - '));
		$this->editors['-']->main->attributes['value']=' - ';
		editor_generic::addeditor('+',new editor_dropdown_list);
		$tgt->append_child($this->editors['+']);
		$this->editors['+']->main->attributes['value']=' + ';
		editor_generic::addeditor('u',new editor_button);
		$tgt->append_child($this->editors['u']);
		$this->editors['u']->main->attributes['value']='↑';
		editor_generic::addeditor('d',new editor_button);
		$tgt->append_child($this->editors['d']);
		$this->editors['d']->main->attributes['value']='↓';
	}
	
	function bootstrap()
	{
		if(isset($this->editors['+']))
		{
			$this->editors['+']->actions=Array();
			foreach($this->com_parent->editor_names as $on => $en)
				$this->editors['+']->actions[]=(object)Array('acc'=>'','text'=>$on,'act'=>$on);
		}
	}

}

class efm_undefined extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		efm_common::controls($this->container);
		$this->css_style['background']='#FFE';
		$this->editors['invert']->css_style['display']='none';
	}
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		efm_common::bootstrap();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		
	}
	function to_var()
	{
	}
	
	function from_var($var)
	{
	}
	function handle_event($ev)
	{
	}
}

class efm_meta_object extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		$this->container->append_child(new dom_statictext('□'));
		editor_generic::addeditor('path',new editor_m_object);
		$this->container->append_child($this->editors['path']);
		
		efm_common::controls($this->container);
		$this->css_style['background']='#EFE';
	}
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		efm_common::bootstrap();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		
	}
	function to_var(){}
	function from_var($v){}
	
	function html_inner()
	{
		$this->args['path']=$this->var->path;
		$this->args['invert']=$this->var->invert;
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}

class efm_limit extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		editor_generic::addeditor('sp',new util_small_pager);
		$this->container->append_child($this->editors['sp']);
		
		efm_common::controls($this->container);
		$this->css_style['background']='#EFE';
		$this->editors['invert']->css_style['display']='none';
	}
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		efm_common::bootstrap();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		
	}
	function to_var(){}
	function from_var($v){}
	
	function html_inner()
	{
		$this->args['ed_offset']=$this->var->offset;
		$this->args['ed_count']=$this->var->count;
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}


class efm_text_constant extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		$this->container->append_child(new dom_statictext('■'));
		editor_generic::addeditor('value',new editor_text);
		$this->container->append_child($this->editors['value']);
		efm_common::controls($this->container);
		$this->css_style['background']='#FEF';
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		efm_common::bootstrap();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		
	}
	function to_var(){}
	function from_var($v){}
	
	function html_inner()
	{
		$this->args['value']=$this->var->value;
		$this->args['invert']=$this->var->invert;
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}

class efm_list extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		$this->container->append_child(new dom_statictext('◉'));
		editor_generic::addeditor('function',new editor_text);
		$this->container->append_child($this->editors['function']);
		efm_common::controls($this->container);
		$this->child_ag=new simple_ag;
		$this->append_child($this->child_ag);
		$this->child_ag->css_style['padding-left']='1em';
		$this->css_style['background']='#EFF';
		$this->css_style['width']='auto';
		$this->css_style['border']='#DEE solid 1px';
	}
	
	function to_var()
	{
		$var['var']=$this->var;
		$var['oid']=$this->oid;
		return $var;
	}
	
	function from_var($var)
	{
		$this->var=$var['var'];
		$this->oid=$var['oid'];
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		efm_common::bootstrap();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		
	}
	
	function output_children()
	{
		if(is_array($this->var->children))
			for($k=0; $k<count($this->var->children)+1; $k++)
			{
				$this->child_ag->open_row();
				if(isset($this->callback_method))
				{
					$m=$this->callback_method;
					if(is_object($this->callback_object))
						$this->callback_object->$m($this->var,$k);
					else
						$m($this->var,$k);
				}
				$this->child_ag->close_row();
			}
	}
	
	
	function html_inner()
	{
		$this->args['function']=$this->var->function;
		$this->args['invert']=$this->var->invert;
		$this->container->html();
		$this->child_ag->html_head();
		$this->context[$this->com_parent->long_name]['children_id']=$this->child_ag->main->id_gen();
		$this->output_children();
		$this->child_ag->html_tail();
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}




class simple_ag extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->s=new dom_table;
		$this->append_child($this->s);
		$this->tr=new dom_tr;
		$this->s->append_child($this->tr);
		$this->td=new dom_td();
		$this->tr->append_child($this->td);
		$this->static=new dom_td();
		$this->tr->append_child($this->static);
		$this->s->id=NULL;
		$this->td->id=NULL;
		$this->tr->id=NULL;
		$this->main=$this;
		
		$this->css_style['border-left']='2px green solid';
		$this->css_style['border-top']='2px green solid';
	}
	
	function header_out()
	{
	}
	
	function footer_out()
	{
	}
	
	function open_row()
	{
		$this->s->html_head();
		$this->tr->html_head();
		$this->td->html_head();
	}
	function close_row()
	{
		$this->td->html_tail();
		if(count($this->static->nodes)>0)$this->static->html();
		$this->tr->html_tail();
		$this->s->html_tail();
	}
	
}




class efm_logical_expression extends efm_list
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		$this->container->append_child(new dom_statictext('◈'));
		editor_generic::addeditor('operator',new editor_text);
		$this->container->append_child($this->editors['operator']);
		efm_common::controls($this->container);
		$this->child_ag=new simple_ag;
		$this->append_child($this->child_ag);
		$this->child_ag->css_style['padding-left']='1em';
		$this->css_style['background']='#EEF';
		$this->css_style['width']='auto';
		$this->css_style['border']='#DDE solid 1px';
	}
	
	
	function html_inner()
	{
		$this->args['operator']=$this->var->operator;
		$this->args['invert']=$this->var->invert;
		$this->container->html();
		$this->child_ag->html_head();
		$this->context[$this->com_parent->long_name]['children_id']=$this->child_ag->main->id_gen();
		$this->output_children();
		$this->child_ag->html_tail();
	}
	
	
}

class efm_logical_group extends efm_list
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		$this->container->append_child(new dom_statictext('◇'));
		editor_generic::addeditor('operator',new editor_text);
		$this->container->append_child($this->editors['operator']);
		efm_common::controls($this->container);
		$this->child_ag=new simple_ag;
		$this->append_child($this->child_ag);
		$this->child_ag->css_style['padding-left']='1em';
		$this->css_style['background']='#EEF';
		$this->css_style['width']='auto';
		$this->css_style['border']='#DDE solid 1px';
	}
	
	
	function html_inner()
	{
		$this->args['operator']=$this->var->operator;
		$this->args['invert']=$this->var->invert;
		$this->container->html();
		$this->child_ag->html_head();
		$this->context[$this->com_parent->long_name]['children_id']=$this->child_ag->main->id_gen();
		$this->output_children();
		$this->child_ag->html_tail();
	}
	
	
}

class emeta_query_gen extends efm_list
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		efm_common::controls($this->container);
		$this->child_ag=new simple_ag;
		$this->append_child($this->child_ag);
		$this->child_ag->css_style['padding-left']='1em';
		$this->css_style['background']='#EEF';
		$this->css_style['width']='auto';
		$this->css_style['border']='#DDE solid 1px';
		$this->editors['invert']->css_style['display']='none';
	}
	
	function output_children()
	{
		#$fields=Array('result_def','update_def','filter_def','sort_def','limit');
		
		#foreach($fields as $field)
		for($field=0;$field<count($this->var->children);$field++)
		{
			$this->child_ag->open_row();
			if(isset($this->callback_method))
			{
				$m=$this->callback_method;
				if(is_object($this->callback_object))
					$this->callback_object->$m($this->var,$field);
				else
					$m($this->var,$field);
			}
			$this->child_ag->close_row();
		}
	}
	
	
	function html_inner()
	{
		$this->container->html();
		$this->child_ag->html_head();
		$this->context[$this->com_parent->long_name]['children_id']=$this->child_ag->main->id_gen();
		$this->output_children();
		$this->child_ag->html_tail();
	}
	
	
	
}

class efm_set_expression extends efm_list
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype=get_class($this);
		$this->container=new container_autotable;
		$this->append_child($this->container);
		efm_common::controls($this->container);
		$this->child_ag=new htbl_ag;
		$this->append_child($this->child_ag);
		$this->eq=new dom_statictext('=');
		$this->append_child($this->eq);
		$this->child_ag->css_style['padding-left']='1em';
		$this->css_style['background']='#FEE';
		$this->css_style['width']='auto';
		$this->css_style['border']='#EDD solid 1px';
		$this->editors['invert']->css_style['display']='none';
	}
	
	function output_child($n=NULL)
	{
		$this->child_ag->open_row();
		if(is_object($n))
		{
			$n->html();
		}else{
			if(isset($this->callback_method))
			{
				$m=$this->callback_method;
				if(is_object($this->callback_object))
					$this->callback_object->$m($this->var,$n);
				else
					$m($this->var,$n);
			}
		}
		$this->child_ag->close_row();
	}
	function output_children()
	{
		$this->output_child(0);
		$this->output_child($this->eq);
		$this->output_child(1);
	}
	
	
	function html_inner()
	{
		$this->container->html();
		$this->child_ag->html_head();
		$this->child_ag->header_out();
		$this->context[$this->com_parent->long_name]['children_id']=$this->child_ag->main->id_gen();
		$this->output_children();
		$this->child_ag->footer_out();
		$this->child_ag->html_tail();
	}
	
	
	
}


class htbl_ag extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->s=new dom_table;
		$this->append_child($this->s);
		$this->tr=new dom_tr;
		$this->s->append_child($this->tr);
		$this->td=new dom_td();
		$this->tr->append_child($this->td);
		$this->static=new dom_div();
		$this->td->append_child($this->static);
		$this->s->id=NULL;
		$this->td->id=NULL;
		$this->main=$this->tr;
	}
	
	function header_out()
	{
		$this->s->html_head();
		$this->tr->html_head();
	}
	
	function footer_out()
	{
		$this->tr->html_tail();
		$this->s->html_tail();
	}
	
	function open_row()
	{
		$this->td->html_head();
	}
	function close_row()
	{
		$this->td->html_tail();
		if(count($this->static->nodes)>0)$this->static->html();
	}
	
}



class m_ref_viewer extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->top=new dom_div;
		$this->bottom=new dom_div;
		$this->append_child($this->top);
		$this->append_child($this->bottom);
		editor_generic::addeditor('list',new editor_button);
		$this->top->append_child($this->editors['list']);
		$this->editors['list']->attributes['value']='list';
		editor_generic::addeditor('settings',new editor_button);
		$this->top->append_child($this->editors['settings']);
		$this->editors['settings']->attributes['value']='settings';
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		$this->context[$this->long_name]['bottom_id']=$this->bottom->id_gen();
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	function c_b($ref)
	{
		$ref->context=&$this->context;
		$ref->keys=&$this->keys;
		$ref->args=&$this->args;
		$ref->oid=$this->oid;
		$ref->bootstrap();
	}
	
	function html_inner()
	{
		$this->activetab=$this->rootnode->setting_val($this->oid,$this->long_name.'.activetab','');
		$this->activetab=$this->activetab*1;
		if($this->activetab>1)$this->activetab=1;
		if($this->activetab<0)$this->activetab=0;
		switch($this->activetab)
		{
		case 0:
			editor_generic::addeditor('c',new m_ref_listx);
			break;
		case 1:
			editor_generic::addeditor('c',new m_ref_settingsx);
			break;
		}
		$this->bottom->append_child($this->editors['c']);
		$this->c_b($this->editors['c']);
		parent::html_inner();
	}
	
	function load_into($id,$class,$ev)
	{
		$r=new $class;
		$r->context=&$ev->context;
		$r->keys=&$ev->keys;
		$r->oid=$this->oid;
		$r->name=$ev->parent_name.'.c';
		$r->etype=$ev->parent_type.'.'.$r->etype;
		$r->bootstrap();
		print "var nya=\$i('".js_escape($id)."');";
		print "try{nya.innerHTML=";
		reload_object($r,true);
		print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
	}
	
	function handle_event($ev)
	{
		global $sql;
		$oid=$ev->context[$ev->long_name]['oid'];
		$this->oid=$oid;
		$st=new settings_tool;
		switch($ev->rem_name)
		{
		case 'list':
			$sql->query($st->set_query($oid,$ev->parent_name.'.activetab',$_SESSION['uid'],0,0));
			$this->load_into($ev->context[$ev->parent_name]['bottom_id'],'m_ref_listx',$ev);
			
			break;
		case 'settings':
			$sql->query($st->set_query($oid,$ev->parent_name.'.activetab',$_SESSION['uid'],0,1));
			$this->load_into($ev->context[$ev->parent_name]['bottom_id'],'m_ref_settingsx',$ev);
			break;
		}
		editor_generic::handle_event($ev);
	}
}




class m_ref_listx extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('pager',new util_small_pager);
		$this->append_child($this->editors['pager']);
		editor_generic::addeditor('list',new ref_listx);
		$this->append_child($this->editors['list']);
		$this->append_child(new dom_statictext('This is:'.get_class($this)));
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		global $sql;
		$st=new settings_tool;
		$r=$sql->q1($st->single_query($this->oid,'ed_count',$_SESSION['uid'],0));
		if($r != '')
			$res=intval($r);
		else
			$res= 10;
		$this->args['ed_count']=$res;
		$r=$sql->q1($st->single_query($this->oid,'ed_offset',$_SESSION['uid'],0));
		if($r != '')
			$res=intval($r);
		else
			$res= 0;
		$this->args['ed_offset']=$res;
		
		
		$this->context[$this->long_name]['list_id']=$this->editors['list']->id_gen();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	
	function handle_event($ev)
	{
		global $sql;
		$this->oid=$ev->context[$ev->long_name]['oid'];
		$list_id=$ev->context[$ev->parent_name]['list_id'];
		$changed=false;
		$st=new settings_tool;
		switch($ev->rem_name)
		{
		case 'pager.ed_count':
			$changed=true;
			$r=$sql->query($st->set_query($this->oid,'ed_count',$_SESSION['uid'],0,intval($_POST['val'])));
			break;
		case 'pager.ed_offset':
			$changed=true;
			$r=$sql->query($st->set_query($this->oid,'ed_offset',$_SESSION['uid'],0,intval($_POST['val'])));
			break;
		}
		if($changed)
		{
			$r=$this->editors['list'];
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$this->oid;
			$r->name=$ev->parent_name.'.list';
			$r->etype=$ev->parent_type.'.'.$r->etype;
			$r->bootstrap();
			print "var nya=\$i('".js_escape($list_id)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
		}
		
		editor_generic::handle_event($ev);
	}
}





class filters_m_rs extends filters_m
{
	function fetch($obj)
	{
		global $sql;
		$st=new settings_tool;
		$r=$sql->q1($st->single_query($this->oid,'mq',$_SESSION['uid'],0));
		if($r != '')
			$res=unserialize($r);
		else
			$res= new meta_query_gen;
		return $res;
	}
	
	function store($obj,$new)
	{
		global $sql;
		if(! isset($new->rev))$new->rev=0;
		else $new->rev++;
		$st=new settings_tool;
		$r=$sql->query($st->set_query($this->oid,'mq',$_SESSION['uid'],0,serialize($new)));
	}
	
}

class m_ref_settingsx extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('btn_reset',new editor_button);
		$this->editors['btn_reset']->attributes['value']='REset';
		$this->append_child($this->editors['btn_reset']);
		editor_generic::addeditor('filters',new filters_m_rs);
		$this->append_child($this->editors['filters']);
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	
	function handle_event($ev)
	{
		global $sql;
		switch($ev->rem_name)
		{
		case '':
			break;
		}
		editor_generic::handle_event($ev);
	}
}



class ref_listx extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->query_v=new dom_statictext;
		$this->query_v_d=new dom_div;
		$this->query_v_d->append_child($this->query_v);
		$this->append_child($this->query_v_d);
		$this->append_child($this->tbl);
		$this->c_c=new dom_tr;
		$this->c_d=new dom_tr;
		$this->tbl->append_child($this->c_c);
		$this->tbl->append_child($this->c_d);
		$td=new dom_td;
		$td->append_child(new dom_statictext('Nn'));
		unset($td->id);
		$this->c_c->append_child($td);
		$td=new dom_td;
		$this->num=new dom_statictext;
		$td->append_child($this->num);
		unset($td->id);
		$this->c_d->append_child($td);
		$this->css_class=get_class($this);
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	
	function prepare()
	{
		global $sql;
		$st=new settings_tool;
		$r=$sql->q1($st->single_query($this->oid,'mq',$_SESSION['uid'],0));
		if($r != '')
			$res=unserialize($r);
		else
			$res= new meta_query_gen;
		$this->meta_query=$res;
		
		$r=$sql->q1($st->single_query($this->oid,'ed_count',$_SESSION['uid'],0));
		if($r != '')$res=intval($r);else $res= 10;
		$ed_count=$res;
		
		$r=$sql->q1($st->single_query($this->oid,'ed_offset',$_SESSION['uid'],0));
		if($r != '')$res=intval($r);else $res= 0;
		$ed_offset=$res;
		
		$colidx=0;
		
		if(is_array($this->meta_query->result_def->children))foreach($this->meta_query->result_def->children as $r)
		{
			if(get_class($r)=='fm_meta_object')
			{
				$hrarr=$sql->qkv("SELECT a.id,coalesce(b.val,a.name) as hr_name FROM `".TABLE_META_TREE."` as a LEFT OUTER JOIN `".TABLE_META_I18N."` as b ON a.id=b.object AND b.var='name' AND b.loc='".$_SESSION['lang']."' WHERE a.id IN (".str_replace('.',',',$r->path).")");
				$hrt='';
				$hrm='';
				foreach($hrarr as $kk => $e)
				{
					if($hrt!='')$hrt.='.';
					$hrt.=$e;
					if($hrm!='')$hrm.='.';
					$hrm.=$kk;
				};
			}else{
				$hrt='expression '.$colidx;
				$hrm='';
			}
			$td=new dom_td;
			unset($td->id);
			$td->append_child(new dom_statictext($hrt));
			$td->attributes['title']=$hrm;
			$this->c_c->append_child($td);
			editor_generic::addeditor('cd'.$colidx,new editor_statictext);
			$td=new dom_td;
			unset($td->id);
			$td->append_child($this->editors['cd'.$colidx]);
			$this->c_d->append_child($td);
			$colidx++;
		}
		$this->meta_query->oid=$this->oid;
		$this->sql_obj=$this->meta_query->to_show();
		$this->sql_obj->lim_count=$ed_count;
		$this->sql_obj->lim_offset=$ed_offset;
		$colidx1=0;
		if(is_array($this->sql_obj->what->exprs))foreach($this->sql_obj->what->exprs as $e)
		{
			$e->alias='cd'.$colidx1;
			$colidx1++;
		}
		
	}
	
	
	function html_inner()
	{
		global $sql;
		$this->prepare();
		$this->bootstrap();
		if(!is_object($this->sql_obj))return;
		$qq=$this->sql_obj->result();
		$this->query_v->text=$qq;
		$this->query_v_d->html();
		$this->tbl->html_head();
		$this->c_c->html();
		$qq=$this->sql_obj->result();
		$res=$sql->query($qq);
		$rowcnt=0;
		while($row=$sql->fetcha($res))
		{
			foreach($row as $k => $v)$this->args[$k]=$v;
			$this->num->text=$rowcnt++;
			$this->c_d->html();
			$this->c_d->id_alloc();
		}
		$this->tbl->html_tail();
	}
	
	function handle_event($ev)
	{
	}
}
















/*
--------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------
	
*/



class ed_tree_null_manipulator
{
	function find($obj,$path)//find node from $obj by $path
	{
		return NULL;//if nothing was found
	}
	
	function del_node($obj,$path)//delete node from $obj by $path
	{
		return true;//if deleted
	}
	
	function pick_node($obj,$path)//find node and mark for cleanup
	//TODO: maybe better to implement move method???
	{
		return true;//if deleted and marked as picked
	}
	
	function add_node($obj,$path,$new)//add node from $new into $obj before $path
	{
	}
	
	function cleanup_picked($obj)//walk tree and delete picked
	{
	}
	
	function children($obj)//return array with children
	{
		return NULL;//array with children
	}
	
	function text($obj)//return short text to display in tree node for item $obj
	{
		return "_undefined_needs_override_";
	}
	
	function item_editor()//return customized item editor class name
	{
		return "ed_tree_item_editor";
	}
	
}


class ed_tree_main extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('fa_cnt',new container_resize_scroll);
		editor_generic::addeditor('fa',new ed_tree_nofa);
		
		editor_generic::addeditor('tracker',new ed_tree_tracker);
		$this->ctl=$this->editors['tracker'];
		/*
			this button holds keyboard input state information and dispatches keyboard generated events related to tree items
			id_list[]
			id_current
		*/
		
		editor_generic::addeditor('clip',new ed_tree_main_cv);
		
		$this->add_menu(NULL);//overridable, create editors only
		
		
		$tbl=new dom_table;
		$this->append_child($tbl);
		$tr=new dom_tr;
		$tbl->append_child($tr);
		$ltd=new dom_td;
		$tr->append_child($ltd);
		$rtd=new dom_td;
		$tr->append_child($rtd);
		$this->right_td=new dom_div;
		$rtd->append_child($this->right_td);
		
		$ltd->append_child($this->ctl);
		$ltd->append_child($this->editors['clip']);
		$this->add_menu($ltd);//overridable, link editors into div
		
		
		$ltd->append_child($this->editors['fa_cnt']);
		
		$div=new dom_div;
		$this->editors['fa_cnt']->append_child($div);
		$div->append_child($this->editors['fa']);
		
		$this->ctl_text=new dom_statictext('Objects:');
		$this->ctl->append_child($this->ctl_text);
		//$this->editors['fa']->ctl=$this->ctl;
		$this->ctl->attributes['onkeyup']="return ed_tree_main_ctl_k(event,this,2);";
		$this->ctl->attributes['onkeypress']="return ed_tree_main_ctl_k(event,this,1);";
		$this->ctl->attributes['onkeydown']="return ed_tree_main_ctl_k(event,this,0);";
		$this->editors['clip']->css_style['cursor']='default';
		$this->editors['clip']->attributes['onmousedown']="resizer.create_ghost(event,this,{t:'cl',d:''});return false;";
		
		$this->editors['clip']->attributes['onmouseup']=
			"return ed_tree_clip_up(event,\$i('".$this->ctl->id_gen()."'));";
		$this->editors['clip']->attributes['onmouseover']=
			"return ed_tree_clip_mov(event,this,'".$this->ctl->id_gen()."');";
		$this->editors['clip']->attributes['onmousemove']=
			"return ed_tree_clip_mov(event,this,'');";
		$this->editors['clip']->attributes['onmouseout']=
			"return ed_tree_clip_mou(event,this);";
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name.'.fa']['button_id']=$this->ctl->id_gen();
		$this->context[$this->long_name]['fa_id']=$this->editors['fa']->id_gen();
		$this->context[$this->long_name]['ctl_id']=$this->ctl->id_gen();
		$this->context[$this->long_name]['clip_id']=$this->editors['clip']->id_gen();
		$this->context[$this->long_name]['right_id']=$this->right_td->id_gen();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		
	}
	
	function html_inner()
	{
		$this->rootnode->endscripts[]="(function(){var a=\$i('".js_escape($this->ctl->id_gen())."');a.id_list=new Array();a.id_current=-1;a.send_static='".$this->ctl->send."';".
		"a.fa_id='".$this->editors['fa_cnt']->in->id_gen()."';".
		"var b=\$i('".js_escape($this->editors['clip']->id_gen())."');b.send_static='".$this->editors['clip']->send."';})();";
		$this->editors['fa']->object=$this->fetch();
		$this->editors['fa']->ma=$this->manipulator();
		$this->editors['clip']->ma=$this->manipulator();
		parent::html_inner();
	}
	
	/* overridables */
	function add_menu($to)
	{
		print 'needs override';
	}
	
	function fetch()
	{
		print "needs override!";
		return $this->args[$this->context[$this->long_name]['var']];
	}
	
	function store($new)
	{
		print "needs override!";
		//do something!
	}
	
	function manipulator()
	{
		print "needs override!";
		return new ed_tree_null_manipulator;
	}
	
	/* end overridables */
	
	
	function handle_event($ev)
	{
		$this->oid=$ev->context[$ev->long_name]['oid'];
		$this->long_name=$ev->parent_name;
		$this->context=&$ev->context;
		$this->keys=&$ev->keys;
		$obj=$this->fetch();
		$ma=$this->manipulator();
		if($ev->keys['!']=='o')
		{
			$current=$ma->find($obj,$ev->keys['path']);
			$n=$ev->rem_name;
			$current->$n=$_POST['val'];
			print "\$i('".$ev->context[$ev->parent_name]['cid']."')[text_content]='".js_escape($ma->text($current))."';";
			$do_store=true;
		}
		if($ev->rem_name=='tracker')
		{
			//$this->editors['fa']->handle_event($ev);
			$this->context=&$ev->context;
			$this->long_name=$ev->parent_name;
			global $clipboard;
			switch($_POST['val'])
			{
			case 'moveti':
				//$node=$this->find($obj,$_POST['path']);
				//$this->del_node($obj,$_POST['path']);
				if(substr($_POST['before'],0,strlen($_POST['path']))==$_POST['path'])
				{
					print "alert('Failed to move an object into itself');";
					return;
				}
				$ma->add_node($obj,$_POST['before'],$ma->pick_node($obj,$_POST['path']));
				$ma->cleanup_picked($obj);
				$reload_fa=true;
				$do_store=true;
				break;
			case 'copyti':
				$node=$ma->find($obj,$_POST['path']);
				$node=clone $node;
				$ma->add_node($obj,$_POST['before'],$node);
				$reload_fa=true;
				$do_store=true;
				break;
			case 'pastecl':
				$new=$clipboard->fetch();
				if(!isset($new))return;
				//if(!method_exists($new,'text_short'))return;
				$ma->add_node($obj,$_POST['before'],$new);
				$reload_fa=true;
				$do_store=true;
				break;
			case 'movecl':
				$node=$ma->find($obj,$_POST['path']);
				$ma->del_node($obj,$_POST['path']);
				$clipboard->store($node);
				$reload_fa=true;
				$reload_clip=true;
				$do_store=true;
				break;
			case 'copycl':
				$node=$ma->find($obj,$_POST['path']);
				$clipboard->store($node);
				$reload_clip=true;
				$do_store=true;
				break;
			case 'pastenew':
				$cn=$_POST['n'];
				if(class_exists($cn))$new=new $cn;
				else return;
				$ma->add_node($obj,$_POST['before'],$new);
				$reload_fa=true;
				$do_store=true;
				break;
			case 'del':
				$ma->del_node($obj,$_POST['path']);
				$reload_fa=true;
				$do_store=true;
				break;
			case 'activate':
				$current=$ma->find($obj,$_POST['path']);
				$reload_right=true;
				$do_store=false;
				break;
			case 'clipboard_clear':
				$clipboard->store(NULL);
				$reload_clip=true;
				break;
			}
		}
			if($do_store)$this->store($obj);
			
			if($reload_right)
			{
				$rt=$ma->item_editor();
				$r=new $rt;
				$r->context=&$ev->context;
				$r->context[$ev->parent_name.'.fa']['button_id']=$ev->context[$ev->parent_name]['ctl_id'];
				$r->context[$ev->parent_name]['cid']=$_POST['cid'];
				$r->keys=&$ev->keys;
				$r->keys['path']=$_POST['path'];
				$r->keys['!']='o';
				$r->oid=$this->oid;
				$r->name=$ev->parent_name;
				$r->etype=$ev->parent_type;
				$r->configure($current);
				print "(function(){";
				print "var nya=\$i('".js_escape($ev->context[$ev->parent_name]['right_id'])."');";
				print "try{nya.innerHTML=";
				reload_object($r,true);
				if(($_POST['mouse']==1) && isset($r->first_editor))print "\$i('".js_escape($r->first_editor->main->id_gen())."').focus();";
				print "}catch(e){ window.location.reload(true);};";
				print "})();";
			}
			if($reload_fa)
			{
				$r=$this->editors['fa'];
				unset($r->com_parent);
				$r->object=$obj;
				$r->ma=$ma;
				$r->context=&$ev->context;
				$r->context[$ev->parent_name.'.fa']['button_id']=$ev->context[$ev->parent_name]['ctl_id'];
				$r->keys=&$ev->keys;
				$r->oid=$this->oid;
				$r->name=$ev->parent_name.'.fa';
				$r->etype=$ev->parent_type.'.'.$r->etype;
				print "(function(){var a=\$i('".js_escape($ev->context[$ev->parent_name]['ctl_id'])."');a.id_list=new Array();a.id_current=-1;";
				print "var nya=\$i('".js_escape($ev->context[$ev->parent_name]['fa_id'])."');";
				print "try{nya.innerHTML=";
				reload_object($r,true);
				print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
				print "\$i('".js_escape($ev->context[$ev->parent_name]['right_id'])."').innerHTML='';";
				print "})();";
			};
			if($reload_clip)
			{
				$r=$this->editors['clip'];
				unset($r->com_parent);
				$r->context=&$ev->context;
				$r->keys=&$ev->keys;
				$r->oid=$this->oid;
				$r->name=$ev->parent_name.'.clip';
				$r->etype=$ev->parent_type.'.'.$r->etype;
				$r->ma=$this->manipulator();
				print "(function(){";
				print "var nya=\$i('".js_escape($ev->context[$ev->parent_name]['clip_id'])."');";
				print "try{nya.innerHTML=";
				reload_object($r,true);
				print "nya.style.backgroundColor='';";
				print "}catch(e){ window.location.reload(true);};";
				print "})();";
			}
			//print 'window.location.reload(true);';
		
			editor_generic::handle_event($ev);
	}
}


////////////////////////////////////////////////////////////////////////////////////////

class ed_tree_main_cv extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->txt=new dom_statictext;
		$this->append_child($this->txt);
		$this->css_style['display']='inline-block';
		$this->css_style['border']='1px solid blue';
	}
	
	function bootstrap()
	{
//		editor_generic::bootstrap_part();
	}
	
	function html_inner()
	{
		global $clipboard;
		$r=$clipboard->fetch();
		if(!isset($r))
		{
			//TODO: translate
			//TODO: add 'title'
			$this->txt->text='Empty';
			parent::html_inner();
			return;
		}
		$this->txt->text=$this->ma->text($r);
		parent::html_inner();
	}
}

////////////////////////////////////////////////////////////////////////////////////////

class ed_tree_main_nd extends dom_div
{
	function __construct($n)
	{
		parent::__construct();
		$this->txt=new dom_statictext($n);
		$this->append_child($this->txt);
		$this->css_style['display']='inline-block';
		$this->css_style['border']='1px solid blue';
	}
	
	function bootstrap()
	{
//		editor_generic::bootstrap_part();
	}
}

/////////////////////////////////////////////////////////////////////////////////////////

class ed_tree_tracker extends dom_any
{
	function __construct()
	{
		parent::__construct('button');
		$this->main=$this;
		$this->etype=get_class($this);
	}
	
	function bootstrap()
	{
		
		editor_generic::bootstrap_part(false);
		//$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:".$this->val_js.",c_id:this.id});";
		$this->attributes['onfocus']='';
		$this->attributes['onblur']='';
		// focus persistence test
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	
	function after_build_before_children()
	{
		$this->rootnode->scripts['settings.js']='../settings/settings.js';
		$this->rootnode->scripts['core.js']='../js/core.js';
		$this->rootnode->scripts['commoncontrols.js']='/js/commoncontrols.js';

	}
}





#####################################################################################################
#####################################################################################################
#####################################################################################################
#####################################################################################################

class ed_tree_nofa extends dom_div
{
/* in:
	$this->object	- Object
	$this->ma		- Object manipulator
*/
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->normal=new dom_div;
		$this->normal->main_div=new dom_div;
		$this->normal->append_child($this->normal->main_div);
		$this->normal->txt=new dom_statictext;
		$this->normal->main_div->append_child($this->normal->txt);
		
		$this->normal->main_div->css_style['cursor']='default';
		
		$this->normal->children_container=new dom_div;
		$this->normal->append_child($this->normal->children_container);
		$this->normal->children_container->css_style['padding-left']='1em';
		
		$this->undef=new dom_div;
		$this->undef->main_div=new dom_div;
		$this->undef->append_child($this->undef->main_div);
		$this->undef->txt=new dom_statictext('++');
		$this->undef->main_div->append_child($this->undef->txt);
		
		$this->undef->main_div->css_style['cursor']='default';
		$this->append_child($this->undef);
		$this->append_child($this->normal);
		
	}
	
	function opene($e)
	{
		$e->html_head();
		$e->main_div->html();
		if(isset($e->children_container))$e->children_container->html_head();
	}
	
	function closee($e)
	{
		if(isset($e->children_container))$e->children_container->html_tail();
		$e->html_tail();
	}
	
	
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->button_id=$this->context[$this->long_name]['button_id'];
		/*foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
			*/
	}
	
	
	function create_editor_for($current,$ref)
	{
		$got=true;
		if(preg_match('/[0-9]+/',$ref))
		{
		//index into main array
			//$obj=$current->children[$ref];
			$ca=$this->ma->children($current);
			$obj=is_array($ca)?$ca[$ref]:NULL;
			if(is_object($obj))
			{
				$this->ed=$this->normal;
			}else{
				if(isset($obj))
				{
					//Text field. Impossible. Objects should handle them themselves. Nothing to do except a debug message.
					$this->rootnode->out('Damn. Got raw field instead of object at ['.$this->keys['path'].']+'.$ref.', "'.htmlspecialchars($obj).'"');
					return;
				}else{
					$this->ed=$this->undef;
					$got=false;
				}
			}
//			if(count($current->children)<=$ref)$this->ed=$this->editors['ed_tree_undef'];
		}else{
			if($ref==='')
				$obj=$current;
			else
				$obj=$current->$ref;
			if(is_object($obj))
			{
				$this->ed=$this->normal;
			}else{
			//Text field. Impossible. Objects should handle them themselves. Nothing to do except a debug message.
				$this->rootnode->out('Damn. Got raw field instead of object at ['.$this->keys['path'].']+'.$ref.', "'.htmlspecialchars($obj).'"');
				return;
			}
		}
		$ed=$this->ed;
		$cid=$this->children_id;
		$oldpath=$this->path;
		
		if($ref !=='')$this->path.='/'.$ref;
		$this->ed->id_alloc();
		
		$this->rootnode->endscripts['ed_tree_nofa'].="\$i('".js_escape($this->button_id)."').id_list.push({keys:'".js_escape($this->path)."',cid:'".js_escape($this->ed->main_div->id_gen())."',pcid:'".js_escape($cid)."'});";
		$this->ed->main_div->attributes['onclick']=
			"return ed_tree_fa_item_click('".js_escape($this->button_id)."','".js_escape($this->path)."');";
		if($got)
			$this->ed->main_div->attributes['onmousedown']="resizer.create_ghost(event,this,{t:'ti',d:'".js_escape($this->path)."'});return false;";
		
		$this->ed->main_div->attributes['onmouseup']=
			"return ed_tree_fa_item_up(event,'".js_escape($this->button_id)."','".js_escape($this->path)."');";
		$this->ed->main_div->attributes['onmousemove']=
			"return ed_tree_fa_item_mov(event,'".js_escape($this->button_id)."','".js_escape($this->path)."',this);";
		$this->ed->main_div->attributes['onmouseout']=
			"return ed_tree_fa_item_mou(event,'".js_escape($this->button_id)."','".js_escape($this->path)."',this);";
		if($got)
			$this->ed->txt->text=$this->ma->text($obj);
		$this->opene($this->ed);
		if(isset($this->ed->children_container))
			$this->children_id=$this->ed->children_container->id_gen();
		$ca=$this->ma->children($obj);
		if(is_array($ca))
				for($k=0;$k<=count($ca);$k++)
					$this->create_editor_for($obj,$k);
		//restore $this->ed ids here if needed
		$this->ed=$ed;
		$this->closee($ed);
		$this->path=$oldpath;
		$this->children_id=$cid;
	}
	
	
	function html_inner()
	{
		$object=$this->object;
		$this->path='';
		$this->create_editor_for($object,'');
		
	}
	
	function handle_event($ev)
	{
		$this->context=&$ev->context;
		$this->long_name=$ev->parent_name;
		$this->oid=$this->context[$this->long_name]['oid'];
		return;
		editor_generic::handle_event($ev);
	}
}

####################################################################################################
class ed_tree_item_editor extends dom_div//virtual component injector
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->title_add(" "," ");
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	
	function field_add($obj,$name,$hrname,$ed)
	{
			editor_generic::addeditor($name,$ed);
			if(!isset($this->first_editor))$this->first_editor=$this->editors[$name];
			$tr=new dom_tr;
			$this->tbl->append_child($tr);
			$td=new dom_td;
			$tr->append_child($td);
			$txt=new dom_statictext($hrname);
			$l=new dom_any('label');
			$td->append_child($l);
			$l->append_child($txt);
			$l->attributes['title']=$name;
			$td=new dom_td;
			$tr->append_child($td);
			$td->append_child($this->editors[$name]);
			$this->args[$name]=$obj->$name;
			$l->attributes['for']=$this->editors[$name]->id_gen();
	}
	
	function title_add($hrname,$title)
	{
			$tr=new dom_tr;
			$this->tbl->append_child($tr);
			$td=new dom_td;
			$tr->append_child($td);
			$txt=new dom_statictext($hrname);
			$td->append_child($txt);
			$td->attributes['title']=$title;
			$td->attributes['colspan']='2';
			$td->css_style['text-align']='center';
			$td->css_style['font-weight']='bold';
			if(!isset($this->title_hr)){$this->title_hr=$hrname;$txt->text=&$this->title_hr;};
			if(!isset($this->title_hint)){$this->title_hint=$title;$td->attributes['title']=&$this->title_hint;};
	}
	
	function title_set($hrname,$title)
	{
		$this->title_hr=$hrname;
		$this->title_hint=$title;
	}
	
	function configure($obj)//virtual method
	{
		
	}
	
	
	function handle_event($ev)//parent handles events
	{
		editor_generic::handle_event($ev);
	}
}




/*
##################################################################################	
	Implement methods for meta_query editor
##################################################################################	
*/


class meta_query_manipulator
{
	function find($obj,$path)
	{
		$found=$obj;
		$path_e=explode('/',$path);
		$c=count($path_e);
		for($k=0;$k<$c;$k++)
			if($path_e[$k]!=='')
			{
				if(preg_match('/[0-9]+/',$path_e[$k]))
					$found=$found->children[$path_e[$k]];
				else
					$found=$found->{$path_e[$k]};
			}
		return $found;
	}
	
	function del_node($obj,$path)
	{
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(preg_match('/[0-9]+/',$last))
		{
			if($obk->static_children)
			{
				$obk->children[$last]=new fm_undefined;
				return true;
			}
			$a=Array();
			foreach($obk->children as $k => $v)
				if($k !=$last)$a[]=$v;
			$obk->children=$a;
		}else{
			
			#$obk->$last=new fm_undefined;
			return false;
		}
		return true;
	}
	
	function pick_node($obj,$path)
	{
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(preg_match('/[0-9]+/',$last))
		{
			foreach($obk->children as $k => $v)
				if($k==$last)
				{
					$found=$v;
					if($obk->static_children)
						$obk->children[$k]=new fm_undefined;
					else
						$obk->children[$k]='Picked';
					break;
				}
				return $found;
		}
		return true;
	}
	
	function add_node($obj,$path,$new)
	{
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(preg_match('/[0-9]+/',$last))
		{
			if($obk->static_children)
			{
				$obk->children[$last]=$new;
				return;
			}
			$a=Array();
			foreach($obk->children as $k => $v)
			{
				if($k ==$last)$a[]=$new;
				$a[]=$v;
			}
			if($last>=count($obk->children))$a[]=$new;
			$obk->children=$a;
		}else{
			
			if($last != '')$obk->$last=$new;
		}
	}
	
	function cleanup_picked($obj)
	{
		if(is_array($obj->children))
		{
			$a=Array();
			$found=false;
			foreach($obj->children as $v)
			{
				if($v==='Picked')$found=true;
				if($v!=='Picked')
				{
					$a[]=$v;
					if(is_array($v->children))$this->cleanup_picked($v);
				}
			}
			if($found)$obj->children=$a;
		}
	}
	
	function children($obj)
	{
		return $obj->children;
	}
	
	function text($obj)
	{
		return (method_exists($obj,'text_short'))?$obj->text_short():'undef?';
	}
	
	function item_editor()
	{
		return 'ed_tree_meta_editor';
	}
	
}

##################################################################################	

class ed_tree_meta_editor extends ed_tree_item_editor//virtual component injector
{
	
	function configure($obj)//virtual method
	{
		$type=get_class($obj);
		$this->title_set($type,$type);
		switch($type)
		{
		case 'fm_undefined':
			break;
		case 'fm_text_constant':
			//TODO: localization
			$this->field_add($obj,'value','Значение',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'fm_logical_expression':
			//TODO: localization
			$this->field_add($obj,'operator','operator',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'fm_list':
			$this->field_add($obj,'function','function',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'fm_logical_group':
			$this->field_add($obj,'operator','operator',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'fm_meta_object':
			$this->field_add($obj,'path','path',new editor_m_object);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'fm_set_expression':
			break;
		case 'fm_limit':
			$this->field_add($obj,'count','count',new editor_text);
			$this->field_add($obj,'offset','offset',new editor_text);
			break;
		case 'meta_query_gen':
			break;
		}
	}
	
	
	function handle_event($ev)//parent handles events
	{
		editor_generic::handle_event($ev);
	}
}





#####################################################################################################



class ed_tree_main_meta extends ed_tree_main
{
	
	function fetch()
	{
		return unserialize($_SESSION['ed_tree_main_fortest']);
	}
	
	function store($new)
	{
		if(! isset($new->rev))$new->rev=0;
		else $new->rev++;
		$_SESSION['ed_tree_main_fortest']=serialize($new);
	}
	
	function manipulator()
	{
		return new meta_query_manipulator;
	}
	
	function add_menu($to)
	{
			//TODO: localization
		$add_d_cont=Array(
			'fm_text_constant'=>'<tc>',
			'fm_logical_expression'=>'<le>',
			'fm_list'=>'<li>',
			'fm_logical_group'=>'<lg>',
			'fm_meta_object'=>'<mo>',
			'fm_set_expression'=>'<se>'
			);
		if(!isset($to))
		{
			foreach($add_d_cont as $nn => $vv)
				editor_generic::addeditor($nn,new ed_tree_main_nd($vv));
		}elseif(is_object($to)){
			foreach($add_d_cont as $nn => $vv)
			{
				$to->append_child($this->editors[$nn]);
				$this->editors[$nn]->css_style['cursor']='default';
				$this->editors[$nn]->attributes['onmousedown']="resizer.create_ghost(event,this,{t:'".$nn."',d:''});return false;";
			}
		}
	}
}

/*
##################################################################################	
		test for ed_tree_main_meta
##################################################################################	
*/

class ed_tree_main_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('r',new editor_button);
		$this->append_child($this->editors['r']);
		$this->editors['r']->attributes['value']='x';
		editor_generic::addeditor('m',new ed_tree_main_meta);
		$this->append_child($this->editors['m']);
		$this->result=new dom_div;
		$this->result_text=new dom_statictext;
		$this->append_child($this->result);
		$this->result->append_child($this->result_text);
		$this->result->css_style['border']='1px solid green';
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		$this->args=Array();
		$this->context=Array();
		$this->keys=Array();
		$this->oid=96;
		$this->context[$this->long_name]['result_div_id']=$this->result->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
			
			
	}
	
	function sn_g($n,$d)
	{
		$ar=Array();
		for($k=0;$k<$n;$k++)
		{
			if((rand(0,1)==1)&&($d>0))
			{
				$t=new fm_logical_expression;
				$t->children=$this->sn_g($n,$d-1);
				$t->operator=$this->oplist[rand(0,count($this->oplist))];
				$ar[]=$t;
			}else{
				$t=new fm_text_constant;
				$t->value=md5(rand());
				$ar[]=$t;
			}
		}
		return $ar;
	}
	
	
	function set_new()
	{
		//$a=new meta_query_gen;
		//$a->oid=$this->oid;
		$n=new meta_query_gen;
		$n->oid=$this->oid;
		$_SESSION['ed_tree_main_fortest']=serialize($n);
		return;
		$this->oplist=Array('+','*','%','&','|');
		$a=new fm_logical_expression;
		$c=new fm_text_constant;
		$c->value='vc';
		$b=new fm_text_constant;
		$b->value='vb';
		$d=new fm_logical_expression;
		$d->operator='+';
		$e=new fm_text_constant;
		$e->value='vf';
		$a->children=Array($c,$b,$d,$e);
		$c=new fm_text_constant;
		$c->value='6';
		$b=new fm_text_constant;
		$b->value='7';
		$f=new fm_logical_expression;
		$f->operator='&';
		$f->children=$this->sn_g(3,2);
		
		$d->children=Array($c,$b,$f);
		$_SESSION['ed_tree_main_fortest']=serialize($a);
	}
	
	function html_inner()
	{
		if(!isset($_SESSION['ed_tree_main_fortest']))
			$this->set_new();
		#$this->args['filters_m']=unserialize($_SESSION['filters_m_test']);
		$prev=unserialize($_SESSION['ed_tree_main_fortest']);
		$qg=$prev->to_show();
		if(is_object($qg))$this->result_text->text=$qg->result();
		else
		 $this->result_text->text='undef';
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		$result_div_id=$ev->context[$ev->parent_name]['result_div_id'];
		if($ev->rem_name=='r')
		{
			$this->oid=$ev->context[$ev->parent_name]['oid'];
			$this->set_new();
			print "window.location.reload(true);";
		}
		$prev=unserialize($_SESSION['ed_tree_main_fortest']);
		editor_generic::handle_event($ev);
		$after=unserialize($_SESSION['ed_tree_main_fortest']);
		if($prev->rev != $after->rev)
		{
			$qg=$after->to_show();
			
			if(is_object($qg))print "\$i('".$result_div_id."').textContent='".js_escape($qg->result())."';";
			else
			 print "\$i('".$result_div_id."').textContent='undef';";
		}
	}
}

$tests_m_array[]='ed_tree_main_test';


/*
##################################################################################	
	Implement methods for query_gen_ext editor
##################################################################################	
*/


class query_gen_ext_manipulator
{
	function find($obj,$path)
	{
		$found=$obj;
		$path_e=explode('/',$path);
		$c=count($path_e);
		for($k=0;$k<$c;$k++)
			if($path_e[$k]!=='')
			{
				$ch=$this->children($found);
				if(preg_match('/[0-9]+/',$path_e[$k]))
					$found=$ch[$path_e[$k]];
				else
					$found=$found->{$path_e[$k]};
			}
		return $found;
	}
	
	function del_node($obj,$path)
	{
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(get_class($obk)=='query_gen_ext')return;
		if(preg_match('/[0-9]+/',$last))
		{
			if($obk->static_children)
			{
				$obk->exprs[$last]=new sql_null;
				return true;
			}
			$a=Array();
			foreach($obk->exprs as $k => $v)
				if($k !=$last)$a[]=$v;
			$obk->exprs=$a;
		}else{
			
			#$obk->$last=new fm_undefined;
			return false;
		}
		return true;
	}
	
	function pick_node($obj,$path)
	{
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(get_class($obk)=='query_gen_ext')$obk->static_exprs=true;
		$chl=$this->children($obk);
		if(preg_match('/[0-9]+/',$last))
		{
			foreach($chl as $k => $v)
				if($k==$last)
				{
					$found=$v;
					if(!$obk->static_exprs)
						$obk->exprs[$k]='Picked';
					break;
				}
				return $found;
		}
		return true;
	}
	
	function add_node($obj,$path,$new)
	{
		$path_e=explode('/',$path);
		$last=array_pop($path_e);
		$obk=$this->find($obj,implode('/',$path_e));
		if(get_class($obk)=='query_gen_ext')$obk->static_exprs=true;
		if(isset($obk->on) && isset($obk->what) && preg_match('/join/i',$obk->type))$obk->static_exprs=true;
		if(get_class($obk)=='sql_joins')
		{
			unset($new);
			$new->type='LEFT INNER JOIN';
			$new->on=new sql_expression('AND');
			$new->what=new sql_list;
		}
		if(preg_match('/[0-9]+/',$last))
		{
			if($obk->static_exprs)
			{
				//$obk->exprs[$last]=$new;
				return;
			}
			$a=Array();
			if(is_array($obk->exprs))
				foreach($obk->exprs as $k => $v)
				{
					if($k ==$last)$a[]=$new;
					$a[]=$v;
				}
			if(($last>=count($obk->exprs))|| !is_array($obk->exprs))$a[]=$new;
			$obk->exprs=$a;
		}else{
			
			if($last != '')$obk->$last=$new;
		}
	}
	
	function cleanup_picked($obj)
	{
		$ch=$this->children($obj);
		if(is_array($ch))
		{
			$a=Array();
			$found=false;
			foreach($ch as $v)
			{
				if($v==='Picked')$found=true;
				if($v!=='Picked')
				{
					$a[]=$v;
					if(is_array($this->children($v)))$this->cleanup_picked($v);
				}
			}
			if($found && isset($obj->exprs))$obj->exprs=$a;
		}
	}
	
	function children($obj)
	{
		if(get_class($obj)=='query_gen_ext')
		{
			return Array($obj->what,$obj->from,$obj->joins,$obj->where,$obj->group,$obj->order,$obj->having);
		}
		if(isset($obj->on) && isset($obj->what) && preg_match('/join/i',$obj->type))
		{
			return Array($obj->what,$obj->on);
		}
		if(is_array($obj->exprs))return $obj->exprs;
		return NULL;
	}
	
	function text($obj)
	{
		switch(get_class($obj))
		{
		case 'sql_null':return 'Null'.(($obj->alias!='')?" as ".$obj->alias:'');
		case 'sql_immed':return "'".$obj->val."'".(($obj->alias!='')?" as ".$obj->alias:'');
		case 'sql_var':return "@'".$obj->val."'".(($obj->alias!='')?" as ".$obj->alias:'');
		case 'sql_subquery':return "subquery".(($obj->alias!='')?" as ".$obj->alias:'');
		case 'sql_expression':return "<".$obj->operator.">:".count($obj->exprs).(($obj->alias!='')?" as ".$obj->alias:'');
		case 'sql_list':return "".$obj->func."(..):".count($obj->exprs).(($obj->alias!='')?" as ".$obj->alias:'');
		case 'sql_column':return	"c: ".(($obj->db!='')?"`".$obj->db."`.":"").
									(($obj->tbl!='')?"`".$obj->tbl."`.":"").
									(($obj->col!='')?"`".$obj->col."`":"").
									(($obj->alias!='')?" as ".$obj->alias:'');
		case 'sql_order':return "order:".count($obj->exprs).(($obj->alias!='')?" as ".$obj->alias:'');
		case 'sql_joins':return "joins:".count($obj->exprs).(($obj->alias!='')?" as ".$obj->alias:'');
		case 'query_gen_ext':return "query:";
		}
		if(isset($obj->on) && isset($obj->what))return $obj->type;
		return "unknown";
	}
	
	function item_editor()
	{
		return 'ed_query_gen_ext_editor';
	}
}

##################################################################################	

class ed_query_gen_ext_editor extends ed_tree_item_editor//virtual component injector
{
	
	function configure($obj)//virtual method
	{
		$type=get_class($obj);
		$this->title_set($type,$type);
		switch($type)
		{
		case 'sql_null':
			break;
		case 'sql_immed':
			//TODO: localization
			$this->field_add($obj,'val','Значение',new editor_text);
			$this->field_add($obj,'alias','alias',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'sql_var':
			//TODO: localization
			$this->field_add($obj,'val','Значение',new editor_text);
			$this->field_add($obj,'alias','alias',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'sql_subquery':
			//TODO: localization
			$this->field_add($obj,'alias','alias',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'sql_expression':
			//TODO: localization
			$this->field_add($obj,'operator','operator',new editor_text);
			$this->field_add($obj,'alias','alias',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'sql_list':
			$this->field_add($obj,'func','function',new editor_text);
			$this->field_add($obj,'alias','alias',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'sql_column':
			$this->field_add($obj,'db','db',new editor_text);
			$this->field_add($obj,'tbl','tbl',new editor_text);
			$this->field_add($obj,'col','col',new editor_text);
			$this->field_add($obj,'alias','alias',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'sql_order':
			$this->field_add($obj,'function','function',new editor_text);
			$this->field_add($obj,'alias','alias',new editor_text);
			$this->field_add($obj,'to_variable','Переменная',new editor_text);
			$this->field_add($obj,'invert','Инвертировать',new editor_checkbox);
			break;
		case 'sql_joins':
			break;
		case 'query_gen_ext':
			$this->field_add($obj,'count','count',new editor_text);
			$this->field_add($obj,'offset','offset',new editor_text);
			break;
		}
		if(isset($obj->on) && isset($obj->what) && preg_match('/join/i',$obj->type))
		{
			$this->field_add($obj,'type','type',new editor_text);
			$this->title_set('join','join');
		}
	}
	
	
	function handle_event($ev)//parent handles events
	{
		editor_generic::handle_event($ev);
	}
}





#####################################################################################################



class ed_tree_main_query_gen_ext extends ed_tree_main
{
	
	function fetch()
	{
		return unserialize($_SESSION['ed_tree_main_query_gen_ext_test']);
	}
	
	function store($new)
	{
		if(! isset($new->rev))$new->rev=0;
		else $new->rev++;
		$_SESSION['ed_tree_main_query_gen_ext_test']=serialize($new);
	}
	
	function manipulator()
	{
		return new query_gen_ext_manipulator;
	}
	
	function add_menu($to)
	{
			//TODO: localization
		$add_d_cont=Array(
			'sql_null'=>'<nul>',
			'sql_immed'=>'<im>',
			'sql_var'=>'<va>',
			'sql_column'=>'<col>',
			'sql_expression'=>'<ex>',
			'sql_list'=>'<li>',
			'sql_subquery'=>'<sq>'
			);
		if(!isset($to))
		{
			foreach($add_d_cont as $nn => $vv)
				editor_generic::addeditor($nn,new ed_tree_main_nd($vv));
		}elseif(is_object($to)){
			foreach($add_d_cont as $nn => $vv)
			{
				$to->append_child($this->editors[$nn]);
				$this->editors[$nn]->css_style['cursor']='default';
				$this->editors[$nn]->attributes['onmousedown']="resizer.create_ghost(event,this,{t:'".$nn."',d:''});return false;";
			}
		}
	}
}

/*
##################################################################################	
		test for ed_tree_main_query_gen_ext
##################################################################################	
*/

class ed_tree_main_query_gen_ext_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('r',new editor_button);
		$this->append_child($this->editors['r']);
		$this->editors['r']->attributes['value']='x';
		editor_generic::addeditor('m',new ed_tree_main_query_gen_ext);
		$this->append_child($this->editors['m']);
		$this->result=new dom_div;
		$this->result_text=new dom_statictext;
		$this->append_child($this->result);
		$this->result->append_child($this->result_text);
		$this->result->css_style['border']='1px solid green';
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		$this->args=Array();
		$this->context=Array();
		$this->keys=Array();
		$this->oid=96;
		$this->context[$this->long_name]['result_div_id']=$this->result->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
			
			
	}
	
	
	function set_new()
	{
		//$a=new meta_query_gen;
		//$a->oid=$this->oid;
		$n=new query_gen_ext;
		$n->oid=$this->oid;
		$_SESSION['ed_tree_main_query_gen_ext_test']=serialize($n);
		return;
	}
	
	function html_inner()
	{
		if(!isset($_SESSION['ed_tree_main_query_gen_ext_test']))
			$this->set_new();
		#$this->args['filters_m']=unserialize($_SESSION['filters_m_test']);
		$prev=unserialize($_SESSION['ed_tree_main_query_gen_ext_test']);
		$this->result_text->text=$prev->result();
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		$result_div_id=$ev->context[$ev->parent_name]['result_div_id'];
		if($ev->rem_name=='r')
		{
			$this->oid=$ev->context[$ev->parent_name]['oid'];
			$this->set_new();
			print "window.location.reload(true);";
		}
		$prev=unserialize($_SESSION['ed_tree_main_query_gen_ext_test']);
		editor_generic::handle_event($ev);
		$after=unserialize($_SESSION['ed_tree_main_query_gen_ext_test']);
		if($prev->rev != $after->rev)
		{
			
			print "\$i('".$result_div_id."').textContent='".js_escape($after->result())."';";
			//print "\$i('".$result_div_id."').textContent='undef';";
		}
	}
}

$tests_m_array[]='ed_tree_main_query_gen_ext_test';




















?>
