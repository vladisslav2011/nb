<?php
$ddc_tables['samples_raw']=(object)
Array(
 'name' => 'samples_raw',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 1,	'sql_comment' =>NULL, 'hname'=>'Идентификатор', 'editor' =>'editor_statictext'),
  Array('name' =>'manufacturer',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Предприятие'),
  Array('name' =>'code',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Обозначение'),
  Array('name' =>'name',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Наименование'),
  Array('name' =>'decoration',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Отделка'),
  Array('name' =>'ordered_by',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Заказчик'),
  Array('name' =>'stored',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Место хранения'),
  Array('name' =>'comment',	'sql_type' =>'text', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Примечания'),
  Array('name' =>'man_date',	'sql_type' =>'date', 'sql_null' =>0, 'sql_default' =>'2010-01-01',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Дата изготовления'),
  Array('name' =>'mtime',	'sql_type' =>'timestamp', 'sql_null' =>0, 'sql_default' =>NULL,	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Дата изменения', 'editor' =>'editor_statictext')
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
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Объект'),
  Array('name' =>'aid',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>NULL,		'sql_sequence' => 1,	'sql_comment' =>NULL, 'hname'=>'Идентификатор'),
  Array('name' =>'type',	'sql_type' =>'varchar(100)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Тип'),
  Array('name' =>'description',	'sql_type' =>'varchar(255)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Описание'),
  Array('name' =>'filename',	'sql_type' =>'varchar(255)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Имя файла'),
  Array('name' =>'thumb',	'sql_type' =>'varchar(255)', 'sql_null' =>1, 'sql_default' =>'',	'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Файл предпросмотра')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'aid', 'sub' => NULL),
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
		
		if($_SESSION['interface']!='samples_view')
		{
			editor_generic::addeditor('ed_new',new editor_button);
			$this->sdiv->append_child($this->editors['ed_new']);
			$this->editors['ed_new']->attributes['value']='Добавить';
		}
		
		editor_generic::addeditor('ed_list',new sdb_QR);
		$this->append_child($this->editors['ed_list']);
		
		editor_generic::addeditor('ed_download',new sdb_DL);
		$this->append_child($this->editors['ed_download']);
		
		
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['ed_list_id']=$this->editors['ed_list']->id_gen();
		foreach($this->editors as $i => $e)
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
		$this->args['ed_filters']=unserialize($this->rootnode->setting_val($this->oid,$this->long_name.'._filters',0));
		$this->args['ed_order']=unserialize($this->rootnode->setting_val($this->oid,$this->long_name.'._order',0));
		$this->editors['ed_list']->table_name='samples_raw';
		parent::html_inner();
	}
	
	function cascade_delete($id)
	{
		global $sql;
		$doc_root=$_SERVER['DOCUMENT_ROOT'];
		if(preg_match('#.*[^/]$#',$doc_root))$doc_root.='/';
		$qg=new query_gen_ext("SELECT");
		$qg->from->exprs[]=new sql_column(NULL,'samples_attachments');
		$qg->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'id'),
			new sql_immed($id)
		));
		$qg->what->exprs[]=new sql_column(NULL,NULL,'aid');
		$qg->what->exprs[]=new sql_column(NULL,NULL,'filename');
		$res=$sql->query($qg->result());
		while($row=$sql->fetchn($res))
		{
			$full=$doc_root.'si/o/'.$id.'/'.$row[1];
			$thumb=$doc_root.'si/t/'.$id.'/'.$row[1];
			if(file_exists($full))unlink($full);
			if(file_exists($thumb))unlink($thumb);
		}
		$qg=new query_gen_ext("DELETE");
		$qg->from->exprs[]=new sql_column(NULL,'samples_attachments');
		$qg->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'id'),
			new sql_immed($id)
		));
		$sql->query($qg->result());
		
	}
	
	function handle_event($ev)
	{
		global $sql,$ddc_tables;
		$ev->do_reload=false;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		$filters=$sql->qv($st->single_query($this->oid,$this->long_name."._filters",$_SESSION['uid'],0));
		$ev->settings->filters=unserialize($filters[0]);
		$order=$sql->qv($st->single_query($this->oid,$this->long_name."._order",$_SESSION['uid'],0));
		$ev->settings->order=unserialize($order[0]);
		switch($ev->rem_name)
		{
			case 'ed_new':
				if($_SESSION['interface']=='samples_view')
				{
					print "alert('Редактирование отключено');window.location.reload(true);";
					exit;
				}
				if($sql->query("INSERT INTO `samples_raw` SET id=''")!==false)
				{
					$r=$sql->qv("SELECT LAST_INSERT_ID()");
					print "window.location.href='".js_escape('?p=samples_db_item&id='.urlencode($r[0]))."';";
					exit;
					$ev->do_reload=true;
				}else{
					print "alert('Не удалось добавить запись.');";
				};
				break;
			case 'ed_list.del':
				if($_SESSION['interface']=='samples_view')
				{
					print "alert('Редактирование отключено');window.location.reload(true);";
					exit;
				}
				$this->cascade_delete($ev->keys['id']);
				$qg=new query_gen_ext('DELETE');
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'id'),
					new sql_immed($ev->keys['id'])
					));
				$qg->from->exprs[]=new sql_column(NULL,'samples_raw');
				$sql->query($qg->result());
				$ev->do_reload=true;
				break;
			case 'ed_list.clone':
				if($_SESSION['interface']=='samples_view')
				{
					print "alert('Редактирование отключено');window.location.reload(true);";
					exit;
				}
				$qg=new query_gen_ext('INSERT SELECT');
				$qg->into->exprs[]=new sql_column(NULL,'samples_raw');
				$qg->from->exprs[]=new sql_column(NULL,'samples_raw');
				foreach($ddc_tables['samples_raw']->cols as $col)
				{
					if($col['name']!='id')
						$qg->what->exprs[]=new sql_column(NULL,NULL,$col['name'],$col['name']);
				}
				$qg->what->exprs[]=new sql_immed('','id');
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'id'),
					new sql_immed($ev->keys['id'])
					));
				
				if($sql->query($qg->result())!==false)
				{
					$r=$sql->qv("SELECT LAST_INSERT_ID()");
					print "window.location.href='".js_escape('?p=samples_db_item&id='.urlencode($r[0]))."';";
					exit;
					$ev->do_reload=true;
				}else{
					print "alert('Не удалось добавить запись.');";
				};
				break;
			case 'ed_list.edit':
				print "window.location.href='".js_escape('?p=samples_db_item&id='.urlencode($ev->keys['id']))."';";
				exit;
				break;
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
		if($ev->filters_changed)
			$sql->query($st->set_query($this->oid,$this->long_name."._filters",$_SESSION['uid'],0,serialize($ev->settings->filters)));
		if($ev->order_changed)
			$sql->query($st->set_query($this->oid,$this->long_name."._order",$_SESSION['uid'],0,serialize($ev->settings->order)));
		if($ev->changed)$ev->do_reload=true;
		if($ev->do_reload)
		{
			$offset_a=$sql->qv($st->single_query($this->oid,$this->long_name."._offset",$_SESSION['uid'],0));
			$count_a=$sql->qv($st->single_query($this->oid,$this->long_name."._count",$_SESSION['uid'],0));
			$this->args['ed_offset']=$offset_a[0];
			$this->args['ed_count']=$count_a[0];
			$this->args['ed_filters']=$ev->settings->filters;
			$this->args['ed_order']=$ev->settings->order;
			$r=new sdb_QR;
			$r->table_name='samples_raw';
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".ed_list";
			$r->etype=$ev->parent_type.".sdb_QR";

			print "(function(){var nya=\$i('".js_escape($ev->context[$this->long_name]['ed_list_id'])."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){/* window.location.reload(true);*/};})();";
		}
		
		
	}
	
};
$tests_m_array['samples_db']['samples_db_list']='samples_db_list';

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
		
		editor_generic::addeditor('attachments',new sdb_attachments);
		$this->append_child($this->editors['attachments']);
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['attachments_id']=$this->editors['attachments']->id_gen();
		if(!is_array($this->args))$this->args=Array();
		if(!is_array($this->keys))$this->keys=Array();
		foreach($this->editors as $i=>$e)
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
		if($_SESSION['interface']!='samples_view')
			$can_edit=true;
		else
			$can_edit=false;
		
		$qg=new query_gen_ext('SELECT');
		$qg->from->exprs[]=new sql_column(NULL,'samples_raw',NULL,'s');
		
		foreach($ddc_tables['samples_raw']->cols as $col)
		{
			$qg->what->exprs[]=new sql_column(NULL,'s',$col['name']);
		}
		
		$qg->where->exprs[]=new sql_expression('=',
			Array(
				new sql_column(NULL,'s','id'),
				new sql_immed($_GET['id'])
				));
		$qc=$qg->result();
		$res=$sql->query($qc);
		while($row=$sql->fetcha($res))
		{
			foreach($row as $ri => $rv)
			{
				$this->args[($can_edit?'e':'v').$ri]=$rv;
			}
		}
		$this->keys['id']=$_GET['id'];
		if($can_edit)
		{
			foreach($this->editors as $e)
				$e->bootstrap();
			$this->editing->html();
		}
		else
		{
			foreach($this->editors as $e)
				$e->bootstrap();
			$this->viewonly->html();
		}
		$this->editors['attachments']->html();
			
	}
	
	function gen_preview($name)
	{
		return $name;
	}
	
	function handle_event($ev)
	{
		global $sql,$ddc_tables;
		$ev->do_reload=false;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		if($_SESSION['interface']=='samples_view')
		{
			print "alert('Редактирование отключено');window.location.reload(true);";
			exit;
		}
		foreach($ddc_tables['samples_raw']->cols as $col)
			if($ev->rem_name=='e'.$col['name'])
			{
				$qg=new query_gen_ext('update');
				$qg->into->exprs[]=new sql_column(NULL,'samples_raw',NULL,'s');
				$qg->where->exprs[]=new sql_expression('=',
					Array(
						new sql_column(NULL,'s','id'),
						new sql_immed($ev->keys['id'])
						));
				$qg->set->exprs[]=new sql_expression('=',
					Array(
						new sql_column(NULL,'s',$col['name']),
						new sql_immed($_POST['val'])
						));
				$r=$sql->query($qg->result());
/*				if($r===false)
					print "alert('".js_escape($qg->result())."');";*/
			}

		$doc_root=$_SERVER['DOCUMENT_ROOT'];
		if(preg_match('#.*[^/]$#',$doc_root))$doc_root.='/';
		switch($ev->rem_name)
		{
			case 'attachments.aadd':
				$name=$_POST['val'];
				$odir = $doc_root.'si/o/'.$ev->keys['id'];
				if(!file_exists($odir))mkdir($odir,0777,true);
				$file_name=preg_replace('#.*/#','',$name);
				$new_name=$odir.'/'.$file_name;
				rename($name,$new_name);
				$pv_name=$this->gen_preview($new_name);
				$qg=new query_gen_ext("INSERT");
				$qg->into->exprs[]=new sql_column(NULL,'samples_attachments');
				$qg->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'aid'),
					new sql_immed('')
					));
				$qg->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'id'),
					new sql_immed($ev->keys['id'])
					));
				$qg->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'type'),
					new sql_immed('unknown')
					));
				$qg->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'description'),
					new sql_immed('')
					));
				$qg->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'filename'),
					new sql_immed($file_name)
					));
				$qg->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'thumb'),
					new sql_immed($pv_name)
					));
				$res=$sql->query($qg->result());
				if($res===NULL)
				{
					//handle error
				}
				$aid=$sql->qv("SELECT LAST_INSERT_ID()");
				if($aid===NULL)
				{
					//handle error
				}
				$aid=$aid[0];
				$ev->do_reload=true;
				$ev->activate_aid=$aid;
				
				
				
				break;
			case 'attachments.adel':
				
				$qg=new query_gen_ext("SELECT");
				$qg->from->exprs[]=new sql_column(NULL,'samples_attachments');
				
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'id'),
					new sql_immed($ev->keys['id'])
					));
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'aid'),
					new sql_immed($ev->keys['aid'])
					));
				$qg->what->exprs[]=new sql_column(NULL,NULL,'filename');
				$qg->what->exprs[]=new sql_column(NULL,NULL,'thumb');
				$r=$sql->qa($qg->result());
				if($r===false)
				{
					//handle error
				}
				$full=$doc_root.'si/o/'.$ev->keys['id'].'/'.$r[0]['filename'];
				$thumb=$doc_root.'si/t/'.$ev->keys['id'].'/'.$r[0]['filename'];
				if(file_exists($full))unlink($full);
				if(file_exists($thumb))unlink($thumb);
					
					
				$qg=new query_gen_ext("DELETE");
				$qg->from->exprs[]=new sql_column(NULL,'samples_attachments');
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'id'),
					new sql_immed($ev->keys['id'])
					));
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'aid'),
					new sql_immed($ev->keys['aid'])
					));
				$res=$sql->query($qg->result());
				$ev->do_reload=true;
				break;
			case 'attachments.adescr':
				$qg=new query_gen_ext("UPDATE");
				$qg->into->exprs[]=new sql_column(NULL,'samples_attachments');
				
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'id'),
					new sql_immed($ev->keys['id'])
					));
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'aid'),
					new sql_immed($ev->keys['aid'])
					));
				$qg->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'description'),
					new sql_immed($_POST['val'])
					));
