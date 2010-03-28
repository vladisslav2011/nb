<?php

set_include_path($_SERVER['DOCUMENT_ROOT']);
//$_SESSION['uid']='1000';
//if(preg_match('/^127\.0\..*/',$_SERVER['REMOTE_ADDR']))$_SESSION['uid']='0';

//phpinfo();
//$_SESSION['sql_design']=false;
//if($_SESSION['uid']==0)$_SESSION['sql_design']=true;
//require_once('lib/auth.php');
require_once('lib/ddc_meta.php');
require_once('lib/commctrls.php');


class workers_container extends dom_void
{
	//$copy_buffer
	function __construct()
	{
		parent::__construct();
		$this->etype='workers_container';
		$this->workers['sql_null']=new editor_sql_null;
		$this->workers['sql_immed']=new editor_sql_immed;
		$this->workers['sql_var']=new editor_sql_var;
		$this->workers['sql_list']=new editor_sql_list;
		$this->workers['sql_order']=$this->workers['sql_list'];
		$this->workers['sql_expression']=new editor_sql_expression;
		$this->workers['sql_column']=new editor_sql_column;
		$this->workers['sql_subquery']=new editor_sql_subquery;
		$this->workers['sql_select']=new editor_sql_select;
		$this->workers['sql_joins']=new editor_sql_joins;
		$this->workers['query_gen_ext']=$this->workers['sql_select'];
		foreach($this->workers as $n=>$e)
		{
			editor_generic::addeditor($n,$e);
			$this->append_child($e);
			$e->workers=&$this->workers;
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
		}
	}
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		$this->context[$long_name]['oid']=$this->oid;
		
