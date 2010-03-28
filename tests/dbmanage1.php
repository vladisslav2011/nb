<?php
session_start();
$_SESSION['sql_design']=false;
$profiler=microtime(true);
define('FILE_PLACEMENT', '..');
require_once(FILE_PLACEMENT.'/lib/ddc_meta.php');
require_once(FILE_PLACEMENT.'/lib/dom.php');
require_once(FILE_PLACEMENT.'/lib/settings.php');
require_once(FILE_PLACEMENT.'/lib/commctrls.php');
$sql->logquerys=true;

if($_SESSION['sql_design']==true)
{
	ddc_gentable_n('%tree_selections',
		Array(
		//requred by tree
		Array('id','bigint(20)',0,NULL,'auto_increment',NULL),
		Array('uid','bigint(20)',0,NULL,NULL,NULL),
		Array('folded','tinyint(1)',0,0,NULL,NULL),
		Array('selected','tinyint(1)',0,0,NULL,NULL)//,
	//	Array('','',1,NULL,NULL,NULL),
	)
	,
	Array(
		Array('PRIMARY','id',NULL),
		Array('PRIMARY','uid',NULL)//,
	)
	,$sql);
	
	
	ddc_gentable_n('%tree_tmp',
		$ddc_tree_structure
	,
		$ddc_tree_structure_keys
	,$sql);
}
$res=$sql->query("SELECT COUNT(id) FROM `%tree_tmp`");
$a=($res)?$sql->fetch1($res):0;
if(isset($a) && $a>0)
{
	$workingtable_old="%tree";
	$workingtable="%tree_tmp";
	$workingreadonly=false;
}else{
	$workingtable_old="%tree";
	$workingtable="%tree";
	$workingreadonly=true;
}
unset($a);




class dom_meta_treeview extends dom_div
{
	
/*	function id_gen()
	{
		return "c".$this->id.'resize_style';
	}
*/	
	function __construct()
	{
		dom_div::__construct();
		$this->custom_id='dom_meta_treeview_resize_style';
		/*
		$this->css_style['border']='4px solid black';
		$this->css_style['position']='fixed';
		$this->css_style['overflow']='hidden';
		//$this->css_style['left']='5px';
		//$this->css_style['top']='5px';
		$this->css_style['width']='400px';
		$this->css_style['height']='500px';
		$this->css_style['cursor']='';
		//$this->attributes['onmouseup']="alert('up');";
		*/
		
		$this->css_class='left';
		$this->controls_div=new dom_div;
		//$this->controls_div->css_style['position']='absolute';
		//$this->controls_div->css_style['top']='-10px';
		$this->controls_div->css_style['height']='22px';
		$this->controls_div->css_style['overflow']='hidden';
		$this->controls_div->css_style['background-color']='#F0FFFF';
		$this->append_child($this->controls_div);
		
		$this->editors['enteredit_btn']=new dom_meta_edit_button;
		$this->editors['enteredit_btn']->bindval='treeview';
		$this->editors['enteredit_btn']->value='Edit';
		$this->editors['enteredit_btn']->attributes['value']='Edit';
		$this->controls_div->append_child($this->editors['enteredit_btn']);
		
		$this->editors['canceledit_btn']=new dom_meta_edit_button;
		$this->editors['canceledit_btn']->bindval='treeview';
		$this->editors['canceledit_btn']->value='Cancel';
		$this->editors['canceledit_btn']->attributes['value']='Cancel';
		$this->controls_div->append_child($this->editors['canceledit_btn']);
		
		$this->editors['save_btn']=new dom_meta_edit_button;
		$this->editors['save_btn']->bindval='treeview';
		$this->editors['save_btn']->value='Save';
		$this->editors['save_btn']->attributes['value']='Save';
		$this->controls_div->append_child($this->editors['save_btn']);
		
		$this->editors['saveexit_btn']=new dom_meta_edit_button;
		$this->editors['saveexit_btn']->bindval='treeview';
		$this->editors['saveexit_btn']->value='Saveexit';
		$this->editors['saveexit_btn']->attributes['value']='Save/exit';
		$this->controls_div->append_child($this->editors['saveexit_btn']);
		
		$this->editors['del_btn']=new dom_meta_edit_button;
		$this->editors['del_btn']->bindval='treeview';
		$this->editors['del_btn']->value='del';
		$this->editors['del_btn']->attributes['value']='Del';
		$this->controls_div->append_child($this->editors['del_btn']);
		
		
		$this->tree_div=new dom_meta_treeview_tree;
		$this->append_child($this->tree_div);
	}
	
	function html()
	{
		global $workingtable,$workingtable_old;
		/*
		if($workingtable==$workingtable_old)$this->canceledit_btn->attributes['disabled']='disabled';
		if($workingtable==$workingtable_old)$this->save_btn->attributes['disabled']='disabled';
		if($workingtable==$workingtable_old)$this->saveexit_btn->attributes['disabled']='disabled';
		if($workingtable!=$workingtable_old)$this->enteredit_btn->attributes['disabled']='disabled';
		*/
		if($workingtable==$workingtable_old)$this->canceledit_btn->	css_style['display']='none';
		if($workingtable==$workingtable_old)$this->save_btn->		css_style['display']='none';
		if($workingtable==$workingtable_old)$this->saveexit_btn->	css_style['display']='none';
		if($workingtable!=$workingtable_old)$this->enteredit_btn->	css_style['display']='none';
		foreach($this->editors as $e)$e->bootstrap();
		
		$this->attributes['onmouseup']="if(resizer.obj){ save_setting_value('".$this->oid."','tree_width',resizer.obj.clientWidth);";
		$this->attributes['onmouseup'].="save_setting_value('".$this->oid."','tree_height',resizer.obj.clientHeight);";
		$this->attributes['onmouseup'].="}";
		//$this->tree_div->oid=$this->oid;
		return dom_div::html();
	}
	
	
	function after_build_before_children()
	{
		$this->rootnode->scripts['settings.js']='../settings/settings.js';
		$this->rootnode->scripts['core.js']='../js/core.js';

	}
}


