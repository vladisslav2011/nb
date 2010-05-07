<?php

require_once("lib/utils.php");


$idcounter=isset($_SESSION['idcounter'])?intval($_SESSION['idcounter']):0;

class dom_node
{
	public $parentnode=NULL,$nodes=array(),$id=0;
	public $container='';//????? is not required
	//$attributes,$css_class,$css_style
	//$name,$type
	//$rootnode
	//$custom_id,$before_id,$after_id
	//after_build_before_children()
	//after_build_after_children()
	//after_link() - the node should have final values of $parentnode,$rootnode
	
	function __construct()
	{
		global $idcounter;
		$this->id=$idcounter;
		$idcounter++;
	}
	
	function __destruct()
	{
		if(is_array($this->nodes))foreach($this->nodes as $m)$m->parentnode=NULL;
		if(is_array($this->editors))foreach($this->editors as $m)$m->comparent=NULL;
		$this->nodes=NULL;
		$this->editors=NULL;
		$this->rootnode=NULL;
		$this->parentnode=NULL;
		$this->nodes=NULL;
	}
	
	function id_alloc()
	{
		global $idcounter;
		if(isset($this->id))$this->id=$idcounter;
		$idcounter++;
		if(isset($this->nodes))foreach($this->nodes as $n)
			$n->id_alloc();
	}
	
	function id_gen()
	{
		if(isset($this->custom_id))return $this->custom_id;
		if(isset($this->before_id) || isset($this->after_id))return $this->before_id . $this->id . $this->after_id;
		return "c".$this->id;
	}
	
	function main_id()
	{
		if(isset($this->main))return $this->main->id_gen();
		return $this->id_gen();
	}
	
	function for_each_set($var,$val)
	{
		$this->$var=$val;
		if(is_array($this->nodes))foreach($this->nodes as $n)
			$n->for_each_set($var,$val);
	}
	
	function set_root_parent($root,$parent)
	{
		$this->rootnode=$root;
		$this->parentnode=$parent;
		if(isset($this->nodes))foreach($this->nodes as $n)
			$n->set_root_parent($root,$this);
	}
	
	function collect_oids($settings_tool)
	{
		if(isset($this->oid))$settings_tool->add_oid($this->oid);
		if(isset($this->nodes))foreach($this->nodes as $n)
			$n->collect_oids($settings_tool);
	}
	
	function byid($id)
	{
		foreach($this->nodes as $n)
		{
			if($n->id===$id)return $n;
			$v=$n->byid($id);
			if($v != NULL) return $v;
		}
		return NULL;
	}
	
	function by_all($depth,$name,$type)
	{
		$results=Array();
		$nm=($name == '' || (isset($this->name) && $this->name==$name));
		$tm=($type == '' || (isset($this->type) && $this->type==$type));
		if($nm && $tm) $results[]=$this;
		foreach($this->nodes as $n)
		{
			$nm=($name == '' || (isset($n->name) && $n->name==$name));
			$tm=($type == '' || (isset($n->type) && $n->type==$type));
			if($nm && $tm) $results[]=$n;
			if(! isset($depth))
			{
				$v=$n->by_all(NULL,$name,$type);
			}else if ($depth>0)
			{
				$v=$n->by_all($depth-1,$name,$type);
			}
			if(is_array($v))
			{
				foreach($v as $e)$results[]=$e;
				unset($v);
			}
		}
		return $results;
	}
	

	function remove_child($node)
	{
		if (is_object($node))
		{
			//maybe $node is real node ref
			foreach($this->nodes as $n)
			{
				if($n!=$node) $new[]=$n;
				if($n==$node) $n->set_root_parent(NULL,NULL);
			}
			$this->nodes=$new;
			return;
		}
		//maybe $node is id string
		foreach($this->nodes as $n)
		{
			if($n->selftype!=$node) $new[]=$n;
			if($n==$node) $n->set_root_parent(NULL,NULL);
		}
		$this->nodes=$new;
		return;
	}
	