		if(!isset($this->path))$this->path='';
		//if(!isset($this->path))die('Both editor_sql_immed::path and editor_sql_immed::keys[\'path\'] are not set');
//		print $this->path;
		if(!isset($this->curr))
		{
			$this->ioclass=new $this->context[$long_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$long_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		
		foreach($this->workers as $n=>$e)
		{
			$e->path=&$this->path;
			$e->obj=&$this->obj;
			$e->oid=$this->oid;
//			$this->context[$long_name.'.'.$n]['ioclass']=$this->context[$long_name]['ioclass'];
			$e->context= &$this->context;
		}
	}
	
	
	function html()
	{
		$worker=&$this->workers[get_class($this->curr)];
		$worker->curr=&$this->curr;
		$worker->id_alloc();
		if(isset($this->custom_id))$worker->custom_id=$this->custom_id; // !!!!!
		$worker->bootstrap();
		$worker->html();
	}
	
	
	function html_inner()
	{
		//cascade initiation
		$worker=&$this->workers[get_class($this->curr)];
		
		$worker->curr=&$this->curr;
		//print $this->path;
		$worker->id_alloc();
		if(isset($this->custom_id))$worker->custom_id=$this->custom_id; // !!!!!
		$worker->bootstrap();
		$worker->html_inner();
		
	}
	
	function find_by_path($path,$obj=NULL)
	{
		if($obj==NULL)$obj=&$this->obj;
		if($path=='')return $obj;
		if($path=='/')return $obj;
		while($path != '')
		{
			$path=preg_replace('/^\//','',$path);
			$part=preg_replace('/\/.+$/','',$path);
			$path=preg_replace('/^[^\/]+/','',$path);
			if(preg_match('/[0-9]+/',$part))
			{
				if(is_array($obj))
					$obj=&$obj[$part];
				else
					$obj=&$obj->exprs[$part];
			}
			else
				$obj=&$obj->{$part};
		}
		return $obj;
	}
	function del_by_path($path,$obj=NULL)
	{
		if($obj==NULL)$obj=&$this->obj;
		if($path=='')return $obj;
		if($path=='/')return $obj;
		while($path != '')
		{
			$path=preg_replace('/^\//','',$path);
			$part=preg_replace('/\/.+$/','',$path);
			$path=preg_replace('/^[^\/]+/','',$path);
			if(preg_match('/[0-9]+/',$part))
			{
				if($path == '')
				{
					if(!is_array($obj))$obj=&$obj->exprs;
					if(!isset($obj[$part]))print 'alert("deleteing unset object ['.$part.']");';
					unset($obj[$part]);
					return;
				}
				if(is_array($obj))
					$obj=&$obj[$part];
				else
					$obj=&$obj->exprs[$part];
			}
			else
				$obj=&$obj->{$part};
		}
	}
	function move_up_by_path($path,$obj=NULL)
	{
		if($obj==NULL)$obj=&$this->obj;
		if($path=='')return $obj;
		if($path=='/')return $obj;
		while($path != '')
		{
			$path=preg_replace('/^\//','',$path);
			$part=preg_replace('/\/.+$/','',$path);
			$path=preg_replace('/^[^\/]+/','',$path);
			if(preg_match('/[0-9]+/',$part))
			{
				if($path == '')
				{
					if(!is_array($obj))$obj=&$obj->exprs;
					unset($prv);
					foreach($obj as $i =>$o)
					{
						if($i==$part)break;
						$prv=$i;
					}
					if(!isset($prv))return;
					$tmp=$obj[$prv];
					$obj[$prv]=$obj[$part];
					$obj[$part]=$tmp;
					return;
				}
				if(is_array($obj))
					$obj=&$obj[$part];
				else
					$obj=&$obj->exprs[$part];
			}
			else
				$obj=&$obj->{$part};
		}
	}
	function move_dn_by_path($path,$obj=NULL)
	{
		if($obj==NULL)$obj=&$this->obj;
		if($path=='')return $obj;
		if($path=='/')return $obj;
		while($path != '')
		{
			$path=preg_replace('/^\//','',$path);
			$part=preg_replace('/\/.+$/','',$path);
			$path=preg_replace('/^[^\/]+/','',$path);
			if(preg_match('/[0-9]+/',$part))
			{
				if($path == '')
				{
					if(!is_array($obj))$obj=&$obj->exprs;
					unset($prv);
					foreach($obj as $i =>$o)
					{
						if($prv==$part && isset($prv))break;
						$prv=$i;
					}
					if(!isset($prv))return;
					$tmp=$obj[$prv];
					$obj[$prv]=$obj[$i];
					$obj[$i]=$tmp;
					return;
				}
				if(is_array($obj))
					$obj=&$obj[$part];
				else
					$obj=&$obj->exprs[$part];
			}
			else
				$obj=&$obj->{$part};
		}
	}
	function change_by_path($new,$path,$obj=NULL)
	{
		if($obj==NULL)$obj=&$this->obj;
		if($path=='')return $obj;
		if($path=='/')return $obj;
		while($path != '')
		{
			$path=preg_replace('/^\//','',$path);
			$part=preg_replace('/\/.+$/','',$path);
			$path=preg_replace('/^[^\/]+/','',$path);
			if(preg_match('/[0-9]+/',$part))
			{
				if($path == '')
				{
					if(!is_array($obj))$obj=&$obj->exprs;
					unset($obj[$part]);
					$obj[$part]=$new;
					return;
				}
				if(is_array($obj))
					$obj=&$obj[$part];
				else
					$obj=&$obj->exprs[$part];
			}
			else
			{
				if($path == '')
				{
					$obj->{$part}=$new;
				}else
					$obj=&$obj->{$part};
			}
		}
	}
	
	function insert_by_path($new,$path,$obj=NULL)
	{
		if($obj==NULL)$obj=&$this->obj;
		if($path=='')return $obj;
		if($path=='/')return $obj;
		while($path != '')
		{
			$path=preg_replace('/^\//','',$path);
			$part=preg_replace('/\/.+$/','',$path);
			$path=preg_replace('/^[^\/]+/','',$path);
			if(preg_match('/[0-9]+/',$part))
			{
				if($path == '')
				{
					if(!is_array($obj))$obj=&$obj->exprs;
					unset($prv);
					foreach($obj as $i =>$o)
					{
						if(isset($prv))
						{
							$obj[$i]=$prv;
							$prv=$o;
						}
						if($i==$part)
						{
							$prv=$o;
							$obj[$i]=$new;
						}
					}
					if(!isset($prv))return;
					$obj[]=$prv;
					return;
				}
				if(is_array($obj))
					$obj=&$obj[$part];
				else
					$obj=&$obj->exprs[$part];
			}
			else
				$obj=&$obj->{$part};
		}
	}

	function genhint_by_path($field,$path,$obj=NULL)
	{
		global $sql;
		if($obj==NULL)$obj=&$this->obj;
		if($path=='')return Array('Error: invalid (empty) path.');
		if($path=='/')return Array();
		if($obj==NULL)return Array();
		$curr=workers_container::find_by_path($path,$obj);
		$what_cl=preg_match('/^.*\\/what\\/[\\/0-9]*$/',$path);
		$from_cl=preg_match('/^.*\\/from\\/[\\/0-9]*$/',$path)||preg_match('/^.*\\/joins\\/[0-9]+\\/what\\/[\\/0-9]*$/',$path);
		if($field=='db' && $from_cl)
		{
			$ret=Array();
			$res=$sql->query('SHOW DATABASES');
			while($row=$sql->fetchn($res))
				if(is_array($row))$ret[]=$row[0];
			return $ret;
			
		}
		if($field=='tbl' && $from_cl)
		{
			$ret=Array();
			$add='';
			if($curr->db !='')$add=' FROM `'.$sql->esc($curr->db).'`';
			$res=$sql->query('SHOW TABLES'.$add);
			while($row=$sql->fetchn($res))
				if(is_array($row))$ret[]=$row[0];
			return $ret;
			
		}
		if($field=='tbl' && !$from_cl)
		{
			$ret=Array();
			while($path != '')
			{
				$path=preg_replace('/^\//','',$path);
				$part=preg_replace('/\/.+$/','',$path);
				$path=preg_replace('/^[^\/]+/','',$path);
				if(preg_match('/[0-9]+/',$part))
				{
					if(is_array($obj))
						$obj=&$obj[$part];
					else
						$obj=&$obj->exprs[$part];
				}
				else
				{
					//collect columns
					if(is_array($obj->from->exprs))
					{
						reset($obj->from->exprs);
						foreach($obj->from->exprs as $e)
							if($e->alias != '') $ret[]=$e->alias;
							elseif(is_a($e,'sql_column'))$ret[]=$e->tbl;
					}
					if(is_array($obj->joins->exprs))
					{
						reset($obj->joins->exprs);
						foreach($obj->joins->exprs as $j)
							if(is_array($j->what->exprs))
							{
								reset($j->what->exprs);
								foreach($j->what->exprs as $e)
								{
									if($e->alias != '') $ret[]=$e->alias;
									elseif(is_a($e,'sql_column'))$ret[]=$e->tbl;
								}
							}
					}
					$obj=&$obj->{$part};
				}
			}
			return $ret;
		}
		if($field=='col' && !$from_cl)
		{
			$ret=Array();
			while($path != '')
			{
				$path=preg_replace('/^\//','',$path);
				$part=preg_replace('/\/.+$/','',$path);
				$path=preg_replace('/^[^\/]+/','',$path);
				if(preg_match('/[0-9]+/',$part))
				{
					if(is_array($obj))
						$obj=&$obj[$part];
					else
						$obj=&$obj->exprs[$part];
				}
				else
				{
					//collect columns
					if(is_array($obj->from->exprs))
					{
						reset($obj->from->exprs);
						foreach($obj->from->exprs as $e)
						{
							if(is_a($e,'sql_column'))
							{
								if($e->tbl==$curr->tbl || $e->alias==$curr->tbl || $curr->tbl=='')
								{
									$cle=clone $e;
									unset($cle->alias);
									$res=$sql->query('SHOW COLUMNS FROM '.$cle->result());
									if($res)while($row=$sql->fetcha($res))if(is_array($row))
									{
										$ret[]=$row['Field'];
									}
								}
							}
							if(is_a($e,'sql_subquery'))
							{
								if(is_array($e->subquery->what->exprs))foreach($e->subquery->what->exprs as $e1)
									if($e1->alias != '')$ret[]=$e1->alias;
							}
						}
					}
					if(is_array($obj->joins->exprs))
					{
						reset($obj->joins->exprs);
						foreach($obj->joins->exprs as $e0)
						{
							if(is_array($e0->what->exprs))
							{
								reset($e0->what->exprs);
								foreach($e0->what->exprs as $e)
								{
									if(is_a($e,'sql_column'))
									{
										if($e->tbl==$curr->tbl || $e->alias==$curr->tbl || $curr->tbl=='')
										{
											$cle=clone $e;
											unset($cle->alias);
											$res=$sql->query('SHOW COLUMNS FROM '.$cle->result());
											if($res)while($row=$sql->fetcha($res))if(is_array($row))
											{
												$ret[]=$row['Field'];
											}
										}
									}
									if(is_a($e,'sql_subquery'))
									{
										if(is_array($e->subquery->what->exprs))foreach($e->subquery->what->exprs as $e1)
											if($e1->alias != '')$ret[]=$e1->alias;
									}
								}
							}
						}
					}
					$obj=&$obj->{$part};
				}
			}
			return $ret;
		}
		return Array($what_cl,$from_cl,$field);
		while($path != '')
		{
			$path=preg_replace('/^\//','',$path);
			$part=preg_replace('/\/.+$/','',$path);
			$path=preg_replace('/^[^\/]+/','',$path);
			if(preg_match('/[0-9]+/',$part))
			{
				if(is_array($obj))
					$obj=&$obj[$part];
				else
					$obj=&$obj->exprs[$part];
			}
			else
				$obj=&$obj->{$part};
		}
	}
	
	
	

	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}



//###############################################################################################3
//##################################   editor_sql_null  definition  ############################3
//###############################################################################################3


class editor_sql_null extends dom_any
{
	function __construct()
	{
		parent::__construct('div');
		$this->etype=get_class($this);
		$tbl=new dom_table;
		$this->append_child($tbl);
		$tr=new dom_tr;
		$tbl->append_child($tr);
		
		$td=new dom_td;
		$tr->append_child($td);
		$td->append_child(new dom_statictext('NULL'));
		
		$this->as_td=$td=new dom_td;
		$tr->append_child($td);
		$st=new dom_statictext;
		$td->append_child($st);
		$st->text='as';
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('ial',new editor_text);
		$td->append_child($this->editors['ial']);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('do_copy',new editor_button_image);
		$td->append_child($this->editors['do_copy']);
		$this->editors['do_copy']->attributes['src']='/i/copy.png';
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_immed::path and editor_sql_immed::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			
			$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->keys['path']=$this->path;
		$this->args['ial']=$this->curr->alias;
		foreach($this->editors as $e)$e->bootstrap();
		unset($this->editors['ial']->css_style['display']);
		unset($this->as_td->css_style['display']);
		if(!preg_match('/.*\\/what(\\/\\d+)$/',$this->path)
		)
		{
			$this->editors['ial']->css_style['display']='none';
			$this->as_td->css_style['display']='none';
		}
	}
	
