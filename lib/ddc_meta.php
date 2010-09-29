<?php

require_once("etc/dbsettings.php");
require_once("sql/my.php");
require_once("lib/base_connect.php");
require_once("lib/ddc_raw.php");


// root->dynamic->refs->objects[type:ref;level:1]
// root->dynamic->refs->objects->maintbl[type:table_def;level:2]
// root->dynamic->refs->objects->maintbl->id[keyname:primary;type:id;level:3
// root->dynamic->refs->objects->maintbl->name
// root->dynamic->refs->objects->maintbl->


/*
service side-tables:
	selections,shadow
	selections:primary key=base primary+uid,columns=primary key+[%isselected]
	or maybe this extended variant
	selections:primary key=base primary+uid+set,columns=primary key+[%isselected]
	
	shadow:primary key=base primary+mtime+state+muser,column=all
	state:0 - is a shadow
	state:-1 - open for edit
	state:>0 - is a template

	shadowed tables should have common columns:
		ctime datetime,mtime timestamp,cuser int,muser int

column inheritance/sharing
inherited columns has some fields updated when parent updates
shared columns has same name and all sql fields updated, may have some 'class' value set on new item creation and used in where clause
inherited tables has all columns from parent set to inherit and updates column set when parent updates
	shared:	inherit=share
			inherit_from=id
	inherited:	inherit=common
				inherit_from=id
physical dropping column from shared parent table will only occur when this coumn will be dropped from all child tables
primary key may be expanded in shared tables(not recommended so)
sharing:
	ref_objects: id:PK,name,
	ref_materials: id:PK,name, basequant,ismaterial=true,default supplier,package,aggregate_class(liquid,particles,sheets,linear,solid,item,unique)
	ref_products: id:PK,name,basequant,isproduct=true,package,aggregate_class(liquid,particles,sheets,linear,solid,item,unique),dim_width,dim_height...color
	ref_intermediate: id:PK,name,basequant,isproduct=true,ismaterial=true
	ref_base_objects: id:PK,name,basequant,isproduct=true,isbase=true,istool=?,isbuilding=?,isterritory=?
	
	doc: id:PK,doctype=0,docnum_num,docnum_template,docnum_res,phys_src,phys_dst,am_src,am_dst,phys,amount
	doc_mul_phys: id:PK,ctime:PK,docnum_num,docnum_template,docnum_res,phys_src,phys_dst,am_src,am_dst,phys,amount


	
	
	
*/


$meta_structure=Array(
Array('name' =>'id', 'sql_type' =>'bigint(20)', 'sql_null' =>0, 'sql_sequence' => 1),
Array('name' =>'parentid', 'sql_type' =>'bigint(20)', 'sql_null' =>1, 'sql_default' =>0),
Array('name' =>'xobject', 'sql_type' =>'varchar(200)', 'sql_null' =>1),
Array('name' =>'xclass', 'sql_type' =>'varchar(200)', 'sql_null' =>1),
Array('name' =>'xtype', 'sql_type' =>'varchar(200)', 'sql_null' =>1),
Array('name' =>'name', 'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'new'),
Array('name' =>'verstamp', 'sql_type' =>'timestamp', 'sql_null' =>0, 'sql_default' =>NULL, 'editor'=>'editor_statictext'),
Array('name' =>'sql_type', 'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'varchar(200)',
	'editor'=>'editor_text_autosuggest_qm','editor_config'=>"select distinct sql_type from `%tree_tmp` where sql_type!='' order by sql_type "),
Array('name' =>'sql_table', 'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>NULL,
	'editor'=>'editor_text_autosuggest_qm','editor_config'=>"select distinct sql_table from `%tree_tmp` where sql_table!='' order by sql_table "),
Array('name' =>'sql_comment', 'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>''),
Array('name' =>'sql_default', 'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>NULL),
Array('name' =>'sql_null', 'sql_type' =>'tinyint(1)', 'sql_null' =>1, 'sql_default' =>1, 'editor'=>'editor_checkbox'),
Array('name' =>'sql_sequence', 'sql_type' =>'int(1)', 'sql_null' =>0, 'sql_default' =>0, 'editor'=>'editor_checkbox'),
Array('name' =>'sql_keyname', 'sql_type' =>'varchar(200)', 'sql_null' =>0, 'sql_default' =>'',
	'editor'=>'editor_text_autosuggest_qm','editor_config'=>"select '' union (select distinct sql_keyname from `%tree_tmp` where sql_keyname!='' order by sql_keyname )"),
Array('name' =>'sql_keylen', 'sql_type' =>'int(3)', 'sql_null' =>1, 'sql_default' =>NULL),
Array('name' =>'isstored', 'sql_type' =>'int(1)', 'sql_null' =>1, 'sql_default' =>0, 'editor'=>'editor_checkbox'),
Array('name' =>'metatables', 'sql_type' =>'varchar(200)', 'sql_null' =>1, 'editor'=>'editor_text_dropdown_set', 'editor_config'=>serialize(Array('full_set'=>'shadow sellist'))),
Array('name' =>'isshared', 'sql_type' =>'int(1)', 'sql_null' =>0, 'sql_default' =>0, 'editor'=>'editor_checkbox'),
Array('name' =>'inheritedfrom', 'sql_type' =>'bigint(20)', 'sql_null' =>0, 'sql_default' =>0),
Array('name' =>'preset_value', 'sql_type' =>'text', 'sql_null' =>1),
Array('name' =>'editor', 'sql_type' =>'varchar(200)', 'sql_null' =>1),
Array('name' =>'viewer', 'sql_type' =>'varchar(200)', 'sql_null' =>1),
Array('name' =>'rel', 'sql_type' =>'bigint(20)', 'sql_null' =>1)
);














$ddc_tables[TABLE_META_TREE]=(object)
Array(
'name' => TABLE_META_TREE,
'cols' => $meta_structure,
'keys' => Array(
Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
Array('key' =>'parentid', 'name' =>'parentid', 'sub' => NULL),
Array('key' =>'name', 'name' =>'name', 'sub' => NULL)
)
);


if($_GET['init']=='init')
	ddc_gentable_o($ddc_tables[TABLE_META_TREE],$sql);




$ddc_key_special_types=Array('table','view','');
$ddc_suffixes=Array('shadow'=>';sh','sellist'=>';se');
class ddc_key
{
	public $ctbl='';
	public $cols;
	public $keys;
	public $querys;
	public $actionlist;
	public $tree;
	public $suffixes=Array();//Array('shadow'=>';sh','sellist'=>';se');
	
	function __construct()
	{
		global $ddc_suffixes;
		$this->suffixes=$ddc_suffixes;
		$this->column_compare_list['name']=1;
		$this->column_compare_list['sql_type']=1;
		$this->column_compare_list['sql_comment']=1;
		$this->column_compare_list['sql_default']=1;
		$this->column_compare_list['sql_null']=1;
		$this->column_compare_list['sql_sequence']=1;
	}
	
	function is_text_type($t)
	{
		return preg_match('/.*(CHAR|VARCHAR|BINARY|VARBINARY|BLOB|TEXT|ENUM|SET).*/i',$t);
	}
	
	
	function attach($tree,$sql)
	{
		if(! isset($sql))return 'Trying attach with NULL $sql object';
		if(! isset($tree))return 'Trying attach with NULL $tree';
		if($tree=='')return 'Trying attach with empty $tree';
		$this->sql=$sql;
		$this->tree=$tree;

	}
	
	function child_check($i,$v)
	{
		$sql=$this->sql;
		if($v==$i)return false;
		$res=$sql->query("SELECT id FROM `".$sql->esc($this->tree)."` WHERE parentid='".$i."'");
		while($row=$sql->fetchn($res))
		{
			if($row[0]==$v) return false;
			if($this->child_check($row[0],$v)==false) return false;
		}
		if($res)$sql->free($res);
		return true;
	}
	
	function set_col($id,$col,$val,$prev=-1)
	{
		global $ddc_tables;
		
		if($col=='parentid' && $this->child_check($id,$val)==false)return "trying to set parentid to child or self";
		
		
		$sql=$this->sql;
		
		
		$immed_id=new sql_immed;

		$qs=new query_gen_ext('UPDATE');
		$qs->into->exprs[]=new sql_column(NULL,$this->tree);
		$qs->set->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,$col),
			new sql_immed($val)
			));
		$qs->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'id'),
			$immed_id
			));
		$q=clone $qs;
		//get info for object
		$qf=new query_gen_ext('SELECT');
		$qf->what->exprs[]=new sql_column(NULL,NULL,'isshared');
		$qf->what->exprs[]=new sql_column(NULL,NULL,'inheritedfrom');
		$qf->what->exprs[]=new sql_column(NULL,NULL,'name');
		$qf->what->exprs[]=new sql_column(NULL,NULL,'sql_table');
		$qf->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'id'),
			new sql_immed($id)));
		$qf->from->exprs[]=new sql_column(NULL,$this->tree);
		$row=$sql->fetcha($sql->query($qf->result()));
		if($row['isshared']==1)
		{
			//fetch all shared columns with same name/table combination
			$qf->what->exprs=Array(new sql_column(NULL,NULL,'id'));
			$qf->where->exprs=Array();
			$qf->where->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'name'),
				new sql_immed($row['name'])));
			$qf->where->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'sql_table'),
				new sql_immed($row['sql_table'])));
			$qf->where->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'isshared'),
				new sql_immed(1)));
			$qf->where->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,'isstored'),
				new sql_immed(1)));
			$res=$sql->query($qf->result());
			while($r1=$sql->fetchn($res))$shared[]=$r1[0];
			if($res)$sql->free($res);
			//update only sql* and name
			if(in_array($col,Array('name','sql_type','sql_table','sql_comment','sql_default','sql_null','sql_sequence','sql_keyname','sql_keylen')))
				foreach($shared as $i)
				{
					$immed_id->val=$i;
					$res=$sql->query($qs->result());
				}
			
		}
		//fetch all columns directly inherited from this
		$qf->what->exprs=Array(new sql_column(NULL,NULL,'id'));
		$qf->where->exprs=Array();
		$qf->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'inheritedfrom'),
			new sql_immed($id)));
		$res=$sql->query($qf->result());
		while($r1=$sql->fetchn($res))$inherited[]=$r1[0];
		if($res)$sql->free($res);
		print "\n\n/* ".$qf->result(). "  */ \n\n\n";
		if(in_array($col,Array('name','sql_type','sql_comment','sql_default','sql_null','sql_sequence','sql_keyname','sql_keylen')) && is_array($inherited))
			foreach($inherited as $i)
			{
				if($i==$prev) return "Inheritance loop:".$i;
				$res=$this->set_col($i,$col,$val,($prev==-1)?$id:$prev);
			}
		
		
		//update object
		$immed_id->val=$id;
		$res=$sql->query($qs->result());
		
		return $res;
	}
	
	
	
	
	
	function compare_triggers($old,$new)
	{
		if(!is_array($old) && !is_array($new))return false;
		
		//print "<pre>";print_r($old);print_r($new);print count($old).";".count($new);print "<pre>";
		
		if(count($old)!=count($new))return true;
		$cl=Array('Trigger','Event','Table','Timing','Statement');
		foreach($old as $i => $v)
		{
			if(!is_array($old[$i]))return true;
			if(!is_array($new[$i]))return true;
			foreach($cl as $ai)
				if($new[$i][$ai]!=$v[$ai])return true;
		}
		return false;
	}


