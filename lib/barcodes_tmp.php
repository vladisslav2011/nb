<?php
#require_once('lib/uncommctrls.php');
require_once('lib/ipp/PrintIPP.php');

$ddc_tables['barcodes_raw']=(object)
Array(
 'name' => 'barcodes_raw',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 1,	'sql_comment' =>NULL, 'hname'=>'Идентификатор'),
  Array('name' =>'name',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>NULL,	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Наименование'),
  Array('name' =>'code',	'sql_type' =>'varchar(13)', 'sql_null' =>1, 'sql_default' =>NULL,	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Код', 'editor' => 'editor_text_ean13'),
  Array('name' =>'isown',	'sql_type' =>'int(1)', 'sql_null' =>0, 'sql_default' =>0,	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Собственный', 'editor' => 'editor_checkbox')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'name', 'name' =>'name', 'sub' => NULL),
  Array('key' =>'code', 'name' =>'code', 'sub' => NULL),
  Array('key' =>'isown', 'name' =>'isown', 'sub' => NULL)
 )
);

$ddc_tables['barcodes_print']=(object)
Array(
 'name' => 'barcodes_print',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'task',	'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'count',	'sql_type' =>'int(10)', 'sql_null' =>1, 'sql_default' =>0,	'sql_sequence' => 0,		'sql_comment' =>NULL),
  Array('name' =>'printed',	'sql_type' =>'int(10)', 'sql_null' =>1, 'sql_default' =>0,	'sql_sequence' => 0,		'sql_comment' =>NULL)
  ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'task', 'sub' => NULL)
 )
);

$ddc_tables['barcodes_counters']=(object)
Array(
 'name' => 'barcodes_counters',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'init',	'sql_type' =>'int(10)', 'sql_null' =>1, 'sql_default' =>0,	'sql_sequence' => 0,		'sql_comment' =>NULL),
  Array('name' =>'current',	'sql_type' =>'int(10)', 'sql_null' =>1, 'sql_default' =>0,	'sql_sequence' => 0,		'sql_comment' =>NULL)
  ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL)
 )
);

$ddc_tables['barcodes_printed']=(object)
Array(
 'name' => 'barcodes_printed',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'when',	'sql_type' =>'timestamp',  'sql_null' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'count',	'sql_type' =>'int(10)', 'sql_null' =>1, 'sql_default' =>0,	'sql_sequence' => 0,		'sql_comment' =>NULL)
  ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'when', 'sub' => NULL)
 )
);

$ddc_tables['barcodes_mapping']=(object)
Array(
 'name' => 'barcodes_mapping',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'name',	'sql_type' =>'varchar(200)', 'sql_null' =>0, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Наименование')
  ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'name', 'sub' => NULL)
 )
);


if($_GET['init']=='init')
{
	ddc_gentable_o($ddc_tables['barcodes_raw'],$sql);
	ddc_gentable_o($ddc_tables['barcodes_print'],$sql);
	ddc_gentable_o($ddc_tables['barcodes_counters'],$sql);
	ddc_gentable_o($ddc_tables['barcodes_printed'],$sql);
	ddc_gentable_o($ddc_tables['barcodes_mapping'],$sql);
}







class query_result_viewer_single extends dom_any
{
	function __construct()
	{
		dom_any::__construct('span');
		$this->etype='query_result_viewer_single';
		$this->txt=new dom_statictext;
		$this->append_child($this->txt);
		$this->main=$this;
		
	}
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!isset($this->attributes['title']))$this->attributes['title']=$this->long_name;
	}
	
	
	function html_inner()
	{
		global $sql;
		if(isset($this->query))
			$q=$this->query->result();
		elseif(isset($this->compiled))
			$q=$this->compiled;
			
		$res=$sql->query($q);
		if($res)$this->txt->text=$sql->fetch1($res);
		else $this->txt->text='error';
		parent::html_inner();
	}
}

class query_result_viewer_single_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='query_result_viewer_single_test';
		editor_generic::addeditor('qw',new query_result_viewer_single);
		$this->append_child($this->editors['qw']);
		
	}
	
	function setup()
	{
		$qw=&$this->editors['qw'];
		$qw->query=new query_gen_ext;
		$s=new sql_expression;
		$s->operator='+';
		
		
		$e=new sql_immed;
		$e->val=10;
		
		$s->exprs[]=$e;
		
		$e=new sql_immed;
		$e->val=1;
		
		$s->exprs[]=$e;
		
		
		
		$qw->query->what->exprs[]=$s;
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->setup();
		
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->bootstrap();
		}
	
	}
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}

class query_result_viewer_single_test2 extends query_result_viewer_single_test
{
	function setup()
	{
		$qw=&$this->editors['qw'];
		$qw->compiled='SELECT count(*) FROM `%tree`';
	}
}


$tests_m_array['simple']['query_result_viewer_single_test']='query_result_viewer_single_test';
$tests_m_array['simple']['query_result_viewer_single_test2']='query_result_viewer_single_test2';
#--------------------------------------------------------------------------------------------------------------





class query_result_viewer_multiline extends dom_any
{
	function __construct()
	{
		dom_any::__construct('table');
		$this->etype='query_result_viewer_multiline';
		$this->row=new dom_tr;
		$this->append_child($this->row);
		$this->cell=new dom_td;
		$this->row->append_child($this->cell);
		$this->txt=new dom_statictext;
		$this->cell->append_child($this->txt);
		
		$this->cell_null=new dom_td;
		$this->row->append_child($this->cell_null);
		$t=new dom_statictext;
		$t->text='null';
		$this->cell_null->append_child($t);
		$this->cell_null->css_style['background-color']='#FFAAAA';
		
	}
	function bootstrap()
	{
	}
	
	
	function html_inner()
	{
		global $sql;
		$hd=true;
		if(isset($this->query))
			$q=$this->query->result();
		elseif(isset($this->compiled))
			$q=$this->compiled;
		if(isset($this->query))
		{
			$this->row->html_head();
			$cnt=0;
			foreach($this->query->what->exprs as $e)
			{
				$this->txt->text=($e->alias=='')?$cnt:$e->alias;
				$cnt++;
				$this->cell->html();
				$this->cell->id_alloc();
			}
			$this->row->html_tail();
			$this->row->id_alloc();
			$hd=false;
		}
		
		$res=$sql->query($q);
		if($res &&($res !== TRUE))
		while($row=$sql->fetcha($res))
		{
			if($hd)
			{
				$this->row->html_head();
				foreach($row as $e =>$dum)
				{
					$this->txt->text=$e;
					$this->cell->html();
					$this->cell->id_alloc();
				}
				$this->row->html_tail();
				$this->row->id_alloc();
				$hd=false;
			}
			$this->row->html_head();
			foreach($row as $e)
			{
				if(!isset($e) && $this->show_nulls)
				{
					$this->cell_null->html();
					$this->cell_null->id_alloc();
				}else{
					$this->txt->text=$e;
					$this->cell->html();
					$this->cell->id_alloc();
				}
			}
			$this->row->html_tail();
			$this->row->id_alloc();
		}elseif($res){
			$this->txt->text='ok';
			$this->txt->html();
		}else{
			$this->txt->text='Sql Error:'.$sql->err();
			$this->txt->html();
		}
	}
}

class query_result_viewer_multiline_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='query_result_viewer_multiline_test';
		editor_generic::addeditor('qw',new query_result_viewer_multiline);
		$this->append_child($this->editors['qw']);
		$qw=&$this->editors['qw'];
		$qw->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border']='2px solid black';
	}
	
	function setup()
	{
		$qw=&$this->editors['qw'];
		#$qw->compiled='show columns from `*settings`';
		$qw->query=new query_gen_ext;
		$qw=&$qw->query;
		$n=0;
		$list=Array('id','name','code');
		foreach($list as $c)
		{
			$col=new sql_column;
			$col->alias='c-'.$c.'-'.$n;
			$n++;
			$col->tbl='a';
			$col->col=$c;
			$qw->what->exprs[]=$col;
		}
		$col=new sql_column;
		$col->alias='a';
		#$col->db='dbfp';
		$col->tbl='barcodes_raw';
		$qw->from->exprs[]=$col;
		
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->setup();
		
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->bootstrap();
		}
	
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}
$tests_m_array['simple']['query_result_viewer_multiline_test']='query_result_viewer_multiline_test';

#-------------------------------------------------------------------------

##-------------------------------------------------------------------------
##-------------------------------------------------------------------------


class editor_text_st1 extends editor_text
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
	
	function bootstrap()
	{
		parent::bootstrap();
		$this->main->attributes['onfocus'].=$this->onfocus_add;
		$this->main->attributes['onblur'].=$this->onblur_add;
	}
	
	function store_sql($ev)
	{
		global $sql;
		$db=$ev->context[$ev->long_name]['dbname'];
		$tbl=$ev->context[$ev->long_name]['tblname'];
		$col=$ev->context[$ev->long_name]['colname'];
		$q=new query_gen_ext;
		$q->type='insert update';
		$into=new sql_column;
		$into->tbl=$tbl;
		if($db!='')$into->db=$db;
		$q->into->exprs[]=$into;
		
		$q->set->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$col),new sql_immed($_POST['val'])));;
		foreach($ev->keys as $kc => $kv)
		{
			$q->set->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$kc),new sql_immed($kv)));
		}
		$query=$q->result();
		//print "/* ".$query." */";
		$sql->query($query);
	}
	
	
	function handle_event($ev)
	{
		$db=$ev->context[$ev->long_name]['dbname'];
		$tbl=$ev->context[$ev->long_name]['tblname'];
		$col=$ev->context[$ev->long_name]['colname'];
		if(isset($ev->context[$ev->long_name]['validator_class']))
		{
			if(class_exists($ev->context[$ev->long_name]['validator_class']))
			{
				$validator=new $ev->context[$ev->long_name]['validator_class'];
				if(method_exists($validator,'validate'))
				{
					$validator->context=&$ev->context;
					$validator->long_name=$ev->long_name;
					$validator->keys=&$ev->keys;
					$validator->oid=&$ev->context[$ev->long_name]['oid'];
					
					if(!$validator->validate($_POST['val']))
					{
						$ev->failure=$validator->failure;
						parent::handle_event($ev);
						return ;
					}
				}else{
					$ev->failure='Не обнаружен класс валидатора: '.$ev->context[$ev->long_name]['validator_class'].' свяжитесь с разработчиком.';
					parent::handle_event($ev);
					return ;
				}
			}else{
				$ev->failure='Не обнаружен класс валидатора: '.$ev->context[$ev->long_name]['validator_class'].' свяжитесь с разработчиком.';
				parent::handle_event($ev);
				return ;
			}
		}
		$this->store_sql($ev);
		parent::handle_event($ev);
		
	}
}


class editor_text_st1_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='editor_text_st1_test';
		editor_generic::addeditor('qw',new editor_text_st1);
		$this->append_child($this->editors['qw']);
	}
	
	function setup()
	{
		$qw=&$this->editors['qw'];
		#$qw->compiled='show columns from `*settings`';
		$this->dbname='codes_db';
		$this->tblname='*users';
		$this->colname='name';
		$this->keys=Array('uid' => '1001');
		
		if(isset($this->dbname))$this->context[$this->long_name.'.qw']['dbname']=$this->dbname;
		$this->context[$this->long_name.'.qw']['tblname']=$this->tblname;
		$this->context[$this->long_name.'.qw']['colname']=$this->colname;
		$this->context[$this->long_name.'.qw']['validator_class']='validator_integer';
		$this->context[$this->long_name.'.qw']['var']='qw';
		
		
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->setup();
		
		
		//$this->args['qw']='1234';
		
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->bootstrap();
		}
	
	}
	
	function html()
	{
		global $sql;
		$q=new query_gen_ext;
		$q->what->exprs[]=new sql_column(NULL,NULL,$this->colname,'qw');
		$q->from->exprs[]=new sql_column(NULL,$this->tblname,NULL,NULL);
		foreach($this->keys as $k => $v)
		$q->where->exprs[]=new sql_expression('=',
			Array(
				new sql_column(NULL,NULL,$k,NULL),
				new sql_immed($v,NULL)),NULL)
				;
		$query=$q->result();
		$res=$sql->query($query);
		$row=$sql->fetcha($res);
		if(is_array($row))foreach($row as $k => $v)$this->args[$k]=$v;
	
		foreach($this->editors as $e)
			$e->bootstrap();
		
		parent::html();
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}

class validator_class_test
{
	function validate($val)
	{
		$db=$this->context[$this->long_name]['dbname'];
		$tbl=$this->context[$this->long_name]['tblname'];
		$col=$this->context[$this->long_name]['colname'];
		global $sql;
		$q=new query_gen_ext;
		$wh=new sql_column;
		$wh->col=$col;
		$f=new sql_list;
		$f->func='count';
		$f->exprs[]=$wh;
		$q->what->exprs[]=$f;
		
		$ex=new sql_expression;
		$ex->operator='=';
		$im=new sql_immed;
		$im->val=$val;
		$ex->exprs[]=clone $wh;
		$ex->exprs[]=$im;
		
		$q->where->exprs[]=$ex;
		
		$fr=new sql_column;
		$fr->tbl=$tbl;
		if($db!='')$fr->db=$db;
		$q->from->exprs[]=$fr;
		$query=$q->result();
		//print "alert('".js_escape($query)."');";
		$cnt=$sql->fetch1($sql->query($query));
		if($cnt>0)
		{
			$this->failure='Уже существует';
			return false;
		}else{
			return true;
		}
	}

}

class validator_integer
{
	function validate($val)
	{
		$db=$this->context[$ev->long_name]['dbname'];
		$tbl=$this->context[$this->long_name]['tblname'];
		$col=$this->context[$this->long_name]['colname'];
		if(!preg_match('/^[0-9]*$/',$val))
		{
			$this->failure='Должно быть целое число';
			return false;
		}else{
			return true;
		}
	}

}


$tests_m_array['complex']['editor_text_st1_test']='editor_text_st1_test';


#-------------------------------------------------------------------------

##-------------------------------------------------------------------------
##-------------------------------------------------------------------------

class editor_text_autofetch extends editor_text_st1
{
	function fetch_sql()
	{
		global $sql;
		$db=$this->context[$this->long_name]['dbname'];
		$tbl=$this->context[$this->long_name]['tblname'];
		$col=$this->context[$this->long_name]['colname'];
		$q=new query_gen_ext;
		$q->type='select';
		$q->from->exprs[]=new sql_column(($db=='')?NULL:$db,$tbl);
		foreach($this->keys as $ki =>$kv)
			$q->where->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,$ki),
				new sql_immed($kv)
				));
		$q->what->exprs[]=new sql_column(NULL,NULL,$col);
		$q->lim_count=1;
		$res=$sql->query($q->result());
		$this->ed->attributes['value']=$sql->fetch1($res);
		if($res)$sql->free($res);
	}
	
	function html_inner()
	{
		$this->fetch_sql();
		parent::html_inner();
	}
	

}

