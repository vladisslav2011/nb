<?php

require_once('lib/xml2any.php');


class test_xml_viewer extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype=get_class($this);
		$this->txt=new dom_statictext;
		$this->append_child($this->txt);
		$this->xml=new xml2Array;
		
	}
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->attributes['title']=$this->long_name;
	}
	
	
	function html_inner()
	{
		//$v=file_get_contents('../greport3.xml');
//		$v=file_get_contents('../xml/xls.xml');
//		$handle = fopen("../xml/LT.fods", "r");
//		$handle = fopen("../xml/greport3.xml", "r");
//		$handle = fopen("../xml/xls.xml", "r");
		$handle = fopen("../xml/ДОХОД_2НДФЛ_6025_6025026876602501001_20090226_CBED40B3-78D3-4B2A-BD21-AEC68E1837DF.xml", "r");
		//print "<pre>".htmlspecialchars($v)."</pre>";
		if($handle)while(!feof($handle))
		{
			//$v=fread($handle,1);
			//if(ord($v)<ord(' '))$v=' ';
			//read 16K
			$v=fread($handle,16384);
			//replace invalid chars with whitespace
			$v=preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/',' ',$v);
			if(feof($handle))
				$this->xml->feed($v,true);
			else
				$this->xml->feed($v,false);
		}
		fclose($handle);
		
		$this->xml->result->dump_o($this,'dd');
		file_put_contents("../xml/greport3-out.xml",$this->t);
		
		$this->xml->result->print_html();
		parent::html_inner();
	}
	function dd($v)
	{
		$this->t.=$v;
	}
}

class test_xml_viewer_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype=get_class($this);
		editor_generic::addeditor('qw',new test_xml_viewer);
		$this->append_child($this->editors['qw']);
		
	}
	
	function setup()
	{
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


$tests_m_array['complex']['test_xml_viewer_test']='test_xml_viewer_test';
#--------------------------------------------------------------------------------------------------------------


	//$tbl(id,nodeid,parent,nodename,textvalue)
	//$atbl(id,nodeid,attrname,textvalue)




$ddc_tables['xml_editor_nodes']=(object)
Array(
 'name' => 'xml_editor_nodes',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Ид'),
  Array('name' =>'nodeid',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'parent',		'sql_type' =>'int(10)',  'sql_null' =>1, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'nodename',	'sql_type' =>'varchar(200)', 'sql_null' =>1, 'sql_default' =>NULL,	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'узел'),
  Array('name' =>'textvalue',	'sql_type' =>'mediumtext', 'sql_null' =>1, 'sql_default' =>NULL,	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Текст')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'nodeid', 'sub' => NULL),
  Array('key' =>'parent', 'name' =>'parent', 'sub' => NULL),
  Array('key' =>'nodename', 'name' =>'nodename', 'sub' => NULL)
 )
);

$ddc_tables['xml_editor_attributes']=(object)
Array(
 'name' => 'xml_editor_attributes',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'id',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL, 'hname'=>'Ид'),
  Array('name' =>'nodeid',		'sql_type' =>'int(10)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0,	'sql_comment' =>NULL),
  Array('name' =>'attrname',	'sql_type' =>'varchar(200)', 'sql_null' =>0, 'sql_default' =>'',	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Имя'),
  Array('name' =>'textvalue',	'sql_type' =>'mediumtext', 'sql_null' =>1, 'sql_default' =>NULL,	'sql_sequence' => 0,			'sql_comment' =>NULL, 'hname'=>'Текст')
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'id', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'nodeid', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'attrname', 'sub' => NULL)
 )
);

if($_GET['init']=='init')
{
ddc_gentable_o($ddc_tables['xml_editor_nodes'],$sql);
ddc_gentable_o($ddc_tables['xml_editor_attributes'],$sql);
}




class test_xml_loader extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype=get_class($this);
		
	}
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->attributes['title']=$this->long_name;
	}
	
	
}



