<?php
$profiler=microtime(true);
require_once('../lib/utils.php');
/*
core.js tests

*/
$staticjs=<<<aaa

function init()
{
	callback_uri='?a=1';
	setInterval('timerch(false);',1000);
}





fetchfuncs.push(function (o,objtype)
{
	if(objtype=='text' || objtype=='textarea')
	return function(obj)
	{
		document.getElementById("res").innerHTML="sending";
		return encodeURIComponent(obj.value);
	};
	if(objtype=='checkbox')
	return function(obj)
	{
		document.getElementById("res").innerHTML="sending";
		return encodeURIComponent(obj.checked?'1':'0');
	};
	if(objtype=='dropdown')
	return function(obj)
	{
		document.getElementById("res").innerHTML="sending";
		return encodeURIComponent(obj.value);
	};
	return null;
}
);

checkerfuncs.push(function genchecker(o,objtype)
{
	if(objtype=='text' || objtype=='textarea')
	{
		if((! o.oldval)&&(o.oldval != '') )
		{
			safe_alert(128,'what a...: ' + o.id + ' = ' + o.oldval);
			o['oldval']=o.value;
		}
		return function(obj)
		{
			if(obj.oldval==obj.value) return false;
			obj.oldval=obj.value;
			return true;
		}
	}
	if(objtype=='checkbox')
	{
		return function(obj)
		{
			return true;
		}
	}
	if(objtype=='dropdown')
	{
		return function(obj)
		{
			return true;
		}
	}
	return null;
}
);


//fix middle click paste in opera
function opera_fix(o)
{
		if((! o.oldval)&&(o.oldval != '') )
		{
			safe_alert(128,'fix: ' + o.oldval);
			o['oldval']=o.value;
		}
}


var cnt=0;
function safe_alert(l,a)
{
	if(l>=128)
	{
		document.getElementById("res").innerHTML= a + ' n:' + cnt;
		cnt++;
	}
}






aaa;



class editor_text
{
public
	$beforefocus='',
	$afterfocus='',
	$beforeblur='',
	$afterblur='',
	$beforekeypress='',
	$afterkeypress='',
	$beforeclick='',
	$afterclick='',
	$type='text'
	;
	function html($id,$val)
	{
		$type=$this->type;
		//safe_alert(\"activated \" + this.oldval);
		$send=urlencode($id);
		return "<input id='$id' type=text onfocus='$this->beforefocus;activatemon(this,\"$type\",&#039;$send&#039;);$this->afterfocus' onblur='$this->beforeblur;deactivatemon(this);$this->afterblur' value='$val' onmouseover='opera_fix(this);'>";
	}
	function into_div($id,$val)
	{
		$div='div_'.$id;
		return "<div style='display:inline;' id='$div'>".$this->html($id,$val)."</div>";
	}
}

class editor_checkbox extends editor_text
{
public
/*	$beforefocus='',
	$afterfocus='',
	$beforeblur='',
	$afterblur='',
	$beforekeypress='',
	$afterkeypress='',
	$beforeclick='',
	$afterclick=''*/
	$type='checkbox'
	;
	function html($id,$val)
	{
		$type=$this->type;
		$checked=($val=='1')?'checked':'';
		$send=urlencode($id);
		return "<input id='$id' type=checkbox onfocus='$this->beforefocus;activatemon(this,\"$type\",&quot;$send&quot;);$this->afterfocus' onblur='$this->beforeblur;deactivatemon(this);$this->afterblur' $checked onchange='timerch(true);'>";
	}
}

class editor_dropdown extends editor_text
{
public
	$type='dropdown',
	$options=''
	;
	function html($id,$val)
	{
		$type=$this->type;
		$opts='';
		$list=$this->options;
		foreach($list as $ind => $opt)
		{
			$opts .= "<option value='$ind' ";
			if($ind == $val) $opts .= 'selected';
			$opts .= ">$opt</option>";
		}
		
		$send=urlencode($id);
		return "<select id='$id' onfocus='$this->beforefocus;activatemon(this,\"$type\",\"$send\");$this->afterfocus' onblur='$this->beforeblur;deactivatemon(this);$this->afterblur' onchange='timerch(true);'>$opts</select>";
	}
}

class editor_textarea extends editor_text
{
public
/*	$beforefocus='',
	$afterfocus='',
	$beforeblur='',
	$afterblur='',
	$beforekeypress='',
	$afterkeypress='',
	$beforeclick='',
	$afterclick=''*/
	$type='textarea',
	$width='',
	$height=''
	;
	function html($id,$val)
	{
		$type=$this->type;
		$width=($this->width != '')?" cols='".$this->width."'":'';
		$height=($this->height != '')?" rows='".$this->height."'":'';
		$send=urlencode($id);
		return "<textarea $width $height id='$id' onfocus='$this->beforefocus;activatemon(this,\"$type\",\"$send\");$this->afterfocus' onblur='$this->beforeblur;deactivatemon(this);$this->afterblur' onmouseover='opera_fix(this);'>$val</textarea>";
	}
}