class editor_text_autofetch_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype=get_class($this);
		editor_generic::addeditor('qw',new editor_text_autofetch);
		$this->append_child($this->editors['qw']);
	}
	
	function setup()
	{
		$qw=&$this->editors['qw'];
		#$qw->compiled='show columns from `*settings`';
		//$this->dbname='codes_db';
		$this->tblname='*settings';
		$this->colname='val';
		$this->keys=Array('oid' => '-1'
					,'uid' => $_SESSION['uid']
					,'setting' => get_class($this).'-test'
					,'preset' => '0');
		
		if(isset($this->dbname))$this->context[$this->long_name.'.qw']['dbname']=$this->dbname;
		$this->context[$this->long_name.'.qw']['tblname']=$this->tblname;
		$this->context[$this->long_name.'.qw']['colname']=$this->colname;
		$this->context[$this->long_name.'.qw']['validator_class']='validator_integer';
		
		
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->setup();
		
		
		//$this->args['qw']='1234';
		
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->bootstrap();
		}
	
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}

$tests_m_array['simple']['editor_text_autofetch_test']='editor_text_autofetch_test';

#-------------------------------------------------------------------------

##-------------------------------------------------------------------------
##-------------------------------------------------------------------------


class editor_text_pass_md5 extends editor_text_st1
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
	
	function store_sql($ev)
	{
		global $sql;
		$db=$ev->context[$ev->long_name]['dbname'];
		$tbl=$ev->context[$ev->long_name]['tblname'];
		$col=$ev->context[$ev->long_name]['colname'];
		$q=new query_gen_ext;
		$q->type='insert update';
		$into=new sql_column;
		$into->tbl=$tbl;
		if($db!='')$into->db=$db;
		$q->into->exprs[]=$into;
		
		$q->set->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$col),new sql_list('md5',Array(new sql_immed($_POST['val'])))));;
		foreach($ev->keys as $kc => $kv)
		{
			$q->set->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$kc),new sql_immed($kv)));
		}
		$query=$q->result();
		print "/* ".$query." */";
		$sql->query($query);
	}
}


#-------------------------------------------------------------------------



class editor_checkbox_st1 extends editor_checkbox
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
	
	function bootstrap()
	{
		parent::bootstrap();
		$this->attributes['onfocus'].=$this->onfocus_add;
		$this->attributes['onblur'].=$this->onblur_add;
	}
	
	function store_sql($ev)
	{
		global $sql;
		$db=$ev->context[$ev->long_name]['dbname'];
		$tbl=$ev->context[$ev->long_name]['tblname'];
		$col=$ev->context[$ev->long_name]['colname'];
		$model=$ev->context[$ev->long_name]['model'];
		if($model=='')$model='i';
		if($model=='i' || ($model=='d' && $_POST['val']==1))
		{
			$q=new query_gen_ext;
			$q->type='insert update';
			$into=new sql_column;
			$into->tbl=$tbl;
			if($db!='')$into->db=$db;
			$q->into->exprs[]=$into;
			
			$q->set->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$col),new sql_immed($_POST['val'])));;
			foreach($ev->keys as $kc => $kv)
			{
				$q->set->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$kc),new sql_immed($kv)));
			}
		}
		if($model=='u')
		{
			$q=new query_gen_ext;
			$q->type='update';
			$into=new sql_column(NULL,$tbl);
			if($db!='')$into->db=$db;
			$q->into->exprs[]=$into;
			
			$q->set->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$col),new sql_immed($_POST['val'])));;
			foreach($ev->keys as $kc => $kv)
			{
				$q->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$kc),new sql_immed($kv)));
			}
		}
		if($model=='d' && $_POST['val']==0)
		{
			$q=new query_gen_ext;
			$q->type='delete';
			$into=new sql_column(NULL,$tbl);
			if($db!='')$into->db=$db;
			$q->from->exprs[]=$into;
			
			//$q->set->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$col),new sql_immed($_POST['val'])));;
			foreach($ev->keys as $kc => $kv)
			{
				$q->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,$kc),new sql_immed($kv)));
			}
		}
		$query=$q->result();
		//print "/* ".$query." */";
		$sql->query($query);
	}
	
	
	function handle_event($ev)
	{
		$db=$ev->context[$ev->long_name]['dbname'];
		$tbl=$ev->context[$ev->long_name]['tblname'];
		$col=$ev->context[$ev->long_name]['colname'];
		if(isset($ev->context[$ev->long_name]['validator_class']))
		{
			if(class_exists($ev->context[$ev->long_name]['validator_class']))
			{
				$validator=new $ev->context[$ev->long_name]['validator_class'];
				if(method_exists($validator,'validate'))
				{
					$validator->context=&$ev->context;
					$validator->long_name=$ev->long_name;
					$validator->keys=&$ev->keys;
					$validator->oid=&$ev->context[$ev->long_name]['oid'];
					
					if(!$validator->validate($_POST['val']))
					{
						$ev->failure=$validator->failure;
						parent::handle_event($ev);
						return ;
					}
				}else{
					$ev->failure='Не обнаружен класс валидатора: '.$ev->context[$ev->long_name]['validator_class'].' свяжитесь с разработчиком.';
					parent::handle_event($ev);
					return ;
				}
			}else{
				$ev->failure='Не обнаружен класс валидатора: '.$ev->context[$ev->long_name]['validator_class'].' свяжитесь с разработчиком.';
				parent::handle_event($ev);
				return ;
			}
		}
		$this->store_sql($ev);
		parent::handle_event($ev);
		
	}
}

class editor_checkbox_st1u extends editor_checkbox_st1
{
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		$this->context[$long_name]['model']='u';
		parent::bootstrap();
	}
}

class editor_checkbox_st1d extends editor_checkbox_st1
{
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		$this->context[$long_name]['model']='d';
		parent::bootstrap();
	}
}
#-------------------------------------------------------------------------



class query_result_viewer_codes extends dom_any
{
	function __construct()
	{
		dom_any::__construct('table');
		$this->etype=get_class($this);
		$this->row=new dom_tr;
		$this->append_child($this->row);
		$this->cell=new dom_td;
		$this->row->append_child($this->cell);
		
		$this->txt=new dom_statictext;
		$this->cell->append_child($this->txt);
		
		$this->keys=Array();
		$this->args=Array();
		
		$this->colcn=0;
		
		
		/*
		$this->edittbl;this->editcol - pass directly to $context[$this->long_name.'.'.$this->editor[$col]->name][tblname/colname]
		
		$keycols=Array('id');
		foreach($row as $k => $v)$this->args[$k]=$v;
		foreach($keycols as $k)$this->keys[$k]=$this->args[$k]
		
		$col_caps
		
		*/
		
		
	}
	
	function add_col($capt,$editor,$arg)
	{
		$this->col_caps[$this->colcn]=$capt;
		$this->col_vars[$this->colcn]=$arg;
		editor_generic::addeditor('ed'.$this->colcn,$editor);
		$this->cell->append_child($editor);
		$this->colcn++;
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(is_array($this->editors))
			foreach($this->editors as $k => $e)
			{
				$e->keys=&$this->keys;
				$e->args=&$this->args;
				$e->context=&$this->context;
				if(isset($this->editdb))$this->context[$this->long_name.'.'.$k]['dbname']=$this->editdb;
				$this->context[$this->long_name.'.'.$k]['tblname']=$this->edittbl;
				if(isset($e->validator_class))$this->context[$this->long_name.'.'.$k]['validator_class']=$e->validator_class;
				if(get_class($e)=='editor_text_st1' && isset($this->fltr_id))
					$e->main->attributes['onkeypress']=
						"var k=event.keyCode;".
						"if(k==13)".
						"{".
							"var x=\$i('".js_escape($this->fltr_id)."');".
							"x.focus();x.selectionStart=0;x.selectionEnd=x.value.length;".
							"return false;".
						"}".
						"return true;";
				$e->bootstrap();
			}
		if(is_array($this->col_vars))
			foreach($this->col_vars as $n => $arg)
			{
				$this->context[$this->long_name.'.ed'.$n]['var']=$arg;
				$this->context[$this->long_name.'.ed'.$n]['colname']=$arg;
				
			}
		
	}
	
	
	function html_inner()
	{
		global $sql;
		if(isset($this->query))
			$q=$this->query->result();
		elseif(isset($this->compiled))
			$q=$this->compiled;
		$this->row->html_head();
		$cnt=0;
		if(is_array($this->col_caps))
			foreach($this->col_caps as $e)
			{
				$this->txt->text=$e;
				$cnt++;
				$this->cell->html_head();
				$this->txt->html();
				$this->cell->html_tail();
				$this->cell->id_alloc();
			}
		$this->row->html_tail();
		$this->row->id_alloc();
		
		unset($first_editor);
		$res=$sql->query($q);
		if($res)
		{
			while($row=$sql->fetcha($res))
			{
				unset($this_editor);
				foreach($row as $k => $e)
					$this->args[$k]=$e;
				
				if(is_array($this->keycols))
					foreach($this->keycols as $kk)
						$this->keys[$kk]=$this->args[$kk];
				unset($dst_rows);
				if(isset($row['bgcolor']))$this->row->css_style['background-color']=$row['bgcolor'];
				$this->row->html_head();
				for($k=0 ; $k < $this->colcn ; $k++)
				{
					$this->editors['ed'.$k]->onfocus_add='$i(\''.js_escape($this->row->id_gen()).'\').style.backgroundColor=\'#FFDDDD\';';
					$this->editors['ed'.$k]->onblur_add='$i(\''.js_escape($this->row->id_gen()).'\').style.backgroundColor=\''.$row['bgcolor'].'\';';
					$this->editors['ed'.$k]->bootstrap();
					$this->cell->html_head();
					if(get_class($this->editors['ed'.$k])!='editor_text_st1')$dst_rows[]=$this->cell->id_gen();
					if((get_class($this->editors['ed'.$k])=='editor_text_st1') && (! isset($first_editor)))
						$first_editor=$this->editors['ed'.$k]->main_id();
					if((get_class($this->editors['ed'.$k])=='editor_text_st1') && (! isset($this_editor)))
						$this_editor=$this->editors['ed'.$k]->main_id();
					$this->editors['ed'.$k]->html();
					#$this->rootnode->out($this->editors['ed'.$k]->context[$this->long_name.'.ed'.$k]['dbname']);
					#$this->rootnode->out($this->editors['ed'.$k]->keys['id']);
					$this->cell->html_tail();
					$this->cell->id_alloc();
				}
				$this->row->html_tail();
	//			$this->rootnode->out('<script type=text/javascript>$i(\''.$this->row->id_gen().'\').onclick=\''.js_escape('$i(\''.js_escape($first_editor).'\').focus();').'\';</script>');
				
				$sc='';
				if(is_array($dst_rows))
					foreach($dst_rows as $r)
					$sc.='$i(\''.js_escape($r).'\').setAttribute("onclick",\''.js_escape('var a=$i(\''.js_escape($this_editor).'\');if(a){a.focus();a.selectionStart=0;a.selectionEnd=a.value.length;};').'\');';
				//	$this->cell->attributes['onclick']='$i(\''.js_escape($first_editor->id_gen()).'\').focus();');
				$this->rootnode->endscripts[]=$sc;
				$this->row->id_alloc();
			}
			$sc='first_row_ed=function(){return $i(\''.js_escape($first_editor).'\');};';
			$this->rootnode->endscripts[]=$sc;
		}
		
		else $this->txt->text='error';
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}

class query_result_viewer_codes_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='query_result_viewer_codes_test';
		editor_generic::addeditor('qw',new query_result_viewer_codes);
		$this->append_child($this->editors['qw']);
		$qw=&$this->editors['qw'];
		$qw->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border']='2px solid black';
	}
	
	function setup()
	{
		$qw=&$this->editors['qw'];
		#$qw->compiled='show columns from `*settings`';
		$qw->query=new query_gen_ext;
		$qr=&$qw->query;
		$n=0;
		$list=Array('id','name','code');
		$edtrs['id']=new editor_statictext;
		$edtrs['name']=new editor_text_st1;
		$edtrs['name']->main->css_style['width']='400px';
		$edtrs['name']->validator_class='validator_class_test';
		$edtrs['code']=new editor_text_st1;
		$edtrs['code']->validator_class='validator_integer';
		#$edtrs['code']=new editor_statictext;
		foreach($list as $c)
		{
			$n++;
			$qr->what->exprs[]=new sql_column(NULL,'a',$c,$c);
			$qw->add_col('col-'.$c,$edtrs[$c],$c);
		}
		$qr->from->exprs[]=new sql_column(NULL,'barcodes_raw',NULL,'a');
		#$qr->lim_count=100;
		
		$qw->editdb=NULL;
		$qw->edittbl='barcodes_raw';
		$qw->keycols=Array('id');
		
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->setup();
		
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->bootstrap();
		}
	
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}

$tests_m_array['simple']['query_result_viewer_codes_test']='query_result_viewer_codes_test';