	function handle_event($ev)
	{
		global $clipboard;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$this->path=$ev->keys['path'];
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		switch($ev->rem_name)
		{
		case 'ial':
			print '/* '.$this->curr->val.' */';
			$this->curr->alias=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'do_copy':
			if(!isset($clipboard))break;
			$copy=clone $this->curr;
			$clipboard->store($copy);
			break;
		}
		editor_generic::handle_event($ev);
	}
}


//###############################################################################################3
//##################################   editor_sql_immed  definition  ############################3
//###############################################################################################3


class editor_sql_immed extends dom_any
{
	function __construct()
	{
		parent::__construct('div');
		$this->etype='editor_sql_immed';
		$tbl=new dom_table;
		$this->append_child($tbl);
		$tr=new dom_tr;
		$tbl->append_child($tr);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('ival',new editor_text);
		$td->append_child($this->editors['ival']);
		
		$this->as_td=$td=new dom_td;
		$tr->append_child($td);
		$st=new dom_statictext;
		$td->append_child($st);
		$st->text='as';
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('ial',new editor_text);
		$td->append_child($this->editors['ial']);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('do_copy',new editor_button_image);
		$td->append_child($this->editors['do_copy']);
		$this->editors['do_copy']->attributes['src']='/i/copy.png';
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_immed::path and editor_sql_immed::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			
			$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->keys['path']=$this->path;
		$this->args['ival']=$this->curr->val;
		$this->args['ial']=$this->curr->alias;
		foreach($this->editors as $e)$e->bootstrap();
		unset($this->editors['ial']->css_style['display']);
		unset($this->as_td->css_style['display']);
		if(!preg_match('/.*\\/what(\\/\\d+)$/',$this->path)
		)
		{
			$this->editors['ial']->css_style['display']='none';
			$this->as_td->css_style['display']='none';
		}
	}
	
	function handle_event($ev)
	{
		global $clipboard;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$this->path=$ev->keys['path'];
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		switch($ev->rem_name)
		{
		case 'ial':
			print '/* '.$this->curr->val.' */';
			$this->curr->alias=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'ival':
			print '/* '.$this->curr->val.' */';
			$this->curr->val=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'do_copy':
			if(!isset($clipboard))break;
			$copy=clone $this->curr;
			$clipboard->store($copy);
			break;
		}
		editor_generic::handle_event($ev);
	}
}
//###############################################################################################3
//##################################   editor_sql_var definition  ###############################3
//###############################################################################################3


class editor_sql_var extends dom_any
{
	function __construct()
	{
		parent::__construct('div');
		$this->etype=get_class($this);
		$tbl=new dom_table;
		$this->append_child($tbl);
		$tr=new dom_tr;
		$tbl->append_child($tr);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('ival',new editor_text);
		$td->append_child($this->editors['ival']);
		
		$this->as_td=$td=new dom_td;
		$tr->append_child($td);
		$st=new dom_statictext;
		$td->append_child($st);
		$st->text='as';
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('ial',new editor_text);
		$td->append_child($this->editors['ial']);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('do_copy',new editor_button_image);
		$td->append_child($this->editors['do_copy']);
		$this->editors['do_copy']->attributes['src']='/i/copy.png';
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_var::path and editor_sql_var::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			
			$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->keys['path']=$this->path;
		$this->args['ival']=$this->curr->val;
		$this->args['ial']=$this->curr->alias;
		foreach($this->editors as $e)$e->bootstrap();
		unset($this->editors['ial']->css_style['display']);
		unset($this->as_td->css_style['display']);
		if(!preg_match('/.*\\/what(\\/\\d+)$/',$this->path)
		)
		{
			$this->editors['ial']->css_style['display']='none';
			$this->as_td->css_style['display']='none';
		}
	}
	
	function handle_event($ev)
	{
		global $clipboard;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$this->path=$ev->keys['path'];
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		switch($ev->rem_name)
		{
		case 'ial':
			print '/* '.$this->curr->val.' */';
			$this->curr->alias=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'ival':
			print '/* '.$this->curr->val.' */';
			$this->curr->val=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'do_copy':
			if(!isset($clipboard))break;
			$copy=clone $this->curr;
			$clipboard->store($copy);
			break;
		}
		editor_generic::handle_event($ev);
	}
}

//###############################################################################################3
//##################################   editor_sql_list  definition  #############################3
//###############################################################################################3

class editor_sql_list extends dom_any
{
	public $obj=NULL;//reference
	public $path='';//'/from/0/where/1...
	