class dom_mark_t extends dom_checkbox
{
	function bootstrap($row)
	{
	$this->id=$row["id"];
	if($row["selected"]==1) $this->attributes["checked"]="checked"; else unset($this->attributes["checked"]);
	$this->attributes["onchange"]='t_cb_change('.$this->id.',this);';
	}
}

class dom_fuf_t extends dom_any
{
	function __construct()
	{
		dom_any::__construct('a');
		$this->attributes['href']='#';
		$this->textnode=new dom_statictext;
		$this->append_child($this->textnode);
	}
	
	function bootstrap($row)
	{
		$this->id=$row["id"];
		$this->textnode->text=(($row["folded"]==1)?"[+]":"[-]");
		$this->attributes["onclick"]="foldunfold('".$this->id_gen()."',".$this->id.");return false;";
	}
}

class dom_edit_t extends dom_fuf_t
{
	function bootstrap($row)
	{
		$this->id=$row["id"];
		unset($this->css_style['background-color']);
		if($row['type']=='table_def')$this->css_style['background-color']='#FFFFE0';
		$this->textnode->text=$row["name"].">";
		$this->attributes["onclick"]="if(window.custom_func)".
		"{window.custom_func('".$this->id."');} else ". "chse.send_or_push({uri:'?push=".$this->id."',static:'last_generated_id=' + last_generated_id + '&amp;dummy',val:'dummy'});return false;";
	}
}

class dom_pshow_t extends dom_any
{
	function __construct()
	{
		dom_any::__construct('span');
		$this->a=new dom_any('a');
		$this->a->attributes['href']='#';
		$this->a->attributes['onclick']='return false;';
		$this->append_child($this->a);
		
		$this->textnode=new dom_statictext;
		$this->textnode->text='?';
		$this->a->append_child($this->textnode);
		
		$this->div=new dom_div;
		$this->append_child($this->div);
		$this->div->css_style['display']='none';
		$this->div->css_style['position']='absolute';
		
	}
	
	
	function bootstrap($row)
	{
		//$this->attributes["onmouseover"]="foldunfold('".$this->id_gen()."',".$this->id.");return false;";
		
		$this->id_alloc();
		$send='';
		$send.="ret_htmlid=".urlencode($this->div->id_gen())."&";
		$send.=urlencode('keys[id]').'='.urlencode($row['id']);
		$send.="&var=dom_pshow_t";
		$send.="&htmlid=' + this.id + '";
		$send.="&last_generated_id=' + last_generated_id + '";
		$send.="&val";
		
		
	
		$this->div->attributes['onmouseover']=
		$this->a->attributes['onclick']=
		"clearTimeout(\$i('".$this->id_gen()."').timeout); var d=\$i('".$this->div->id_gen()."');".
//		"d.innerHTML='preved';d.style.display='block';".
//		"alert(d.style.display != 'block');".
		"if(d.style.display != 'block'){".
		"chse.send_or_push({uri:'?cb=3',static:'$send',val:''});".
		"};".
		"d.style.display='block';return false;".
		"";
		$this->div->attributes['onmouseout']=
		$this->a->attributes['onmouseout']=
		"if(\$i('".$this->id_gen()."'))\$i('".$this->id_gen()."').timeout=setTimeout(\"var v=\$i('".$this->div->id_gen()."'); if(v)v.style.display='none';\",300);";
	
	}
}

class dom_pshow_t_ret extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->tbl=new dom_table;
		$this->tbl->css_style['font-size']='9px';
		$this->tbl->css_style['background-color']='white';
		$this->tbl->css_style['border']='1px solid gray';
		$this->append_child($this->tbl);
	}
	
	function addoption($var,$val)
	{
		$o=new dom_tr;
		$o->css_style['background-color']=($this->rowcnt)?'#FFF0FF':'#F0FFF0';
		
		$td=new dom_td;
		$o->append_child($td);
		
		$t=new dom_statictext;
		$td->append_child($t);
		$t->text=$var;
		
		$td=new dom_td;
		//$td->css_style['background-color']='yellow';
		$o->append_child($td);
		
		$t=new dom_statictext;
		$td->append_child($t);
		$t->text=$val;
		
		$this->tbl->append_child($o);
		$this->rowcnt= !$this->rowcnt;
	}
	
}

class dom_treeview_children_div extends dom_div
{
	function html_inner()
	{
		$this->rootnode->out($this->treeview->child_nodes_of($this->id,1));
	
	}
}



