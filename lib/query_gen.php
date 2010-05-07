<?php



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

class sql_csv_set
{
	function __construct($op=',')
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
				$res .= (preg_match('/select.*/i',$e->type) ? ('('.$e->result().") ") : ($e->result()." "));
			else
				$res .= $e;
		}
		return $res;
	}
}







class query_gen
{
function __construct($type='select')
{
	$this->type=$type;
	$this->what=new sql_csv_set;
	$this->from=new sql_csv_set;
//	$this->joins=new sql_logical('');
	$this->where=new sql_logical;
	$this->order=new sql_csv_set;
	$this->group=new sql_csv_set;
	$this->having=new sql_logical;

	$this->into=new sql_csv_set;
	$this->set=new sql_csv_set;
	$this->union_order = new sql_csv_set;
}

function __clone()
{
	$this->what = clone $this->what;
	$this->from = clone $this->from;
	$this->where = clone $this->where;
	$this->order = clone $this->order;
	$this->group = clone $this->group;
	$this->having = clone $this->having;
	$this->into = clone $this->into;
	$this->set = clone $this->set;
	$this->union_order = clone $this->union_order;
//	$this->union_order = clone $this->union_order;
}

function add($where,$what,$alias='',$on='')
{
	if($alias != '')
	{
		$rwhat=new sql_csv_set('AS');
		$rwhat->add($what);
		$rwhat->add("`".sql::esc($alias)."`");
	}else $rwhat=$what;
	switch(strtolower($where))
	{
		case 'what':
			$this->what->add($rwhat);
			return ;
		case 'from':
			$this->from->add($rwhat);
			return ;
		case 'where':
			$this->where->add($rwhat);
			return ;
		case 'order':
			if(is_object($rwhat))$rwhat->op='';
			$this->order->add($rwhat);
			return ;
		case 'group':
			$this->group->add($rwhat);
			return ;
		case 'having':
			$this->having->add($rwhat);
			return ;
		case 'left join':
		case 'right join':
		case 'left outer join':
		case 'right outer join':
			unset($newjoin);
			$newjoin->type=$where;
			$newjoin->what=$rwhat;
			$newjoin->on=$on;
			$this->joins[]=$newjoin;
			return ;
		case 'union':
			$this->unions[]=$what;
			return ;
		case 'union_order':
			if(is_object($rwhat))$rwhat->op='';
			$this->union_order->add($rwhat);
			return ;
		
		case 'into':
			$this->into->add($rwhat);
			return ;
		case 'set':
			$this->set->add($what);
			return ;
	}
}

function set_limit($cnt,$ofs=NULL)
{
	$this->lim_count=$cnt;
	$this->lim_offset=$ofs;
}


function clear($type='select')
{
	$this->type=$type;
	unset($this->what);
	unset($this->from);
	unset($this->joins);
	unset($this->where);
	unset($this->order);
	unset($this->group);
	unset($this->having);
	unset($this->unions);
	unset($this->lim_count);
	unset($this->lim_offset);
	
	unset($this->into);
	unset($this->set);
	unset($this->union_order);
	
	$this->what=new sql_csv_set;
	$this->from=new sql_csv_set;
//	$this->joins=new sql_logical('');
	$this->where=new sql_logical;
	$this->order=new sql_csv_set;
	$this->group=new sql_csv_set;
	$this->having=new sql_logical;
	
	$this->into=new sql_csv_set;
	$this->set=new sql_csv_set;
	$this->union_order=new sql_csv_set;
//	unset($this->);
}


function result()
{
	
	$res='';
	$what_part='';
	if(isset($this->what) && is_array($this->what->exprs))$what_part=$this->what->result();
	$from_part='';
	if(isset($this->from) && is_array($this->from->exprs))$from_part=$this->from->result();
	$joins_part='';
	if(isset($this->joins) && is_array($this->joins))
	{
		reset($this->joins);
		foreach($this->joins as $join)
		{
			$join_what= is_object($join->what) ? $join->what->result() : $join->what;
			$join_on= is_object($join->on) ? $join->on->result() : $join->on;
			$joins_part.= " ".strtoupper($join->type)." ".$join_what." ON ".$join_on;
		}
	}
	$where_part='';
	if(isset($this->where) && is_array($this->where->exprs))$where_part=$this->where->result();
	$having_part='';
	if(isset($this->having) && is_array($this->having->exprs))$having_part=$this->having->result();
	$order_part='';
	if(isset($this->order) && is_array($this->order->exprs))$order_part=$this->order->result();
	$group_part='';
	if(isset($this->group) && is_array($this->group->exprs))$group_part=$this->group->result();
	$union_part='';
	if(isset($this->unions) && is_array($this->unions))
		foreach($this->unions as $e)
			$union_part.=" UNION ".(is_object($e) ? $e->result() : $e);
		
	$set_part='';
	if(isset($this->set) && is_array($this->set->exprs))$set_part=$this->set->result();
	$into_part='';
	if(isset($this->into) && is_array($this->into->exprs))$into_part=$this->into->result();
	$union_order_part='';
	if(isset($this->union_order) && is_array($this->union_order->exprs))$union_order_part=$this->union_order->result();
	
	
	if(preg_match('/^select.*/i',$this->type))
	{
		$res= strtoupper($this->type)." ".$what_part;
		if($from_part !='')$res .= " FROM ".$from_part;
		if($joins_part !='')$res .= $joins_part;
		if($where_part !='')$res .= " WHERE ".$where_part;
		if($order_part !='')$res .= " ORDER BY ".$order_part;
		if($group_part !='')$res .= " GROUP BY ".$group_part;
		if($having_part !='')$res .= " HAVING ".$having_part;
		if(isset($this->lim_count))
		{
			$ofs='';if($this->lim_offset !='')$ofs .= $this->lim_offset." , ";
			$res .= " LIMIT ".$ofs.$this->lim_count;
		}
		if($union_part !='')$res .= $union_part;
		if($union_order_part !='')$res .= " ORDER BY ".$union_order_part;
		
		return $res;
	}
	if(preg_match('/^insert.*/i',$this->type))
	{
		$res= "INSERT INTO ".$into_part;
		if($set_part !='')$res .= " SET ".$set_part;
		if($where_part !='')$res .= " WHERE ".$where_part;
		if(preg_match('/.*update.*/i',$this->type))
			if($set_part !='')$res .= " ON DUPLICATE KEY UPDATE ".$set_part;
		
		return $res;
	}
	if(preg_match('/^delete.*/i',$this->type))
	{
		$res= strtoupper($this->type)." ";
		if($from_part !='')$res .= " FROM ".$from_part;
		if($joins_part !='')$res .= $joins_part;
		if($where_part !='')$res .= " WHERE ".$where_part;
		if($order_part !='')$res .= " ORDER BY ".$order_part;
		if(isset($this->lim_count))
		{
			$ofs='';if($this->lim_offset !='')$ofs .= $this->lim_offset." , ";
			$res .= " LIMIT ".$ofs.$this->lim_count;
		}
		
		return $res;
	}
	if(preg_match('/^update.*/i',$this->type))
	{
		$res= "UPDATE ".$into_part;
		if($joins_part !='')$res .= $joins_part;
		if($set_part !='')$res .= " SET ".$set_part;
		if($where_part !='')$res .= " WHERE ".$where_part;
		if($order_part !='')$res .= " ORDER BY ".$order_part;
		if(isset($this->lim_count))
		{
			$ofs='';if($this->lim_offset !='')$ofs .= $this->lim_offset." , ";
			$res .= " LIMIT ".$ofs.$this->lim_count;
		}
		
		return $res;
	}
}
}