class test_xml_loader_test extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype=get_class($this);
		editor_generic::addeditor('qw',new test_xml_loader);
		editor_generic::addeditor('bt',new editor_button);
		editor_generic::addeditor('wo',new editor_button);
		$this->append_child($this->editors['qw']);
		$this->append_child($this->editors['bt']);
		$this->append_child($this->editors['wo']);
		$this->msgdiv=new dom_div;
		$this->append_child($this->msgdiv);
		
	}
	
	function setup()
	{
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->context[$this->long_name.'.bt']
		$this->editors['bt']->attributes['value']='++';
		$this->editors['wo']->attributes['value']='Write';
		$this->context[$this->long_name]['msgid']=$this->msgdiv->id_gen();
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
		global $sql;
		$this->long_name=$ev->parent_name;
	//$id,$current
	//$sql,$db,$tbl,$atbl
		if($ev->rem_name=='bt')
		{
			$sql->logquerys=false;
			$ld=new xml2sql;
			$ld->sql=&$sql;
			$ld->tbl='xml_editor_nodes';
			$ld->atbl='xml_editor_attributes';
			$ld->id=0;
			$sql->query('DELETE FROM xml_editor_nodes');
			$sql->query('DELETE FROM xml_editor_attributes');
			$ld->notrans=true;
			if($ld->notrans)$sql->query('ALTER TABLE '.$ld->tbl.' DISABLE KEYS');
			if($ld->notrans)$sql->query('ALTER TABLE '.$ld->atbl.' DISABLE KEYS');
			if($ld->notrans)$sql->query('START TRANSACTION');
			
			ini_set('max_execution_time',60*10);
			
			$handle = fopen("../xml/LT.fods", "r");
	//		$handle = fopen("../xml/o.zml", "r");
	//		$handle = fopen("../xml/greport3.smc", "r");
	//		$handle = fopen("../xml/greport3.xml", "r");
	//		$handle = fopen("../xml/xls.xml", "r");
	//		$handle = fopen("../xml/ДОХОД_2НДФЛ_6025_6025026876602501001_20090226_CBED40B3-78D3-4B2A-BD21-AEC68E1837DF.xml", "r");
			//print "<pre>".htmlspecialchars($v)."</pre>";
			if($handle)while(!feof($handle))
			{
				//$v=fread($handle,1);
				//if(ord($v)<ord(' '))$v=' ';
				//read 16K
				$v=fread($handle,16384);
				//replace invalid chars with whitespace
				$v=preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/',' ',$v);
				if(feof($handle))
					$ld->feed($v,true);
				else
					$ld->feed($v,false);
			}
			fclose($handle);
			if($ld->notrans)$sql->query('COMMIT');
			if($ld->notrans)$sql->query('ALTER TABLE '.$ld->tbl.' ENABLE KEYS');
			if($ld->notrans)$sql->query('ALTER TABLE '.$ld->atbl.' ENABLE KEYS');
			print "\$i('".js_escape($ev->context[$this->long_name]['msgid'])."').innerHTML='read-Ok';";
			if($sql->logquerys)
			{
				print "\$i('".js_escape($ev->context[$this->long_name]['msgid'])."').innerHTML='";
				foreach($sql->querylog as $e)
				{
					$a="<div><div>".htmlspecialchars($e->q)."</div><div>".
						htmlspecialchars($e->e)."</div></div>\n";
						print js_escape($a);
				}
				print "';";
			}
		}
		if($ev->rem_name=='wo')
		{
			$sql->logquerys=false;
			$ld=new xml2sql;
			$ld->sql=&$sql;
			$ld->tbl='xml_editor_nodes';
			$ld->atbl='xml_editor_attributes';
			$ld->id=0;
			$this->handle = fopen("../xml/xmlsql-out.xml", "w");
			
			ini_set('max_execution_time',60*5);
			
	//		$handle = fopen("../xml/greport3.xml", "r");
	//		$handle = fopen("../xml/xls.xml", "r");
	//		$handle = fopen("../xml/ДОХОД_2НДФЛ_6025_6025026876602501001_20090226_CBED40B3-78D3-4B2A-BD21-AEC68E1837DF.xml", "r");
			//print "<pre>".htmlspecialchars($v)."</pre>";
			$ld->wo(0,-1,'testw',$this);
			$this->flushw();
			fclose($this->handle);
			print "\$i('".js_escape($ev->context[$this->long_name]['msgid'])."').innerHTML='Write-ok';";
			if($sql->logquerys)
			{
				print "\$i('".js_escape($ev->context[$this->long_name]['msgid'])."').innerHTML='";
				foreach($sql->querylog as $e)
				{
					$a="<div><div>".htmlspecialchars($e->q)."</div><div>".
						htmlspecialchars($e->e)."</div></div>\n";
						print js_escape($a);
				}
				print "';";
			}
		}
		editor_generic::handle_event($ev);
	}
	
	function testw($v)
	{
		$this->testw_buffer.=$v;
		if(strlen($this->testw_buffer)>1000000)
		{
			fwrite($this->handle,$this->testw_buffer);
			$this->testw_buffer='';
		}
	}
	
	function flushw()
	{
		fwrite($this->handle,$this->testw_buffer);
	}
	
}




