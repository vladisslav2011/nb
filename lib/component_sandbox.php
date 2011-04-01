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



class test_code128 extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->res=new dom_div;
		$this->field=new dom_input;
		$this->field->attributes['type']='text';
		$this->doit=new dom_input;
		$this->doit->attributes['type']='button';
		$this->doit->attributes['onclick']=
			"var c=\$i('".$this->field->id_gen()."').value;".
			"\$i('".$this->res->id_gen()."').innerHTML='';".
			"\$i('".$this->res->id_gen()."').appendChild(code128l(c[0],c.replace(/^./,\"\"),'2px','150px',1));";
		
		$this->append_child($this->field);
		$this->append_child($this->doit);
		
		$this->append_child($this->res);
		
	}
	
	function bootstrap()
	{
	}
	
	function handle_event($ev)
	{
	}
}







$tests_m_array['sandbox']['code128']='test_code128';




class regexp_update extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('tbl',new regexp_update_txtasg);
		editor_generic::addeditor('col',new regexp_update_txtasg);
		editor_generic::addeditor('match',new editor_text);
		editor_generic::addeditor('replace',new editor_text);
		editor_generic::addeditor('update',new editor_button);
		$this->editors['update']->main->attributes['value']='update';
		editor_generic::addeditor('commit',new editor_button);
		$this->editors['commit']->main->attributes['value']='commit';
		editor_generic::addeditor('res',new regexp_update_list);
		
		$this->link_nodes();
	}
	
	function link_nodes()
	{
		$d=new dom_div;
		$this->append_child($d);
		$d->append_child($this->editors['tbl']);
		$d->append_child($this->editors['col']);
		$d->append_child($this->editors['match']);
		$d->append_child($this->editors['replace']);
		$d->append_child($this->editors['update']);
		$d->append_child($this->editors['commit']);
		$this->append_child($this->editors['res']);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['res_id']=$this->editors['res']->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		if(!is_array($this->keys))$this->keys=Array();
		if(!is_array($this->args))$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		
		//$this->args['res']=
		$this->args['tbl']=$this->rootnode->setting_val($this->oid,$this->long_name.'._tbl','');
		$this->args['col']=$this->rootnode->setting_val($this->oid,$this->long_name.'._col','');
		$this->args['match']=$this->rootnode->setting_val($this->oid,$this->long_name.'._match','');
		$this->args['replace']=$this->rootnode->setting_val($this->oid,$this->long_name.'._replace','');
		
		if(is_array($this->editors))foreach($this->editors as $i => $e)
			$e->bootstrap();
		
	}
	
	function handle_event($ev)
	{
		global $sql,$ddc_tables;
		$ev->do_reload=false;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		$this->args=Array();
		$this->args['tbl']=$sql->qv1($st->single_query($this->oid,$this->long_name."._tbl",$_SESSION['uid'],""));
		$this->args['col']=$sql->qv1($st->single_query($this->oid,$this->long_name."._col",$_SESSION['uid'],""));
		$this->args['match']=$sql->qv1($st->single_query($this->oid,$this->long_name."._match",$_SESSION['uid'],""));
		$this->args['replace']=$sql->qv1($st->single_query($this->oid,$this->long_name."._replace",$_SESSION['uid'],""));
		$ev->real_name=preg_replace('/.fo$/','',$ev->rem_name);
		$ev->tbl=$this->args['tbl'];
		
		if($ev->rem_name=='tbl')
		{
				$sql->query($st->set_query($this->oid,$this->long_name.'._tbl',$_SESSION['uid'],0,$_POST['val']));
				$this->args['tbl']=$_POST['val'];
				$ev->do_reload=true;
		}
		if($ev->rem_name=='col')
		{
				$sql->query($st->set_query($this->oid,$this->long_name.'._col',$_SESSION['uid'],0,$_POST['val']));
				$this->args['col']=$_POST['val'];
				$ev->do_reload=true;
		}
		if($ev->rem_name=='match')
		{
				$sql->query($st->set_query($this->oid,$this->long_name.'._match',$_SESSION['uid'],0,$_POST['val']));
				$this->args['match']=$_POST['val'];
				$ev->do_reload=true;
		}
		if($ev->rem_name=='replace')
		{
				$sql->query($st->set_query($this->oid,$this->long_name.'._replace',$_SESSION['uid'],0,$_POST['val']));
				$this->args['replace']=$_POST['val'];
				$ev->do_reload=true;
		}
		if($ev->rem_name=='update')
		{
			print "window.location.reload(true);";
		}
		if($ev->rem_name=='commit')
		{
			$ka=$sql->qa("SHOW KEYS FROM `".$sql->esc($this->args['tbl'])."`");
			$sel=new query_gen_ext('SELECT');
			$sel->from->exprs[]=new sql_column(NULL,$this->args['tbl']);
			$sel->what->exprs[]=new sql_column(NULL,NULL,$this->args['col'],'s');
			
			$up=new query_gen_ext('UPDATE');
			$up->into->exprs[]=new sql_column(NULL,$this->args['tbl']);
			$set=new sql_immed;
			$up->set->exprs[]=new sql_expression('=',Array(
				new sql_column(NULL,NULL,$this->args['col']),
				$set
				));
			foreach($ka as $kk)
			if($kk['Key_name']=='PRIMARY')
			{
				$kwhere[$kk['Column_name']]=new sql_immed;
				$up->where->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,$kk['Column_name']),
					$kwhere[$kk['Column_name']]
					));
				$sel->what->exprs[]=new sql_column(NULL,NULL,$kk['Column_name']);
			}
			$res=$sql->query($sel->result());
			while($row=$sql->fetcha($res))
			if(preg_match($this->args['match'],$row['s']))
			{
				$set->val=preg_replace($this->args['match'],$this->args['replace'],$row['s']);
				foreach($kwhere as $kn => $kimm)
					$kimm->val=$row[$kn];
				
				$sql->query($up->result());
			}
			print "window.location.reload(true);";
		}
		
		editor_generic::handle_event($ev);
	}
}