class dom_meta_treeview_tree extends dom_div
{
	
	
	function __construct()
	{
		dom_div::__construct();
		$this->custom_id='meta_treeview_tree';
		
		$this->node_div=new dom_div;
		$this->node_div->css_style['padding-left']='1em';
		$this->append_child($this->node_div);
		$this->controls_div=new dom_div;
		$this->node_div->append_child($this->controls_div);
		$this->children_div=new dom_treeview_children_div;
		$this->children_div->treeview=$this;
		$this->children_div->css_style['display']='block';
		$this->children_div->before_id='t_r';
		$this->node_div->append_child($this->children_div);
		
		
		$this->mark_t=new dom_mark_t;
		$this->mark_t->before_id="t_cb";
		$this->editors['mark_t']=$this->mark_t;
		$this->controls_div->append_child($this->mark_t);
		
		$this->fuf_t=new dom_fuf_t;
		$this->fuf_t->before_id="t_fuf";
		$this->editors['fuf_t']=$this->fuf_t;
		$this->controls_div->append_child($this->fuf_t);

		$this->edit_t=new dom_edit_t;
		$this->edit_t->before_id="t_edit";
		$this->editors['edit_t']=$this->edit_t;
		$this->controls_div->append_child($this->edit_t);
		
		$edit_t=new dom_pshow_t;
		$this->editors['pshow_t']=$edit_t;
		$this->controls_div->append_child($edit_t);
		
	}
	
	function html()
	{
		$this->attributes['onscroll']=
			"if(this.savetimeout)clearTimeout(this.savetimeout);".
			"this.savetimeout=setTimeout(\"".
			"save_setting_value('".$this->oid."','tree_scroll_x',\" + this.scrollLeft + \");".
			"save_setting_value('".$this->oid."','tree_scroll_y',\" + this.scrollTop + \");".
			"\",1000);".
			"";
		//unset($this->attributes['onscroll']);
		$this->css_style['margin']="-1px";
		//$this->css_style['height']="100%";
		//$this->css_style['height']="auto";
		$this->css_style['width']="100%";
		$this->css_style['position']="absolute";
		$this->css_style['top']="22px";
		$this->css_style['bottom']="0px";
		$this->css_style['overflow']="auto";
		$this->rootnode->endscripts['tree_scroll_settings']=
		"\$i('".$this->id_gen()."').scrollLeft=".
			$this->rootnode->setting_val($this->oid,'tree_scroll_x','0').";".
			"\$i('".$this->id_gen()."').scrollTop=".
			$this->rootnode->setting_val($this->oid,'tree_scroll_y','0').";".
			"";
		//$this->rootnode->out($this->rootnode->setting_val($this->oid,'tree_scroll_y',''));
		dom_div::html();
	}
	
	function html_inner()
	{
		$this->active_id=$this->rootnode->setting_val($this->oid,'tree_active','');
		$this->rootnode->out($this->child_nodes_of(0,0));
	}
	
	function child_nodes_of($parent,$level)
	{
		global $workingtable,$workingtable_old;
		global $sql;
		$res='';
		
		
		
		$sres=$sql->query("SELECT a.*,b.selected,b.folded FROM `".$sql->esc($workingtable)."` as a LEFT JOIN `%tree_selections` as b ON a.id=b.id AND b.uid='".$_SESSION['uid']."' WHERE a.parentid=$parent ");//testing only
		while($row=$sql->fetcha($sres))
		{
			foreach($this->editors as $a)$a->bootstrap($row);
			$this->children_div->css_style['display']=($row['folded']==1)?"none":"block";
			$this->children_div->id=$row['id'];
			if($row['id']==$this->active_id)$this->node_div->css_style['background-color']='#FFD0D0';
			else $this->node_div->css_style['background-color']='white';
			$this->node_div->html();
		}
		$sql->free($sres);
		return $res;
	}
	
}






class dom_meta_att_view extends dom_any
{
	function __construct()
	{
		global $ddc_tree_structure;
		dom_any::__construct('table');
		$this->workingtable='%tree';
		$this->css_style['width']='100%';
		$this->css_style['height']='100%';
		$this->refresh_list=Array();
		$add_here=new dom_meta_edit_button;
		$add_here->bindval='treeview';
		$add_here->value='add here';
		$add_here->attributes['value']='add here';
		$add_child=new dom_meta_edit_button;
		$add_child->bindval='treeview';
		$add_child->value='add child';
		$add_child->attributes['value']='add child';
		$this->editors[]=$add_here;
		$this->editors[]=$add_child;
		$this->add_row($add_here,$add_child);
		foreach($ddc_tree_structure as $s)
		{
			switch($s[0])
			{
				case 'storable':
				case 'calculateable':
				case 'sqlnull':
					$editor=new dom_meta_edit_checkbox;
					break;
				case 'parentid':
					$editor=new dom_meta_edit_text_pick;
					break;
				case 'sqlextra':
					$editor=new dom_meta_edit_text_extra;
					break;
				case 'level':
					$editor=new dom_meta_edit_text_level;
					break;
				case 'type':
					$editor=new dom_meta_edit_text_postback;
					break;
				case '':
				default:
					$editor=new dom_meta_edit_text;
			}
			if($s[0]=='verstamp')$this->refresh_list[]=$editor;
			if($s[0]=='verstamp')$editor->attributes['disabled']='disabled';
			
			$editor->refresh_list = & $this->refresh_list;
			$editor->bindval=$s[0];
			$editor->callback_uri='?cb=1';
			$this->editors[]=$editor;
			$td_l_text=new dom_statictext;
			$td_l_text->text=$s[0];
			$this->add_row($td_l_text,$editor);
		}
	}
	