	function __construct()
	{
		parent::__construct('div');
		$this->etype='editor_sql_list';
		$this->args=Array();
		
		$this->css_style['border']='1px gray solid';
		#$this->head=new dom_div;
		$this->head=new container_autotable;
		$this->head->css_style['border']='1px red solid';
		$this->head->css_style['background']='#aaaaaa';
		$this->append_child($this->head);
		
		editor_generic::addeditor('func',new editor_text);
		$this->head->append_child($this->editors['func']);
		
		
		editor_generic::addeditor('add',new editor_pick_button);
		$this->head->append_child($this->editors['add']);
		$this->editors['add']->list_class='editor_sql_pick_list';
		$this->editors['add']->button->attributes['type']='image';
		$this->editors['add']->button->attributes['src']='/i/add.png';
		
		editor_generic::addeditor('do_copy',new editor_button_image);
		$this->head->append_child($this->editors['do_copy']);
		$this->editors['do_copy']->attributes['src']='/i/copy.png';
		
		editor_generic::addeditor('do_paste',new editor_button_image);
		$this->head->append_child($this->editors['do_paste']);
		$this->editors['do_paste']->attributes['src']='/i/paste.png';
		
		editor_generic::addeditor('alias',new editor_text);
		$this->head->append_child($this->editors['alias']);
		
		$this->row=new dom_table;
		$this->row->css_style['border']='1px green solid';
		$this->append_child($this->row);
		
		$this->tr= new dom_tr;
		$this->row->append_child($this->tr);
		
		$this->td_sub=new dom_td;
		$this->td_sub->css_style['border']='1px pink solid';
		$this->tr->append_child($this->td_sub);//container for virtual editor from workers_container
		
		$this->cont_td=new dom_td;
		$this->tr->append_child($this->cont_td);
		
		editor_generic::addeditor('invert',new editor_checkbox);
		$this->cont_td->append_child($this->editors['invert']);
		
		editor_generic::addeditor('paste_here',new editor_button_image);
		$this->cont_td->append_child($this->editors['paste_here']);
		$this->editors['paste_here']->attributes['src']='/i/paste.png';
		
		//button to change type,delete button ?add here button?
		editor_generic::addeditor('chtype',new editor_pick_button);
		$this->cont_td->append_child($this->editors['chtype']);
		$this->editors['chtype']->list_class='editor_sql_pick_list';
		$this->editors['chtype']->button->attributes['value']='♲';
		
		editor_generic::addeditor('del',new editor_button);
		$this->cont_td->append_child($this->editors['del']);
		$this->editors['del']->attributes['value']='-';
		
		editor_generic::addeditor('move_up',new editor_button);
		$this->cont_td->append_child($this->editors['move_up']);
		$this->editors['move_up']->attributes['value']='↑';
		
		editor_generic::addeditor('move_dn',new editor_button);
		$this->cont_td->append_child($this->editors['move_dn']);
		$this->editors['move_dn']->attributes['value']='↓';
		
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_immed::path and editor_sql_immed::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
			
			$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		//$this->context[$this->long_name.'.func']['var']='func';
		$this->context[$this->long_name.'.alias']['var']='ial';
		$this->args['func']=$this->curr->func;
		$this->args['ial']=$this->curr->alias;
		
		$this->keys['path']=$this->path;
		
		$this->args['chtype']=$this->curr->val;
		$this->args['del']=$this->curr->alias;
		foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		
		unset($this->editors['alias']->css_style['display']);
		unset($this->editors['func']->css_style['display']);
		$this->editors['invert']->css_style['display']='none';
		if(preg_match('/.*\\/(from|what|order|group)$/',$this->path))
		{
			$this->editors['alias']->css_style['display']='none';
			$this->editors['func']->css_style['display']='none';
		}
		if(preg_match('/.*\\/(where|on|having)(\\/\\d+)*$/',$this->path))
		{
			$this->editors['alias']->css_style['display']='none';
		}
		if(preg_match('/.*\\/(group|order)(\\/\\d+)*$/',$this->path))
		{
			unset($this->editors['invert']->css_style['display']);
		}
		$this->head->html();
		unset($this->custom_id);
		$this->row->html_head();
		$curr=$this->curr;
		$path=$this->path;
		$long_name=$this->long_name;
		$htmlid=$this->context[$long_name]['htmlid'];
		$expr_counter=0;
		$expr_max=is_array($curr->exprs)?count($curr->exprs):0;
		if(is_array($curr->exprs))foreach($curr->exprs as $k=>$e)
		{
			$cn=get_class($e);//get worker cell
			if(!isset($this->workers[$cn]))continue;
			//output worker
			//print $cn;
			$this->workers[$cn]->id_alloc();
			$this->workers[$cn]->path=$path.'/'.$k;
			$this->workers[$cn]->curr=&$e;
			$this->workers[$cn]->bootstrap();
			
			$this->tr->html_head();
			$this->td_sub->html_head();
			$this->workers[$cn]->html();
			//output row tail
			$this->path=$path.'/'.$k;
			$this->context[$long_name]['htmlid']=$htmlid;
			$this->args['invert']=$e->invert;
			
			foreach($this->editors as $e)$e->bootstrap();
			
			if($expr_counter==0)$this->editors['move_up']->css_style['visibility']='hidden';
			else unset($this->editors['move_up']->css_style['visibility']);
			if($expr_counter==$expr_max-1)$this->editors['move_dn']->css_style['visibility']='hidden';
			else unset($this->editors['move_dn']->css_style['visibility']);
			
			
			
			$this->td_sub->html_tail();
			$this->cont_td->html();
			$this->tr->html_tail();
			$this->tr->id_alloc();
			//restor possibly damaged fields
			$this->path=$path;
			$this->curr=$curr;
			$this->keys['path']=$this->path;
			//foreach($this->editors as $e)$e->bootstrap();
			$expr_counter++;
			
			
		}
		$this->row->html_tail();
	
	}
	
	function handle_event($ev)
	{
		global $clipboard;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		
		$this->path=$ev->keys['path'];
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		print "\n\n/** $io_name **/ \n\n\n";
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		$reload=false;
		switch($ev->rem_name)
		{
		case 'alias':
			$this->curr->alias=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'del':
			workers_container::del_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'invert':
			if($_POST['val']==1)$this->curr->invert=1;
			else unset($this->curr->invert);
			$this->ioclass->save($this->obj);
			break;
		case 'move_up':
			workers_container::move_up_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'move_dn':
			workers_container::move_dn_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'add.btn':
			$new = new $_POST['val'];
			$this->curr->exprs[]=$new;
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'do_paste':
			if(!isset($clipboard))break;
			$new=$clipboard->fetch();
			if(!method_exists($new,'result'))break;
			$this->curr->exprs[]=$new;
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'paste_here':
			if(!isset($clipboard))break;
			$new=$clipboard->fetch();
			if(!method_exists($new,'result'))break;
			workers_container::insert_by_path($new,$this->path,$this->obj);
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'do_copy':
			if(!isset($clipboard))break;
			$copy=clone $this->curr;
			$clipboard->store($copy);
			break;
		case 'chtype.btn':
			$new = new $_POST['val'];
			workers_container::change_by_path($new,$this->path,$this->obj);
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'func':
			$this->curr->func=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		}
		if($reload)
		{
			$r=new workers_container;
			//$r->obj=&$this->obj;// not required
			$r->path=&$this->path;
			$r->name=preg_replace('/\.[^.]+$/','',$ev->parent_name);
			$r->etype=preg_replace('/\.[^.]+$/','',$ev->parent_type);
			$r->context=$ev->context;
			$r->oid=$oid;
			
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=";
			reload_object($r,true);
			print "}catch(e){ window.location.reload(true);};";
		}
		editor_generic::handle_event($ev);
	}
}



//###############################################################################################3
//##################################   editor_sql_pick_list definition  ######################3
//###############################################################################################3

class editor_sql_pick_list extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->etype='editor_pick_button';
		$div=new dom_tr;
		$this->append_child($div);
		editor_generic::addeditor('text',new editor_statictext);
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['text']);
		
		editor_generic::addeditor('btn',new editor_button);
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['btn']);
		$this->editors['btn']->attributes['value']='+';
		
	}
	
	function bootstrap()
	{
		//$this->picklist=$this->context
		$this->long_name=$long_name=editor_generic::long_name();
		$this->context[$long_name.'.text']['var']='c';
		$this->context[$long_name.'.btn']['var']='c';
		
	}
	
	
	function html_inner()
	{
		//$this->long_name=$long_name=editor_generic::long_name();
		
		$this->picklist=Array('sql_null','sql_immed','sql_var','sql_list','sql_expression','sql_column','sql_subquery'); //replace with correct list fetching from info_class/workers_container
		//$this->picklist[]=$this->keys['path'];
		reset($this->picklist);
		foreach($this->editors as $e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
		}
		foreach($this->picklist as $i => $r)
		{
			$this->args['c']=$r;
			$this->id_alloc();
			reset($this->editors);
			foreach($this->editors as $e)
				$e->bootstrap();
			dom_table::html_inner();
		}
	}
}