class regexp_update_txtasg extends editor_txtasg
{
	function fetch_list($ev,$k=NULL)
	{
		global $sql;
		if(preg_match('/tbl$/',$ev->real_name))
		{
			$res=$sql->query('SHOW TABLES'.
				(($k != '')?(" LIKE '%".$sql->esc($k)."%'"):""));
			while($row=$sql->fetchn($res))
			{
				$ra[]=Array('val'=>$row[0]);
			}
			$sql->free($res);
			return $ra;
		};
		if(preg_match('/col$/',$ev->real_name))
		{
			if($ev->tbl!='')
			{
				$res=$sql->query("SHOW COLUMNS FROM `".$sql->esc($ev->tbl)."`".
					(($k != '')?(" LIKE '%".$sql->esc($k)."%'"):""));
				while($row=$sql->fetchn($res))
				{
					$ra[]=Array('val'=>$row[0]);
				}
				$sql->free($res);
			}
			return $ra;
		};
		return NULL;
	}
}


class regexp_update_list extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('rn',new editor_statictext);
		editor_generic::addeditor('s',new editor_statictext);
		editor_generic::addeditor('d',new editor_statictext);
		
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->tr=new dom_tr;
		$this->tbl->append_child($this->tr);
		$td1=new dom_td;
		$td2=new dom_td;
		$td3=new dom_td;
		
		$td1->append_child($this->editors['rn']);
		$td2->append_child($this->editors['s']);
		$td3->append_child($this->editors['d']);
		$this->tr->append_child($td1);
		$this->tr->append_child($td2);
		$this->tr->append_child($td3);
		
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		if(!is_array($this->keys))$this->keys=Array();
		if(!is_array($this->args))$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
	}
	
	function html_inner()
	{
		global $sql;
		$res=$sql->query("SELECT `".$sql->esc($this->args['col'])."` as s FROM `".$sql->esc($this->args['tbl'])."` ORDER BY s");
		$this->tbl->html_head();
		$this->args['rn']=1;
		while($row=$sql->fetcha($res))
		if(preg_match($this->args['match'],$row['s']))
		{
			$this->args['s']=$row['s'];
			$this->args['d']=preg_replace($this->args['match'],$this->args['replace'],$row['s']);
			if(is_array($this->editors))foreach($this->editors as $i => $e)
				$e->bootstrap();
			$this->tr->html();
			$this->tr->id_alloc();
			$this->args['rn']+=1;
		}
		$this->tbl->html_tail();
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}

$tests_m_array['util']['regexp_update']='regexp_update';