/////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////// CONSTANTS /////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////
	define("OP_R_UPDATED",1);
	define("OP_R_MATCHED",0);
	define("OP_R_DIFF",2);
	define("MA_FROM",0);
	define("MA_WHERE",1);


class sql_null
{
	public $alias='';
	public $error=NULL;
	
	function __construct($alias=NULL)
	{
		if(isset($alias))$this->alias=$alias;
	}
	
	function result()
	{
		/*
		if($alias=='')
		{
			$this->error=loc_get_val('sql_immed','noalias','Immediate value with no alias set!');
			return NULL;
		}*/
		//do not quote numbers
		$res=' NULL';
		if($this->alias != '')
			$res.=' AS `'.sql::esc($this->alias).'`';
		return $res;
	}
	
	function match_update($root,$item,$scope)
	{
		if(get_class($this)===get_class($item))return OP_R_MATCHED;
		else return OP_R_DIFF;
	}
}


class sql_immed
{
	public $val='',$alias='';
	public $error=NULL;
	
	function __construct($val=NULL,$alias=NULL)
	{
		if(isset($val))$this->val=$val;
		if(isset($alias))$this->alias=$alias;
	}
	
	function result()
	{
		/*
		if($alias=='')
		{
			$this->error=loc_get_val('sql_immed','noalias','Immediate value with no alias set!');
			return NULL;
		}*/
		//do not quote numbers
		if(preg_match('/^-?\d+\.?\d*$/',$this->val)||preg_match('/^-?\d*\.\d+$/',$this->val)||preg_match('/^-?\d\.\d*[eE]-?\d+$/',$this->val))
			$res=' '.sql::esc($this->val);
		else
			$res=' \''.sql::esc($this->val).'\'';
		if($this->alias != '')
			$res.=' AS `'.sql::esc($this->alias).'`';
		if($this->variable !='')
			$res=' @`'.sql::esc($this->variable).'` :='.$res;
		return $res;
	}
	
	function match_update($root,$item,$scope)
	{
		if(get_class($this)===get_class($item) && $this->val===$item->val)return OP_R_MATCHED;
		else return OP_R_DIFF;
	}
}

class sql_var
{
	public $var='',$alias='';
	public $error=NULL;
	
	function __construct($var=NULL,$alias=NULL)
	{
		if(isset($var))$this->var=$var;
		if(isset($alias))$this->alias=$alias;
	}
	
	function result()
	{
		/*
		if($alias=='')
		{
			$this->error=loc_get_val('sql_immed','noalias','Immediate value with no alias set!');
			return NULL;
		}*/
		//do not quote numbers
			$res=' @`'.sql::esc($this->var).'`';
		if($this->alias != '')
			$res.=' AS `'.sql::esc($this->alias).'`';
		if($this->variable !='')
			$res=' @`'.sql::esc($this->variable).'` :='.$res;
		return $res;
	}
	
	function match_update($root,$item,$scope)
	{
		if(get_class($this)===get_class($item) && $this->var===$item->var)return OP_R_MATCHED;
		else return OP_R_DIFF;
	}
}

class sql_column
{
	public $db='',$tbl='',$col='',$alias='';
	public $error=NULL;
	
	function __construct($db=NULL,$tbl=NULL,$col=NULL,$alias=NULL)
	{
		if(isset($db)&&($db!=''))$this->db=$db;
		if(isset($tbl)&&($tbl!=''))$this->tbl=$tbl;
		if(isset($col)&&($col!=''))$this->col=$col;
		if(isset($alias)&&($alias!=''))$this->alias=$alias;
	}
	
	function result()
	{
		if($this->col=='' && $this->tbl=='' && $this->db=='')
		{
			$this->error=loc_get_val('sql_column','nocol','None of column name,db name,table name is set!');
			return NULL;
		}
		//check for dupplicate alias/ambigous column
		//check for nonexisting column/table(check current scope)/db
		//TODO: add asserts
		$res=' ';
		if($this->db != '')$res.='`'.sql::esc($this->db).'`';
		if($this->tbl != '')
		{
			if($res != ' ')$res.='.';
			$res.='`'.sql::esc($this->tbl).'`';
		}
		if($this->col != '')
		{
			if($res != ' ')$res.='.';
			$res.='`'.sql::esc($this->col).'`';
		}
		if($this->alias != '')
		{
			$res.=' AS `'.sql::esc($this->alias).'`';
		}
		
		if($this->variable !='')
			$res=' @`'.sql::esc($this->variable).'` :='.$res;
		
		return $res;
	}
	