//				print "alert('".js_escape($qg->result())."');";
				$sql->query($qg->result());
				
				break;
		};
		
		
		editor_generic::handle_event($ev);
		
		if($ev->do_reload)
		{
			$r=new sdb_attachments;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".attachments";
			$r->etype=$ev->parent_type.".sdb_attachments";

			print "var nya=\$i('".js_escape($ev->context[$this->long_name]['attachments_id'])."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){/* window.location.reload(true);*/};";
		}
		
		
	}
	

};
$tests_m_array['samples_db']['samples_db_item']='samples_db_item';

class samples_db_users extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		$this->table_name='*users';
		
		
		
		editor_generic::addeditor('ed_filters',new sdb_filters);
		$this->sdiv->append_child($this->editors['ed_filters']);
		
		editor_generic::addeditor('ed_order',new sdb_order);
		$this->sdiv->append_child($this->editors['ed_order']);
		
		editor_generic::addeditor('ed_pager',new util_small_pager);
		$this->sdiv->append_child($this->editors['ed_pager']);
		
		if($_SESSION['interface']!='samples_view')
		{
			editor_generic::addeditor('ed_new',new editor_button);
			$this->sdiv->append_child($this->editors['ed_new']);
			$this->editors['ed_new']->attributes['value']='Добавить';
		}
		
		editor_generic::addeditor('ed_list',new sdb_QR);
		$this->append_child($this->editors['ed_list']);
		
		editor_generic::addeditor('ed_download',new sdb_DL);
		$this->append_child($this->editors['ed_download']);
		
		
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['ed_list_id']=$this->editors['ed_list']->id_gen();
		foreach($this->editors as $i => $e)
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
		$this->args['ed_filters']=unserialize($this->rootnode->setting_val($this->oid,$this->long_name.'._filters',0));
		$this->args['ed_order']=unserialize($this->rootnode->setting_val($this->oid,$this->long_name.'._order',0));
		$this->editors['ed_list']->table_name=$this->table_name;
		parent::html_inner();
	}
	
	
	function handle_event($ev)
	{
		global $sql,$ddc_tables;
		$ev->do_reload=false;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		$filters=$sql->qv($st->single_query($this->oid,$this->long_name."._filters",$_SESSION['uid'],0));
		$ev->settings->filters=unserialize($filters[0]);
		$order=$sql->qv($st->single_query($this->oid,$this->long_name."._order",$_SESSION['uid'],0));
		$ev->settings->order=unserialize($order[0]);
		switch($ev->rem_name)
		{
			case 'ed_new':
				if($_SESSION['interface']=='samples_view')
				{
					print "alert('Редактирование отключено');window.location.reload(true);";
					exit;
				}
				if($sql->query("INSERT INTO `".$this->table_name."` SET uid=''")!==false)
				{
					$r=$sql->qv("SELECT LAST_INSERT_ID()");
					print "window.location.href='".js_escape('?p=samples_db_usersitem&uid='.urlencode($r[0]))."';";
					exit;
					$ev->do_reload=true;
				}else{
					print "alert('Не удалось добавить запись.');";
				};
				break;
			case 'ed_list.del':
				if($_SESSION['interface']=='samples_view')
				{
					print "alert('Редактирование отключено');window.location.reload(true);";
					exit;
				}
				$this->cascade_delete($ev->keys['uid']);
				$qg=new query_gen_ext('DELETE');
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'uid'),
					new sql_immed($ev->keys['uid'])
					));
				$qg->from->exprs[]=new sql_column(NULL,$this->table_name);
				$sql->query($qg->result());
				$ev->do_reload=true;
				break;
			case 'ed_list.clone':
				if($_SESSION['interface']=='samples_view')
				{
					print "alert('Редактирование отключено');window.location.reload(true);";
					exit;
				}
				$qg=new query_gen_ext('INSERT SELECT');
				$qg->into->exprs[]=new sql_column(NULL,$this->table_name);
				$qg->from->exprs[]=new sql_column(NULL,$this->table_name);
				foreach($ddc_tables[$this->table_name]->cols as $col)
				{
					if($col['name']!='uid')
						$qg->what->exprs[]=new sql_column(NULL,NULL,$col['name'],$col['name']);
				}
				$qg->what->exprs[]=new sql_immed('','uid');
				$qg->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'uid'),
					new sql_immed($ev->keys['uid'])
					));
				
				if($sql->query($qg->result())!==false)
				{
					$r=$sql->qv("SELECT LAST_INSERT_ID()");
					print "window.location.href='".js_escape('?p=samples_db_usersitem&uid='.urlencode($r[0]))."';";
					exit;
					$ev->do_reload=true;
				}else{
					print "alert('Не удалось добавить запись.');";
				};
				break;
			case 'ed_list.edit':
				print "window.location.href='".js_escape('?p=samples_db_usersitem&uid='.urlencode($ev->keys['uid']))."';";
				exit;
				break;
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
		if($ev->filters_changed)
			$sql->query($st->set_query($this->oid,$this->long_name."._filters",$_SESSION['uid'],0,serialize($ev->settings->filters)));
		if($ev->order_changed)
			$sql->query($st->set_query($this->oid,$this->long_name."._order",$_SESSION['uid'],0,serialize($ev->settings->order)));
		if($ev->changed)$ev->do_reload=true;
		if($ev->do_reload)
		{
			$offset_a=$sql->qv($st->single_query($this->oid,$this->long_name."._offset",$_SESSION['uid'],0));
			$count_a=$sql->qv($st->single_query($this->oid,$this->long_name."._count",$_SESSION['uid'],0));
			$this->args['ed_offset']=$offset_a[0];
			$this->args['ed_count']=$count_a[0];
			$this->args['ed_filters']=$ev->settings->filters;
			$this->args['ed_order']=$ev->settings->order;
			$r=new sdb_QR;
			$r->table_name='*users';
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".ed_list";
			$r->etype=$ev->parent_type.".sdb_QR";

			print "(function(){var nya=\$i('".js_escape($ev->context[$this->long_name]['ed_list_id'])."');";
			
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){/* window.location.reload(true);*/};})();";
		}
		
		
	}
	
};
$tests_m_array['samples_db']['samples_db_users']='samples_db_users';

