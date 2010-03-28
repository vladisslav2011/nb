<?php
$profiler=microtime(true);
require_once("../lib/utils.php");
require_once("../lib/dom.php");

class dom_storable_test extends dom_node
{
	
}

class dom_complex_test extends dom_node
{
	public $value=0,$min=0,$max=10;
	function __construct($name='')
	{
		dom_node::__construct($name);
		$tmpdiv=new dom_div;
		$this->inp=new dom_textinput;
		$tmpdiv->append_child($this->inp);
		$this->sp_inc=new dom_span;
		$txt=new dom_statictext;
		$txt->text='[+]';
		$this->sp_inc->append_child($txt);
		$tmpdiv->append_child($this->sp_inc);
		$this->sp_dec=new dom_span;
		$txt=new dom_statictext;
		$txt->text='[-]';
		$this->sp_dec->append_child($txt);
		$tmpdiv->append_child($this->sp_dec);
		$this->css_style['width']='250px';
		$this->append_child($tmpdiv);
		$this->dis=new dom_div;
		$this->append_child($this->dis);
		$this->dis->css_style['width']='50px';
	}
	
	function afterlink()
	{
		$this->inp->attributes['onkeypress']=htmlspecialchars("setTimeout('check(\"".$this->inp->id_gen()."\",' + $this->min + ',' + $this->max + ',\"".$this->dis->id_gen()."\");',200);",ENT_QUOTES);
		$this->inp->attributes['onkeydown']=$this->inp->attributes['onkeypress'];
		$this->inp->css_style['display']='inline';
		$this->rootnode->inlinescripts['dom_complex_test']="function check(id,l,h,rid)
		{if(parseInt(document.getElementById(id).value)<l || parseInt(document.getElementById(id).value)>h)document.getElementById(id).style.backgroundColor='red'; else document.getElementById(id).style.backgroundColor='';document.getElementById(rid).innerHTML=document.getElementById(id).value;}";
		$this->dis->css_style['background-color']='white';
		$this->sp_inc->attributes['onclick']="document.getElementById(\"".$this->inp->id_gen()."\").value=parseInt(document.getElementById(\"".$this->inp->id_gen()."\").value)+1;setTimeout('check(\"".$this->inp->id_gen()."\",' + $this->min + ',' + $this->max + ',\"".$this->dis->id_gen()."\");',200);";
		$this->sp_dec->css_style=&$this->sp_inc->css_style;
		$this->sp_inc->css_style['color']='white';
		$this->sp_inc->css_style['font-weight']='bold';
		$this->sp_dec->attributes['onclick']="document.getElementById(\"".$this->inp->id_gen()."\").value=parseInt(document.getElementById(\"".$this->inp->id_gen()."\").value)-1;setTimeout('check(\"".$this->inp->id_gen()."\",' + $this->min + ',' + $this->max + ',\"".$this->dis->id_gen()."\");',200);";
	}
	
	function html()
	{
		$this->rootnode->out('<div');
		$this->rootnode->out($this->common_attributes());
		$this->rootnode->out(">");
		foreach($this->nodes as $node)$this->rootnode->out($node->html());
		$this->rootnode->out("</div>");
		//return $res;
	}
}



class dom_profiler extends dom_statictext
{
	function html()
	{
		global $profiler;
		$this->rootnode->out(microtime(true)-$profiler);
	}
}


class dom_resizeable_div extends dom_node
{
	function html()
	{
		$this->rootnode->out('<div');
		$this->rootnode->out($this->common_attributes());
		$this->rootnode->out(">");
		foreach($this->nodes as $node)$this->rootnode->out($node->html());
		$this->rootnode->out("</div>");
	//	return $res;
	}

}


