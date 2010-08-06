<?php
$ddc_tables['samples_raw']=(object)
Array(
 'name' => 'samples_raw',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 1,	'sql_comment' =>NULL, 'hname'=>'Идентификатор'),
  Array('name' =>'manufacturer',	'sql_type' =>'varchar(5)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Предприятие'),
  Array('name' =>'code',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Обозначение'),
  Array('name' =>'name',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Наименование'),
  Array('name' =>'decoration',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Отделка'),
  Array('name' =>'ordered_by',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Заказчик'),
  Array('name' =>'stored',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Место хранения'),
  Array('name' =>'comment',	'sql_type' =>'text', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Примечания'),
  Array('name' =>'man_date',	'sql_type' =>'date', 'sql_null' =>0, 'sql_default' =>'2010-01-01',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Дата изготовления'),
  Array('name' =>'mtime',	'sql_type' =>'timestamp', 'sql_null' =>0, 'sql_default' =>NULL,	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Дата изменения')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'name', 'name' =>'name', 'sub' => NULL),
  Array('key' =>'code', 'name' =>'code', 'sub' => NULL)
 )
);

$ddc_tables['samples_attachments']=(object)
Array(
 'name' => 'samples_attachments',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 1,	'sql_comment' =>NULL, 'hname'=>'Идентификатор'),
  Array('name' =>'type',	'sql_type' =>'varchar(100)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Тип'),
  Array('name' =>'filename',	'sql_type' =>'varchar(255)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Имя файла')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'filename', 'name' =>'filename', 'sub' => NULL)
 )
);

if($_GET['init']=='init')
{
	ddc_gentable_o($ddc_tables['samples_raw'],$sql);
	ddc_gentable_o($ddc_tables['samples_attachments'],$sql);
};


class samples_db_list extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		
		
		
		
		editor_generic::addeditor('ed_filters',new sdb_filters);
		$this->sdiv->append_child($this->editors['ed_filters']);
		
		editor_generic::addeditor('ed_order',new sdb_order);
		$this->sdiv->append_child($this->editors['ed_order']);
		
		editor_generic::addeditor('ed_pager',new util_small_pager);
		$this->sdiv->append_child($this->editors['ed_pager']);
		
		editor_generic::addeditor('ed_new',new editor_button);
		$this->sdiv->append_child($this->editors['ed_new']);
		$this->editors['ed_new']->attributes['value']='Добавить';
		
		editor_generic::addeditor('ed_list',new sdb_QR);
		$this->append_child($this->editors['ed_list']);
		
		editor_generic::addeditor('ed_download',new sdb_DL);
		$this->append_child($this->editors['ed_download']);
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		foreach($this->editors as $e)
		{
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$this->context[$this->long_name.'.'.$i]['var']=$i;
		}
		foreach($this->editors as $e)
			$e->bootstrap();
	}
	
	function html_inner()
	{
		$this->args['ed_count']=$this->rootnode->setting_val($this->oid,$this->long_name.'._count',20);
		$this->args['ed_offset']=$this->rootnode->setting_val($this->oid,$this->long_name.'._offset',0);
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		global $sql;
		$ev->do_reload=false;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		switch($ev->rem_name)
		{
			case 'ed_pager.ed_offset':
				$sql->query($st->set_query($this->oid,$this->long_name.'._offset',$_SESSION['uid'],0,$_POST['val']));
				$ev->do_reload=true;
				break;
			case 'ed_pager.ed_count':
				$sql->query($st->set_query($this->oid,$this->long_name.'._count',$_SESSION['uid'],0,$_POST['val']));
				$ev->do_reload=true;
				break;
		};
		
		
		editor_generic::handle_event($ev);
		
		
		
	}
	
};
$tests_m_array[]='samples_db_list';

class samples_db_item extends dom_div
{
	function __construct()
	{
		global $sql,$ddc_tables;
		parent::__construct();
		$this->etype=get_class($this);
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		
		$this->editing=new dom_table;
		$this->append_child($this->editing);
		$this->viewonly=new dom_table;
		$this->append_child($this->viewonly);
		
		foreach($ddc_tables['samples_raw']->cols as $col)
		{
			$tr=new dom_tr;
			$this->editing->append_child($tr);
			
			$ntd=new dom_td;
			$tr->append_child($ntd);
			$ntext=new dom_statictext;
			$ntd->append_child($ntext);
			$ntext->text=isset($col['hname'])?$col['hname']:$col['name'];
			
			$ntd=new dom_td;
			$tr->append_child($ntd);
			$ed='editor_text';
			if(isset($col['editor']))$ed=$col['editor'];
			editor_generic::addeditor('e'.$col['name'],new $ed);
			$ntd->append_child($this->editors['e'.$col['name']]);
			
			$tr=new dom_tr;
			$this->viewonly->append_child($tr);
			
			$ntd=new dom_td;
			$tr->append_child($ntd);
			$ntext=new dom_statictext;
			$ntd->append_child($ntext);
			$ntext->text=isset($col['hname'])?$col['hname']:$col['name'];
			
			$ntd=new dom_td;
			$tr->append_child($ntd);
			$ed='editor_statictext';
			if(isset($col['viewer']))$ed=$col['viewer'];
			editor_generic::addeditor('v'.$col['name'],new $ed);
			$ntd->append_child($this->editors['v'.$col['name']]);
			
		}
		
		$this->attachments=new dom_table;
		$this->append_child($this->attachments);
		$this->atr=new dom_tr;
		$this->attachments->append_child($this->atr);
		$td=new dom_td;
		$this->atr->append_child($td);
		unset($td->id);
		editor_generic::addeditor('anum',new editor_statictext);
		$td->append_child($this->editors['anum']);
		
		$td=new dom_td;
		$this->atr->append_child($td);
		unset($td->id);
		editor_generic::addeditor('alink',new editor_href);
		$td->append_child($this->editors['alink']);
		$this->editors['alink']->href='/uploads/%s';
		editor_generic::addeditor('aname',new editor_statictext);
		$this->editors['href']->main->append_child($this->editors['aname']);
		
		$td=new dom_td;
		$this->atr->append_child($td);
		unset($td->id);
		editor_generic::addeditor('adel',new editor_button_image);
		$td->append_child($this->editors['adel']);
		
		editor_generic::addeditor('aadd',new editor_file_upload);
		$this->append_child($this->editors['aadd']);
		$this->editors['aadd']->type_hidden->attributes['value']='rawname';
		$this->editors['aadd']->normal_postback=1;
		
		
		
		
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		foreach($this->editors as $e)
		{
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$this->context[$this->long_name.'.'.$i]['var']=$i;
		}
		foreach($this->editors as $e)
			$e->bootstrap();
	}
	
	function html_inner()
	{
		global $sql,$ddc_tables;
		
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		global $sql;
		$ev->do_reload=false;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		switch($ev->rem_name)
		{
			case 'ed_pager.ed_offset':
				$sql->query($st->set_query($this->oid,$this->long_name.'._offset',$_SESSION['uid'],0,$_POST['val']));
				$ev->do_reload=true;
				break;
			case 'ed_pager.ed_count':
				$sql->query($st->set_query($this->oid,$this->long_name.'._count',$_SESSION['uid'],0,$_POST['val']));
				$ev->do_reload=true;
				break;
		};
		
		
		editor_generic::handle_event($ev);
		
		
		
	}
	

};
$tests_m_array[]='samples_db_item';

class samples_db_users extends dom_div
{
};
$tests_m_array[]='samples_db_users';





class sdb_filters extends dom_div
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

class sdb_order extends dom_div
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

class sdb_DL extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
	
	function bootstrap()
	{
	}
	
	function handle_event($ev)
	{
	}
}

