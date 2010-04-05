<?php
#meta_query.php
class fm_undefined
{
	function __construct()
	{
	}
	
	function add_qg($o,$s)
	{
	}
}

class fm_text_constant
{
	function __construct()
	{
		$this->value='';
		$this->alias='';
		$this->to_variable='';
		$this->invert=0;#
		//$this->static_children=true;
	}
	
	function add_qg($o,$s,$make_alias=NULL)
	{
		$ns=new sql_immed($this->value);
		if(isset($make_alias))
		{
			$ns->alias='a'.$o->alias_c;
			$o->alias_c++;
		}
		$ns->invert=$this->invert;
		$s->exprs[]=$ns;
	}
	
	function text_short()
	{
		return '"'.$this->value.'"';
	}
	
	function text_long()
	{
		return '"'.$this->value.'"';
	}
}

class fm_logical_expression
{
	function __construct()
	{
		$this->operator='=';
		$this->alias='';
		$this->to_variable='';
		$this->invert=0;#
		$this->children=Array();
		//$this->static_children=true;
	}
	
	function get_meta_objects($c)
	{
		foreach($this->children as $child)
		{
			if(method_exists($child,'get_meta_objects'))$child->get_meta_objects($c);
		}
	}
	
	function add_qg($o,$s,$make_alias=NULL)
	{
		$ns=new sql_expression;
		$this->set_qg($o,$ns,$make_alias);
		$s->exprs[]=$ns;
	}
	
	function set_qg($o,$s,$make_alias=NULL)
	{
		$s->operator=$this->operator;
		foreach($this->children as $child)
			$child->add_qg($o,$s);
		$s->invert=$this->invert;
		if(isset($make_alias))
		{
			$s->alias='a'.$o->alias_c;
			$o->alias_c++;
		}
	}
	
	function text_short()
	{
		return 'ex: '.$this->operator.' ;';
	}
	
	function text_long()
	{
		$r='';
		if(is_array($this->children) && count($this->children)>0)
			foreach($this->children as $c)
			{
				if($r !='')$r.=' '.$this->operator.' ';
				$r.=$c->text_long();
			}
		else return $this->text_short();
		return $r;
	}
}

class fm_list
{
	function __construct()
	{
		$this->function='';
		$this->alias='';
		$this->to_variable='';
		$this->children=Array();
		$this->invert=0;
		//$this->static_children=true;
	}
	
	function get_meta_objects($c)
	{
		foreach($this->children as $child)
		{
			if(method_exists($child,'get_meta_objects'))$child->get_meta_objects($c);
		}
	}
	
	function add_qg($o,$s,$make_alias=NULL)
	{
		$ns=new sql_list;
		$this->set_qg($o,$ns,$make_alias);
		$s->exprs[]=$ns;
	}
	
	function set_qg($o,$s,$make_alias=NULL)
	{
		$s->func=$this->function;
		foreach($this->children as $child)
			$child->add_qg($o,$s);
		$s->invert=$this->invert;
		if(isset($make_alias))
		{
			$s->alias='a'.$o->alias_c;
			$o->alias_c++;
		}
	}
	function text_short()
	{
		return 'li: '.$this->function.'(...); ';
	}
	
	function text_long()
	{
		$r='';
		if(is_array($this->children) && count($this->children)>0)
			foreach($this->children as $c)
			{
				if($r !='')$r.=', ';
				$r.=$c->text_long();
			}
		else return $this->text_short();
		return $this->function.'('.$r.')';
	}
}

class fm_logical_group
{
	function __construct()
	{
		$this->operator='AND';
		$this->alias='';
		$this->to_variable='';
		$this->children=Array();
		$this->invert=0;
		//$this->static_children=true;
	}
	
	function get_meta_objects($c)
	{
		foreach($this->children as $child)
		{
			if(method_exists($child,'get_meta_objects'))$child->get_meta_objects($c);
		}
	}
	function add_qg($o,$s,$make_alias=NULL)
	{
		$ns=new sql_expression;
		$this->set_qg($o,$ns,$make_alias);
		$s->exprs[]=$ns;
	}
	
	function set_qg($o,$s,$make_alias=NULL)
	{
		$s->operator=$this->operator;
		foreach($this->children as $child)
			$child->add_qg($o,$s);
		$s->invert=$this->invert;
		if(isset($make_alias))
		{
			$s->alias='a'.$o->alias_c;
			$o->alias_c++;
		}
	}
	
	function text_short()
	{
		return 'lg: '.$this->operator.' ;';
	}
	
	function text_long()
	{
		$r='';
		if(is_array($this->children) && count($this->children)>0)
			foreach($this->children as $c)
			{
				if($r !='')$r.=' '.$this->operator.' ';
				$r.=$c->text_long();
			}
		else return $this->text_short();
		return $r;
	}
}

