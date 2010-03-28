<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once('lib/ddc_meta.php');
require_once('lib/settings.php');
require_once('lib/commctrls.php');


$ddc_tables[TABLE_META_TREE_TMP]= clone $ddc_tables[TABLE_META_TREE];
$ddc_tables[TABLE_META_TREE_TMP]->name=TABLE_META_TREE_TMP;
$ddc_tables[TABLE_META_TREE_SELECTIONS]=(object)Array(
	'name' => TABLE_META_TREE_SELECTIONS,
	'cols' => Array(
		Array('name' =>'id', 'sql_type' =>'bigint(20)', 'sql_null' =>0, 'sql_default' =>NULL, 'sql_sequence' => 0, 'sql_comment' =>NULL),
		Array('name' =>'uid', 'sql_type' =>'bigint(20)', 'sql_null' =>0, 'sql_default' =>NULL, 'sql_sequence' => 0, 'sql_comment' =>NULL),
		Array('name' =>'folded', 'sql_type' =>'tinyint(1)', 'sql_null' =>0, 'sql_default' =>0, 'sql_sequence' => 0, 'sql_comment' =>NULL),
		Array('name' =>'selected', 'sql_type' =>'tinyint(1)', 'sql_null' =>0, 'sql_default' =>0, 'sql_sequence' => 0, 'sql_comment' =>NULL)
	),
	'keys' => Array(
		Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
		Array('key' =>'PRIMARY', 'name' =>'uid', 'sub' => NULL)
	)
);


if($_GET['init']=='init')
{
	ddc_gentable_o($ddc_tables[TABLE_META_TREE_SELECTIONS],$sql);
	
	
	ddc_gentable_o($ddc_tables[TABLE_META_TREE_TMP],$sql);
}