//###############################################################################################3
//##################################   editor_sql_expression  definition  #######################3
//###############################################################################################3

class editor_sql_expression extends editor_sql_list
{
	function __construct()
	{
		parent::__construct();
		$this->etype='editor_sql_expression';
	}
	
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_expression::path and editor_sql_expression::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
			
			$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name.'.func']['var']='func';
		$this->context[$this->long_name.'.alias']['var']='ial';
		$this->args['func']=$this->curr->operator;
		$this->args['ial']=$this->curr->alias;
		
		$this->keys['path']=$this->path;
		
		$this->args['chtype']=$this->curr->val;
		$this->args['del']=$this->curr->alias;
		foreach($this->editors as $e)$e->bootstrap();
	}
	
	function handle_event($ev)
	{
		global $clipboard;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		
		$this->path=$ev->keys['path'];
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		$reload=false;
		switch($ev->rem_name)
		{
		case 'del':
			workers_container::del_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'move_up':
			workers_container::move_up_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'move_dn':
			workers_container::move_dn_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'add.btn':
			$new = new $_POST['val'];
			$this->curr->exprs[]=$new;
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'do_paste':
			if(!isset($clipboard))break;
			$new=$clipboard->fetch();
			if(!method_exists($new,'result'))break;
			$this->curr->exprs[]=$new;
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'do_copy':
			if(!isset($clipboard))break;
			$copy=clone $this->curr;
			$clipboard->store($copy);
			break;
		case 'paste_here':
			if(!isset($clipboard))break;
			$new=$clipboard->fetch();
			if(!method_exists($new,'result'))break;
			workers_container::insert_by_path($new,$this->path,$this->obj);
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'chtype.btn':
			$new = new $_POST['val'];
			workers_container::change_by_path($new,$this->path,$this->obj);
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'func':
			$this->curr->operator=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'alias':
			$this->curr->alias=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		}
		if($reload)
		{
			$r=new workers_container;
			//$r->obj=&$this->obj;// not required
			$r->path=&$this->path;
			$r->name=preg_replace('/\.[^.]+$/','',$ev->parent_name);
			$r->etype=preg_replace('/\.[^.]+$/','',$ev->parent_type);
			$r->context=$ev->context;
			$r->oid=$oid;
			
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=";
			reload_object($r,true);
			print "}catch(e){ window.location.reload(true);};";
		}
		editor_generic::handle_event($ev);
	}
}








//class sql_column
//{
//	public $db='',$tbl='',$col='',$alias='';

//###############################################################################################3
//##################################   editor_sql_column  definition  ############################3
//###############################################################################################3


class editor_sql_column extends dom_any
{
	function __construct()
	{
		parent::__construct('div');
		$this->etype='editor_sql_column';
		$tbl=new dom_table;
		$this->append_child($tbl);
		$tr=new dom_tr;
		$tbl->append_child($tr);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('db',new editor_text_autosuggest);
		$this->editors['db']->list_class='editor_taswc_list';
		$this->editors['db']->refresh_always=TRUE;
		$td->append_child($this->editors['db']);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('tbl',new editor_text_autosuggest);
		$this->editors['tbl']->list_class='editor_taswc_list';
		$this->editors['tbl']->refresh_always=TRUE;
		$td->append_child($this->editors['tbl']);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('col',new editor_text_autosuggest);
		$this->editors['col']->list_class='editor_taswc_list';
		$this->editors['col']->refresh_always=TRUE;
		$td->append_child($this->editors['col']);
		
		$this->aliast=$td=new dom_td;
		$tr->append_child($td);
		$st=new dom_statictext;
		$td->append_child($st);
		$st->text='as';
		
		$this->aliase=$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('ial',new editor_text);
		$td->append_child($this->editors['ial']);
		
		$td=new dom_td;
		$tr->append_child($td);
		editor_generic::addeditor('do_copy',new editor_button_image);
		$td->append_child($this->editors['do_copy']);
		$this->editors['do_copy']->attributes['src']='/i/copy.png';
		
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_immed::path and editor_sql_immed::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			
			$this->context[$long_name.'.'.$i]['var']=$i;
//			$this->context[$long_name.'.'.$i]['ioclass']=$this->context[$long_name]['ioclass'];
			
		}
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->keys['path']=$this->path;
		$this->args['db']=$this->curr->db;
		$this->args['tbl']=$this->curr->tbl;
		$this->args['col']=$this->curr->col;
		$this->args['ial']=$this->curr->alias;
		foreach($this->editors as $e)$e->bootstrap();
		unset($this->aliast->css_style['display']);
		unset($this->aliase->css_style['display']);
		if(preg_match('/.*\\/(where|on|order|group)(\\/\\d+)*$/',$this->path)||preg_match('/.*\\/(what|from)\\/\\d+(\\/\\d+)+$/',$this->path))
		{
			$this->aliast->css_style['display']='none';
			$this->aliase->css_style['display']='none';
		}
	}
	
	function handle_event($ev)
	{
		global $clipboard;
		$this->path=$ev->keys['path'];
		$oid=$ev->context[$ev->parent_name]['oid'];
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		switch($ev->rem_name)
		{
		case 'ial':
			$this->curr->alias=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'db':
			$this->curr->db=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'tbl':
			$this->curr->tbl=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'col':
			$this->curr->col=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'do_copy':
			if(!isset($clipboard))break;
			$copy=clone $this->curr;
			$clipboard->store($copy);
			break;
		}
		editor_generic::handle_event($ev);
	}
}

class editor_taswc_list extends editor_text_autosuggest_list_example
{
	function __construct()
	{
		parent::__construct();
		unset($this->list_items);
	}
	
	function html_inner()
	{
		$parent_name=preg_replace('/\.[^.]*$/','',$this->name);
		$a=Array();
		$this->list_items=Array();
		//$this->list_items[0]=$this->context[$parent_name]['ioclass'];	//debug
		//$this->list_items[1]=$this->keys['path'];			//debug
		//$this->list_items[2]=$this->name;				//debug
		
		$this->path=$this->keys['path'];
		$io_name=preg_replace('/\\.[^.]+$/','',$parent_name);
		$ioclass=$this->context[$io_name]['ioclass'];
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$this->context;
		$this->ioclass->oid=$this->context[$io_name]['oid'];
		$this->ioclass->long_name=$io_name;
		
		$this->obj=$this->ioclass->load();
		$part_list=workers_container::genhint_by_path(preg_replace('/^.*\./','',$this->name),$this->path,$this->obj);
		if(is_array($part_list))foreach($part_list as $l)$this->list_items[]=$l;
		
		
		if(!is_array($this->list_items))
		{
			$this->list_items=Array();
		}
		foreach($this->list_items as $v)
		{
			//if(!preg_match('/'.preg_quote($this->input_part,'/').'/',$v))continue;
			if($this->input_part=='')
			{
				$this->args['i']=htmlspecialchars($v);
			}else{
				$v1=htmlspecialchars($this->input_part);
				$v2=htmlspecialchars($v);
				$this->args['i']=preg_replace('/'.preg_quote($v1,'/').'/','<span style=\'font-size:1.2em;\'>'.$v1.'</span>',$v2);
			}
			$this->id_alloc();
			foreach($this->editors as $e)$e->bootstrap();
			unset($it);
			$it->id=$this->tr->id_gen();
			$it->val=$v;
			$this->setup_tr(count($a));
			$a[]=$it;
			$this->tr->html();
		}
		$this->result_array=&$a;
	}

}




