class query_result_viewer_srch_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='query_result_viewer_srch_test';
		
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		editor_generic::addeditor('fltr',new editor_text);
		$this->sdiv->append_child($this->editors['fltr']);
		
		$this->rdiv=new dom_div;
		$this->append_child($this->rdiv);
		editor_generic::addeditor('qw',new query_result_viewer_codes);
		$this->rdiv->append_child($this->editors['qw']);
	}
	
	function setup($qw)
	{
		
		$fltr=preg_replace('/  +/',' ',$_SESSION['fltr']);
		$fltr=preg_replace('/%/','\\%',$fltr);
		$fltr=preg_replace('/^ /','',$fltr);
		$fltr=preg_replace('/ $/','',$fltr);
		$fltr=preg_replace('/_/','\\_',$fltr);
		$fltr=preg_replace('/ /','%',$fltr);
		
		#$qw=&$this->editors['qw'];
		#$qw->compiled='show columns from `*settings`';
		$qw->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border']='2px solid black';
		
		$qw->query=new query_gen_ext;
		$qr=&$qw->query;
		$n=0;
		$list=Array('id' => 'id','name' => 'Наименование','code' => 'Штрихкод');
		$edtrs['id']=new editor_statictext;
		$edtrs['name']=new editor_text_st1;
		$edtrs['name']->main->css_style['width']='400px';
		$edtrs['name']->validator_class='validator_class_test';
		$edtrs['code']=new editor_text_st1;
		$edtrs['code']->validator_class='validator_integer';
		#$edtrs['code']=new editor_statictext;
		foreach($list as $c => $nn)
		{
			$n++;
			$qr->what->exprs[]=new sql_column(NULL,'a',$c,$c);
			$qw->add_col($nn,$edtrs[$c],$c);
		}
		$qr->from->exprs[]=new sql_column(NULL,'barcodes_raw',NULL,'a');
		if(preg_match('/[0-9]{12}/',$fltr))
		{
			$qr->where->exprs[]=new sql_expression('LIKE',
				Array(
					new sql_column(NULL,NULL,'code',NULL),
					new sql_immed($fltr,NULL)
				)
				,NULL);
		}else{
			$qr->where->exprs[]=new sql_expression('LIKE',
				Array(
					new sql_column(NULL,NULL,'name',NULL),
					new sql_immed('%'.$fltr.'%',NULL)
				)
				,NULL);
		}
		$qr->lim_count=50;
		
		#$qw->editdb='dbfp';
		$qw->edittbl='barcodes_raw';
		$qw->keycols=Array('id');
		
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->setup($this->editors['qw']);
		$this->context[$this->long_name.'.fltr']['var']='@@fltr';
		$this->context[$this->long_name]['retid']=$this->rdiv->id_gen();
		$this->args['@@fltr']=$_SESSION['fltr'];
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->bootstrap();
		}
	
	}
	
	function handle_event($ev)
	{
		if($ev->rem_name=='fltr')
		{
			//child node targeted event
			
			$customid=$ev->context[$ev->parent_name]['retid'];
			$oid=$ev->context[$ev->long_name]['oid'];
			$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$_SESSION['fltr']=$_POST['val'];
			//common part
			$r= new query_result_viewer_codes;
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->name=$ev->parent_name.'.qw';
			$r->etype=$ev->parent_type.'.query_result_viewer_codes';
			$this->setup($r);
			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');".
			"try{nya.innerHTML=";
			reload_object($r);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
		
		}
		editor_generic::handle_event($ev);
	}
	
}

$tests_m_array['sandbox']['query_result_viewer_srch_test']='query_result_viewer_srch_test';

##########################################################################################
class editor_code_ref extends dom_any
{
	function __construct()
	{
		parent::__construct('a');
		$this->text=new dom_statictext;
		$this->append_child($this->text);
		$this->etype=get_class($this);
		$this->main=$this;
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->arg_key=$this->context[$this->long_name]['var'];
		//$this->text=$this->args[$this->context[$this->long_name]['var']];
	}
	
	function html()
	{
		$this->text->text=$this->args[$this->arg_key];
		$this->attributes['href']=preg_replace('/%s/',$this->args['id'],$this->href);
		parent::html();
	}
}















###################################################################################
###################################################################################
###################################################################################

class query_result_viewer_codessel extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='query_result_viewer_codessel';
		
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		
		$tbl=new dom_any('table');
		$this->sdiv->append_child($tbl);
		$tr=new dom_any('tr');
		$tbl->append_child($tr);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		$td->append_child(new dom_statictext('Набор'));
		
		$td=new dom_any('td');
		$tr->append_child($td);
		$td->attributes['colspan']='2';
		$td->css_style['text-align']='center';
		$td->append_child(new dom_statictext('Фильтр'));
		
		$td=new dom_any('td');
		$tr->append_child($td);
		$td->attributes['colspan']='3';
		$td->css_style['text-align']='center';
		$td->append_child(new dom_statictext('Действия'));
		
		$td=new dom_any('td');
		$tr->append_child($td);
		$td->attributes['colspan']='2';
		$td->css_style['text-align']='center';
		$td->append_child(new dom_statictext('Отчет'));
		
		$td=new dom_any('td');
		$tr->append_child($td);
		$td->css_style['text-align']='center';
		$td->append_child(new dom_statictext('Плотн'));
		
		$td=new dom_any('td');
		$tr->append_child($td);
		$td->css_style['text-align']='center';
		$td->append_child(new dom_statictext('Скор'));
		
		$td=new dom_any('td');
		$tr->append_child($td);
		$td->css_style['text-align']='center';
		$td->append_child(new dom_statictext('host'));
		
		$td=new dom_any('td');
		$tr->append_child($td);
		$td->css_style['text-align']='center';
		$td->append_child(new dom_statictext('printer'));
/* ---------------------------------------------------------- */
		
		$tr=new dom_any('tr');
		$tbl->append_child($tr);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		editor_generic::addeditor('current_task',new editor_text_autosuggest_query);
		$td->append_child($this->editors['current_task']);
		$this->editors['current_task']->ed->css_style['width']='5em';
		
		$td=new dom_any('td');
		$tr->append_child($td);
//		$this->sdiv->append_child(new dom_statictext('Поиск'));
		editor_generic::addeditor('fltr',new editor_text);
//		$this->sdiv->append_child($this->editors['fltr']);
		$td->append_child($this->editors['fltr']);
		$this->editors['fltr']->attributes['title']="Фильтр по наименованию: введите части наименования двери по порядку, разделенные пробелами.";
		//$this->editors['fltr']->node_name='span';
		
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$lbl=new dom_any('label');
//		$this->sdiv->append_child($lbl);
		$td->append_child($lbl);
		editor_generic::addeditor('only_selected',new editor_checkbox);
		$lbl->append_child($this->editors['only_selected']);
		$this->editors['only_selected']->attributes['title']='Отображать только выбранные для печати.';
		$txt=new dom_statictext;
		$lbl->append_child($txt);
		$txt->text='Только выбранные';
		
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		editor_generic::addeditor('clear_btn',new editor_button);
//		$this->sdiv->append_child($this->editors['clear_btn']);
		$td->append_child($this->editors['clear_btn']);
		$this->editors['clear_btn']->attributes['value']='Очистить';
		$this->editors['clear_btn']->attributes['title']='Удалить все выбранные для печати';
		
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$sp=new dom_any('span');
		$td->append_child($sp);
		$a=new dom_any('a');
		$a->attributes['href']='/codes-print.php';
		$a->attributes['target']='_blank';
		$a->attributes['title']='Страница печати';
		#$sp->append_child($a);
		#$a->append_child(new dom_statictext('Печать'));
		
		/*
		editor_generic::addeditor('print_direct_btn',new editor_button_image);
		$this->editors['print_direct_btn']->attributes['src']='/i/print.png';
		$this->editors['print_direct_btn']->attributes['title']='Печать через CUPS';
		$td->append_child($this->editors['print_direct_btn']);
		*/
		
		editor_generic::addeditor('print_direct_btn_acct',new editor_button_image);
#		$this->editors['print_direct_btn_acct']->attributes['value']='DP♻';
		$this->editors['print_direct_btn_acct']->attributes['src']='/i/print-a.png';
		$this->editors['print_direct_btn_acct']->attributes['title']='Печать через CUPS и учесть';
		$td->append_child($this->editors['print_direct_btn_acct']);
		
		editor_generic::addeditor('print_direct_btn_+1',new editor_button_image);
#		$this->editors['print_direct_btn_acct']->attributes['value']='DP♻';
		$this->editors['print_direct_btn_+1']->attributes['src']='/i/print-+1.png';
		$this->editors['print_direct_btn_+1']->attributes['title']='Печать через CUPS и учесть (повтор последней)';
		$td->append_child($this->editors['print_direct_btn_+1']);
		
		
		$td=new dom_any('td');
		$tr->append_child($td);
		editor_generic::addeditor('total_count',new query_result_viewer_single);
		$td->append_child($this->editors['total_count']);
		$this->editors['total_count']->attributes['title']='Всего выбрано для печати';
		
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$sp=new dom_any('span');
		if($_SESSION['uid']==0)$td->append_child($sp);
		$a=new dom_any('a');
		$a->attributes['href']='/dump.php?d=,&e=UTF-8&q='.urlencode("SELECT * FROM `barcodes_raw` ORDER BY name");
		//$a->attributes['target']='_blank';
		$a->attributes['title']='Сохранить в CSV';
		$sp->append_child($a);
		$a->append_child(new dom_statictext('Сохранить'));
		
		
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$sp=new dom_any('span');
		$td->append_child($sp);
		$a=new dom_any('a');
		$this->csv_report_link=$a;
		//$a->attributes['target']='_blank';
		$a->attributes['title']='Сохранить в CSV';
		$sp->append_child($a);
		$a->append_child(new dom_statictext('Сохранить'));
		
		$td=new dom_any('td');//плотн
		$tr->append_child($td);
		editor_generic::addeditor('density',new editor_select);
		for($k=0;$k<16;$k++)$this->editors['density']->options[$k]=$k;
		$td->append_child($this->editors['density']);
		
		$td=new dom_any('td');//скор
		$tr->append_child($td);
		editor_generic::addeditor('speed',new editor_select);
		for($k=0;$k<7;$k++)$this->editors['speed']->options[$k]=$k;
		$td->append_child($this->editors['speed']);
		
		$td=new dom_any('td');//host
		$tr->append_child($td);
		editor_generic::addeditor('ipp_host',new editor_text);
		$td->append_child($this->editors['ipp_host']);
		$this->editors['ipp_host']->main->css_style['width']='5em';
		
		$td=new dom_any('td');//printer
		$tr->append_child($td);
		editor_generic::addeditor('ipp_printer',new editor_text);
		$td->append_child($this->editors['ipp_printer']);
		$this->editors['ipp_printer']->main->css_style['width']='9em';
		