class editor_meta_tree extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->global_actions=new dom_div;
		$this->append_child($this->global_actions);
		
		$tbl=new dom_table_x(2,1);
		$this->append_child($tbl);
		
		editor_generic::addeditor('state_view',new EMT_state_view);
		$tbl->cells[0][0]->append_child($this->editors['state_view']);
		
		
		editor_generic::addeditor('nodes',new container_resize_scroll);
		$tbl->cells[0][0]->append_child($this->editors['nodes']);
		//$this->editors['nodes']->css_style['vertical-align']='top';
		$tbl->cells[0][0]->css_style['vertical-align']='top';
		
		$this->nodes_container=new dom_div;
		$this->editors['nodes']->append_child($this->nodes_container);
		editor_generic::addeditor('node',new editor_meta_tree_node);
		$this->nodes_container->append_child($this->editors['node']);
		$this->editors['node']->parent_editor=$this;
		
		editor_generic::addeditor('right',new editor_meta_tree_right);
		$tbl->cells[0][1]->append_child($this->editors['right']);
		
		
		$n='file';
		editor_generic::addeditor('m_'.$n,new container_dropdown_div('div'));
		$this->global_actions->append_child($this->editors['m_'.$n]);
		$this->editors['m_'.$n]->button->attributes['value']=$n;
		$this->editors['m_'.$n]->css_style['display']='inline-block';
		$this->add_button('load','m_'.$n);
		$this->add_button('saveuncond','m_'.$n);
		$this->add_button('apply_nonint_single','m_'.$n);
		$this->add_button('discard','m_'.$n);
		
		$n='edit';
		editor_generic::addeditor('m_'.$n,new container_dropdown_div('div'));
		$this->global_actions->append_child($this->editors['m_'.$n]);
		$this->editors['m_'.$n]->button->attributes['value']=$n;
		$this->editors['m_'.$n]->css_style['display']='inline-block';
		$this->add_button('copy','m_'.$n);
		$this->add_button('paste','m_'.$n);
		$this->add_button('del','m_'.$n);
		$this->add_button('add','m_'.$n);
		$this->add_button('clone','m_'.$n);
		$this->add_button('selnear','m_'.$n);
		$this->add_button('selchildren','m_'.$n);
		$this->add_button('desel','m_'.$n);
		$this->add_button('foldall','m_'.$n);
		$this->add_button('unfoldall','m_'.$n);
		
		#$this->add_button('fields');
		#$this->add_button('diff');
		$this->add_link('diff','?p=editor_meta_tree_diff');
		#$this->add_button('logcheck');
		$this->add_link('logcheck','?p=editor_meta_tree_logerrors');
		#$this->add_button('sqlcheck');
		$this->add_link('sqlcheck','?p=editor_meta_tree_sqlerrors');
		#$this->add_button('cmpold');
		$this->add_link('cmpold','?p=editor_meta_tree_cmpold');
		$this->add_link('inheritance','?p=editor_meta_tree_inh');
		$this->add_button('setfromsql');
		$this->add_button('inheritance_update');
		$this->add_link('save','/ext/table_xml_dump.php?table='.urlencode(TABLE_META_TREE));
		
	}
	
	function add_button($n,$to=NULL)
	{
		$button=new editor_button;
		if(isset($to))
			$button->css_style['width']='10em';
		editor_generic::addeditor($n,$button);
		$button->attributes['value']=$n;
		if(isset($to))
			$this->editors[$to]->append_child($button);
		else $this->global_actions->append_child($button);
	}
	
	function add_link($n,$to=NULL)
	{
		$button=new dom_any('a');
		$button->css_style['border']='1px solid black';
		$button->css_style['margin-left']='1px';
		$button->css_style['margin-right']='1px';
		if(isset($to))
			$button->attributes['href']=$to;
		if(isset($to))
			$button->attributes['target']=$to;
		$txt=new dom_statictext;
		$txt->text=" ".$n." ";
		$button->append_child($txt);
		$this->global_actions->append_child($button);
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
			
			
			//$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		//$this->keys['path']=$this->path;
		$this->context[$long_name]['oid']=$this->oid;
		$this->context[$long_name]['left_id']=$this->editors['nodes']->in->id_gen();
		$this->context[$long_name]['right_id']=$this->editors['right']->id_gen();
		$this->context[$long_name]['state_view_id']=$this->editors['state_view']->id_gen();
		$this->editors['node']->oid=-1;
		//$this->context[$long_name.'.node']['tbl']=$this->context[$long_name]['tbl'];
		$this->editors['node']->keys=Array();
		$this->editors['node']->keys['id']=0;
		unset($this->editors['node']->args);
		$this->editors['node']->args['folded']=(editor_meta_tree_node::FOLDING_MODE==1)?0:1;
		//right cell
		$mode=$this->rootnode->setting_val($this->oid,$this->long_name.'.right._mode',0);
		
		
		foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		//output head
		//output root_node
		//call output child nodes
		parent::html_inner();
	}
	
	
	
	function clone_recursive($id,$p=NULL)
	{
		global $sql;
		$res=$sql->query("SELECT * FROM `".TABLE_META_TREE_TMP."` WHERE id=".$id);
		$row=$sql->fetcha($res);
		$sql->free($res);
		$row['id']=0;
		if(isset($p))$row['parentid']=$p;
		$qt='';
		foreach($row as $i =>$v)
		{
			if(isset($f))$qt.=',';
			$f=1;
			$qt.="`".$sql->esc($i)."`='".$sql->esc($v)."'";
		}
		$res=$sql->query("INSERT INTO `".TABLE_META_TREE_TMP."` SET ".$qt);
		
		$nid=$sql->fetch1($sql->query("SELECT LAST_INSERT_ID()"));
		$res=$sql->query("SELECT id FROM `".TABLE_META_TREE_TMP."` WHERE parentid=".$id);
		while ($row=$sql->fetcha($res))
		{
			$this->clone_recursive($row['id'],$nid);
		}
		$sql->free($res);
		return $nid;
		
	}
	
	function go($id)
	{
		$uri=$_SERVER['PHP_SELF'].'?';
		foreach($_GET as $k =>$v)
			if($k!='id')
		{
			if(isset($f))$uri.='&';
			$f=1;
			$uri.=urlencode($k).'='.urlencode($v);
		}
		if(isset($id))
		{
			if(isset($f))$uri.='&';
			$uri.='id='.urlencode($id);
		}
		print 'window.location.href=\''.$uri.'\';';
	}

	function handle_event($ev)
	{
		global $sql,$ddc_tables;
		$val=$_POST['val'];
		$vale=$sql->esc($val);
		$id=intval($_GET['id']);
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$t=TABLE_META_TREE_TMP;
		}else{//working on real !!
			$t=TABLE_META_TREE;
		}
		$left_reload=false;
		
		/*if(preg_match('/right.e0.pr-/',$ev->rem_name))
		{
		}
		else*/	switch($ev->rem_name)
		{
			//handle root object events here
/*		case 'right.e0.id':
			if($sql->fetch1($sql->query("SELECT COUNT(1) FROM `".$t."` WHERE id='".$vale."'"))>0)
			{
				$ev->failure='Dupplicate id:'.$val;
			};
			break;
		case 'right.e0.parentid':
			if($this->child_check($t,$id,$val))
			{
				$left_reload=true;
				$sql->query("UPDATE `".$t."` SET parentid=".$vale." WHERE id=".$id);
				$ev->updated=true;
			}else{
				$ev->failure='Loop detected:'.$val;
			};
			break;*/
		
		case 'right.e0.pr':
			
			if($val =='id')break;
			if($val == 'verstamp')break;
			$left_reload=true;
			$sql->query("SET @v:=(SELECT `".$sql->esc($val)."` FROM `".$t."` WHERE id=".$id.")");
			$sql->query("UPDATE `".$t."` AS u SET u.`".$sql->esc($val)."`=@v WHERE id IN (SELECT s.id FROM `".TABLE_META_TREE_SELECTIONS."` AS s WHERE s.selected=1 AND s.uid='".$sql->esc($_SESSION['uid'])."')");
			$ev->updated=true;
			break;
	
		case 'right.e0.mmark':
			
			if($val =='id')break;
			if($val == 'verstamp')break;
			$left_reload=true;
			$sql->query("SET @v:=(SELECT `".$sql->esc($val)."` FROM `".$t."` WHERE id=".$id.")");
			$res=$sql->query("SELECT s.id FROM `".$t."` AS s WHERE s.`".$sql->esc($val)."`=@v");
			while($row=$sql->fetcha($res))
			{
				$q="INSERT INTO `".TABLE_META_TREE_SELECTIONS."` SET selected=1 ,uid='".$sql->esc($_SESSION['uid'])."' ,id='".$row['id']."' ON DUPLICATE KEY UPDATE selected=1";
				print "/*".$q."*/\n";
				if(!$sql->query($q))
				{
					print "/*".$sql->err()."*/\n";
				};
			}
			$ev->updated=true;
			$ev->reload_state=true;
			break;
		case 'right.e0.mumark':
			
			if($val =='id')break;
			if($val == 'verstamp')break;
			$left_reload=true;
			$sql->query("SET @v:=(SELECT `".$sql->esc($val)."` FROM `".$t."` WHERE id=".$id.")");
			$res=$sql->query("SELECT s.id FROM `".$t."` AS s WHERE s.`".$sql->esc($val)."`=@v");
			while($row=$sql->fetcha($res))
			{
				$q="INSERT INTO `".TABLE_META_TREE_SELECTIONS."` SET selected=0 ,uid='".$sql->esc($_SESSION['uid'])."' ,id='".$row['id']."' ON DUPLICATE KEY UPDATE selected=0";
				print "/*".$q."*/\n";
				if(!$sql->query($q))
				{
					print "/*".$sql->err()."*/\n";
				};
			}
			$ev->updated=true;
			$ev->reload_state=true;
			break;
		case 'right.e0.invs':
			
			if($val =='id')break;
			if($val == 'verstamp')break;
			$left_reload=true;
			$sql->query("SET @v:=(SELECT `".$sql->esc($val)."` FROM `".$t."` WHERE id=".$id.")");
			$res=$sql->query("SELECT s.id FROM `".$t."` AS s WHERE s.`".$sql->esc($val)."`=@v");
			while($row=$sql->fetcha($res))
			{
				$q="INSERT INTO `".TABLE_META_TREE_SELECTIONS."` SET selected= ! selected ,uid='".$sql->esc($_SESSION['uid'])."' ,id='".$row['id']."' ON DUPLICATE KEY UPDATE selected= ! selected";
				print "/*".$q."*/\n";
				if(!$sql->query($q))
				{
					print "/*".$sql->err()."*/\n";
				};
			}
			$ev->updated=true;
			$ev->reload_state=true;
			break;
		
		case 'fields':
			break;
			$st=new settings_tool;
			$sq=$st->set_query($ev->context[$ev->parent_name]['oid'],$ev->parent_name.'.right._mode',$_SESSION['uid'],0,0);
			$sql->query($sq);
			
			//print 'window.location.reload(true);';
			//exit;
			$right_reload=true;
			break;
		
		
		
		case 'setfromsql':
			$ddc=new ddc_key;
			$ddc->attach($t,$sql);
			$ddc->meta_from_sql_0($_GET['id']);
			//print 'window.location.reload(true);';
			//exit;
			$right_reload=true;
			$ev->updated=true;
			break;
			
		case 'inheritance_update':
			$ddc=new ddc_key;
			$ddc->attach($t,$sql);
			$ddc->inheritance_update(false);
			print 'window.location.reload(true);';
			exit;
			//$right_reload=true;
			//$ev->updated=true;
			break;
		
		case 'discard':
			$sql->query("DELETE FROM `".TABLE_META_TREE_TMP."`");
			$go_id=$sql->fetch1($sql->query("SELECT id FROM `".TABLE_META_TREE_SELECTIONS."` WHERE id=".$sql->esc($_GET['id'])));
			$this->go($go_id);
			//print 'window.location.reload(true);';
			exit;
			break;
		case 'load':
			$sql->query("DELETE FROM `".TABLE_META_TREE_TMP."`");
			$wh="";
			foreach($ddc_tables[TABLE_META_TREE_TMP]->cols as $o)
			{
				if($wh != '' )$wh.=", ";
				$wh.="`".$sql->esc($o['name'])."`";
			}
			$sql->query("INSERT INTO `".TABLE_META_TREE_TMP."` (".$wh.") SELECT ".$wh." FROM `".TABLE_META_TREE."`");
			$go_id=$sql->fetch1($sql->query("SELECT id FROM `".TABLE_META_TREE_TMP."` WHERE id=".$sql->esc($_GET['id'])));
			$this->go($go_id);
			//print 'window.location.reload(true);';
			exit;
			break;
		case 'saveuncond':
			$sql->query("DELETE FROM `".TABLE_META_TREE."`");
			$wh='';
			foreach($ddc_tables[TABLE_META_TREE]->cols as $o)
			{
				if($wh != '' )$wh.=", ";
				$wh.="`".$sql->esc($o['name'])."`";
			}
			$sql->query("INSERT INTO `".TABLE_META_TREE."` (".$wh.") SELECT ".$wh." FROM `".TABLE_META_TREE_TMP."`");
			$go_id=$sql->fetch1($sql->query("SELECT id FROM `".TABLE_META_TREE."` WHERE id=".$sql->esc($_GET['id'])));
			$this->go($go_id);
			//print 'window.location.reload(true);';
			exit;
			break;
		case 'apply_nonint_single':
			if($t==TABLE_META_TREE)
			{
				print "alert('Open and make some changes first');";
				break;
			}
			$new=new ddc_key;
			$new->attach(TABLE_META_TREE_TMP,$sql);
			$old=new ddc_key;
			$old->attach(TABLE_META_TREE,$sql);
			$diff=$new->gen_changes($old);
			if(!is_array($diff) || count($diff)==0)
			{
				print "alert('Open and make some changes first');";
				break;
			}
			reset($diff);
			foreach($diff as $e)
			{
				$sql->query($e->query);
			}
			$sql->query("DELETE FROM `".TABLE_META_TREE."`");
			$wh='';
			foreach($ddc_tables[TABLE_META_TREE]->cols as $o)
			{
				if($wh != '' )$wh.=", ";
				$wh.="`".$sql->esc($o['name'])."`";
			}
			$sql->query("INSERT INTO `".TABLE_META_TREE."` (".$wh.") SELECT ".$wh." FROM `".TABLE_META_TREE_TMP."`");
			$go_id=$sql->fetch1($sql->query("SELECT id FROM `".TABLE_META_TREE."` WHERE id=".$sql->esc($_GET['id'])));
			$this->go($go_id);
			//print 'window.location.reload(true);';
			exit;
			break;
		case 'del':
			$res=$sql->query("SELECT id FROM `".TABLE_META_TREE_SELECTIONS."` WHERE selected=1 AND uid='".$_SESSION['uid']."'");
			while($row=$sql->fetcha($res))
			{
				$q="DELETE FROM `".TABLE_META_TREE_TMP."` WHERE id=".$row['id'];
				$rr=$sql->query($q);
				$q="DELETE FROM `".TABLE_META_TREE_SELECTIONS."` WHERE id=".$row['id']." AND uid='".$_SESSION['uid']."'";
				$rr=$sql->query($q);
			}
			$sql->free($res);
			$k=0;
			while (true)
			{
				$res=$sql->query("SELECT b.id from `".TABLE_META_TREE_TMP."` as b LEFT OUTER JOIN `".TABLE_META_TREE_TMP."` as c ON b.parentid=c.id WHERE c.id IS NULL AND b.parentid != 0");
				$f=0;
				if($res)
				{
					while($row=$sql->fetchn($res))
					{
						$sql->query("DELETE FROM `".TABLE_META_TREE_TMP."` WHERE id=".$row[0]);
						$f=1;
					};
					$sql->free($res);
				}
				$res=$sql->query("SELECT b.id from `".TABLE_META_TREE_SELECTIONS."` as b LEFT OUTER JOIN `".TABLE_META_TREE_TMP."` as c ON b.id=c.id WHERE c.id IS NULL AND c.uid ='".$_SESSION['uid']."'");
				$d=0;
				if($res)
				{
					while($row=$sql->fetchn($res))
					{
						$sql->query("DELETE FROM `".TABLE_META_TREE_SELECTIONS."` WHERE id=".$row[0]." AND uid ='".$_SESSION['uid']."'");
						$d=1;
					};
					$sql->free($res);
				}
				if($f==0)break;
				$k++;
				if($k==10)break;//10 iterations??????????????? Maybe do more, or do delete twice.
			}
			$go_id=$sql->fetch1($sql->query("SELECT id FROM `".TABLE_META_TREE_TMP."` WHERE id=".$sql->esc($_GET['id'])));
			$this->go($go_id);
			//print 'window.location.reload(true);';
			exit;
			break;
		case 'add':
			$parent=intval($_GET['id']);
			if($t !=TABLE_META_TREE_TMP)$parent=0;
			$sql->query("INSERT INTO `".TABLE_META_TREE_TMP."` SET id=0,parentid=".$parent);
			$id=$sql->fetch1($sql->query("SELECT LAST_INSERT_ID()"));
			$this->go($id);
			exit;
			break;
		case 'clone':
			if(isset($_GET['id']))
			{
				$id=$this->clone_recursive($_GET['id']);
				$this->go($id);
			}
			exit;
			break;
		case 'copy':
			if(isset($_GET['id']))
			{
				$_SESSION['clipboard']['format']='ddc_meta_node';
				$_SESSION['clipboard']['data']=$_GET['id'];
			}
			break;
		case 'paste':
			if(isset($_SESSION['clipboard']['data']) && ($_SESSION['clipboard']['format']=='ddc_meta_node'))
			{
				$id=$this->clone_recursive($_SESSION['clipboard']['data']);
				$sql->query("UPDATE `".$t."` SET parentid=".$_GET['id']." WHERE id=".$id);
				$ev->updated=true;
				
			}
			break;
		case 'desel':
			$sql->query("UPDATE `".TABLE_META_TREE_SELECTIONS."`  SET selected=0 WHERE uid='".$_SESSION['uid']."'");
			$ev->updated=true;
			$ev->reload_state=true;
			break;
		case 'selchildren':
			if(!isset($_GET['id']))exit;
			$f_q="SELECT id FROM `".$t."` WHERE parentid=".intval($_GET['id']);
			$ev->updated=true;
			$ev->reload_state=true;
		case 'selnear':
			if(!isset($_GET['id']))exit;
			$p_id=$sql->fetch1($sql->query("SELECT parentid FROM `".$t."` WHERE id=".intval($_GET['id'])));
			if(!isset($f_q))$f_q="SELECT id FROM `".$t."` WHERE parentid=".$p_id;
			$ev->reload_state=true;
		case 'foldall':
		case 'unfoldall':
			$v=new sql_immed(editor_meta_tree_node::FOLDING_MODE);
			if($ev->rem_name=='unfoldall')
				$v->val=($v->val==1)?0:1;
			$m_Col='folded';
			if(!isset($f_q))$f_q="SELECT id FROM `".$t."`";
			if($ev->rem_name=='selnear' || $ev->rem_name=='selchildren')
			{
				$m_Col='selected';
				$v->val=1;
			}
			$q=new query_gen_ext('INSERT UPDATE');
			$i_id=new sql_immed();
			$q->into->exprs[]=new sql_column(NULL,TABLE_META_TREE_SELECTIONS);
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,$m_Col),
				$v
				));
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'id'),
				$i_id
				));
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'uid'),
				new sql_immed($_SESSION['uid'])
				));
			
			
			$res=$sql->query($f_q);
			while($row=$sql->fetcha($res))
			{
				$i_id->val=$row['id'];
				$sql->query($q->result());
			}
			$ev->updated=true;
			break;
		default:
			;
		}
		
		if($right_reload)
		{
			$right_id=$ev->context[$ev->parent_name]['right_id'];
			$e=$this->editors['right'];
			unset($e->com_parent);
			$e->context=&$ev->context;
			$e->name=$ev->parent_name.'.right';
			$e->etype=$ev->parent_type.'.'.$e->etype;
			$e->oid=-1;
			$e->keys=Array();
			print "\$i('".js_escape($right_id)."').innerHTML=";
			reload_object($e,true);
			
		}
		
		$left_id=$ev->context[$ev->parent_name]['left_id'];
		$state_view_id=$ev->context[$ev->parent_name]['state_view_id'];
		$e=$this->editors['node'];
		unset($e->com_parent);
		$e->context=&$ev->context;
		$e->name=$ev->parent_name.'.node';
		$e->etype=$ev->parent_type.'.'.$e->etype;
		$e->oid=-1;
		$e->keys=Array();
		$e->keys['id']=0;
		$e->args['folded']=(editor_meta_tree_node::FOLDING_MODE==1)?0:1;
		
		editor_generic::handle_event($ev);
		if($ev->updated)
		{
			print "\$i('".js_escape($left_id)."').innerHTML=";
			reload_object($e);
		}
		if($ev->reload_state)
		{
			$this->editors['state_view']->id_alloc();
			print "\$i('".js_escape($state_view_id)."').innerHTML=";
			reload_object($this->editors['state_view'],true);
		}
	}
}