	function col_replace($from,$to)
	{
		if($this->tbl==$from)$this->tbl=$to;
		if($this->col==$from)$this->col=$to;
	}
	
	function match_update($root,$item,$scope)
	{
		if(	get_class($this)===get_class($item)	&&
			$this->db===$item->db			&&
			$this->tbl===$item->tbl			&&
			$this->col===$item->col
			)
		{
			if($this->alias===$item->alias)return OP_R_MATCHED;
			if($scope==MA_FROM)
			{
				$root->col_replace($item->alias,$this->alias);
				$item->alias=$this->alias;
				return OP_R_UPDATED;
			}
		}else return OP_R_DIFF;
	}
}

class sql_subquery
{
	//may appear in what/from/where/join/on order? having?
	//must have alias in what(results namespace)/from,join(tables namespace)
	//must appear inside expression in where/on/having
	//should not have alias when inside expression
	//TODO: add asserts
	
	public $subquery=NULL,$alias='';
	public $error=NULL;
	
	function __construct($subquery=NULL,$alias=NULL)
	{
		if(isset($subquery))$this->subquery=$subquery;
		else $this->subquery=new query_gen_ext;
		if(isset($alias))$this->alias=$alias;
	}
	
	function result()
	{
		if($this->subquery==NULL)
		{
			$this->error=loc_get_val('sql_subquery','subqueryunset','Subquery is not set!');
			return NULL;
		}
		//check for dupplicate alias/ambigous column
		$res=' ('.$this->subquery->result().')';
		if($this->alias != '')
			$res.=' AS `'.sql::esc($this->alias).'`';
		if($this->variable !='')
			$res=' @`'.sql::esc($this->variable).'` :='.$res;
		return $res;
	}
	function __clone()
	{
		if(is_object($this->subquery))$this->subquery=clone $this->subquery;
	}
	
	function col_replace($from,$to)
	{
		$this->subquery->col_replace($from,$to);
	}
	function match_update($root,$item,$scope)
	{
		//check for subquery in from/join clause and handle as sql_column
		//may be in some situations subquery from/join has to be treated as main query from/join!!!
		//$b=clone $this->subquery;
		$tr=$this->subquery->match_update($item->subquery,$item->subquery,MA_WHERE);
		//if($tr==OP_R_DIFF)
		//	$this->subquery=$b;
		return $tr;
	}
}

class sql_expression
{
	public $exprs=Array(),$alias='';
	public $operator='AND';
	public $error=NULL;
	public $nopar=false;
	
	function __construct($operator=NULL,$exprs=NULL,$alias=NULL)
	{
		if(isset($operator))$this->operator=$operator;
		if(isset($exprs))$this->exprs=$exprs;
		if(isset($alias))$this->alias=$alias;
	}
	
	function result()
	{
		if(!isset($this->exprs))
		{
			$this->error=loc_get_val('sql_expression','unset','Expression is not set!');
			return NULL;
		}
		$res=' ';
		if(is_array($this->exprs))
			$exprs=&$this->exprs;
		else
			$exprs[]=&$this->exprs;
		if(count($exprs)==0)return '';
		$this->operator=strtoupper($this->operator);
		foreach($exprs as $e)
		{
			if($e->invert==1)
			{
				switch($this->operator)
				{
				case 'AND':
				case 'OR':
				case '&&':
				case '||':
				case 'XOR':
					if($res != ' ')$res.=$this->operator.' NOT ';
					else $res.='NOT ';
					break;
				case '*':
					if($res != ' ')$res.='/ ';
					else $res.='1/ ';
					break;
				case '+':
					$res.='- ';
					break;
				}
			}else{
				if($res != ' ')$res.=$this->operator.' ';
			}
			if(is_a($e,'sql_immed')||is_a($e,'sql_var')||is_a($e,'sql_column')||is_a($e,'sql_list')||is_a($e,'sql_null')||is_a($e,'sql_subquery')|| $this->nopar)
			{
			//no parenthesis required
				$res.=$e->result().' ';
			}else{
				$res.='('.$e->result().') ';
			}
		}
		//check for dupplicate alias/ambigous column
		if($this->alias != '')
		{
			$res.=' AS `'.sql::esc($this->alias).'`';
		}
		if($this->variable !='')
			$res=' @`'.sql::esc($this->variable).'` :='.$res;
		return $res;
	}
	function __clone()
	{
		if(is_array($this->exprs))foreach($this->exprs as $k => $e)if(is_object($e))$this->exprs[$k]=clone $e;
	}
	
	function col_replace($from,$to)
	{
		if(!is_array($this->exprs))return;
		foreach($this->exprs as $e)
			if(method_exists($e,'col_replace'))
				$e->col_replace($from,$to);
	}
	