class samples_db_usersitem extends dom_div
{
	function __construct()
	{
		global $sql,$ddc_tables;
		parent::__construct();
		$this->table_name='*users';
		$this->etype=get_class($this);
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		
		$this->editing=new dom_table;
		$this->append_child($this->editing);
		$this->viewonly=new dom_table;
		$this->append_child($this->viewonly);
		
		foreach($ddc_tables[$this->table_name]->cols as $col)
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
		
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		if(!is_array($this->args))$this->args=Array();
		if(!is_array($this->keys))$this->keys=Array();
		foreach($this->editors as $i=>$e)
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
		if($_SESSION['interface']!='samples_view')
			$can_edit=true;
		else
			$can_edit=false;
		
		$qg=new query_gen_ext('SELECT');
		$qg->from->exprs[]=new sql_column(NULL,$this->table_name,NULL,'s');
		
		foreach($ddc_tables[$this->table_name]->cols as $col)
		{
			$qg->what->exprs[]=new sql_column(NULL,'s',$col['name']);
		}
		
		$qg->where->exprs[]=new sql_expression('=',
			Array(
				new sql_column(NULL,'s','uid'),
				new sql_immed($_GET['uid'])
				));
		$qc=$qg->result();
		$res=$sql->query($qc);
		while($row=$sql->fetcha($res))
		{
			foreach($row as $ri => $rv)
			{
				$this->args[($can_edit?'e':'v').$ri]=$rv;
			}
		}
		$this->keys['uid']=$_GET['uid'];
		if($can_edit)
		{
			foreach($this->editors as $e)
				$e->bootstrap();
			$this->editing->html();
		}
		else
		{
			foreach($this->editors as $e)
				$e->bootstrap();
			$this->viewonly->html();
		}
			
	}
	
	function gen_preview($name)
	{
		return $name;
	}
	
	function handle_event($ev)
	{
		global $sql,$ddc_tables;
		$ev->do_reload=false;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		if($_SESSION['interface']=='samples_view')
		{
			print "alert('Редактирование отключено');window.location.reload(true);";
			exit;
		}
		foreach($ddc_tables[$this->table_name]->cols as $col)
			if($ev->rem_name=='e'.$col['name'])
			{
				$qg=new query_gen_ext('update');
				$qg->into->exprs[]=new sql_column(NULL,$this->table_name,NULL,'s');
				foreach($ddc_tables[$this->table_name]->keys as $key)
					if($key['key']=='PRIMARY')
						$qg->where->exprs[]=new sql_expression('=',
							Array(
								new sql_column(NULL,'s',$key['name']),
								new sql_immed($ev->keys[$key['name']])
								));
				$qg->set->exprs[]=new sql_expression('=',
					Array(
						new sql_column(NULL,'s',$col['name']),
						new sql_immed($_POST['val'])
						));
				$r=$sql->query($qg->result());
/*				if($r===false)
					print "alert('".js_escape($qg->result())."');";*/
			}

		$doc_root=$_SERVER['DOCUMENT_ROOT'];
		if(preg_match('#.*[^/]$#',$doc_root))$doc_root.='/';
		
		
		editor_generic::handle_event($ev);
		
		
		
	}
	

};
$tests_m_array['samples_db']['samples_db_usersitem']='samples_db_usersitem';