$tests_m_array['complex']['test_xml_loader_test']='test_xml_loader_test';



class test_selection extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->ed=new dom_any('textarea');
		$this->append_child($this->ed);
		$this->t=new dom_table_x(20,20);
		$this->t->css_class='test_selection_tbl';
		$t=new dom_statictext('selectionStart');
		$this->t->cells[0][0]->append_child($t);
		$t=new dom_statictext('selectionEnd');
		$this->t->cells[0][1]->append_child($t);
		
		$this->append_child($this->t);
	}
	
	function bootstrap()
	{
		$this->rootnode->exstyle['table.test_selection_tbl']['border-collapse']='collapse';
		$this->rootnode->exstyle['table.test_selection_tbl td']['border']='1px solid black';
		$ed=js_escape($this->ed->id_gen());
		$c1=js_escape($this->t->cells[1][0]->id_gen());
		$c2=js_escape($this->t->cells[1][1]->id_gen());
		$this->rootnode->endscripts['test_selection_tbl']=<<<aaa
		function test_selection_tbl_p()
		{
			var e=\$i('$ed');
			\$i('$c1').innerHTML=e.selectionStart;
			\$i('$c2').innerHTML=e.selectionEnd;
		}
		setInterval("test_selection_tbl_p();",500);
aaa;
	}
	
	function handle_event($ev)
	{
	}
}

$tests_m_array['complex']['test_selection']='test_selection';

class test_urlencode extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		editor_generic::addeditor('transform',new editor_select);
		$div=new dom_div;$this->append_child($div);
		$div->append_child($this->editors['transform']);
		
		$this->resend=new dom_any_noterm('input');
		$div->append_child($this->resend);
		$this->resend->attributes['value']='(<>)';
		$this->resend->attributes['type']='button'
		;
		$this->moveback=new dom_any_noterm('input');
		$div->append_child($this->moveback);
		$this->moveback->attributes['value']='^^';
		$this->moveback->attributes['type']='button';
		
		editor_generic::addeditor('txt',new editor_textarea);
		$this->append_child($this->editors['txt']);
		$this->editors['txt']->css_style['width']='80%';
		$this->editors['txt']->css_style['height']='200px';
		$this->t=new dom_any('div');
		$this->append_child($this->t);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		
		$this->editors['transform']->options['>h']="> htmlspecialchars";
		$this->editors['transform']->options['<h']="< htmlspecialchars";
		$this->editors['transform']->options['>u']="> Urlencode";
		$this->editors['transform']->options['<u']="< Urlencode";
		$this->editors['transform']->options['>b']="> BASE64";
		$this->editors['transform']->options['<b']="< BASE64";
		
		$this->resend->attributes['onclick']="var nya=\$i('".$this->editors['txt']->id_gen()."');nya.focus();nya.oldval += ' ';";
		$this->moveback->attributes['onclick']=
			"var n=\$i('".$this->editors['txt']->id_gen()."');".
			"var t=\$i('".$this->t->id_gen()."');".
			"n.focus();n.value = t.textContent;";
		
		$this->context[$this->long_name]['t']=$this->t->id_gen();
		$this->context[$this->long_name.'.transform']['var']='tr';
		if($_SESSION['test_urlencode->tr']=='')$_SESSION['test_urlencode->tr']='>h';
		$this->args['tr']=$_SESSION['test_urlencode->tr'];
		foreach($this->editors as $e)
		{
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
		}
		foreach($this->editors as $e)$e->bootstrap();
		$this->editors['transform']->attributes['onchange'].=$this->resend->attributes['onclick'];
	}
	
	function handle_event($ev)
	{
		switch($ev->rem_name)
		{
			case 'txt':
				$id=$ev->context[$ev->parent_name]['t'];
				$v=$_POST['val'];
				switch($_SESSION['test_urlencode->tr'])
				{
					case '>h':
					case '':
						$v=htmlspecialchars($v);
						break;
					case '<h':
						$v=htmlspecialchars_decode($v);
						break;
					case '>b':
						$v=base64_encode($v);
						break;
					case '<b':
						$v=base64_decode($v);
						break;
					case '>u':
						$v=urlencode($v);
						break;
					case '<u':
						$v=urldecode($v);
						break;
				}
				print "\$i('".js_escape($id)."').innerHTML='".js_escape(htmlspecialchars($v,ENT_QUOTES))."';";
				break;
			case 'transform':
				$_SESSION['test_urlencode->tr']=$_POST['val'];
				break;
		}
		editor_generic::handle_event($ev);
	}
}