/*
################################################################################################
################################################################################################
################################################################################################

*/
	
	function compare($old)
	{
		global $ddc_tables;
		//return array of objects
		// { $type,$id,$diff:Array($attr:{$old,$new})}
		//$type: enum (+=>node created,-=>node delete,*=>node changed)
		if(!is_object($old)) return 'Error: wrong argument: not object or not ddc_key instance';
		if(get_class($old)!='ddc_key') return 'Error: wrong argument: not object or not ddc_key instance';
		$result=Array();
		$qg=new query_gen_ext('SELECT');
		$qg->from->exprs[0]=new sql_column(NULL,$this->tree,NULL,'new');
		$join->what=new sql_list('',Array(new sql_column(NULL,$old->tree,NULL,'old')));
		$join->on=new sql_expression('AND',Array(
			new sql_expression('=',Array(
				new sql_column(NULL,'new','id'),
				new sql_column(NULL,'old','id')
										))
												));
		$join->type='left outer join';
		$qg->joins->exprs[0]=$join;
		foreach($ddc_tables[TABLE_META_TREE]->cols as $c)
		{
			$qg->what->exprs[]=new sql_column(NULL,'new',$c['name'],'new.'.$c['name']);
			$qg->what->exprs[]=new sql_column(NULL,'old',$c['name'],'old.'.$c['name']);
		}
		$qg->where->exprs[0]=new sql_expression('IS',Array(
			new sql_column(NULL,'old','id'),
			new sql_null
			));
		
		
		$rs=$this->sql->query($qg->result());
		//print $qg->result();
		while($row=$this->sql->fetcha($rs))
		{
			unset($diff);
			foreach($row as $k => $v)
				if(preg_match('/^new\..*$/',$k))
					$diff[preg_replace('/^new\./','',$k)]->new=$v;
				else
					$diff[preg_replace('/^old\./','',$k)]->old=$v;
			unset($change);
			$change->type='+';
			$change->id=$row['new.id'];
			$change->diff=$diff;
			$result[]=$change;
		}
		if($rs)$this->sql->free($rs);
		
		$qg->joins->exprs[0]->type='right outer join';
		$qg->where->exprs[0]->exprs[0]->tbl='new';
		
		$rs=$this->sql->query($qg->result());
		while($row=$this->sql->fetcha($rs))
		{
			unset($diff);
			foreach($row as $k => $v)
				if(preg_match('/^new\..*$/',$k))
					$diff[preg_replace('/^new\./','',$k)]->new=$v;
				else
					$diff[preg_replace('/^old\./','',$k)]->old=$v;
			unset($change);
			$change->type='-';
			$change->id=$row['old.id'];
			$change->diff=$diff;
			$result[]=$change;
		}
		if($rs)$this->sql->free($rs);
	
		$qg->joins->exprs[0]->type='join';
		
		$or=new sql_expression('OR');
		foreach($ddc_tables[TABLE_META_TREE]->cols as $c)
			$or->exprs[]=new sql_expression('!=',Array(
				new sql_column(NULL,'new',$c['name']),
				new sql_column(NULL,'old',$c['name'])
			));
		$qg->where->exprs=Array($or);
		
		$rs=$this->sql->query($qg->result());
		while($row=$this->sql->fetcha($rs))
		{
			unset($diff);
			foreach($row as $k => $v)
				if(preg_match('/^new\..*$/',$k))
					$diff[preg_replace('/^new\./','',$k)]->new=$v;
				else
					$diff[preg_replace('/^old\./','',$k)]->old=$v;
			unset($change);
			$change->type='*';
			$change->id=$row['new.id'];
			$change->diff=$diff;
			$result[]=$change;
		}
		if($rs)$this->sql->free($rs);
		return $result;
	}
	
/*
################################################################################################
################################################################################################
################################################################################################

*/
	function check_logical()
	{
		global $ddc_key_special_types;
		$result=Array();
		/*
			Object(type='table duplicate',
		*/
		//check for duplicate tables
		$qg=new query_gen_ext('SELECT');
		$qg->from->exprs[0]=new sql_column(NULL,$this->tree,NULL,'new');
		$qg->what->exprs=Array(
			//new sql_list('count',Array(new sql_immed(1))),
			new sql_column(NULL,NULL,'name'),
			new sql_list('count',Array(new sql_column(NULL,NULL,'id')),'cnt')
			);
		
		$qg->where->exprs[0]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'sql_type'),
			new sql_immed('table')
			));
		$qg->where->exprs[1]=new sql_expression('!=',Array(
			new sql_column(NULL,NULL,'isshared'),
			new sql_immed(1)
			));
		$qg->having->exprs[1]=new sql_expression('>',Array(
			new sql_column(NULL,NULL,'cnt'),
			new sql_immed(1)
			));
		$qg->group->exprs[0]=new sql_column(NULL,NULL,'name');
		$res=$this->sql->query($qg->result());
		while($row=$this->sql->fetcha($res))
		{
			unset($r);
			$r->type='table duplicate';
			$r->name=$row['name'];
			$result[]=$r;
		}
		if($res)$this->sql->free($res);
		
		//check for duplicate columns
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		$qg->where->exprs[0]=new sql_expression('NOT ',Array(
			new sql_column(NULL,NULL,'sql_type'),
			$restypes
			));
		$qg->group->exprs[0]=new sql_column(NULL,NULL,'sql_table');
		$qg->group->exprs[1]=new sql_column(NULL,NULL,'name');
		$res=$this->sql->query($qg->result());
		while($row=$this->sql->fetcha($res))
		{
			unset($r);
			$r->type='column duplicate';
			$r->name=$row['name'];
			$result[]=$r;
		}
		if($res)$this->sql->free($res);
		
		//check for auto_increment without primary key
		$qg->group->exprs=Array();
		$qg->having->exprs=Array();
		$qg->what->exprs=Array(
			//new sql_list('count',Array(new sql_immed(1))),
			new sql_column(NULL,NULL,'name'),
			new sql_column(NULL,NULL,'id')
			);
		$qg->where->exprs[1]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'sql_sequence'),
			new sql_immed(1)
			));
		$qg->where->exprs[2]=new sql_expression('!=',Array(
			new sql_column(NULL,NULL,'sql_keyname'),
			new sql_immed('PRIMARY')
			));
		$res=$this->sql->query($qg->result());
		while($row=$this->sql->fetcha($res))
		{
			unset($r);
			$r->type='auto_increment and not primary key';
			$r->name=$row['name'];
			$r->id=$row['id'];
			$result[]=$r;
		}
		if($res)$this->sql->free($res);
		
		//check for null columns within primary key
		$qg->group->exprs=Array();
		$qg->having->exprs=Array();
		$qg->what->exprs=Array(
			//new sql_list('count',Array(new sql_immed(1))),
			new sql_column(NULL,NULL,'name'),
			new sql_column(NULL,NULL,'id')
			);
		$qg->where->exprs[1]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'sql_null'),
			new sql_immed(1)
			));
		$qg->where->exprs[2]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'sql_keyname'),
			new sql_immed('PRIMARY')
			));
		$res=$this->sql->query($qg->result());
		while($row=$this->sql->fetcha($res))
		{
			unset($r);
			$r->type='null and primary key';
			$r->name=$row['name'];
			$r->id=$row['id'];
			$result[]=$r;
		}
		if($res)$this->sql->free($res);
		
		//check for lost columns missing table definition
		$qg=new query_gen_ext('select');
		$qg->from->exprs=Array(
			new sql_column(NULL,$this->tree,NULL,'c')
		);
		$qg->what->exprs=Array(
			//new sql_list('count',Array(new sql_immed(1))),
			new sql_column(NULL,'c','id'),
			new sql_column(NULL,'c','name'),
			new sql_column(NULL,'c','sql_table'),
			);
		$join->type="left outer join";
		$join->what=new sql_column(NULL,$this->tree,NULL,'t');
		$join->on=new sql_expression('AND',Array(
				new sql_expression('=',Array(
					new sql_column(NULL,'c','sql_table'),
					new sql_column(NULL,'t','name')
				)),
				new sql_expression('=',Array(
					new sql_column(NULL,'t','sql_table'),
					new sql_column(NULL,'t','name')
				)),
				new sql_expression('=',Array(
					new sql_column(NULL,'t','sql_type'),
					new sql_immed('table')
				))/*,
				new sql_expression('=',Array(
					new sql_column(NULL,'t','isstored'),
					new sql_immed(1)
				))*/
			));
		$qg->joins->exprs=Array($join);
		$qg->where->exprs=Array(
			new sql_expression('NOT ',Array(
				new sql_column(NULL,'c','sql_type'),
				$restypes
			)),
			new sql_expression('!=',Array(
				new sql_column(NULL,'c','sql_type'),
				new sql_immed('')
			)),
			new sql_expression('=',Array(
				new sql_column(NULL,'c','isstored'),
				new sql_immed(1)
			)),
			new sql_expression('IS',Array(
				new sql_column(NULL,'t','id'),
				new sql_null
			))
		);
		//print $qg->result();
		$res=$this->sql->query($qg->result());
		while($row=$this->sql->fetcha($res))
		{
			unset($r);
			$r->type='lost column';
			$r->name=$row['name'];
			$r->id=$row['id'];
			$result[]=$r;
		}
		if($res)$this->sql->free($res);
		
		
		
		
		
		
		return $result;
	}
	