class sdb_attachments extends dom_div
{
	function __construct()
	{
		global $sql,$ddc_tables;
		parent::__construct();
		$this->etype=get_class($this);
		
		$this->attachments=new dom_table;
		$this->append_child($this->attachments);
		if($_SESSION['interface']!='samples_view')
		{
			$adescr_editor='editor_text';
			$adel_editor='editor_button_image';
		}else{
			$adescr_editor='editor_statictext';
			$adel_editor='editor_statictext';
		}
		
		
		
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
		$this->editors['alink']->href='%s';
		editor_generic::addeditor('aname',new editor_statictext);
		$this->editors['alink']->main->append_child($this->editors['aname']);
		
		$td=new dom_td;
		$this->atr->append_child($td);
		unset($td->id);
		editor_generic::addeditor('adescr',new $adescr_editor);
		$td->append_child($this->editors['adescr']);
		
		$td=new dom_td;
		$this->atr->append_child($td);
		unset($td->id);
		editor_generic::addeditor('adel',new $adel_editor);
		$td->append_child($this->editors['adel']);
		$this->editors['adel']->attributes['src']='/i/del.png';
		
		$this->ahtr=new dom_tr;
		$this->attachments->append_child($this->ahtr);
		
		$td=new dom_td;	$this->ahtr->append_child($td);unset($td->id);$td->append_child(new dom_statictext('№ п/п'));
		$td=new dom_td;	$this->ahtr->append_child($td);unset($td->id);$td->append_child(new dom_statictext('Вложение'));
		$td=new dom_td;	$this->ahtr->append_child($td);unset($td->id);$td->append_child(new dom_statictext('Описание'));
		$td=new dom_td;	$this->ahtr->append_child($td);unset($td->id);$td->append_child(new dom_statictext('Операции'));
		
		if($_SESSION['interface']!='samples_view')
		{
			editor_generic::addeditor('aadd',new editor_file_upload);
			$this->append_child($this->editors['aadd']);
			$this->editors['aadd']->type_hidden->attributes['value']='rawname';
			$this->editors['aadd']->normal_postback=1;
		}else{
			editor_generic::addeditor('aadd',new editor_statictext);
			$this->append_child($this->editors['aadd']);
		}
		
		
		$this->notr=new dom_tr;
		$this->attachments->append_child($this->notr);
		$td=new dom_td;	$this->notr->append_child($td);unset($td->id);$td->append_child(new dom_statictext('Нет вложений'));
		$td->attributes['colspan']='4';$td->css_style['text-align']='center';
		
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		if(!is_array($this->args))$this->args=Array();
		if(!is_array($this->keys))$this->keys=Array();
		foreach($this->editors as $i=>$e)
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
		if($_SESSION['interface']!='samples_view')
			$can_edit=true;
		else
			$can_edit=false;
		$qg=new query_gen_ext('SELECT');
		
		$qg->where->exprs[]=new sql_expression('=',
			Array(
				new sql_column(NULL,'s','id'),
				new sql_immed($_GET['id'])
				));
		$qg->from->exprs=Array(new sql_column(NULL,'samples_attachments',NULL,'s'));
		$qg->what->exprs=Array();
		foreach($ddc_tables['samples_attachments']->cols as $col)
		{
			$qg->what->exprs[]=new sql_column(NULL,'s',$col['name']);
		}
		
		$qc=$qg->result();
		
		$res=$sql->query($qc);
		$this->attachments->html_head();
		$this->ahtr->html();
		$no_allachments=true;
		$nn=1;
		while($row=$sql->fetcha($res))
		{
			$this->args['alink']='/si/o/'.$row['id'].'/'.$row['filename'];
			$this->args['anum']=$nn;
			$nn++;
			$this->keys['aid']=$row['aid'];
			$this->args['aname']=$row['filename'];
			$this->args['adescr']=$row['description'];
			$this->id_alloc();
			foreach($this->editors as $e)
				$e->bootstrap();
			$this->atr->html();
			$no_allachments=false;
			
		}
		if($no_allachments)
		{
			$this->notr->html();
		}
		$this->attachments->html_tail();
		$this->editors['aadd']->html();
		
		
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
	
}














class sdb_filters extends dom_div
{
	function __construct()
	{
		global $ddc_tables;
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->row=new dom_tr;
		$this->tbl->append_child($this->row);
		
		$this->row_caps=new dom_tr;
		$this->tbl->append_child($this->row_caps);
		$this->add_cap('Столбец');
		$this->add_cap('Операция');
		$this->add_cap('Значение');
		$this->add_cap('-');
		
		$this->cells=Array();
		
		$this->keys=Array();
		$this->args=Array();
		
		$this->colcn=0;
		
		$this->add_col(new editor_select,'col');
		$this->editors['col']->options['any']='Везде';
		foreach($ddc_tables['samples_raw']->cols as $c)
			$this->editors['col']->options[$c['name']]=(isset($c['hname'])?$c['hname']:$c['name']);
		
		$this->add_col(new editor_select,'oper');
		$this->editors['oper']->options=Array(
			'~=' => '~=',
			'=' => '=',
			'>' => '>',
			'<' => '<',
			'>=' => '>=',
			'<=' => '<=',
			'!=' => '!=',
			'!~=' => '!~='
			);
		
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
		$this->context[$this->long_name]['retid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		
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
		$this->filters_where=$this->args[$this->context[$this->long_name]['var']];
		$this->tbl->html_head();
		$this->row_caps->html();
		$nn=0;
		if(is_array($this->filters_where))foreach($this->filters_where as $f)
		{
			$this->args['col']=$f->col;
			$this->args['oper']=$f->operator;
			$this->args['val']=$f->val;
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
		$this->filters_where=$ev->settings->filters;
		
		
		$v=$_POST['val'];
		if($ev->rem_name=='col')
		{
			$this->filters_where[$ev->keys['n']]->col=$v;
			$changed=true;
		}
		if($ev->rem_name=='oper')
		{
			$this->filters_where[$ev->keys['n']]->operator=$v;
			$changed=true;
		}
		if($ev->rem_name=='val')
		{
			$this->filters_where[$ev->keys['n']]->val=$v;
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
				$n->col='any';
				$n->operator='~=';
				$n->val='';
				$this->filters_where[$ev->keys['n']]=$n;
			}
			$changed=true;
			$reload_self=true;
		}
		
		$ev->settings->filters=$this->filters_where;
		if($changed) $ev->filters_changed=true;
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
			$r->args[$r->context[$ev->parent_name]['var']]=&$ev->settings->filters;

			$r->bootstrap();
			print "(function(){var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};})();";
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
		global $ddc_tables;
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->row=new dom_tr;
		$this->tbl->append_child($this->row);
		
		$this->row_caps=new dom_tr;
		$this->tbl->append_child($this->row_caps);
		$this->add_cap('Столбец');
		$this->add_cap('В обратном порядке');
		$this->add_cap('-');
		
		$this->cells=Array();
		
		$this->keys=Array();
		$this->args=Array();
		
		$this->colcn=0;
		
		$this->add_col(new editor_select,'col');
		foreach($ddc_tables['samples_raw']->cols as $c)
			$this->editors['col']->options[$c['name']]=(isset($c['hname'])?$c['hname']:$c['name']);
		
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
			
		$order=$this->args[$this->context[$this->long_name]['var']];
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
		$order=$ev->settings->order;
		
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
				$order[$ev->keys['n']]->col='';
				$order[$ev->keys['n']]->invert=0;
				
			}
			$changed=true;
			$reload_self=true;
		}
		
		$ev->settings->order=$order;
		if($changed) $ev->order_changed=true;
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
			$r->custom_id=$customid;
			$r->args[$r->context[$ev->parent_name]['var']]=&$order;

			print "(function(){var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};})();";
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
		
		if($_SESSION['interface']!='samples_view')
		{
			editor_generic::addeditor('del',new editor_button_image);
			$this->td_b->append_child($this->editors['del']);
			$this->editors['del']->attributes['title']='Удалить';
			$this->editors['del']->attributes['src']='/i/del.png';
		}
		editor_generic::addeditor('edit',new editor_button_image);
		$this->td_b->append_child($this->editors['edit']);
		$this->editors['edit']->attributes['title']='Редактировать/просмотреть';
		$this->editors['edit']->attributes['src']='/i/edit.png';
		if($_SESSION['interface']!='samples_view')
		{
			editor_generic::addeditor('clone',new editor_button_image);
			$this->td_b->append_child($this->editors['clone']);
			$this->editors['clone']->attributes['title']='Копировать';
			$this->editors['clone']->attributes['src']='/i/copy.png';
		}
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		foreach($this->editors as $i => $e)
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
	
	function map_op($op)
	{
		switch($op)
		{
			case "~=":
				return 'LIKE';
			case "!~=":
				return 'NOT LIKE';
			default:
				return $op;
		}
	}
	
	function transform_val($op,$val)
	{
		switch($op)
		{
			case "~=":
			case "!~=":
				return preg_replace('/ +/','%'," ".$val." ");
			default:
				return $val;
		}
	}
	