	function insert_before($before,$node)
	{
		if (is_object($node) && is_object($before))
		{
			//maybe $node is real node ref
			$node->set_root_parent($this->rootnode,$this);
			$found=false;
			foreach($this->nodes as $n)
			{
				if($n===$before)
				{
					$new[]=$node;
					$found=true;
					if(method_exists($node,'afterlink'))$node->afterlink();
				}
				$new[]=$n;
			}
			$this->nodes=$new;
			if(! $found ) $this->append_child($node);
			return;
		}
	}
	
	function append_child($node)
	{
		/*
		if($a=$this->byid($node->id) != NULL)
		{
			$a->parentnode->remove_child($a);
		}
		*/
		//if(!is_object($node))print_r($node);
		$this->nodes[]=$node;
		$node->set_root_parent($this->rootnode,$this);
		if(method_exists($node,'afterlink'))$node->afterlink();
		return $this;
	}
	
	function css_tostring()
	{
		if(isset($this->css_style))
			if(is_array($this->css_style))
			{
				$tmps='';
				foreach($this->css_style as $sel => $tx)$tmps .= "$sel:$tx;";
				$this->css_style=$tmps;
			}
	}
	
	function css_toarray()
	{
		if(is_array($this->css_style))return;
		if( ! isset($this->css_style))return;
		$styles_raw=explode(';',$this->css_style);
		foreach($styles_raw as $st)
		{
			$selector=preg_replace('/:.*/','',$st);
			$val=preg_replace('/^[^:].*:/','',$st);
			if($selector=='')continue;
			if($val=='')continue;
			$res[$selector]=$val;
		}
		$this->css_style=$res;
	}
	
	function common_attributes()
	{
		$res='';
		if(isset($this->id))$res=" id='".$this->id_gen()."'";
		if(isset($this->css_class))
			$res.=" class='".(is_array($this->css_class)?implode(' ',$this->css_class):$this->css_class)."'";
		if(isset($this->css_style))
			if(is_array($this->css_style))
			{
				$tmps='';
				foreach($this->css_style as $sel => $tx)$tmps.="$sel:$tx;";
				$res.=" style='".$tmps."'";
			}else
				$res.=" style='".$this->css_style."'";
		if(isset($this->attributes) && is_array($this->attributes))
			foreach($this->attributes as $a => $v) $res .= " ".$a."='".htmlspecialchars($v,ENT_QUOTES)."'";
			///////escaping convention????
		return $res;
	}
	
	function after_build()
	{
		if(method_exists($this,'after_build_before_children'))$this->after_build_before_children();
		foreach($this->nodes as $n)
		{
			if(method_exists($n,'after_build'))$n->after_build();
		}
		if(method_exists($this,'after_build_after_children'))$this->after_build_after_children();
	}
	
}


##########################################################################################################
#####################################         dom_root        ############################################
##########################################################################################################

class dom_root extends dom_node
{
	public $encoding,$scripts,$styles;
	public $title;
	public $out_buffer='';

/*
css_class
css_style
scripts
inlinescripts
inlinestyles
exstyle
endscripts
settings_array[oid][setting]
*/
	function html()
	{
		$this->out_buffer='';
		$this->out("<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"    \"http://www.w3.org/TR/html4/loose.dtd\"><html><head>\n");
		if(isset($this->encoding) && $this->encoding != '')
			$this->out("<meta http-equiv=content-type content=\"text/html; charset=".$this->encoding."\">\n");
		else
			$this->out("<meta http-equiv=content-type content=\"text/html; charset=UTF-8\">\n");
		$this->out('<title>'.htmlspecialchars($this->title)."</title>\n");
		if(isset($this->scripts) && is_array($this->scripts))
			foreach($this->scripts as $e) $this->out("<script type='text/javascript' src='$e'></script>\n");
		if(isset($this->inlinescripts) && is_array($this->inlinescripts))
			foreach($this->inlinescripts as $e) $this->out("<script type='text/javascript'><!--\n".$e."\n--></script>\n");
		if(isset($this->styles) && is_array($this->styles))
			foreach($this->styles as $e) $this->out("<link rel=\"stylesheet\" href='$e' type='text/css'>\n");
		if(isset($this->inlinestyles) && is_array($this->inlinestyles))
			foreach($this->inlinestyles as $e) $this->out("<style type='text/css'>".$e."</style>\n");
		if(isset($this->exstyle) && is_array($this->exstyle))
		{
			$this->out("<style type='text/css'>\n");
			foreach($this->exstyle as $si=>$se)//selector
			{
			 $this->out($si." {\n");
			 foreach($se as $ai=>$ae)//attribute
			 	$this->out($ai.' : '.$ae." ;\n");
			 $this->out("}\n");
			}
			$this->out("</style>\n");
		}
		$this->out("</head>\n");
		$this->out("<body");
		$this->out($this->common_attributes());
		$this->out('>');
		foreach($this->nodes as $node)$node->html();
		$this->id_alloc();
		$this->out("<script type='text/javascript'><!--\nlast_generated_id=".$this->id.";\n--></script>\n");
		if(isset($this->endscripts) && is_array($this->endscripts))
			foreach($this->endscripts as $e) $this->out("<script type='text/javascript'><!--\n".$e."\n--></script>\n");
		$this->out("</body></html>");
		return $this->endout();
	}
	