/*
################################################################################################
################################################################################################
################################################################################################

*/
	function enum_tables__($mode='')
	{
		global $ddc_key_special_types;
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		//fetch table names from column definitions
		$qt=new query_gen_ext('SELECT DISTINCT');
			$qt->from->exprs[]=new sql_column(NULL,$this->tree);
			$qt->what->exprs[]=new sql_column(NULL,NULL,'sql_table','name');
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'isstored'),new sql_immed(1)));
			$qt->where->exprs[]=new sql_expression('!=',Array(new sql_column(NULL,NULL,'sql_table'),new sql_immed('')));
			$qt->where->exprs[]=new sql_expression('NOT',Array(new sql_column(NULL,NULL,'sql_type'),$restypes));
			$qt->where->exprs[]=new sql_expression('!=',Array(new sql_column(NULL,NULL,'sql_type'),new sql_immed('')));
		$subq=new sql_subquery($qt,'names');
		$qt=new query_gen_ext('SELECT');
		$qt->from->exprs[]=$subq;
		$join->type='left outer join';
		$join->what=new sql_column(NULL,$this->tree,NULL,'rem');
		$join->on=new sql_expression('AND',Array(
			new sql_expression('=',Array(new sql_column(NULL,'names','name'),new sql_column(NULL,'rem','name'))),
			new sql_expression('=',Array(new sql_column(NULL,'rem','sql_type'),new sql_immed('table')))
			));
		$qt->joins->exprs[]=$join;
		$qt->what->exprs[]=new sql_column(NULL,'names','name');
		$qt->what->exprs[]=new sql_column(NULL,'rem','metatables');
		$table_res=$this->sql->query($qt->result());
		$this->table_mode=$mode;
		while($row=$this->sql->fetcha($table_res))
		{
			$result[]=$row['name'];
			if($mode=='all' && is_array($table_suffixes))
				{
					$subs=$this->get_subtables($row['name'],$row['metatables']);
					foreach($subs as $subt)$result[]=$subt;
				}
		}
		if($table_res)$this->sql->free($table_res);
		return $result;
	}
	
	function enum_tables($mode='')
	{
		global $ddc_key_special_types;
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		//fetch table names from column definitions
		$qt=new query_gen_ext('SELECT');
			$qt->from->exprs[]=new sql_column(NULL,$this->tree);
			$qt->what->exprs[]=new sql_column(NULL,NULL,'sql_table','name');
			$qt->what->exprs[]=new sql_column(NULL,NULL,'metatables');
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'isstored'),new sql_immed(1)));
			$qt->where->exprs[]=new sql_expression('!=',Array(new sql_column(NULL,NULL,'sql_table'),new sql_immed('')));
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'sql_table'),new sql_column(NULL,NULL,'name')));
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'sql_type'),new sql_immed('table')));
		$table_res=$this->sql->query($qt->result());
		$this->table_mode=$mode;
		while($row=$this->sql->fetcha($table_res))
		{
			$result[]=$row['name'];
			if($mode=='all' && is_array($table_suffixes))
				{
					$subs=$this->get_subtables($row['name'],$row['metatables']);
					foreach($subs as $subt)$result[]=$subt;
				}
		}
		if($table_res)$this->sql->free($table_res);
		return $result;
	}
	
/*
################################################################################################
################################################################################################
################################################################################################

*/
	function get_subtables($tbl,$metatables)
	{
		$table_suffixes=explode(' ',$metatables);
		$result=Array();
		foreach($table_suffixes as $ss)
			if($this->suffixes[$ss]!='')
				$result[$ss]=$tbl.$this->suffixes[$ss];
		return $result;
	}
	
	#####-------------------------------------------------------!!!!!!!!!!!!!!!!!!!!!!!
	
	
/*
################################################################################################
################################################################################################
################################################################################################

*/
	function enum_columns_0($tbl)
	{
		global $ddc_tables;
		global $ddc_key_special_types;
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		$qt=new query_gen_ext('SELECT');
			$qt->from->exprs[]=new sql_column(NULL,$this->tree);
			//$qt->what->exprs[]=new sql_column(NULL,NULL,'name');
			$qt->what->exprs[]=new sql_list('min',Array(new sql_column(NULL,NULL,'id')),'id');
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'isstored'),new sql_immed(1)));
			$qt->where->exprs[]=new sql_expression('!=',Array(new sql_column(NULL,NULL,'sql_type'),new sql_immed('')));
			$qt->where->exprs[]=new sql_expression('NOT',Array(new sql_column(NULL,NULL,'sql_type'),$restypes));
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'sql_table'),new sql_immed($tbl)));
			$qt->group->exprs[]=new sql_column(NULL,NULL,'name');
		$sub=new sql_subquery($qt,'names');
			
		$qt=new query_gen_ext('SELECT');
			$qt->from->exprs[]=$sub;
			$qt->from->exprs[]=new sql_column(NULL,$this->tree,NULL,'rem');
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,'names','id'),new sql_column(NULL,'rem','id')));
//			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,'rem','sql_table'),new sql_immed($tbl)));
			foreach($ddc_tables[TABLE_META_TREE]->cols as $col)
				$qt->what->exprs[]=new sql_column(NULL,'rem',$col['name'],$col['name']);
		$columns_res=$this->sql->query($qt->result());
		$result=$this->sql->fetchma($columns_res);
		if($columns_res)$this->sql->free($columns_res);
		return $result;
	}
	
/*
################################################################################################
################################################################################################
################################################################################################

*/
	function enum_columns($tbl_t)
	{
		global $ddc_tables;
		//if($this->columns_res)$this->sql->free($this->columns_res);
		//no sharing at this moment,debug basic fuctionality first
		$tbl=$tbl_t;
		foreach($this->suffixes as $sn => $ss)
			if(preg_match('/'.preg_quote($ss,'/').'$/',$tbl_t))
			{
				$tbl=preg_replace('/'.preg_quote($ss,'/').'$/','',$tbl_t);
				$detected_suffix=$sn;
			}
		$result=$this->enum_columns_0($tbl);
		if(isset($detected_suffix))
			return $this->enum_columns_suffix($tbl_t,$detected_suffix,$result);
		return $result;
		//$this->table_mode=$mode;
	}
	
	
/*
################################################################################################
################################################################################################
################################################################################################

*/
	function enum_columns_suffix($tbl,$detected_suffix,$result)
	{
		switch($detected_suffix)
		{
			case 'sellist':
				$nres=Array();
				foreach($result as $r)
					if($r['sql_keyname']=='PRIMARY')
					{
						unset($r['sql_sequence']);
						unset($r['sql_default']);
						$r['sql_null']=0;
						$nres[]=$r;
					}
				$nres[]=Array('name'=>'uid','sql_type'=>'bigint(20)','sql_default'=>'0','sql_table'=>$tbl,'sql_null'=>0,'sql_keyname'=>'PRIMARY','isstored'=>1);
				$nres[]=Array('name'=>'preset','sql_type'=>'bigint(20)','sql_default'=>'0','sql_table'=>$tbl,'sql_null'=>0,'sql_keyname'=>'PRIMARY','isstored'=>1);
				$nres[]=Array('name'=>'selected','sql_type'=>'tinyint(1)','sql_default'=>'0','sql_table'=>$tbl,'sql_null'=>1,'isstored'=>1);
				return $nres;
				break;
			case 'shadow':
				$nres=Array();
				reset($result);
				foreach($result as $r)
				{
					unset($r['sql_sequence']);
					if($r['name']=='muid')
					{
						$r['sql_keyname']='PRIMARY';
						$got_muid=true;
					}
					if($r['name']=='mtime')
					{
						$r['sql_keyname']='PRIMARY';
						$got_mtime=true;
					}
					$nres[]=$r;
				}
				$nres[]=Array('name'=>'shadow_type','sql_type'=>'int(10)','sql_default'=>'0','sql_table'=>$tbl,'sql_null'=>0, 'isstored'=>1,'sql_keyname'=>'PRIMARY','sql_comment'=>'-1 - shadow, 0 - editing, >0 - preset');
				if(!isset($got_muid))
					$nres[]=Array('name'=>'muid','sql_type'=>'bigint(20)','sql_default'=>'0','sql_table'=>$tbl,'sql_null'=>0,'isstored'=>1,'sql_keyname'=>'PRIMARY');
				if(!isset($got_mtime))
					$nres[]=Array('name'=>'mtime','sql_type'=>'timestamp','sql_null'=>0,'sql_table'=>$tbl, 'isstored'=>1,'sql_keyname'=>'PRIMARY');
								
								return $nres;
				break;
		}
		return NULL;
	}
	
/*
CREATE TRIGGER `test2_update` before update ON `test2`
FOR EACH ROW BEGIN
  INSERT INTO `test2;sh` Set id = OLD.id, string_test = OLD.string_test, m1 = OLD.m1, shadow_type=-1, muid = @UID;
END;	
	
	*/
	
	function enum_triggers($tbl,$suff=NULL)
	{
		global $ddc_key_special_types;
		global $ddc_tables;
		//if($this->columns_res)$this->sql->free($this->columns_res);
		//no sharing at this moment,debug basic fuctionality first
		//$tbl=$tbl_t;
		if(!isset($suff))
		{
			$qt=new query_gen_ext('SELECT');
				$qt->from->exprs[]=new sql_column(NULL,$this->tree);
				$qt->what->exprs[]=new sql_column(NULL,NULL,'metatables');
				$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'isstored'),new sql_immed(1)));
				$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'sql_type'),new sql_immed('table')));
				$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'sql_table'),new sql_immed($tbl)));
				$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'name'),new sql_immed($tbl)));
				$qt->lim_count=1;
			$suff=explode(' ',$this->sql->fetch1($this->sql->query($qt->result())));
		}else $suff=explode(' ',$suff);
		$result=$this->enum_columns_0($tbl);
		foreach($suff as $ss)
		switch($ss)
		{
			case 'sellist':
			//maybe clean up rows on delete
				break;
			case 'shadow':
				$nres=Array();
				if(!is_array($result))break;
				reset($result);
				$set='';
				$skipmuid=false;
				foreach($result as $r)
				{
					if($set!='')$set.=', ';
					$set.="`".$this->sql->esc($r['name'])."` = OLD.`".$this->sql->esc($r['name'])."` ";
					if($r['name']=='muid')$skipmuid=true;
				}
				if($set!='')$set.=', ';
				$set.="`shadow_type` = 0 ";
				if(!$skipmuid)$set.=", `muid` = @UID ";
				
				$nres[$tbl."_BEFORE_UPDATE"]=Array(
				'Trigger'=>$tbl."_BEFORE_UPDATE",
				'Event'=>'UPDATE',
				'Table'=>$tbl,
				'Timing'=>'BEFORE',
				'Statement'=>'BEGIN INSERT INTO `'.$this->sql->esc($tbl.$this->suffixes[$ss])."` SET ".$set." ON DUPLICATE KEY UPDATE ".$set."; END"
				);
				$nres[$tbl."_BEFORE_DELETE"]=Array(
				'Trigger'=>$tbl."_BEFORE_DELETE",
				'Event'=>'DELETE',
				'Table'=>$tbl,
				'Timing'=>'BEFORE',
				'Statement'=>'BEGIN INSERT INTO `'.$this->sql->esc($tbl.$this->suffixes[$ss])."` SET ".$set." ON DUPLICATE KEY UPDATE ".$set." ; END"
				);
				return $nres;
				break;
		}
		return NULL;
		
	}
	
	
	
	
	
	
	
	
	
	