	function html_inner()
	{
		global $sql,$ddc_tables;
		
		$this->tbl->html_head();
		
		$qg=new query_gen_ext('SELECT');
		$qg->from->exprs[]=new sql_column(NULL,$this->table_name,NULL,'s');
		$this->tr->html_head();
		foreach($ddc_tables[$this->table_name]->cols as $col)
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
		
		if(is_array($this->args['ed_filters']))
			foreach($this->args['ed_filters'] as $e)
				if($e->col=='any')
				{
					$op=new sql_expression('OR');
					foreach($ddc_tables[$this->table_name]->cols as $col)
					{
						$op->exprs[]=new sql_expression($this->map_op($e->operator),Array(
							new sql_column(NULL,NULL,$col['name']),
							new sql_immed($this->transform_val($e->operator,$e->val))
							));
					}
					$qg->where->exprs[]=$op;
				}else{
					$qg->where->exprs[]=new sql_expression($this->map_op($e->operator),Array(
						new sql_column(NULL,NULL,$e->col),
						new sql_immed($this->transform_val($e->operator,$e->val))
						));
				};
		
		if(is_array($this->args['ed_order']))
			foreach($this->args['ed_order'] as $e)
			{
				$col=($e->col=='')?$ddc_tables[$this->table_name]->cols[0]['name']:$e->col;
				$m=new sql_column(NULL,NULL,$col);
				$m->invert=$e->invert;
				$qg->order->exprs[]=$m;
					
			}
		$pk_cols=Array();
		foreach($ddc_tables[$this->table_name]->keys as $k)
			if($k['key']=='PRIMARY')$pk_cols[]=$k['name'];
		$qg->lim_count=$this->args['ed_count'];
		$qg->lim_offset=$this->args['ed_offset'];
		$qc=$qg->result();
		
		$res=$sql->query($qc);
		while($row=$sql->fetcha($res))
		{
			$this->tr->html_head();
			foreach($row as $rn=>$rv)
			{
				$this->td_text->text=$rv;
				$this->td->attributes['title']=$ddc_tables[$this->table_name]->cols[$rn]['name'];
				$this->td->html();
			}
			foreach($pk_cols as $k)
				$this->keys[$k]=$row[$k];
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

class editor_txtasg_QRVA extends editor_txtasg
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
	
	function fetch_list($ev,$part=NULL)
	{
		global $sql,$ddc_tables;
		$r= Array(
/*			Array(
				'val'=>$ev->long_name,
				'title'=>'long_name'//works
				),
			Array(
				'val'=>$ev->rem_name,
				'title'=>'rem_name'//works
				),
			Array(
				'val'=>$ev->parent_name,
				'title'=>'parent_name'//works
				),
			Array(
				'val'=>"ed_db=".$ev->ed_db,
				'title'=>'ed_db'//works
				),
			Array(
				'val'=>"ed_table=".$ev->ed_table,
				'title'=>'ed_table'//works
				),
			Array(
				'val'=>"ed_column=".$ev->ed_column,
				'title'=>'ed_column'//works
				),
			Array(
				'val'=>"db=".$ev->settings->db,
				'title'=>'db'//works
				),
			Array(
				'val'=>'line2',
				'hint'=>'hint for line 2' //TODO: implement hint
				),
			Array(
				'val'=>'line3',//TODO: implement dynamic hint request
				'qh'=>1
				)*/
			);
		if($ev->ed_db===1)
		{
			$r[]=Array('val'=>'','title'=>'current');
			$res=$sql->query("SHOW DATABASES".(($part !== NULL)?" LIKE '%".$sql->escl($part)."%'":""));
			while($row=$sql->fetchn($res))
			{
				$r[]=Array('val'=>$row[0],'title'=>$row[0]);
			};
			$sql->free($res);
		};
		if($ev->ed_table===1)
		{
			$res=$sql->query("SHOW TABLES".(($ev->settings->db !='')?" FROM `".$sql->esc($ev->settings->db)."`":"").(($part !== NULL)?" LIKE '%".$sql->escl($part)."%'":""));
			while($row=$sql->fetchn($res))
			{
				$r[]=Array('val'=>$row[0],'title'=>$row[0]);
			};
			$sql->free($res);
		};
		if($ev->ed_column===1)
		{
			if(($ev->settings->db =='')&& isset($ddc_tables[$ev->settings->table]))
			{
				foreach($ddc_tables[$ev->settings->table]->cols as $col)
					$r[]=Array('val'=>$col['name'],'title'=>$col['hname']);
			}else{
				$fr=(($ev->settings->db !='')?"`".$sql->esc($ev->settings->db)."`.":"")."`".$sql->esc($ev->settings->table)."`";
				$res=$sql->query("SHOW COLUMNS FROM ".$fr.(($part !== NULL)?" LIKE '%".$sql->escl($part)."%'":""));
				while($row=$sql->fetcha($res))
				{
					$r[]=Array('val'=>$row['Field'],'title'=>$row['Field']);
				};
				$sql->free($res);
			}
		};
		return $r;
	}
	
}


$tests_m_array['util']['query_result_viewer_any']='query_result_viewer_any';
class query_result_viewer_any extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->sdiv=new dom_div;
		$this->append_child($this->sdiv);
		
		
		$autotbl=new container_autotable;
		$this->sdiv->append_child($autotbl);
		editor_generic::addeditor('ed_db',new editor_txtasg_QRVA);
		$autotbl->append_child($this->editors['ed_db']);
		#$this->sdiv->append_child(new dom_any_noterm('br'));
		editor_generic::addeditor('ed_table',new editor_txtasg_QRVA);
		$autotbl->append_child($this->editors['ed_table']);
		
		$this->link_save_xml=new dom_any('a');
		$autotbl->append_child($this->link_save_xml);
		$txt=new dom_statictext('xml');
		$this->link_save_xml->append_child($txt);
		
		$this->link_save_csv=new dom_any('a');
		$autotbl->append_child($this->link_save_csv);
		$txt=new dom_statictext('csv');
		$this->link_save_csv->append_child($txt);
		
		
		editor_generic::addeditor('ed_filters',new QRVA_filters);
		$this->sdiv->append_child($this->editors['ed_filters']);
		
		editor_generic::addeditor('ed_order',new QRVA_order);
		$this->sdiv->append_child($this->editors['ed_order']);
		
		editor_generic::addeditor('ed_pager',new util_small_pager);
		$this->sdiv->append_child($this->editors['ed_pager']);
		
		editor_generic::addeditor('ed_list',new QRVA_QR);
		$this->append_child($this->editors['ed_list']);
		
		editor_generic::addeditor('ed_insert',new QRVA_insert);
		$this->append_child($this->editors['ed_insert']);

		editor_generic::addeditor('ed_download',new sdb_DL);
		$this->append_child($this->editors['ed_download']);
		
		
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['ed_list_id']=$this->editors['ed_list']->id_gen();
		$this->context[$this->long_name]['ed_insert_id']=$this->editors['ed_insert']->id_gen();
		$this->context[$this->long_name]['link_save_xml_id']=$this->link_save_xml->id_gen();
		$this->context[$this->long_name]['link_save_csv_id']=$this->link_save_csv->id_gen();
		
		$this->args['ed_table']=$this->rootnode->setting_val($this->oid,$this->long_name.'._table','');
		$this->args['ed_db']=$this->rootnode->setting_val($this->oid,$this->long_name.'._db','');
		
		$this->link_save_xml->attributes['href']="/ext/table_xml_dump.php?table=".urlencode($this->args['ed_table']);
		$this->link_save_csv->attributes['href']="/ext/table_csv_dump.php?table=".urlencode($this->args['ed_table']);
		foreach($this->editors as $i => $e)
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
		$this->args['ed_filters']=unserialize($this->rootnode->setting_val($this->oid,$this->long_name.'._filters',0));
		$this->args['ed_order']=unserialize($this->rootnode->setting_val($this->oid,$this->long_name.'._order',0));
		$this->args['ed_insert']=unserialize($this->rootnode->setting_val($this->oid,$this->long_name.'._insert',0));
		$this->editors['ed_list']->table_name='samples_raw';
		parent::html_inner();
	}
	
	
	function u_sq($ev,$v,$s)
	{
		global $sql,$ddc_tables;
		$st=new settings_tool;
		$rv=$sql->qv($st->single_query($this->oid,$this->long_name.".".$s,$_SESSION['uid'],0));
		$ev->settings->$v=unserialize($rv[0]);
	}
	
	function i_sq($ev,$v,$s)
	{
		global $sql,$ddc_tables;
		$st=new settings_tool;
		$rv=$sql->qv($st->single_query($this->oid,$this->long_name.".".$s,$_SESSION['uid'],0));
		$ev->settings->$v=$rv[0];
	}
	