$tests_m_array['util']['test_urlencode']='test_urlencode';





class test_query_exec extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		editor_generic::addeditor('qcont',new container_resize);
		$this->append_child($this->editors['qcont']);
		editor_generic::addeditor('qed',new editor_textarea);
		$this->editors['qcont']->append_child($this->editors['qed']);
		
		$this->editors['qed']->css_style['width']='100%';
		$this->editors['qed']->css_style['height']='100%';
		$this->editors['qed']->css_style['margin-left']='-3px';
		$this->editors['qed']->css_style['margin-top']='-3px';
		$this->editors['qed']->css_style['padding']='0px';
		
		editor_generic::addeditor('exec',new editor_button);
		$this->append_child($this->editors['exec']);
		$this->editors['exec']->attributes['value']='exec';
		
		editor_generic::addeditor('qres',new container_resize_scroll);
		$this->append_child($this->editors['qres']);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['qres_id']=$this->editors['qres']->in->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name.'.qed']['var']='qed';
		foreach($this->editors as $e)
		{
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
		}
		foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		$this->args['qed']=$this->rootnode->setting_val($this->oid,$this->long_name.'.q','');
		//print $this->args['qed'];
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		$st=new settings_tool;
		global $sql;
		$oid=$ev->context[$ev->parent_name]['oid'];
		switch($ev->rem_name)
		{
			case 'qed':
				$sfq=$st->set_query($oid,$ev->parent_name.'.q',$_SESSION['uid'],0,$_POST['val']);
				$sql->query($sfq);
				print '/* '.$sfq.';'.$ev->parent_name.' */';
				break;
			
			case 'exec':
				$res=new query_result_viewer_multiline;
				$res->show_nulls=true;
				$res->css_style['border-collapse']='collapse';
				$res->cell->css_style['border']='1px solid blue';
				$sfq=$st->single_query($oid,$ev->parent_name.'.q',$_SESSION['uid'],0);
				$res->compiled=$sql->fetch1($sql->query($sfq));

				$id=$ev->context[$ev->parent_name]['qres_id'];
				print "\$i('".js_escape($id)."').innerHTML=";
				reload_object($res);
		}
		editor_generic::handle_event($ev);
	}
}

$tests_m_array['util']['test_query_exec']='test_query_exec';


