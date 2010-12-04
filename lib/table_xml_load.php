<?php
#########################################################################################
############################################ class table_csvdump_parser #################
#########################################################################################

class table_csvdump_parser
{
	//var $result = array();
	var $resParser;
	var $strXmlData;
		//$current,$path
	function __construct($file_name=NULL,$encoding='utf-8')
	{
		$this->file_name=$file_name;
		$this->encoding=$encoding;
		$this->table_create_query='';
		$this->table_stored_name='';
		$this->table_columns=Array();
		$this->table_keys=Array();
		$this->temp_row=Array();
		$this->fetch_query=NULL;
		$this->row_index=0;
		$this->tag='';
		$this->mode='struct';//name rows all
		$this->state=0;
		$this->row_object='';
		$this->row_method='';
		$this->mapping=null;
		$this->first_pass=false;
	}
	
	function __destruct()
	{

	}
	
	function run()
	{
		$fd=false;
		if(file_exists($this->file_name))
			$fd=fopen($this->file_name,'r');
		if($fd !== false)
		{
			$csv=new csv;
			while($str=fgets($fd))
			{
				if($this->encoding != 'utf-8')$str=iconv($this->encoding,'utf-8',$str);
				$values=$csv->split(trim($str));
				if(isset($this->_2d_mode))
				{
					if(!is_array($this->horizontal))
					{
						$this->horizontal=$values;
					}else{
						$_2d_count=0;
						$hor_ptr=0;
						unset($tmp);
						foreach($values as $v)
						{
							if($_2d_count<$this->_2d_mode)
							{
								$tmp['col'.$_2d_count]=$v;
								$_2d_count++;
								$hor_ptr++;
							}else{
								$tmp['col'.$_2d_count]=$this->horizontal[$hor_ptr];
								$hor_ptr++;
								$tmp['col'.($_2d_count+1)]=$v;
								if(($this->mode=='rows' || $this->mode=='all') && $v != '')
								{
									if($this->row_method=='')die("\$this->row_method==''");
									$method=$this->row_method;
									if($this->row_object!='')
										$this->stop=!$this->row_object->$method($tmp);
									else
										$this->stop=!$method($tmp);
								}
							}
							if($this->stop)return false;
						}
					}
				}else{
					if(!is_array($this->table_columns) || count($this->table_columns)==0)
					{
						$this->column_count=count($values);
						$this->first_pass=true;
						foreach($values as $i => $v)
							$this->table_columns['col'.$i]=Array('Field' => 'col'.$i);
							
					}else{
						if($this->column_count!=count($values))
						{
							$this->stop=true;
							return false;
						}
					}
					if($this->mode=='rows' || $this->mode=='all')
					{
						if($this->row_method=='')die("\$this->row_method==''");
						$cnt=0;
						foreach($this->table_columns as $c =>$v)
							$out[$c]=$values[$cnt++];
						$method=$this->row_method;
						if($this->row_object!='')
							$this->stop=!$this->row_object->$method($out);
						else
							$this->stop=!$method($out);
						unset($out);
					}
				}
				if($this->stop)break;
			}
			fclose($fd);
		}else return false;
		return true;
	}
	
}


#########################################################################################
############################################ class table_xmldump_parser #################
#########################################################################################

class table_xmldump_parser
{
	//var $result = array();
	var $resParser;
	var $strXmlData;
		//$current,$path
	function __construct($file_name=NULL)
	{
		$this->file_name=$file_name;
		$this->resParser = xml_parser_create ();
		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->resParser, "tagData");
		xml_parser_set_option ($this->resParser ,XML_OPTION_CASE_FOLDING , 0 );
		$this->table_create_query='';
		$this->table_stored_name='';
		$this->table_columns=Array();
		$this->table_keys=Array();
		$this->temp_row=Array();
		$this->fetch_query=NULL;
		$this->row_index=0;
		$this->tag='';
		$this->mode='struct';//name rows all
		$this->state=0;
		$this->row_object='';
		$this->row_method='';
		$this->mapping=null;
	}
	
	function __destruct()
	{
		xml_parser_free($this->resParser);
	}
	
	function run()
	{
		$fd=false;
		if(file_exists($this->file_name))
			$fd=fopen($this->file_name,'r');
		if($fd !== false)
		{
			while(true)
			{
				$rd=fread($fd,8192);
				if($rd==null)break;
				if(!$this->feed($rd,false))
				{
					return false;
				}
				if($this->stop)break;
			}
			if(!$this->stop)
			{
				if(!$this->feed($rd,true))
				{
					return false;
				}
			}
			fclose($fd);
		}else return false;
		return true;
	}
	
	function feed($strInputXML,$final=false)
	{
		if($this->stop)return;
		$this->strXmlData = xml_parse($this->resParser,$strInputXML ,$final);
		if(!$this->strXmlData)
		{
			$this->xml_error_string=xml_error_string(xml_get_error_code($this->resParser));
			xml_parse($this->resParser,"" ,true);
			$this->stop=true;
			return false;
		}
		return true;
	}
	
	function tagOpen($parser, $name, $attrs)
	{
		if($this->stop)return;
		$this->tag=$name;
		if($this->tag=='definition')
		{
			$this->table_stored_name=$attrs['name'];
			if($this->mode=='name')
			{
				$this->stop=true;
				return;
			}
		}
		if($this->tag=='rows')
		{
			if($this->mode=='struct')
			{
				$this->stop=true;
				if($this->stop)return;
			}
		}
		if($this->tag=='r')
		{
			$this->row_index=-1;
			$this->temp_row=Array();
		}

		if($this->tag=='create_table')$this->table_stored_name.=$tagData;
		if($this->tag=='column')$this->table_columns[$attrs['Field']]=$attrs;
		if($this->tag=='key')$this->table_keys[]=$attrs;
#		if($attrs['null']!=1)
		if($this->tag=='c' && isset($attrs['name']) && ! isset($this->table_columns[$attrs['name']]))
			$this->table_columns[$attrs['name']]=Array('Field' => $attrs['name']);
		if($this->tag=='c')
		{
			$this->row_index++;
		}
	}
	
	function tagData($parser, $tagData)
	{
		if($this->stop)return;
		if($this->tag=='create_table')$this->table_create_query.=$tagData;
		if($this->tag=='c')$this->temp_row[$this->row_index].=$tagData;
	}
	
	function tagClosed($parser, $name)
	{
		if($this->stop)return;
		if($name=='r')
		{
			if($this->mode=='rows' || $this->mode=='all')
			{
				if($this->row_method=='')die("\$this->row_method==''");
				$cnt=0;
				foreach($this->table_columns as $c =>$v)
					$out[$c]=$this->temp_row[$cnt++];
				$method=$this->row_method;
				if($this->row_object!='')
					$this->stop=!$this->row_object->$method($out);
				else
					$this->stop=!$method($out);
				unset($out);
			}
			$this->temp_row=Array();
		}
		unset($this->tag);
	}
}