//class sql_subquery
//public $subquery=NULL,$alias='';
//###############################################################################################3
//##################################   editor_sql_subquery  definition  #########################3
//###############################################################################################3
class editor_sql_subquery extends dom_any
{
	public $obj=NULL;//reference
	public $path='';//'/from/0/where/1...
	
	function __construct()
	{
		parent::__construct('div');
		$this->etype='editor_sql_subquery';
		$this->args=Array();
		$this->row=new dom_table;
		$this->append_child($this->row);
		
		$this->tr= new dom_tr;
		$this->row->append_child($this->tr);
		
		$this->td_sub=new dom_td;
		$this->td_sub->css_style['border']='1px pink solid';
		$this->tr->append_child($this->td_sub);//container for virtual editor from workers_container
		
		$this->cont_td=new dom_td;
		$this->tr->append_child($this->cont_td);
		
		editor_generic::addeditor('ial',new editor_text);
		$this->cont_td->append_child($this->editors['ial']);
		
		editor_generic::addeditor('do_copy',new editor_button_image);
		$this->cont_td->append_child($this->editors['do_copy']);
		$this->editors['do_copy']->attributes['src']='/i/copy.png';
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_immed::path and editor_sql_immed::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
			
			$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->args['ial']=$this->curr->alias;
		
		$this->keys['path']=$this->path;
		
		foreach($this->editors as $e)$e->bootstrap();
		unset($this->editors['ial']->css_style['display']);
		if(!preg_match('/.*\\/what(\\/\\d+)$/',$this->path)
		)
		{
			$this->editors['ial']->css_style['display']='none';
		}
	}
	
	function html_inner()
	{
		
		unset($this->custom_id);
		$this->row->html_head();
		$curr=$this->curr;
		$path=$this->path;
		$long_name=$this->long_name;
		$htmlid=$this->context[$long_name]['htmlid'];
		if(isset($curr->subquery))
		{
			$cn='sql_select';//get worker cell
			
			//output worker
			//print $cn;
			$this->workers[$cn]->id_alloc();
			$this->workers[$cn]->path=$path.'/subquery';
			$this->workers[$cn]->curr=&$this->curr->subquery;
			$this->workers[$cn]->bootstrap();
			
			$no_settings_old=isset($this->workers[$cn]->editors['tabs']->no_settings);
			$this->workers[$cn]->editors['tabs']->no_settings=1;
			
			$this->tr->html_head();
			$this->td_sub->html_head();
			$this->workers[$cn]->html();
			
			if(!$no_settings_old)unset($this->workers[$cn]->editors['tabs']->no_settings);
			
			//output row tail
			$this->path=$path;
			$this->curr=$curr;
			$this->keys['path']=$this->path;
			$this->args['ial']=$this->curr->alias;
			$this->context[$long_name]['htmlid']=$htmlid;
			foreach($this->editors as $e)$e->bootstrap();
			$this->td_sub->html_tail();
			$this->cont_td->html();
			$this->tr->html_tail();
			//restor possibly damaged fields
			//foreach($this->editors as $e)$e->bootstrap();
			
			
		}
		$this->row->html_tail();
	
	}
	
	function handle_event($ev)
	{
		global $clipboard;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		
		$this->path=$ev->keys['path'];
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		$reload=false;
		switch($ev->rem_name)
		{
		case 'ial':
			print ' /*'.get_class($this->curr).'*/ ';
			$this->curr->alias=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'do_copy':
			if(!isset($clipboard))break;
			$copy=clone $this->curr;
			$clipboard->store($copy);
			break;
		}
		editor_generic::handle_event($ev);
	}
}




//###############################################################################################3
//##################################   editor_sql_joins       definition  #######################3
//###############################################################################################3

class editor_sql_joins extends dom_any
{
	public $obj=NULL;//reference
	public $path='';//'/from/0/where/1...
	
	function __construct()
	{
		parent::__construct('div');
		$this->etype='editor_sql_joins';
		$this->args=Array();
		
		$this->css_style['border']='1px gray solid';
		$this->head=new dom_div;
		$this->head->css_style['border']='1px red solid';
		$this->head->css_style['background']='#aaaaaa';
		$this->append_child($this->head);
		
		
		editor_generic::addeditor('add',new editor_button_image);
		$this->head->append_child($this->editors['add']);
		$this->editors['add']->attributes['src']='/i/add.png';
		
		$this->row=new dom_table;
		$this->row->css_style['border']='1px green solid';
		$this->append_child($this->row);
		
		$this->tr= new dom_tr;
		$this->row->append_child($this->tr);
		$this->tr->css_style['background-color']='#70FFFF';
		
		$this->tr1= new dom_tr;
		$this->row->append_child($this->tr1);
		$this->tr1->css_style['background-color']='#FF70FF';
		
		$this->td_jtype=new dom_td;
		$this->td_jtype->css_style['border']='1px pink solid';
		$this->tr->append_child($this->td_jtype);//join type
		editor_generic::addeditor('jtype',new editor_text);
		$this->td_jtype->append_child($this->editors['jtype']);
		
		$this->td_jwhat=new dom_td;
		$this->td_jwhat->css_style['border']='1px pink solid';
		$this->tr->append_child($this->td_jwhat);//container for join tables list
		
		$this->td_on=new dom_td;
		$this->td_on->css_style['border']='1px pink solid';
		$this->tr1->append_child($this->td_on);
		$txt=new dom_statictext;
		$txt->text='ON';
		$this->td_on->append_child($txt);
		
		$this->td_j_on=new dom_td;
		$this->td_j_on->css_style['border']='1px pink solid';
		$this->tr1->append_child($this->td_j_on);//container for join condition expression
		
		$this->cont_td=new dom_td;
		$this->tr1->append_child($this->cont_td);
		
		editor_generic::addeditor('del',new editor_button);
		$this->cont_td->append_child($this->editors['del']);
		$this->editors['del']->attributes['value']='-';
		
		editor_generic::addeditor('move_up',new editor_button);
		$this->cont_td->append_child($this->editors['move_up']);
		$this->editors['move_up']->attributes['value']='↑';
		
		editor_generic::addeditor('move_dn',new editor_button);
		$this->cont_td->append_child($this->editors['move_dn']);
		$this->editors['move_dn']->attributes['value']='↓';
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_immed::path and editor_sql_immed::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
			
			$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		//$this->context[$this->long_name.'.func']['var']='func';
		$this->keys['path']=$this->path;
		foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		
		$this->head->html();
		unset($this->custom_id);
		$this->row->html_head();
		$curr=$this->curr;
		$path=$this->path;
		$long_name=$this->long_name;
		$htmlid=$this->context[$long_name]['htmlid'];
		$expr_counter=0;
		$expr_max=is_array($curr->exprs)?count($curr->exprs):0;
		if(is_array($curr->exprs))foreach($curr->exprs as $k=>$e)
		{
			
			$this->keys['path']=$this->path.'/'.$k;
			$this->args['jtype']=$e->type;
			foreach($this->editors as $ed)$ed->bootstrap();
			$this->tr->html_head();
			$this->td_jtype->html();
			$this->td_jwhat->html_head();
			$cn=get_class($e->what);//get worker cell
			$this->workers[$cn]->id_alloc();
			$this->workers[$cn]->path=$path.'/'.$k.'/what';
			$this->workers[$cn]->curr=&$e->what;
			$this->workers[$cn]->bootstrap();
			$this->workers[$cn]->html();
			$this->td_jwhat->html_tail();
			$this->path=$path.'/'.$k;
			$this->context[$long_name]['htmlid']=$htmlid;
			foreach($this->editors as $ed)$ed->bootstrap();
			
			$this->tr->html_tail();
			
			$this->tr1->html_head();
			$this->td_on->html();
			
			$this->td_j_on->html_head();
			//print_r( $e);
			$cn=get_class($e->on);//get worker cell
			//print 'cn='.$cn;
			$this->workers[$cn]->id_alloc();
			$this->workers[$cn]->path=$path.'/'.$k.'/on';
			$this->workers[$cn]->curr=&$e->on;
			$this->workers[$cn]->bootstrap();
			$this->workers[$cn]->html();
			$this->td_j_on->html_tail();
			$this->path=$path.'/'.$k;
			$this->context[$long_name]['htmlid']=$htmlid;
			foreach($this->editors as $ed)$ed->bootstrap();
			
			
			
			if($expr_counter==0)$this->editors['move_up']->css_style['visibility']='hidden';
			else unset($this->editors['move_up']->css_style['visibility']);
			if($expr_counter==$expr_max-1)$this->editors['move_dn']->css_style['visibility']='hidden';
			else unset($this->editors['move_dn']->css_style['visibility']);
			
			
			
			$this->cont_td->html();
			$this->tr1->html_tail();
			//restor possibly damaged fields
			$this->path=$path;
			$this->curr=$curr;
			$this->keys['path']=$this->path;
			$this->tr->id_alloc();
			$this->tr1->id_alloc();
			//foreach($this->editors as $e)$e->bootstrap();
			$expr_counter++;
			
			
		}
		$this->row->html_tail();
	
	}
	