###########################################################################################################################3

class EMT_state_view extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->open=new dom_span;
		$text=new dom_statictext;
		$this->append_child($this->open->append_child($text));
		$text->text='Open';
		$this->css_style['border']='1px dashed';
		$this->open->css_style['background']='green';
		$this->open->css_style['color']='white';
		$this->cnt=new dom_span;
		$this->cnttext=new dom_statictext;
		$this->append_child($this->cnt->append_child($this->cnttext));
		$this->cnt->css_style['color']='black';
	}
	
	function html_inner()
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		$c=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_SELECTIONS."` WHERE selected=1 AND uid=".$_SESSION['uid']));
		$this->cnttext->text=' ['.$c.']';
		if($r==0)
			$this->open->css_style['display']='none';
		if($c==0)
			$this->cnt->css_style['display']='none';
		parent::html_inner();
		
	}
	
	function bootstrap()
	{
	}
	
	function handle_event($ev)
	{
	}
}



############################################################################################################################3


class editor_meta_tree_node extends dom_div
{
	const FOLDING_MODE=0;
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->id_stack=Array();
		$this->controls=new dom_div;
		$this->children=new dom_div;
		$this->append_child($this->controls);
		$this->append_child($this->children);
		
		//editor_generic::addeditor('folded',new editor_checkbox);
		if(self::FOLDING_MODE==0)
			editor_generic::addeditor('folded',new editor_fold(true));
		else
			editor_generic::addeditor('folded',new editor_fold);
		$this->controls->append_child($this->editors['folded']);
		//$this->editors['fold']->attributes['value']='*';
		
