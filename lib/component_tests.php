<?php

require_once("lib/dev_controls.php");
require_once("lib/query_editor.php");



// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
class clipboard_session
{
	function fetch()
	{
		if($_SESSION['clipboard']['format']=='x-serialized')
			return unserialize($_SESSION['clipboard']['data']);
		else
			return NULL;
	}
	
	function store($var)
	{
		$_SESSION['clipboard']['data']=serialize($var);
		$_SESSION['clipboard']['format']='x-serialized';
	}
}

$clipboard=new clipboard_session;

class editor_text_test extends dom_div
{
	public $testing='editor_text';
	function __construct()
	{
		dom_div::__construct();
		$capt=new container_captioned;
		$capt->caption='Manipulate the control [';
		$capt->topdiv->css_style['padding']='0.2em';
		$this->append_child($capt);
		$capt->caption.=$this->testing.'_test ] below';
		$this->etype=$this->testing.'_test';
		$t=$this->testing;
		editor_generic::addeditor('test',new $t);
		$capt->append_child($this->editors['test']);
		
		$capt=new container_captioned;
		$capt->caption='And see results here!';
		$capt->topdiv->css_style['padding']='0.2em';
		$this->append_child($capt);
		$this->res=$res=new dom_div;
		$capt->append_child($res);
		
	
	}
	
	
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		$this->context[$long_name.'.test']['ret']=$this->res->id_gen();
		$this->context[$long_name.'.test']['var']='a';
		$this->args['a']='blah';
		reset($this->editors);
		foreach($this->editors as $e)
		{
			$e->context= & $this->context;
			$e->args= &$this->args;
			$e->keys= &$this->keys;
		}
		$this->for_each_set('oid',$this->oid);
	}
	
	function html_inner()
	{
		foreach($this->editors as $e)
			$e->bootstrap();
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		switch($ev->rem_name)
		{
		case 'test':
			if($_POST['val']=='error')
			{
				$ev->failure='Invalid value. Damn you entered damn bad fucking value that is so bad, soooo bad, damn motherfucking idiot. Try (or, shit, don\'t ever try) to fix text, you entered, bastard and see results, little asssucker!';
				break;
			}
			print "var a=\$i('".js_escape($ev->context[$ev->long_name]['ret'])."');".
				"a.innerHTML='".js_escape(htmlspecialchars($_POST['val'],ENT_QUOTES))."';".
				"";
		default:
			;
		}
		editor_generic::handle_event($ev);
	}

}


class editor_button_test extends editor_text_test
{
	public $testing='editor_button';
	function handle_event($ev)
	{
		switch($ev->rem_name)
		{
		case 'test':
			print "var a=\$i('".js_escape($ev->context[$ev->long_name]['ret'])."');".
				"if(a.cnt){a.cnt++;}else{a.cnt=1;};a.innerHTML='Button pressed '+a.cnt+' times';";
		default:
			;
		}
		editor_generic::handle_event($ev);
	}

}

class editor_checkbox_test extends editor_text_test
{
	public $testing='editor_checkbox';
}

class editor_text_submit_test extends editor_text_test
{
	public $testing='editor_text_submit';
}

class editor_textarea_test extends editor_text_test
{
	public $testing='editor_textarea';
	function html_inner()
	{
		$this->editors['test']->css_style['width']='100em';
		$this->editors['test']->css_style['height']='2-emem';
		parent::html_inner();
	}
}



class editor_text_autosuggest_test extends editor_text_test
{
	public $testing='editor_text_autosuggest';
}


class editor_text_autosuggest_session_test extends editor_text_autosuggest_test
{
	public $testing='editor_text_autosuggest_session';
}

class editor_container_hidden_test extends dom_div
{
	public $testing='editor_container_hidden';
	function __construct()
	{
		dom_div::__construct();
		$cont=new container_hidden;
		$this->append_child($cont);
		$cont->title='Click button';
		$text=new dom_statictext;
		$cont->append_child($text);
		$text->text='This is hidden text';
	
	}
	
	
	function bootstrap()
	{
	}
	
