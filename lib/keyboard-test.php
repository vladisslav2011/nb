<?php


$ddc_tables['keyboard_test']=(object)
Array(
 'name' => 'keyboard_test',
 'cols' => Array(
  #Array('name' =>'', 'sql_type' =>'', 'sql_null' =>, 'sql_default' =>'', 'sql_sequence' => 0, 'sql_comment' =>NULL),
  Array('name' =>'user-agent',	'sql_type' =>'varchar(100)',  'sql_null' =>0, 'sql_default' =>'',		'sql_sequence' => 0),
  Array('name' =>'testn',	'sql_type' =>'smallint(2)',  'sql_null' =>0, 'sql_default' =>0,		'sql_sequence' => 0),
  Array('name' =>'keypress-result',	'sql_type' =>'text',  'sql_null' =>0),
  Array('name' =>'keydown-result',	'sql_type' =>'text',  'sql_null' =>0),
  Array('name' =>'keyup-result',	'sql_type' =>'text',  'sql_null' =>0),
  Array('name' =>'result',	'sql_type' =>'text',  'sql_null' =>0),
 ),
 'keys' => Array(
#  Array('key' =>'PRIMARY', 'name' =>'', 'sub' => NULL)
  Array('key' =>'PRIMARY', 'name' =>'user-agent', 'sub' => NULL),
  Array('key' =>'PRIMARY', 'name' =>'testn', 'sub' => NULL)
 )
);
if($_GET['init']=='init')
	ddc_gentable_o($ddc_tables['keyboard_test'],$sql);


class keyboard_test extends dom_div
{
	const TEST_NUM=4;
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->btn=new dom_any('button');
		$this->append_child($this->btn);
		$this->btn->append_child(new dom_statictext('---'));
		$this->append_child(new dom_statictext("Press and hold 'n',Enter,Shift,Arrow down"));
		$this->tbl=new dom_table_x(4,5);
		$this->tbl->css_class='keyboard_test';
		$this->append_child($this->tbl);
		$this->tbl->cells[0][0]->append_child(new dom_statictext('test'));
		$this->tbl->cells[0][1]->append_child(new dom_statictext('keydown'));
		$this->tbl->cells[0][2]->append_child(new dom_statictext('keypress'));
		$this->tbl->cells[0][3]->append_child(new dom_statictext('keyup'));
		$this->tbl->cells[1][0]->append_child(new dom_statictext('alfa'));
		$this->tbl->cells[2][0]->append_child(new dom_statictext('shift'));
		$this->tbl->cells[3][0]->append_child(new dom_statictext('enter'));
		$this->tbl->cells[4][0]->append_child(new dom_statictext('arrow'));
		for($y=1;$y<=keyboard_test::TEST_NUM;$y++)
			for($x=1;$x<=3;$x++)
				$this->tbl->cells[$y][$x]->append_child(new dom_statictext('did not fire'));
		editor_generic::addeditor('submit',new editor_button);
		$this->append_child($this->editors['submit']);
		$this->editors['submit']->attributes['value']='store';
		
		
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
		$this->editors['submit']->attributes['onclick']="chse.send_or_push({static:'".$this->editors['submit']->send."',val:js2php(\$i('".js_escape($this->btn->id_gen())."').result_struct),c_id:this.id});";
	}
	
	function html_inner()
	{
		$this->btn->attributes['onkeydown']=
			"return keyboard_test_btn_key(event,this,0);";
		$this->btn->attributes['onkeypress']=
			"return keyboard_test_btn_key(event,this,1);";
		$this->btn->attributes['onkeyup']=
			"return keyboard_test_btn_key(event,this,2);";
		parent::html_inner();
		$sn='';
		for($k=1;$k<=keyboard_test::TEST_NUM;$k++)
		{
			$sn.="btn.result_divs[".$k."]=new Array('".$this->tbl->cells[$k][1]->id_gen()."',";
			$sn.="'".$this->tbl->cells[$k][2]->id_gen()."',";
			$sn.="'".$this->tbl->cells[$k][3]->id_gen()."'";
			$sn.=");";
		}
		$this->rootnode->endscripts[]=
			"(function(){".
			"var btn=\$i('".$this->btn->id_gen()."');".
			"btn.result_struct=new Array();".
			"btn.result_divs=new Array();".
			$sn.
			"btn.focus();".
			"})();";
	}
	
	
	function handle_event($event)
	{
		global $sql;
		if($event->rem_name=='submit')
		{
			$r=unserialize($_POST['val']);
			//print 'alert(\''.js_escape(serialize($r[1][0]['ok'])).'\');';
			//print 'alert(\''.js_escape(serialize($t)).'\');';
			for($y=1;$y<=keyboard_test::TEST_NUM;$y++)
			{
				$q=new query_gen_ext('INSERT UPDATE');
				$q->into->exprs[]=new sql_column(NULL,'keyboard_test');
				$q->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'user-agent'),
					new sql_immed($_SERVER['HTTP_USER_AGENT'])
					));
				$q->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'testn'),
					new sql_immed($y)
					));
				$q->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'keypress-result'),
					new sql_immed(($r[$y][0]['ok']==1)?($r[$y][0]['cnt'].'/'.$r[$y][0]['kc'].'/'.$r[$y][0]['cc'].'/'.$r[$y][0]['wh']):'n')
					));
				$q->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'keydown-result'),
					new sql_immed(($r[$y][1]['ok']==1)?($r[$y][1]['cnt'].'/'.$r[$y][1]['kc'].'/'.$r[$y][1]['cc'].'/'.$r[$y][1]['wh']):'n')
					));
				$q->set->exprs[]=new sql_expression('=',Array(
					new sql_column(NULL,NULL,'keyup-result'),
					new sql_immed(($r[$y][2]['ok']==1)?($r[$y][2]['cnt'].'/'.$r[$y][2]['kc'].'/'.$r[$y][2]['cc'].'/'.$r[$y][2]['wh']):'n')
					));
				$sql->query($q->result());
			}
		}
		editor_generic::handle_event($event);
	}
}

$tests_m_array['sandbox']['keyboard_test']='keyboard_test';



















?>