		editor_generic::addeditor('selected',new editor_checkbox);
		$this->controls->append_child($this->editors['selected']);
		
		
		editor_generic::addeditor('id',new editor_href);
		$this->controls->append_child($this->editors['id']);
		editor_generic::addeditor('name',new editor_statictext);
		$this->editors['id']->append_child($this->editors['name']);
		$this->editors['id']->href='?p='.$_GET['p'].'&id=%s';
		
		$this->css_class='editor_meta_tree_node';
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['children_id']=$this->children->id_gen();
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $e)$e->bootstrap();
		$this->editors['folded']->css_style['visibility']=($this->args['nofold'] || $this->keys['id']==0)?'hidden':'';
	}
	
	function html_head()
	{
		parent::html_head();
		$this->controls->html();
		$this->children->html_head();
		
	}
	function html_tail()
	{
		$this->children->html_tail();
		parent::html_tail();
	}
	function html_inner()
	{
		global $sql;
		$isfolded=isset($this->args['folded'])?$this->args['folded']:0;
		if(($isfolded!=self::FOLDING_MODE)&&($this->args['nofold']==0))
		{
			if(!isset($this->working_tbl))
			{
				$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
				if($r>0)//working on copy
				{
					$this->working_tbl=TABLE_META_TREE_TMP;
				}else{//working on real !!
					$this->working_tbl=TABLE_META_TREE;
				}
			}
			$res=$sql->query("SELECT t.id,t.parentid,t.name,s.folded,s.selected, (SELECT COUNT(1) FROM `".$sql->esc($this->working_tbl)."` AS a WHERE a.parentid=t.id )=0 as nofold FROM `".$sql->esc($this->working_tbl)."` AS t LEFT OUTER JOIN `".$sql->esc(TABLE_META_TREE_SELECTIONS)."` AS s ON t.id=s.id AND s.uid='".$sql->esc($_SESSION['uid'])."' WHERE t.parentid=".$this->keys['id']." ORDER BY t.id");
			while($this->args=$sql->fetcha($res))
			{
				array_push($this->id_stack,$this->keys['id']);
				$this->keys['id']=$this->args['id'];
				if($this->args['id']==$_GET['id'])
					$this->editors['id']->css_style['font-weight']='bold';
				else
					unset($this->editors['id']->css_style['font-weight']);
				$folded=isset($this->args['folded'])?$this->args['folded']:0;
				/*
				if($folded==self::FOLDING_MODE)
					$this->editors['id']->css_style['color']='gray';
				else
					unset($this->editors['id']->css_style['color']);
				*/
				$this->editors['id']->attributes['title']=$this->args['id'];
				$this->editors['id']->attributes['onclick']="if(window.default_action)return window.default_action(event,'".js_escape($this->args['id'])."');";
				
				$this->id_alloc();
				$this->bootstrap();
				$this->html();
				$this->keys['id']=array_pop($this->id_stack);
			}
			if($res)$sql->free($res);
		}
		if(count($this->id_stack)==0)unset($this->working_tbl);
	}
	
	
	function handle_event($ev)
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$t=TABLE_META_TREE_TMP;
		}else{//working on real !!
			$t=TABLE_META_TREE;
		}
		$val=$_POST['val'];
		switch($ev->rem_name)
		{
			//handle root object events here
		case 'folded':
			$q=new query_gen_ext('INSERT UPDATE');
			$q->into->exprs[]=new sql_column(NULL,TABLE_META_TREE_SELECTIONS);
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'folded'),
				new sql_immed($val)
				));
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'id'),
				new sql_immed($ev->keys['id'])
				));
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'uid'),
				new sql_immed($_SESSION['uid'])
				));
			$res=$sql->query($q->result());
			//print "/*  ".$q->result()."  */";
			$disp=($val==self::FOLDING_MODE)?'none':'';
			print "\$i('".js_escape($ev->context[$ev->parent_name]['children_id'])."').style.display='".$disp."';";
			//if($val==0)$ev->updated=true;
			if($val!=self::FOLDING_MODE)
			{//self reload
				$e=$this;
				unset($e->com_parent);
				$e->context=&$ev->context;
				$e->name=$ev->parent_name;
				$e->etype=$ev->parent_type;
				$e->oid=-1;
				$e->keys=Array();
				$e->keys['id']=$ev->keys['id'];
				$e->args['id']=$ev->keys['id'];
				$e->args['folded']=$val;
				print "\$i('".js_escape($ev->context[$ev->parent_name]['children_id'])."').innerHTML=";
				reload_object($e,true);
				//print "alert('".js_escape($ev->context[$ev->parent_name]['htmlid'])."');";
			}
			break;
		case 'selected':
			$q=new query_gen_ext('INSERT UPDATE');
			$q->into->exprs[]=new sql_column(NULL,TABLE_META_TREE_SELECTIONS);
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'selected'),
				new sql_immed($val)
				));
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'id'),
				new sql_immed($ev->keys['id'])
				));
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'uid'),
				new sql_immed($_SESSION['uid'])
				));
			$res=$sql->query($q->result());
			$ev->reload_state=true;
			break;
		default:
			;
		}
		
		editor_generic::handle_event($ev);
	}
	
}