#########################################################################################
############################################ class table_xml_load_ui#####################
#########################################################################################
class table_xml_load_ui extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		$this->error_text=new dom_any('span');
		$this->append_child($this->error_text);
		
		$atbl=new container_autotable;
		$this->append_child($atbl);
		editor_generic::addeditor('file_picker',new file_pick_or_upload);
		$atbl->append_child($this->editors['file_picker']);
		editor_generic::addeditor('ed_table',new editor_text_autosuggest_query);
		$atbl->append_child($this->editors['ed_table']);
		editor_generic::addeditor('ed_horizontal',new editor_text);
		$atbl->append_child($this->editors['ed_horizontal']);
		editor_generic::addeditor('ed_csv_encoding',new editor_txtasg_encodings);
		$atbl->append_child($this->editors['ed_csv_encoding']);
		
		
		editor_generic::addeditor('load_controls',new table_xml_load_ui_controls);
		$this->append_child($this->editors['load_controls']);
		
		editor_generic::addeditor('pager',new util_small_pager);
		$this->append_child($this->editors['pager']);
		
		$this->file_contents=new table_xml_load_ui_contents;
		$this->append_child($this->file_contents);
		editor_generic::addeditor('file_contents',$this->file_contents);
		
		$this->errmsg=new dom_statictext;
		$this->append_child($this->errmsg);
		
		editor_generic::addeditor('clear_accept',new editor_button);
		$this->editors['clear_accept']->attributes['value']="X&>>";
		$this->append_child($this->editors['clear_accept']);
		editor_generic::addeditor('accept',new editor_button);
		$this->editors['accept']->attributes['value']=">>";
		$this->append_child($this->editors['accept']);
		$this->settings_list=Array(
			'file_picker'	=> '!txlufile',
			'ed_table'		=> '!txlutbl',
			'ed_csv_encoding'		=> '!txluenc',
			'mapping_val'	=> '!txlumapping',
			'search_table_val'	=> '!txlustbl',
			'select_val'	=> '!txlusel',
			'search_val'	=> '!txlusearch',
			'dict_val'		=> '!txludict',
			'initial_val'	=> '!txluinitial',
			'ed_horizontal'	=> '!txluhorizontal',
			'ed_offset'		=> '!txlupgoffset',
			'ed_count'		=> '!txlupgcount'
			);
		$this->settings_type=Array(
			'file_picker'		=> 'r',
			'ed_table'			=> 'r',
			'ed_csv_encoding'	=> 'r',
			'mapping_val'		=> 's',
			'search_table_val'	=> 's',
			'select_val'		=> 's',
			'search_val'		=> 's',
			'dict_val'			=> 's',
			'initial_val'		=> 's',
			'ed_horizontal'		=> 'r',
			'ed_offset'			=> 'r',
			'ed_count'			=> 'r'
			);
		$this->settings_ed=Array(
			'mapping_val'	=> 'ed_map',
			'search_table_val'	=> 'ed_search_tbl',
			'select_val'	=> 'ed_select',
			'search_val'	=> 'ed_search',
			'dict_val'		=> 'ed_dict',
			'initial_val'	=> 'ed_initial',
			'ed_horizontal'	=> 'ed_horizontal',
			'ed_offset'		=> 'ed_offset',
			'ed_count'		=> 'ed_count'
			);
			
		
	}
	
	function bootstrap()
	{
		$this->args=Array();
		$this->keys=Array();
		$this->long_name=editor_generic::long_name();
		//$this->oid=-1;
		$this->context[$this->long_name.'.ed_table']['rawquery']='SHOW TABLES';
		$this->context[$this->long_name.'.ed_table']['var']='ed_table';
		$this->context[$this->long_name.'.ed_csv_encoding']['var']='ed_csv_encoding';
		$this->context[$this->long_name.'.ed_horizontal']['var']='ed_horizontal';
		$this->context[$this->long_name.'.pager.ed_offset']['var']='ed_offset';
		$this->context[$this->long_name.'.pager.ed_count']['var']='ed_count';
		$this->context[$this->long_name]['file_contents_id']=$this->editors['file_contents']->id_gen();
		$this->context[$this->long_name]['load_controls_id']=$this->editors['load_controls']->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['error_text']=$this->error_text->id_gen();
		$this->context[$this->long_name.'.file_contents']['var']='file_picker';
		$this->context[$this->long_name.'.file_picker']['var']='file_picker';
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
		foreach($this->settings_list as $i => $v)
			$this->args[$i]=($this->settings_type[$i]=='s')
			?
				unserialize($this->rootnode->setting_val($this->oid,$this->long_name.$v,''))
			:
				$this->rootnode->setting_val($this->oid,$this->long_name.$v,'');
//		if($this->args['ed_csv_encoding']=='')$this->args['ed_csv_encoding']='utf-8';
		$this->editors['clear_accept']->attributes['onclick']="if(confirm('Table contents will be deleted!')){".
			$this->editors['clear_accept']->attributes['onclick']."};";
		parent::html_inner();
	}
	
	function do_accept($file,$table,$mapping,$initial,$search_tbl,$select,$search,$dict,$parser,$_2d_mode,$enc)
	{
		global $sql;
		set_time_limit(60*20);//////////////////////////////////////WARNING
		$this->faillog=fopen($_SERVER['DOCUMENT_ROOT'].'/log/load_'.date("YmdHi").'.csv','w');
		$sql->logquerys=false;
		if(!isset($this->qg))
		{
			$res=$sql->query("show columns from `".$sql->esc($table)."`");
			while($ros=$sql->fetcha($res))
				$validcolumns[]=$ros['Field'];
			$this->qg=new query_gen_ext('INSERT UPDATE');
			$this->qg->into->exprs[]=new sql_column(NULL,$table);
			if(is_array($mapping))
			{
				foreach($mapping as $to =>$from)
				if(in_array($to,$validcolumns))
				{
					$this->qg_consts[$from]=new sql_immed();
					$expr=new sql_expression('=');
					$expr->exprs[]=new sql_column(NULL,NULL,$to);
					if($search_tbl[$to]!='')
					{
						$subq=new sql_subquery;
						$qg=new query_gen_ext('SELECT');
						$qg->from->exprs[]=new sql_column(NULL,$search_tbl[$to],NULL);
						$qg->what->exprs[]=new sql_column(NULL,NULL,$select[$to],'id');
						$qg->where->exprs[]=new sql_expression('OR',Array(
							new sql_expression('=',Array(
								new sql_column(NULL,NULL,$search[$to]),
								$this->qg_consts[$from]
								)),
							new sql_expression('=',Array(
								new sql_column(NULL,NULL,$dict[$to]),
								$this->qg_consts[$from]
								))
							));
						$subq->subquery=$qg;
						$expr->exprs[]=$subq;
						unset($subq);unset($qg);
					}else{
						$expr->exprs[]=$this->qg_consts[$from];
					};
					$this->qg->set->exprs[]=$expr;
				}
				if(is_array($initial))
				foreach($initial as $to =>$from)
					if(!isset($this->qg_consts[$to]) && in_array($to,$validcolumns))
					{
						$expr=new sql_expression('=',Array(
							new sql_column(NULL,NULL,$to),
							new sql_immed($from))
							);
						$this->qg->set->exprs[]=$expr;
					}
			
			}else{
/*				foreach($this->table_columns as $c =>$v)
				{
					if(!is_object($this->qg_consts[$c]))$this->qg_consts[$c]=new sql_immed();
					$expr=new sql_expression('=',Array(
						new sql_column(NULL,NULL,$c),
						$this->qg_consts[$c])
						);
					$this->qg->set->exprs[]=$expr;
				}*/
				unset($this->qg);
			}
		};
		if(isset($this->qg))
		{
			$f=$file;
			if(preg_match('/\\//',$f))$f=preg_replace('/^.*\\//','',$f);
			$doc_root=$_SERVER['DOCUMENT_ROOT'];
			if(preg_match('#.*[^/]$#',$doc_root))$doc_root.='/';
			$f=$doc_root.'uploads/'.$f;
			$xload=new $parser($f);
			if($parser=='table_csvdump_parser')$xload->encoding=$enc;
			$xload->row_object=$this;
			$xload->row_method='out_row';
			$xload->mode='rows';
 			if($parser=='table_csvdump_parser' && intval($_2d_mode)>0)
				$xload->_2d_mode=intval($_2d_mode);
			$this->row_ok=0;
			$this->row_failed=0;
			$xload->run();
			unset($xload);
		}
		fclose($this->faillog);
	}
	
	function out_row($row)
	{
		global $sql;
		foreach($this->qg_consts as $i => $v)
			$this->qg_consts[$i]->val=$row[$i];
		$res=$sql->query($this->qg->result());
		if($res===true)$this->row_ok++;
		else{
			$this->row_failed++;
			if($this->faillog)fwrite($this->faillog,implode(',',$row)."\n");
		}
		return true;
	}
	

	function evaluate_simple_mapping($value,$search_tbl,$select,$search,$dict)
	{
		global $sql;
		$qg=new query_gen_ext('SELECT');
		$qg->from->exprs[]=new sql_column(NULL,$search_tbl,NULL);
		$qg->what->exprs[]=new sql_column(NULL,NULL,$select,'id');
		$qg->where->exprs[]=new sql_expression('OR',Array(
			new sql_expression('=',Array(
				new sql_column(NULL,NULL,$search),
				new sql_immed($value)
				)),
			new sql_expression('=',Array(
				new sql_column(NULL,NULL,$dict),
				new sql_immed($value)
				))
			));
		return $sql->fetch1($sql->query($qg->result()));
			
	}
	
	function update_dectionary($v,$id,$search_tbl,$select,$dict)
	{
		global $sql;
		$qg=new query_gen_ext('UPDATE');
		$qg->into->exprs[]=new sql_column(NULL,$search_tbl,NULL);
		$qg->set->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,$dict),
			new sql_immed('')
			));
		$qg->where->exprs[]=
			new sql_expression('=',Array(
				new sql_column(NULL,NULL,$dict),
				new sql_immed($v)
			));
		$sql->query($qg->result());
		unset($qg);
		$qg=new query_gen_ext('INSERT UPDATE');
		$qg->into->exprs[]=new sql_column(NULL,$search_tbl,NULL);
		$qg->set->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,$dict),
			new sql_immed($v)
			));
		$qg->set->exprs[]=
			new sql_expression('=',Array(
				new sql_column(NULL,NULL,$select),
				new sql_immed($id)
			));
		return $sql->query($qg->result());
	}
	
	
	function handle_event($ev)
	{
		global $sql;
		$reload_list=false;
		$oid=$ev->context[$ev->parent_name]['oid'];
		$dbg=$ev->context[$ev->parent_name]['dbg'];
		#$customid=$ev->context[$ev->parent_name]['htmlid'];
		$setting_tool=new settings_tool;
		foreach($this->settings_list as $i => $v)
			$_val[$i]=($this->settings_type[$i]=='s')
			?
				unserialize($sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.$v,$_SESSION['uid'],0))))
			:
				$sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.$v,$_SESSION['uid'],0)));
		$parser=$sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.'.file_contents!txluparser',$_SESSION['uid'],0)));
		
		switch($ev->rem_name)
		{
		case 'file_picker':
				$_val['file_picker']=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txlufile',$_SESSION['uid'],0,$_val['file_picker']));
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txlumapping',$_SESSION['uid'],0,serialize(Array())));
				$_val['mapping_val']=Array();
				$reload_list=true;
				$reload_controls=$reload_list;
				break;
		case 'ed_table':
				$reload_list=($_val['ed_table']!=$_POST['val']);
				$reload_controls=$reload_list;
				$_val['ed_table']=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txlutbl',$_SESSION['uid'],0,$_val['ed_table']));
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txlumapping',$_SESSION['uid'],0,serialize(Array())));
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txluinitial',$_SESSION['uid'],0,serialize(Array())));
				break;
		case 'ed_csv_encoding':
				$reload_list=true;
				$reload_controls=$reload_list;
				$_val['ed_csv_encoding']=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txluenc',$_SESSION['uid'],0,$_val['ed_csv_encoding']));
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txlumapping',$_SESSION['uid'],0,serialize(Array())));
				$_val['mapping_val']=Array();
				break;
		case 'ed_horizontal':
				$reload_list=($_val['ed_horizontal']!=$_POST['val']);
				$reload_controls=$reload_list;
				$_val['ed_horizontal']=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txluhorizontal',$_SESSION['uid'],0,$_val['ed_horizontal']));
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txlumapping',$_SESSION['uid'],0,serialize(Array())));
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txluinitial',$_SESSION['uid'],0,serialize(Array())));
				/*
				print "\$i('".$ev->context[$ev->parent_name]['error_text']."').innerHTML='".js_escape(htmlspecialchars(
					$_val['ed_horizontal']
				))."';";
				*/
				break;
		case 'pager.ed_offset':
				$reload_list=($_val['ed_offset']!=$_POST['val']);
				$_val['ed_offset']=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txlupgoffset',$_SESSION['uid'],0,$_val['ed_offset']));
				break;
		case 'pager.ed_count':
				$reload_list=($_val['ed_count']!=$_POST['val']);
				$_val['ed_count']=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!txlupgcount',$_SESSION['uid'],0,$_val['ed_count']));
				break;
		case 'clear_accept':
				$clear_result=$sql->query("DELETE FROM `".$sql->esc($_val['ed_table'])."`");
		case 'accept':
				$this->do_accept(
					$_val['file_picker'],
					$_val['ed_table'],
					$_val['mapping_val'],
					$_val['initial_val'],
					$_val['search_table_val'],
					$_val['select_val'],
					$_val['search_val'],
					$_val['dict_val'],
					$parser,
					$_val['ed_horizontal'],
					$_val['ed_csv_encoding']
					);
				#$reload_list=true;
				if(is_object($this->qg))print "alert('".js_escape($this->qg->result().";\nok=".$this->row_ok.";\nfailed=".$this->row_failed.";\nsql time=".$sql->querytime)."');";
				break;
		case 'load_controls.ed_search_tbl':
				$ev->context[$ev->long_name]['rawquery']="SHOW TABLES";
				break;
		case 'load_controls.default_map':
		case 'load_controls.reset_map':
		case 'load_controls.direct_map':
				$reload_controls=true;
				$refetch=true;
				break;
		case 'load_controls.ed_select':
		case 'load_controls.ed_search':
		case 'load_controls.ed_dict':
				$ev->context[$ev->long_name]['rawquery']="SHOW COLUMNS FROM `".$sql->esc($_val['search_table_val'][$ev->keys['pos']])."`";
				break;
		case 'file_contents.m_suggestion':
				$res=$this->update_dectionary($ev->keys['v'],$_POST['val'],$_val['search_table_val'][$ev->keys['to']],$_val['select_val'][$ev->keys['to']],$_val['dict_val'][$ev->keys['to']]);
				break;
		default:
		}
		foreach($this->settings_list as $i => $v)
			if(isset($this->settings_ed[$i]) && $ev->rem_name=='load_controls.'.$this->settings_ed[$i])
			{
				#print "\$i('".$ev->context[$ev->parent_name]['error_text']."').innerHTML='".js_escape(htmlspecialchars($_POST['val'].";".$ev->keys['pos'].";".$i.";".$v))."';";
				#print "alert('".$ev->rem_name.";".$i."');";
				$reload_list=($_val[$i][$ev->keys['pos']]!=$_POST['val'] && in_array($ev->rem_name,
					Array(
						'load_controls.ed_map',
						'load_controls.ed_search_tbl',
						'load_controls.ed_select',
						'load_controls.ed_search',
						'load_controls.ed_dict',
						))
					);
				$_val[$i][$ev->keys['pos']]=$_POST['val'];
				$sql->query($setting_tool->set_query($oid,$ev->parent_name.$v,$_SESSION['uid'],0,serialize($_val[$i])));
				
			};
		
		editor_generic::handle_event(clone $ev);
		if($refetch)
		{
		foreach($this->settings_list as $i => $v)
			$_val[$i]=($this->settings_type[$i]=='s')
			?
				unserialize($sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.$v,$_SESSION['uid'],0))))
			:
				$sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.$v,$_SESSION['uid'],0)));
		}
		if($reload_list)
		{
			
			foreach($this->settings_list as $i => $v)
				$this->args[$i]=$_val[$i];
			$customid=$ev->context[$ev->parent_name]['file_contents_id'];
			$oid=$ev->context[$ev->parent_name]['oid'];
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$r=new table_xml_load_ui_contents;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".file_contents";
			$r->etype=$ev->parent_type.".table_xml_load_ui_contents";

			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
			//common part
		}
		if($reload_controls)
		{
			
			if(!$reload_list)
				foreach($this->settings_list as $i => $v)
					$this->args[$i]=$_val[$i];
			$customid=$ev->context[$ev->parent_name]['load_controls_id'];
			$oid=$ev->context[$ev->parent_name]['oid'];
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$r=new table_xml_load_ui_controls;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".load_controls";
			$r->etype=$ev->parent_type.".table_xml_load_ui_controls";

			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
			//common part
		}
	}
}