	function handle_event($ev)
	{
		return;
	}

}









class workers_container_test extends dom_div
{
	public $testing='workers_container_test';
	function __construct()
	{
		dom_div::__construct();
		$this->etype='workers_container_test';
		$this->ioclasstype='query_gen_io_test';
		$this->wc=new workers_container;
		editor_generic::addeditor('wc',$this->wc);
		$this->append_child($this->wc);
		$this->res=new dom_div;
		$this->append_child($this->res);
		$this->r=new query_result_v;
		editor_generic::addeditor('r',$this->r);
		$this->res->append_child($this->r);
		
		$errordump=new dom_div;
		$errordump->custom_id='errordump';
		$this->append_child($errordump);
	}
	
	
	
	
	function bootstrap()
	{
		
//		print 'oid='.$this->oid;
		$long_name=editor_generic::long_name();
		$this->context[$long_name.'.wc']['ioclass']=$this->ioclasstype;
		$this->context[$long_name]['retdiv']=$this->res->id_gen();
		$this->context[$long_name]['oid']=$this->oid;
		//unset($_SESSION['qg']);
		//if(!isset($_SESSION['qg']))
		//	$_SESSION['qg']=serialize($this->make_qg());
		
		//$this->wc->obj=unserialize($_SESSION['qg']);//$this->make_qg();
		
		reset($this->editors);
		foreach($this->editors as $e)
		{
			$e->context= & $this->context;
			$e->args= &$this->args;
			$e->keys= &$this->keys;
		}
		$this->for_each_set('oid',$this->oid);
		
		$qgio=new $this->context[$long_name.'.wc']['ioclass'];
		$qgio->context=$this->context;
		$qgio->oid=$this->oid;
		$qgio->long_name=$long_name.'.wc';
		$res=$qgio->load();
		$q=$res->result();
		$this->r->query=$q;
		foreach($this->editors as $e)
			$e->bootstrap();
		
	}
	
	function html_inner()
	{
		foreach($this->editors as $e)
			$e->bootstrap();
		parent::html_inner();
		print_r($_SESSION['focus-state']);
	}
	
	function handle_event($ev)
	{
		$qgio=new $ev->context[$ev->parent_name.'.wc']['ioclass'];
		$retdiv=$ev->context[$ev->parent_name]['retdiv'];
		$qgio->context=$ev->context;
		$qgio->oid=$ev->context[$ev->parent_name]['oid'];
		$qgio->long_name=$ev->parent_name.'.wc';
		editor_generic::handle_event($ev);
		$res=$qgio->load();
		$q=$res->result();
		$r=new query_result_v;
		$r->oid=$qgio->oid;
		$r->query=$q;
		//$r->custom_id=$customid;
		$r->bootstrap();
		
		print "var a=\$i('".js_escape($retdiv)."');try{a.innerHTML= ";
		reload_object($r,false);
		print "}catch(e){ window.location.reload(true);};";
		//print '$i(\''.js_escape($retdiv).'\').innerHTML=\''.js_escape(htmlspecialchars($q,ENT_QUOTES)).'\';';
		
		
	}
}



class query_gen_io_test
{
	function make_qg()
	{
		
		$qg= new query_gen_ext;
		return $qg;
	}
/////////////////////////////////////////////////////////////////////////	
	function load()
	{
		if(!isset($_SESSION['qg']))
			$_SESSION['qg']=serialize($this->make_qg());
		
		return unserialize($_SESSION['qg']);//$this->make_qg();
	}
	function save($ref)
	{
		$_SESSION['qg']=serialize($ref);
	}
}











//////////////////////////////////////////////////////////////////////////	
/////////////////////////////////////////////////////////////////////////	
////////////////////////////////////////////////////////////////////////	




class workers_container_test1 extends workers_container_test
{
	public $testing='workers_container_test1';
	function __construct()
	{
		parent::__construct();
		$this->etype='workers_container_test1';
		$this->ioclasstype='query_gen_io_test_sets';

	}
}