/*	
	function enum_keys($tbl_t)
	{
		global $ddc_key_special_types;
		global $ddc_tables;
		//if($this->keys_res)$this->sql->free($this->keys_res);
		//no sharing at this moment,debug basic fuctionality first
		$tbl=$tbl_t;
		foreach($this->suffixes as $sn => $ss)
			if(preg_match('/'.preg_quote($ss,'/').'$/',$tbl_t))
			{
				$tbl=preg_replace('/'.preg_quote($ss,'/').'$/','',$tbl_t);
				$detected_suffix=$sn;
			}
		
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		$qt=new query_gen_ext('SELECT DISTINCT');
			$qt->from->exprs[]=new sql_column(NULL,$this->tree);
			$qt->what->exprs[]=new sql_column(NULL,NULL,'name');
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'isstored'),new sql_immed(1)));
			$qt->where->exprs[]=new sql_expression('!=',Array(new sql_column(NULL,NULL,'sql_type'),new sql_immed('')));
			$qt->where->exprs[]=new sql_expression('NOT',Array(new sql_column(NULL,NULL,'sql_type'),$restypes));
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'sql_table'),new sql_immed($tbl)));
		$sub=new sql_subquery($qt,'names');
		$qt=new query_gen_ext('SELECT');
			$qt->from->exprs[]=$sub;
			$qt->from->exprs[]=new sql_column(NULL,$this->tree,NULL,'rem');
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,'names','name'),new sql_column(NULL,'rem','name')));
			$qt->where->exprs[]=new sql_expression('!=',Array(new sql_column(NULL,'rem','sql_keyname'),new sql_immed('')));
			$qt->what->exprs[]=new sql_column(NULL,'rem','name','col');
			$qt->what->exprs[]=new sql_column(NULL,'rem','sql_keyname','name');
			$qt->what->exprs[]=new sql_column(NULL,'rem','sql_keylen','sub');
		$keys_res=$this->sql->query($qt->result());
		//$this->table_mode=$mode;
		$result=$this->sql->fetchma($keys_res);
		if($keys_res)$this->sql->free($keys_res);
		if(isset($detected_suffix))
			return $this->enum_keys_suffix($tbl,$detected_suffix,$result);
		else
			return $result;
	}
*/
	
	
	function col_different($str_col,$sql_col)
	{
		$diff=false;
		if($str_col['name']!=$sql_col['Field'])
		{
			$diff=true;
			$ret['name']=Array($sql_col['Field'],$str_col['name']);
		}
		if($str_col['sql_type']!=$sql_col['Type'])
		{
			$diff=true;
			$ret['sql_type']=Array($sql_col['Type'],$str_col['sql_type']);
		}
		if($str_col['sql_sequence']!=(($sql_col['Extra']=='auto_increment')?1:0))
		{
			$diff=true;
			$ret['sql_sequence']=Array(($sql_col['Extra']=='auto_increment')?1:0,$str_col['sql_sequence']);
		}
		if($str_col['sql_comment']!=$sql_col['Comment'])
		{
			$diff=true;
			$ret['sql_comment']=Array($sql_col['Comment'],$str_col['sql_comment']);
		}
		if($str_col['sql_null']!=(($sql_col['Null']=='YES')?1:0))
		{
			$diff=true;
			$ret['sql_null']=Array((($sql_col['Null']=='YES')?1:0),$str_col['sql_null']);
		}
		if($str_col['sql_default']!=$sql_col['Default'] && $sql_col['Type']!='timestamp')
		{
			$diff=true;
			$ret['sql_default']=Array($sql_col['Default'],$str_col['sql_default']);
		}
		if($diff)return $ret;
		else return false;
	}
	
	function sql_drop_view($name)
	{
		return "DROP VIEW `".$this->sql->esc($name)."`";
	}
	
	function sql_drop_table($name)
	{
		return "DROP TABLE `".$this->sql->esc($name)."`";
	}
	
	function sql_drop_col($tbl,$name)
	{
		return "ALTER TABLE `".$this->sql->esc($tbl)."` DROP COLUMN `".$this->sql->esc($name)."`";
	}
	
	function sql_mod_col($tbl,$col,$force=false)
	{
		return "ALTER TABLE `".$this->sql->esc($tbl)."` MODIFY COLUMN ".$this->col_def_str($col,$force);
	}
	function sql_remove_auto_increment($tbl,$col)
	{
		unset($col['Extra']);
		return "ALTER TABLE `".$this->sql->esc($tbl)."` MODIFY COLUMN ".$this->col_def_sql($col);
	}
	function sql_add_col($tbl,$col)
	{
		return Array("ALTER TABLE `".$this->sql->esc($tbl)."` ADD COLUMN ".$this->col_def_str($col,false));
	}
	
	function sql_drop_trigger($t)
	{
		return "DROP TRIGGER `".$this->sql->esc($t)."`";
	}
	
	function sql_create_trigger($t)
	{
		/*CREATE TRIGGER `test2_update` before update ON `test2`
FOR EACH ROW BEGIN
  INSERT INTO `test2;sh` Set id = OLD.id, string_test = OLD.string_test, m1 = OLD.m1, shadow_type=-1, muid = @UID;
END;	*/
	return "CREATE TRIGGER `".$this->sql->esc($t['Table']."_".$t['Timing']."_".$t['Event'])."` ".
		$t['Timing']." ".$t['Event']." ON `".$this->sql->esc($t['Table'])."` ".
		"FOR EACH ROW ".$t['Statement'];

	}
	
	function sql_drop_key($tbl,$name)
	{
		if($name=='PRIMARY')
			return "ALTER TABLE `".$this->sql->esc($tbl)."` DROP PRIMARY KEY";
		else
			return "ALTER TABLE `".$this->sql->esc($tbl)."` DROP KEY `".$this->sql->esc($name)."`";
	}
	
	function sql_add_key($tbl,$keyname,$keystruct)
	{
		$a='';
		if(!is_array($keystruct))return '';
		foreach($keystruct as $coln => $sub)
		{
			$sub=($sub==0)?'':"(".$sub.")";
			if($a !='')$a.=', ';
			$a.='`'.$this->sql->esc($coln).'`'.$sub;
		}
		if($keyname=='PRIMARY')
			return "ALTER TABLE `".$this->sql->esc($tbl)."` ADD PRIMARY KEY (".$a.")";
		else
			return "ALTER TABLE `".$this->sql->esc($tbl)."` ADD KEY `".$this->sql->esc($keyname)."` (".$a.")";
	}
	
	function col_def_sql($cols)
	{
		$nt='`'.$this->sql->esc($cols['Field']).'` '.$cols['Type'];
		if($cols['Null']=='NO') $nu=" NOT NULL";
		if($cols['Extra']!='') $ex=" ".$cols['Extra'];
		
		if(isset($cols['Default'])) $def=" DEFAULT '".$this->sql->esc($cols['Default'])."'";
		else if($cols['Null']=='YES' && $cols['Type'] != 'timestamp') $def=" DEFAULT NULL";
		if(! $this->is_text_type($cols['Type']) && $cols['Default']==='')unset($def);
		
		if($cols['Comment']!='') $co=" COMMENT '".$this->sql->esc($cols['Comment'])."'";
		return $nt.$nu.$ex.$def.$co;
	}
	
	function col_def_str($cols,$force)
	{
		$nt='`'.$this->sql->esc($cols['name']).'` '.$cols['sql_type'];
		if($cols['sql_null']==0) $nu=" NOT NULL";
		if($force)if($cols['sql_sequence']==1) $ex=" auto_increment";
		
		if(isset($cols['sql_default'])) $def=" DEFAULT '".$this->sql->esc($cols['sql_default'])."'";
		else if($cols['sql_null'] && $cols['sql_type'] != 'timestamp') $def=" DEFAULT NULL";
		if(! $this->is_text_type($cols['sql_type']) && $cols['sql_default']==='')unset($def);
		
		if($cols['sql_comment']!='') $co=" COMMENT '".$this->sql->esc($cols['sql_comment'])."'";
		return $nt.$nu.$ex.$def.$co;
	}
	
	function sql_create_table($tbl,$col_s=NULL)
	{
		$q="";
		if(!is_array($col_s))$col_s=$this->enum_columns($tbl);
		if(is_array($col_s)) foreach($col_s as $cols)
		{
			if($cols['sql_keyname']!='')$keys[$cols['sql_keyname']][$cols['name']]=$cols['sql_keylen'];
			if($q != "")$q.=', ';
			$q.=$this->col_def_str($cols,true);
		}
		$k='';
		if(is_array($keys))
		foreach($keys as $key => $rw)
		{
			if($key=='PRIMARY')
				$tk="PRIMARY KEY (";
			else
				$tk="KEY (";
			$cols='';
			foreach($rw as $col => $sub)
			{
				if($cols != '')$cols.=',';
				$cols.="`".$this->sql->esc($col)."`";
				if($sub)$cols.="(".$sub.")";
			}
			if($k != '')$k.=',';
			$k.=$tk.$cols.')';
		}
		if($k != '') $q .= ", $k";
		return "CREATE TABLE `".$this->sql->esc($tbl)."` ($q)";
		
		
	}
	
	function str_drop_table($tbl)
	{
		return Array("DELETE FROM `".$this->sql->esc($this->tree)."` WHERE sql_table='".$this->sql->esc($tbl)."'");
	}

	function str_add_col($tbl,$name,$col)
	{
		return Array("INSERT INTO `".$this->sql->esc($this->tree)."` SET".
			" sql_table='".$this->sql->esc($tbl)."',".
			" name='".$this->sql->esc($name)."',".
			" sql_type='".$this->sql->esc($col['Type'])."',".
			" sql_comment='".$this->sql->esc($col['Comment'])."',".
			" sql_default='".$this->sql->esc($col['Default'])."',".
			" sql_null=".(($col['Null']=='YES')?1:0).",".
			" sql_sequence='".(($col['Extra']=='auto_increment')?1:0)."',".
			" isstored=1".
			""
			);
	}

	function str_set_col($tbl,$name,$col)
	{
		$r="INSERT INTO `".$this->sql->esc($this->tree)."` SET";
		$set=
			" sql_table='".$this->sql->esc($tbl)."',".
			" name='".$this->sql->esc($name)."',".
			" sql_type='".$this->sql->esc($col['sql_type'])."',".
			" sql_comment='".$this->sql->esc($col['sql_comment'])."',".
			" sql_default='".$this->sql->esc($col['sql_default'])."',".
			" sql_null=".$col['sql_null'].",".
			" sql_sequence='".(($col['Extra']=='auto_increment')?1:0)."'".
			"";
		$r.=$set." ON DUPLICATE KEY UPDATE ".$set;
		return Array($r);
	}
	
	function str_del_col($tbl,$col)
	{
		global $ddc_key_special_types;
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		return Array("DELETE FROM `".$this->sql->esc($this->tree)."` WHERE sql_table='".$this->sql->esc($tbl)."' AND".
			" name='".$this->sql->esc($tbl)."' AND".
			" sql_type NOT ".$restypes->result()
		);
	}
	
	function str_set_key($tbl,$keyname,$keystruct)
	{
		global $ddc_key_special_types;
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		
		$ret[]="UPDATE `".$this->sql->esc($this->tree)."` SET".
		"  sql_keyname='', ".
		"  sql_keylen='' WHERE".
		"  sql_table='".$this->sql->esc($tbl)."' AND".
		"  sql_type NOT ".$restypes->result()
		;
		$a='';
		if(is_array($keystruct))
		foreach($keystruct as $coln => $sub)
		{
		$r="INSERT INTO `".$this->sql->esc($this->tree)."` SET";
		$set=
			" sql_table='".$this->sql->esc($tbl)."',".
			" name='".$this->sql->esc($coln)."',".
			" sql_keyname='".$this->sql->esc($keyname)."',".
			" sql_keylen='".$this->sql->esc($sub)."'".
			"";
		$r.=$set." ON DUPLICATE KEY UPDATE ".$set;
		$ret[]=$r;
		}
		
		return $ret;
	}
	
	