class editor_txtasg_encodings extends editor_txtasg
{
	function fetch_list($ev,$k=NULL)
	{
		return Array(
			Array('val'=>'utf-8'),
			Array('val'=>'cp1251'),
			);
	}

}

#############################################################################################################
#############################################################################################################
#############################################################################################################

class table_xml_load_ui_contents extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		$this->detection=new dom_div;
		$this->detection_text=new dom_statictext;
		$this->append_child($this->detection);
		$this->detection->append_child($this->detection_text);
		
		$this->tbl=new dom_table;
		
		$this->tr=new dom_tr;
		$this->td=new dom_td;
		unset($this->tr->id);
		unset($this->td->id);
		$this->tbl->append_child($this->tr);
		$this->tr->append_child($this->td);
		editor_generic::addeditor('txt',new editor_statictext);
		$this->td->append_child($this->editors['txt']);
		
		$this->td_map=new dom_td;
		unset($this->td_map->id);
		$this->tr->append_child($this->td_map);
		editor_generic::addeditor('m_txt',new editor_statictext);
		$this->td_map->append_child($this->editors['m_txt']);
		editor_generic::addeditor('m_suggestion',new editor_search_pick);
		$this->td_map->append_child($this->editors['m_suggestion']);
		$this->editors['m_suggestion']->io=new editor_search_pick_txl_io;
		
		
		
		
		$this->append_child($this->tbl);
		$this->rows_skipped=0;
		$this->rows_out=0;
	}
	
	
	function bootstrap()
	{
		$this->keys=Array('pos' => 0);
		$this->long_name=editor_generic::long_name();
		foreach($this->editors as $i => $e)
		{
			$e->oid=$this->oid;
			$e->keys=&$this->keys;
			$e->context=&$this->context;
			$e->args=&$this->args;
			$this->context[$this->long_name.".".$i]['var']=$i;
		};
		foreach($this->editors as $e)
			$e->bootstrap();
	}
	
	function out_row($row)
	{
#		return true;
		if(!isset($this->first_row))
		{
			#names
			$this->tr->html_head();
			foreach($row as $i => $v)
			{
				$this->args['txt']=$i;
				$this->src_rows[$i]=$i;
				$this->td->html();
			}
			$this->tr->html_tail();
			$this->first_row=true;
		}
		if($this->rows_skipped<$this->args['ed_offset'])
		{
			$this->rows_skipped++;
			return true;
		}
		if($this->rows_out>=$this->args['ed_count'])
		{
			return false;
		}else{
			$this->rows_out++;
		}
#		$this->rootnode->out("<tr>");
		$this->tr->html_head();
		foreach($row as $i => $v)
		{
#			$this->rootnode->out("<td>".htmlspecialchars($v)."</td>");
			$this->args['txt']=$v;
			$hasout=false;
			foreach($this->args['mapping_val'] as $to => $from)
				if($from==$i && $this->args['search_table_val'][$to]!='')
				{
					$this->args['m_txt']=$v;
				#print_r($this->args);
					$this->keys['pos']=$i;
					$this->keys['to']=$to;
					$this->keys['v']=$v;
					$this->args['m_suggestion']=
						table_xml_load_ui::evaluate_simple_mapping(
							$v,
							$this->args['search_table_val'][$to],
							$this->args['select_val'][$to],
							$this->args['search_val'][$to],
							$this->args['dict_val'][$to]
						);
					$this->editors['m_suggestion']->id_alloc();
					$this->editors['m_suggestion']->bootstrap();
					
					$this->td_map->html();
					$hasout=true;
				}
				if(!$hasout)$this->td->html();
		}
		$this->tr->html_tail();
#		$this->rootnode->out("</tr>");
		return true;
	}
	
	function html_inner()
	{
		
		global $sql;
		$oid=$this->oid;
		$setting_tool=new settings_tool;
		$fd=false;
		$this->mapper=$this->args['file_picker'];
		#WAARNING! this context is passed ONLY to bootstrapped in parser callback children
		$this->context[$this->long_name.'.m_suggestion']['search_table_val']=$this->args['search_table_val'];
		$this->context[$this->long_name.'.m_suggestion']['mapping_val']=$this->args['mapping_val'];
		$this->context[$this->long_name.'.m_suggestion']['select_val']=$this->args['select_val'];
		$this->context[$this->long_name.'.m_suggestion']['search_val']=$this->args['search_val'];
		$f=$this->args['file_picker'];
		if(preg_match('/\\//',$f))$f=preg_replace('/^.*\\//','',$f);
		$doc_root=$_SERVER['DOCUMENT_ROOT'];
		if(preg_match('#.*[^/]$#',$doc_root))$doc_root.='/';
		$f=$doc_root.'uploads/'.$f;
		$this->detection_text->text='not detected';
		$this->tbl->html_head();
		
		$xload=new table_xmldump_parser($f);
		$xload->row_object=$this;
		$xload->row_method='out_row';
		$xload->mode='rows';
		$is_valid=$xload->run();
		unset($xload);
		if(!$is_valid)
		{
			$xload=new table_csvdump_parser($f,$this->args['ed_csv_encoding']);
			$xload->row_object=$this;
			$xload->row_method='out_row';
			$xload->mode='rows';
			if(intval($this->args['ed_horizontal'])>0)$xload->_2d_mode=intval($this->args['ed_horizontal']);
			$is_valid=$xload->run();
			if($is_valid)
				$this->detection_text->text='csv detected';
			unset($xload);
			$sql->query($setting_tool->set_query($oid,$this->long_name.'!txluparser',$_SESSION['uid'],0,'table_csvdump_parser'));
		}else{
				$this->detection_text->text='xml detected';
				$sql->query($setting_tool->set_query($oid,$this->long_name.'!txluparser',$_SESSION['uid'],0,'table_xmldump_parser'));
		}
		$this->tbl->html_tail();
		$this->detection_text->text=$f.' in '.$this->args['ed_csv_encoding']." : ".$this->detection_text->text;
		$this->detection->html();
	}
	
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}