	function __construct()
	{
		dom_node::__construct();
		$this->rootnode=$this;
		$this->out_buffer='';
	}
	
	function setting_val($oid,$setting,$defval)
	{
		global $sql;
		
		if(! is_array($this->settings_array))$qod=true;
		elseif(! is_array($this->settings_array[$oid]))$qod=true;
		elseif(! isset($this->settings_array[$oid][$setting]))$qod=true;
		else  return $this->settings_array[$oid][$setting];
		$settings_tool=new settings_tool;
		$res=$sql->q1($settings_tool->single_query($oid,$setting,$_SESSION['uid'],0));
		if(!isset($res))return $defval;
		else return $res;
	}
	
	function out($s)
	{
		$this->out_buffer.=$s;
	}
	
	function endout()
	{
		return $this->out_buffer;
	}
}
#---------------------------------------------------------------------------------------------------------
class dom_root_print extends dom_root
{
	function out($s)
	{
		print $s;//."\n";
	}
}
#---------------------------------------------------------------------------------------------------------
class dom_root_reload extends dom_root
{
	function html()
	{
		$this->scripts_out='';
		$this->out_buffer='';
		if(isset($this->inlinescripts) && is_array($this->inlinescripts))
			foreach($this->inlinescripts as $e) $this->scripts_out .= $e.";\n";
		
		foreach($this->nodes as $node)$node->html();
		if(isset($this->endscripts) && is_array($this->endscripts))
			foreach($this->endscripts as $e) $this->scripts_out .= $e.";\n";
		return $this->endout();
	}
	
	function firstinner($mname=NULL)
	{
		$this->scripts_out='';
		$this->out_buffer='';
		if(isset($this->inlinescripts) && is_array($this->inlinescripts))
			foreach($this->inlinescripts as $e) $this->scripts_out .= $e.";\n";
		
		foreach($this->nodes as $node)
		{
			if(isset($mname))
				$node->$mname();
			else $node->html_inner();
			break;
		}
		if(isset($this->endscripts) && is_array($this->endscripts))
			foreach($this->endscripts as $e) $this->scripts_out .= $e.";\n";
		return $this->endout();
	}
	
	function out($s)
	{
		print js_escape($s);
	}
	
	function endout()
	{
		global $idcounter;
		print "';".'last_generated_id='.$idcounter.';'.$this->scripts_out;
	}

}

#---------------------------------------------------------------------------------------------------------
#------------------------------------        /dom_root        --------------------------------------------
#---------------------------------------------------------------------------------------------------------



















class dom_div extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
	}
}

class dom_any extends dom_node
{
	
	function __construct($node_name=NULL)
	{
		dom_node::__construct();
		if(isset($node_name))$this->node_name=$node_name;
	}
	
	function html_head()
	{
		$this->rootnode->out('<'.$this->node_name);
		$this->rootnode->out($this->common_attributes());
		$this->rootnode->out(">");
	}
	
	function html_tail()
	{
		$this->rootnode->out("</".$this->node_name.">");
	}
	
	function html_inner()
	{
		if(is_array($this->nodes))
		{
			reset($this->nodes);
			foreach($this->nodes as $node)$this->rootnode->out($node->html());
		}
	}
	