/*
################################################################################################
################################################################################################
################################################################################################

*/
	function check_db()
	{
		global $ddc_key_special_types;
		$result=Array();
		//find missing tables
			//suugested solutions: delete table from definition,create table in sql
			//enum tables from sql
			//WARNING ARRAY MUST FIT IN MEMORY
		$res=$this->sql->query("SHOW FULL TABLES");
		while($row=$this->sql->fetchn($res))
		{
			$tbl_sql[$row[0]]=$row[1];
		}
		if($res)$this->sql->free($res);
		
		//enum triggers from sql
		$res=$this->sql->query("SHOW TRIGGERS");
		while($row=$this->sql->fetcha($res))
		{
			$triggers_sql[$row['Table']][$row['Trigger']]=$row;
		}
		if($res)$this->sql->free($res);
		
		//enum triggers from meta
		$tbl_meta=$this->enum_tables('all');
		if(is_array($tbl_meta))foreach($tbl_meta as $tbl)
		{
			//table do not exist
			if($tbl_sql[$tbl]!='BASE TABLE')
			{
				unset($r);
				$r->type='+t';
				$r->descr=$tbl;
				unset($querylist);
				if($tbl_sql[$tbl]=='VIEW')
					$querylist[]=$this->sql_drop_view($tbl);//returns single query		+
				$querylist[]=$this->sql_create_table($tbl);//returns single query			
				$r->sql_change=$querylist;
				$r->meta_change=$this->str_drop_table($tbl);//returns array of querys
				$result[]=$r;
			}
			
			//table exists
			if($tbl_sql[$tbl]=='BASE TABLE')
			{
				$result_len=count($result);
				//enum keys from  sql
				//WARNING ARRAY MUST FIT IN MEMORY
				$res=$this->sql->query("SHOW KEYS FROM `".$this->sql->esc($tbl)."`");
				unset($key_sql);
				while($row=$this->sql->fetcha($res))
					$key_sql[$row['Key_name']][$row['Column_name']]=isset($row['Sub_part'])?intval($row['Sub_part']):0;
				if($res)$this->sql->free($res);
				
				//enum columns from  sql
				//WARNING ARRAY MUST FIT IN MEMORY
				$res=$this->sql->query("SHOW FULL COLUMNS FROM `".$this->sql->esc($tbl)."`");
				unset($col_sql);
				while($row=$this->sql->fetcha($res))
					$col_sql[$row['Field']]=$row;
				if($res)$this->sql->free($res);
				
				//enum columns from  structure
				$col_meta=$this->enum_columns($tbl);
				//if($tbl=='test2;sh'){print "<pre>";print_r($col_meta);print "</pre>";};//debug
				unset($key_meta);
				if(is_array($col_meta))
					foreach($col_meta as $kk)
						if($kk['sql_keyname']!='' && $kk['isstored']==1)
							$key_meta[$kk['sql_keyname']][$kk['name']]=isset($kk['sql_keylen'])?intval($kk['sql_keylen']):0;
				/*print "<pre>";
				print_r($key_meta);
				print "</pre>";*/
				
				
				
				//drop differing keys
				/*
				unset($key_meta);
				$this->enum_keys($tbl);
				while($key=$this->fetch_key())
					$key_meta[$key['name']][$key['col']]=intval($key['sub']);
				*/
				$key_notchanged=true;
				if(is_array($key_sql))foreach($key_sql as $key_sql_i =>$key_sql_v)
				{
					$same=true;
					foreach($key_sql_v as $key_col =>$keysub)
						if($key_meta[$key_sql_i][$key_col]!==$keysub)$same=false;
					if(!$same)
					{
						unset($r);
						unset($sql_l);
						if($key_sql_i=='PRIMARY')
							foreach($col_sql as $col)
								if($col['Extra']=='auto_increment')
									$sql_l[]=$this->sql_remove_auto_increment($tbl,$col);
						$r->type='-k/';
						$r->descr=$tbl.' : '.$key_sql_i;
						$sql_l[]=$this->sql_drop_key($tbl,$key_sql_i);
						$r->sql_change=$sql_l;
						$r->meta_change=$this->str_set_key($tbl,$key_sql_i,$key_sql_v);
						$result[]=$r;
						$key_notchanged=false;
					}
				}
				if(is_array($key_meta))foreach($key_meta as $key_sql_i =>$key_sql_v)
				{
					$same=true;
					foreach($key_sql_v as $key_col =>$keysub)
						if($key_sql[$key_sql_i][$key_col]!==$keysub)
							$same=false;
					if(!$same && isset($key_sql[$key_sql_i]))
					{
						unset($r);
						unset($sql_l);
						if($key_sql_i=='PRIMARY')
							foreach($col_sql as $col)
								if($col['Extra']=='auto_increment')
									$sql_l[]=$this->sql_remove_auto_increment($tbl,$col);
						$r->type='-k\\';
						$r->descr=$tbl.' : '.$key_sql_i;
						$sql_l[]=$this->sql_drop_key($tbl,$key_sql_i);
						$r->sql_change=$sql_l;
						$r->meta_change=$this->str_set_key($tbl,$key_sql_i,$key_sql[$key_sql_i]);
						$result[]=$r;
					}
					if(!$same)
						$key_notchanged=false;
				}
				
				
				
				
				
				
				
				
				if(is_array($col_meta))foreach($col_meta as $col)
				{
					//find missing columns
					if(!isset($col_sql[$col['name']]))
					{
						//TODO
					unset($r);
					$r->type='+c';
					$r->descr=$tbl.' : '.$col['name'];
					$r->sql_change=$this->sql_add_col($tbl,$col);
					$r->meta_change=$this->str_del_col($tbl,$col['name'],$col);
					$result_2[]=$r;
						
					}
					//find changed columns
					if(isset($col_sql[$col['name']])&&$this->col_different($col,$col_sql[$col['name']]))
					{
						//TODO
					unset($r);
					$r->type='*c';
					$r->descr=$tbl.'.'.$col['name'];
					$r->sql_change=Array($this->sql_mod_col($tbl,$col,$key_notchanged));
					$r->meta_change=$this->str_set_col($tbl,$col['name'],$col);
					$r->difference=$this->col_different($col,$col_sql[$col['name']]);
					$result_1[]=$r;
						
					}
					if(isset($col_sql[$col['name']]))$col_sql[$col['name']]['wasdescribed']=1;
				}
				//find undescribed columns
				if(is_array($col_sql))foreach($col_sql as $col)if(!isset($col['wasdescribed']))
				{
					//TODO
					unset($r);
					$r->type='-c';
					$r->descr=$tbl.' : '.$col['Field'];
					$r->sql_change=Array($this->sql_drop_col($tbl,$col['Field']));
					$r->meta_change=$this->str_add_col($tbl,$col['Field'],$col);
					$result[]=$r;
				}
				unset($r);
				if(is_array($result_1))foreach($result_1 as $r)$result[]=$r;
				if(is_array($result_2))foreach($result_2 as $r)$result[]=$r;
				unset($result_1);
				unset($result_2);
				
				//add new indexes
				if(is_array($key_sql))foreach($key_sql as $key_sql_i =>$key_sql_v)
				{
					$same=true;
					foreach($key_sql_v as $key_col =>$keysub)
						if($key_meta[$key_sql_i][$key_col]!==$keysub)$same=false;
					if(!$same)
					{
						unset($r);
						$r->type='+k';
						$r->descr=$tbl.' : '.$key_sql_i;
						$r->sql_change=Array($this->sql_add_key($tbl,$key_sql_i,$key_meta[$key_sql_i]));
						$r->meta_change=$this->str_set_key($tbl,$key_sql_i,$key_sql_v);
						if($key_sql_i=='PRIMARY' && is_array($col_meta))
							foreach($col_meta as $col)
								if($col['sql_sequence']==1)
									$r->sql_change[]=$this->sql_mod_col($tbl,$col,true);
						$result[]=$r;
					}
				}
				if(is_array($key_meta))foreach($key_meta as $key_sql_i =>$key_sql_v)
				{
					$same=true;
					if(is_array($key_sql_v))foreach($key_sql_v as $key_col =>$keysub)
						if($key_sql[$key_sql_i][$key_col]!==$keysub)$same=false;
					if(!$same)
					{
						unset($r);
						$r->type='+k';
						$r->descr=$tbl.' : '.$key_sql_i;
						$r->sql_change=Array($this->sql_add_key($tbl,$key_sql_i,$key_sql_v));
						if($key_sql_i=='PRIMARY')
							foreach($col_meta as $col)
								if($col['sql_sequence']==1)
									$r->sql_change[]=$this->sql_mod_col($tbl,$col,true);
						$r->meta_change=$this->str_set_key($tbl,$key_sql_i,$key_sql[$key_sql_i]);
						$result[]=$r;
					}
				}
				
				
			}
			
			
			$triggers_meta=$this->enum_triggers($tbl);
			
			if($result_len!=count($result) ||$this->compare_triggers($triggers_sql[$tbl],$triggers_meta))
			{
				//print $this->compare_triggers($triggers_sql[$tbl],$triggers_meta);
				if(is_array($triggers_sql[$tbl]))
				foreach($triggers_sql[$tbl] as $t)
				{
					unset($r);
					$r->type='-t';
					$r->descr=$tbl.' : '.$t['Trigger'];
					$r->sql_change=Array($this->sql_drop_trigger($t['Trigger']));
					$r->meta_change=Array();
					$result[]=$r;
				}
				if(is_array($triggers_meta))
				foreach($triggers_meta as $t)
				{
					unset($r);
					$r->type='+t';
					$r->descr=$tbl.' : '.$t['Trigger'];
					$r->sql_change=Array($this->sql_create_trigger($t));
					$r->meta_change=Array();
					$result[]=$r;
				}
			}
			
		}
		//find changed views
		//find changed triggers
		
		
		return $result;
	}
	