	function add_row($a,$b)
	{
		$tr=new dom_tr;
		$this->append_child($tr);
		$td_l=new dom_td;
		$tr->append_child($td_l);
		$td_l->append_child($a);
		$td_r=new dom_td;
		$tr->append_child($td_r);
		$td_r->append_child($b);
	}
	
	
	function html()
	{
		global $workingtable,$workingtable_old;
		global $sql;
		$active=$this->rootnode->setting_val($this->oid,'tree_active','');
		if($active != '')
		{
			$this->args=$sql->fetcha($sql->query("SELECT * FROM `".$sql->esc($workingtable)."` WHERE id='".$sql->esc($active)."'"));
			$this->keys['id']=$this->args['id'];
			foreach($this->editors as $e)
			{
				$e->args= &$this->args;
				$e->keys= &$this->keys;
				$e->bootstrap();
			}
			
		}
		dom_any::html();
	}
}


class dom_meta_edit_text extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->bindval='';
		$this->callback_prefix='';
		$this->callback_uri='';
		$this->attributes['type']='text';
		$this->attributes['onmouseover']='opera_fix(this)';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$send=urlencode('keys[id]').'='.urlencode($this->keys['id']);
		$send.="&var=".urlencode($this->bindval);
		$send.="&htmlid=' + this.id + '";
		$send.="&last_generated_id=' + last_generated_id + '";
		$cnt=0;
		if(is_array($this->refresh_list))
			foreach($this->refresh_list as $r)
			{
				$send.="&refresh_list[".$cnt."]=".urlencode($r->id_gen().'/'.$r->bindval);
				$cnt++;
			}
		$send.="&val";
		$this->attributes['onfocus']=$this->onfocus_before."chse.activatemon({obj:this,objtype:'dom_meta_edit_text',static:'$send'});".$this->onfocus_after;
		$this->attributes['onblur']=$this->onblur_before.'chse.latedeactivate(this)'.$this->onblur_after;
		$this->attributes['value']=$this->args[$this->bindval];
	}
}

class dom_meta_edit_text_pick extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->edit=new dom_meta_edit_text;
		$this->append_child($this->edit);
		$this->btn=new dom_textbutton;
		$this->btn->attributes['value']='>';
		$this->btn->attributes['onclick']='';
		$this->append_child($this->btn);
		
	}
	function bootstrap()
	{
		$this->edit->bindval=$this->bindval;
		$this->edit->args=$this->args;
		$this->edit->keys=$this->keys;
		$this->edit->callback_prefix=$this->callback_prefix;
		$this->edit->callback_uri=$this->callback_uri;
		$this->edit->refresh_list=$this->refresh_list;
		$this->btn->attributes['onclick']=
		"window.custom_func=function(a)".
		"{ var t=\$i('".$this->edit->id_gen()."');".
		"t.focus();".
		"t.value=a;".
		"window.custom_func=null;} ;".
		'';
		$this->edit->bootstrap();
	}
}

class dom_meta_edit_text_extra extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->edit=new dom_meta_edit_text;
		$this->append_child($this->edit);
		$this->btn=new dom_textbutton;
		$this->btn->attributes['value']='*';
		$this->btn->attributes['onclick']='';
		$this->append_child($this->btn);
		
	}
	function bootstrap()
	{
		$this->edit->bindval=$this->bindval;
		$this->edit->args=$this->args;
		$this->edit->keys=$this->keys;
		$this->edit->callback_prefix=$this->callback_prefix;
		$this->edit->callback_uri=$this->callback_uri;
		$this->edit->refresh_list=$this->refresh_list;
		$this->btn->attributes['onclick']=
		"var t=\$i('".$this->edit->id_gen()."');".
		"t.focus();".
		"if(t.value != '') t.value='' ; ".
		"else t.value='auto_increment';".
		'';
		$this->edit->bootstrap();
	}
}

class dom_meta_edit_text_level extends dom_div
{
	function suggestoption($val)
	{
		$o=new dom_div;
		$a=new dom_any('a');
		$o->append_child($a);
		$t=new dom_statictext;
		$a->append_child($t);
		$t->text=" = $val = ";
		$a->attributes['href']='#';
		$a->val=$val;
		$this->opts[]=$a;
		return $o;
	}
	function __construct()
	{
		dom_div::__construct();
		$this->edit=new dom_meta_edit_text;
		$this->append_child($this->edit);
		$this->sugg=new dom_div;
		$this->sugg->css_style['border']='1px solid gray';
		$this->sugg->css_style['position']='absolute';
		$this->sugg->css_style['display']='none';
		$this->sugg->css_style['background-color']='white';
		$this->sugg->css_style['text-align']='center';
//		$this->sugg->css_style['max-height']='30px';
		//$this->sugg->css_style['overflow']='auto';
		$this->append_child($this->sugg);
		for($k=0;$k<4;$k++)$this->sugg->append_child($this->suggestoption($k));
		
	}
	function bootstrap()
	{
		$this->edit->bindval=$this->bindval;
		$this->edit->args=$this->args;
		$this->edit->keys=$this->keys;
		$this->edit->callback_prefix=$this->callback_prefix;
		$this->edit->callback_uri=$this->callback_uri;
		$this->edit->refresh_list=$this->refresh_list;
		foreach($this->opts as $o)
		{
			$o->attributes['onclick']=
			"var t=\$i('".$this->edit->id_gen()."');".
			"t.focus();".
			"t.value='".$o->val."';".
			'return false;';
		}
		$this->sugg->attributes['onmouseover']=
		$this->edit->attributes['onmouseover']=
		"clearTimeout(\$i('".$this->id_gen()."').timeout); \$i('".$this->sugg->id_gen()."').style.display='block';";
		$this->sugg->attributes['onmouseout']=
		$this->edit->attributes['onmouseout']=
		"\$i('".$this->id_gen()."').timeout=setTimeout(\"var v=\$i('".$this->sugg->id_gen()."');if(v)v.style.display='none';\",500);";
		$this->edit->bootstrap();
	}
}