	function handle_event($ev)
	{
		$oid=$ev->context[$ev->parent_name]['oid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		
		$this->path=$ev->keys['path'];
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		$reload=false;
		switch($ev->rem_name)
		{
		case 'del':
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			workers_container::del_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'jtype':
			//$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$curr=workers_container::find_by_path($this->path,$this->obj);
			$curr->type=$_POST['val'];
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=false;
			break;
		case 'move_up':
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			workers_container::move_up_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'move_dn':
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			workers_container::move_dn_by_path($this->path,$this->obj);
			//path points to deleted object, so reload it's parent
			$this->path=preg_replace('/\/[^\/]+$/','',$this->path);
			$this->ioclass->save($this->obj);
			$reload=true;
			break;
		case 'add':
			unset($new);
			$new->on=new sql_expression;
			$new->what=new sql_list;
			$new->type='join';
			$this->curr->exprs[]=$new;
			$this->ioclass->save($this->obj);
			$reload=true;
			//print_r($this->curr);
			break;
		}
		if($reload)
		{
			$r=new workers_container;
			//$r->obj=&$this->obj;// not required
			$r->path=&$this->path;
			$r->name=preg_replace('/\.[^.]+$/','',$ev->parent_name);
			$r->etype=preg_replace('/\.[^.]+$/','',$ev->parent_type);
			$r->context=$ev->context;
			$r->oid=$oid;
			
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=";
			reload_object($r,true);
			print "}catch(e){ window.location.reload(true);};";
		}
		editor_generic::handle_event($ev);
	}
}













//###############################################################################################3
//##################################   editor_sql_select  definition  ###########################3
//###############################################################################################3


class editor_sql_select extends dom_any
{
	public $obj=NULL;//reference
	public $path='';//'/from/0/where/1...
	
	function __construct()
	{
		parent::__construct('div');
		$this->etype='editor_sql_select';
		$this->css_class=$this->etype;
		$this->args=Array();
		
		editor_generic::addeditor('clean',new editor_button);
		$this->editors['clean']->attributes['value']='clean';
		$this->append_child($this->editors['clean']);
		
		$editors_tabs=new container_tab_control;
		//$editors_tabs=new container_tab_control_l;
		editor_generic::addeditor('tabs',$editors_tabs);
		$this->append_child($editors_tabs);
		
		$wrapper=new wrapper_sql_select($this,'sql_list','what');
		$editors_tabs->add_tab('what',loc_get_val('editor_sql_select','sql_what','what'));
		$editors_tabs->tabs['what']->div->append_child($wrapper);
	
		$wrapper=new wrapper_sql_select($this,'sql_list','from');
		$editors_tabs->add_tab('from',loc_get_val('editor_sql_select','sql_from','from'));
		$editors_tabs->tabs['from']->div->append_child($wrapper);
		
		$wrapper=new wrapper_sql_select($this,'sql_joins','joins');
		$editors_tabs->add_tab('join',loc_get_val('editor_sql_select','sql_joins','join'));
		$editors_tabs->tabs['join']->div->append_child($wrapper);
	
		$wrapper=new wrapper_sql_select($this,'sql_expression','where');
		$editors_tabs->add_tab('where',loc_get_val('editor_sql_select','sql_where','where'));
		$editors_tabs->tabs['where']->div->append_child($wrapper);
		
		$wrapper=new wrapper_sql_select($this,'sql_list','group');
		$editors_tabs->add_tab('group',loc_get_val('editor_sql_select','sql_group','group'));
		$editors_tabs->tabs['group']->div->append_child($wrapper);
		
		$wrapper=new wrapper_sql_select($this,'sql_expression','having');
		$editors_tabs->add_tab('having',loc_get_val('editor_sql_select','sql_having','having'));
		$editors_tabs->tabs['having']->div->append_child($wrapper);
		
		$wrapper=new wrapper_sql_select($this,'sql_list','order');
		$editors_tabs->add_tab('order',loc_get_val('editor_sql_select','sql_order','order'));
		$editors_tabs->tabs['order']->div->append_child($wrapper);
		
		$editors_tabs->add_tab('limit',loc_get_val('editor_sql_select','sql_limit','limit'));
		$tb=new dom_table;
		$tr=new dom_tr;	$tb->append_child($tr);
		$td=new dom_td;	$tr->append_child($td);
		$txt=new dom_statictext; $td->append_child($txt);
		$txt->text=loc_get_val('editor_sql_select','sql_limit_skip','skip');
		$td=new dom_td;	$tr->append_child($td);
		editor_generic::addeditor('limit_skip',new editor_text);
		$td->append_child($this->editors['limit_skip']);
		
		$this->editors['limit_skip']->add_btn=new dom_any_noterm('input');
		$td->append_child($this->editors['limit_skip']->add_btn);
		$this->editors['limit_skip']->add_btn->attributes['value']='+';
		$this->editors['limit_skip']->add_btn->attributes['type']='submit';
		
		$this->editors['limit_skip']->sub_btn=new dom_any_noterm('input');
		$td->append_child($this->editors['limit_skip']->sub_btn);
		$this->editors['limit_skip']->sub_btn->attributes['value']='-';
		$this->editors['limit_skip']->sub_btn->attributes['type']='submit';
		
		
		
		$tr=new dom_tr;	$tb->append_child($tr);
		$td=new dom_td;	$tr->append_child($td);
		$txt=new dom_statictext; $td->append_child($txt);
		$txt->text=loc_get_val('editor_sql_select','sql_limit_count','count');
		$td=new dom_td;	$tr->append_child($td);
		editor_generic::addeditor('limit_count',new editor_text);
		$td->append_child($this->editors['limit_count']);
		
		$editors_tabs->tabs['limit']->div->append_child($tb);
		
		$editors_tabs->add_tab('result',loc_get_val('editor_sql_select','result_tab','result'));
		editor_generic::addeditor('result_button',new editor_button);
		$editors_tabs->tabs['result']->div->append_child($this->editors['result_button']);
		//$this->editors['result_button']->css_style['display']='none';
		$this->result_div=new dom_div;
		$editors_tabs->tabs['result']->div->append_child($this->result_div);
		

	}
	