####################################################################################	
	
	
	function meta_from_sql_0($id)
	{
		global $ddc_key_special_types;
		$res=$this->sql->query(
			"SELECT name,sql_table,sql_type,isstored,parentid FROM `".$this->sql->esc($this->tree)."` WHERE id='".$this->sql->esc($id)."'");
		$row=$this->sql->fetcha($res);
		if($res)$this->sql->free($res);
		if(!isset($row) || $row==false)return false;
		if($row['sql_type']!='table' && in_array($row['sql_type'],$ddc_key_special_types))return false;
		if($row['sql_table']=='' || $row['name']=='')return false;
		if($row['sql_type']!='table') $like=" LIKE '".$row['name']."'";
		$res=$this->sql->query(
			"SHOW FULL COLUMNS FROM `".$this->sql->esc($row['sql_table'])."`".$like);
		while($sr=$this->sql->fetcha($res))
		{
			$res1=$this->sql->query(
				"SHOW KEYS FROM `".$this->sql->esc($row['sql_table'])."`");
			while($sk=$this->sql->fetcha($res1))
				if($sk['Column_name']==$sr['Field']) break;
			if($res1)$this->sql->free($res1);
			
			if($row['sql_type']!='table')
			{
				$res2=$this->sql->query(
					"UPDATE `".$this->sql->esc($this->tree)."` SET sql_type='".$this->sql->esc($sr['Type'])."',".
									"  sql_comment='".$this->sql->esc($sr['Comment'])."',".
									"  sql_default='".$this->sql->esc($sr['Default'])."',".
									"  sql_null='".(($sr['Null']=='YES')?1:0)."',".
									"  sql_sequence='".(($sr['Extra']=='auto_increment')?1:0)."',".
									"  sql_keyname='".$this->sql->esc($sk['Key_name'])."',".
									"  sql_keylen='".$this->sql->esc($sk['Sub_part'])."' ".
					"WHERE			id='".$this->sql->esc($id)."'");
				return $res2;
			}else{
				$res2=$this->sql->query(
					"INSERT INTO `".$this->sql->esc($this->tree)."` SET sql_type='".$this->sql->esc($sr['Type'])."',".
									"  name='".$this->sql->esc($sr['Field'])."',".
									"  sql_table='".$this->sql->esc($row['sql_table'])."',".
									"  sql_comment='".$this->sql->esc($sr['Comment'])."',".
									"  sql_default='".$this->sql->esc($sr['Default'])."',".
									"  sql_null='".(($sr['Null']=='YES')?1:0)."',".
									"  sql_sequence='".(($sr['Extra']=='auto_increment')?1:0)."',".
									"  sql_keyname='".$this->sql->esc($sk['Key_name'])."',".
									"  sql_keylen='".$this->sql->esc($sk['Sub_part'])."', ".
									"  parentid='".$this->sql->esc($id)."'");
			}
		}
		if($res)$this->sql->free($res);
		return $res2;
	}
	
	
	function tables_from_meta_a()
	{
		global $ddc_key_special_types;
		$result=Array();
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		//fetch tables from colmn defs
		$qt=new query_gen_ext('SELECT DISTINCT');
			$qt->from->exprs[]=new sql_column(NULL,$this->tree);
			$qt->what->exprs[]=new sql_column(NULL,NULL,'sql_table','name');
			$qt->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'isstored'),new sql_immed(1)));
			$qt->where->exprs[]=new sql_expression('!=',Array(new sql_column(NULL,NULL,'sql_table'),new sql_immed('')));
			$qt->where->exprs[]=new sql_expression('NOT',Array(new sql_column(NULL,NULL,'sql_type'),$restypes));
			$qt->where->exprs[]=new sql_expression('!=',Array(new sql_column(NULL,NULL,'sql_type'),new sql_immed('')));
		$q1=new query_gen_ext('SELECT');
		$q1->from->exprs[]=new sql_column(NULL,$this->tree);
		$q1->where[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'sql_type'),new sql_immed('table')));
		$q1->where[]=new sql_expression('!=',Array(new sql_column(NULL,NULL,'metatables'),new sql_immed('')));
		$q1->what=new sql_column(NULL,NULL,'name');
		$res=$this->sql->query($qt->result());
		
		$this->table_mode=$mode;
		
	}
	
	
	
	
	//function save_unconditional($
	
	
	