class barcode_fill_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		editor_generic::addeditor('id_doc',new editor_text);
		editor_generic::addeditor('clear',new editor_button);
		$this->editors['clear']->attributes['value']='Clear';
		editor_generic::addeditor('list',new barcode_fill_test_list);
		editor_generic::addeditor('codes',new editor_textarea);
		
		
		$this->append_child($this->editors['id_doc']);
		$this->append_child($this->editors['clear']);
		
		$link=new dom_any('a');
		$link->append_child(new dom_statictext(' with groups '));
		$this->append_child($link);
		$this->get_all=$link;
		
		$link=new dom_any('a');
		$link->append_child(new dom_statictext(' combined '));
		$this->append_child($link);
		$this->get_combined=$link;
		
		$link=new dom_any('a');
		$link->append_child(new dom_statictext(' special '));
		$this->append_child($link);
		$this->get_special=$link;

		$link=new dom_any('a');
		$link->append_child(new dom_statictext(' place_zone '));
		$this->append_child($link);
		$this->get_place_zone=$link;

		
		$this->tbl=new dom_table;
		$tr=new dom_tr;
		$left=new dom_td;
		$right=new dom_td;
		$right->css_style['vertical-align']='top';
		$this->append_child($this->tbl->append_child($tr->append_child($left)));
		$tr->append_child($right);
		$left->append_child($this->editors['list']);
		$this->rs=new container_resize;
		$right->append_child($this->rs);
		$this->rs->append_child($this->editors['codes']);
		$this->editors['codes']->main->css_style['width']='100%';
		$this->editors['codes']->main->css_style['height']='100%';
		$this->editors['codes']->main->css_style['margin-top']='-3px';
		$this->editors['codes']->main->css_style['margin-left']='-3px';
		
		
	}
	
	function gen_links($a)
	{
		$this->link_all='/ext/table_csv_dump.php?query='.
			urlencode("SELECT a.zone,a.place,b.name,cast(a.count as decimal(10,0)) as `count` FROM barcodes_raw as b,test_doc as a WHERE b.id=a.prod AND doc_id=".intval($a));
		$this->link_combined='/ext/table_csv_dump.php?query='.
			urlencode("SELECT b.name,cast(sum(a.count) as decimal(10,0)) as `count` FROM barcodes_raw as b,test_doc as a WHERE b.id=a.prod  AND doc_id=".intval($a)." GROUP BY a.prod");
		$this->link_special='/ext/table_csv_dump.php?query='.
			urlencode("SELECT `br`.`name` , (SELECT sum( `d`.`count` ) FROM `test_doc` AS `d` WHERE ( `td`.`prod` = `d`.`prod` ) AND ( `d`.`zone` = '1' ) AND ( `d`.`doc_id` = '".intval($a)."' ) ) AS `z1` , (SELECT sum( `d`.`count` ) FROM `test_doc` AS `d` WHERE ( `td`.`prod` = `d`.`prod` ) AND ( `d`.`zone` = '2' ) AND ( `d`.`doc_id` = '".intval($a)."' ) ) AS `z2` , (SELECT sum( `d`.`count` ) FROM `test_doc` AS `d` WHERE ( `td`.`prod` = `d`.`prod` ) AND ( `d`.`zone` = '3' ) AND ( `d`.`doc_id` = '".intval($a)."' ) ) AS `z3` FROM `barcodes_raw` AS `br` , `test_doc` AS `td` WHERE ( `br`.`id` = `td`.`prod` ) AND ( `td`.`doc_id` = '".intval($a)."' ) GROUP BY `br`.`name` ASC");

		$this->link_place_zone='/ext/table_csv_dump.php?query='.
			urlencode("SELECT `td`.`zone` , `td`.`place` , cast(sum(td.count) as decimal(10,0)) as `count` FROM `test_doc` AS `td` WHERE ( `td`.`doc_id` = '".intval($a)."' ) GROUP BY `td`.`zone` ASC, `td`.`place` ASC");

	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['list_id']=$this->editors['list']->id_gen();
		$this->context[$this->long_name]['all_id']=$this->get_all->id_gen();
		$this->context[$this->long_name]['combined_id']=$this->get_combined->id_gen();
		$this->context[$this->long_name]['special_id']=$this->get_special->id_gen();
		$this->context[$this->long_name]['place_zone_id']=$this->get_place_zone->id_gen();
		if(!is_array($this->keys))$this->keys=Array();
		if(!is_array($this->args))$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		$this->args['id_doc']=$this->rootnode->setting_val($this->oid,$this->long_name.'._id_doc',0);
		$this->gen_links($this->args['id_doc']);
		$this->get_all->attributes['href']=$this->link_all;
		$this->get_combined->attributes['href']=$this->link_combined;
		$this->get_special->attributes['href']=$this->link_special;
		$this->get_place_zone->attributes['href']=$this->link_place_zone;
		$this->editors['list']->def=$this->gen_def();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
			$e->bootstrap();
	}
	
	function gen_def()
	{
		$r=new query_gen_ext('select');
		$r->from->exprs=Array(
			new sql_column(NULL,'test_doc',NULL,'td'),
			new sql_column(NULL,'barcodes_raw',NULL,'br')
			);
		$r->what->exprs=Array(
			new sql_column(NULL,'td','count'),
			new sql_column(NULL,'td','zone'),
			new sql_column(NULL,'td','place'),
			new sql_column(NULL,'br','name')
			);
			
		$r->where->exprs=Array(
			new sql_expression('=',Array(
				new sql_column(NULL,'td','doc_id'),
				new sql_immed($this->args['id_doc'])
				)),
			new sql_expression('=',Array(
				new sql_column(NULL,'td','prod'),
				new sql_column(NULL,'br','id'),
				))
			);
		return $r;
	}
	
	function check_doc()
	{
		global $sql;
		$qg=$this->gen_def();
		$qg->what->exprs=Array(new sql_list('count',Array(
			new sql_column(NULL,NULL,'row')
			)));
		return ($sql->qv1($qg->result())>0);
		
	}
	
	function perform_clear()
	{
		global $sql;
		$qg=new query_gen_ext('delete');
		$qg->from->exprs=Array(
			new sql_column(NULL,'test_doc',NULL),
		);
		$qg->where->exprs=Array(
			new sql_expression('=',Array(
				new sql_column(NULL,NULL,'doc_id'),
				new sql_immed($this->args['id_doc'])
				)),
			);
		$sql->query($qg->result());
	}
	
	function parse($l)
	{
		global $sql;
		$le=explode("\n",$l);
		$im_code=new sql_immed;
		$im_count=new sql_immed(1);
		$im_place=new sql_immed;
		$im_zone=new sql_immed;
		
		$sq=new query_gen_ext('select');
		$sq->from->exprs[]=new sql_column(NULL,'barcodes_raw',NULL,'br');
		$sq->what->exprs[]=new sql_column(NULL,'br','id');
		$sq->where->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,'br','code'),
			$im_code
			));
		
		$qg=new query_gen_ext('insert update');
		$qg->into->exprs[]=new sql_column(NULL,'test_doc',NULL);
		$qg->set->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'doc_id'),
			new sql_immed($this->args['id_doc'])
			));
		$qg->set->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'prod'),
			new sql_subquery($sq)
			));
		$qg->set->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'place'),
			$im_place
			));
		$qg->set->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'zone'),
			$im_zone
			));
		$qg->set->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'count'),
			$im_count
			));
		$qg->update->exprs[]=new sql_expression('=',Array(
			new sql_column(NULL,NULL,'count'),
			new sql_expression('+',Array(
				new sql_column(NULL,NULL,'count'),
				$im_count
				))
			));
		$mm=Array();
		$im_place->val=1;
		$place=0;
		$zone=1;
		foreach($le as $r)
		{
			if(preg_match('/^[0-9]{13}$/',$r))
			{
//				print "alert('".js_escape($qg->result())."');";
				if(isset($mm[$zone][$place][$r]))$mm[$zone][$place][$r]+=1;
				else $mm[$zone][$place][$r]=1;
			}
			if(preg_match('/^0[1-9]$/i',$r)||preg_match('/^X[0-9]$/i',$r))
			{
				$place-=($place % 10);
				$place+=intval($r[1]);
			}
			if(preg_match('/^A[0-9]$/i',$r))
			{
				$zone=intval($r[1]);
			}
			if(preg_match('/^[0-9]$/',$r))
			{
				$zone=intval($r[0]);
			}
			if(preg_match('/^[1-9]0$/i',$r)||preg_match('/^[0-9]X$/i',$r))
			{
				$place=($place % 10);
				$place+=intval($r[0])*10;
			}
			if(preg_match('/^00$/',$r))
			{
				$place=0;
			}
		}
		foreach( $mm as $zone => $xx)
			foreach( $xx as $place => $u)
				foreach( $u as $code => $count)
				{		
					$im_zone->val=$zone;
					$im_place->val=$place;
					$im_code->val=$code;
					$im_count->val=$count;
					$sql->query($qg->result());
				}
	}
	
	function handle_event($ev)
	{
		global $sql;
		$this->long_name=$ev->parent_name;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$st=new settings_tool;
		$this->args=Array();
		$this->args['id_doc']=$sql->qv1($st->single_query($this->oid,$this->long_name."._id_doc",$_SESSION['uid'],""));
		if($ev->rem_name=='id_doc')
		{
			$sql->query($st->set_query($this->oid,$this->long_name.'._id_doc',$_SESSION['uid'],0,$_POST['val']));
			$this->args['id_doc']=$_POST['val'];
			$this->gen_links($_POST['val']);
			print "\$i('".js_escape($ev->context[$this->long_name]['all_id'])."').setAttribute('href','".js_escape($this->link_all)."');";
			print "\$i('".js_escape($ev->context[$this->long_name]['combined_id'])."').setAttribute('href','".js_escape($this->link_combined)."');";
			print "\$i('".js_escape($ev->context[$this->long_name]['special_id'])."').setAttribute('href','".js_escape($this->link_special)."');";
			print "\$i('".js_escape($ev->context[$this->long_name]['place_zone_id'])."').setAttribute('href','".js_escape($this->link_place_zone)."');";
			$ev->reload_list=true;
		}
		if($ev->rem_name=='clear')
		{
			$this->perform_clear();
			$ev->reload_list=true;
		}
		if($ev->rem_name=='codes')
		{
			$this->perform_clear();
			$this->parse($_POST['val']);
			$ev->reload_list=true;
		}
		editor_generic::handle_event($ev);
		if($ev->reload_list)
		{
			$r=new barcode_fill_test_list;
			$r->def=$this->gen_def();
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$this->oid;
			$r->name=$ev->parent_name.'.list';
			$r->etype=$ev->parent_type.'.'.$r->etype;
			print "(function(){";
			print "var nya=\$i('".js_escape($ev->context[$ev->parent_name]['list_id'])."');";
			print "try{nya.innerHTML=";
			reload_object($r,true);
			print "}catch(e){ window.location.reload(true);};";
			print "})();";
		}
	}
}