	function fetch_struct($ev)
	{
		global $sql,$ddc_tables;
		unset($this->t_cols);
		unset($this->t_keys);
		if(($ev->settings->db=='')&&(isset($ddc_tables[$ev->settings->table])))
		{
			foreach($ddc_tables[$ev->settings->table]->cols as $col)
			{
				$this->t_cols[$col['name']]=$col;
				if(!isset($col['hname']))$this->t_cols[$col['name']]['hname']=$col['name'];
				if(!isset($col['editor']))$this->t_cols[$col['name']]['editor']='editor_text';
			}
			foreach($ddc_tables[$ev->settings->table]->keys as $key)
			{
				if($key['key']=='PRIMARY')$this->t_keys[$key['name']]=true;
			}
		}else{
			unset($db);
			if($ev->setting->db != '')$db="`".$sql->esc($ev->settings->db)."`.";
			$res=$sql->query("SHOW COLUMNS FROM ".$db."`".$sql->esc($ev->settings->table)."`");
			while($row=$sql->fetcha($res))
			{
				$this->t_cols[$row['Field']]=Array(
					'name' =>$row['Field'],
					'hname' =>$row['Field'],
					'sql_type' =>$row['Type'],
					'sql_default' =>$row['Default'],
					'editor' =>'editor_text',
					'viewer' =>'editor_statictext'
					);
				if($row['Key']=='PRI')
					$this->t_keys[$row['Field']]=true;
			}
		}
	}
	
	function handle_event($ev)
	{
		global $sql,$ddc_tables;
		$ev->do_reload=false;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		$this->u_sq($ev,'filters','_filters');
		$this->u_sq($ev,'order','_order');
		$this->u_sq($ev,'insert','_insert');
		$this->i_sq($ev,'offset','_offset');
		$this->i_sq($ev,'count','_count');
		$this->i_sq($ev,'table','_table');
		$this->i_sq($ev,'db','_db');
		if(intval($ev->settings->count)<=0)$ev->settings->count=20;
		if(intval($ev->settings->offset)<=0)$ev->settings->offset=0;
		if($ev->settings->db=='')unset($ev->settings->db);
/*		$filters=$sql->qv($st->single_query($this->oid,$this->long_name."._filters",$_SESSION['uid'],0));
		$ev->settings->filters=unserialize($filters[0]);
		$order=$sql->qv($st->single_query($this->oid,$this->long_name."._order",$_SESSION['uid'],0));
		$ev->settings->order=unserialize($order[0]);*/
		$this->fetch_struct($ev);
		switch($ev->rem_name)
		{
			case 'ed_insert.insert':
				
				$qg=new query_gen_ext("INSERT");
				$qg->into->exprs[]=new sql_column($ev->settings->db,$ev->settings->table);
				foreach($this->t_keys as $kn =>$kv)
					$qg->set->exprs[]=new sql_expression('=',Array(
						new sql_column(NULL,NULL,$kn),
						new sql_immed($ev->settings->insert['+'.$kn])
						));
				if($sql->query($qg->result())!==false)
				{
					$ev->do_reload=true;
				}else{
					print "alert('Не удалось добавить запись.');";
				};
				break;
			case 'ed_list.del':
//				$this->cascade_delete($ev->keys['id']);
				$qg=new query_gen_ext('DELETE');
				foreach($this->t_keys as $kn =>$kv)
					$qg->where->exprs[]=new sql_expression('=',Array(
						new sql_column(NULL,NULL,$kn),
						new sql_immed($ev->keys['-'.$kn])
						));
				$qg->from->exprs[]=new sql_column($ev->settings->db,$ev->settings->table);
				$sql->query($qg->result());
				$ev->do_reload=true;
				break;
			case 'ed_list.clone':
				$qg=new query_gen_ext('INSERT SELECT');
				$qg->into->exprs[]=new sql_column($ev->settings->db,$ev->settings->table);
				$qg->from->exprs[]=new sql_column($ev->settings->db,$ev->settings->table);
				foreach($this->t_cols as $col)
				{
					if($this->t_keys[$col['name']]!=true)
						$qg->what->exprs[]=new sql_column(NULL,NULL,$col['name'],$col['name']);
					else{
						$qg->what->exprs[]=new sql_immed($ev->settings->insert['+'.$col['name']],$col['name']);
						$qg->where->exprs[]=new sql_expression('=',Array(
							new sql_column(NULL,NULL,$col['name']),
							new sql_immed($ev->keys['-'.$col['name']])
							));
					}
				}
				if($sql->query($qg->result())!==false)
				{
					$ev->do_reload=true;
				}else{
					print "alert('Не удалось добавить запись.');";
				};
				break;
			case 'ed_list.edit':
				print "alert('Not supported yet');";
				return;
				break;
			case 'ed_pager.ed_offset':
				$d=intval($_POST['val']);
				$sql->query($st->set_query($this->oid,$this->long_name.'._offset',$_SESSION['uid'],0,$d));
				$ev->settings->offset=$d;
				$ev->do_reload=true;
				break;
			case 'ed_pager.ed_count':
				$d=intval($_POST['val']);
				$sql->query($st->set_query($this->oid,$this->long_name.'._count',$_SESSION['uid'],0,$d));
				$ev->settings->count=$d;
				$ev->do_reload=true;
				break;
			case 'ed_table':
				$sql->query($st->set_query($this->oid,$this->long_name.'._table',$_SESSION['uid'],0,$_POST['val']));
				$ev->settings->table=$_POST['val'];
				$ev->do_reload=true;
				$ev->reload_insert=true;
			case 'ed_table.fo':
				$ev->ed_table=1;
				break;
			case 'ed_db':
				$sql->query($st->set_query($this->oid,$this->long_name.'._db',$_SESSION['uid'],0,$_POST['val']));
				$ev->settings->db=$_POST['val'];
				$ev->do_reload=true;
				$ev->reload_insert=true;
			case 'ed_db.fo':
				$ev->ed_db=1;
				break;
		};
		
			foreach($this->t_cols as $col)
			{
				if($ev->rem_name==='ed_list.-'.$col['name'])
				{
					$qg=new query_gen_ext('UPDATE');
					foreach($this->t_keys as $key => $kv)
						$qg->where->exprs[]=new sql_expression('=',Array(
							new sql_column(NULL,NULL,$key),
							new sql_immed($ev->keys['-'.$key])
							));
					$qg->into->exprs=Array(new sql_column($ev->settings->db,$ev->settings->table));
					$qg->set->exprs=Array(new sql_expression('=',Array(
						new sql_column(NULL,NULL,$col['name']),
						new sql_immed($_POST['val'])
						))
						);
					$res=$sql->query($qg->result());
				}
			}
		
		
		editor_generic::handle_event($ev);
		if($ev->filters_changed)
			$sql->query($st->set_query($this->oid,$this->long_name."._filters",$_SESSION['uid'],0,serialize($ev->settings->filters)));
		if($ev->order_changed)
			$sql->query($st->set_query($this->oid,$this->long_name."._order",$_SESSION['uid'],0,serialize($ev->settings->order)));
		if($ev->reload_insert)
		{
			$sql->query($st->set_query($this->oid,$this->long_name."._insert",$_SESSION['uid'],0,serialize(Array())));
			$ev->settings->insert=Array();
		}
		if($ev->insert_changed)
		{
			$sql->query($st->set_query($this->oid,$this->long_name."._insert",$_SESSION['uid'],0,serialize($ev->settings->insert)));
		}
		if($ev->changed)$ev->do_reload=true;
		if($ev->do_reload)
		{
			$this->args['ed_offset']=$ev->settings->offset;
			$this->args['ed_count']=$ev->settings->count;
			$this->args['ed_filters']=$ev->settings->filters;
			$this->args['ed_order']=$ev->settings->order;
			$this->args['ed_table']=$ev->settings->table;
			$this->args['ed_db']=$ev->settings->db;
			$this->args['ed_insert']=$ev->settings->insert;
			$r=new QRVA_QR;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=&$this->args;
			$r->name=$ev->parent_name.".ed_list";
			$r->etype=$ev->parent_type.".QRVA_QR";

			print "(function(){var nya=\$i('".js_escape($ev->context[$this->long_name]['ed_list_id'])."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "\$i('".js_escape($ev->context[$this->long_name]['link_save_xml_id'])."').setAttribute('href','".
				js_escape("/ext/table_xml_dump.php?table=".urlencode($this->args['ed_table']))."');";
			print "\$i('".js_escape($ev->context[$this->long_name]['link_save_csv_id'])."').setAttribute('href','".
				js_escape("/ext/table_csv_dump.php?table=".urlencode($this->args['ed_table']))."');";
			print "}catch(e){/* window.location.reload(true);*/};})();";
			if($ev->reload_insert)
			{
				$r=new QRVA_insert;
				
				$r->context=&$ev->context;
				$r->keys=&$ev->keys;
				$r->oid=$oid;
				$r->args=&$this->args;
				$r->name=$ev->parent_name.".ed_insert";
				$r->etype=$ev->parent_type.".QRVA_insert";

				print "(function(){var nya=\$i('".js_escape($ev->context[$this->long_name]['ed_insert_id'])."');";
				print "try{nya.innerHTML=";
				reload_object($r,true);
				print "}catch(e){/* window.location.reload(true);*/};})();";
			}
		}
		
		
	}
	
};

class QRVA_QR extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->tr=new dom_tr;
		$this->tbl->append_child($this->tr);
		$this->tdh=new dom_td;
		$this->tr->append_child($this->tdh);
		$this->td_text=new dom_statictext;
		$this->tdh->append_child($this->td_text);
		unset($this->tdh->id);
		