##########################################################################################################
##########################################################################################################
class editor_meta_tree_filtered extends dom_div
{
}




























##########################################################################################################
##########################################################################################################


class editor_meta_tree_right extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('e0',new editor_meta_tree_row);
		$this->append_child($this->editors['e0']);
		editor_generic::addeditor('e1',new editor_meta_tree_diff);
		$this->append_child($this->editors['e1']);
		editor_generic::addeditor('e2',new editor_meta_tree_logerrors);
		$this->append_child($this->editors['e2']);
		editor_generic::addeditor('e3',new editor_meta_tree_sqlerrors);
		$this->append_child($this->editors['e3']);
		editor_generic::addeditor('e4',new editor_meta_tree_cmpold);
		$this->append_child($this->editors['e4']);
	}
	
	function bootstrap()
	{
		global $sql;
		$this->long_name=editor_generic::long_name();
		$st=new settings_tool;
		$sq=$st->single_query($this->oid,$this->long_name.'._mode',$_SESSION['uid'],0);
		$this->mode=$sql->fetch1($sql->query($sq));
		if(!isset($this->mode))$this->mode=0;
		//if(is_array($this->editors))foreach($this->editors as $i => $e)
		//{
			$i='e'.$this->mode;
			$e=$this->editors[$i];
			
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		//}
		//if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
		$e->bootstrap();
	}
	
	function html_inner()
	{
	$this->editors['e'.$this->mode]->html();
	}
	
	function handle_event($ev)
	{

		editor_generic::handle_event($ev);
	}
	
}


#############################################################################
#############################################################################

class editor_meta_tree_row extends dom_div
{
	function __construct()
	{
		parent::__construct();
		global $ddc_tables;
		$this->collist=&$ddc_tables[TABLE_META_TREE]->cols;
		$this->etype=get_class($this);
		$this->id_stack=Array();
		$tbl=new dom_table;
		$this->append_child($tbl);
		foreach($this->collist as $col)
		{
			$tr=new dom_tr;
			$td_n=new dom_td;//name
			$tr->append_child($td_n);
			$txt_n=new dom_statictext;
			$td_n->append_child($txt_n);
			$td_e=new dom_td;//editor
			$tr->append_child($td_e);
			//add info/action/etc/column?...
			$tr->css_style['background']=string_to_color($col['name'],1);
			$tr->css_style['color']=bgcolor_to_color($tr->css_style['background']);
			
			$txt_n->text=(isset($col['hname']))?$col['hname']:$col['name'];
			$td_n->attributes['title']=$col['name'];
			
			if(isset($col['editor']))
				$ed=new $col['editor']($col);
				#else $ed=new $col['editor'];
			else $ed=new editor_text;
			editor_generic::addeditor($col['name'],$ed);
			$td_e->append_child($ed);
			
			$td_e=new dom_td;//st selection to this button
			$tr->append_child($td_e);
			$cont_dd=new container_dropdown_div('div');
			$cont_dd->button->attributes['value']='...';
			$td_e->append_child($cont_dd);
			
			$ed=new editor_valbutton;
			$cont_dd->append_child($ed);
			$ed->attributes['value']='*>';
			$ed->attributes['title']='Propagate value to selection';
			editor_generic::addeditor('pr-'.$col['name'],$ed);
			$ed->name='pr';
			$ed->value=$col['name'];
			
			$ed=new editor_valbutton_image;
			$cont_dd->append_child($ed);
			$ed->attributes['src']='/i/mark-eq.png';
			$ed->attributes['title']='Mark matching items';
			editor_generic::addeditor('mmark-'.$col['name'],$ed);
			$ed->name='mmark';
			$ed->value=$col['name'];
			
			$ed=new editor_valbutton_image;
			$cont_dd->append_child($ed);
			$ed->attributes['src']='/i/unmark-eq.png';
			$ed->attributes['title']='Unmark matching items';
			editor_generic::addeditor('mumark-'.$col['name'],$ed);
			$ed->name='mumark';
			$ed->value=$col['name'];
			
			$ed=new editor_valbutton_image;
			$cont_dd->append_child($ed);
			$ed->attributes['src']='/i/inv-eq.png';
			$ed->attributes['title']='Invert matching items selection';
			editor_generic::addeditor('invs-'.$col['name'],$ed);
			$ed->name='invs';
			$ed->value=$col['name'];
			
			
			
			$tbl->append_child($tr);
			
		}
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->args=Array();
		$this->context[$this->long_name]['verstamp_id']=$this->editors['verstamp']->parentnode->id_gen();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		global $sql;
		if($_GET['id']=='')
		{
			$this->rootnode->out('Select node on the left');
			return;
		}
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$t=TABLE_META_TREE_TMP;
		}else{//working on real !!
			$t=TABLE_META_TREE;
		}
		$q=new query_gen_ext('SELECT');
		$q->from->exprs[]=new sql_column(NULL,$t);
		foreach($this->collist as $col)
		{
			$q->what->exprs[]=new sql_column(NULL,NULL,$col['name']);
			//$q->what->exprs[]=new sql_immed($col['name'],'pr-'.$col['name']);
		}
		$q->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'id'),
			new sql_immed($_GET['id'])
			));
		$res=$sql->query($q->result());
		if(!$res)
		{
			$this->rootnode->out('Hmmm.. Query failed. Maybe selected object has been deleted in another window.<br>');
			$this->rootnode->out($q->result());
			return;
		}
		$row=$sql->fetcha($res);
		if(!$row)
		{
			$this->rootnode->out('Hmmm.. Query failed. Maybe selected object has been deleted in another window.<br>');
			$this->rootnode->out($q->result());
			return;
		}
		foreach($row as $i=>$v)$this->args[$i]=$v;
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$t=TABLE_META_TREE_TMP;
		}else{//working on real !!
			$t=TABLE_META_TREE;
		}
		if(!preg_match('/\./',$ev->rem_name) && !$ev->updated)
		{
			$ddc=new ddc_key;
			$ddc->attach($t,$sql);
			$res=$ddc->set_col($_GET['id'],$ev->rem_name,$_POST['val']);
			/*
			$q=new query_gen_ext('UPDATE');
			$q->into->exprs[]=new sql_column(NULL,$t);
			$q->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,$ev->rem_name),
				new sql_immed($_POST['val'])
				));
			$q->where->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'id'),
				new sql_immed($_GET['id'])
				));
			$res=$sql->query($q->result());
			*/
			//print 'alert(\'x'.$res.'x\');';
			
			if($res!==true)
			{
				$ev->failure=$res.' : '.$sql->err();
			}
			$ev->updated=true;
		}
		if($ev->updated)
		{
			$q=new query_gen_ext('SELECT');
			$q->from->exprs[]=new sql_column(NULL,$t);
			$q->what->exprs[]=new sql_column(NULL,NULL,'verstamp');
			$q->where->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'id'),
				new sql_immed($_GET['id'])
				));
			$verstamp=$sql->fetch1($sql->query($q->result()));
			print "\$i('".js_escape($ev->context[$ev->parent_name]['verstamp_id'])."').innerHTML='".js_escape(htmlspecialchars($verstamp,ENT_QUOTES))."';";
		}
		editor_generic::handle_event($ev);
	}
	
}