//function single_query($oid,$setting,$uid,$preset,$flags='')
//function set_query($oid,$setting,$uid,$preset,$val)

class query_gen_io_test_sets
{
	function make_qg()
	{
		
		$qg= new query_gen_ext;
		return $qg;
	}
/////////////////////////////////////////////////////////////////////////	
	function load()
	{
		global $sql;
		$res=$sql->query(settings_tool::single_query($this->oid,$this->long_name,$_SESSION['uid'],0));
		$qg=$sql->fetch1($res);
		//print 'oid='.$this->oid;
		//print 'n='.$this->long_name;
		if(!isset($qg))
			$qg=serialize($this->make_qg());
		$ret=unserialize($qg);
		if(!is_object($ret))$ret=$this->make_qg();
		return $ret;
	}
	function save($ref)
	{
		global $sql;
		$res=$sql->query(settings_tool::set_query($this->oid,$this->long_name,$_SESSION['uid'],0,serialize($ref)));
	}
}






class query_result_v extends dom_any
{
	function __construct()
	{
		parent::__construct('div');
		
		$this->divtxt=new dom_div;
		$this->append_child($this->divtxt);
		$this->qtxt=new dom_statictext;
		$this->divtxt->append_child($this->qtxt);
		
		$this->tbl=new dom_table;
		$this->append_child($this->tbl);
		$this->row=new dom_tr;
		unset($this->row->id);
		$this->tbl->append_child($this->row);
		$this->col=new dom_td;
		unset($this->col->id);
		$this->row->append_child($this->col);
		$this->tbl->css_class='res_table_test';
		$this->txt=new dom_statictext;
		$this->col->append_child($this->txt);
	}
	
	function bootstrap()
	{
		$this->rootnode->exstyle['.res_table_test']['border-collapse']='collapse';
		$this->rootnode->exstyle['.res_table_test td']['border']='1px solid black';
	}
	
	function html_inner()
	{
		global $sql;
		
		$this->qtxt->text=$this->query;
		$this->divtxt->html();
		
		$this->tbl->html_head();
		
		$f=0;
		$res=$sql->query($this->query);
		if($res)
			while($row=$sql->fetcha($res))
			{
				if($f==0)
				{
					//$this->row->id_alloc();
					$this->row->html_head();
					$this->txt->text=$f;
					$this->col->html();
					foreach($row as $h => $v)
					{
						$this->txt->text=$h;
						$this->col->html();
						//$this->col->id_alloc();
					}
					$this->row->html_tail();
				}
				$f++;
				//$this->row->id_alloc();
				$this->row->html_head();
				//output row number
				$this->txt->text=$f;
				$this->col->html();
				//$this->rootnode->out('<td>'.htmlspecialchars($f).'</td>');
				//output columns
				foreach($row as $h => $v)
				{
					$this->txt->text=$v;
					$this->col->html();
					//$this->rootnode->out('<td>'.htmlspecialchars($v).'</td>');
					//$this->col->id_alloc();
				}
				$this->row->html_tail();
			}
		$this->tbl->html_tail();
	}
	
	function handle_event($ev)
	{
		return;
	}
	
}


//////------------------------------------------------------------------------------------------------


