<?php

set_include_path($_SERVER['DOCUMENT_ROOT']);
session_start();
//$_SESSION['uid']=0;
//$_SESSION['sql_design']=false;
$profiler=microtime(true);
require_once('lib/ddc_meta.php');
require_once('lib/dom.php');
require_once('lib/settings.php');
require_once('lib/commctrls.php');
require_once('lib/query_gen.php');





$page=new dom_root_print;
$page->title='Тестовый отчет';
$page->scripts[]='/js/core.js';
$page->scripts[]='/js/commoncontrols.js';


$page->exstyle['*']=Array(
'margin'=>'0mm',
'padding'=>'0mm'
);	
$page->exstyle['div']=Array(
'font-family'=>'serif,sans',
'font-size'=>'5mm',
'padding'=>'0mm'
);	
$page->exstyle['td>div']=Array(
'overflow'=>'hidden',
'height'=>'16mm'
);	
$page->exstyle['tr.head>td>div']=Array(
'border'=>'0.5mm solid black',
'border-collapse'=>'collapse',
);	
$page->exstyle['tr.even']=Array(
'background-color'=>'#ffeeee'
);	
$page->exstyle['tr.odd']=Array(
'background-color'=>'white'
);	

$page->exstyle['@page']=Array(
'margin'=>'0mm',
'padding'=>'0mm'
//'size'=>'57mm 31mm'
//,'size'=>'landscape'
);

class qres extends dom_any
{
	function __construct($qg)
	{
		parent::__construct('div');
		$this->qg=$qg;
		$this->css_style['padding']='0px';
		$this->css_style['margin']='0px';
		$this->css_style['border']='0px';
		$this->table=new dom_table;
		$this->row=new dom_tr;
		$this->hrow=new dom_tr;
		$this->hrow->css_class='head';
		$this->table->css_style['page-break-after']='always';
		$this->table->css_style['padding']='0px';
		$this->table->css_style['margin']='0px';
		$this->table->css_style['border']='0px';
		$this->table->css_style['border-collapse']='collapse';
		$this->row->css_style['height']='8mm';
		foreach($this->qg->what->exprs as $e)
		{
			if($e->alias == '')
			{
				print "You should specify an alias for each column.";
				exit;
			}
			$this->txts[$e->alias]=new dom_statictext;
			$this->htxts[$e->alias]=new dom_statictext($e->alias);
			$td=new dom_td;unset($td->id);
			$div=new dom_div;unset($div->id);
			$div->append_child($this->txts[$e->alias]);
			$td->append_child($div);
			$this->row->append_child($td);
			$td=new dom_td;unset($td->id);
			$div=new dom_div;unset($div->id);
			$div->append_child($this->htxts[$e->alias]);
			$td->append_child($div);
			$this->hrow->append_child($td);
			
		}
		
		unset($this->row->id);
		unset($this->table->id);
		$this->append_child($this->table);
		$this->table->append_child($this->hrow);
		$this->table->append_child($this->row);
		$this->row_height=16.0;
		$this->page_height=270.0;
	}
	
	function html_inner()
	{
		global $sql;
		$ch=0;
		$qc=$this->qg->result();
		$res=$sql->query($qc);
		$oe=true;
		while($row=$sql->fetcha($res))
		{
			if($ch+$this->row_height>$this->page_height)
			{
				$this->table->html_tail();
				$ch=0;
			}
			if($ch==0)
			{
				$this->table->html_head();
				$this->hrow->html();
				$ch+=$this->row_height;
			}
			$ch+=$this->row_height;
			foreach($row as $k => $v)
			{
				$this->txts[$k]->text=$v;
			}
			if($oe)
			{
				$oe=false;
				$this->row->css_class='odd';
			}else{
				$oe=true;
				$this->row->css_class='even';
			}
			$this->row->html();
		}
		$this->table->html_tail();
	}
}


$div=new dom_div;
$div->css_style['width']='190mm';
$qg=new query_gen_ext('select');
$qg->from->exprs[]=new sql_column(NULL,'barcodes_raw',NULL,NULL);
$qg->what->exprs[]=new sql_column(NULL,NULL,'id','id');
$qg->what->exprs[]=new sql_column(NULL,NULL,'name','name');
$qg->what->exprs[]=new sql_column(NULL,NULL,'code','code');
$qg->lim_count=500;

$q=new qres($qg);
$qid=$q->id_gen();
$div->append_child($q);
$page->append_child($div);

$page->endscripts[]=
"
window.onload=function()
{
	var d=document.createElement('div');
	d.appendChild(document.createTextNode(' '));
	d.style.width='1000mm';
	d.style.height='1000mm';
	document.body.appendChild(d);
	var r=document.createTextNode('1000mm is '+d.offsetWidth.toString()+'px');
	var px_h=Math.round((d.offsetWidth*260)/1000);
	var px_w=Math.round((d.offsetWidth*200)/1000);
	//document.body.appendChild(r);
	document.body.removeChild(d);
	//alert(px280+','+px200);
	
};
";













$page->html();


?>