class editor_text_autosuggest_qm extends editor_text_autosuggest_query
{
	function __construct(&$row)
	{
		parent::__construct();
		if(isset($row['editor_config']))$this->rawquery=$row['editor_config'];
		//$this->etype=get_class($this);
		$this->etype='editor_text_autosuggest';
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['rawquery']=$this->rawquery;
		#parent::bootstrap();
		editor_text_autosuggest::bootstrap();
	}
}




#################################################################################################################
#################################################################################################################
#################################################################################################################
#################################################################################################################


class editor_meta_tree_diff extends dom_div
{
	function __construct()
	{
		parent::__construct();
		global $ddc_tables;
		$tbl=new dom_table;
		$this->tbl=$tbl;
		$this->append_child($tbl);
		$this->row=new dom_tr;
		$tbl->append_child($this->row);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('type',new editor_statictext);
		$td->append_child($this->editors['type']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('href',new editor_href);
		$td->append_child($this->editors['href']);
		$this->editors['href']->href='?p='.$_GET['p'].'&id=%s';
		editor_generic::addeditor('id',new editor_statictext);
		$this->editors['href']->append_child($this->editors['id']);
	
		$td=new dom_td;
		$this->row->append_child($td);
		//editor_generic::addeditor('change',new editor_statictext);
		//$td->append_child($this->editors['change']);
		$this->ch=new EMTD_diff;
		$td->append_child($this->ch);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$this->tbl->html_head();
			$new=new ddc_key;
			$new->attach(TABLE_META_TREE_TMP,$sql);
			$old=new ddc_key;
			$old->attach(TABLE_META_TREE,$sql);
			$diff=$new->compare($old);
			foreach($diff as $d)
			{
				$this->args['type']=$d->type;
				$this->args['id']=$d->id;
				$this->args['href']=$d->id;
				//$this->args['change']=count($d->diff);
				$this->ch->htext=count($d->diff);
				$this->ch->rows=&$d->diff;
				$this->row->css_style['background']='';
				switch($d->type)
				{
					case '-':	$this->row->css_style['background']='red';break;
					case '+':	$this->row->css_style['background']='green';break;
					case '*':	$this->row->css_style['background']='yellow';break;
				}
				$this->row->html();
				$this->row->id_alloc();
			}
		
			$this->tbl->html_tail();
		}else{//working on real !!
			$this->rootnode->out('nothing to show. Open structure for edit first and make some changes.');
		}
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}


class EMTD_diff extends dom_div//in:string htext,Array rows
{
	function __construct()
	{
		parent::__construct();
		$this->hint=new dom_statictext;
		$this->hidden=new dom_div;
		$this->tbl=new dom_table;
		$this->tr=new dom_tr;
		$this->td=new dom_td;
		$this->txt=new dom_statictext;
		$this->append_child($this->hint);
		$this->append_child(
			$this->hidden->append_child(
				$this->tbl->append_child(
					$this->tr->append_child(
						$this->td->append_child(
							$this->txt
			)	)	)	)	);
		$this->css_class=get_class($this);
		
		
	}
	function html_inner()
	{
		$this->hint->text=$this->htext;
		$this->hint->html();
		$this->hidden->html_head();
		$this->tr->css_style['background']=string_to_color('head');
		$this->tr->css_style['color']=bgcolor_to_color(string_to_color('head'));
		$this->tbl->html_head();
		
				$this->tr->html_head();
				$this->txt->text='field';
				$this->td->html();
				$this->td->id_alloc();
				
				$this->txt->text='old';
				$this->td->html();
				$this->td->id_alloc();
				
				$this->txt->text='new';
				$this->td->html();
				$this->td->id_alloc();
				
				$this->tr->html_tail();
				$this->tr->id_alloc();
		if(is_array($this->rows))
			foreach($this->rows as $rt => $rv)
				if($rv->old != $rv->new)
			{
				$this->tr->css_style['background']=string_to_color($rt,1);
				$this->tr->css_style['color']=bgcolor_to_color($this->tr->css_style['background']);
				
				$this->tr->html_head();
				$this->txt->text=$rt;
				$this->td->html();
				$this->td->id_alloc();
				
				$this->txt->text=$rv->old;
				$this->td->html();
				$this->td->id_alloc();
				
				$this->txt->text=$rv->new;
				$this->td->html();
				$this->td->id_alloc();
				
				$this->tr->html_tail();
				$this->tr->id_alloc();
			}
		
		$this->tbl->html_tail();
		$this->hidden->html_tail();
	}
	function after_build_before_children()
	{
		$this->rootnode->exstyle['div.'.get_class($this).' div']=Array(
			'display'=>'none'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div']=Array(
			'display'=>'block',
			'position'=>'absolute',
			'border'=>'1px grey solid',
			'background'=>'white',
			'margin-left'=>'0.3em'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div table']=Array(
			'border-collapse'=>'collapse'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div table tr td']=Array(
			'border'=>'1px solid grey'
			);
	}
}


class editor_meta_tree_logerrors extends dom_div
{
	function __construct()
	{
		parent::__construct();
		global $ddc_tables;
		$tbl=new dom_table;
		$this->tbl=$tbl;
		$this->append_child($tbl);
		$this->row=new dom_tr;
		$tbl->append_child($this->row);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('type',new editor_statictext);
		$td->append_child($this->editors['type']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('href',new editor_href);
		$td->append_child($this->editors['href']);
		$this->editors['href']->href='?p='.$_GET['p'].'&id=%s';
		editor_generic::addeditor('id',new editor_statictext);
		$this->editors['href']->append_child($this->editors['id']);
		
	
		$td=new dom_td;
		$this->row->append_child($td);
		//editor_generic::addeditor('change',new editor_statictext);
		//$td->append_child($this->editors['change']);
		editor_generic::addeditor('name',new editor_statictext);
		$td->append_child($this->editors['name']);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$this->tbl->html_head();
			$new=new ddc_key;
			$new->attach(TABLE_META_TREE_TMP,$sql);
			$diff=$new->check_logical();
			foreach($diff as $d)
			{
				$this->args['type']=$d->type;
				$this->args['id']=isset($d->id)?$d->id:'?';
				$this->args['href']=isset($d->id)?"?p=editor_meta_tree&id=".$d->id:'';
				$this->args['name']=$d->name;
				//$this->args['change']=count($d->diff);
				$this->row->css_style['background']='';
				switch($d->type)
				{
					case 'table duplicate':	$this->row->css_style['background']=string_to_color('sql_table',2);break;
					case 'column duplicate':	$this->row->css_style['background']=string_to_color('name',2);break;
					//case '*':	$this->row->css_style['background']='yellow';break;
				}
				$this->row->html();
				$this->row->id_alloc();
			}
		
			$this->tbl->html_tail();
		}else{//working on real !!
			$this->rootnode->out('nothing to show. Open structure for edit first and make some changes.');
		}
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}


#############################################################################################################################
#############################################################################################################################
##########################################     editor_meta_tree_sqlerrors
#############################################################################################################################
#############################################################################################################################


class editor_meta_tree_sqlerrors extends dom_div
{
	function __construct()
	{
		parent::__construct();
		global $ddc_tables;
		$this->etype=get_class($this);
		$tbl=new dom_table;
		$this->tbl=$tbl;
		$this->append_child($tbl);
		$this->row=new dom_tr;
		$tbl->append_child($this->row);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('type',new editor_statictext);
		$td->append_child($this->editors['type']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('href',new editor_href);
		$td->append_child($this->editors['href']);
		$this->editors['href']->href='?p='.$_GET['p'].'&id=%s';
		editor_generic::addeditor('id',new editor_statictext);
		$this->editors['href']->append_child($this->editors['id']);
	
		$td=new dom_td;
		$this->row->append_child($td);
		//editor_generic::addeditor('change',new editor_statictext);
		//$td->append_child($this->editors['change']);
		editor_generic::addeditor('name',new editor_statictext);
		$td->append_child($this->editors['name']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('sql_change',new EMTSE_choice);
		$td->append_child($this->editors['sql_change']);
		$this->editors['sql_change']->htext='sql';
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('meta_change',new EMTSE_choice);
		$td->append_child($this->editors['meta_change']);
		$this->editors['meta_change']->htext='meta';
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('difference',new EMTSE_list);
		$td->append_child($this->editors['difference']);
		$this->editors['difference']->htext='dif';
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$t=TABLE_META_TREE_TMP;
		}else{
			$t=TABLE_META_TREE;
		}
		$this->tbl->html_head();
		$new=new ddc_key;
		$new->attach($t,$sql);
		$diff=$new->check_db();
		foreach($diff as $d)
		{
			$this->args['type']=$d->type;
			$this->args['id']='?';
			$this->args['href']='';
			$this->args['name']=$d->descr;
			$this->args['sql_change']=$d->sql_change;
			$this->args['meta_change']=$d->meta_change;
			unset($this->args['difference']);
			if(isset($d->difference))$this->args['difference']=$d->difference;
			//$this->args['change']=count($d->diff);
			$this->row->css_style['background']='';
			switch($d->type)
			{
				case 'table duplicate':	$this->row->css_style['background']=string_to_color('sql_table',2);break;
				case 'column duplicate':	$this->row->css_style['background']=string_to_color('name',2);break;
				//case '*':	$this->row->css_style['background']='yellow';break;
			}
			$this->row->html();
			$this->row->id_alloc();
		}
	
		$this->tbl->html_tail();
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}

class EMTSE_choice extends dom_div//in:string htext,Array rows
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		//$this->hint=new dom_statictext;
		editor_generic::addeditor('vb',new editor_valbutton);
		$this->hint=$this->editors['vb'];
		
		$this->hidden=new dom_div;
		$this->tbl=new dom_table;
		$this->tr=new dom_tr;
		$this->td=new dom_td;
		$this->txt=new dom_statictext;
		$this->append_child($this->hint);
		$this->append_child(
			$this->hidden->append_child(
				$this->tbl->append_child(
					$this->tr->append_child(
						$this->td->append_child(
							$this->txt
			)	)	)	)	);
		$this->css_class=get_class($this);
		
		
	}
	