function array_to_table($arr)
{
	$res=new dom_any;
	$res->node_name='table';
	$res->attributes['cellspacing']='0';
	$cnt=0;
	foreach($arr as $i => $v)
	{
		$tr=new dom_any;
		$tr->node_name='tr';
		$td=new dom_any;
		$td->node_name='td';
		$txt=new dom_statictext;
		$txt->text=$i;
		$td->append_child($txt);
		$tr->append_child($td);
		$td=new dom_any;
		$td->node_name='td';
		$txt=new dom_statictext;
		$txt->text=$v;
		$td->append_child($txt);
		$tr->append_child($td);
		$tr->attributes['width']=($cnt++)%2;
		$res->append_child($tr);
	}
	return $res;
}


function array_to_table_div($arr)
{
	$res=new dom_div;
	$res->css_class='table_div';
	$cnt=0;
	foreach($arr as $i => $v)
	{
		$tr=new dom_div;
		$td=new dom_div;
		$txt=new dom_statictext;
		$txt->text=$i;
		$td->append_child($txt);
		$tr->append_child($td);
		$td=new dom_div;
		$txt=new dom_statictext;
		$txt->text=$v;
		$td->append_child($txt);
		$tr->append_child($td);
		$tr->attributes['width']=($cnt++)%2;
		$res->append_child($tr);
	}
	return $res;
}








$test=new dom_root;
$test->title='preved';
$div=new dom_div;
$div->name='1';
$div->css_style['background-color']='red';
$div->css_style['width']='15em';
$div->css_style['height']='auto';
$div->css_style['position']='absolute';
$div->css_style['left']='50';
$div->css_style['top']='50';

$text=new dom_statichtml;
$text->text="<u>krossafcheg </u>\n<br>";
$test->append_child($div);
$div_id=$div->id;
$div=new dom_div;
$div->name='2';
$div->css_style='background-color:green;width:15em;height:40px;position:absolute;left:50;top:150;';
$div->css_toarray();
$tbl=array_to_table_div($div->css_style);
//$tbl=array_to_table($div->css_style);

$test->append_child($div);

$olddiv=$test->byid($div_id);
for($k=1;$k<=5;$k++)$olddiv->append_child($text);
$olddiv->css_style['left']=250;
$div->css_tostring();
$olddiv->attributes['onmousedown']='this.innerHTML += "fakamaka! <br>";';
$olddiv->attributes['onmouseup']='this.innerHTML += "muthafaka! <br>";';


$text=new dom_complex_test;
$text->value='krossafcheg';
$text->css_style['color']='gray';
$text->css_style['height']='auto';
$text->css_style['background-color']='black';
$test->append_child($text);

$test->append_child($tbl);

$ab=$tbl->id_gen();
$style_text=<<<aaaaa

body{
font-family:arial;
font-size:14px;
}

table#$ab{
display:block;position:absolute;left:500;top:20;background-color:yellow;border: 1px solid blue;border-collapse:collapse;
}
table#$ab td{
border:3px solid black;color:blue;
}
table#$ab tr[width="0"]{
background-color:#F0FFFF;
}

div.table_div{
display:block;position:absolute;left:500;top:20;background-color:yellow;border: 1px solid blue;border-collapse:collapse;width:400px;
}
div.table_div div[width="0"]{
background-color:white;overflow:hidden;width:auto;
}
div.table_div div[width="1"]{
overflow:hidden;width:auto;
}
div.table_div div div{
float:left;border:1px solid black;width:46%;
}


aaaaa;
$style_tag=new dom_any;
$style_tag->node_name='style';
$style_tag->attributes['type']='text/css';
$txt=new dom_statichtml;
$txt->text=$style_text;
$style_tag->append_child($txt);
//$test->insert_before($test->nodes[0],$style_tag);
$test->inlinestyles[0]=$style_text;


//$tbl->css_class='ab';

//$test->endscripts[]='alert("after page loading");';
//$test->inlinescripts[]='alert("inline before page loading");';
$prof=new dom_profiler;
$test->append_child($prof);
print $test->html();


?>