/* ---------------------------------------------------------- */
		$tbl=new dom_any('table');
		$this->sdiv->append_child($tbl);
		$tr=new dom_any('tr');
		$tbl->append_child($tr);
		
		$td=new dom_any('td');
		$t=new dom_statictext;
		$t->text='Лента';
		$tr->append_child($td->append_child($t));
		
		$td=new dom_any('td');
		editor_generic::addeditor('ribbon_init',new editor_text_autofetch);
		$this->editors['ribbon_init']->ed->css_style['width']='5em';
		$tr->append_child($td->append_child($this->editors['ribbon_init']));
		
		$td=new dom_any('td');
		$this->ribbon_reset=new dom_textbutton('=>');
		$tr->append_child($td->append_child($this->ribbon_reset));
		
		$td=new dom_any('td');
		editor_generic::addeditor('ribbon_remaining',new editor_text_autofetch);
		$this->editors['ribbon_remaining']->ed->css_style['width']='5em';
		$tr->append_child($td->append_child($this->editors['ribbon_remaining']));
		
		$td=new dom_any('td');
		$t=new dom_statictext;
		$t->text='Этикетки';
		$tr->append_child($td->append_child($t));
		
		$td=new dom_any('td');
		editor_generic::addeditor('labels_init',new editor_text_autofetch);
		$this->editors['labels_init']->ed->css_style['width']='5em';
		$tr->append_child($td->append_child($this->editors['labels_init']));
		
		$td=new dom_any('td');
		$this->labels_reset=new dom_textbutton('=>');
		$tr->append_child($td->append_child($this->labels_reset));
		
		$td=new dom_any('td');
		editor_generic::addeditor('labels_remaining',new editor_text_autofetch);
		$this->editors['labels_remaining']->ed->css_style['width']='5em';
		$tr->append_child($td->append_child($this->editors['labels_remaining']));
		
		$td=new dom_any('td');
		editor_generic::addeditor('subtract_current',new editor_button);
		$this->editors['subtract_current']->attributes['value']='Учесть';
		$tr->append_child($td->append_child($this->editors['subtract_current']));
		
		
		
		$tbl=new dom_any('table');
		$this->sdiv->append_child($tbl);
		$tr=new dom_any('tr');
		$tbl->append_child($tr);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$this->ed_zero=new dom_any_noterm('input');
		$this->ed_zero->attributes['type']='button';
		$this->ed_zero->attributes['value']='«';
		$this->ed_zero->attributes['title']='В начало';
		$td->append_child($this->ed_zero);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$this->ed_less=new dom_any_noterm('input');
		$this->ed_less->attributes['type']='button';
		$this->ed_less->attributes['value']='<';
		$this->ed_less->attributes['title']='На страницу назад.';
		$td->append_child($this->ed_less);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		editor_generic::addeditor('ed_count',new editor_text);
		$td->append_child($this->editors['ed_count']);
		$this->editors['ed_count']->main->css_style['width']='4em';
		$this->editors['ed_count']->attributes['title']='Количество строк.';
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		editor_generic::addeditor('ed_offset',new editor_text);
		$td->append_child($this->editors['ed_offset']);
		$this->editors['ed_offset']->main->css_style['width']='4em';
		$this->editors['ed_offset']->attributes['title']='Пропустить строк.';
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$this->ed_more=new dom_any_noterm('input');
		$this->ed_more->attributes['type']='button';
		$this->ed_more->attributes['value']='>';
		$this->ed_more->attributes['title']='На страницу вперед.';
		$td->append_child($this->ed_more);
		
		
		
		
		
		
		
		
		
		
		$this->rdiv=new dom_div;
		$this->append_child($this->rdiv);
		editor_generic::addeditor('qw',new query_result_viewer_codes);
		$this->rdiv->append_child($this->editors['qw']);
	}
	
	function setup_h()
	{
		if(!isset($_SESSION['current_task']))$_SESSION['current_task']=0;
		$this->current_task=intval($_SESSION['current_task']);
		$this->editors['total_count']->compiled="SELECT SUM(`count`) FROM `barcodes_print` WHERE task=".$this->current_task;
	}
	
	function setup($qw)
	{
		
		if(!isset($_SESSION['ed_count']))$_SESSION['ed_count']=50;
		$ed_count=intval($_SESSION['ed_count']);
		if($ed_count==0)$ed_count=1;
		
		if(!isset($_SESSION['ed_offset']))$_SESSION['ed_offset']=0;
		$ed_offset=intval($_SESSION['ed_offset']);
		$this->setup_h();
		$this->csv_report_link->attributes['href']='/dump.php?n=pr'.urlencode(date("YmdHi")).'&d=,&e=UTF-8&q='.urlencode("SELECT barcodes_print.id,barcodes_raw.name,barcodes_print.`count`,barcodes_raw.code FROM barcodes_raw,barcodes_print WHERE count <> 0 AND barcodes_raw.id=barcodes_print.id AND barcodes_print.task=".$this->current_task." ORDER BY name");
		
		
		$fltr=preg_replace('/  +/',' ',$_SESSION['fltr']);
		$fltr=preg_replace('/%/','\\%',$fltr);
		$fltr=preg_replace('/^ /','',$fltr);
		$fltr=preg_replace('/ $/','',$fltr);
		$fltr=preg_replace('/_/','\\_',$fltr);
		$fltr=preg_replace('/ /','%',$fltr);
		
		
		#$qw=&$this->editors['qw'];
		#$qw->compiled='show columns from `*settings`';
		$qw->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border']='2px solid black';
		
		$qw->query=new query_gen_ext;
		$qr=&$qw->query;
		$n=0;
		$list=Array('id' => 'id','name' => 'Наименование','code' => 'Штрихкод');
		$edtrs['id']=new editor_statictext;
		$edtrs['name']=new editor_statictext;
#		$edtrs['code']=new editor_statictext;
#		$edtrs['code']=new editor_code_ref;
#		$edtrs['code']->href="/codes-print.php?forceid=%s";
#		$edtrs['code']->attributes['target']="_blank";
		$edtrs['code']=new editor_divbutton;
		$edtrs['code']->usevar=true;
		$edtrs['code']->css_style['border']='1px blue solid';
		$edtrs['code']->css_style['background']='lightgray';
		$edtrs['code']->attributes['title']="Печатать данное наименование";
		$edtrs['count']=new editor_text_st1;
		$edtrs['count']->validator_class='validator_integer';
		#$edtrs['code']=new editor_statictext;
		foreach($list as $c => $nn)
		{
			$n++;
			$qr->what->exprs[]=new sql_column(NULL,'ref',$c,$c);
			$qw->add_col($nn,$edtrs[$c],$c);
		}
		$qr->what->exprs[]=new sql_column(NULL,'sel','count','count');
		$qr->what->exprs[]=new sql_immed($this->current_task,'task');
		$qw->add_col('Количество',$edtrs['count'],'count');
		//show own codes in different color
		$qr->what->exprs[]=new sql_list('if',Array(
			new sql_expression('=',Array(
				new sql_column(NULL,'ref','isown'),
				new sql_immed(1)
				)),
			new sql_immed('gray'),
			new sql_immed('white')
		),'bgcolor');
		
		$qr->from->exprs[]=new sql_column(NULL,'barcodes_raw',NULL,'ref');
		#$qr->from->exprs[]=new sql_column('dbfp','barcodes_print',NULL,'sel');
		//check for code
		if(preg_match('/^[0-9]{13}[^0-9]*$/',$fltr))
		{
			//looks like code
			$qr->where->exprs[]=new sql_expression('LIKE',
				Array(
					new sql_column(NULL,NULL,'code',NULL),
					new sql_immed($fltr,NULL)
				)
				,NULL);
		}else{
			//looks like name parts
			$qr->where->exprs[]=new sql_expression('LIKE',
				Array(
					new sql_column(NULL,'ref','name',NULL),
					new sql_immed('%'.$fltr.'%',NULL)
				)
				,NULL);
		}
		//add task condition
		//$qr->where->exprs[]=new sql_expression('=',
		$join=Array(
			'type' => $_SESSION['selonly']?'JOIN':'LEFT OUTER JOIN',
			'what' => new sql_list(NULL,
				Array(new sql_column(NULL,'barcodes_print',NULL,'sel')),
				NULL),
			'on'   => new sql_expression('AND',
				Array(new sql_expression('=',
					Array(
						new sql_column(NULL,'ref','id',NULL),
						new sql_column(NULL,'sel','id',NULL),
					)),
					new sql_expression('=',
					Array(
						new sql_column(NULL,'sel','task'),
						new sql_immed($this->current_task)
					))
					)
				)
			
			);
		$qr->joins->exprs[]=(object) $join;
		$ord=new sql_column(NULL,'ref','name',NULL);
		$qr->order->exprs[]=$ord;
		/*			
		$qr->where->exprs[]=new sql_expression('=',
			Array(
				new sql_column(NULL,'ref','id',NULL),
				new sql_column(NULL,'sel','id',NULL),
			)
			,NULL);*/
		if($_SESSION['selonly'])unset($qr->lim_count);
		else $qr->lim_count=$ed_count;
		if($_SESSION['selonly'])unset($qr->lim_offset);
		else $qr->lim_offset=$ed_offset;
		
		#$qw->editdb='dbfp';
		$qw->edittbl='barcodes_print';
		$qw->keycols=Array('id','task');
		
		$qw->fltr_id=$this->context[$this->long_name]['fltr_id'];
		
		
	}
	
	function selclear()
	{
		global $sql;
		$q=new query_gen_ext('delete');
		$q->from->exprs[]=new sql_column(NULL,'barcodes_print',NULL,NULL);
		$q->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,"task"),
			new sql_immed($_SESSION['current_task'])
			));
		$query=$q->result();
		$sql->query($query);
		
	}
	
	function clean_zeroes()
	{
		global $sql;
		$q=new query_gen_ext('delete');
		$q->from->exprs[]=new sql_column(NULL,'barcodes_print',NULL,NULL);
		$q->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'count',NULL),new sql_immed(0,NULL)),NULL);
		$query=$q->result();
		$sql->query($query);
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		#counters
		$this->context[$this->long_name.'.labels_init']['tblname']='barcodes_counters';
		$this->context[$this->long_name.'.labels_init']['colname']='init';
		$this->context[$this->long_name.'.labels_remaining']['tblname']='barcodes_counters';
		$this->context[$this->long_name.'.labels_remaining']['colname']='current';
		$this->context[$this->long_name.'.ribbon_init']['tblname']='barcodes_counters';
		$this->context[$this->long_name.'.ribbon_init']['colname']='init';
		$this->context[$this->long_name.'.ribbon_remaining']['tblname']='barcodes_counters';
		$this->context[$this->long_name.'.ribbon_remaining']['colname']='current';
		
		foreach($this->editors as $n => $e)
			$this->context[$this->long_name.'.'.$n]['var']=$n;
		
		$this->context[$this->long_name.'.fltr']['var']='@@fltr';
		$this->context[$this->long_name.'.only_selected']['var']='@@selonly';
		$this->context[$this->long_name.'.ed_count']['var']='@@ed_count';
		$this->context[$this->long_name.'.ed_offset']['var']='@@ed_offset';
		$this->context[$this->long_name.'.current_task']['var']='@@current_task';
		$this->context[$this->long_name.'.current_task']['rawquery']='SELECT DISTINCT `task` FROM `barcodes_print`';
		$this->context[$this->long_name]['retid']=$this->rdiv->id_gen();
		$this->context[$this->long_name]['fltr_id']=$this->editors['fltr']->main_id();
		$this->context[$this->long_name]['selonly_id']=$this->editors['only_selected']->main_id();
		$this->context[$this->long_name]['ed_offset_id']=$this->editors['ed_offset']->main_id();
		$this->context[$this->long_name]['total_count_id']=$this->editors['total_count']->main_id();
		$this->labels_reset->attributes['onclick']="var a=\$i('".js_escape($this->editors['labels_remaining']->main->id_gen())."');".
			"a.focus();a.value=\$i('".js_escape($this->editors['labels_init']->main->id_gen())."').value;";
		$this->ribbon_reset->attributes['onclick']="var a=\$i('".js_escape($this->editors['ribbon_remaining']->main->id_gen())."');".
			"a.focus();a.value=\$i('".js_escape($this->editors['ribbon_init']->main->id_gen())."').value;";
		$this->args['@@fltr']=$_SESSION['fltr'];
		$this->args['@@selonly']=$_SESSION['selonly'];
		if(!isset($_SESSION['ed_count']))$_SESSION['ed_count']=50;
		if(!isset($_SESSION['ed_offset']))$_SESSION['ed_offset']=0;
		$this->args['@@ed_count']=intval($_SESSION['ed_count']);
		$this->args['@@ed_offset']=intval($_SESSION['ed_offset']);
		$this->args['@@current_task']=intval($_SESSION['current_task']);

		$this->setup($this->editors['qw']);
		
		$this->ed_more->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"var cnt=\$i('".$this->editors['ed_count']->main_id()."');".
			"ofs.focus();".
			"var iofs=isNaN(ofs.value)?0:parseInt(ofs.value);".
			"var icnt=isNaN(cnt.value)?0:parseInt(cnt.value);".
			"ofs.value=iofs+icnt;".
//			"this.focus();".
			"";
		$this->ed_less->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"var cnt=\$i('".$this->editors['ed_count']->main_id()."');".
			"ofs.focus();".
			"var iofs=isNaN(ofs.value)?0:parseInt(ofs.value);".
			"var icnt=isNaN(cnt.value)?0:parseInt(cnt.value);".
			"ofs.value=(iofs>=icnt)?iofs-icnt:0;".