class sdb_QR extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->tr=new dom_tr;
		$this->tbl->append_child($this->tr);
		$this->td=new dom_td;
		$this->tr->append_child($this->td);
		$this->td_text=new dom_statictext;
		$this->td->append_child($this->td_text);
		
		unset($this->table->id);
		unset($this->tr->id);
		unset($this->td->id);
		$this->td_b=new dom_td;
		$this->tr->append_child($this->td_b);
		unset($this->td_b->id);
		
		editor_generic::addeditor('del',new editor_button_image);
		$this->td_b->append_child($this->editors['del']);
		editor_generic::addeditor('edit',new editor_button_image);
		$this->td_b->append_child($this->editors['edit']);
		editor_generic::addeditor('clone',new editor_button_image);
		$this->td_b->append_child($this->editors['clone']);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		foreach($this->editors as $e)
		{
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$this->context[$this->long_name.'.'.$i]['var']=$i;
		}
		foreach($this->editors as $e)
			$e->bootstrap();
	}
	
	function html_inner()
	{
		global $sql,$ddc_tables;
		
		$this->tbl->html_head();
		
		$qg=new query_gen_ext('SELECT');
		$qg->from->exprs[]=new sql_column(NULL,'samples_raw',NULL,'s');
		$this->tr->html_head();
		foreach($ddc_tables['samples_raw']->cols as $col)
		{
			$qg->what->exprs[]=new sql_column(NULL,'s',$col['name']);
			$this->td_text->text=$col['name'];
			$this->td->attributes['title']=$col['name'];
			if(isset($col['hname']))
				$this->td_text->text=$col['hname'];
			$this->td->html();
		}
		$this->td->attributes['title']='Операции';
		$this->td_text->text='Операции';
		$this->td_b->attributes['title']='Операции';
		$this->td->html();
		$this->tr->html_tail();
		$qc=$qg->result();
		
		$res=$sql->query($qc);
		while($row=$sql->fetcha($res))
		{
			$this->tr->html_head();
			foreach($row as $rn=>$rv)
			{
				$this->td_text->text=$rv;
				$this->td->attributes['title']=$ddc_tables['samples_raw']->cols[$rn]['name'];
				$this->td->html();
			}
			$this->keys['id']=$row['id'];
			foreach($this->editors as $e)
				$e->bootstrap();
			$this->td_b->html();
			$this->tr->html_tail();
		}
		$this->tbl->html_tail();
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}






//###################################################################################################################
//###################################################################################################################
//###################################################################################################################
//###################################################################################################################
//###################################################################################################################
//###################################################################################################################


/*
class query_result_viewer_any extends dom_any
{
	function __construct()
	{
		parent::__construct;
		$this->etype=get_class($this);
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		
		
		
		
		editor_generic::addeditor('ed_filters',new sdb_filters);
		$this->sdiv->append_child($this->editors['ed_filters']);
		
		editor_generic::addeditor('ed_order',new sdb_order);
		$this->sdiv->append_child($this->editors['ed_order']);
		
		
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
			print "nya.scrollTop=0;}catch(e){ };";
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

$tests_m_array[]='query_result_viewer_any';

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
		
		unset($first_editor);
		unset($dst_rows);
a=$i(\''.js_escape($this->editors['ed0']->id_gen()).'\');a.focus();a.selectionStart=0;a.selectionEnd=a.value.length;'
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
		$q=$qq->result();
		$res=$sql->query($q);
		$ar=$sql->ar();
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
		
		
		if(preg_match('/ed.* /',$ev->rem_name))
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

*/




?>