class test_multi_exec extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		editor_generic::addeditor('qsel',new editor_select);
		$this->append_child($this->editors['qsel']);
		editor_generic::addeditor('q-',new editor_button);
		$this->append_child($this->editors['q-']);$this->editors['q-']->attributes['value']='-';
		editor_generic::addeditor('q+',new editor_button);
		$this->append_child($this->editors['q+']);$this->editors['q+']->attributes['value']='+';
		
		editor_generic::addeditor('qcont',new container_resize);
		$this->append_child($this->editors['qcont']);
		editor_generic::addeditor('qed',new editor_textarea);
		$this->editors['qcont']->append_child($this->editors['qed']);
		
		$this->editors['qed']->css_style['width']='100%';
		$this->editors['qed']->css_style['height']='100%';
		$this->editors['qed']->css_style['margin-left']='-3px';
		$this->editors['qed']->css_style['margin-top']='-3px';
		$this->editors['qed']->css_style['padding']='0px';
		
		editor_generic::addeditor('X',new editor_button);
		$this->append_child($this->editors['X']);
		$this->editors['X']->attributes['value']='X';
		editor_generic::addeditor('all',new editor_button);
		$this->append_child($this->editors['all']);
		$this->editors['all']->attributes['value']='all';
		editor_generic::addeditor('this',new editor_button);
		$this->append_child($this->editors['this']);
		$this->editors['this']->attributes['value']='this';
		
		editor_generic::addeditor('qres',new container_resize_scroll);
		$this->append_child($this->editors['qres']);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['qres_id']=$this->editors['qres']->in->id_gen();
		$this->context[$this->long_name]['qed_id']=$this->editors['qed']->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		#$this->context[$this->long_name.'.qed']['var']='qed';
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
		}
		foreach($this->editors as $e)$e->bootstrap();
	}
	
	function html_inner()
	{
		$num=$this->rootnode->setting_val($this->oid,$this->long_name.'.test_multi_exec_n','1');
		for($k=0;$k<$num;$k++)$this->editors['qsel']->options[$k]='"'.$k.'"';
		$sel=$this->rootnode->setting_val($this->oid,$this->long_name.'.test_multi_exec_s','0');
		$this->args['qsel']=$sel;
		$this->args['qed']=$this->rootnode->setting_val($this->oid,$this->long_name.'.test_multi_exec_q'.$sel,'');
		//print $this->args['qed'];
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		$st=new settings_tool;
		global $sql;
		$oid=$ev->context[$ev->parent_name]['oid'];
		switch($ev->rem_name)
		{
			case 'X':
				print "var a=\$i('".$ev->context[$ev->parent_name]['qed_id']."');a.focus();a.value='';";
				break;
				
			case 'q+':
			case 'q-':
				$ffq=$st->single_query($oid,$ev->parent_name.'.test_multi_exec_n',$_SESSION['uid'],0);
				$res=$sql->fetch1($sql->query($ffq));
				$res=intval($res);
				if($res==0)$res=1;
				if($ev->rem_name=='q+')$res++;
				if($ev->rem_name=='q-')$res--;
				if($res<1)$res=1;
				$sfq=$st->set_query($oid,$ev->parent_name.'.test_multi_exec_n',$_SESSION['uid'],0,$res);
				$sql->query($sfq);
				$ffq=$st->single_query($oid,$ev->parent_name.'.test_multi_exec_s',$_SESSION['uid'],0);
				$s=$sql->fetch1($sql->query($ffq));
				$s=intval($s);
				if($s>=$res)$s=$res-1;
				$sfq=$st->set_query($oid,$ev->parent_name.'.test_multi_exec_s',$_SESSION['uid'],0,$s);
				$sql->query($sfq);
				print "window.location.reload(true);";
				break;
			case 'qsel':
				$sfq=$st->set_query($oid,$ev->parent_name.'.test_multi_exec_s',$_SESSION['uid'],0,$_POST['val']);
				$sql->query($sfq);
				print "window.location.reload(true);";
				break;
			case 'qed':
				$ffq=$st->single_query($oid,$ev->parent_name.'.test_multi_exec_s',$_SESSION['uid'],0);
				$res=$sql->fetch1($sql->query($ffq));
				$res=intval($res);
				$sfq=$st->set_query($oid,$ev->parent_name.'.test_multi_exec_q'.$res,$_SESSION['uid'],0,$_POST['val']);
				$sql->query($sfq);
				break;
			
			case 'this':
			case 'all':
				$ffq=$st->single_query($oid,$ev->parent_name.'.test_multi_exec_n',$_SESSION['uid'],0);
				$nn=$sql->fetch1($sql->query($ffq));
				$nn=intval($nn);
				if($nn==0)$nn=1;
				
				$ffq=$st->single_query($oid,$ev->parent_name.'.test_multi_exec_s',$_SESSION['uid'],0);
				$s=$sql->fetch1($sql->query($ffq));
				$s=intval($s);
				
				$id=$ev->context[$ev->parent_name]['qres_id'];
					print "\$i('".js_escape($id)."').innerHTML='';";
				for($k=0;$k<$nn;$k++)
				{
					if(($ev->rem_name=='this') && ($k!=$s))continue;
					$res=new query_result_viewer_multiline;
					$res->show_nulls=true;
					$res->css_style['border-collapse']='collapse';
					$res->cell->css_style['border']='1px solid blue';
					$sfq=$st->single_query($oid,$ev->parent_name.'.test_multi_exec_q'.$k,$_SESSION['uid'],0);
					$res->compiled=$sql->fetch1($sql->query($sfq));
					print "\$i('".js_escape($id)."').innerHTML+='".js_escape('<div>'.htmlspecialchars($res->compiled).'</div>')."';";
	
					print "\$i('".js_escape($id)."').innerHTML+=";
						reload_object($res);
				}
		}
		editor_generic::handle_event($ev);
	}
}

$tests_m_array['util']['test_multi_exec']='test_multi_exec';