class fm_meta_object
{
	function __construct()
	{
		$this->path='';
		$this->alias='';
		$this->to_variable='';
		$this->invert=0;#
		//$this->static_children=true;
	}
	
	function get_meta_objects($c)
	{
		$c->meta_object_list[$this->path]=explode('.',$this->path);
	}
	
	function add_qg($o,$s,$make_alias=NULL)
	{
		global $sql;
		$path_e=explode('.',$this->path);
		if(count($path_e)>1)
		{
			$oid=array_pop($path_e);
			$join_id=implode('.',$path_e);
		}else{
			$oid=$this->path;
			$join_id='';
		}
		# Cache names to improve performance
		if(isset($o->name_cache[$this->path]))
		{
			$name=$o->name_cache[$this->path];
		}else{
			$namea=$sql->qa("SELECT name FROM `".$sql->esc(TABLE_META_TREE)."` WHERE id=".$oid);
			$name=$namea[0]['name'];
			$o->name_cache[$this->path]=$name;
		}
		$ns=new sql_column(NULL,$o->join_cache[$join_id],$name);
		$ns->invert=$this->invert;
		if(isset($make_alias))
		{
			$ns->alias='a'.$o->alias_c;
			$o->alias_c++;
		}
		$s->exprs[]=$ns;
	}
	
	function text_short()
	{
		return 'meta: '.$this->path.' ;';
	}
	
	function text_long()
	{
		return $this->text_short();
	}
}

class fm_set_expression
{
	function __construct()
	{
		$this->static_children=true;
		$this->children[0]=new fm_undefined;
		$this->children[1]=new fm_undefined;
	}
	
	function get_meta_objects($c)
	{
		foreach($this->children as $child)
		{
			if(method_exists($child,'get_meta_objects'))$child->get_meta_objects($c);
		}
	}
	
	function add_qg($o,$s,$make_alias=NULL)
	{
		$ns=new sql_expression('=');
		$this->set_qg($o,$ns,$make_alias);
		$s->exprs[]=$ns;
	}
	
	function set_qg($o,$s,$make_alias=NULL)
	{
		if(!isset($this->children[0]))$this->children[0]=new fm_undefined;
		if(!isset($this->children[1]))$this->children[1]=new fm_undefined;
		$this->left=$this->children[0];
		$this->right=$this->children[1];
		$this->left->add_qg($o,$s);
		$this->right->add_qg($o,$s);
		$s->invert=$this->invert;
		if(isset($make_alias))
		{
			$s->alias='a'.$o->alias_c;
			$o->alias_c++;
		}
		unset($this->left);unset($this->right);
	}
	
	function text_short()
	{
		return 'ex: = ;';
	}
	
	function text_long()
	{
		return $this->children[0]." = ".$this->children[1];
	}
}

class fm_limit
{
	function __construct()
	{
		$this->count=0;
		$this->offset=0;
		//$this->static_children=true;
	}
	
	function out()
	{
	}
	
	function add_qg($o,$s,$make_alias=NULL)
	{
		print "Assertion: fm_limit->add_qg called.";
		exit;
	}
	function text_short()
	{
		return 'limit... ;';
	}
	
	function text_long()
	{
		return 'limit... ;';
	}
}


class meta_query_gen
{
	function __construct()
	{
		$this->static_children=true;
		
		//$this->result_def=new fm_list;
		$this->children[0]=new fm_list;
		//$this->update_def=new fm_list;
		$this->children[1]=new fm_list;
		//$this->filter_def=new fm_logical_group;
		$this->children[2]=new fm_logical_group;
		//$this->sort_def=new fm_list;
		$this->children[3]=new fm_list;
		//$this->limit=new fm_limit;
		$this->children[4]=new fm_limit;
		$this->oid=-1;
	}
	