	function match_update($root,$item,$scope)
	{
		//some care required
		//check if expression is ordered and compare sequentally
		//check if expression is unordered and try find once
		//print 'alert("cn='.$this->operator.','.$item->operator.'");';//debug
		if($this->operator!=$item->operator)return OP_R_DIFF;
		if(!is_array($this->exprs) && !is_array($item->exprs))return OP_R_MATCHED;
		if(is_array($this->exprs) != is_array($item->exprs))return OP_R_DIFF;
		
		//check for special situation where partial match is enough
		if(count($this->exprs)!=count($item->exprs) && $root->where!=$item)return OP_R_DIFF;
		
		$unordered=Array('+' =>1,'*' => 1,'or' => 1, 'and' => 1, 'xor' => 1, '|' => 1, '&' => 1, '^' => 1,'=' =>1 );
		if(isset($unordered[strtolower($this->operator)]))
		{
			//unordered match - not good, slow search
			$unfound=Array();
			foreach($item->exprs as $ti => $te)$unfound[$ti]=1;
			reset($unfound);
			reset($this->exprs);
			foreach($this->exprs as $te)
				foreach($unfound as $ei => $ee)
				{
					$ee=$item->exprs[$ei];
					$tr=$te->match_update($root,$ee,$scope);
					if($tr == OP_R_MATCHED)
					{
						unset($unfound[$ei]);
						reset($unfound);
						break;
					}
					if($tr == OP_R_UPDATED)return $tr;
				}
			if(count($unfound)>0)return OP_R_DIFF;
			return OP_R_MATCHED;//only list can trigger update so return OP_R_MATCHED
		}else{
			foreach($this->exprs as $ti => $te)
			{
				$tr=$item->exprs[$ti]->match_update($root,$ee,$scope);
				if($tr != OP_R_MATCHED)return $tr;
			}
			return OP_R_MATCHED;
		}
		return OP_R_DIFF;
	}
}


class sql_list
{
	public $func=NULL,$exprs=Array();
	public $delimiter=',',$alias='';
	public $error=NULL;
	public $nopar=false;
	
	function __construct($func=NULL,$exprs=NULL,$alias=NULL,$delimiter=NULL)
	{
		if(isset($func))$this->func=$func;
		if(isset($exprs))$this->exprs=$exprs;
		if(isset($alias))$this->alias=$alias;
		if(isset($delimiter))$this->delimiter=$delimiter;
	}
	
	function result()
	{
		if(!isset($this->exprs))
		{
			$this->error=loc_get_val('sql_list','unset','Expression is not set!');
			return NULL;
		}
		$res=' ';
		if(is_array($this->exprs))
			$exprs=&$this->exprs;
		else
			$exprs[]=&$this->exprs;
		if(count($exprs)==0)return '';
		foreach($exprs as $e)
		{
			if($res != ' ')$res.=$this->delimiter.' ';
			if(!is_object($e))
			{
				$this->error=loc_get_val('sql_list','not_an_object','$exprs array contains weird data.');
				return NULL;
			}
			if(is_a($e,'sql_immed')||is_a($e,'sql_var')||is_a($e,'sql_column')||is_a($e,'sql_list')|| $this->nopar)
			{
			//no parenthesis required
				$res.=$e->result().' ';
			}else{
				$res.='('.$e->result().') ';
			}
		}
		if(isset($this->func) && $this->func != '')$res=$this->func.'('.$res.')';
		//check for dupplicate alias/ambigous column
		if($this->alias != '')
		{
			$res.=' AS `'.sql::esc($this->alias).'`';
		}
		if($this->variable !='')
			$res=' @`'.sql::esc($this->variable).'` :='.$res;
		return $res;
	}
	
	function __clone()
	{
		if(is_array($this->exprs))foreach($this->exprs as $k => $e)if(is_object($e))$this->exprs[$k]=clone $e;
	}
	
	function col_replace($from,$to)
	{
		if(!is_array($this->exprs))return;
		foreach($this->exprs as $e)
			if(method_exists($e,'col_replace'))
				$e->col_replace($from,$to);
	}
	
	function match_update($root,$item,$scope)
	{
		//some care required
		//check if expression is ordered and compare sequentally
		//check if expression is unordered and try find once
		if($this->func!=$item->func)return OP_R_DIFF;
		if(!is_array($this->exprs) && !is_array($item->exprs))return OP_R_MATCHED;
		if(is_array($this->exprs) != is_array($item->exprs))return OP_R_DIFF;
		
		//check for special situation where partial match is enough
		if(count($this->exprs)!=count($item->exprs) && $root->from!=$item)return OP_R_DIFF;
		
		$unordered=Array('' =>1);//TODO: figure out which mysql/pgsql functions take unordered list of arguments
		if(isset($unordered[strtolower($this->func)]) || !isset($this->func))
		{
			//unordered match - not good, slow search
			$unfound=Array();
			foreach($item->exprs as $i => $e)$unfound[$i]=1;
			//return OP_R_DIFF;
			reset($unfound);
			reset($this->exprs);
			foreach($this->exprs as $te)
				foreach($unfound as $ei => $ee)
				{
					$ee=$item->exprs[$ei];
					$tr=$te->match_update($root,$ee,$scope);
					if($te->invert != $ee->invert)$tr=OP_R_DIFF;
					//if($tr==OP_R_UPDATED)print 'alert("ee.col='.$ee->alias.',te.col='.$te->alias.'");';
					if($tr == OP_R_MATCHED)
					{
						unset($unfound[$ei]);
						reset($unfound);
						break;
					}
					if($tr == OP_R_UPDATED)return $tr;
				}
			if(count($unfound)>0)return OP_R_DIFF;
			return OP_R_MATCHED;//only list can trigger update so return OP_R_MATCHED
		}else{
			foreach($this->exprs as $ti => $te)
			{
				$tr=$item->exprs[$ti]->match_update($root,$ee,$scope);
				if($tr != OP_R_MATCHED)return $tr;
			}
			return OP_R_MATCHED;
		}
		return OP_R_DIFF;
	}
}


class sql_order extends sql_list
{
	
	function result()
	{
		if(!isset($this->exprs))
		{
			$this->error=loc_get_val('sql_list','unset','Expression is not set!');
			return NULL;
		}
		$res=' ';
		if(is_array($this->exprs))
			$exprs=&$this->exprs;
		else
			$exprs[]=&$this->exprs;
		if(count($exprs)==0)return '';
		foreach($exprs as $e)
		{
			if($res != ' ')$res.=$this->delimiter.' ';
			if(!is_object($e))
			{
				$this->error=loc_get_val('sql_list','not_an_object','$exprs array contains weird data.');
				return NULL;
			}
			$desc=($e->invert==1)?'DESC ':'ASC';
			if(is_a($e,'sql_immed')||is_a($e,'sql_column')||is_a($e,'sql_list')|| $this->nopar)
			{
			//no parenthesis required
				$res.=$e->result().' '.$desc;
			}else{
				$res.='('.$e->result().') '.$desc;
			}
		}
		//check for dupplicate alias/ambigous column
		return $res;
	}
	