	function bootstrap()
	{
		$this->long_name=$long_name=editor_generic::long_name();
		if(!isset($this->path))$this->path=$this->keys['path'];
		if(!isset($this->path))die('Both editor_sql_immed::path and editor_sql_immed::keys[\'path\'] are not set');
		if(!isset($this->curr))
		{
			$parent_name=preg_replace('/\\.[^.]+$/','',$long_name);
			$this->ioclass=new $this->context[$parent_name]['ioclass'];
			$this->ioclass->context=&$this->context;
			$this->ioclass->oid=$this->oid;
			$this->ioclass->long_name=$parent_name;
			$this->obj=$this->ioclass->load();
			$this->curr=workers_container::find_by_path($this->path,$this->obj);
		}
		$this->args['limit_skip']=$this->curr->lim_offset;
		$this->args['limit_count']=$this->curr->lim_count;
		$this->context[$this->long_name]['result_div_id']=$this->result_div->id_gen();
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
			
			
			$this->context[$long_name.'.'.$i]['var']=$i;
			
		}
		$this->keys['path']=$this->path;
		
		foreach($this->editors as $e)$e->bootstrap();
		
	}
	
	function wrapped($cn,$field)
	{
		$curr=$this->curr;
		$path=$this->path;
		$args=$this->args;
		$long_name=$this->long_name;
		$htmlid=$this->context[$long_name]['htmlid'];
		$result_div_id=$this->result_div->id;
		$limit_skip_id=$this->editors['limit_skip']->ed->id;
		$limit_count_id=$this->editors['limit_count']->ed->id;
		//output worker
		//print $cn;
		$this->workers[$cn]->id_alloc();
		$this->workers[$cn]->path=$path.'/'.$field;
		$this->workers[$cn]->curr=&$curr->{$field};
		$this->workers[$cn]->bootstrap();
		$this->workers[$cn]->html();
		//output row tail
		$this->path=$path.'/'.$k;
		$this->context[$long_name]['htmlid']=$htmlid;
		//foreach($this->editors as $e)$e->bootstrap();
		//$this->cont_td->html();
		//restor possibly damaged fields
		$this->path=$path;
		$this->args=$args;
		$this->curr=$curr;
		$this->keys['path']=$this->path;
		$this->result_div->id=$result_div_id;
		$this->editors['limit_skip']->ed->id=$limit_skip_id;
		$this->editors['limit_count']->ed->id=$limit_count_id;
		foreach($this->editors as $e)$e->bootstrap();
		
		
	}
	
	function html_inner()
	{
		
		unset($this->custom_id);
		$curr=$this->curr;
		$path=$this->path;
		$long_name=$this->long_name;
		$htmlid=$this->context[$long_name]['htmlid'];
		$limit_add_sub=
		"var ed=\$i('".$this->editors['limit_skip']->ed->id_gen()."');".
		"var add=\$i('".$this->editors['limit_count']->ed->id_gen()."');".
		"var a=ed.value*1;".
		"var b=add.value*1;".
		"ed.focus();";
		$this->editors['limit_skip']->add_btn->attributes['onclick']=$limit_add_sub."ed.value=a+b;";
		$this->editors['limit_skip']->sub_btn->attributes['onclick']=$limit_add_sub."var c=a-b;if(c<0)ed.value=0; else ed.value=c;";
		$this->editors['tabs']->tabs['result']->selector->attributes['onclick']=$this->editors['result_button']->attributes['onclick'];
		
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		$oid=$ev->context[$ev->long_name]['oid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		
		$this->path=$ev->keys['path'];
		$io_name=($ev->rem_name != 'result_button')?preg_replace('/\\.[^.]+$/','',$ev->parent_name):$ev->parent_name;
		$io_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
		$ioclass=$ev->context[$io_name]['ioclass'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		//print "\n\n/* ".$customid." */\n\n";
		//$oid=$ev->context[$io_name]['oid'];
		//print 'oid='.$oid;
		$this->ioclass=new $ioclass;
		$this->ioclass->context=&$ev->context;
		$this->ioclass->oid=$oid;
		$this->ioclass->long_name=$io_name;
		$this->obj=$this->ioclass->load();
		$this->curr=workers_container::find_by_path($this->path,$this->obj);
		$reload=false;
		switch($ev->rem_name)
		{
		case 'clean':
			//$this->obj=;
			workers_container::change_by_path(new query_gen_ext,$this->path);
			$this->ioclass->save($this->obj);
			//print 'window.location.reload(true);';
			$reload=true;
			break;
		case 'limit_count':
			$this->curr->lim_count=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'limit_skip':
			$this->curr->lim_offset=$_POST['val'];
			$this->ioclass->save($this->obj);
			break;
		case 'result_button':
			$res=$this->obj->result();
			$result_div_id=$ev->context[$ev->parent_name]['result_div_id'];
			print "\$i('".$result_div_id."').innerHTML='".js_escape(htmlspecialchars($res))."';";
			break;
		}
		if($reload)
		{
			$r=new workers_container;
			//$r->obj=&$this->obj;// not required
			$r->path=&$this->path;
			$r->name=preg_replace('/\.[^.]+$/','',$ev->parent_name);
			$r->etype=preg_replace('/\.[^.]+$/','',$ev->parent_type);
			$r->context=$ev->context;
			$r->oid=$oid;
			
			$r->custom_id=$customid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=";
			reload_object($r,true);
			print "}catch(e){ window.location.reload(true);};";
		}
		editor_generic::handle_event($ev);
	}
	
}

class wrapper_sql_select extends dom_void
{
	function __construct($cb_obj,$cn,$field)
	{
		$this->cb_obj=$cb_obj;
		$this->cn=$cn;
		$this->field=$field;
	}
	function html()
	{
		$this->cb_obj->wrapped($this->cn,$this->field);
	}
}


















class editor_sql_infogen //maybe abstract class would be better... anyway...
{

}

class editor_sql_infoquery extends editor_sql_infogen
{


}






//--------------------------------------------------------------------------------------






?>