		$this->td=new dom_td;
		$this->tr->append_child($this->td);
		
		unset($this->table->id);
		unset($this->tr->id);
		unset($this->td->id);
		$this->td_b=new dom_td;
		$this->tr->append_child($this->td_b);
		unset($this->td_b->id);
		
		editor_generic::addeditor('del',new editor_button_image);
		$this->td_b->append_child($this->editors['del']);
		$this->editors['del']->attributes['title']='Удалить';
		$this->editors['del']->attributes['src']='/i/del.png';
		$this->ceditors[]=$this->editors['del'];
		
		editor_generic::addeditor('edit',new editor_button_image);
		$this->td_b->append_child($this->editors['edit']);
		$this->editors['edit']->attributes['title']='Редактировать/просмотреть';
		$this->editors['edit']->attributes['src']='/i/edit.png';
		$this->ceditors[]=$this->editors['edit'];
		
		editor_generic::addeditor('clone',new editor_button_image);
		$this->td_b->append_child($this->editors['clone']);
		$this->editors['clone']->attributes['title']='Копировать';
		$this->editors['clone']->attributes['src']='/i/copy.png';
		$this->ceditors[]=$this->editors['clone'];
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->keys))$this->keys=Array();
		if(!is_array($this->args))$this->args=Array();
		foreach($this->editors as $i => $e)
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
	
	function map_op($op)
	{
		switch($op)
		{
			case "~=":
				return 'LIKE';
			case "!~=":
				return 'NOT LIKE';
			default:
				return $op;
		}
	}
	
	function transform_val($op,$val)
	{
		switch($op)
		{
			case "~=":
			case "!~=":
				return preg_replace('/ +/','%'," ".str_replace('_','\\_',str_replace('%','\\%',$val))." ");
			default:
				return $val;
		}
	}
	
	function fetch_col_info()
	{
		global $sql,$ddc_tables;
		if($this->args['ed_db']=='')
		{
			if(isset($ddc_tables[$this->args['ed_table']]))
			{
				foreach($ddc_tables[$this->args['ed_table']]->cols as $col)
				{
					$this->t_cols[$col['name']]=$col;
					if(!isset($this->t_cols[$col['name']]['hname']))$this->t_cols[$col['name']]['hname']=$col['name'];
					if(!isset($this->t_cols[$col['name']]['editor']))$this->t_cols[$col['name']]['editor']='editor_text';
				}
				foreach($ddc_tables[$this->args['ed_table']]->keys as $key)
				if($key['key']=='PRIMARY')
				{
					$this->t_keys[$key['name']]=true;
					$this->t_cols[$key['name']]['editor']='editor_statictext';
				}
				
				return;
			}
			$f="`".$sql->esc($this->args['ed_table'])."`";
		}else{
			$f="`".$sql->esc($this->args['ed_db'])."`.`".$sql->esc($this->args['ed_table'])."`";
		}
		$res=$sql->query("SHOW FULL COLUMNS FROM ".$f);
		while($row=$sql->fetcha($res))
		{
			$this->t_cols[$row['Field']]['name']=$row['Field'];
			$this->t_cols[$row['Field']]['hname']=$row['Field'];
			$this->t_cols[$row['Field']]['sql_type']=$row['Type'];
			if($row['Key']=='PRI')$this->t_keys[$row['Field']]=true;
			$this->t_cols[$row['Field']]['editor']=(($row['Key']=='PRI')?'editor_statictext':'editor_text');
		}
	}
	
	function html_inner()
	{
		global $sql,$ddc_tables;
		$this->fetch_col_info();
		if(!is_array($this->t_cols))return;
		foreach($this->t_cols as $col)
		{
			$ed=$col['editor'];
			editor_generic::addeditor('-'.$col['name'],new $ed ($col));
			$this->td->append_child($this->editors['-'.$col['name']]);
			$this->editors['-'.$col['name']]->oid=$this->oid;
			$this->editors['-'.$col['name']]->keys=&$this->keys;
			$this->editors['-'.$col['name']]->args=&$this->args;
			$this->editors['-'.$col['name']]->context=&$this->context;
			$this->context[$this->long_name.'.-'.$col['name']]['var']='-'.$col['name'];
			
		}
		
		$this->tbl->html_head();
		
		unset($db);
		if($this->args['ed_db']!='')$db=$this->args['ed_db'];
		$qg=new query_gen_ext('SELECT');
		$qg->from->exprs[]=new sql_column($db,$this->args['ed_table'],NULL,'s');
		$this->tr->html_head();
		foreach($this->t_cols as $col)
		{
			$qg->what->exprs[]=new sql_column(NULL,'s',$col['name']);
			$this->td_text->text=$col['name'];
			$this->tdh->attributes['title']=$col['hname'];
			$this->tdh->html();
		}
		$this->tdh->attributes['title']='Операции';
		$this->td_text->text='Операции';
		$this->td_b->attributes['title']='Операции';
		$this->tdh->html();
		$this->tr->html_tail();
		
		if(is_array($this->args['ed_filters']))
			foreach($this->args['ed_filters'] as $e)
				if($e->col=='any')
				{
					$op=new sql_expression('OR');
					foreach($this->t_cols as $col)
					{
						$op->exprs[]=new sql_expression($this->map_op($e->operator),Array(
							new sql_column(NULL,'s',$col['name']),
							new sql_immed($this->transform_val($e->operator,$e->val))
							));
					}
					$qg->where->exprs[]=$op;
				}else{
					$qg->where->exprs[]=new sql_expression($this->map_op($e->operator),Array(
						new sql_column(NULL,'s',$e->col),
						new sql_immed($this->transform_val($e->operator,$e->val))
						));
				};
		
		if(is_array($this->args['ed_order']))
			foreach($this->args['ed_order'] as $e)
			{
				$col=($e->col=='')?$this->t_cols[0]['name']:$e->col;
				$m=new sql_column(NULL,'s',$col);
				$m->invert=$e->invert;
				$qg->order->exprs[]=$m;
					
			}
		$qg->lim_count=$this->args['ed_count'];
		$qg->lim_offset=$this->args['ed_offset'];
		$qc=$qg->result();
		if($this->args['ed_table']!='')
		{
			$res=$sql->query($qc);
			while($row=$sql->fetcha($res))
			{
				$this->tr->html_head();
				foreach($this->t_keys as $k =>$kv)
					$this->keys['-'.$k]=$row[$k];
				foreach($row as $rn=>$rv)
				{
					$this->args['-'.$rn]=$rv;
					$this->editors['-'.$rn]->bootstrap();
					$this->td->attributes['title']=$this->t_cols[$rn]['hname'];
					$this->td->html_head();
					$this->editors['-'.$rn]->html();
					$this->td->html_tail();
					$this->td->id_alloc();
					
				}
				foreach($this->ceditors as $e)
					$e->bootstrap();
				$this->td_b->html();
				$this->tr->html_tail();
			}
		}
		$this->tbl->html_tail();
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}
class QRVA_order extends dom_div
{
	function __construct()
	{
		global $ddc_tables;
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->row=new dom_tr;
		$this->tbl->append_child($this->row);
		
		$this->row_caps=new dom_tr;
		$this->tbl->append_child($this->row_caps);
		$this->add_cap('Столбец');
		$this->add_cap('В обратном порядке');
		$this->add_cap('-');
		
		$this->cells=Array();
		
		$this->keys=Array();
		$this->args=Array();
		
		$this->colcn=0;
		
		$this->add_col(new editor_txtasg_QRVA,'col');
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
			
		$order=$this->args[$this->context[$this->long_name]['var']];
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
		$order=$ev->settings->order;
		
		$v=$_POST['val'];
		if($ev->rem_name=='col.fo')
		{
			$ev->ed_column=1;
		}
		if($ev->rem_name=='col')
		{
			$ev->ed_column=1;
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
				$order[$ev->keys['n']]->col='';
				$order[$ev->keys['n']]->invert=0;
				
			}
			$changed=true;
			$reload_self=true;
		}
		