	function add_join($prev_key,$join_key)
	{
		global $sql;
		$alias_j='a'.$this->alias_c;
		$this->alias_c++;
		$rela=$sql->qa("SELECT name,rel FROM `".TABLE_META_TREE."` WHERE  id=".$join_key);
		$rel_id=$rela[0]['rel'];
		$rel_self=$rela[0]['name'];
		$relb=$sql->qa("SELECT name,sql_table FROM `".TABLE_META_TREE."` WHERE id=".$rel_id);
		$rel_tbl=$relb[0]['sql_table'];
		$rel_ext=$relb[0]['name'];
		$relk=$sql->qa("SELECT DISTINCT name FROM `".TABLE_META_TREE."` WHERE sql_keyname='PRIMARY' AND isstored=1 AND sql_table='".$sql->esc($rel_tbl)."'");
		
		$join->type='LEFT OUTER JOIN';
		$join->what=new sql_column(NULL,$rel_tbl,NULL,$alias_j);
		$join->on=new sql_expression('AND');
		$join->on->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,$this->join_cache[$prev_key],$rel_self),
			new sql_column(NULL,$alias_j,$rel_ext)
			));
		if($this->has_atime)
		{
		foreach($relk as $key)
			if($key['name']=='atime')
			{
				$alias_s='a'.$this->alias_c;
				$this->alias_c++;
				$date_not_gt=new sql_subquery();
				$date_not_gt->subquery->from->exprs[]=new sql_column(NULL,$rel_tbl,NULL,$alias_s);
				$date_not_gt->subquery->what->exprs[]=new sql_column(NULL,$alias_s,$key['name']);
				$date_not_gt->subquery->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,$this->join_cache[$prev_key],$rel_self),
					new sql_column(NULL,$alias_s,$rel_ext)
					));
				$date_not_gt->subquery->where->exprs[]=new sql_expression('<=',Array(
					new sql_column(NULL,$alias_s,$key['name']),
					new sql_column(NULL,$this->join_cache[''],'atime')
					));
				$oe=new sql_column(NULL,$alias_s,$key['name']);
				$oe->invert=1;
				$date_not_gt->subquery->order->exprs[]=$oe;
				$date_not_gt->subquery->lim_count=1;
				
				$join->on->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,$alias_j,$key['name']),
					$date_not_gt
					));
			}
		}
		#add the same join for other time columns if there will be any other than atime
		$this->qg->joins->exprs[]=$join;
		if($prev_key != '')$join_key_ex=$prev_key.".".$join_key;
		else $join_key_ex=$join_key;
		$this->join_cache[$join_key_ex]=$alias_j;
	}
	
	function to_show()
	{
		global $sql,$ddc_suffixes;
		$this->result_def=$this->children[0];
		$this->update_def=$this->children[1];
		$this->filter_def=$this->children[2];
		$this->sort_def=$this->children[3];
		$this->limit=$this->children[4];
		if($this->oid==-1)return NULL;
		$this->qg=new query_gen_ext;
		$this->alias_c=0;
		//add 'from' for main table
		$aa=$sql->qa("SELECT sql_table FROM `".TABLE_META_TREE."` WHERE isstored=1 AND parentid=".$this->oid." LIMIT 1");
		$row=$aa[0];
		$this->qg->from->exprs[]=new sql_column(NULL,$row['sql_table'],NULL,'a'.$this->alias_c);
		$this->join_cache['']='a'.$this->alias_c;
		$this->alias_c++;
		//test if there is checkmarks subtable and create a join
		if(preg_match('/sellist/',$row['metatables']))
		{
			$join->type='LEFT OUTER JOIN';
			$join->what=new sql_column(NULL,$row['sql_table'].$ddc_suffixes['sellist'],NULL,'a'.$this->alias_c);
			$keya=$sql->qa("SELECT DISTINCT name FROM `".TABLE_META_TREE."` WHERE sql_keyname='PRIMARY' AND isstored=1 AND sql_table='".$sql->esc($row['sql_table'])."'");
			$join->on=new sql_expression('AND');
			$join->on->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,'a'.$this->alias_c,'preset'),
				new sql_immed('0')
				));
			$join->on->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,'a'.$this->alias_c,'uid'),
				new sql_var('uid')
				));
			foreach($keya as $key)
				$join->on->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,'a'.$this->alias_c,$key['name']),
					new sql_column(NULL,$this->join_cache[''],$key['name']),
					));
			$this->qg->joins->exprs[]=$join;
			$this->join_cache['!']='a'.$this->alias_c;
			$this->alias_c++;
		}
		//create joins
		
		
		$this->meta_object_list=Array();
		$this->result_def->get_meta_objects($this);
		#$this->update_def->get_meta_objects($this);
		$this->filter_def->get_meta_objects($this);
		$this->sort_def->get_meta_objects($this);
		
		$time_check_a=$sql->qa("SELECT DISTINCT name FROM `".TABLE_META_TREE."` WHERE isstored=1 AND sql_table='".$sql->esc($row['sql_table'])."' AND name='atime'");
		if(count($time_check_a)>0)$this->has_atime=1;
		foreach($this->meta_object_list as $path => $path_e)
		{
			$lim_k=count($path_e)-1;
			$join_id='';
			for($k=0 ; $k<$lim_k ; $k++)
			{
				$join_prev=$join_id;
				if($join_id !='')$join_id.='.';
				$join_id.=$path_e[$k];
				if(!isset($this->join_cache[$join_id]))
				{
					$this->add_join($join_prev,$path_e[$k]);
				}
			}
		}
		$this->result_def->set_qg($this,$this->qg->what);
		$this->filter_def->set_qg($this,$this->qg->where);
		$this->sort_def->set_qg($this,$this->qg->order);
		return $this->qg;
	}
	
	function to_update()
	{
	}
	
	function to_delete()
	{
	}
	
	function to_update_preview()
	{
	}
	
	function to_delete_preview()
	{
	}
}





?>