/*
################################################################################################
################################################################################################
################################################################################################

*/
		
	function enum_tables_x()
	{
		$x_q=new query_gen_ext('SELECT');
		$x_q->from->exprs[]=new sql_column(NULL,$this->tree);
		$x_q->what->exprs[]=new sql_column(NULL,NULL,'id');
		$x_q->what->exprs[]=new sql_column(NULL,NULL,'name');
		$x_q->what->exprs[]=new sql_column(NULL,NULL,'metatables');
		$x_q->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'sql_type'),
			new sql_immed('table')
			));
		$x_q->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'isstored'),
			new sql_immed(1)
			));
		$res=$this->sql->query($x_q->result());
		while($row=$this->sql->fetcha($res))
		{
			unset($n);
			$n->name=$row['name'];
			$n->id=$row['id'];
			$n->metatable='';
			$n->metatables=$row['metatables'];
			$ret->by_id[$n->id]['']=$n;
			$ret->by_name[$n->name]=$n;
			$subs=$this->get_subtables($n->name,$n->metatables);
			foreach($subs as $meta => $name)
			{
				unset($ns);
				$ns->name=$name;
				$ns->id=$n->id;
				$ns->metatable=$meta;
				$ret->by_id[$n->id][$meta]=$ns;
				$ret->by_name[$ns->name]=$ns;
				
			}
		}
		$this->sql->free($res);
		unset($this->tables);
		$this->tables=$ret;
		//return $ret;
		
	}
	
	function drop_tables_id(&$ret,$tbl_l)
	{
		//drop both main and meta tables
		$triggers=$this->enum_triggers($tbl_l['']->name,$tbl_l['']->metatables);
		if(is_array($triggers))foreach($triggers as $ti =>$tr)
			$ret[]=(Object)Array("id"=>$tbl_l['']->id,"type"=>"-r","query"=>"DROP TRIGGER `".$this->sql->esc($ti)."`");
		
		//drop all tables subtables
		foreach($tbl_l as $ti => $to)
			$ret[]=(Object)Array("id"=>$to->id,"type"=>"-t","query"=>"DROP TABLE `".$this->sql->esc($to->name)."`","name"=>$to->name);
		//drop def
		unset($this->tables->by_id[$tbl_l['']->id]);
		foreach($tbl_l as $ti => $to)
			unset($this->tables->by_name[$to->name]);
	}
	
	function drop_tables_meta(&$ret,$tbl_t)
	{
		//drop both main and meta tables
		$full=$this->tables->by_id[$tbl_t->id][''];
		$triggers=$this->enum_triggers($full->name,$full->metatables);
		if(is_array($triggers))foreach($triggers as $ti =>$tr)
			$ret[]=(Object)Array("id"=>$tbl_l['']->id,"type"=>"-r","query"=>"DROP TRIGGER `".$this->sql->esc($ti)."`");
		
		//drop all tables subtables
		$ret[]=(Object)Array("id"=>$to->id,"type"=>"-t","query"=>"DROP TABLE `".$this->sql->esc($tbl_t->name)."`","name"=>$tbl_t->name);
		//drop def
		unset($this->tables->by_id[$tbl_t->id][$tbl_t->metatable]);
		unset($this->tables->by_name[$tbl_t->name]);
	}
	
	function rename_tables_tmp(&$ret,$tbl_l)
	{
		do{
			$rndid='tmp'.rand();
		}while(isset($this->tables->by_name[$rndid]));
		$tbl_l['']->tmp_renamed_=1;
		$tmp=Array();
		$this->rename_tables_do($tmp,$tbl_l,$rndid);
		foreach($tmp as $e)
		{
			$e->tmp=true;
			$ret[]=$e;
		}
		
	}
	
	function rename_tables_do(&$ret,$tbl_l,$new)
	{
		if(!isset($this->old_table_names[$tbl_l['']->id]))$this->old_table_names[$tbl_l['']->id]=$tbl_l['']->name;
		$ret[]=(Object)Array("id"=>$tbl_l['']->id,"type"=>"*t","query"=>"ALTER TABLE `".$this->sql->esc($tbl_l['']->name)."` RENAME `".$this->sql->esc($new).'`',"name"=>$tbl_l['']->name,"new_name"=>$new);
		$new_sub_names=$this->get_subtables($new,$tbl_l['']->metatables);
		foreach($new_sub_names as $m => $n)
			$ret[]=(Object)Array("id"=>$tbl_l['']->id,"type"=>"*t","query"=>"ALTER TABLE `".$this->sql->esc($tbl_l[$m]->name)."` RENAME `".$this->sql->esc($n).'`',"name"=>$tbl_l[$m]->name,"new_name"=>$n);
		foreach($tbl_l as $m => $tbl_t)
		{
			unset($this->tables->by_name[$tbl_t->name]);
			if($m !='')$tbl_l[$m]->name=$new_sub_names[$m];
			else $tbl_l['']->name=$new;
			$this->tables->by_name[$tbl_l[$m]->name]=$tbl_t;
		}
		unset($this->tables->by_id[$tbl_l['']->id]);
		$this->tables->by_id[$tbl_l['']->id]=$tbl_l;
	}
	
	function enum_columns_x($id,$metatable,$tbl_l)
	{
		unset($this->columns);
		unset($this->indexes);
		$name=$tbl_l['']->name;
		if(isset($this->old_table_names[$id]))$name=$this->old_table_names[$id];
		$tmp=$this->enum_columns_0($name);// !!!!
		if($metatable !='')$tmp=$this->enum_columns_suffix($tbl_l['']->name,$metatable,$tmp);
		if(is_array($tmp))foreach($tmp as $col)
		{
			if(isset($col['id']))$res->by_id[$col['id']]=$col;
			$res->by_name[$col['name']]=$col;
			if($col['sql_keyname']!='')
			{
				unset($ind);
				$ind->len=intval($col['sql_keylen']);
				$ind->name=$col['name'];
				$this->indexes[$col['sql_keyname']][$col['name']]=$ind;
			}
		}
		
		$this->columns=$res;
		
	}
	
	
	function drop_index(&$ret,$tbl_name,$key_name)
	{
		if($key_name=='PRIMARY')
		{
			//remove auto_increment
			foreach($this->indexes[$key_name] as $coln => $sub)
			if($this->columns->by_name[$coln]['sql_sequence']==1)
				$ret[]=(Object)Array("id"=>$this->columns->by_name[$coln]['id'],"type"=>"*c","query"=>"ALTER TABLE `".$this->sql->esc($tbl_name)."` CHANGE COLUMN `".$this->sql->esc($coln)."` ".$this->col_def_str($this->columns->by_name[$coln],false),"sql_table"=>$tbl_name,'name'=>$coln,"sql_sequence"=>0);
			$ret[]=(Object)Array("id"=>'n/a',"type"=>"-k","query"=>"ALTER TABLE `".$this->sql->esc($tbl_name)."` DROP PRIMARY KEY","sql_table"=>$tbl_name,"name"=>"PRIMARY");
			
		}else
			$ret[]=(Object)Array("id"=>'n/a',"type"=>"-k","query"=>"ALTER TABLE `".$this->sql->esc($tbl_name)."` DROP KEY `".$this->sql->esc($key_name)."`","sql_table"=>$tbl_name,"name"=>$key_name);
		unset($this->indexes[$key_name]);
	}
	
	
	function drop_columns_id(&$ret,$tbl,$col_l)
	{
		$ret[]=(Object)Array("id"=>$col_l['id'],"type"=>"-c","query"=>"ALTER TABLE `".$this->sql->esc($tbl)."` DROP COLUMN `".$this->sql->esc($col_l['name'])."`","sql_table"=>$tbl_name,"name"=>$col_l['name']);
		if(isset($col_l['id']))unset($this->columns->by_id[$col_l['id']]);
		unset($this->columns->by_name[$col_l['name']]);
		foreach($this->indexes as $ind =>$ind_t)
			unset($this->indexes[$ind][$col_l['name']]);
	}
	
	function rename_columns_tmp(&$ret,$tbl_name,$col_l)
	{
		do{
			$rndid='tmp'.rand();
		}while(isset($this->columns->by_name[$rndid]));
		$col_n=$col_l;
		$col_n['name']=$rndid;
		$col_n['tmp_renamed_']=1;
		$this->change_columns_do($ret,$tbl_name,$col_l,$col_n);
	}
	
	function change_columns_do(&$ret,$tbl_name,$col_l,$col_n)
	{
		$ret[]=(Object)Array("id"=>$col_l['id'],"type"=>"*c","query"=>"ALTER TABLE `".$this->sql->esc($tbl_name)."` CHANGE COLUMN `".$this->sql->esc($col_l['name'])."` ".$this->col_def_str($col_n,false),"sql_table"=>$tbl_name,"name"=>$col_l['name'],"new"=>$col_n);
		//if(isset($col_l['id']))unset($this->columns->by_id[$col_l['id']]);
		unset($this->columns->by_name[$col_l['name']]);
		$this->columns->by_name[$col_n['name']]=$col_n;
		if(isset($col_n['id']))$this->columns->by_id[$col_n['id']]=$col_n;
		//	foreach($col_n as $i => $v)
		//		$this->colums->by_id[$col_n['id']][$i]=$v;
	}
	
	function col_different_x($l1,$l2)
	{
		foreach($this->column_compare_list as $ind => $op)
			if($l1[$ind]!=$l2[$ind])return true;
		return false;
	}
	
	function add_columns_id(&$ret,$tbl_name,$col_n)
	{
		$ret[]=(Object)Array("id"=>$col_n['id'],"type"=>"+c","query"=>"ALTER TABLE `".$this->sql->esc($tbl_name)."` ADD COLUMN  ".$this->col_def_str($col_n,false),"sql_table"=>$tbl_name,"name"=>$col_n['name'],"new"=>$col_n);
		$this->colums->by_name[$col_n['name']]=$col_n;
		if(isset($col_n['id']))
			$this->colums->by_id[$col_n['id']]=$col_n;
	}
	
	function add_index(&$ret,$tbl_name,$key_name,$key_struct)
	{
		$a='';
		if(!is_array($key_struct))return ;
		foreach($key_struct as $coln => $sub)
		{
			$sub->len=($sub->len==0)?'':"(".$sub->len.")";
			if($a !='')$a.=', ';
			$a.=$this->sql->esc($coln).$sub->len;
		}
		if($key_name=='PRIMARY')
		{
			$ret[]=(Object)Array("id"=>'n/a',"type"=>"+k","query"=>"ALTER TABLE `".$this->sql->esc($tbl_name)."` ADD PRIMARY KEY  (".$a.")","sql_table"=>$tbl_name,"name"=>"PRIMARY");
			//set back sequence
			foreach($key_struct as $coln => $sub)
			if($this->columns->by_name[$coln]['sql_sequence']==1)
				$ret[]=(Object)Array("id"=>$this->columns->by_name[$coln]['id'],"type"=>"*c","query"=>"ALTER TABLE `".$this->sql->esc($tbl_name)."` CHANGE COLUMN `".$this->sql->esc($coln)."` ".$this->col_def_str($this->columns->by_name[$coln],true),"sql_table"=>$tbl_name,'name'=>$coln,"sql_sequence"=>1);
			
		}else
			$ret[]=(Object)Array("id"=>'n/a',"type"=>"+k","query"=>"ALTER TABLE `".$this->sql->esc($tbl_name)."` ADD KEY ` ".$this->sql->esc($key_name)."` (".$a.")","sql_table"=>$tbl_name,"name"=>$key_name);
		$this->indexes[$key_name]=$key_struct;
		
	}
	function create_tables_id(&$ret,$tbl_t,$columns)
	{
		$ret[]=(Object)Array("id"=>$tbl_t->id,"type"=>"+t","query"=>$this->sql_create_table($tbl_t->name,$columns),"name"=>$tbl_t->name,"columns"=>$columns);
	}
	

	function gen_changes($old)
	{
		$r=$old->check_db();
		if(is_array($r) && count($r)>0)
		{
			//foreach($r as $e)$s.=$e->type.','.$e->descr.';';
			unset($s);
			return "Failed: old structure didn't pass check_db. ".$s;
		}
		$r=$old->check_logical();
		if(is_array($r) && count($r)>0)return "Failed: old structure didn't pass check_logical.";
		$r=$this->check_logical();
		if(is_array($r) && count($r)>0)return "Failed: new structure didn't pass check_logical.";
		
		
		//check if sql conforms to meta if failed return ...
		unset($this->dropped_tables);//{by_id,by_name}
		unset($this->created_tables);//{by_id,by_name}
		unset($this->dropped_columns);//{by_id,by_name}
		unset($this->created_columns);//{by_id,by_name}
		unset($this->table_map);//{by_id,by_name}
		unset($this->column_map);//{by_id,by_name}
		$ret=Array();
		$old->enum_tables_x();
		$this->enum_tables_x();
		//->by_id[id][meta]{id,name,meta,isstored}
		//->by_name[name]{id,name,meta,isstored}
		//drop by id
		if(is_array($old->tables->by_id))
		foreach($old->tables->by_id as $id => $tbl_l)
			if(!isset($this->tables->by_id[$id]))
			{
				$old->drop_tables_id($ret,$tbl_l);
				
			}
		//drop by metatables
		if(is_array($old->tables->by_id))
		foreach($old->tables->by_id as $id => $tbl_l)
			foreach($tbl_l as $metatable => $tbl_t)
			if(!isset($this->tables->by_id[$id][$metatable]))
			{
				$old->drop_tables_meta($ret,$tbl_t);
				
			}
		//return $ret;
		//eliminate rename collisions
		if(is_array($old->tables->by_id))
		foreach($old->tables->by_id as $id => $tbl_l)
			if(
				isset($this->tables->by_id[$id]) &&
				($this->tables->by_id[$id]['']->name!=$old->tables->by_id[$id]['']->name) &&
				isset( $old->tables->by_name[$this->tables->by_id[$id]['']->name])
			)
			{
				$old->rename_tables_tmp($ret,$tbl_l);
			}
		//rename to final names collision makers
		if(is_array($old->tables->by_id))
		foreach($old->tables->by_id as $id => $tbl_l)
			if(
				isset($this->tables->by_id[$id]) &&
				($this->tables->by_id[$id]['']->name!=$old->tables->by_id[$id]['']->name) &&
				($tbl_l['']->tmp_renamed_!=1)
			)
			{
				$old->rename_tables_do($ret,$tbl_l,$this->tables->by_id[$id]['']->name);
			}
		//rename to final names not collision makers
		if(is_array($old->tables->by_id))
		foreach($old->tables->by_id as $id => $tbl_l)
			if(
				isset($this->tables->by_id[$id]) &&
				($this->tables->by_id[$id]['']->name!=$old->tables->by_id[$id]['']->name) &&
				($tbl_l['']->tmp_renamed_==1)
			)
			{
				$old->rename_tables_do($ret,$tbl_l,$this->tables->by_id[$id]['']->name);
			}
		//drop indexes where indexed colums list changed or indexed length changed
		if(is_array($old->tables->by_id))
		foreach($old->tables->by_id as $id => $tbl_l)
		{
			//loop thru metatables
			foreach($tbl_l as $metatable =>$tbl_t)
			{
				$this->enum_columns_x($id,$metatable,$this->tables->by_id[$id]);
				$old->enum_columns_x($id,$metatable,$tbl_l);
				//compare indexes against dropped/added columns
				//drop indexes!
				foreach($old->indexes as $key_name => $key_struct)
				{
					if(!isset($this->indexes[$key_name]))
					{
						$old->drop_index($ret,$tbl_t->name,$key_name);
						continue;
					}
					foreach($key_struct as $key_col =>$key_col_s)
					{
						$this_col_name=isset($old->columns->by_name[$key_col]['id'])?$this->columns->by_id[$old->columns->by_name[$key_col]['id']]['name']:$key_col;
						if(!isset($this_col_name) || !isset($this->indexes[$key_name][$this_col_name]) || $key_col_s->len != $this->indexes[$key_name][$this_col_name]->len)
						{
							$old->drop_index($ret,$tbl_t->name,$key_name);
							continue 2;
						}
					}
					foreach($this->indexes[$key_name] as $key_col =>$key_col_s)
					{
						$this_col_name=isset($this->columns->by_name[$key_col]['id'])?$old->columns->by_id[$this->columns->by_name[$key_col]['id']]['name']:$key_col;
						if(!isset($this_col_name) || !isset($old->indexes[$key_name][$this_col_name]) || $key_col_s->len != $old->indexes[$key_name][$this_col_name]->len)
						{
							$old->drop_index($ret,$tbl_t->name,$key_name);
							continue 2;
						}
					}
				}
				//drop columns
				//drop by id
				foreach($old->columns->by_id as $cid => $col_l)
					if(!isset($this->columns->by_id[$cid]))
					{
						$old->drop_columns_id($ret,$tbl_t->name,$col_l);//TODO!!check
						
					}
				//eliminate rename collisions
				foreach($old->columns->by_id as $cid => $col_l)
					if(
						isset($this->columns->by_id[$cid]) &&
						($this->columns->by_id[$cid]['name']!=$old->columns->by_id[$cid]['name']) &&
						isset( $old->columns->by_name[$this->columns->by_id[$cid]['name']])
					)
					{
						$old->rename_columns_tmp($ret,$tbl_t->name,$col_l);//TODO!!check
					}
				
				//rename/update to final names collision makers
				foreach($old->columns->by_id as $cid => $col_l)
					if(
						isset($this->columns->by_id[$cid]) &&
						$this->col_different_x($this->columns->by_id[$cid],$old->columns->by_id[$cid]) &&
						($col_l['tmp_renamed_']!=1)
					)
					{
						$old->change_columns_do($ret,$tbl_t->name,$col_l,$this->columns->by_id[$cid]);//TODO!!check
					}
				//rename/update to final names not collision makers
				foreach($old->columns->by_id as $cid => $col_l)
					if(
						isset($this->columns->by_id[$cid]) &&
						$this->col_different_x($this->columns->by_id[$cid],$old->columns->by_id[$cid]) &&
						($col_l['tmp_renamed_']==1)
					)
					{
						$old->change_columns_do($ret,$tbl_t->name,$col_l,$this->columns->by_id[$cid]);//TODO!!check
					}
				//add by id
				foreach($this->columns->by_id as $cid => $col_l)
					if(!isset($old->columns->by_id[$cid]))
					{
						$old->add_columns_id($ret,$tbl_t->name,$col_l);
						
					}
				//create indexes
				foreach($this->indexes as $key_name => $key_struct)
				{
					if(!isset($old->indexes[$key_name]))
					{
						$old->add_index($ret,$tbl_t->name,$key_name,$key_struct);
						continue;
					}
				}
			}
		}
		//create tables
		//create by id
		if(is_array($this->tables->by_id))
		foreach($this->tables->by_id as $id => $tbl_l)
			if(!isset($old->tables->by_id[$id]))
			foreach($tbl_l as $metatable => $tbl_t)
			{
				
				$this->enum_columns_x($id,$metatable,$tbl_l);
				$old->create_tables_id($ret,$tbl_t,$this->columns->by_name);
				
			}
		//create by metatables
		if(is_array($this->tables->by_id))
		foreach($this->tables->by_id as $id => $tbl_l)
		if(isset($old->tables->by_id[$id]))
			foreach($tbl_l as $metatable => $tbl_t)
			if(!isset($old->tables->by_id[$id][$metatable]))
			{
				$this->enum_columns_x($id,$metatable,$tbl_l);
				$old->create_tables_id($ret,$tbl_t,$this->columns->by_name);
				//$old->create_tables_meta($ret,$tbl_t);
				
			}
		//check and resreate triggers (maybe simple sql_check will do...)
		
		
		return $ret;
	}
	
	
	function inheritance_update($show=true)
	{
		//table level
		//find columns that differ from parents - update
		global $ddc_key_special_types;
		$synclist=Array('name','sql_type','sql_null','sql_sequence','sql_default','sql_comment','sql_keyname','sql_keylen');
		$qt=new query_gen_ext('UPDATE');
		$qt->into->exprs[]=new sql_column(NULL,$this->tree,NULL,'a');
		$qt->into->exprs[]=new sql_column(NULL,$this->tree,NULL,'b');
		$qt->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,'a','inheritedfrom'),
			new sql_column(NULL,'b','id')
			));
		$dl=new sql_expression('OR');
		foreach($synclist as $li)
		{
			$dl->exprs[]=new sql_expression('!=',Array(
				new sql_column(NULL,'a',$li),
				new sql_column(NULL,'b',$li)
				));
			$qt->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,'a',$li),
				new sql_column(NULL,'b',$li)
				));
			$qt->what->exprs[]=new sql_column(NULL,'b',$li,$li);
		}
		$qt->where->exprs[]=$dl;
		$restypes=new sql_list('IN');
		foreach($ddc_key_special_types as $t)
			$restypes->exprs[]=new sql_immed($t);
		$qt->where->exprs[]=new sql_expression('NOT',Array(
			new sql_column(NULL,'a','sql_type'),
			$restypes //only column defs
			));
		//exec query
		$inh_update=$qt;
		$inh_update_v=new query_gen_ext("select");
		$inh_update_v->from=$qt->into;
		$inh_update_v->what=$qt->what;
		$inh_update_v->what->exprs[]=new sql_column(NULL,'a','id','id');
		$inh_update_v->what->exprs[]=new sql_column(NULL,'b','id','inheritedfrom');
		$inh_update_v->where=$qt->where;
		if(!$show)
		{
			$res=$this->sql->query($inh_update->result());
		}else{
			$r->type="*q";
			$r->row=Array("query"=>$inh_update->result());
			$ret[]=$r;
			unset($r);
			$res=$this->sql->query($inh_update_v->result());
			while($row=$this->sql->fetcha($res))
			{
				$r->type='*';
				$r->row=$row;
				$ret[]=$r;
				unset($r);
			}
		}
		
		
		
		//find lost columns - delete
		$qt=new query_gen_ext('DELETE');
		$qt->what->exprs[]=new sql_column(NULL,'a');
		$qt->from->exprs[]=new sql_column(NULL,$this->tree,NULL,'a');
		$join->what=new sql_column(NULL,$this->tree,NULL,'b');
		$join->on=new sql_expression('=',Array(
			new sql_column(NULL,'a','inheritedfrom'),
			new sql_column(NULL,'b','id')
		));
		$join->type='left outer join';
		$qt->joins->exprs[]=$join;
		$qt->where->exprs[]=new sql_expression('IS',Array(
			new sql_column(NULL,'b','id'),
			new sql_null
			));
		$qt->where->exprs[]=new sql_expression('IS NOT',Array(
			new sql_column(NULL,'a','inheritedfrom'),
			new sql_null
			));
		$qt->where->exprs[]=new sql_expression('!=',Array(
			new sql_column(NULL,'a','inheritedfrom'),
			new sql_immed(0)
			));
		//exec query while result is true
		$inh_delete=$qt;
		$inh_delete_v=new query_gen_ext("select");
		$inh_delete_v->from=$qt->from;
		$inh_delete_v->joins=$qt->joins;
		$inh_delete_v->where=$qt->where;
		$inh_delete_v->what->exprs[]=new sql_column(NULL,'a','id','id');
		$inh_delete_v->what->exprs[]=new sql_column(NULL,'a','id','inheritedfrom');
		
		
		if(!$show)
		{
			$res&=$this->sql->query($inh_delete->result());
		}else{
			$r->type="-q";
			$r->row=Array("query"=>$inh_delete->result());
			$ret[]=$r;
			unset($r);
			$res=$this->sql->query($inh_delete_v->result());
			while($row=$this->sql->fetcha($res))
			{
				$r->type='-';
				$r->row=$row;
				$ret[]=$r;
				unset($r);
			}
		}
		
		
		//find new columns (parent has, child does not) - add as shared to all objects having columns from that table
		$qt=new query_gen_ext('SELECT');
		$qt->from->exprs[]=new sql_column(NULL,$this->tree,NULL,'tp');
		$qt->from->exprs[]=new sql_column(NULL,$this->tree,NULL,'tc');
		$qt->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,'tp','id'),
			new sql_column(NULL,'tc','inheritedfrom')
			));
		$qt->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,'tp','sql_type'),
			new sql_immed('table')
			));
		$qt->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,'tc','sql_type'),
			new sql_immed('table')
			));
		$qt->from->exprs[]=new sql_column(NULL,$this->tree,NULL,'cp');
		$qt->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,'tp','sql_table'),
			new sql_column(NULL,'cp','sql_table')
			));
		$qt->where->exprs[]=new sql_expression('NOT',Array(
			new sql_column(NULL,'cp','sql_type'),
			$restypes //only column defs
			));
		//fetch objects
		unset($join);
		
		$join->what=new sql_column(NULL,$this->tree,NULL,'ob');
		$join->on=new sql_expression('AND',Array(
			new sql_expression('=',Array(
				new sql_column(NULL,'tc','sql_table'),
				new sql_column(NULL,'ob','sql_table')
			)),
			new sql_expression('NOT',Array(
				new sql_column(NULL,'ob','sql_type'),
				clone $restypes
			)),
		));
		$join->type='join';
		$qt->joins->exprs[]=$join;
		
		//fetch parent columns, not found in child table
		unset($join);
		
		$join->what=new sql_column(NULL,$this->tree,NULL,'cc');
		$join->on=new sql_expression('AND',Array(
			new sql_expression('=',Array(
				new sql_column(NULL,'cc','inheritedfrom'),
				new sql_column(NULL,'cp','id')
			)),
			new sql_expression('=',Array(
				new sql_column(NULL,'cc','parentid'),
				new sql_column(NULL,'ob','parentid')
			)),
		));
		$join->type='left outer join';
		$qt->joins->exprs[]=$join;
		$qt->where->exprs[]=new sql_expression('IS',Array(
			new sql_column(NULL,'cc','id'),
			new sql_null
			));
		
		foreach($synclist as $li)
		{
			$qt->what->exprs[]=new sql_column(NULL,'cp',$li,$li);
			$qt->group->exprs[]=new sql_column(NULL,'cp',$li);
		}
		$qt->what->exprs[]=new sql_column(NULL,'cp','isstored','isstored');
		$qt->group->exprs[]=new sql_column(NULL,'cp','isstored');
		$qt->what->exprs[]=new sql_immed(1,'isshared');
		$qt->what->exprs[]=new sql_column(NULL,'ob','sql_table','sql_table');
		$qt->group->exprs[]=new sql_column(NULL,'ob','sql_table');
		//fetch objects that have columns with sql_table==tc.sql_table
		$qt->what->exprs[]=new sql_column(NULL,'cp','id','inheritedfrom');
		//$qt->what->exprs
		$qt->what->exprs[]=new sql_column(NULL,'ob','parentid','parentid');
		$qt->group->exprs[]=new sql_column(NULL,'ob','parentid');
		
		$setq=new query_gen_ext("insert select");
		$setq->into->exprs[]=new sql_column(NULL,$this->tree);
		$setq->select=$qt;
		if(!$show)
		{
			$res&=$this->sql->query($setq->result());
		}else{
			$r->type="+q";
			$r->row=Array("query"=>$setq->result());
			$ret[]=$r;
			unset($r);
			$res=$this->sql->query($qt->result());
			while($row=$this->sql->fetcha($res))
			{
				$r->type='+';
				$r->row=$row;
				$ret[]=$r;
				unset($r);
			}
		}

		if($show)return $ret;
		else return NULL;
		
		
	}
	
	
	
	
	
	
	
	
	
	
	

}





?>