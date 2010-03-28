<?php
class query_select
{


function add_what($what,$alias)
{
	$new->alias=$alias;
	$new->val=$what;
	if(isset($alias) && $alias != '')$this->what_byalias[$alias]=$new;
	$this->what[]=$new;
}

function add_from($what,$alias)
{
	$new->alias=$alias;
	$new->val=$what;
	if(isset($alias) && $alias != '')$this->from_byalias[$alias]=$new;
	$this->from[]=$new;
}

function add_where($what)
{
	$this->where[]=$what;
}

function set_limit($cnt,$ofs)
{
	$this->lim_count=$cnt;
	$this->lim_offset=$ofs;
}

function add_having($what)
{
	$this->having[]=$what;
}

function add_group($what)
{
	$this->group[]=$what;
}

function add_order($what,$dir)
{
	$new->val=$what;
	$new->dir=$dir;
	$this->order[]=$new;
}

function union_with($what)
{
	$this->unions[]=$what;
}

function alias_of_what($what)
{
	if(isset($this->what_byalias) && is_array($this->what_byalias))
	{
		foreach($this->what_byalias as $a -> $e)
			if($e->val == $what) return $a;
	}else{
		return null;
	};
	
}

function alias_of_from($what)
{
	if(isset($this->from_byalias) && is_array($this->from_byalias))
	{
		foreach($this->from_byalias as $a -> $e)
			if($e->val == $what) return $a;
	}else{
		return null;
	};

}

function clear()
{
	unset($this->what);
	unset($this->what_byalias);
	unset($this->from);
	unset($this->from_byalias);
	unset($this->where);
	unset($this->having);
	unset($this->group);
	unset($this->order);
	unset($this->unions);
	unset($this->lim_count);
	unset($this->lim_offset);
//	unset($this->);
}


function result()
{
	$res='';
	$what_part='';
	if(isset($this->what) && is_array($this->what))foreach($this->what as $e)
	{
		if($what_part != '')$what_part .= ', ';
		$what_part .= $e->val . (($e->alias != '')?' as '.$e->alias:'');
	}
	$from_part='';
	if(isset($this->from) && is_array($this->from))foreach($this->from as $e)
	{
		if($from_part != '')$from_part .= ', ';
		$from_part .= $e->val . (($e->alias != '')?' as '.$e->alias:'');
	}
	$where_part='';
	if(isset($this->where) && is_array($this->where))foreach($this->where as $e)
	{
		if($where_part != '')$where_part .= ' and ';
		$where_part .= $e;
	}
	$having_part='';
	if(isset($this->having) && is_array($this->having))foreach($this->having as $e)
	{
		if($having_part != '')$having_part .= ' and ';
		$havind_part .= $e;
	}
	$order_part='';
	if(isset($this->order) && is_array($this->order))foreach($this->order as $e)
	{
		if($order_part != '')$order_part .= ', ';
		$order_part .= $e->val . (($e->dir == 0)?' asc':' desc');
	}
	$group_part='';
	if(isset($this->group) && is_array($this->group))foreach($this->group as $e)
	{
		if($group_part != '')$group_part .= ', ';
		$group_part .= $e;
	}
	//TODO : union support
	
	$res="SELECT ".$what_part;
	if($from_part !='')$res .= " FROM ".$from_part;
	if($where_part !='')$res .= " WHERE ".$where_part;
	if($order_part !='')$res .= " ORDER BY ".$order_part;
	if($group_part !='')$res .= " GROUP BY ".$group_part;
	if($having_part !='')$res .= " HAVING ".$having_part;
	//TODO: SQL_DIALECT checking against postgres
	if($this->lim_count !='')$res .= " LIMIT ".$this->lim_count;
	if($this->lim_offset !='')$res .= " , ".$this->lim_offset;
	return $res;
}

}







?>