class dom_meta_edit_text_postback extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->edit=new dom_meta_edit_text;
		$this->append_child($this->edit);
		$this->sugg=new dom_div;
		$this->sugg->css_style['border']='1px solid gray';
		$this->sugg->css_style['position']='absolute';
		$this->sugg->css_style['display']='none';
		$this->sugg->css_style['background-color']='white';
//		$this->sugg->css_style['text-align']='center';
//		$this->sugg->css_style['max-height']='30px';
		//$this->sugg->css_style['overflow']='auto';
		$this->append_child($this->sugg);
		
	}
	
	function bootstrap()
	{
		$this->edit->bindval=$this->bindval;
		$this->edit->args=$this->args;
		$this->edit->keys=$this->keys;
		$this->edit->callback_prefix=$this->callback_prefix;
		$this->edit->callback_uri=$this->callback_uri;
		$this->edit->refresh_list=$this->refresh_list;
		$send='';
		$send.="ret_htmlid=".urlencode($this->sugg->id_gen())."&";
		$send.="set_htmlid=".urlencode($this->edit->id_gen())."&";
		if(isset($this->keys['id']))$send.=urlencode('keys[id]').'='.urlencode($this->keys['id']);
		if(isset($this->bindval))$send.="&var=".urlencode($this->bindval);
		$send.="&htmlid=' + this.id + '";
		$send.="&last_generated_id=' + last_generated_id + '";
		$send.="&val";
		
		$this->edit->onfocus_after=
		"if(\$i('".$this->id_gen()."').timeout)clearTimeout(\$i('".$this->id_gen()."').timeout);".
		"if(\$i('".$this->sugg->id_gen()."'))\$i('".$this->sugg->id_gen()."').style.display='block';".
		"chse.send_or_push({uri:'?cb=3',static:'$send',val:''});".
		"";
		
		$this->edit->onblur_before="if(\$i('".$this->id_gen()."'))\$i('".$this->id_gen()."').timeout=setTimeout(".
		"\"var v=\$i('".$this->sugg->id_gen()."');if(v)v.style.display='none';\",500);";
		$this->edit->bootstrap();
	}
	
}

class dom_meta_edit_text_postback_ret extends dom_div
{
	function suggestoption($val)
	{
		$o=new dom_div;
		$a=new dom_any('div');
		$o->append_child($a);
		$t=new dom_statictext;
		$a->append_child($t);
		$t->text="\"$val\"";
		//$a->attributes['href']='#';
		$a->css_style['cursor']='hand';
		$a->val=$val;
		$this->opts[]=$a;
		$this->append_child($o);
	}
	
	function html_inner()
	{
		foreach($this->opts as $a)
		{
			$a->attributes['onclick']=
			"var t=\$i('".$this->set_htmlid."');".
			"t.focus();".
			"t.value='".$a->val."';".
			'return false;';
		}
		dom_div::html_inner();
	}
}


class dom_meta_edit_checkbox extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->bindval='';
		$this->callback_prefix='';
		$this->callback_uri='';
		$this->attributes['onchange']='chse.timerch(true);';
		$this->attributes['type']='checkbox';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$send=urlencode('keys[id]').'='.urlencode($this->keys['id']);
		$send.="&var=".urlencode($this->bindval);
		$send.="&htmlid=' + this.id + '";
		$send.="&last_generated_id=' + last_generated_id + '";
		$cnt=0;
		if(is_array($this->refresh_list))
			foreach($this->refresh_list as $r)
			{
				$send.="&refresh_list[".$cnt."]=".urlencode($r->id_gen().'/'.$r->bindval);
				$cnt++;
			}
		$send.="&val";
		$this->attributes['onfocus']="chse.activatemon({obj:this,objtype:'dom_meta_edit_checkbox',static:'$send'});";
		$this->attributes['onblur']='chse.latedeactivate(this);';
		
		if($this->args[$this->bindval]==1)$this->attributes['checked']='checked';
		
	}
}

class dom_meta_edit_button extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->bindval='';
		$this->callback_prefix='';
		$this->callback_uri='';
		$this->attributes['type']='submit';
		$this->css_style['margin']='0px';
		$this->css_style['padding']='0px';
		$this->css_style['font-size']='10px';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$send='';
		if(isset($this->keys['id']))$send.=urlencode('keys[id]').'='.urlencode($this->keys['id']);
		if(isset($this->bindval))$send.="&var=".urlencode($this->bindval);
		$send.="&htmlid=' + this.id + '";
		$send.="&last_generated_id=' + last_generated_id + '";
		$cnt=0;
		if(is_array($this->refresh_list))
			foreach($this->refresh_list as $r)
			{
				$send.="&refresh_list[".$cnt."]=".urlencode($r->id_gen().'/'.$r->bindval);
				$cnt++;
			}
		$send.="&button";
		$value=js_escape($this->value);
		$this->attributes['onclick']="chse.send_or_push({uri:'?cb=2',static:'$send',val:'$value'});";