//			"this.focus();".
			"";
		$this->ed_zero->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"ofs.focus();".
			"ofs.value=0;".
			"this.focus();".
			"";
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		
		$this->args['density']=$this->rootnode->setting_val($this->oid,$this->long_name.'.density',7);
		$this->args['speed']=$this->rootnode->setting_val($this->oid,$this->long_name.'.speed',5);
		$this->args['ipp_host']=$this->rootnode->setting_val($this->oid,$this->long_name.'.ipp_host','localhost');
		$this->args['ipp_printer']=$this->rootnode->setting_val($this->oid,$this->long_name.'.ipp_printer','/printers/TLP2824_');
		unset($this->editors['labels_init']->keys);$this->editors['labels_init']->keys=Array('id'=>0);
		unset($this->editors['labels_remaining']->keys);$this->editors['labels_remaining']->keys=Array('id'=>0);
		unset($this->editors['ribbon_init']->keys);$this->editors['ribbon_init']->keys=Array('id'=>1);
		unset($this->editors['ribbon_remaining']->keys);$this->editors['ribbon_remaining']->keys=Array('id'=>1);
		//print "<pre>";print_r($this->keys);print "</pre>";
		foreach($this->editors as $e)
			$e->bootstrap();
		$this->editors['fltr']->main->attributes['onkeypress']=
			"var k=event.keyCode;".
			"if(k==13)".
			"{".
				"var x=first_row_ed();".
				"x.focus();x.selectionStart=0;x.selectionEnd=x.value.length;".
				"return false;".
			"}".
			"return true;";
		$this->editors['print_direct_btn_acct']->attributes['onkeypress'].=
			"var k=event.charCode;".
			"if(k==49)".
			"{".
				"var x=\$i('".js_escape($this->editors['print_direct_btn_+1']->main->id_gen())."');".
				"x.click();".
				"return false;".
			"}".
			"if(k==43)".
			"{".
				"var x=first_row_ed();".
				"x.focus();y=parseInt(x.value);if(isNaN(y))x.value=4;else x.value=y+4;this.focus();".
				"return false;".
			"}".
			"if(k==108)".
			"{".
				"var x=\$i('".js_escape($this->editors['labels_remaining']->main->id_gen())."');".
				"x.focus();x.value=parseInt(x.value)+2;this.focus();".
				"return false;".
			"}".
			"if(k==76)".
			"{".
				"var x=\$i('".js_escape($this->editors['labels_remaining']->main->id_gen())."');".
				"x.focus();x.value=\$i('".js_escape($this->editors['labels_init']->main->id_gen())."').value;this.focus();".
				"return false;".
			"}".
			"if(k==114)".
			"{".
				"var x=\$i('".js_escape($this->editors['ribbon_remaining']->main->id_gen())."');".
				"x.focus();x.value=parseInt(x.value)+5;this.focus();".
				"return false;".
			"}".
			"if(k==82)".
			"{".
				"var x=\$i('".js_escape($this->editors['ribbon_remaining']->main->id_gen())."');".
				"x.focus();x.value=\$i('".js_escape($this->editors['ribbon_init']->main->id_gen())."').value;this.focus();".
				"return false;".
			"}";
			
	}
	
	function reset_lr($id)
	{
		global $sql;
		$sql->query("UPDATE barcodes_counters SET current=init WHERE id=".$id);
	}
	
	function subtract_current()
	{
		global $sql;
		$this->setup_h();
		$printed=$sql->fetch1($sql->query("SELECT SUM(`printed`) FROM barcodes_print WHERE task=".$this->current_task));
		if($printed!=0)
		{
		$sql->query("INSERT INTO barcodes_printed SELECT `id`, NULL as `when`,`printed` as `count` FROM barcodes_print WHERE task=".$this->current_task." AND `printed`>0");
		$sql->query("UPDATE barcodes_print SET `count`=`count`-`printed` WHERE task=".$this->current_task);
		$sql->query("UPDATE barcodes_counters SET current=current-(SELECT SUM(`printed`) FROM barcodes_print WHERE task=".$this->current_task.")");
		$sql->query("UPDATE barcodes_print SET `printed`=0 WHERE task=".$this->current_task);
		$sql->query("DELETE FROM barcodes_print WHERE `count`=0");
		}else print "alert('Ничего не было напечатано');";
		
	}
	
	function print_plus_1()
	{
		global $sql;
		$sql->query("UPDATE barcodes_counters SET current=current-1");
		$this->print_job("\nP1\n");
	}
	
	
	
	
	function print_direct($id=-1)
	{
		global $sql;// !
		$current_task=intval($_SESSION['current_task']);
		if($id != -1)
			$s=" AND barcodes_raw.id=".$id;
		else
			$s="";
		$result=$sql->query("SELECT barcodes_print.id,barcodes_raw.name,barcodes_print.`count`,barcodes_raw.code FROM barcodes_raw,barcodes_print WHERE count <> 0 AND barcodes_raw.id=barcodes_print.id AND barcodes_print.task=".$current_task.$s." ORDER BY name LIMIT 1");
		$row=$sql->fetcha($result);
		$lim_labels=$sql->fetch1($sql->query("SELECT `current` FROM `barcodes_counters` WHERE `id`=0"));
		$lim_ribbon=$sql->fetch1($sql->query("SELECT `current` FROM `barcodes_counters` WHERE `id`=1"));
		$count=min($row['count'],min($lim_labels,$lim_ribbon));
		$name=iconv('UTF-8','CP866//IGNORE',$row['name']);
		if(strlen($name)<55)
		{
			$ln=27;
			$s1=substr($name,0,$ln);
			$s2=substr($name,$ln,$ln);
		}else{
			$ln=36;
			$s1=substr($name,0,$ln);
			$s2=substr($name,$ln,$ln);
			$s3=substr($name,$ln*2,$ln);
		}
		$s1=preg_replace('/"/','\\"',preg_replace('/\\\\/','\\\\',$s1));
		$s2=preg_replace('/"/','\\"',preg_replace('/\\\\/','\\\\',$s2));
		$s3=preg_replace('/"/','\\"',preg_replace('/\\\\/','\\\\',$s3));
		$barcode=$row['code'];
		
		$sql->query("UPDATE `barcodes_print` SET `printed`=".$count." WHERE `id`=".$row['id']." AND `task`=".$current_task);

		if(preg_match('/^[ 0]+$/',$barcode))
			$barcode_part="";
		else
			$barcode_part="B10,80,0,E30,4,4,120,B,\"".$barcode."\"\n";
		if(strlen($name)<55)
		{
			$print=	"N\n".
					"I8,10,001\n".
					"A8,10,0,4,1,1,N,\"".$s1."\"\n".
					"A8,39,0,4,1,1,N,\"".$s2."\"\n".
					$barcode_part.
					"P".$count."\n";
		}else{
			$print=	"N\n".
					"I8,10,001\n".
					"A7,10,0,2,1,1,N,\"".$s1."\"\n".
					"A7,31,0,2,1,1,N,\"".$s2."\"\n".
					"A7,52,0,2,1,1,N,\"".$s3."\"\n".
					$barcode_part.
					"P".$count."\n";
		}
		$this->print_job($print);
		/*
		$print=	"\nS0\n".
				"OD\n".
				"D0\n".
				"N\n".
				"I8,10,001\n".
				"A7,10,0,2,1,1,N,\"".$s1."\"\n".
				"A7,31,0,2,1,1,N,\"".$s2."\"\n".
				"A7,52,0,2,1,1,N,\"".$s3."\"\n".
				"B10,80,0,E30,4,4,120,B,\"".$barcode."\"\n".
				"P1\n";
		for($k=0;$k<$count;$k++)
			$this->print_job($print);
		*/
		
		
	}
	
	function print_job($job)
	{
		global $sql;
		$settings=new settings_tool;
		$speed=$sql->q1($settings->single_query(-1,$this->name.'.speed',$_SESSION['uid'],0));
		if(!isset($speed))$speed=5;
		$density=$sql->q1($settings->single_query(-1,$this->name.'.density',$_SESSION['uid'],0));
		if(!isset($density))$density=4;
		$ipp_host=$sql->q1($settings->single_query(-1,$this->name.'.ipp_host',$_SESSION['uid'],0));
		if(!isset($ipp_host))$ipp_host='localhost';
		$ipp_printer=$sql->q1($settings->single_query(-1,$this->name.'.ipp_printer',$_SESSION['uid'],0));
		if(!isset($ipp_printer))$ipp_printer='/printers/TLP2824_';
		
		$ipp = new PrintIPP();
		$ipp->setHost($ipp_host);
		$ipp->setPrinterURI($ipp_printer);
		$ipp->setData("\nS".$speed."\nD".$density."\n".$job);
		$ipp->setRawText();
		$ipp->printJob();
	}
	
	
	function handle_event($ev)
	{
		global $sql;// !
		$changed=false;
		
		$this->name=$ev->parent_name;
		$this->context=&$ev->context;
		$this->oid=$ev->context[$ev->long_name]['oid'];
		$this->long_name=$ev->parent_name;
		
		$total_count_id=$ev->context[$ev->parent_name]['total_count_id'];
		if($ev->rem_name=='fltr')
		{
			//child node targeted event
			$_SESSION['fltr']=$_POST['val'];
			$_SESSION['ed_offset']=0;
			print "\$i('".js_escape($ev->context[$ev->parent_name]['ed_offset_id'])."').value='0';";
			print "\$i('".js_escape($ev->context[$ev->parent_name]['ed_offset_id'])."').oldval='0';";
			$changed=true;
		}
		if(preg_match('/^speed$|^density$|^ipp_host$|^ipp_printer$/',$ev->rem_name))
		{
			$settings=new settings_tool;
			$sql->query($settings->set_query(-1,$ev->long_name,$_SESSION['uid'],0,$_POST['val']));
		}
		if($ev->rem_name=='ed_count')
		{
			//child node targeted event
			$_SESSION['ed_count']=intval($_POST['val']);
			$changed=true;
		}
		if($ev->rem_name=='ed_offset')
		{
			//child node targeted event
			$_SESSION['ed_offset']=intval($_POST['val']);
			$changed=true;
		}
		if($ev->rem_name=='current_task')
		{
			//child node targeted event
			$_SESSION['current_task']=intval($_POST['val']);
			$changed=true;
		}
		if($ev->rem_name=='only_selected')
		{
			//child node targeted event
			
			$_SESSION['selonly']=$_POST['val'];
			if($_POST['val']==1)
			{
				$_SESSION['fltr']='';
				print "\$i('".js_escape($ev->context[$ev->parent_name]['fltr_id'])."').value='';";
				print "\$i('".js_escape($ev->context[$ev->parent_name]['fltr_id'])."').oldval='';";
				$_SESSION['ed_offset']=0;//intval($_POST['val']);
				print "\$i('".js_escape($ev->context[$ev->parent_name]['ed_offset_id'])."').value='0';";
				print "\$i('".js_escape($ev->context[$ev->parent_name]['ed_offset_id'])."').oldval='0';";
			}
			$changed=true;
		}
		if($ev->rem_name=='subtract_current')
		{
			$this->subtract_current();
			print "window.location.reload(true);";
			exit;
		}
		if($ev->rem_name=='clear_btn')
		{
			//child node targeted event
			
			//$_SESSION['selonly']=0;
			$_SESSION['selonly']=0;
			print "\$i('".js_escape($ev->context[$ev->parent_name]['selonly_id'])."').checked=0;";
			$this->selclear();
			$changed=true;
		}
/*		
$ipp = new PrintIPP();
$ipp->setHost("localhost");
$ipp->setPrinterURI("/printers/epson");
$ipp->setData("./testfiles/test-utf8.txt"); // Path to file.
$ipp->printJob();
*/		
		if($ev->rem_name=='print_direct_btn')
		{
			//child node targeted event
			//$_SESSION['selonly']=0;
			$this->print_direct();
			$changed=true;
		}
		
		if($ev->rem_name=='print_direct_btn_acct')
		{
			//child node targeted event
			//$_SESSION['selonly']=0;
			$this->print_direct();
			$this->subtract_current();
			print "window.location.reload(true);";
			exit;
		}
		if($ev->rem_name=='qw.ed2')
		{
			//child node targeted event
			//$_SESSION['selonly']=0;
			$this->print_direct($ev->keys['id']);
			$this->subtract_current();
			print "window.location.reload(true);";
			exit;
		}
		
		if($ev->rem_name=='print_direct_btn_+1')
		{
			//child node targeted event
			//$_SESSION['selonly']=0;
			$this->print_plus_1();
			print "window.location.reload(true);";
			exit;
		}
		
		
		
		if($changed)
		{
			//common part
			$customid=$ev->context[$ev->parent_name]['retid'];
			$oid=$ev->context[$ev->long_name]['oid'];
			$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$r=new query_result_viewer_codes;
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->name=$ev->parent_name.'.qw';
			$r->etype=$ev->parent_type.'.query_result_viewer_codes';
			$this->setup($r);
			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');".
			"try{nya.innerHTML=";
			reload_object($r);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
		}
		$evparent_name=$ev->parent_name;
		editor_generic::handle_event($ev);
		$this->clean_zeroes();
		unset($this->editors['total_count']->com_parent);
		$this->editors['total_count']->name=$evparent_name.'.total_count';
		$this->setup_h();
		print "var nya=\$i('".js_escape($total_count_id)."');".
		"try{nya.innerHTML=";
		reload_object($this->editors['total_count']);
		print "}catch(e){nya.innerHTML='exception';};";
		
	}
	
}

$tests_m_array['util']['query_result_viewer_codessel']='query_result_viewer_codessel';

//###################################################################################################################
//###################################################################################################################
//###################################################################################################################
//###################################################################################################################
//###################################################################################################################
//###################################################################################################################



class query_result_viewer_any_old extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='query_result_viewer_any';
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		
		
		
		$autotbl=new container_autotable;
		$this->sdiv->append_child($autotbl);
		editor_generic::addeditor('ed_db',new editor_text_autosuggest_query);
		$autotbl->append_child($this->editors['ed_db']);
		#$this->sdiv->append_child(new dom_any_noterm('br'));
		editor_generic::addeditor('ed_table',new editor_text_autosuggest_query);
		$autotbl->append_child($this->editors['ed_table']);
		
		$this->link_save_xml=new dom_any('a');
		$autotbl->append_child($this->link_save_xml);
		$txt=new dom_statictext('xml');
		$this->link_save_xml->append_child($txt);
		
		$this->link_save_csv=new dom_any('a');
		$autotbl->append_child($this->link_save_csv);
		$txt=new dom_statictext('csv');
		$this->link_save_csv->append_child($txt);
		
		
		editor_generic::addeditor('ed_filters',new editor_filters_ch);
		$this->sdiv->append_child($this->editors['ed_filters']);
		
		editor_generic::addeditor('ed_order',new editor_order_ch);
		$this->sdiv->append_child($this->editors['ed_order']);
		
		
		
		
		$tbl=new dom_any('table');
		$this->sdiv->append_child($tbl);
		$tr=new dom_any('tr');
		$tbl->append_child($tr);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$this->ed_zero=new dom_any_noterm('input');
		$this->ed_zero->attributes['type']='button';
		$this->ed_zero->attributes['value']='«';
		$this->ed_zero->attributes['title']='В начало';
		$td->append_child($this->ed_zero);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$this->ed_less=new dom_any_noterm('input');
		$this->ed_less->attributes['type']='button';
		$this->ed_less->attributes['value']='<';
		$this->ed_less->attributes['title']='На страницу назад.';
		$td->append_child($this->ed_less);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		editor_generic::addeditor('ed_count',new editor_text);
		$td->append_child($this->editors['ed_count']);
		$this->editors['ed_count']->ed->css_style['width']='4em';
		$this->editors['ed_count']->attributes['title']='Количество строк.';
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		editor_generic::addeditor('ed_offset',new editor_text);
		$td->append_child($this->editors['ed_offset']);
		$this->editors['ed_offset']->ed->css_style['width']='4em';
		$this->editors['ed_offset']->attributes['title']='Пропустить строк.';
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		$this->ed_more=new dom_any_noterm('input');
		$this->ed_more->attributes['type']='button';
		$this->ed_more->attributes['value']='>';
		$this->ed_more->attributes['title']='На страницу вперед.';
		$td->append_child($this->ed_more);
		
		$td=new dom_any('td');
		$tr->append_child($td);
		
		editor_generic::addeditor('ed_rowcount',new query_result_viewer_single);
		$td->append_child($this->editors['ed_rowcount']);
		
		
		
		
		
		
		
		
		
		
		$this->rdiv=new dom_div;
		$this->append_child($this->rdiv);
		editor_generic::addeditor('qw',new query_result_viewer_codes);
		$this->rdiv->append_child($this->editors['qw']);
		
		editor_generic::addeditor('an',new editor_insert_ch);
		$this->append_child($this->editors['an']);
		
		$this->settings->ed_count=10;
		$this->settings->ed_offset=0;
		$this->settings->ed_table='barcodes_raw';
		$this->settings->ed_db='';
		$this->settings->order=serialize(Array());
		$this->settings->filters_where=serialize(Array());
		$this->settings->insert_args=serialize(Array());
		//print(isset($this->settings->ed_table));
		$this->settings_io=new QRVA_settings_io;
		$this->struct_io=new qrva_guess_struct;
	}
	
	function is_pk($a,$structure)
	{
		if(is_array($structure->keys))
			foreach($structure->keys as $key)
				if($key['key']=='PRIMARY' && $key['name']==$a) return 1;
		return 0;
	}
	
	function setup($qw)
	{
		global $ddc_tables;
		if($this->settings->ed_count==0)$this->settings->ed_count=1;
		$fl->edittbl=$this->settings->ed_table;
		$fl->editdb=$this->settings->ed_db;
		//$fl->filters_where;
		//print_r($_SESSION);
		
		#$qw=&$this->editors['qw'];
		#$qw->compiled='show columns from `*settings`';
		$qw->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border-collapse']='collapse';
		$qw->cell->css_style['border']='2px solid black';
		
		$qw->query=new query_gen_ext;
		$qr=&$qw->query;
		$n=0;
		//structure request
		//$structure=$ddc_tables[$this->settings->ed_table];
		$structure=$this->struct_io->load($this);
		
		//$list=Array('id' => 'id','name' => 'Наименование','code' => 'Штрихкод');
		if(is_array($structure->cols))
			foreach($structure->cols as $col)
			{
				if($this->is_pk($col['name'],$structure))
					$edtr=new editor_statictext;
				elseif(isset($col['editor']) && $col['editor']!='')
					$edtr=new $col['editor']($col);
				else
					$edtr=new editor_text_st1;
				
				$n++;
				$qr->what->exprs[]=new sql_column(NULL,'t',$col['name'],$col['name']);
				$qw->add_col(isset($col['hname'])?$col['hname']:$col['name'],$edtr,$col['name']);
			}
		//delete button
		$del=new editor_button;
		$del->attributes['value']='-';
		$qw->add_col('-',$del,'-');
		$del->name='del';
		
		$qr->from->exprs[]=new sql_column($this->settings->ed_db,$this->settings->ed_table,NULL,'t');
		$qr->order->exprs=unserialize($this->settings->order);
		$qr->where->exprs=unserialize($this->settings->filters_where);
		$qr->lim_count=$this->settings->ed_count;
		$qr->lim_offset=$this->settings->ed_offset;
		
		$qcount=clone $qr;
		$qcount->what->exprs=Array(new sql_list('count',Array(new sql_immed(1)),'c'));
		unset($qcount->lim_offset);
		unset($qcount->lim_count);
		unset($qcount->order->exprs);
		unset($this->editors['ed_rowcount']->query);
		$this->editors['ed_rowcount']->query=$qcount;
		
		#$qw->editdb='dbfp';
		$qw->edittbl=$this->settings->ed_table;
		$qw->editdb=$this->settings->ed_db;
//		$this->editors['an']->edittbl=$this->settings->ed_table;
//		$this->editors['an']->editdb=$this->settings->ed_db;
		
		unset($qw->keycols);
		if(is_array($structure->keys))
			foreach($structure->keys as $key)
				if($key['key']=='PRIMARY')
				{
					$qw->keycols[]=$key['name'];
					$this->editors['an']->add_col($key['name'],new editor_text,$key['name']);
				}
		
	}
	
	function selclear()
	{
		global $sql;
		$q=new query_gen_ext('delete');
		$q->from->exprs[]=new sql_column(NULL,'barcodes_print',NULL,NULL);
		$query=$q->result();
		$sql->query($query);
		
	}
	
	function del_single($keys)
	{
		global $sql;
		$q=new query_gen_ext('delete');
		$q->from->exprs[]=new sql_column($this->settings->ed_db,$this->settings->ed_table,NULL,NULL);
		foreach($keys as $k => $v)
			$q->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,$k,NULL),new sql_immed($v,NULL)),NULL);
		$query=$q->result();
		$sql->query($query);
		//print 'alert(\''.js_escape($query).'\');';
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->settings_io->load($this);
		
		$this->editors['ed_order']->settings=&$this->settings;
		$this->editors['ed_order']->settings_io=&$this->settings_io;
		
		$this->editors['ed_filters']->settings=&$this->settings;
		$this->editors['ed_filters']->settings_io=&$this->settings_io;
		
		$this->editors['an']->settings=&$this->settings;
		$this->editors['an']->settings_io=&$this->settings_io;
		
		
		$this->setup($this->editors['qw']);
		$this->context[$this->long_name.'.ed_rowcount']['var']='@@ed_rowcount';
		$this->context[$this->long_name.'.ed_count']['var']='@@ed_count';
		$this->context[$this->long_name.'.ed_offset']['var']='@@ed_offset';
		$this->context[$this->long_name.'.ed_table']['var']='@@ed_table';
		$this->context[$this->long_name.'.ed_table']['rawquery']=
			($this->settings->ed_db=='')?'SHOW TABLES':'SHOW TABLES FROM `'.$this->settings->ed_db.'`';
		$this->context[$this->long_name.'.ed_db']['var']='@@ed_db';
		$this->context[$this->long_name.'.ed_db']['rawquery']='SHOW DATABASES';
		$this->context[$this->long_name]['retid']=$this->rdiv->id_gen();
		
		$this->context[$this->long_name]['ed_rowcount_id']=$this->editors['ed_rowcount']->main_id();
		$this->context[$this->long_name]['ed_filters_id']=$this->editors['ed_filters']->main_id();
		$this->context[$this->long_name]['ed_order_id']=$this->editors['ed_order']->main_id();
		$this->context[$this->long_name]['an_id']=$this->editors['an']->main_id();
		$this->context[$this->long_name]['link_save_xml_id']=$this->link_save_xml->id_gen();
		$this->context[$this->long_name]['link_save_csv_id']=$this->link_save_csv->id_gen();
		
		$this->context[$this->long_name]['ed_offset_id']=$this->editors['ed_offset']->main_id();