	function html()
	{
		if(! isset($this->node_name) || $this->node_name == '') die('Trying to insert dom_any with unset node_name'.$this->id);
		$this->html_head();
		$this->html_inner();
		$this->html_tail();
//		return $res;
	}
}

class dom_any_noterm extends dom_any
{
	function html()
	{
		if(! isset($this->node_name) || $this->node_name == '') die('Trying to insert dom_any_noterm with unset node_name');
		$this->html_head();
	}
}

class dom_void extends dom_any
{
	function html()
	{
		$this->html_inner();
	}
}



class dom_span extends dom_any
{
	function __construct()
	{
		dom_any::__construct('span');
	}
}


class dom_ol extends dom_any
{
	function __construct()
	{
		dom_any::__construct('ol');
	}
}

class dom_li extends dom_any
{
	function __construct()
	{
		dom_any::__construct('li');
	}
}


class dom_ul extends dom_any
{
	function __construct()
	{
		dom_any::__construct('ul');
	}
}



class dom_statictext extends dom_node
{
	public $text;
	
	function __construct($text='')
	{
		parent::__construct();
		if($text != '')$this->text=$text;
	}
	
	function html()
	{
		$this->rootnode->out(htmlspecialchars($this->text));
	}
}

class dom_statictext_nonempty extends dom_node
{
	public $text;
	
	function __construct($text='')
	{
		parent::__construct();
		if($text != '')$this->text=$text;
	}
	
	function html()
	{
		$this->rootnode->out('&nbsp;'.htmlspecialchars($this->text));
	}
}

class dom_statichtml extends dom_node
{
	public $text;
	function html()
	{
		$this->rootnode->out($this->text);
	}
}


class dom_input extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
	}
}

class dom_textinput extends dom_input
{
	function __construct()
	{
		parent::__construct();
		$this->attributes['type']='text';
	}
}



class dom_textbutton extends dom_any_noterm
{
	function __construct($val='')
	{
		dom_any_noterm::__construct('input');
		$this->attributes['type']='button';
		if($val !='')$this->attributes['value']=$val;
	}
}


class dom_imgbutton extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->attributes['type']='image';
	}
}


class dom_checkbox extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->attributes['type']='checkbox';
	}
}


class dom_select extends dom_any
{
	function __construct()
	{
		dom_any::__construct('select');
	}
}

class dom_select_option extends dom_any
{
	function __construct()
	{
		dom_any::__construct('option');
	}
}

class dom_table extends dom_any
{
	function __construct()
	{
		dom_any::__construct('table');
	}
	function html_tail()
	{
		if(count($this->nodes)==0)$this->rootnode->out('<tr style="display:none"><td  style="display:none"></td></tr>');
		parent::html_tail();
	}
}

class dom_table_x extends dom_table
{
	function __construct($width,$height)
	{
		parent::__construct();
		for($y=0;$y<$height;$y++)
		{
			$row=new dom_tr;
			$this->append_child($row);
			for($x=0;$x<$width;$x++)
			{
				$cell=new dom_td;
				$this->cells[$y][$x]=$cell;
				$row->append_child($cell);
			}
		}
	}
}

class dom_tr extends dom_any
{
	function __construct()
	{
		dom_any::__construct('tr');
	}
}

class dom_td extends dom_any
{
	function __construct()
	{
		dom_any::__construct('td');
	}
}

class dom_th extends dom_any
{
	function __construct()
	{
		dom_any::__construct('th');
	}
}

class dom_js extends dom_any
{
	function __construct($script=NULL)
	{
		dom_any::__construct('script');
		if(isset($script))$this->script=$script;
	}
	function html()
	{
		$this->rootnode->out("<script type='text/javascript'><!--\n".$this->script."\n--></script>");
	}
}

class dom_style extends dom_any
{
	function __construct()
	{
		parent::__construct('style');
		$this->attributes['type']='text/css';
	}
	
	function html_inner()
	{
		if(isset($this->exstyle) && is_array($this->exstyle))
		{
			foreach($this->exstyle as $si=>$se)//selector
			{
			 $this->rootnode->out($si." {\n");
			 foreach($se as $ai=>$ae)//attribute
			 	$this->rootnode->out($ai.' : '.$ae." ;\n");
			 $this->rootnode->out("}\n");
			}
		}
	}
}






?>