//		$this->attributes['onfocus']="chse.activatemon({obj:this,objtype:'dom_meta_edit_checkbox',static:'$send'});";
//		$this->attributes['onblur']='chse.latedeactivate(this);';
//		$this->attributes['onchange']='chse.timerch(true);';
		
		//if(isset($this->args) && is_array($this->args) && ! isset($this->value))$this->attributes['value']=$this->args[$this->bindval];
	}
}











class query_log_viewer extends dom_any
{
	function __construct()
	{
		dom_any::__construct('table');
		//$this->css_style['position']='absolute';
		//$this->css_style['left']='250px';
		//$this->css_style['top']='650px';
		$r=new dom_tr;
		$d=new dom_td;
		$d1=new dom_div;
		$d2=new dom_div;
		$this->t1=new dom_statictext;
		$this->t2=new dom_statictext;
		
		$r->append_child($d);
		$d->append_child($d1);
		$d->append_child($d2);
		$d1->append_child($this->t1);
		$d2->append_child($this->t2);
		$this->append_child($r);
	}
	function html_inner()
	{
		global $sql;
		reset($sql->querylog);
		foreach($sql->querylog as $q)
		{
			
			$this->t1->text=$q->q;
			$this->t2->text=$q->e;
			$this->id_alloc();
			dom_any::html_inner();
		}
	}
}








function hiercheck($id,$new)
{
	global $sql,$workingtable;
	if($id==$new)return true;
	$res=$sql->query("SELECT id FROM `".$sql->esc($workingtable)."` WHERE parentid='$id'");
	while($row=$sql->fetcha($res))
	{
		if($row['id']==$new) return true;
		if(hiercheck($row['id'],$new))return true;
	}
	return false;
}


function prevalidate($var,$val,$keys)
{
	global $sql,$workingtable;
	switch($var)
	{
	case 'parentid':
		$vale=$sql->esc($val);
		$exists=$sql->fetch1($sql->query("SELECT id FROM `".$sql->esc($workingtable)."` WHERE id='$vale'"));
		$hier=hiercheck($keys['id'],$val);
		if($hier)return false;
		if($val==0)return true;
		if($exists==$val)return true;
		return false;
	default:
		return true;
	}
}








if($_GET['a']==1)//checkboxes
{
	$id=$_POST['id'];
	$uid=$_SESSION['uid'];
	$val=$_POST['v'];
	$sql->query("INSERT INTO `%tree_selections` SET id=$id , uid=$uid , selected=$val ON DUPLICATE KEY UPDATE selected=$val");
	exit;
}

if($_GET['a']==2)//folding
{
	$id=$_POST['id'];
	$uid=$_SESSION['uid'];
	$val=$_POST['v'];
	$sql->query("INSERT INTO `%tree_selections` SET id=$id , uid=$uid , folded=$val ON DUPLICATE KEY UPDATE folded=$val");
	exit;
}

if($_GET['cb']==3)//dynamic parts
{
	if(isset($_POST['last_generated_id']))$idcounter=$_POST['last_generated_id'];
	switch($_POST['var'])
	{
		case 'type':
			$t=new dom_meta_edit_text_postback_ret;
			$lvl=$sql->fetch1($sql->query("SELECT level FROM `".$sql->esc($workingtable)."` WHERE id=".$_POST['keys']['id']." "));
			if(is_array($ddc_types[$lvl]))foreach($ddc_types[$lvl] as $a)
				$t->suggestoption($a);
			$t->set_htmlid=$_POST['set_htmlid'];
			print "if(\$i('".$_POST['ret_htmlid']."'))\$i('".$_POST['ret_htmlid']."').innerHTML=".reload_object($t,true);
			//print "\$i('".$_POST['ret_htmlid']."').innerHTML='".js_escape($workingtable)."';";
			print "last_generated_id=".$idcounter.";";
			exit;
		case 'dom_pshow_t':
			$t=new dom_pshow_t_ret;
			$res=$sql->query("SELECT * FROM `".$sql->esc($workingtable)."` WHERE id=".$_POST['keys']['id']." ");
			if($row=$sql->fetcha($res))
			{
				foreach($row as $i => $v)$t->addoption($i,$v);
			}
			print "if(\$i('".$_POST['ret_htmlid']."'))\$i('".$_POST['ret_htmlid']."').innerHTML=".reload_object($t,true);
			print "last_generated_id=".$idcounter.";";
			exit;
		default:
			;
	}
	exit;
}