class barcode_fill_test_list extends dom_table
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		editor_generic::addeditor('num', new editor_statictext);
		editor_generic::addeditor('zone', new editor_statictext);
		editor_generic::addeditor('place', new editor_statictext);
		editor_generic::addeditor('name', new editor_statictext);
		editor_generic::addeditor('count', new editor_statictext);
		
		$this->tr=new dom_tr;
		$this->append_child($this->tr);
		foreach($this->editors as $n => $e)
		{
			$td=new dom_td;
			$this->tr->append_child($td);
			$td->append_child($e);
		};
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		if(!is_array($this->keys))$this->keys=Array();
		if(!is_array($this->args))$this->args=Array();
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
	}
	
	function html_inner()
	{
		global $sql;
		//$this->def is query_gen_ext returning 'name' and 'count'
		$sel=$this->def;
		$res=$sql->query($sel->result());
		$this->args['num']=0;
		while($row=$sql->fetcha($res))
		{
			$this->args['num']++;
			$this->args['name']=$row['name'];
			$this->args['zone']=$row['zone'];
			$this->args['place']=$row['place'];
			$this->args['count']=$row['count'];
			if(is_array($this->editors))foreach($this->editors as $i => $e)
				$e->bootstrap();
			$this->tr->html();
			$this->tr->id_alloc();
		};
		$sql->free($res);
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}