class test_color_get extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$rows=100;
		$tbl=new dom_table_x(2,$rows);
		$this->append_child($tbl);
		$ir=rand();
		for($k=0; $k<$rows ; $k++)
		{
			$css=string_to_color($ir,1);
			$ir=$css.rand();
			$tc=bgcolor_to_color($css);
			$tbl->cells[$k][1]->css_style['background']=$css;
			$tbl->cells[$k][1]->css_style['color']=$tc;
			$t=new dom_statictext;
			$tbl->cells[$k][0]->append_child($t);
			$t->text=$k;
			$t=new dom_statictext;
			$tbl->cells[$k][1]->append_child($t);
			$t->text=$ir;
		}
	}
	function bootstrap()
	{
	}
	function handle_event()
	{
	}
}
$tests_m_array['sandbox']['test_color_get']='test_color_get';

class test_drop_button_0 extends dom_any
{
	function __construct()
	{
		parent::__construct('button');
		$this->txt=new dom_statictext;
		$this->append_child($this->txt);
		$this->hdiv=new dom_div;
		$this->append_child($this->hdiv);
		$this->css_class=get_class($this);
		for($k=0;$k<5;$k++)
		{
			$d=new dom_div;
			$b=new dom_any_noterm('input');
			$d->append_child($b);
			$b->attributes['value']='bt'.$k;
			$b->attributes['type']='button';
			$this->hdiv->append_child($d);
			
		}
		$this->txt->text='test';
		
	}
	function bootstrap()
	{
	
	$this->rootnode->exstyle['button.'.$this->css_class.'>div']=
		Array('display'=>'none');
	$this->rootnode->exstyle['button.'.$this->css_class.':hover>div']=
		Array('display'=>'block','position'=>'absolute','background'=>'white','border'=>'solid red 2px');
	$this->rootnode->exstyle['button.'.$this->css_class.':focus>div']=
		Array('display'=>'block','position'=>'absolute','background'=>'white','border'=>'solid red 2px');
		
	}
	function handle_event()
	{
	}
}
$tests_m_array['sandbox']['test_drop_button_0']='test_drop_button_0';

/*--------------------------------------------------------------------------------------------



--------------------------------------------------------------------------------------------*/

class csv2vcard_1 extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('fn',new file_pick_or_upload);
		$this->append_child($this->editors['fn']);
		editor_generic::addeditor('do',new editor_button);
		$this->append_child($this->editors['do']);
		$this->editors['do']->main->attributes['value']='do!';
		$this->res=new dom_div;
		$this->append_child($this->res);
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['res_id']=$this->res->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->oid=$this->oid;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
		}
		if(is_array($this->editors))
			foreach($this->editors as $e)$e->bootstrap();
	}
	
	function write2($s)
	{
		fwrite($this->unix_fd,$s."\n");
		fwrite($this->win_fd,iconv('utf-8','cp1251',$s)."\n");
	}
	
	function handle_event($ev)
	{
		switch($ev->rem_name)
		{
		case 'fn':
			$file_name=$_POST['val'];
			$this->unix_fd=fopen('../uploads/utf8.vcf',"w");
			$this->win_fd=fopen('../uploads/cp1251.vcf',"w");
			$csv=new csv;
			$in=file_get_contents('../uploads/'.$file_name);
			$r=explode("\n",$in);
			foreach($r as $line)
				if($line !='')
				{
					$row=$csv->split($line);
					$vc->email=$row[0];
					$vc->name=$row[1];
					$vc->surname=$row[2];
					$this->write2("BEGIN:VCARD");
					$this->write2("VERSION:3.0");
					$this->write2("REV:2008-06-24T15:18:51Z");
					$this->write2("EMAIL;TYPE=OTHER:".$vc->email);
					$this->write2("X-EVOLUTION-FILE-AS:".$vc->surname."\\, ".$vc->name);
					$this->write2("N:".$vc->surname.";".$vc->name.";;;");
					$this->write2("FN:".$vc->name." ".$vc->surname);
					$this->write2("END:VCARD");
					$this->write2("");
				}
			fclose($this->unix_fd);
			fclose($this->win_fd);
			print "\$i('".js_escape($ev->context[$ev->parent_name]['res_id'])."').innerHTML='".
				js_escape(
					"<div>".htmlspecialchars($in)."</div>".
					"<a href='../uploads/utf8.vcf'>utf-8</a> ".
					"<a href='../uploads/cp1251.vcf'>cp1251</a> "
				)."';";
			break;
		case 'do':
			break;
		}
		
		
		editor_generic::handle_event($ev);
	}
	
}
$tests_m_array['util']['csv2vcard_1']='csv2vcard_1';

/*--------------------------------------------------------------------------------------------



--------------------------------------------------------------------------------------------*/

























?>