	function __clone()
	{
		if(is_array($this->exprs))foreach($this->exprs as $k => $e)if(is_object($e))$this->exprs[$k]=clone $e;
	}
}

class sql_joins
{
	public $exprs=Array();
	public $error=NULL;
	public $nopar=false;
	
	function __construct($exprs=NULL)
	{
		/* Array(
		(object)Array('type' => 'LEFT OUTER JOIN', 'what' => new sql_list(..), 'on' => new sql_expression(...)),
		(object)Array('type' => 'LEFT OUTER JOIN', 'what' => new sql_list(..), 'on' => new sql_expression(...))
		) */
		if(isset($exprs))$this->exprs=$exprs;
	}
	
	function result()
	{
		$res=' ';
		if(is_array($this->exprs))
			$exprs=&$this->exprs;
		else
			$exprs[]=&$this->exprs;
		if(count($exprs)==0)return '';
		reset($exprs);
		foreach($exprs as $e)
		{
			if($res != ' ')$res.=' ';
			$join_what= is_object($e->what) ? $e->what->result() : $e->what;
			$join_on= is_object($e->on) ? $e->on->result() : $e->on;
			if($join_what != '' && $join_on != '')$res.=" ".strtoupper($e->type)." ".$join_what." ON ".$join_on;
		}
		return $res;
	}
	
	function __clone()
	{
		if(is_array($this->exprs))foreach($this->exprs as $k => $e)if(is_object($e))$this->exprs[$k]=clone $e;
	}
	
	function col_replace($from,$to)
	{
		if(!is_array($this->exprs))return;
		foreach($this->exprs as $e)
		{
			if(method_exists($e->what,'col_replace'))
				$e->what->col_replace($from,$to);
			if(method_exists($e->on,'col_replace'))
				$e->on->col_replace($from,$to);
		}
	}
	
	function match_update($root,$item,$scope)
	{
		
		if(!is_array($this->exprs) && !is_array($item->exprs))return OP_R_MATCHED;
		if(is_array($this->exprs) != is_array($item->exprs))return OP_R_DIFF;
		//joins are always this 'special case' (unordered partial match)
		$unfound=Array();
		foreach($item->exprs as $ti => $te)$unfound[$ti]=1;
		reset($unfound);
		reset($this->exprs);
		foreach($this->exprs as $te)
			foreach($unfound as $ei => $ee)
			{
				$ee=$item->exprs[$ei];
				$tr0=(strtoupper($te->type)==strtoupper($ee->type))?OP_R_MATCHED:OP_R_DIFF;
				
				$tr1=$te->what->match_update($root,$ee->what,MA_FROM);
				if($tr1==OP_R_UPDATED) return $tr1;//restart after update
				
				$tr2=$te->on->match_update($root,$ee->on,MA_WHERE);
				if($tr0 == OP_R_MATCHED && $tr1 == OP_R_MATCHED && $tr2 == OP_R_MATCHED)
				{
					unset($unfound[$ei]);
					reset($unfound);
					break;
				}
			}
		if(count($unfound)>0)return OP_R_DIFF;
		return OP_R_MATCHED;//only list can trigger update so return OP_R_MATCHED
	}
}