	function bootstrap()
	{
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
		$this->long_name=editor_generic::long_name();
	}
	
	function html_inner()
	{
		$var=$this->args[$this->context[$this->long_name]['var']];
		//$this->hint->text=$this->htext;
		$this->hint->attributes['value']=$this->htext;
		$this->hint->value=serialize($var);
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
		$this->hint->html();
		$this->hidden->html_head();
		$this->tr->css_style['background']=string_to_color('head');
		$this->tr->css_style['color']=bgcolor_to_color(string_to_color('head'));
		$this->tbl->html_head();
		if(is_array($var))
			foreach($var as $rv)
			{
				$this->tr->css_style['background']=string_to_color($rv,1);
				$this->tr->css_style['color']=bgcolor_to_color($this->tr->css_style['background']);
				
				$this->tr->html_head();
				$this->txt->text=$rv;
				$this->td->html();
				//$this->td->id_alloc();
				$this->tr->html_tail();
				$this->tr->id_alloc();
			}
		
		$this->tbl->html_tail();
		$this->hidden->html_tail();
	}
	
	function handle_event($ev)
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$t=TABLE_META_TREE_TMP;
		}else{//working on real !!
			$t=TABLE_META_TREE;
		}
		if($ev->rem_name=='vb')
		{
			$q=unserialize($_POST['val']);
			if(is_array($q))
			foreach($q as $qw)
			{
				$r=$sql->query($qw);
				if($r!=true)
				{
					$err=$sql->err();
					break;
				}
			}
			if(isset($err))
			{
				print "var s=\$i('".js_escape($ev->context[$ev->long_name]['htmlid'])."')";
				print "s.value='".js_escape($err)."';";
				print "s.disabled=true;";
			}else
				print 'window.location.reload(true);';
			exit;
		}
		editor_generic::handle_event($ev);
	}
	
	function after_build_before_children()
	{
		$this->rootnode->exstyle['div.'.get_class($this).' div']=Array(
			'display'=>'none'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div']=Array(
			'display'=>'block',
			'position'=>'absolute',
			'border'=>'1px grey solid',
			'background'=>'white',
			'margin-left'=>'0.3em'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div table']=Array(
			'border-collapse'=>'collapse'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div table tr td']=Array(
			'border'=>'1px solid grey'
			);
	}
}


class EMTSE_list extends dom_div//in:string htext,Array rows
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->hint=new dom_statictext;
		$this->hidden=new dom_div;
		$this->tbl=new dom_table;
		$this->tr=new dom_tr;
		$this->td=new dom_td;
		$this->txt=new dom_statictext;
		$this->append_child($this->hint);
		$this->append_child(
			$this->hidden->append_child(
				$this->tbl->append_child(
					$this->tr->append_child(
						$this->td->append_child(
							$this->txt
			)	)	)	)	);
		$this->css_class=get_class($this);
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
	}
	function html()
	{
		if(is_array($this->args[$this->context[$this->long_name]['var']]))
			parent::html();
	}
	
	function html_inner()
	{
		$var=$this->args[$this->context[$this->long_name]['var']];
		$this->hint->text=$this->htext;
		$this->hint->html();
		$this->hidden->html_head();
		$this->tr->css_style['background']=string_to_color('head');
		$this->tr->css_style['color']=bgcolor_to_color(string_to_color('head'));
		$this->tbl->html_head();
				$this->tr->html_head();
				$this->txt->text='field';
				$this->td->html();
				$this->td->id_alloc();
				
				$this->txt->text='old';
				$this->td->html();
				$this->td->id_alloc();
				
				$this->txt->text='new';
				$this->td->html();
				$this->td->id_alloc();
				
				$this->tr->html_tail();
				$this->tr->id_alloc();
		if(is_array($var))
			foreach($var as $i =>$rv)
			{
				$this->tr->css_style['background']=string_to_color($i,1);
				$this->tr->css_style['color']=bgcolor_to_color($this->tr->css_style['background']);
				$this->tr->html_head();
				
				$this->txt->text=$i;
				$this->td->html();
				$this->td->id_alloc();
				foreach($rv as $v)
				{
					$this->txt->text=$v;
					$this->td->html();
					$this->td->id_alloc();
				}
				$this->tr->html_tail();
				$this->tr->id_alloc();
			}
		
		$this->tbl->html_tail();
		$this->hidden->html_tail();
	}
	
	function handle_event($ev)
	{
	}
	
	function after_build_before_children()
	{
		$this->rootnode->exstyle['div.'.get_class($this).' div']=Array(
			'display'=>'none'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div']=Array(
			'display'=>'block',
			'position'=>'absolute',
			'border'=>'1px grey solid',
			'background'=>'white',
			'margin-left'=>'0.3em'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div table']=Array(
			'border-collapse'=>'collapse'
			);
		$this->rootnode->exstyle['div.'.get_class($this).':hover div table tr td']=Array(
			'border'=>'1px solid grey'
			);
	}
}