class query_merge_test extends dom_div
{
	public $testing='query_merge';
	const NUM_QUERYS=3;
	function __construct()
	{
		dom_div::__construct();
		$this->etype='query_merge_test';
		$this->ioclasstype='query_gen_io_test_sets';
		for($k=0;$k<query_merge_test::NUM_QUERYS;$k++)
		{
			editor_generic::addeditor('wc'.$k,new workers_container);
			$this->append_child($this->editors['wc'.$k]);
		}
		$this->res=new dom_div;
		$this->append_child($this->res);
		$this->r=new workers_container;
		editor_generic::addeditor('r',$this->r);
		$this->res->append_child($this->r);
		
		$errordump=new dom_div;
		$errordump->custom_id='errordump';
		$this->append_child($errordump);
	}
	
	
	
	
	function bootstrap()
	{
		
//		print 'oid='.$this->oid;
		$long_name=editor_generic::long_name();
		for($k=0;$k<query_merge_test::NUM_QUERYS;$k++)
			$this->context[$long_name.'.wc'.$k]['ioclass']=$this->ioclasstype;
		$this->context[$long_name.'.r']['ioclass']=$this->ioclasstype;
		//$this->context[$long_name]['ioclass']=$this->ioclasstype;
		$this->context[$long_name]['retdiv']=$this->res->id_gen();
		$this->context[$long_name]['oid']=$this->oid;
		//unset($_SESSION['qg']);
		//if(!isset($_SESSION['qg']))
		//	$_SESSION['qg']=serialize($this->make_qg());
		
		//$this->wc->obj=unserialize($_SESSION['qg']);//$this->make_qg();
		
		reset($this->editors);
		foreach($this->editors as $e)
		{
			$e->context= & $this->context;
			$e->args= &$this->args;
			$e->keys= &$this->keys;
		}
		$this->for_each_set('oid',$this->oid);
		
		foreach($this->editors as $e)
			$e->bootstrap();
		
	}
	
	function html_inner()
	{
		//foreach($this->editors as $e)$e->bootstrap();
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		$cont=$ev->context;
		$pna=$ev->parent_name;
		$type=$ev->parent_type;
		$sna=$pna.'.'.preg_replace('/\..*$/','',$ev->rem_name);
		$retdiv=$ev->context[$ev->parent_name]['retdiv'];
		
		editor_generic::handle_event($ev);
		
		for($k=0;$k<query_merge_test::NUM_QUERYS;$k++)
		{
		//print '/* **** ioclass '.$e0->name.' **** */';
		$qgio=new $cont[$sna]['ioclass'];
		$qgio->context=$cont;
		$qgio->oid=$cont[$pna]['oid'];
		$qgio->long_name=$pna.'.wc'.$k;
		$res[$k]=$qgio->load();
		unset($qgio);
		}
		for($k=1;$k<query_merge_test::NUM_QUERYS;$k++)
		{
			$res[0]->smart_merge($res[$k]);
		}
		
		$qgio=new $cont[$sna]['ioclass'];
		$qgio->context=$cont;
		$qgio->oid=$cont[$pna]['oid'];
		$qgio->long_name=$pna.'.r';
		$old=$qgio->load();
		if(serialize($old) != serialize($res[0]))
		{
			$qgio->save($res[0]);
			
			$r=new workers_container;
			$r->name=$qgio->long_name;
			$r->etype=$type.'.'.$r->etype;
			$r->oid=$qgio->oid;
			$r->context[$qgio->long_name]['ioclass']=$cont[$sna]['ioclass'];
			//$r->bootstrap();
			print "var a=\$i('".js_escape($retdiv)."');try{a.innerHTML= ";
			reload_object($r);
			print	"}catch(e){ window.location.reload(true);};";
		}
		
		
		if(false)
		{
		$qgio=new $ev->context[$ev->parent_name.'.wc']['ioclass'];
		$retdiv=$ev->context[$ev->parent_name]['retdiv'];
		$qgio->context=$ev->context;
		$qgio->oid=$ev->context[$ev->parent_name]['oid'];
		$qgio->long_name=$ev->parent_name.'.wc';
		editor_generic::handle_event($ev);
		$res=$qgio->load();
		$q=$res->result();
		$r=new query_result_v;
		$r->oid=$qgio->oid;
		$r->query=$q;
		//$r->custom_id=$customid;
		$r->bootstrap();
		
		print "var a=\$i('".js_escape($retdiv)."');try{a.innerHTML= ";
		reload_object($r,false);
		print "}catch(e){ window.location.reload(true);};";
		//print '$i(\''.js_escape($retdiv).'\').innerHTML=\''.js_escape(htmlspecialchars($q,ENT_QUOTES)).'\';';
		
		}

	}
	function lb($cnt,$pna,$a)
	{
	}
	
}




