//		$this->context[$this->long_name.'.ed_filters']['retid']=$this->fldiv->id_gen();
		$this->args['@@ed_count']=$this->settings->ed_count;
		$this->args['@@ed_offset']=$this->settings->ed_offset;
		$this->args['@@ed_table']=$this->settings->ed_table;
		$this->link_save_xml->attributes['href']="/ext/table_xml_dump.php?table=".urlencode($this->settings->ed_table);
		$this->link_save_csv->attributes['href']="/ext/table_csv_dump.php?table=".urlencode($this->settings->ed_table);
		$this->args['@@ed_db']=$this->settings->ed_db;
		
		$this->ed_more->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"var cnt=\$i('".$this->editors['ed_count']->main_id()."');".
			"ofs.focus();".
			"var iofs=isNaN(ofs.value)?0:parseInt(ofs.value);".
			"var icnt=isNaN(cnt.value)?0:parseInt(cnt.value);".
			"ofs.value=iofs+icnt;".
//			"this.focus();".
			"";
		$this->ed_less->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"var cnt=\$i('".$this->editors['ed_count']->main_id()."');".
			"ofs.focus();".
			"var iofs=isNaN(ofs.value)?0:parseInt(ofs.value);".
			"var icnt=isNaN(cnt.value)?0:parseInt(cnt.value);".
			"ofs.value=(iofs>=icnt)?iofs-icnt:0;".
//			"this.focus();".
			"";
		$this->ed_zero->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"ofs.focus();".
			"ofs.value=0;".
			"this.focus();".
			"";
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			if(!isset($e->keys))$e->keys=&$this->keys;
			if(!isset($e->args))$e->args=&$this->args;
			$e->bootstrap();
		}
	}
	
	function handle_event($ev)
	{
		$changed=false;
		$reload_an=false;
		$this->settings_io->load($this);
		if($ev->rem_name=='qw.del')
		{
			//child node targeted event
			$this->del_single($ev->keys);
			$changed=true;
			$reload_an=true;
			//$reload_an=true;
		}
		if($ev->rem_name=='ed_table')
		{
			$ev->context[$ev->long_name]['rawquery']=
				($this->settings->ed_db=='')?'SHOW TABLES':'SHOW TABLES FROM `'.$this->settings->ed_db.'`';
			//child node targeted event
			if($this->settings->ed_table!=$_POST['val'])
			{
				$this->settings->ed_table=$_POST['val'];
				print "\$i('".js_escape($ev->context[$ev->parent_name]['link_save_xml_id'])."').setAttribute('href','".js_escape("/ext/table_xml_dump.php?table=".urlencode($this->settings->ed_table))."');";
				print "\$i('".js_escape($ev->context[$ev->parent_name]['link_save_csv_id'])."').setAttribute('href','".js_escape("/ext/table_csv_dump.php?table=".urlencode($this->settings->ed_table))."');";
				$changed=true;
				$reload_an=true;
			}
		}
		if($ev->rem_name=='ed_db')
		{
			//child node targeted event
			if($this->settings->ed_db!=$_POST['val'])
			{
				$this->settings->ed_db=$_POST['val'];
				$changed=true;
				$reload_an=true;
			}
		}
		if($ev->rem_name=='ed_count')
		{
			//child node targeted event
			$this->settings->ed_count=intval($_POST['val']);
			$changed=true;
		}
		if($ev->rem_name=='ed_offset')
		{
			//child node targeted event
			$this->settings->ed_offset=intval($_POST['val']);
			$changed=true;
		}
		if($ev->rem_name=='clear_btn')
		{
			//child node targeted event
			
			//$_SESSION['selonly']=0;
			$_SESSION['selonly']=0;
			print "\$i('".js_escape($ev->context[$ev->parent_name]['selonly_id'])."').checked=0;";
			$this->selclear();
			$changed=true;
		}
		$this->settings_io->store($this);
		//common part
		$customid=$ev->context[$ev->parent_name]['retid'];
		$anid=$ev->context[$ev->parent_name]['anid'];
		$ed_rowcount_id=$ev->context[$ev->parent_name]['ed_rowcount_id'];
		$oid=$ev->context[$ev->long_name]['oid'];
		$htmlid=$ev->context[$ev->long_name]['htmlid'];
		$rel=Array('an','ed_filters','ed_order','ed_rowcount');
		foreach($rel as $e)if($reload_an || ($e=='ed_rowcount'))
		{
			$ids[$e]=js_escape($ev->context[$ev->parent_name][$e."_id"]);
			$ee=$this->editors[$e];
			$ee->context=&$ev->context;
			$ee->keys=Array();
			$ee->oid=$oid;
			$ee->name=$ev->parent_name.'.'.$e;
			$ee->etype=$ev->parent_type.'.'.get_class($ee);
			$ee->settings=&$this->settings;
			$ee->settings_io=&$this->settings_io;
			unset($ee->com_parent);
		}
		
		$r=new query_result_viewer_codes;
		$r->context=&$ev->context;
		$r->keys=Array();
		$r->oid=$oid;
		$r->name=$ev->parent_name.'.qw';
		$r->etype=$ev->parent_type.'.query_result_viewer_codes';
		editor_generic::handle_event($ev);
		$this->settings_io->load($this);
		if($changed || $ev->changed)
		{
			$this->setup($r);
			$r->bootstrap();
			foreach($rel as $e)if($reload_an || ($e=='ed_rowcount'))
				$this->editors[$e]->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');";
			foreach($rel as $e)if($reload_an || ($e=='ed_rowcount'))
				print "\n\nvar ".$e."_r=\$i('".$ids[$e]."');\n\n";
			print "try{nya.innerHTML=";
			reload_object($r);
			foreach($rel as $e)if($reload_an || ($e=='ed_rowcount'))
			{
				print $e."_r.innerHTML=";
				reload_object($this->editors[$e],true);
			}
			print "nya.scrollTop=0;}catch(e){ /*window.location.reload(true);*/};";
		}
		//$this->clean_zeroes();
	}
	
}




class QRVA_settings_io
{
	function load($class,$force=false)
	{
		if($force)
		{
			if(!is_array($_SESSION['QRVA']))return 1;
			foreach($_SESSION['QRVA'] as $s => $v)
			{
				$class->settings->$s=$v;
			}
		}else{
			if(! isset($class->settings))return 1;//no settings
			if(! is_object($class->settings)) return 2; //settings structure is invalid
			foreach($class->settings as $s => $v)
				if(isset($_SESSION['QRVA'][$s]))$ns->$s=$_SESSION['QRVA'][$s];
			if(is_object($ns))foreach($ns as $k => $v)$class->settings->$k=$v;
		}
	}
	function store($class)
	{
		if(! isset($class->settings))return 1;//no settings
		if(! is_object($class->settings)) return 2; //settings structure is invalid
		unset($_SESSION['QRVA']);
		foreach($class->settings as $s => $v)
			$_SESSION['QRVA'][$s]=$v;
	}
}

$tests_m_array['util']['query_result_viewer_any_old']='query_result_viewer_any_old';

class editor_insert_ch extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->etype='editor_insert_ch';
		$this->row=new dom_tr;
		$this->tbl->append_child($this->row);
		
		$this->row_caps=new dom_tr;
		$this->tbl->append_child($this->row_caps);
		$this->cell_caps=new dom_td;
		$this->row_caps->append_child($this->cell_caps);
		$this->text_caps=new dom_statictext;
		$this->cell_caps->append_child($this->text_caps);
		
		$this->cells=Array();
		
		$this->keys=Array();
		$this->args=Array();
		
		$this->colcn=0;
		
	}
	
	function cleanup()
	{
		unset($this->row->nodes);
		unset($this->cells);
		unset($this->editors);
		unset($this->col_caps);
		unset($this->col_vars);
		$this->cells=Array();
	}
	
	function add_col($capt,$editor,$arg)
	{
		$this->col_caps[$this->colcn]=$capt;
		$this->col_vars[$this->colcn]=$arg;
		editor_generic::addeditor('ed'.$this->colcn,$editor);
		$this->cells[$this->colcn]=new dom_td;
		//inherit properties from template???
		$this->row->append_child($this->cells[$this->colcn]);
		$this->cells[$this->colcn]->append_child($editor);
		$this->colcn++;
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->args=unserialize($this->settings->insert_args);
		//$this->temp_storage->load($this);
		//if($this->editdb!='')$this->context[$this->long_name]['dbname']=$this->editdb;
		//$this->context[$this->long_name]['tblname']=$this->edittbl;
		$this->context[$this->long_name]['io_class']=get_class($this->settings_io);
		
		if(!isset($this->editors['submit']))
		{
			editor_generic::addeditor('submit',new editor_button);
			$this->editors['submit']->attributes['value']='+';
			$this->cells[$this->colcn]=new dom_td;
			$this->row->append_child($this->cells[$this->colcn]);
			$this->cells[$this->colcn]->append_child($this->editors['submit']);
		}
		
		$this->context[$this->long_name]['submit_id']=$this->editors['submit']->main_id();
		if(!$this->test_submit())$this->editors['submit']->attributes['disabled']='disabled';
		
		if(is_array($this->col_vars))
			foreach($this->col_vars as $n => $arg)
			{
				$this->context[$this->long_name.'.ed'.$n]['var']=$arg;
				$this->context[$this->long_name.'.ed'.$n]['colname']=$arg;
				
			}
		if(is_array($this->editors))
			foreach($this->editors as $k => $e)
			{
				$e->keys=&$this->keys;
				$e->args=&$this->args;
				$e->context=&$this->context;
				if(isset($e->validator_class))$this->context[$this->long_name.'.'.$k]['validator_class']=$e->validator_class;
				$e->bootstrap();
			}
		
	}
	
	
	function html_inner()
	{
			
		$this->tbl->html_head();
		$this->row_caps->html_head();
		$cnt=0;
		if(is_array($this->col_caps))
			foreach($this->col_caps as $e)
			{
				$this->text_caps->text=$e;
				$cnt++;
				$this->cell_caps->html();
				$this->cell_caps->id_alloc();
			}
		$this->row_caps->html_tail();
		
		/*temp_storage will set?????
		foreach($this->keycols as $kk)
			$this->keys[$kk]=$this->args[$kk];
		*/
		unset($first_editor);
		unset($dst_rows);
		/*for($k=0 ; $k < $this->colcn ; $k++)
		{
			$this->editors['ed'.$k]->onfocus_add='$i(\''.js_escape($this->row->id_gen()).'\').style.backgroundColor=\'#FFDDDD\';';
			$this->editors['ed'.$k]->onblur_add='$i(\''.js_escape($this->row->id_gen()).'\').style.backgroundColor=\'\';';
			$this->editors['ed'.$k]->bootstrap();
		}*/
		//$this->row->attributes["onclick"]='var a=$i(\''.js_escape($this->editors['ed0']->id_gen()).'\');a.focus();a.selectionStart=0;a.selectionEnd=a.value.length;'
		$this->row->html();
		$this->tbl->html_tail();
	}
	
	function test_submit()
	{
		global $sql;
		$qq=new query_gen_ext;
		if(!is_array($this->args))return false;
		
		foreach($this->args as $i => $a)
		$qq->where->exprs[]=new sql_expression('=',
			Array(
				new sql_column(NULL,$this->edittbl,$i,NULL),
				new sql_immed($a,NULL)
			),NULL
		);
		$qq->from->exprs[]=new sql_column($this->editdb,$this->edittbl,NULL,NULL);
		//$qq->what->exprs[]=new sql_list('COUNT',Array(new sql_column(NULL,$a,NULL,NULL)),NULL);
		$qq->what->exprs[]=new sql_list('COUNT',Array(new sql_immed('1')),NULL);
		$r=$sql->fetch1($sql->query($qq->result()));
		return ($r==0);
	}
	
	function try_insert()
	{
		global $sql;
		$ca=$sql->qa("SHOW COLUMNS FROM `".$sql->esc($this->edittbl)."`");
		if(!is_array($ca))return false;
		$qq=new query_gen_ext('INSERT IGNORE');
		$qq->into->exprs[]=new sql_column($this->editdb,$this->edittbl,NULL,NULL);
		foreach($ca as $row)
			if(isset($this->args[$row['Field']]) && $row['Key']=='PRI')
			{
				$qq->set->exprs[]=
					new sql_expression('=',
						Array(
						new sql_column(NULL,NULL,$row['Field']),
						new sql_immed($this->args[$row['Field']])
					),NULL
				);
			}
/*		foreach($this->args as $i => $v)
			$qq->set->exprs[]=
				new sql_expression('=',
					Array(
						new sql_column(NULL,NULL,$i),
						new sql_immed($v)
					),NULL);
*/
		$q=$qq->result();
		$res=$sql->query($q);
		$ar=$sql->ar();
//		print 'alert(\''.js_escape($q).'\');';
		return ($ar>0);
	}
	
	function handle_event($ev)
	{
		$changed=false;
		$this->long_name=$ev->parent_name;
		$this->context=&$ev->context;
		$sio=$ev->context[$ev->parent_name]['io_class'];
		$this->settings_io=new $sio;
		unset($this->settings);
		$this->settings_io->load($this,true);
		$this->editdb=$this->settings->ed_db;
		$this->edittbl=$this->settings->ed_table;
		$this->args=unserialize($this->settings->insert_args);
		
		
		if(preg_match('/ed.*/',$ev->rem_name))
		{
			$this->args[$this->context[$this->long_name.'.'.$ev->rem_name]['colname']]=$_POST['val'];
			//$changed=true;
			$this->settings->insert_args=serialize($this->args);
			$this->settings_io->store($this);
			if($this->test_submit())
			{
				//disable submit button
				print '$i(\''.js_escape($this->context[$this->long_name]['submit_id']).'\').disabled=false;';
			}else{
				//enable submit button
				print '$i(\''.js_escape($this->context[$this->long_name]['submit_id']).'\').disabled=true;';
			}
		}
		
		if($ev->rem_name=='submit')
		{
			if($this->try_insert())
			{
				$changed=true;
			}else{
				print 'alert(\'insert failed\')';
			}
		}
		
		if($changed)
		{
			$this->settings->insert_args=serialize($this->args);
			$this->settings_io->store($this);
			//common part
			$customid=$ev->context[$ev->parent_name]['retid'];
			$oid=$ev->context[$ev->long_name]['oid'];
			$htmlid=$ev->context[$ev->long_name]['htmlid'];
			print "window.location.reload(true);";
		}
		editor_generic::handle_event($ev);
	}
	

}