class query_gen_ext
{
function __construct($type='select')
{
	$this->type=$type;
	$this->what=new sql_list;
	$this->what->nopar=true;
	$this->from=new sql_list;
	$this->from->nopar=true;
	$this->joins=new sql_joins;
	$this->where=new sql_expression;
	$this->order=new sql_order;
	$this->order->nopar=true;
	$this->group=new sql_order;
	$this->group->nopar=true;
	$this->having=new sql_expression;

	$this->into=new sql_list;
	$this->into->nopar=true;
	$this->set=new sql_list;
	$this->set->nopar=true;
	$this->update=new sql_list;
	$this->update->nopar=true;
	$this->union_order = new sql_list;
	//$this->select; - for insert select
}

function __clone()
{
	if(is_object($this->what))$this->what = clone $this->what; else $this->what=new sql_list;
	if(is_object($this->from))$this->from = clone $this->from; else $this->from=new sql_list;
	if(is_object($this->where))$this->where = clone $this->where; else $this->where=new sql_expression;
	if(is_object($this->order))$this->order = clone $this->order; else $this->order=new sql_order;
	if(is_object($this->group))$this->group = clone $this->group; else $this->group=new sql_order;
	if(is_object($this->having))$this->having = clone $this->having; else $this->having=new sql_expression;
	if(is_object($this->into))$this->into = clone $this->into; else $this->into=new sql_list;
	if(is_object($this->set))$this->set = clone $this->set; else $this->set=new sql_list;
	if(is_object($this->update))$this->update = clone $this->update; else $this->update=new sql_list;
	if(is_object($this->union_order))$this->union_order = clone $this->union_order; else $this->union_order = new sql_list;
	if(is_object($this->joins))$this->joins = clone $this->joins; else $this->joins=new sql_joins;
	if(is_object($this->select))$this->select = clone $this->select; else unset($this->select);
	if(is_array($this->unions))
		foreach($this->unions as $i => $j)
			if(is_object($j))$this->unions[$i]=clone $j; else 
//	$this->union_order = clone $this->union_order;
	$this->what->nopar=true;
	$this->from->nopar=true;
	$this->order->nopar=true;
	$this->group->nopar=true;
	$this->set->nopar=true;
	$this->update->nopar=true;
}

function add($where,$path,$what)
{
	switch(strtolower($where))
	{
		case 'what':
			$this->what->add($path,$what);
			return ;
		case 'from':
			$this->from->add($path,$what);
			return ;
		case 'where':
			$this->where->add($path,$what);
			return ;
		case 'order':
			$this->order->add($path,$what);
			return ;
		case 'group':
			$this->group->add($path,$what);
			return ;
		case 'having':
			$this->having->add($path,$what);
			return ;
		case 'left join':
		case 'right join':
		case 'left outer join':
		case 'right outer join':
			$what->type=$where;
			$this->joins->exprs[]=$what;
			return ;
		case 'union':
			$this->unions[]=$what;
			return ;
		case 'union_order':
			$this->union_order->add($path,$what);
			return ;
		
		case 'into':
			$this->into->add($path,$what);
			return ;
		case 'set':
			$this->set->add($path,$what);
			return ;
		case 'update':
			$this->update->add($path,$what);
			return ;
	}
}

function set_limit($cnt,$ofs=NULL)
{
	$this->lim_count=$cnt;
	$this->lim_offset=$ofs;
}


function clear($type='select')
{
	$this->type=$type;
	unset($this->what);
	unset($this->from);
	unset($this->joins);
	unset($this->where);
	unset($this->order);
	unset($this->group);
	unset($this->having);
	unset($this->unions);
	unset($this->lim_count);
	unset($this->lim_offset);
	
	unset($this->into);
	unset($this->set);
	unset($this->update);
	unset($this->union_order);
	unset($this->select);
	
	$this->__construct($type);
}

	
function smart_merge($q,$ref_table='',$ref_column='')
{
	//try to implement merging a template to a query
	//in:	$q template query to merge
	//	$this - incomplete query to merge to
	//	$base_table alias or table name to use as base
	//out:	$this - compound query
	//	returns: $q->$this result portion mapping in a form of array: $result[$q->what[%n]->alias]=$this->what[%n]->alias
	//1:	check from and join->from lists for already merged template parts
	//	check on/where lists for already merged template parts
	//	if found, take table-aliases from them and replace in $q
	//	if not, generate unique table-aliases for each table alias in $q, replace in $q
	//	 then copy join/from parts to $this
	//2:	generate unique aliases for each $q->what item, create mappings, replace aliases in $q,add $q->what to $this->what
	// database is defined statically(if defined. may be NULL == current)
	// table name defined static or `!` means source table
	// columns are defined statically
	
	//generic style: SELECT tn.a AS `something from ref twisted` FROM JOIN `twisted table` AS `tn` ON `tn`.x=`!`.y AND `tn`.d=(SELECT MAX(tx.d) FROM `twisted table` AS `tx` WHERE `tx`.x=`!`.y AND ... ) AND ...
	// push $q
	// replace ! in table scope of $q with $base_table
	// check for `twisted table` in JOIN WHAT lists
	// if any, replace tn in table scope of $q with alias of `twisted table` in JOIN WHAT of $this
	//  compare ON expression of this JOIN in 'follow' mode (if table matches but alias differs get alias from $this, replace in
	//  $q and recompare
	//  if matches compare WHAT list node with nodes in $this ignoring alias, if found
	//   ?????????? make a new instance or return null??????
	//  if not found
	//   create new unique alias in result scope, create sub_list, set $q->what[0]->alias to it, add $q->what[0] to $this->what
	//   and return sub_list
	// if not found or ON compariion fails
	//  pop $q
	//  merge $q as new JOIN
	// if 
	
	$from_matched=false;
	$joins_matched=false;
	$where_matched=false;
	
	$q_prepared=clone $q;
	if($ref_table != '')$q_prepared->col_replace('!',$ref_table);
	if($ref_column != '')$q_prepared->col_replace('!!',$ref_column);
	$q_backup= serialize($q_prepared);
	$deadloop=10;//is it enough&
	if(isset($this->from)&&isset($q_prepared->from))//has from in both src and dest, so do check and update
		do{
			$op_r=$this->from->match_update($q_prepared,$q_prepared->from,MA_FROM);
			$deadloop--;
			if($deadloop<=0)
			{
				print 'alert("lockup in from update");';
				break;
			}
		}while($op_r == OP_R_UPDATED);
	else{
		if(isset($q_prepared->from))$op_r = OP_R_DIFF;
		else $op_r = OP_R_MATCHED;
	}
	if($op_r == OP_R_MATCHED)
	{
		$from_matched=1;//got match ; try to compare
		$q_backup= serialize($q_prepared);
		
		$deadloop=10;
		if(isset($this->where)&&isset($q_prepared->where))//has from in both src and dest, so do check and update
			do{
				$op_r=$this->where->match_update($q_prepared,$q_prepared->where,MA_WHERE);
				$deadloop--;
				if($deadloop<=0)
				{
					print 'alert("lockup in from update");';
					break;
				}
			}while($op_r == OP_R_UPDATED);
		else{
			if(isset($q_prepared->where))$op_r = OP_R_DIFF;
			else $op_r = OP_R_MATCHED;
		}
		if($op_r == OP_R_DIFF)
		{
			//got no match. Let's create new join
			$q_prepared=unserialize($q_backup);
		}else $where_matched=1;
	}else{
	//not matched! revert updates
		$q_prepared=unserialize($q_backup);
	}
	//joins dammn
	$q_backup= serialize($q_prepared);
	$deadloop=10;
	if(isset($this->joins)&&isset($q_prepared->joins))//has from in both src and dest, so do check and update
		do{
			$op_r=$this->joins->match_update($q_prepared,$q_prepared->joins,MA_FROM);
			$deadloop--;
			if($deadloop<=0)
			{
				print 'alert("lockup in from update");';
				break;
			}
		}while($op_r == OP_R_UPDATED);
	else{
		if(isset($q_prepared->joins))$op_r = OP_R_DIFF;
		else $op_r = OP_R_MATCHED;
	}
	if($op_r == OP_R_DIFF)
	{
		$q_prepared=unserialize($q_backup);
		//print 'alert("restored");';
	}else $joins_matched=1;
	
	
	//non smart test
	//dumb merge mode do it only for pure new templates
	$this->merge_add_exprs('what',$q_prepared);
	if(!$from_matched)$this->merge_add_exprs('from',$q_prepared);
	if(!$joins_matched)$this->merge_add_exprs('joins',$q_prepared);
	if(!$where_matched)$this->merge_add_exprs('where',$q_prepared);
	$this->merge_add_exprs('into',$q_prepared);
	$this->merge_add_exprs('set',$q_prepared);
	$this->merge_add_exprs('update',$q_prepared);
	
	
	return OP_R_MATCHED;
}

function merge_add_exprs($e,$q)
{
	if(isset($this->$e) && (is_array($this->$e->exprs) || !isset($this->$e->exprs)) &&
	   isset($q->$e) && (is_array($q->$e->exprs)))
	 {
	 	reset($q->$e->exprs);
	 	foreach($q->$e->exprs as $t)
	 		$this->$e->exprs[]=$t;
	 }
	else
	{
		//debug
		//print 'alert("DBG:$e='.$e.'<br>");';
		/*if($e=='from')
		{
			print 'alert("DBG:$e:'.isset($this->$e).','.is_array($this->$e->exprs).'<br>");';
			print 'alert("DBG:$q:'.isset($q->$e->exprs).','.is_array($q->$e->exprs).','.get_class($q).'<br>");';
		}*/
	}
}

function match_update($root,$item,$scope)
{
	if($this->match_update_p($root,'where',MA_WHERE) != OP_R_MATCHED)return OP_R_DIFF;
	if($this->match_update_p($root,'what' ,MA_WHAT ) != OP_R_MATCHED)return OP_R_DIFF;
	if($this->match_update_p($root,'from',MA_FROM) != OP_R_MATCHED)return OP_R_DIFF;
	if($this->match_update_p($root,'joins',MA_FROM) != OP_R_MATCHED)return OP_R_DIFF;
	if($this->match_update_p($root,'having',MA_WHERE) != OP_R_MATCHED)return OP_R_DIFF;
	if($this->match_update_p($root,'order',MA_WHERE) != OP_R_ORDER)return OP_R_DIFF;
	if($this->match_update_p($root,'group',MA_WHERE) != OP_R_ORDER)return OP_R_DIFF;
	if($this->match_update_p($root,'set',MA_WHAT) != OP_R_MATCHED)return OP_R_DIFF;
	if($this->match_update_p($root,'update',MA_WHAT) != OP_R_MATCHED)return OP_R_DIFF;
	if($this->match_update_p($root,'into',MA_FROM) != OP_R_MATCHED)return OP_R_DIFF;
	if(intval($this->lim_count)  != intval($root->lim_count)) return OP_R_DIFF;
	if(intval($this->lim_offset) != intval($root->lim_offset))return OP_R_DIFF;
}

function match_update_p($root,$item,$scope)
{
	$i=$item;
	if(method_exists($this->$i,'match_update'))
	{
		$deadloop=10;
		if(isset($this->$i) && isset($root->$i))
			do{
				$op_r=$this->$i->match_update($root,$root->$i,$scope);
				$deadloop--;
				if($deadloop<=0)
				{
					print 'alert("lockup in '.$i.' subquery update");';
					return OP_R_DIFF;
					break;
				}
			}while($op_r == OP_R_UPDATED);
		return $op_r;
	}
	return OP_R_DIFF;
}



function col_replace($from,$to)
{
	if(method_exists($this->what,'col_replace'))
		$this->what->col_replace($from,$to);
	if(method_exists($this->from,'col_replace'))
		$this->from->col_replace($from,$to);
	if(method_exists($this->joins,'col_replace'))
		$this->joins->col_replace($from,$to);
	if(method_exists($this->where,'col_replace'))
		$this->where->col_replace($from,$to);
	if(method_exists($this->having,'col_replace'))
		$this->having->col_replace($from,$to);
	if(method_exists($this->order,'col_replace'))
		$this->order->col_replace($from,$to);
	if(method_exists($this->group,'col_replace'))
		$this->group->col_replace($from,$to);
	if(method_exists($this->into,'col_replace'))
		$this->into->col_replace($from,$to);
	if(method_exists($this->union_order,'col_replace'))
		$this->union_order->col_replace($from,$to);
	//unions so far....
}



function result()
{
	
	$res='';
	$what_part='';
	if(isset($this->what) && is_array($this->what->exprs))$what_part=$this->what->result();
	$joins_part='';
	if(isset($this->joins) && is_array($this->joins->exprs))$joins_part=$this->joins->result();
	if(($joins_part !='') && (count($this->from->exprs)>1))$this->from->func=' ';
	$from_part='';
	if(isset($this->from) && is_array($this->from->exprs))$from_part=$this->from->result();
	$where_part='';
	if(isset($this->where) && is_array($this->where->exprs))$where_part=$this->where->result();
	$having_part='';
	if(isset($this->having) && is_array($this->having->exprs))$having_part=$this->having->result();
	$order_part='';
	if(isset($this->order) && is_array($this->order->exprs))$order_part=$this->order->result();
	$group_part='';
	if(isset($this->group) && is_array($this->group->exprs))$group_part=$this->group->result();
	$union_part='';
	if(isset($this->unions) && is_array($this->unions))
		foreach($this->unions as $e)
			$union_part.=" UNION ".(is_object($e) ? $e->result() : $e);
		
	$set_part='';
	if(isset($this->set) && is_array($this->set->exprs))$set_part=$this->set->result();
	$update_part='';
	if(isset($this->update) && is_array($this->update->exprs))$update_part=$this->update->result();
	$into_part='';
	if(isset($this->into) && is_array($this->into->exprs))$into_part=$this->into->result();
	$union_order_part='';
	if(isset($this->union_order) && is_array($this->union_order->exprs))$union_order_part=$this->union_order->result();
	
	if(preg_match('/^select.*/i',$this->type))
	{
		$res= strtoupper($this->type)." ".$what_part;
		if($from_part !='')$res .= " FROM ".$from_part;
		if($joins_part !='')$res .= $joins_part;
		if($where_part !='')$res .= " WHERE ".$where_part;
		if($group_part !='')$res .= " GROUP BY ".$group_part;
		if($having_part !='')$res .= " HAVING ".$having_part;
		if($order_part !='')$res .= " ORDER BY ".$order_part;
		if(isset($this->lim_count) && $this->lim_count != '')
		{
			$ofs='';if(intval($this->lim_offset) != 0)$ofs .= intval($this->lim_offset)." , ";
			$res .= " LIMIT ".$ofs.$this->lim_count;
		}
		if($union_part !='')$res .= $union_part;
		if($union_order_part !='')$res .= " ORDER BY ".$union_order_part;
		
		return $res;
	}
	if(preg_match('/^insert.*/i',$this->type))
	{
		$res= "INSERT ";
		if(preg_match('/.*ignore.*/i',$this->type))$res.="IGNORE ";
		if(preg_match('/.*select/',$this->type))
		{
			if(count($this->into->exprs)!=1)return "error: insert-select into multiple tables not implemented yet";
			$res.="INTO ".$into_part;
			unset($al_list);
			foreach($this->what->exprs as $e)
			{
				if($al_list!='')$al_list.=',';
				$al_list.=("`".$e->alias."`");
			}
			$res.="(".$al_list.") ";
			$res= strtoupper($this->type)." ".$what_part;
			if($from_part !='')$res .= " FROM ".$from_part;
			if($joins_part !='')$res .= $joins_part;
			if($where_part !='')$res .= " WHERE ".$where_part;
			if($group_part !='')$res .= " GROUP BY ".$group_part;
			if($having_part !='')$res .= " HAVING ".$having_part;
			if($order_part !='')$res .= " ORDER BY ".$order_part;
			if(isset($this->lim_count) && $this->lim_count != '')
			{
				$ofs='';if(intval($this->lim_offset) != 0)$ofs .= intval($this->lim_offset)." , ";
				$res .= " LIMIT ".$ofs.$this->lim_count;
			}
			if($union_part !='')$res .= $union_part;
			if($union_order_part !='')$res .= " ORDER BY ".$union_order_part;
			return $res;
			
		}else{
			$res.="INTO ".$into_part;
			if($set_part !='')$res .= " SET ".$set_part;
	//		if($where_part !='')$res .= " WHERE ".$where_part;
			if(preg_match('/.*update.*/i',$this->type))
				if($set_part !='')$res .= " ON DUPLICATE KEY UPDATE ".(($update_part=='')?$set_part:$update_part);
			
			return $res;
		}
	}
	if(preg_match('/^delete.*/i',$this->type))
	{
		$res= strtoupper($this->type)." ".$what_part;
		if($from_part !='')$res .= " FROM ".$from_part;
		if($joins_part !='')$res .= $joins_part;
		if($where_part !='')$res .= " WHERE ".$where_part;
		if($order_part !='')$res .= " ORDER BY ".$order_part;
		if(isset($this->lim_count) && $this->lim_count != '')
		{
			$ofs='';if(intval($this->lim_offset) != 0)$ofs .= intval($this->lim_offset)." , ";
			$res .= " LIMIT ".$ofs.$this->lim_count;
		}
		
		return $res;
	}
	if(preg_match('/^update.*/i',$this->type))
	{
		$res= "UPDATE ".$into_part;
		if($joins_part !='')$res .= $joins_part;
		if($set_part !='')$res .= " SET ".$set_part;
		if($where_part !='')$res .= " WHERE ".$where_part;
		if($order_part !='')$res .= " ORDER BY ".$order_part;
		if(isset($this->lim_count) && $this->lim_count != '')
		{
			$ofs='';if(intval($this->lim_offset) != 0)$ofs .= intval($this->lim_offset)." , ";
			$res .= " LIMIT ".$ofs.$this->lim_count;
		}
		
		return $res;
	}
}

function strip_aliases($o=NULL,$skip=false)
{
	if($o==NULL)$o=$this;
	if(get_class($o)=='query_gen_ext')
	{
		unset($o->what->alias);
		unset($o->what->func);
		if(is_array($o->what->exprs))
			foreach($o->what->exprs as $e)
				$this->strip_aliases($e,true);
		unset($o->from->alias);
		unset($o->from->func);
		if(is_array($o->from->exprs))
			foreach($o->from->exprs as $e)
				$this->strip_aliases($e,true);
		$this->strip_aliases($o->joins);
		$o->where->operator='AND';
		$this->strip_aliases($o->where);
		unset($o->order->func);
		$this->strip_aliases($o->order);
		unset($o->group->func);
		$this->strip_aliases($o->group);
		$o->having->operator='AND';
		$this->strip_aliases($o->having);
		#$this->strip_aliases($o->unions);
		
		unset($o->into->func);
		$this->strip_aliases($o->into);
		unset($o->set->func);
		$this->strip_aliases($o->set);
		unset($o->update->func);
		$this->strip_aliases($o->update);
		unset($o->union_order->func);
		$this->strip_aliases($o->union_order);
		return;
	}
	if(get_class($o)=='sql_joins')
	{
		unset($o->alias);
		if(is_array($o->exprs))
			foreach($o->exprs as $e)
			{
				if(is_array($e->what->exprs) && (get_class($e->what)=='sql_list') && ($e->func==''))
				{
					unset($e->what->alias);
					unset($o->what->func);
					foreach($e->what->exprs as $x)
						$this->strip_aliases($x,true);
				}
				$e->on->operator='AND';
				$this->strip_aliases($e->on);
			}
		return;
	}
	if(get_class($o)=='sql_subquery')
		$this->strip_aliases($o->subquery);
	if(!$skip)unset($o->alias);
	if(is_array($o->exprs))
		foreach($o->exprs as $e)
			$this->strip_aliases($e);
}







}











?>