if($_GET['cb']==2)//buttons hardcoded
{
	switch($_POST['button'])
	{
		case 'add here':
			$parent=$sql->fetch1($sql->query("(SELECT parentid FROM `".$sql->esc($workingtable)."` WHERE id=".$_POST['keys']['id']." )"));
			break;
		case 'add child':
			$parent=$_POST['keys']['id'];
			break;
		case 'Edit':
			$sql->query("DROP TABLE `%tree_tmp`");
			ddc_gentable_n('%tree_tmp',
				$ddc_tree_structure
			,
				$ddc_tree_structure_keys
			,$sql);
			$sql->query("INSERT INTO `%tree_tmp` SELECT * FROM `%tree`");
			print "window.location.reload(true);";
			exit;
		case "Cancel":
			$sql->query("DROP TABLE `%tree_tmp`");
			print "window.location.reload(true);";
			exit;
			
		case "Save":
			
			$ddc=new ddc_key;
			$ddc->attach('%tree',$sql);
			$ddc2=new ddc_key;
			$ddc2->attach('%tree_tmp',$sql);
			
			$ddc->gen_changes($ddc2);
			foreach($ddc->actions as $i => $r)
				print "chse.safe_alert(111,'".js_escape($r->q)."');";
			$ddc->apply_changes($sql);
			$sql->query("DROP TABLE `%tree`");
			ddc_gentable_n('%tree',
				$ddc_tree_structure
			,
				$ddc_tree_structure_keys
			,$sql);
			$sql->query("INSERT INTO `%tree` SELECT * FROM `%tree_tmp`");
			

			
			//print "window.location.reload(true);";
			exit;
		case 'del':
			$q="DELETE FROM `".$workingtable."` WHERE (SELECT `%tree_selections`.selected FROM `%tree_selections` WHERE `".$workingtable."`.id=`%tree_selections`.id )=1";
			$sql->query($q);
			//clean up orphans
			$q="DELETE FROM `".$workingtable."` WHERE (SELECT a.id FROM `".$workingtable."` as a WHERE `".$workingtable."`.parentid=a.id ) IS NULL";
			$sql->query($q);
			
			$q="DELETE FROM `%tree_selections` WHERE (SELECT a.id FROM `".$workingtable."` as a WHERE a.id=`%tree_selections`.id ) IS NULL";
			$sql->query($q);
			
			print 'chse.safe_alert(111,\''.js_escape($q)."');";
			print "window.location.reload(true);";
			exit;
			break;
	}
	if($parent=='')$parent=0;
	$q="INSERT INTO `".$sql->esc($workingtable)."` SET parentid=".$parent." , id=0";
	print 'chse.safe_alert(111,\''.js_escape($q)."');";
	$sql->query($q);
	$new=$sql->fetch1($sql->query("SELECT LAST_INSERT_ID()"));
	if(isset($new))
	{
		$settings_tool=new settings_tool;
		$q=$settings_tool->set_query(-1,'tree_active',0,'',$new);
		$sql->query($q);
		print "window.location.reload(true);";
	}
	exit;
}



if($_GET['cb']==1)//change events
{
	$var=$_POST['var'];
	$val=$_POST['val'];
	$q="INSERT INTO `".$sql->esc($workingtable)."` SET ".
	$sql->esc($var)."='".$sql->esc($val).
	"' , id=".$sql->esc($_POST['keys']['id']).
	" ON DUPLICATE KEY UPDATE ".
	$sql->esc($var)."='".$sql->esc($val).
	"'";
	if(! prevalidate($var,$val,$_POST['keys']))
	{
		print "if(\$i('".$_POST['htmlid']."'))\$i('".$_POST['htmlid']."').style.backgroundColor='red';";
		exit;
	}
	
	$sql->query($q);
	if(isset($_POST['last_generated_id']))$idcounter=$_POST['last_generated_id'];
	switch($_POST['var'])
	{
	case 'name':
	case 'id':
	case 'parentid':
		/*$settings_tool=new settings_tool;
		$tr=new dom_root_reload;
		if(isset($_POST['last_generated_id']))$idcounter=$_POST['last_generated_id'];
		$l=new dom_meta_treeview_tree;
		$tr->append_child($l);
		$tr->for_each_set('oid',-1);
		$tr->collect_oids($settings_tool);
		$tr->settings_array=$settings_tool->read_oids($sql);
		$tr->after_build();*/
		//print "\$i('meta_treeview_tree').innerHTML=".$tr->firstinner();
		print "\$i('meta_treeview_tree').innerHTML=".reload_object(new dom_meta_treeview_tree,true);
	default:
		;
	}
	print "if(\$i('".$_POST['htmlid']."'))\$i('".$_POST['htmlid']."').style.backgroundColor='green';";
	print "setTimeout(\"if(\$i('".$_POST['htmlid']."'))\$i('".$_POST['htmlid']."').style.backgroundColor='';\",500);";
	print "last_generated_id=".$idcounter.";";
	if(isset($_POST['refresh_list']))
	{
		foreach($_POST['refresh_list'] as $a)
		{
			$l=explode('/',$a);
			$res=$sql->fetch1($sql->query("SELECT ".$l[1]." FROM `".$sql->esc($workingtable)."` WHERE id=".$sql->esc($_POST['keys']['id'])));
			print "\$i('".$l[0]."').value='".js_escape($res)."';";
		}
	}
//	print "alert('".$_POST['keys']['id']."');";
//	print "alert('".js_escape($q.':'.$sql->err())."');";
	exit;
}