####################################################
class editor_filters_ch extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype='editor_filters_ch';
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->row=new dom_tr;
		$this->tbl->append_child($this->row);
		
		$this->row_caps=new dom_tr;
		$this->tbl->append_child($this->row_caps);
		$this->add_cap('Col');
		$this->add_cap('Oper');
		$this->add_cap('Val');
		$this->add_cap('-');
		
		$this->cells=Array();
		
		$this->keys=Array();
		$this->args=Array();
		
		$this->colcn=0;
		
		$ed=new editor_text_autosuggest_query;
		//$ed->list_class='etasl_fch_c';
		$this->add_col($ed,'col');
		$ed->name='col';
		
		$ed=new editor_text_autosuggest;
		$ed->list_class='etasl_fch_o';
		$ed->ed->css_style['width']='3em';
		$this->add_col($ed,'oper');
		$ed->name='oper';
		
		$ed=new editor_text;
		$this->add_col($ed,'val');
		$ed->name='val';
		
		$ed=new editor_button;
		$ed->attributes['value']='-';
		$this->add_col($ed,'del');
		$ed->name='del';
		
	}
	function add_cap($t)
	{
		$cell_caps=new dom_td;
		$this->row_caps->append_child($cell_caps);
		$text_caps=new dom_statictext;
		$cell_caps->append_child($text_caps);
		$text_caps->text=$t;
	}
	
	function add_col($editor,$arg)
	{
		editor_generic::addeditor($arg,$editor);
		$this->cells[$this->colcn]=new dom_td;
		//inherit properties from template???
		$this->row->append_child($this->cells[$this->colcn]);
		$this->cells[$this->colcn]->append_child($editor);
		$this->colcn++;
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if($this->settings->ed_db!='')$dbe='`'.sql::esc($this->settings->ed_db).'`.';
		$this->context[$this->long_name.'.col']['rawquery']=
			'show columns from '.$dbe.'`'.sql::esc($this->settings->ed_table).'`';
		
		$this->context[$this->long_name]['retid']=$this->id_gen();
		$this->context[$this->long_name]['io_class']=get_class($this->settings_io);
		
		if(is_array($this->editors))
			foreach($this->editors as $k => $e)
			{
				$this->context[$this->long_name.'.'.$k]['var']=$k;
				$e->keys=&$this->keys;
				$e->args=&$this->args;
				$e->context=&$this->context;
				if(isset($e->validator_class))$this->context[$this->long_name.'.'.$k]['validator_class']=$e->validator_class;
				$e->bootstrap();
			}
		
	}
	
	
	function html_inner()
	{
		$this->filters_where=unserialize($this->settings->filters_where);
		$this->tbl->html_head();
		$this->row_caps->html();
		$nn=0;
		if(is_array($this->filters_where))foreach($this->filters_where as $f)
		{
			$this->args['col']=$f->exprs[0]->col;
			$this->args['oper']=$f->operator;
			if($this->args['oper']=='like')$this->args['oper']='~=';
			$this->args['val']=$f->exprs[1]->val;
			$this->keys['n']=$nn;
			$nn++;
			foreach($this->editors as $e)$e->bootstrap();
			$this->row->html();
			$this->row->id_alloc();
		}
		$this->editors['col']->main->css_style['display']='none';
		$this->editors['oper']->main->css_style['display']='none';
		$this->editors['val']->main->css_style['display']='none';
		$this->editors['del']->attributes['value']='+';
		$this->keys['n']=$nn;
		$nn++;
		foreach($this->editors as $e)$e->bootstrap();
		$this->row->html();
		$this->row->id_alloc();
		$this->tbl->html_tail();
	}
	
	
	function handle_event($ev)
	{
		$changed=false;
		$reload_self=false;
		$this->long_name=$ev->parent_name;
		$this->context=&$ev->context;
		/*$this->editdb=$this->context[$this->long_name]['dbname'];
		$this->edittbl=$this->context[$this->long_name]['tblname'];
		$this->temp_storage->load($this);
		*/
		$sio=$ev->context[$ev->parent_name]['io_class'];
		$this->settings_io=new $sio;
		unset($this->settings);
		$this->settings_io->load($this,true);
		$this->filters_where=unserialize($this->settings->filters_where);
		
		
		$v=$_POST['val'];
		if($ev->rem_name=='col')
		{
			$this->filters_where[$ev->keys['n']]->exprs[0]->col=$v;
			$changed=true;
		}
		if($ev->rem_name=='oper')
		{
			$this->filters_where[$ev->keys['n']]->operator=($v=='~=')?'like':$v;
			$changed=true;
		}
		if($ev->rem_name=='val')
		{
			$this->filters_where[$ev->keys['n']]->exprs[1]->val=$v;
			$changed=true;
		}
		if($ev->rem_name=='del')
		{
			if(isset($this->filters_where[$ev->keys['n']]))
			{
				for($k=0;$k<count($this->filters_where);$k++)
					if($k!=$ev->keys['n'])$nfl[]=$this->filters_where[$k];
					$this->filters_where=$nfl;
			}else{
				$this->filters_where[$ev->keys['n']]=new sql_expression('',
					Array(new sql_column,new sql_immed),NULL);
			}
			$changed=true;
			$reload_self=true;
		}
		
		$this->settings->filters_where=serialize($this->filters_where);
		if($changed) $this->settings_io->store($this);
		if($reload_self)
		{
			
			$customid=$ev->context[$ev->parent_name]['retid'];
			$oid=$ev->context[$ev->parent_name]['oid'];
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			
			$class=get_class($this);
			$r=new $class;
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->custom_id=$customid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->settings=&$this->settings;
			$r->settings_io=&$this->settings_io;

			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
			//common part
		}
		editor_generic::handle_event($ev);
		if($changed)$ev->changed=true;
	}
	

}

class etasl_fch_c extends editor_text_autosuggest_list_example
{
	function __construct()
	{
		parent::__construct();
		$this->list_items=Array();
	}
}

class etasl_fch_o extends editor_text_autosuggest_list_example
{
	function __construct()
	{
		parent::__construct();
		$this->list_items=Array('<','<=','=','>=','>','<>','~=');
		$this->nofilter=1;
	}
}


class qrva_guess_struct
{
	function load($o)
	{
		global $sql,$ddc_tables;
		$db=$o->settings->ed_db;
		$tbl=$o->settings->ed_table;
		if(($db=='') && isset($ddc_tables[$tbl]))
			return $ddc_tables[$tbl];
		if($db!='')$xdb="`".$sql->esc($db)."`.";
		$res=$sql->query("SHOW FULL COLUMNS FROM ".$xdb."`".$sql->esc($tbl)."`");
		while($r=$sql->fetcha($res))
		{
			$ra->name=$tbl;
			$c['name']=$r['Field'];
			$c['sql_type']=$r['type'];
			$c['sql_null']=($r['null']=='YES');
			$c['sql_default']=$r['Default'];
			$c['sql_sequence']=($r['Extra']=='auto_increment')?1:0;
			$c['sql_comment']=$r['Comment'];
			
			$ra->cols[]=$c;
		}
		$res=$sql->query("SHOW INDEXES FROM ".$xdb."`".$sql->esc($tbl)."`");
		while($r=$sql->fetcha($res))
		{
			$c['key']=$r['Key_name'];
			$c['name']=$r['Column_name'];
			$c['sub']=$r['Sub_part'];
			
			$ra->keys[]=$c;
		}
		return $ra;
	}
}
####################################################
class editor_order_ch extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->row=new dom_tr;
		$this->tbl->append_child($this->row);
		
		$this->row_caps=new dom_tr;
		$this->tbl->append_child($this->row_caps);
		$this->add_cap('Col');
		$this->add_cap('rev');
		$this->add_cap('-');
		
		$this->cells=Array();
		
		$this->keys=Array();
		$this->args=Array();
		
		$this->colcn=0;
		
		$ed=new editor_text_autosuggest_query;
		//$ed->list_class='etasl_fch_c';
		$this->add_col($ed,'col');
		$ed->name='col';
		
		$ed=new editor_checkbox;
		$this->add_col($ed,'rev');
		
		$ed=new editor_button;
		$ed->attributes['value']='-';
		$this->add_col($ed,'del');
		$ed->name='del';
		
		
	}
	function add_cap($t)
	{
		$cell_caps=new dom_td;
		$this->row_caps->append_child($cell_caps);
		$text_caps=new dom_statictext;
		$cell_caps->append_child($text_caps);
		$text_caps->text=$t;
	}
	
	function add_col($editor,$arg)
	{
		editor_generic::addeditor($arg,$editor);
		$this->cells[$this->colcn]=new dom_td;
		//inherit properties from template???
		$this->row->append_child($this->cells[$this->colcn]);
		$this->cells[$this->colcn]->append_child($editor);
		$this->colcn++;
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//if(!isset($this->settings))$this->settings_io->load($this);
		//if($this->settings->ed_db!='')$this->context[$this->long_name]['dbname']=$this->settings->ed_db;
		//$this->context[$this->long_name]['tblname']=$this->edittbl;
		
		if($this->settings->ed_db!='')$dbe='`'.sql::esc($this->settings->ed_db).'`.';
		$this->context[$this->long_name.'.col']['rawquery']=
			'show columns from '.$dbe.'`'.sql::esc($this->settings->ed_table).'`';
		
		$this->context[$this->long_name]['retid']=$this->id_gen();
		$this->context[$this->long_name]['io_class']=get_class($this->settings_io);
		
		if(is_array($this->editors))
			foreach($this->editors as $k => $e)
			{
				$this->context[$this->long_name.'.'.$k]['var']=$k;
				$e->keys=&$this->keys;
				$e->args=&$this->args;
				$e->context=&$this->context;
				if(isset($e->validator_class))$this->context[$this->long_name.'.'.$k]['validator_class']=$e->validator_class;
				$e->bootstrap();
			}
		
	}
	
	
	function html_inner()
	{
			
		$order=unserialize($this->settings->order);
		$this->tbl->html_head();
		$this->row_caps->html();
		$nn=0;
		if(is_array($order))foreach($order as $f)
		{
			$this->args['col']=$f->col;
			$this->args['rev']=$f->invert;
			$this->keys['n']=$nn;
			$nn++;
			foreach($this->editors as $e)$e->bootstrap();
			$this->row->html();
			$this->row->id_alloc();
		}
		$this->editors['col']->main->css_style['display']='none';
		$this->editors['rev']->css_style['display']='none';
		$this->editors['del']->attributes['value']='+';
		$this->keys['n']=$nn;
		$nn++;
		foreach($this->editors as $e)$e->bootstrap();
		$this->row->html();
		$this->row->id_alloc();
		$this->tbl->html_tail();
	}
	
	
	function handle_event($ev)
	{
		$changed=false;
		$reload_self=false;
		$this->long_name=$ev->parent_name;
		$this->context=&$ev->context;
		/*$this->editdb=$this->context[$this->long_name]['dbname'];
		$this->edittbl=$this->context[$this->long_name]['tblname'];
		$this->temp_storage->load($this);
		*/
		$sio=$ev->context[$ev->parent_name]['io_class'];
		$this->settings_io=new $sio;
		unset($this->settings);
		$this->settings_io->load($this,true);
		$order=unserialize($this->settings->order);
		
		$v=$_POST['val'];
		if($ev->rem_name=='col')
		{
			$order[$ev->keys['n']]->col=$v;
			$changed=true;
		}
		if($ev->rem_name=='rev')
		{
			$order[$ev->keys['n']]->invert=$v;
			//print 'alert(\''.$v.'\');';
			$changed=true;
		}
		if($ev->rem_name=='del')
		{
			if(isset($order[$ev->keys['n']]))
			{
				for($k=0;$k<count($order);$k++)
					if($k!=$ev->keys['n'])$nfl[]=$order[$k];
					$order=$nfl;
			}else{
				$order[$ev->keys['n']]=new sql_column;
			}
			$changed=true;
			$reload_self=true;
		}
		
		$this->settings->order=serialize($order);
		if($changed) $this->settings_io->store($this);
		if($reload_self)
		{
			
			$customid=$ev->context[$ev->parent_name]['retid'];
			$oid=$ev->context[$ev->parent_name]['oid'];
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			
			$class=get_class($this);
			$r=new $class;
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->settings=&$this->settings;
			$r->settings_io=$this->settings_io;
			$r->custom_id=$customid;

			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
			//common part
		}
		editor_generic::handle_event($ev);
		if($changed)$ev->changed=true;
	}
	

}