$tests_m_array['sandbox']['barcode_fill_test']='barcode_fill_test';



class codes_match extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		editor_generic::addeditor('fltr',new editor_text);
		editor_generic::addeditor('list',new codes_match_list);
		$this->append_child($this->editors['fltr']);
		$this->append_child($this->editors['list']);
		$this->errmsg=new dom_statictext;
		$this->append_child($this->errmsg);
		
		
	}
	
	function bootstrap()
	{
		$this->args=Array();
		$this->keys=Array();
		$this->long_name=editor_generic::long_name();
		//$this->oid=-1;
		$this->context[$this->long_name]['list_id']=$this->editors['list']->id_gen();
		$this->context[$this->long_name]['oid']=$this->oid;
		if(is_array($this->editors))foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->args=&$this->args;
			$e->keys=&$this->keys;
			$e->context=&$this->context;
			$e->oid=$this->oid;
		}
		if(is_array($this->editors))foreach($this->editors as $e)
			$e->bootstrap();
		$this->args['fltr']=$this->rootnode->setting_val($this->oid,$this->long_name.'.fltr','');
	}
	
	function handle_event($ev)
	{
		global $sql;
		$ev->reload_list=false;
		$this->oid=$ev->context[$ev->parent_name]['oid'];
		$this->long_name=$ev->parent_name;
		$st=new settings_tool;
		$this->args['fltr']=$sql->qv1($st->single_query($this->oid,$this->long_name.".fltr",$_SESSION['uid'],""));
		switch($ev->rem_name)
		{
		case 'fltr':
			$sql->query($st->set_query($this->oid,$this->long_name.'.fltr',$_SESSION['uid'],0,$_POST['val']));
			$this->args['fltr']=$_POST['val'];
			$ev->reload_list=true;
			break;
		}
		if($ev->reload_list)
		{
			
			$customid=$ev->context[$ev->parent_name]['list_id'];
			//$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$r=new codes_match_list;
			
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->oid=$this->oid;
			$r->args=$this->args;
			$r->name=$ev->parent_name.".list";
			$r->etype=$ev->parent_type.".".get_class($r);

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

class codes_match_list extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('o_name',new editor_statictext);
		editor_generic::addeditor('m_id',new editor_search_pick);
		editor_generic::addeditor('m_del',new editor_button);
		$this->editors['m_id']->io=new editor_search_pick_sqltest_io;
		
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
						$brdiv->append_child($this->editors['o_name']);
				$td=new dom_td;
				$td->attributes['rowspan']=2;
			$tr=new dom_tr;
			$tbl->append_child($tr);
				$td=new dom_td;
				$tr->append_child($td);
					#editor_generic::addeditor('m_id',new editor_text);
					$td->append_child($this->editors['m_id']);
					$this->editors['m_id']->css_style['display']='inline-block';
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
	
	
	
	function html_inner()
	{
		global $sql;
		$fltr='%'.str_replace(' ','%',$this->args['fltr']).'%';
		$res=$sql->query("SELECT n1.id as `n1-id`, n1.`mapid`, n1.`normalized-name`, br.name FROM `names-1c` as n1 LEFT OUTER JOIN `barcodes_raw` as br ON n1.mapid=br.id WHERE n1.`normalized-name` LIKE '".$sql->esc($fltr)."' LIMIT 50");
		while($row=$sql->fetcha($res))
		{
			$this->args['o_name']=$row['normalized-name'];
			$this->keys['id']=$row['n1-id'];
			foreach($this->editors as $e)
				$e->bootstrap();
			if($row['name']=='')
			{
				$this->editors['m_del']->css_style['display']='none';
			}else{
				$this->editors['m_del']->css_style['display']='inline';
			}
			if($row['mapid']==0)$row['mapid']=NULL;
			$this->args['m_id']=$row['mapid'];
			$this->xdiv->html();
			$this->xdiv->id_alloc();
		}
	}
	
	function add_map($n1id,$id)
	{
		global $sql;
		$q="UPDATE `names-1c` SET mapid='".$sql->esc($id)."' WHERE id='".$sql->esc($n1id)."'";
		$sql->query($q);
	}
	
	function del_map($n1id)
	{
		global $sql;
		$q="UPDATE `names-1c` SET mapid='0' WHERE id='".$sql->esc($n1id)."'";
		$sql->query($q);
	}
	
	function handle_event($ev)
	{
		if($ev->rem_name=='m_id')
		{
			$this->add_map($ev->keys['id'],$_POST['val']);
		}
		if($ev->rem_name=='m_del')
		{
			$this->del_map($ev->keys['id']);
		}
		editor_generic::handle_event($ev);
	}
}

$tests_m_array['util']['codes_match']='codes_match';
















?>