///////////////////reloading tests
if($_GET['push']!='')
{
	$settings_tool=new settings_tool;
	$q=$settings_tool->set_query(-1,'tree_active',0,'',$_GET['push']);
	$sql->query($q);
	print 'chse.safe_alert(111,\''.js_escape($q)."');";
/*	$tr=new dom_root_reload;
	if(isset($_POST['last_generated_id']))$idcounter=$_POST['last_generated_id'];
	$l=new dom_meta_att_view;
	$tr->append_child($l);
	$tr->for_each_set('oid',-1);
	$tr->collect_oids($settings_tool);
	$tr->settings_array=$settings_tool->read_oids($sql);
	$tr->after_build();
	print "\$i('rdiv').innerHTML=".$tr->html();
	*/
	if(isset($_POST['last_generated_id']))$idcounter=$_POST['last_generated_id'];
	print "\$i('rdiv').innerHTML=".reload_object(new dom_meta_att_view);
	print "\$i('meta_treeview_tree').innerHTML=".reload_object(new dom_meta_treeview_tree,true);
	print "last_generated_id=".$idcounter.";";
	exit;

}


function reload_object($obj,$inneronly=false)
{
	global $sql,$idcounter;
	$settings_tool=new settings_tool;
	$tr=new dom_root_reload;
	$tr->append_child($obj);
	$tr->for_each_set('oid',-1);
	$tr->collect_oids($settings_tool);
	$tr->settings_array=$settings_tool->read_oids($sql);
	$tr->after_build();
	//print "\$i('dom_meta_treeview_resize_style').innerHTML=".$tr->html();
	if($inneronly)return $tr->firstinner();
	return $tr->html();
}









//default path
$page=new dom_root_print;
$page->title='dbmanage tests';

$ttab=new dom_tab_control;
$ttab->add_tab('tree','tree');
$ttab->oid='-1';
$ttab->name='dbmanage.tabcontrol';
$page->append_child($ttab);

//some text flood
/*
for($k=0;$k<400;$k++)
{
	$tex=new mazafaker;
	$page->append_child($tex);
}
*/


$leftdiv=new dom_meta_treeview;
//$page->append_child($leftdiv);
$ttab->tabs['tree']->div->append_child($leftdiv);


$tbl=new dom_table;
//$page->append_child($tbl);
$ttab->tabs['tree']->div->append_child($tbl);


$tr=new dom_tr;
$tbl->append_child($tr);
$td=new dom_td;
$tr->append_child($td);
$div_placeholder=new dom_div;
$div_placeholder->css_class='left';
$div_placeholder->css_style['border']='0px none white';
$div_placeholder->css_style['position']='static';
$td->append_child($div_placeholder);
$td=new dom_td;
$tr->append_child($td);


$rdiv=new dom_div;
//$rdiv->css_style['position']='absolute';
//$rdiv->css_style['left']='250px';
$rdiv->custom_id='rdiv';

$right=new dom_meta_att_view;
//$page->append_child($right);
$rdiv->append_child($right);
//$page->append_child($rdiv);
$td->append_child($rdiv);

$querylog=new query_log_viewer;

$ttab->add_tab('querylog','query log');
$ttab->tabs['querylog']->div->append_child($querylog);


$debug=new dom_any;
$debug->custom_id='debug';
$debug->node_name='textarea';
$debug->attributes['rows']=5;
$debug->attributes['cols']=5;
//$debug->css_style['position']='absolute';
//$debug->css_style['top']='1000px';
$debug->css_style['width']='80%';
$debug->css_style['height']='50%';
//$page->append_child($debug);
$ttab->add_tab('debug','debug');
$ttab->tabs['debug']->div->append_child($debug);



$page->inlinescripts[]=<<<aaaa


function foldunfold(id,e)
{
	var s=\$i('t_r' + e).style;
	var o=\$i('t_fuf' +e);
	if(s.display!='none')
	{
		s.display='none';
		o.innerHTML='[+]';
		c=1;
	}else{
		s.display='block';
		o.innerHTML='[-]';
		c=0;
	}
	var data='id=' + encodeURIComponent(e) + '&v=' + c;
	async_post('?a=2',data,function(){});
	
}


function t_cb_change(id,cb)
{
	c= cb.checked ? 1 : 0;
	var data='id=' + encodeURIComponent(id) + '&v=' + c;
	async_post('?a=1',data,function(){});
}




function init()
{
	chse.callback_uri='?cb=1';
	setInterval('chse.timerch(false);',1000);
}

init();



chse.fetchfuncs.push(function (o)
{
	if(o.objtype=='dom_meta_edit_text')
	return function()
	{
		return encodeURIComponent(this.obj.value);
	};
	if(o.objtype=='dom_meta_edit_checkbox')
		return function()
		{
			return this.obj.checked?1:0;
		}
	return null;
}
);

chse.checkerfuncs.push(function (o)
{
	if(o.objtype=='dom_meta_edit_text')
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
	if(o.objtype=='dom_meta_edit_checkbox')
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








$_SESSION['uid']='0';
if($_SESSION['settings_preset']=='')$_SESSION['settings_preset']=0;
$settings_tool=new settings_tool;


$page->for_each_set('oid',-1);
$page->collect_oids($settings_tool);
$page->settings_array=$settings_tool->read_oids($sql);



$tree_width=$page->setting_val(-1,'tree_width','');
$tree_height=$page->setting_val(-1,'tree_height','');


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
width:${tree_width}px;
height:${tree_height}px;
}
.left:hover{
border:4px solid red;
position:fixed;
overflow:hidden;
top:40px;
width:${tree_width}px;
height:${tree_height}px;
}



aaaa;



//$leftdiv->oid=-1;
$page->after_build();
print $page->html();

	echo microtime(true)-$profiler;echo ":".$sql->querytime;
?>