function textinput($id,$type,$val)
{
	global $toenable;
	$toenable[]=$id;
	return "<input id='$id' type=text onfocus='$this->beforefocus;activatemon(this,\"$type\");$this->afterfocus' onblur='$this->beforeblur;deactivatemon(this);$this->afterblur' value='$val' onmouseover='opera_fix(this);'>";
}

function checkbox($id,$type,$val)
{
	global $toenable;
	$toenable[]=$id;
	if($val=='1')$checked='checked';
	return "<input id='$id' type=checkbox onfocus='$this->beforefocus;activatemon(this,\"$type\");$this->afterfocus' onblur='$this->beforeblur;deactivatemon(this);$this->afterblur' $checked onchange='timerch(true);'>";
}


function dropdown($id,$type,$val,$list)
{
	global $toenable;
	$toenable[]=$id;
	$opts='';
	foreach($list as $ind => $opt)
	{
		$opts .= "<option value='$ind' ";
		if($ind == $val) $opts .= 'selected';
		$opts .= ">$opt</option>";
	}
	return "<select id='$id' onfocus='$this->beforefocus;activatemon(this,\"$type\");$this->afterfocus' onblur='$this->beforeblur;deactivatemon(this);$this->afterblur' onchange='timerch(true);'>$opts</select>";
}

/*
<input id='i1' type=text onfocus='activatemon(this,'text');' onblur='deactivatemon(this);'>
genfetch(obj,objtype)
 возвращает function(obj), возращающую строку для добавления к postdata
genchecker(obj,objtype)
 возвращает function(obj), возращающую true, если объект изменился и валиден
*/

function printhead()
{
	global $staticjs;
	$encoding='<meta http-equiv=content-type content="text/html; charset=UTF-8">';
	$styles=<<<aaa
<style type='text/css'>
tr.heading1 {
 	background-color:#000000;
 	color:#FFFFFF;
}
tr.odd1 {
 	background-color:#FFF0FF;
}
tr.even1 {
 	background-color:#FFFFFF;
}
input {
	font-size:14px;
}
</style>
aaa;
	$events=' onload="init();"';
	print "<html><head>$encoding<title>core.js test</title>$styles</head><body$events>";
	print "<script type='text/javascript' src='../js/core.js'></script>";
	print "<script type='text/javascript'>$staticjs</script>";
};




function obj_reload($id)
{
	$textinp=new editor_text();
	$checkbox=new editor_checkbox();
	$dropdown=new editor_dropdown();
	$textarea=new editor_textarea();
	switch($id)
	{
		case 'tt':return $textinp->into_div('tt',$_SESSION['tt']);
		case 'tt1':return $textinp->into_div('tt1',$_SESSION['tt1']);
		case 'tt4':return $checkbox->into_div('tt4',$_SESSION['tt4']);
		case 'tt5':	$dropdown->options=array("1" =>'option 1',"2" => 'option2',"3" =>'option3',"4"=>'option24');
				return print $dropdown->into_div('tt5',$_SESSION['tt5']);
		case 'tt8':return $textarea->into_div('tt8',$_SESSION['tt8']);
	}
	return '';
}



session_start();
if($_GET['a']=='1')
{
//async querys
	foreach($_POST as $key => $val)
		$_SESSION[$key]=$val;
	print 'document.getElementById("res").innerHTML="ok";';
	if(isset($_POST['tt5']))
	{
		switch($_POST['tt5'])
		{
			case '1':
				$_SESSION['tt']='prevedt';
				print "document.getElementById('div_tt').innerHTML='".js_escape(obj_reload('tt'))."';";
				print "document.getElementById('tt').focus();";
				exit;
			case '2':
				$_SESSION['tt']='krossafcheg';
				print "document.getElementById('div_tt').innerHTML='".js_escape(obj_reload('tt'))."';";
				print "document.getElementById('tt').focus();";
				exit;
			case '3':
				$_SESSION['tt']='test';
				print "document.getElementById('div_tt').innerHTML='".js_escape(obj_reload('tt'))."';";
				print "document.getElementById('tt').focus();";
				exit;
		}
	}
	exit;
}


$idcounter=isset($_SESSION['idcounter'])?intval($_SESSION['idcounter']):0;

class dom_node
{
	public $parentnode=NULL,$nodes=array(),$id=0;
	public $container='';
	