class act_codes_cleanup extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('cl',new editor_button);
		editor_generic::addeditor('ls',new query_result_viewer_multiline);
		editor_generic::addeditor('lss',new query_result_viewer_multiline);
		$this->append_child($this->editors['cl']);
		$this->append_child($this->editors['ls']);
		$this->append_child($this->editors['lss']);
		$this->editors['cl']->attributes['value']="Clear";
		$this->editors['ls']->compiled="SELECT `id`,`name`,`code` FROM `barcodes_raw` as a WHERE (SELECT count( 0 ) FROM `barcodes_raw` as b WHERE b.`code`=a.`code` AND b.`name`!=a.`name`)>0 ORDER BY `code`,`name`";
		$this->editors['lss']->compiled="SELECT `id`,`name`,`code` FROM `barcodes_raw` as a WHERE (SELECT count( 0 ) FROM `barcodes_raw` as b WHERE b.`code`=a.`code` AND b.`name`=a.`name`)>1 ORDER BY `code`,`name`";
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['retid']=$this->id_gen();
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			if(!isset($e->keys))$e->keys=&$this->keys;
			if(!isset($e->args))$e->args=&$this->args;
			$e->bootstrap();
		}
		
	}
	
	function handle_event($ev)
	{
		global $sql;
		if($ev->rem_name=='cl')
		{
			$sql->query("update `barcodes_raw` as a SET a.name=(SELECT name FROM `barcodes_raw` as c WHERE b.`code`=a.`code` AND b.`name`!=a.`name` ORDER BY id DESC LIMIT 1) WHERE (SELECT count( 0 ) FROM `barcodes_raw` as b WHERE b.`code`=a.`code` AND b.`name`!=a.`name`)>0");
			$sql->query("delete a from `barcodes_raw` as a,(SELECT max(id) as mid,`code`, count( `code` ) as c FROM `barcodes_raw` GROUP BY `code` having c>1) as b where a.id=b.mid");
			$customid=$ev->context[$ev->parent_name]['retid'];
			$oid=$ev->context[$ev->parent_name]['oid'];
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			
			$class=get_class($this);
			$r=new $class;
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->custom_id=$customid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;

			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
		}
	}
}



$tests_m_array['util']['act_codes_cleanup']='act_codes_cleanup';



class codes_import extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		#editor_generic::addeditor('file_picker',new editor_text);
		
		$tbl=new dom_table;$this->append_child($tbl);
		$tr=new dom_tr;$td1=new dom_td; $td2=new dom_td;
		$tbl->append_child($tr);$tr->append_child($td1);$tr->append_child($td2);
		$td1->append_child(new dom_statictext('file'));$td2->append_child(new dom_statictext('task'));
		unset($tr->id);unset($td1->id);unset($td2->id);
		$tr=new dom_tr;$td1=new dom_td; $td2=new dom_td;
		$tbl->append_child($tr);$tr->append_child($td1);$tr->append_child($td2);
		unset($tr->id);unset($td1->id);unset($td2->id);
		
		editor_generic::addeditor('file_picker',new file_pick_or_upload);
		$td1->append_child($this->editors['file_picker']);
		editor_generic::addeditor('task',new editor_text);
		$td2->append_child($this->editors['task']);
		
		
		$this->file_contents=new codes_import_xdiv;
		editor_generic::addeditor('file_contents',$this->file_contents);
		$this->append_child($this->file_contents);
		$this->errmsg=new dom_statictext;
		$this->append_child($this->errmsg);
		editor_generic::addeditor('accept',new editor_button);
		$this->editors['accept']->attributes['value']=">>";
		$this->append_child($this->editors['accept']);
		editor_generic::addeditor('add',new editor_button);
		$this->editors['add']->attributes['value']="++";
		$this->append_child($this->editors['add']);
		editor_generic::addeditor('unmatch',new editor_button);
		$this->editors['unmatch']->attributes['value']="Unmatched";
		$this->append_child($this->editors['unmatch']);
		#DEBUG
		$this->dbg=new dom_div;
		$this->append_child($this->dbg);
		#/DEBUG
		
		
	}
	
	function bootstrap()
	{
		$this->args=Array();
		$this->keys=Array();
		$this->long_name=editor_generic::long_name();
		//$this->oid=-1;
		$this->context[$this->long_name]['file_contents_id']=$this->file_contents->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['dbg']=$this->dbg->id_gen();
		$this->context[$this->long_name.'.file_contents']['var']='file_picker';
		$this->context[$this->long_name.'.file_picker']['var']='file_picker';
		$this->context[$this->long_name.'.task']['var']='task';
		foreach($this->editors as $e)
		{
			$e->args=&$this->args;
			$e->keys=&$this->keys;
			$e->context=&$this->context;
			$e->oid=$this->oid;
			$e->bootstrap();
		}
	}
	
	
	function html_inner()
	{
		$this->args['file_picker']=$this->rootnode->setting_val($this->oid,$this->long_name.'!file','');
		$this->args['task']=$this->rootnode->setting_val($this->oid,$this->long_name.'!task','0');
		
		parent::html_inner();
	}
	
	function do_accept($f,$add=false)
	{
		global $sql;
		if(preg_match('/\\//',$f))$f=preg_replace('/^.*\\//','',$f);
		$f=$_SERVER['DOCUMENT_ROOT'].'uploads/'.$f;
		$fd=false;
		if(file_exists($f))
			$fd=fopen($f,'r');
		if($fd !== false)
		{
			$csv=new csv;
			while($str=fgets($fd))
			{
				$values=$csv->split(trim($str));
				$rs=$sql->fetch1($sql->query("SELECT id FROM barcodes_raw WHERE name = '".$sql->esc($values[0])."'"));
				if($rs=='')
				{
					$rs=$sql->fetch1($sql->query("SELECT id FROM barcodes_mapping WHERE name = '".$sql->esc($values[0])."'"));
				}
				if($rs>0)
				{
					if($add)
						$res=$sql->query("INSERT INTO barcodes_print(id,task,`count`,printed) VALUES(".$rs.",".intval($this->task).",".$values[1].",0) ON DUPLICATE KEY UPDATE task=".intval($this->task).", `count`=`count`+".$values[1]);
					else
						$res=$sql->query("INSERT INTO barcodes_print(id,task,`count`,printed) VALUES(".$rs.",".intval($this->task).",".$values[1].",0) ON DUPLICATE KEY UPDATE task=".intval($this->task).", `count`=".$values[1]);
				}
				if(!$res)print "/*".$sql->err().'*/';
			}
			fclose($fd);
		}else return false;
		return true;
	}
	
	function dump_unmatch($f,$fo)
	{
		global $sql;
		if(preg_match('/\\//',$f))$f=preg_replace('/^.*\\//','',$f);
		if(preg_match('/\\//',$fo))$fo=preg_replace('/^.*\\//','',$fo);
		$f=$_SERVER['DOCUMENT_ROOT'].'uploads/'.$f;
		$fo=$_SERVER['DOCUMENT_ROOT'].'uploads/'.$fo;
		$fdi=false;
		$fdo=false;
		if(file_exists($f))
			$fdi=fopen($f,'r');
		$fdo=fopen($fo,'w');
		$linecount=0;
		if($fdi !== false)
		{
			$csv=new csv;
			while($str=fgets($fdi))
			{
				$values=$csv->split(trim($str));
				$rs=$sql->fetch1($sql->query("SELECT id FROM barcodes_raw WHERE name = '".$sql->esc($values[0])."'"));
				if($rs=='')
				{
					$rs=$sql->fetch1($sql->query("SELECT id FROM barcodes_mapping WHERE name = '".$sql->esc($values[0])."'"));
				}
				if(! ($rs>0))
				{
					fwrite($fdo,$str);
					$linecount++;
				}
			}
			fclose($fdi);
			fclose($fdo);
		}else return 0;
		return $linecount;
	}
	
	
	function handle_event($ev)
	{
		global $sql;
		$reload_list=false;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$dbg=$ev->context[$ev->parent_name]['dbg'];
		#$customid=$ev->context[$ev->parent_name]['htmlid'];
		$setting_tool=new settings_tool;
		$file_val=$sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.'!file',$_SESSION['uid'],0)));
		$this->task=$sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.'!task',$_SESSION['uid'],0)));
		switch($ev->rem_name)
		{
		case 'task':
				$val=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!task',$_SESSION['uid'],0,intval($val)));
				break;
		case 'file_picker':
				$file_val=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!file',$_SESSION['uid'],0,$file_val));
				#print #'$i(\''.$dbg."').innerHTML='".js_escape(htmlspecialchars($setting_tool->set_query($oid,$ev->parent_name.'!file',$_SESSION['uid'],0,$file_val)))."';";
				$reload_list=true;
				break;
		case 'accept':
				$this->do_accept($file_val,false);
				$reload_list=true;
 				break;
		case 'add':
				$this->do_accept($file_val,true);
				$reload_list=true;
 				break;
		case 'unmatch':
				$c=$this->dump_unmatch($file_val,preg_replace('/\.([^.]+)$/','_u.$1',$file_val));
				print "alert('".js_escape($c)."');";
 				break;
		}
		if($reload_list)
		{
			
			$this->args['file_picker']=$file_val;
			$this->args['task']=$this->task;
			$customid=$ev->context[$ev->parent_name]['file_contents_id'];
			$oid=$ev->context[$ev->parent_name]['oid'];
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$r=new codes_import_xdiv;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".file_contents";
			$r->etype=$ev->parent_type.".codes_import_xdiv";

			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
			//common part
		}
		editor_generic::handle_event($ev);
	}
}

class codes_import_xdiv extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->xdiv=new dom_div;
		$this->append_child($this->xdiv);
		$tbl=new dom_table;
		$this->xdiv->append_child($tbl);
			$tr=new dom_tr;
			$tr->css_style['background']='gray';
			$tbl->append_child($tr);
				$td=new dom_td;
				$tr->append_child($td);
					$brdiv=new dom_div;
					$brdiv->css_style['width']='700px';
					$td->append_child($brdiv);
						editor_generic::addeditor('o_name',new editor_statictext);
						$brdiv->append_child($this->editors['o_name']);
				$td=new dom_td;
				$td->attributes['rowspan']=2;
				$this->count_td=$td;
				$tr->append_child($td);
					editor_generic::addeditor('count',new editor_statictext);
					$td->append_child($this->editors['count']);
			$tr=new dom_tr;
			$tbl->append_child($tr);
				$td=new dom_td;
				$tr->append_child($td);
					#editor_generic::addeditor('m_id',new editor_text);
					editor_generic::addeditor('m_id',new editor_search_pick);
					$this->editors['m_id']->io=new editor_search_pick_sqltest_io;
					$td->append_child($this->editors['m_id']);
					$this->editors['m_id']->css_style['display']='inline-block';
					editor_generic::addeditor('m_del',new editor_button);
					$this->editors['m_del']->attributes['value']='-';
					$this->editors['m_del']->css_style['display']='none';
					$td->append_child($this->editors['m_del']);
		
	}
	
	
	function bootstrap()
	{
		$this->keys=Array();
		$this->long_name=editor_generic::long_name();
		foreach($this->editors as $n => $e)
			$this->context[$this->long_name.".".$n]['var']=$n;
		foreach($this->editors as $e)
		{
			$e->args=&$this->args;
			$e->keys=&$this->keys;
			$e->context=&$this->context;
			$e->oid=$this->oid;
			$e->bootstrap();
		}
	}
	
	
	function file_contents_out($fd)
	{
		global $sql;
		$csv=new csv;
		while($str=fgets($fd))
		{
			$values=$csv->split(trim($str));
			$this->args['o_name']=$values[0];
			$this->args['count']=$values[1];
			$this->keys['name']=$values[0];
			foreach($this->editors as $e)
				$e->bootstrap();
			$rs=$sql->fetch1($sql->query("SELECT id FROM barcodes_raw WHERE name = '".$sql->esc($values[0])."'"));
			if($rs=='')
			{
				$rs=$sql->fetch1($sql->query("SELECT id FROM barcodes_mapping WHERE name = '".$sql->esc($values[0])."'"));
				if($rs=='')
				{
					$this->editors['m_del']->css_style['display']='none';
				}else{
					$this->editors['m_del']->css_style['display']='inline';
				}
			}else{
				$this->editors['m_del']->css_style['display']='none';
			}

			$oc=$sql->fetch1($sql->query("SELECT `count` FROM barcodes_print WHERE id = '".$sql->esc($rs)."' AND task=".intval($this->args['task'])));
			if($oc == $values[1])$this->count_td->css_style['background']='green';
			if($oc != $values[1])$this->count_td->css_style['background']='red';
			if($oc == 0)$this->count_td->css_style['background']='yellow';
			$this->args['m_id']=$rs;
			$this->xdiv->html();
			$this->xdiv->id_alloc();
		}
	}
	
	function html_inner()
	{
		
		$fd=false;
		$f=$this->args['file_picker'];
		if(preg_match('/\\//',$f))$f=preg_replace('/^.*\\//','',$f);
		$f=$_SERVER['DOCUMENT_ROOT'].'uploads/'.$f;
		if(file_exists($f))
			$fd=fopen($f,'r');
		if($fd !== false)
		{
			$this->file_contents_out($fd);
			fclose($fd);
		}
	}
	
	function add_map($name,$id)
	{
		global $sql;
		$q="INSERT INTO barcodes_mapping SET name='".$sql->esc($name)."',id='".$sql->esc($id)."' ON DUPLICATE KEY UPDATE id='".$sql->esc($id)."'";
		print "/*".$q."*/\n";
		$sql->query($q);
	}
	function del_map($name)
	{
		global $sql;
		$q="DELETE FROM barcodes_mapping WHERE name='".$sql->esc($name)."'";
		print "/*".$q."*/\n";
		$sql->query($q);
	}
	
	function handle_event($ev)
	{
		if($ev->rem_name=='m_id')
		{
			$this->add_map($ev->keys['name'],$_POST['val']);
		}
		if($ev->rem_name=='m_del')
		{
			$this->del_map($ev->keys['name']);
		}
		editor_generic::handle_event($ev);
	}
}

























$tests_m_array['util']['codes_import']='codes_import';


class editor_text_ean13 extends editor_text
{
	
	function bootstrap()
	{
		parent::bootstrap();
		$this->main->attributes['onfocus'].="editor_text_ean13_focus(\$i('".js_escape($this->id_gen())."'),this,'".js_escape($this->keys['-id'])."');";
		$this->main->attributes['onblur'].="editor_text_ean13_blur(\$i('".js_escape($this->id_gen())."'),this,'".js_escape($this->keys['-id'])."');";
	}
}







/*
Array(
 'name' => 'barcodes_raw',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 1,	'comment' =>NULL, 'hname'=>'Идентификатор'),
  Array('name' =>'name',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>NULL,	'sql_sequence' => 0,			'comment' =>NULL, 'hname'=>'Наименование'),
  Array('name' =>'code',	'sql_type' =>'varchar(13)', 'sql_null' =>1, 'sql_default' =>NULL,	'sql_sequence' => 0,			'comment' =>NULL, 'hname'=>'Код')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'name', 'name' =>'name', 'sub' => NULL),
  Array('key' =>'code', 'name' =>'code', 'sub' => NULL)
 )
);
*/

?>