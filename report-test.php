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
'font-size'=>'5mm',
'padding'=>'0mm'
);	

$page->exstyle['@page']=Array(
'margin'=>'0mm',
'padding'=>'0mm'
//'size'=>'57mm 31mm'
//,'size'=>'landscape'
);

class qres extends dom_any
{
	function __construct()
	{
		parent::__construct('table');
		$this->css_style['padding']='0px';
		$this->css_style['margin']='0px';
		$this->css_style['border']='0px';
		$this->css_style['border-collapse']='collapse';
		$this->row=new dom_tr;
		$this->cell=new dom_td;
		$this->cell->css_style['padding']='0px';
		$this->cell->css_style['margin']='0px';
		$this->cell->css_style['border']='0px';
		$this->data=new dom_statictext;
		unset($this->row->id);
		unset($this->cell->id);
		$this->append_child($this->row);
		$this->row->append_child($this->cell);
		$this->cell->append_child($this->data);
	}
	
	function html_inner()
	{
		global $sql;
		$qc=$this->qg->result();
		$res=$sql->query($qc);
		while($row=$sql->fetchn($res))
		{
			$this->row->html_head();
			foreach($row as $v)
			{
				$this->data->text=$v;
				$this->cell->html();
			}
			$this->row->html_tail();
		}
	}
}


$div=new dom_div;
$div->css_style['width']='190mm';
$q=new qres;
$qid=$q->id_gen();
$q->qg=new query_gen_ext('select');
$q->qg->from->exprs[]=new sql_column(NULL,'barcodes_raw',NULL,NULL);
$q->qg->what->exprs[]=new sql_column(NULL,NULL,'id','id');
$q->qg->what->exprs[]=new sql_column(NULL,NULL,'name','name');
$q->qg->what->exprs[]=new sql_column(NULL,NULL,'code','code');
$q->qg->lim_count=500;

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
	document.body.appendChild(r);
	document.body.removeChild(d);
	//alert(px280+','+px200);
	var tbl=\$i('".$qid."');
	tbl=tbl.firstChild;
	var cc=tbl.firstChild;
	var ah=0;
	while(cc!=null)
	{
		if(ah>=px_h)
		{
			ah=0;
			cc.style.pageBreakBefore='always';
			cc.style.backgroundColor='red';
		}
		ah+=cc.offsetHeight;
		cc.title=ah.toString()+' , '+px_h;
		cc=cc.nextSibling;
	}
	
};
";













$page->html();


?>