	function __construct($name)
	{
		global $idcounter;
		$this->id=$idcounter;
		$this->name=$name;
		$idcounter++;
	}
	function byid($id)
	{
		foreach($this->nodes as $n)
		{
			if($n->id===$id)return $n;
			$v=$n->byid($id);
			if($v != NULL) return $v;
		}
		return NULL;
	}
	
	function remove_child($node)
	{
		if (is_object($node))
		{
			//maybe $node is real node ref
			foreach($this->nodes as $n)
			{
				if($n!=$node) $new[]=$n;
				if($n==$node) $n->parentnode=NULL;
			}
			$this->nodes=$new;
			return;
		}
		//maybe $node is id string
		foreach($this->nodes as $n)
		{
			if($n->selftype!=$node) $new[]=$n;
			if($n->selftype==$node) $n->parentnode=NULL;
		}
		$this->nodes=$new;
		return;
	}
	
	function insert_before($before,$node)
	{
		if (is_object($node) && is_object($before))
		{
			//maybe $node is real node ref
			$node->parentnode=$this;
			foreach($this->nodes as $n)
			{
				if($n==$node) $new[]=$node;
				$new[]=$n;
			}
			$this->nodes=$new;
			return;
		}
	}
	
	function append_child($node)
	{
		/*
		if($a=$this->byid($node->id) != NULL)
		{
			$a->parentnode->remove_child($a);
		}
		*/
		$this->nodes[]=$node;
		$node->parentnode=$this;
	}
	
	
}

class root_container extends dom_node
{
	public $encoding,$scripts,$styles;
	public $title;
	function html()
	{
		$res='<html><head>';
		$res.='<title>'.htmlspecialchars($this->title).'</title>';
		if(isset($this->encoding) && $this->encoding != '')
			$res.="<meta http-equiv=content-type content=\"text/html; charset=".$this->encoding."UTF-8\">";
		if(isset($this->scripts) && is_array($this->scripts))
			foreach($this->scripts as $e) $res.="<script type='text/javascripte src='$e'></script>";
		if(isset($this->styles) && is_array($this->styles))
			foreach($this->styles as $e) $res.="<link rel=\"stylesheet\" href='$e' type='text/css'>";
		$res.="</head>";
		$res.="<body";
		if(isset($this->css_class))$res.=" class='".$this->css_class."'";
		if(isset($this->css_style))
			if(is_array($this->css_style))
			{
				$tmps='';
				foreach($this->css_style as $sel => $tx)$tmps.="$sel:$tx;";
				$res.=" style='".$tmps."'";
			}else
				$res.=" style='".$this->css_style."'";
		$res.=">";
		foreach($this->nodes as $node)$res.=$node->html();
		$res.="</body></html>";
		return $res;
	}
}

class div_container extends dom_node
{
	function html()
	{
		$res='<div';
		if(isset($this->id))$res.=" id='n".$this->id."'";
		if(isset($this->css_class))$res.=" class='".$this->css_class."'";
		if(isset($this->css_style))
			if(is_array($this->css_style))
			{
				$tmps='';
				foreach($this->css_style as $sel => $tx)$tmps.="$sel:$tx;";
				$res.=" style='".$tmps."'";
			}else
				$res.=" style='".$this->css_style."'";
		$res.=">";
		foreach($this->nodes as $node)$res.=$node->html();
		$res.="</div>";
		return $res;
	}
}

class text_node extends dom_node
{
	public $text;
	function html()
	{
		return htmlspecialchars($this->text);
	}
}

class table_node extends dom_node
{
}








//body
printhead();

$textinp=new editor_text();
$checkbox=new editor_checkbox();
$dropdown=new editor_dropdown();
$textarea=new editor_textarea();


print "<div style='border: 1px solid black;'>";
print $textinp->into_div('tt',$_SESSION['tt']);
print $textinp->into_div('tt1',$_SESSION['tt1']);
print $checkbox->into_div('tt4',$_SESSION['tt4']);
$dropdown->options=array("1" =>'option 1',"2" => 'option2',"3" =>'option3',"4"=>'option24');
print $dropdown->into_div('tt5',$_SESSION['tt5']);
print $textarea->into_div('tt8',$_SESSION['tt8']);

print "<div id=res></div>";
print "</div>";
print "<div style='width:100%;border:2px solid red;'>";
print "tetst";
print "</div>";
print "<div style='width:100px;height:100%;float:right;border:2px solid red;overflow:scroll;'>";
print "Script processing took ".(microtime(true)-$profiler)." sec<br>";
print $sql->qcnt.' querys, took '.$sql->querytime.' sec&#'.intval('0x21e7').';&#'.intval('0x21e9').';';
print "</div>";
print "</body></html>";
?>