#############################################################################################
################################table_xml_load_ui_controls
#############################################################################################

class table_xml_load_ui_controls extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		$this->initial=new dom_table;
		$this->append_child($this->initial);
		$this->tr_i=new dom_tr;
		unset($this->tr_i->id);
		$this->initial->append_child($this->tr_i);
		
		$this->td_i=new dom_td;unset($this->td_i->id);$this->tr_i->append_child($this->td_i);
		editor_generic::addeditor('lb_initial',new editor_statictext);
		$this->td_i->append_child($this->editors['lb_initial']);
		
		$this->td_i=new dom_td;unset($this->td_i->id);$this->tr_i->append_child($this->td_i);
		editor_generic::addeditor('ed_initial',new editor_text);
		$this->td_i->append_child($this->editors['ed_initial']);
		
		$this->td_i=new dom_td;unset($this->td_i->id);$this->tr_i->append_child($this->td_i);
		editor_generic::addeditor('ed_map',new editor_select);
		$this->td_i->append_child($this->editors['ed_map']);
		
		$this->td_i=new dom_td;unset($this->td_i->id);$this->tr_i->append_child($this->td_i);
		editor_generic::addeditor('ed_search_tbl',new editor_text_autosuggest_query);
		$this->td_i->append_child($this->editors['ed_search_tbl']);
		
		$this->td_i=new dom_td;unset($this->td_i->id);$this->tr_i->append_child($this->td_i);
		editor_generic::addeditor('ed_select',new editor_text_autosuggest_query);
		$this->td_i->append_child($this->editors['ed_select']);
		
		$this->td_i=new dom_td;unset($this->td_i->id);$this->tr_i->append_child($this->td_i);
		editor_generic::addeditor('ed_search',new editor_text_autosuggest_query);
		$this->td_i->append_child($this->editors['ed_search']);
		
		$this->td_i=new dom_td;unset($this->td_i->id);$this->tr_i->append_child($this->td_i);
		editor_generic::addeditor('ed_dict',new editor_text_autosuggest_query);
		$this->td_i->append_child($this->editors['ed_dict']);
		editor_generic::addeditor('default_map',new editor_button);
		$this->append_child($this->editors['default_map']);
		$this->editors['default_map']->attributes['value']='Default map';
		
		editor_generic::addeditor('direct_map',new editor_button);
		$this->append_child($this->editors['direct_map']);
		$this->editors['direct_map']->attributes['value']='Direct map';
		
		editor_generic::addeditor('reset_map',new editor_button);
		$this->append_child($this->editors['reset_map']);
		$this->editors['reset_map']->attributes['value']='Reset map';
	}
	
	
	function bootstrap()
	{
		$this->keys=Array('pos' => 0);
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		foreach($this->editors as $i => $e)
		{
			$e->oid=$this->oid;
			$e->keys=&$this->keys;
			$e->context=&$this->context;
			$e->args=&$this->args;
			$this->context[$this->long_name.".".$i]['var']=$i;
		};
		foreach($this->editors as $e)
			$e->bootstrap();
	}
	
	function out_row($row)
	{
		if(!isset($this->first_row))
		{
			#names
			$this->editors['ed_map']->options[""]="";
			foreach($row as $i => $v)
			{
				$this->editors['ed_map']->options[$i]=$i;
				$this->src_rows[$i]=$i;
			}
			$this->first_row=true;
			return true;
		}else{
			return false;
		}
	}
	
	function html_inner()
	{
		
		global $sql;
		$oid=$this->oid;
		$setting_tool=new settings_tool;
		if(isset($this->args['ed_table']))
		{
			global $sql;
			$res=$sql->query("SHOW COLUMNS FROM `".$sql->esc($this->args['ed_table'])."`");
			while($row=$sql->fetcha($res))
			{
				$this->collist[$row['Field']]=$this->args['initial_val'][$row['Field']];
			}
		}
		$fd=false;
		$this->mapper=$this->args['file_picker'];
		$f=$this->args['file_picker'];
		if(preg_match('/\\//',$f))$f=preg_replace('/^.*\\//','',$f);
		$doc_root=$_SERVER['DOCUMENT_ROOT'];
		if(preg_match('#.*[^/]$#',$doc_root))$doc_root.='/';
		$f=$doc_root.'uploads/'.$f;
		
		$xload=new table_xmldump_parser($f);
		$xload->row_object=$this;
		$xload->row_method='out_row';
		$xload->mode='rows';
		$is_valid=$xload->run();
		unset($xload);
		if(!$is_valid)
		{
			$xload=new table_csvdump_parser($f,$this->args['ed_csv_encoding']);
			$xload->row_object=$this;
			$xload->row_method='out_row';
			$xload->mode='rows';
			if(intval($this->args['ed_horizontal'])>0)$xload->_2d_mode=intval($this->args['ed_horizontal']);
			$is_valid=$xload->run();
			unset($xload);
			$sql->query($setting_tool->set_query($oid,$this->long_name.'!txluparser',$_SESSION['uid'],0,'table_csvdump_parser'));
		}else{
				$sql->query($setting_tool->set_query($oid,$this->long_name.'!txluparser',$_SESSION['uid'],0,'table_xmldump_parser'));
		}
		$this->initial->html_head();
		if(is_array($this->collist))foreach($this->collist as $i=>$v)
		{
				$this->args['ed_initial']=$v;
				$this->args['lb_initial']=$i;
				$this->args['ed_search_tbl']=$this->args['search_table_val'][$i];
				$this->args['ed_map']=$this->args['mapping_val'][$i];
				$this->args['ed_select']=$this->args['select_val'][$i];
				$this->args['ed_search']=$this->args['search_val'][$i];
				$this->args['ed_dict']=$this->args['dict_val'][$i];
				$this->tr_i->id_alloc();
				$this->keys['pos']=$i;
				foreach($this->editors as $e)
					$e->bootstrap();
				$this->tr_i->html();
		};
		$this->initial->html_tail();
		foreach(Array('default_map','direct_map','reset_map') as $ed)
		{
			$this->context[$this->long_name.'.'.$ed]['collist']=$this->collist;
			$this->context[$this->long_name.'.'.$ed]['src_rows']=$this->src_rows;
			$this->context[$this->long_name.'.'.$ed]['self_id']=$this->id_gen();
			$this->editors[$ed]->bootstrap();
			$this->editors[$ed]->html();
		}
	}
	
	
	function handle_event($ev)
	{
		global $sql;
		$oid=$ev->context[$ev->parent_name]['oid'];
		switch($ev->rem_name)
		{
		case 'default_map':
		case 'reset_map':
		case 'direct_map':
			$parent_name=preg_replace('/\\.[^.]+$/','',$ev->parent_name);
			$setting_tool=new settings_tool;
			$mapping=unserialize($sql->fetch1($sql->query($setting_tool->single_query($oid,$parent_name.'!txlumapping',$_SESSION['uid'],0))));
			$collist=$ev->context[$ev->long_name]['collist'];
			$self_id=$ev->context[$ev->long_name]['self_id'];
			$src_rows=$ev->context[$ev->long_name]['src_rows'];
			$cnt=0;
			foreach($collist as $r => $v)
				$c_i[$cnt++]=$r;
			$c2=0;
			foreach($src_rows as $r)
			{
				switch($ev->rem_name)
				{
				case 'default_map':
					$mapping[$c_i[$c2++]]=$r;
					break;
				case 'reset_map':
					unset($mapping[$c_i[$c2++]]);
					break;
				case 'direct_map':
					if(isset($src_rows[$c_i[$c2]]))
						$mapping[$c_i[$c2]]=$c_i[$c2];
					else
						unset($mapping[$c_i[$c2]]);
					$c2++;
					break;
				}
			}
			unset($mapping['']);
			$sql->query($setting_tool->set_query($oid,$parent_name.'!txlumapping',$_SESSION['uid'],0,serialize($mapping)));
			
			
			break;
		}
		editor_generic::handle_event($ev);
	}
}