		$ev->settings->order=$order;
		if($changed) $ev->order_changed=true;
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
			$r->custom_id=$customid;
			$r->args[$r->context[$ev->parent_name]['var']]=&$order;

			print "(function(){var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};})();";
			//common part
		}
		editor_generic::handle_event($ev);
		if($changed)$ev->changed=true;
	}
	
}

class QRVA_filters extends dom_div
{
	function __construct()
	{
		global $ddc_tables;
		parent::__construct();
		$this->etype=get_class($this);
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->row=new dom_tr;
		$this->tbl->append_child($this->row);
		
		$this->row_caps=new dom_tr;
		$this->tbl->append_child($this->row_caps);
		$this->add_cap('Столбец');
		$this->add_cap('Операция');
		$this->add_cap('Значение');
		$this->add_cap('-');
		
		$this->cells=Array();
		
		$this->keys=Array();
		$this->args=Array();
		
		$this->colcn=0;
		
		$this->add_col(new editor_txtasg_QRVA,'col');
		$this->editors['col']->options['any']='Везде';
		
		$this->add_col(new editor_select,'oper');
		$this->editors['oper']->options=Array(
			'~=' => '~=',
			'=' => '=',
			'>' => '>',
			'<' => '<',
			'>=' => '>=',
			'<=' => '<=',
			'!=' => '!=',
			'!~=' => '!~='
			);
		
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
		$this->context[$this->long_name]['retid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		
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
		$this->filters_where=$this->args[$this->context[$this->long_name]['var']];
		$this->tbl->html_head();
		$this->row_caps->html();
		$nn=0;
		if(is_array($this->filters_where))foreach($this->filters_where as $f)
		{
			$this->args['col']=$f->col;
			$this->args['oper']=$f->operator;
			$this->args['val']=$f->val;
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
		$this->filters_where=$ev->settings->filters;
		
		
		$v=$_POST['val'];
		if($ev->rem_name=='col.fo')
		{
			$ev->ed_column=1;
		}
		if($ev->rem_name=='col')
		{
			$ev->ed_column=1;
			$this->filters_where[$ev->keys['n']]->col=$v;
			$changed=true;
		}
		if($ev->rem_name=='oper')
		{
			$this->filters_where[$ev->keys['n']]->operator=$v;
			$changed=true;
		}
		if($ev->rem_name=='val')
		{
			$this->filters_where[$ev->keys['n']]->val=$v;
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
				$n->col='any';
				$n->operator='~=';
				$n->val='';
				$this->filters_where[$ev->keys['n']]=$n;
			}
			$changed=true;
			$reload_self=true;
		}
		
		$ev->settings->filters=$this->filters_where;
		if($changed) $ev->filters_changed=true;
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
			$r->args[$r->context[$ev->parent_name]['var']]=&$ev->settings->filters;

			$r->bootstrap();
			print "(function(){var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};})();";
			//common part
		}
		editor_generic::handle_event($ev);
		if($changed)$ev->changed=true;
	}
}

class QRVA_insert extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->etype=get_class($this);
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
	
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->temp_storage->load($this);
		//if($this->editdb!='')$this->context[$this->long_name]['dbname']=$this->editdb;
		//$this->context[$this->long_name]['tblname']=$this->edittbl;
		
		if(!is_array($this->keys))$this->keys=Array();
		if(!is_array($this->args))$this->args=Array();
		if(is_array($this->editors))
			foreach($this->editors as $k => $e)
			{
				$e->oid=$this->oid;
				$e->keys=&$this->keys;
				$e->args=&$this->args;
				$e->context=&$this->context;
				if(isset($e->validator_class))$this->context[$this->long_name.'.'.$k]['validator_class']=$e->validator_class;
			}
		if(is_array($this->editors))
			foreach($this->editors as $k => $e)
				$e->bootstrap();
		
	}
	
	function fetch_col_info()
	{
		global $sql,$ddc_tables;
		if($this->args['ed_db']=='')
		{
			if(isset($ddc_tables[$this->args['ed_table']]))
			{
				foreach($ddc_tables[$this->args['ed_table']]->cols as $col)
				{
					$this->t_cols[$col['name']]=$col;
					if(!isset($this->t_cols[$col['name']]['hname']))$this->t_cols[$col['name']]['hname']=$col['name'];
					if(!isset($this->t_cols[$col['name']]['editor']))$this->t_cols[$col['name']]['editor']='editor_text';
				}
				foreach($ddc_tables[$this->args['ed_table']]->keys as $key)
				if($key['key']=='PRIMARY')
				{
					$this->t_keys[$key['name']]=true;
				}
				
				return;
			}
			$f="`".$sql->esc($this->args['ed_table'])."`";
		}else{
			$f="`".$sql->esc($this->args['ed_db'])."`.`".$sql->esc($this->args['ed_table'])."`";
		}
		$res=$sql->query("SHOW FULL COLUMNS FROM ".$f);
		while($row=$sql->fetcha($res))
		{
			$this->t_cols[$row['Field']]['name']=$row['Field'];
			$this->t_cols[$row['Field']]['hname']=$row['Field'];
			$this->t_cols[$row['Field']]['sql_type']=$row['Type'];
			if($row['Key']=='PRI')$this->t_keys[$row['Field']]=true;
			$this->t_cols[$row['Field']]['editor']='editor_text';
		}
	}
	
	function html_inner()
	{
			
		$this->fetch_col_info();
		if(is_array($this->t_keys))
			foreach($this->t_keys as $kn => $kv)
			{
				$col=$this->t_cols[$kn];
				$ed=$col['editor'];
				editor_generic::addeditor('+'.$col['name'],new $ed ($col));
				$td=new dom_td;
				$this->row->append_child($td);
				$td->append_child($this->editors['+'.$col['name']]);
				$this->editors['+'.$col['name']]->oid=$this->oid;
				$this->editors['+'.$col['name']]->keys=&$this->keys;
				$this->editors['+'.$col['name']]->args=&$this->args;
				$this->editors['+'.$col['name']]->context=&$this->context;
				$this->context[$this->long_name.'.+'.$col['name']]['var']='+'.$col['name'];
				$this->args['+'.$col['name']]=$this->args[$this->context[$this->long_name]['var']]['+'.$col['name']];
				
			}
				editor_generic::addeditor('insert',new editor_button);
				$td=new dom_td;
				$this->row->append_child($td);
				$td->append_child($this->editors['insert']);
				$this->editors['insert']->oid=$this->oid;
				$this->editors['insert']->keys=&$this->keys;
				$this->editors['insert']->args=&$this->args;
				$this->editors['insert']->context=&$this->context;
				$this->context[$this->long_name.'.insert']['var']='insert';
			
		
		$this->tbl->html_head();
		$this->row_caps->html_head();
		$cnt=0;
		if(is_array($this->t_keys))
			foreach($this->t_keys as $kn => $kv)
			{
				$this->text_caps->text=$this->t_cols[$kn]['hname'];
				$this->cell_caps->attributes['title']=$this->t_cols[$kn]['hname'];
				$cnt++;
				$this->cell_caps->html();
				$this->cell_caps->id_alloc();
			}
		$this->row_caps->html_tail();
		
		
		if(is_array($this->editors))
			foreach($this->editors as $k => $e)
				$e->bootstrap();
		$this->row->html();
		$this->tbl->html_tail();
	}
	
	
	function handle_event($ev)
	{
		
		if((!preg_match('/\./',$ev->rem_name))&&($ev->rem_name!='insert'))
		{
			$ev->settings->insert[$ev->rem_name]=$_POST['val'];
			$ev->insert_changed=true;
		}
		editor_generic::handle_event($ev);
	}
	

}

class editor_password_md5 extends editor_text
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
	}
}



?>