class editor_debugger_test extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		//$this->etype='editor_debugger_test';
		$cont=new dom_any('button');
		$this->append_child($cont);
		$cont->css_class='mf';
		//$cont->attributes['href']='http://google.com';
		$v=new dom_statictext;
		$v->text=&$m->m;
		$p->m='text1';
		$m=$p;
		//$v->{$this->m}='mmmdffmdmf';
		$cont->append_child($v);
	
	}
	
	
	function bootstrap()
	{
	}
	
	function handle_event($ev)
	{
		return;
	}
	function after_build_before_children()
	{
		$this->rootnode->exstyle['.mf']['border']='2px solid transparent';
		$this->rootnode->exstyle['.mf:hover']['border']='2px outset buttonface';
		$this->rootnode->exstyle['.mf:focus']['border']='2px outset buttonface';

	}


}

class editor_select_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('t',new editor_select);
		$this->append_child($this->editors['t']);
		$this->resdiv=new dom_div;
		$this->append_child($this->resdiv);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		$this->context[$this->long_name.'.t']['var']='t';
		$this->context[$this->long_name.'.t']['options']=serialize(Array(
			'0'=>'Value 0',
			'1'=>'Value 1',
			'2'=>'Value 2',
			'30'=>'Value 30',
			'ddd'=>'Value ddd',
			'1w3'=>'Value 1w3',
		));
		$this->context[$this->long_name]['retv']=$this->resdiv->id_gen();
		foreach($this->editors as $i=>$e)
		{
			$e->args=&$this->args;
			$e->context=&$this->context;
			$e->oid=$this->oid;
			$e->keys=&$this->keys;
		}
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		if($ev->rem_name=='t')
		{
			$id=$ev->context[$ev->parent_name]['retv'];
			print "\n/*".$ev->parent_name."\n*/";
			print "\$i('".js_escape($id)."').innerHTML='".
				js_escape(htmlspecialchars("Gottcha: ".$_POST['val'],ENT_QUOTES)).
				"';";
		}
		
		editor_generic::handle_event($ev);
	}
}




$tests_m_array[]='editor_select_test';


class test_long_run extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('start',new editor_button);
		$this->append_child($this->editors['start']);
		$this->editors['start']->attributes['value']='Start/stop';
		$this->status=new dom_div;
		$this->append_child($this->status);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!is_array($this->editors))return;
		foreach($this->editors as $i => $e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$e->context=&$this->context;
			$e->keys=&$this->keys;
			$e->args=&$this->args;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i => $e)
			$e->bootstrap();
		$this->rootnode->endscripts['test_long_run']=
		"function status_fetch()".
		"{".
		"async_get('/ext/test_long_run.php',function(){if ((xmlHttp.readyState == 4)&&(xmlHttp.status == 200))\$i('".$this->status->id_gen()."').textContent=xmlHttp.responseText;});".
		"}".
		"setInterval('status_fetch();',1000);".
		"";
	}
	
	function handle_event($ev)
	{
		global $sql;
		switch($ev->rem_name)
		{
		case 'start':
				$running=$sql->q1("SELECT `value` FROM `test_long_run` WHERE id=2 LIMIT 1");
				if($running != 1.0)
				{
					$sql->query("INSERT INTO `test_long_run` (`id`,`value`) VALUES (2,1) ON DUPLICATE KEY UPDATE `value`=1");
					print "/*ok*/";
					$sql->logquerys=false;
					while(true)
					{
						$running=$sql->q1("SELECT `value` FROM `test_long_run` WHERE id=2 LIMIT 1");
						if($running != 1.0 )break;
						$sql->query("INSERT INTO `test_long_run` (`id`,`value`) VALUES (1,0) ON DUPLICATE KEY UPDATE `value`=`value`+1");
					}
				}else{
					$sql->query("INSERT INTO `test_long_run` (`id`,`value`) VALUES (2,0) ON DUPLICATE KEY UPDATE `value`=0");
					$running=$sql->q1("SELECT `value` FROM `test_long_run` WHERE id=0 LIMIT 1");
				}
			break;
		}
	}
}




$tests_m_array[]='test_long_run';






?>