##########################################################################################################################################
##########################################################################################################################################
##########################################################################################################################################

class editor_search_pick_txl_io
{
	function __construct()
	{
		global $sql;
		$this->sql=&$sql;
		
	}
	
	function get_list($obj)
	{
		#print_r($obj->context[$obj->name]);print_r($obj->keys);
		$this->qq=new query_gen_ext('SELECT');
		$this->qq->from->exprs[]=new sql_column(NULL,$obj->context[$obj->long_name]['search_table_val'][$obj->keys['to']]);
		$this->qq->what->exprs[]=new sql_column(NULL,NULL,$obj->context[$obj->long_name]['select_val'][$obj->keys['to']]);
		$this->qq->what->exprs[]=new sql_column(NULL,NULL,$obj->context[$obj->long_name]['search_val'][$obj->keys['to']]);
		$this->qq->lim_count=9;
		$qq=$this->qq;
		if($this->res)$this->sql->free($this->res);
		unset($this->res);
		$fltr=preg_replace('/  +/',' ',$obj->filter_val);
		$fltr=preg_replace('/%/','\\%',$fltr);
		$fltr=preg_replace('/^ /','',$fltr);
		$fltr=preg_replace('/ $/','',$fltr);
		$fltr=preg_replace('/_/','\\_',$fltr);
		$fltr=preg_replace('/ /','%',$fltr);
		$fltr='%'.$fltr.'%';
		if($fltr != '')$qq->where->exprs[]=new sql_expression('LIKE',Array(
			new sql_column(NULL,NULL,$obj->context[$obj->long_name]['search_val'][$obj->keys['to']]),
			new sql_immed($fltr)
			));
		$qq->lim_offset=$this->qq->lim_count*$this->page_offset;
		$this->res=$this->sql->query($qq->result());
		return;
	}
	