#############################################################################################################################
#############################################################################################################################
##########################################     editor_meta_tree_cmpold
#############################################################################################################################
#############################################################################################################################


class editor_meta_tree_cmpold extends dom_div
{
	function __construct()
	{
		parent::__construct();
		global $ddc_tables;
		$this->etype=get_class($this);
		$this->gen_error=new dom_statictext;
		$this->append_child($this->gen_error);$this->gen_error->text="\n!\n";
		$tbl=new dom_table;
		$this->tbl=$tbl;
		$this->append_child($tbl);
		$this->row=new dom_tr;
		$tbl->append_child($this->row);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('type',new editor_statictext);
		$td->append_child($this->editors['type']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('href',new editor_href);
		$td->append_child($this->editors['href']);
		$this->editors['href']->href='?p='.$_GET['p'].'&id=%s';
		editor_generic::addeditor('id',new editor_statictext);
		$this->editors['href']->append_child($this->editors['id']);
	
		$td=new dom_td;
		$this->row->append_child($td);
		//editor_generic::addeditor('change',new editor_statictext);
		//$td->append_child($this->editors['change']);
		editor_generic::addeditor('name',new editor_statictext);
		$td->append_child($this->editors['name']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('sql_change',new editor_statictext);
		$td->append_child($this->editors['sql_change']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('difference',new EMTSE_list);
		$td->append_child($this->editors['difference']);
		$this->editors['difference']->htext='dif';
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$t=TABLE_META_TREE_TMP;
		}else{
			$t=TABLE_META_TREE;
		}
		$new=new ddc_key;
		$new->attach(TABLE_META_TREE_TMP,$sql);
		$old=new ddc_key;
		$old->attach(TABLE_META_TREE,$sql);
		$diff=$new->gen_changes($old);
		if(is_array($diff))$this->tbl->html_head();

		if(is_array($diff))foreach($diff as $d)
		{
			$this->args['type']=$d->type;
			$this->args['id']=$d->id;
			$this->args['href']='';
			$this->args['name']=$d->descr;
			$this->args['sql_change']=$d->query;
			//$this->args['meta_change']=$d->meta_change;
			unset($this->args['difference']);
			if(isset($d->difference))$this->args['difference']=$d->difference;
			//$this->args['change']=count($d->diff);
			$this->row->css_style['background']='';
			switch($d->type)
			{
				case 'table duplicate':	$this->row->css_style['background']=string_to_color('sql_table',2);break;
				case 'column duplicate':	$this->row->css_style['background']=string_to_color('name',2);break;
				//case '*':	$this->row->css_style['background']='yellow';break;
			}
			$this->row->html();
			$this->row->id_alloc();
		}
		else
		{
			$this->gen_error->text=$diff;
			$this->gen_error->html();
		}
		if(is_array($diff))	$this->tbl->html_tail();
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}





#############################################################################################################################
#############################################################################################################################
##########################################     editor_meta_tree_inh
#############################################################################################################################
#############################################################################################################################


class editor_meta_tree_inh extends dom_div
{
	function __construct()
	{
		parent::__construct();
		global $ddc_tables;
		$this->etype=get_class($this);
		$this->gen_error=new dom_statictext;
		$this->append_child($this->gen_error);$this->gen_error->text="\n!\n";
		$tbl=new dom_table;
		$this->tbl=$tbl;
		$this->append_child($tbl);
		$this->row=new dom_tr;
		$tbl->append_child($this->row);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('type',new editor_statictext);
		$td->append_child($this->editors['type']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('href',new editor_href);
		$td->append_child($this->editors['href']);
		$this->editors['href']->href='?p='.$_GET['p'].'&id=%s';
		editor_generic::addeditor('id',new editor_statictext);
		$this->editors['href']->append_child($this->editors['id']);
	
		$td=new dom_td;
		$this->row->append_child($td);
		//editor_generic::addeditor('change',new editor_statictext);
		//$td->append_child($this->editors['change']);
		editor_generic::addeditor('name',new editor_statictext);
		$td->append_child($this->editors['name']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('change',new editor_statictext);
		$td->append_child($this->editors['change']);
		
		$td=new dom_td;
		$this->row->append_child($td);
		editor_generic::addeditor('apply',new editor_valbutton);
		$td->append_child($this->editors['apply']);
		$this->editors['apply']->attributes['value']='Exec';
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		if(is_array($this->editors))foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		global $sql;
		$r=$sql->fetch1($sql->query("SELECT count(1) FROM `".TABLE_META_TREE_TMP."`"));
		if($r>0)//working on copy
		{
			$t=TABLE_META_TREE_TMP;
			$ins=new query_gen_ext('INSERT UPDATE');
			$ins->into->exprs[]=new sql_column(NULL,$t);
			unset($ins_vars);
		}else{
			$t=TABLE_META_TREE;
		}
		$new=new ddc_key;
		$new->attach(TABLE_META_TREE_TMP,$sql);
		$diff=$new->inheritance_update();
		if(is_array($diff))$this->tbl->html_head();

		if(is_array($diff))foreach($diff as $d)
		{
			$this->args['type']=$d->type;
			$this->args['id']=$d->id;
			$this->args['href']='';
			$this->args['name']=$d->descr;
			$this->args['change']="";
			$this->args['apply']="apply";
			foreach($d->row as $rk => $rv)
			{
				$this->args['change'].=$rk."=".$rv.";";
				if(isset($ins) && $d->type=='+')
				{
					if(isset($ins_vars[$rk]))
					{
						$ins_vars[$rk]->value=$rv;
					}else{
						$ins_vars[$rk]=new sql_immed($rv);
						$ins->set->exprs[]=new sql_expression('=',Array(
							new sql_column(NULL,NULL,$rk),
							$ins_vars[$rk]
						));
					}
				}
			}
			if(isset($ins) && $d->type=='+')
				$this->args['apply']=$ins->result();
			else
				$this->args['apply']="";
			if(isset($ins) && ($d->type=='*q' || $d->type=='-q' || $d->type=='+q') )
				$this->args['apply']=$d->row['query'];
			//$this->args['meta_change']=$d->meta_change;
			//$this->args['change']=count($d->diff);
			$this->row->css_style['background']='';
			switch($d->type)
			{
				case '-':	$this->row->css_style['background']=string_to_color('sql_table',2);break;
				case '+':	$this->row->css_style['background']=string_to_color('name',2);break;
				case '*':	$this->row->css_style['background']='yellow';break;
			}
			$this->row->html();
			$this->row->id_alloc();
		}
		else
		{
			$this->gen_error->text=$diff;
			$this->gen_error->html();
		}
		if(is_array($diff))	$this->tbl->html_tail();
		
	}
	
	function handle_event($ev)
	{
		global $sql;
		switch($ev->rem_name)
		{
		case 'apply':
			//print "alert('".js_escape($_POST['val'])."');";
			if($_POST['val']!='')
				$res=$sql->query($_POST['val']);
			print "alert('(".js_escape($_POST['val']).")=".$res."');window.location.reload(true);";
			break;
		};
		editor_generic::handle_event($ev);
	}
	
}



















$tests_m_array[]='editor_meta_tree';
$tests_m_array[]='editor_meta_tree_diff';

$tests_m_array[]='editor_meta_tree_logerrors';
$tests_m_array[]='editor_meta_tree_sqlerrors';
$tests_m_array[]='editor_meta_tree_cmpold';
$tests_m_array[]='editor_meta_tree_inh';




?>