	function next()
	{
		if($this->res)
		{
			//print 'xxxxx';
			if($r= $this->sql->fetchn($this->res))
				return $r;
			else return NULL;
		}
		return NULL;
		
		if($this->cnt)
		$this->cnt--;
		if($this->cnt)
			return each($this->res);
		else
			return NULL;
	}
	
	
	function to_hr($obj,$v)
	{
		$this->qq=new query_gen_ext('SELECT');
		$this->qq->from->exprs[]=new sql_column(NULL,$obj->context[$obj->long_name]['search_table_val'][$obj->keys['to']]);
		$this->qq->what->exprs[]=new sql_column(NULL,NULL,$obj->context[$obj->long_name]['select_val'][$obj->keys['to']]);
		$this->qq->what->exprs[]=new sql_column(NULL,NULL,$obj->context[$obj->long_name]['search_val'][$obj->keys['to']]);
		$qq=$this->qq;
		//print $qq->result();
		$qq->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,$obj->context[$obj->long_name]['select_val'][$obj->keys['to']]),
			new sql_immed($v)
			));
		$res=$this->sql->query($qq->result());
		if($res)
		{
			$r=$this->sql->fetchn($res);
			$this->sql->free($res);
			return $r[1];
		} else return '';
	}
}



$tests_m_array['util']['table_xml_load_ui']='table_xml_load_ui';






?>