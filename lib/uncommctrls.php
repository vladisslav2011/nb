<?php

//###############################################################################################3
//##################################    editor_text_autosuggest_query   #########################3
//###############################################################################################3




class editor_text_autosuggest_query extends editor_text_autosuggest
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->list_class='editor_text_autosuggest_query_list';
	}
/*	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		parent::bootstrap();
	}*/
}





class editor_text_autosuggest_query_list extends editor_text_autosuggest_list_example
{
	function __construct()
	{
		parent::__construct();
		unset($this->list_items);
	}
	
	function html_inner()
	{
		global $sql;
		$a=Array();
		//print_r($_SESSION);
		if(isset($this->context[$this->long_name]['query']))
		{
			$q=unserialize($this->context[$this->long_name]['query']);
			$qc=$q->result();
			$this->list_items=$sql->fetchm($sql->query($qc));
		}
		if(isset($this->context[$this->long_name]['rawquery']))
		{
			
			$qc=$this->context[$this->long_name]['rawquery'];
			$this->list_items=$sql->fetchm($sql->query($qc));
		}
		
		if(!is_array($this->list_items))
		{
			$this->list_items=Array();
		}
		#	$this->list_items=Array($this->long_name);
		#	$this->list_items=Array($qc);
		foreach($this->list_items as $v)
		{
			if(isset($this->context[$this->long_name]['dofilter']))
				if(!preg_match('/'.preg_quote($this->input_part,'/').'/',$v))continue;
			if($this->input_part=='')
			{
				$this->args['i']=htmlspecialchars($v).'&nbsp;';
			}else{
				$v1=htmlspecialchars($this->input_part);
				$v2=htmlspecialchars($v);
				$this->args['i']=preg_replace('/'.preg_quote($v1,'/').'/','<span style=\'font-size:1.2em;\'>'.$v1.'</span>',$v2).'&nbsp;';
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


//###############################################################################################3
//##################################    editor_search_pick              #########################3
//###############################################################################################3

class editor_search_pick extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->rval=new dom_any_noterm('input');
		$this->rval->css_style['width']='4em';
		$this->append_child($this->rval);
		$this->vval=new dom_any('button');
		$this->append_child($this->vval);
		$this->vdiv=new dom_div;
		$this->vval->append_child($this->vdiv);
		$this->vval_text=new dom_statictext;
		$this->vdiv->append_child($this->vval_text);
		$this->bottom_div=new dom_div;
		$this->append_child($this->bottom_div);
		$this->bottom_div->css_style['position']='absolute';
		$this->bottom_div->css_style['border']='1px solid blue';
		$this->bottom_div->css_style['background']='#F0FFFF';
		$this->bottom_div->css_style['display']='none';
		//$this->filter=new dom_any_noterm('input');
		$this->filter=new editor_text;
		editor_generic::addeditor('filter',$this->filter);
		$this->bottom_div->append_child($this->filter);
		
		editor_generic::addeditor('first_page',new editor_button);
		$this->bottom_div->append_child($this->editors['first_page']);
		$this->editors['first_page']->attributes['value']='<<';
		editor_generic::addeditor('prev_page',new editor_button);
		$this->editors['prev_page']->attributes['value']='<';
		$this->bottom_div->append_child($this->editors['prev_page']);
		editor_generic::addeditor('next_page',new editor_button);
		$this->bottom_div->append_child($this->editors['next_page']);
		$this->editors['next_page']->attributes['value']='>';
		
		
		
		$this->suggestions=new dom_div;
		$this->bottom_div->append_child($this->suggestions);
		$this->suggestions->css_style['min-width']='50px';
		$this->suggestions->css_style['max-height']='200px';
		$this->suggestions->css_style['overflow']='auto';
		$this->main=$this->rval;
	}
	
	function bootstrap()
	{
		$rval_id=$this->rval->id_gen();
		$vval_id=$this->vdiv->id_gen();
		$suggestions_id=$this->suggestions->id_gen();
		$filter_id=$this->filter->main_id();
		
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name]['oid']=$this->oid;
		$this->context[$this->long_name]['io_class']=get_class($this->io);
		$this->context[$this->long_name.'.filter']['var']='filter';
		$this->context[$this->long_name]['vval_id']=$vval_id;
		$this->context[$this->long_name]['rval_id']=$rval_id;
		$this->context[$this->long_name]['suggestions_id']=$suggestions_id;
		$this->context[$this->long_name]['filter_id']=$filter_id;
		$this->context[$this->long_name]['suggestions_page']=0;
		editor_generic::bootstrap_part();
		$this->rval->attributes['onfocus']="chse.activatemon({obj:this,objtype:'editor_text',static:".$this->send."});";
		$this->rval->attributes['onblur']='chse.latedeactivate(this);';
		$this->vval->attributes['onclick']="\$i('".js_escape($this->bottom_div->id_gen())."').style.display='block';".
			"var d=\$i('".js_escape($filter_id)."');d.focus();".
			"if(typeof(editor_search_pick)=='undefined')editor_search_pick=new Array();".
			"if(typeof(editor_search_pick['".js_escape($this->long_name)."'])!='undefined')".
			"{d.value=editor_search_pick['".js_escape($this->long_name)."'];};".
			"d.oldval+='1';chse.timerch();";
		$this->rval->attributes['value']=$this->args[$this->context[$this->long_name]['var']];
		$this->editors['first_page']->val_js="0";
		$this->editors['next_page']->val_js="\$i('".js_escape($suggestions_id)."').page_offset";
		$this->editors['prev_page']->val_js="\$i('".js_escape($suggestions_id)."').page_offset";
		foreach($this->editors as $e)
		{
			$e->context=&$this->context;
			#$e->keys=Array();
			$e->keys=&$this->keys;
			$e->args=Array();
			$e->oid=$this->oid;
		}
		foreach($this->editors as $e)
			$e->bootstrap();
		$hide_prev_focus="var suggestions=\$i('".js_escape($suggestions_id)."');if(suggestions.hide_timeout){clearTimeout(suggestions.hide_timeout);suggestions.hide_timeout=null;};";
		$hide_prev_blur="var suggestions=\$i('".js_escape($suggestions_id)."');if(suggestions.hide_timeout)clearTimeout(suggestions.hide_timeout);suggestions.hide_timeout=setTimeout('".js_escape("\$i('".js_escape($this->bottom_div->id_gen())."').style.display='none';")."',200);".
			"var d=\$i('".js_escape($filter_id)."');".
			"if(typeof(editor_search_pick)=='undefined')editor_search_pick=new Array();".
			"editor_search_pick['".js_escape($this->long_name)."']=d.value;"
		;
		$this->filter->main->attributes['onfocus'].=$hide_prev_focus;
		$this->filter->main->attributes['onblur'].=$hide_prev_blur;
		$this->filter->main->attributes['onkeypress']="editor_text_autosuggest_keypress(this,event,'".js_escape($filter_id)."','".js_escape($rval_id)."','".js_escape($suggestions_id)."');";
		$this->editors['first_page']->attributes['onfocus']=$hide_prev_focus;
		$this->editors['first_page']->attributes['onblur']=$hide_prev_blur;
		$this->editors['first_page']->attributes['onclick'].="var suggestions=\$i('".js_escape($suggestions_id)."');suggestions.page_offset=0;";
		$this->editors['first_page']->attributes['onkeypress']="editor_text_autosuggest_keypress(\$i('".js_escape($filter_id)."'),event,'".js_escape($filter_id)."','".js_escape($rval_id)."','".js_escape($suggestions_id)."');";
		
		$this->editors['prev_page']->attributes['onfocus']=$hide_prev_focus;
		$this->editors['prev_page']->attributes['onblur']=$hide_prev_blur;
		$this->editors['prev_page']->attributes['onclick']="var suggestions=\$i('".js_escape($suggestions_id)."');if(suggestions.page_offset)suggestions.page_offset-=1;else suggestions.page_offset=0;".$this->editors['prev_page']->attributes['onclick'];
		$this->editors['prev_page']->attributes['onkeypress']="editor_text_autosuggest_keypress(\$i('".js_escape($filter_id)."'),event,'".js_escape($filter_id)."','".js_escape($rval_id)."','".js_escape($suggestions_id)."');";
		
		$this->editors['next_page']->attributes['onfocus']=$hide_prev_focus;
		$this->editors['next_page']->attributes['onblur']=$hide_prev_blur;
		$this->editors['next_page']->attributes['onclick']="var suggestions=\$i('".js_escape($suggestions_id)."');if(suggestions.page_offset)suggestions.page_offset+=1;else suggestions.page_offset=1;".$this->editors['next_page']->attributes['onclick'];
		$this->editors['next_page']->attributes['onkeypress']="editor_text_autosuggest_keypress(\$i('".js_escape($filter_id)."'),event,'".js_escape($filter_id)."','".js_escape($rval_id)."','".js_escape($suggestions_id)."');";
		
		
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore($this->rval,$this->rval);
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore($this->rval,$this->vval);
		
		
	}
	
	
	function html_inner()
	{
		$this->editors['filter']->args['filter']=$this->rootnode->setting_val($this->oid,$this->long_name.'!filter','');
		$this->rval->attributes['value']=$this->args[$this->context[$this->long_name]['var']];
		if(isset($this->context[$this->long_name]['var0']))
			$this->vval_text->text=$this->args[$this->context[$this->long_name]['var0']];
		else
		{
			#$a=$this->io->to_hr($this,$this->rval->attributes['value']);
			$a=$this->io->to_hr($this,$this->args[$this->context[$this->long_name]['var']]);
			#$this->vval_text->text=$a[1];
			$this->vval_text->text=$a;
		}
		$this->vval_text->text.='>';
		//print_r($this->rootnode->settings_array);
		parent::html_inner();
	}
	
	function handle_event($ev)
	{
		global $sql;
		$oid=$ev->context[$ev->long_name]['oid'];
		$customid=$ev->context[$ev->parent_name]['htmlid'];
		$io_class=$ev->context[$ev->parent_name]['io_class'];
		$setting_tool=new settings_tool;
		$filter_val=$sql->fetch1($sql->query($setting_tool->single_query($oid,$ev->parent_name.'!filter',$_SESSION['uid'],0)));

		//function single_query($oid,$setting,$uid,$preset,$flags='')

		//$oid=$ev->context[$io_name]['oid'];
		//print 'oid='.$oid;
		//print $ev->parent_name.','.$ev->name.','.$ev->rem_name.':';
		$this->io=new $io_class;
		$pl=new editor_search_pick_list;
		$pl->io=&$this->io;
		$pl->name=$ev->parent_name;
		$pl->long_name=$ev->parent_name;
		$pl->context=&$ev->context;
		$pl->keys=&$ev->keys;
		$pl->text_inp=$ev->context[$ev->parent_name]['rval_id'];
		$pl->cont_inp=$ev->context[$ev->parent_name]['filter_id'];
		switch($ev->rem_name)
		{
		case $ev->name:
			$v=$_POST['val'];
			$hr=$this->io->to_hr($pl,$v);
			print "var mm=\$i('".js_escape($ev->context[$ev->parent_name]['vval_id'])."');mm.textContent='".js_escape($hr.'>')."';";
			print "mm.setAttribute('title','".js_escape($hr)."');";
			print "\$i('".js_escape($ev->context[$ev->parent_name]['rval_id'])."').style.backgroundColor='white';";
			return;
			break;
		case 'filter':
			$pl->filter_val=$_POST['val'];
			
			$sql->query($setting_tool->set_query($oid,$ev->parent_name.'!filter',$_SESSION['uid'],0,$pl->filter_val));
			$pl->bootstrap();
			print "var sugg=\$i('".js_escape($ev->context[$ev->parent_name]['suggestions_id'])."');sugg.innerHTML=";
			reload_object($pl);
			$js='';
			foreach($pl->result_array as $v)
			{
				if($js!='')$js.=',';
				$js.='{id:\''.js_escape($v->id).'\',val:\''.js_escape($v->val).'\'}';
			}
			print 'var filter=$i(\''.js_escape($ev->context[$ev->parent_name]['filter_id']).'\');';
			print 'filter.as_objects=['.$js.'];';
			print 'filter.as_id = null;';
			print 'filter.page_offset = 0;';
			//print "alert((findPosY(sugg)+sugg.offsetHeight)+' '+(window.pageYOffset+window.innerHeight));";
			print "if((findPosY(sugg)+sugg.offsetHeight) > (window.pageYOffset+window.innerHeight))window.scroll(0,findPosY(sugg)+sugg.offsetHeight-window.innerHeight);";
			//print 'filter.scrollIntoView();';
			
			break;
		case 'next_page':
		case 'prev_page':
		case 'first_page':
			$pl->filter_val=$filter_val;
			$pl->io->page_offset=$_POST['val'];
			//print "alert('".$_POST['val']."');";
			$pl->bootstrap();
			print "\$i('".js_escape($ev->context[$ev->parent_name]['suggestions_id'])."').innerHTML=";
			reload_object($pl);
			$js='';
			foreach($pl->result_array as $v)
			{
				if($js!='')$js.=',';
				$js.='{id:\''.js_escape($v->id).'\',val:\''.js_escape($v->val).'\'}';
			}
			print '$i(\''.js_escape($ev->context[$ev->parent_name]['filter_id']).'\').as_objects=['.$js.'];';
			print '$i(\''.js_escape($ev->context[$ev->parent_name]['filter_id']).'\').as_id = null;';
			break;
		}
		
		editor_generic::handle_event($ev);
	}
}


//###############################################################################################3
//##################################   editor_search_pick_list   ################################3
//###############################################################################################3


class editor_search_pick_list extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->css_style['border']='1px solid blue';
		$this->css_style['background']='white';
		$this->tr=new dom_tr;
		$this->tr->css_style['border']='1px solid gray';
		$this->td=new dom_td;
		$this->append_child($this->tr);
		$this->tr->append_child($this->td);
		editor_generic::addeditor('text',new editor_statichtml);
		$this->td->append_child($this->editors['text']);
		$this->etype='editor_search_pick';
		$this->args=Array();
		$this->keys=Array();
	}
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->context[$this->long_name.'.text']['var']='i';
		foreach($this->editors as $e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
		}
		
	}
	
	function setup_tr($it)
	{
		
		$text_inp=js_escape($this->text_inp);
		$cont_inp=js_escape($this->cont_inp);
		$it_e=js_escape($it);
		$this->tr->attributes['onmouseover']="editor_text_autosuggest_list_mouseover('".$text_inp."','".$cont_inp."','".$it_e."');";
		$this->tr->attributes['onmouseout']="editor_text_autosuggest_list_mouseout('".$text_inp."','".$cont_inp."','".$it_e."');";
		//$this->tr->attributes['onclick']=
		$click=
		"var text_inp=\$i('".js_escape($this->text_inp)."');".
		"var cont_inp=\$i('".js_escape($this->cont_inp)."');".
		"if(cont_inp.as_objects)".
		"{".
			"if(cont_inp.as_id || cont_inp.as_id==0)".
			"{".
				"text_inp.value=cont_inp.as_objects[cont_inp.as_id].val;".
				"text_inp.focus();".
			"};".
		"}".
		"";
		$this->tr->attributes['onmousedown']="event.preventDefault();\$i('".js_escape($this->cont_inp)."').focus();";
		$this->tr->attributes['onmouseup']="editor_text_autosuggest_list_mouseup('".$text_inp."','".$cont_inp."','".$it_e."');";
		$this->tr->css_style['cursor']='pointer';

	}
	
	function html_inner()
	{
		$a=Array();
		$this->io->get_list($this);
		while($v=$this->io->next())
		{
			$this->args['i']=htmlspecialchars($v[1]);
			$this->id_alloc();
			foreach($this->editors as $e)$e->bootstrap();
			unset($it);
			$it->id=$this->tr->id_gen();
			$it->val=$v[0];
			$this->setup_tr(count($a));
			$a[]=$it;
			$this->tr->attributes['title']=$v[0];
			$this->tr->html();
		}
		$this->result_array=&$a;
	}

}


class editor_search_pick_def_io
{
	function __construct()
	{
		$this->vals=Array(
			1 => 'One',
			2 => 'Two',
			3 => 'Three',
			4 => 'Four',
			5 => 'Five',
			6 => 'Six',
			7 => 'Seven',
			8 => 'Eight',
			9 => 'Nine',
			10 => 'Ten',
			11 =>'Eleven',
			12 =>'Twelve',
			0 =>'zero'
		);
	}
	
	function get_list($obj)//$obj->ev is catched event, so it is possible to fetch $obj->ev->context
	{
		unset($this->res);
		$this->res=Array();
		foreach($this->vals as $i => $v)
			if(preg_match('/'.preg_quote($obj->filter_val).'/',$v))
				$this->res[$i]=$v;
		reset($this->res);
		for($k=0;$k<$this->page_offset*6;$k++)$dummy=each($this->res);
		$this->cnt=6;
	}
	
	function next()
	{
		if($this->cnt)
		$this->cnt--;
		if($this->cnt)
			return each($this->res);
		else
			return NULL;
	}
	
	
	function to_hr($obj,$v)//$obj->ev is catched event, so it is possible to fetch $obj->ev->context
	{
		return $this->vals[$v];
	}
}




class editor_search_pick_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		for($k=1;$k<=10;$k++)
		{
			editor_generic::addeditor('t'.$k,new editor_search_pick);
			$this->append_child($this->editors['t'.$k]);
		}
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		foreach($this->editors as $i=>$e)
		{
			$this->editors[$i]->io=new editor_search_pick_def_io;
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}


$tests_m_array['complex']['editor_search_pick_test']='editor_search_pick_test';


##########################################################################################################################################
##########################################################################################################################################
##########################################################################################################################################

class editor_search_pick_sqltest_io
{
	function __construct()
	{
		global $sql;
		$this->sql=&$sql;
		$this->qa=new query_gen_ext;
		$this->qa->from->exprs[]=new sql_column(NULL,'barcodes_raw');
		$this->qa->what->exprs[]=new sql_column(NULL,NULL,'id');
		$this->qa->what->exprs[]=new sql_column(NULL,NULL,'name');
		$this->qa->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,NULL,'isown'),new sql_immed(0)));
		$this->qa->lim_count=9;
		
	}
	
	function get_list($obj)
	{
		if($this->res)$this->sql->free($this->res);
		unset($this->res);
		$fltr=preg_replace('/  +/',' ',$obj->filter_val);
		$fltr=preg_replace('/%/','\\%',$fltr);
		$fltr=preg_replace('/^ /','',$fltr);
		$fltr=preg_replace('/ $/','',$fltr);
		$fltr=preg_replace('/_/','\\_',$fltr);
		$fltr=preg_replace('/ /','%',$fltr);
		$fltr='%'.$fltr.'%';
		$qq=clone $this->qa;
		if($fltr != '')$qq->where->exprs[]=new sql_expression('LIKE',Array(new sql_column(NULL,NULL,'name'),new sql_immed($fltr)));
		$qq->lim_offset=$this->qa->lim_count*$this->page_offset;
		$this->res=$this->sql->query($qq->result());
		return;
		
		if(is_array($this->vals))
		foreach($this->vals as $i => $v)
			if(preg_match('/'.preg_quote($obj->filter_val).'/',$v))
				$this->res[$i]=$v;
		reset($this->res);
		for($k=0;$k<$this->page_offset*6;$k++)$dummy=each($this->res);
		$this->cnt=6;
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
		$qq=clone $this->qa;
		$qq->where->exprs[]=new sql_expression('=',Array(new sql_column(NULL,'id'),new sql_immed($v)));
		$res=$this->sql->query($qq->result());
		if($res)
		{
			$r=$this->sql->fetchn($res);
			$this->sql->free($res);
			return $r[1];
		} else return '';
	}
}




class editor_search_pick_b_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		for($k=1;$k<=13;$k++)
		{
			editor_generic::addeditor('t'.$k,new editor_search_pick);
			$this->append_child($this->editors['t'.$k]);
			$this->editors['t'.$k]->vval->css_style['max-width']='15em';
			$this->editors['t'.$k]->vval->css_style['max-height']='2em';
			$this->editors['t'.$k]->vval->css_style['overflow']='hidden';
			$this->editors['t'.$k]->rval->css_style['width']='3em';
		}
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		$cc=10;
		foreach($this->editors as $i=>$e)
		{
			$this->editors[$i]->io=new editor_search_pick_sqltest_io;
			#$this->context[$this->long_name.'.'.$i]['var0']='';
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$this->args[$i]=$cc++;
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}


$tests_m_array['complex']['editor_search_pick_b_test']='editor_search_pick_b_test';








######################################################################################################################
######################################################################################################################
######################################################################################################################

class editor_file_upload extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->form=new dom_any('form');
		$this->form->attributes['method']='post';
		$this->form->attributes['action']='/ext/file.php';
		$this->form->attributes['enctype']='multipart/form-data';
		$this->form->attributes['target']='';
		$this->file_inp=new dom_any_noterm('input');
		$this->file_inp->attributes['type']='file';
		$this->file_inp->attributes['name']='file1';
		//$this->file_inp->attributes['size']='1';
		//$this->file_inp->css_style['width']='100%';
		$this->form->append_child($this->file_inp);
		$this->type_hidden=new dom_any_noterm('input');
		$this->type_hidden->attributes['name']='rtype';
		$this->type_hidden->attributes['type']='hidden';
		$this->type_hidden->attributes['value']='test';
		$this->form->append_child($this->type_hidden);
		$this->subm_btn=new dom_any_noterm('input');
		$this->subm_btn->attributes['type']='submit';
		$this->subm_btn->attributes['value']='upload';
		$this->form->append_child($this->subm_btn);
		$this->append_child($this->form);
		//$this->form->attributes['onsubmit']='this.firstChild.style.display=\'none\'';
		$this->res=new dom_any('iframe');
		$this->append_child($this->res);
		$this->res->css_style['width']='10em';
		$this->res->css_style['height']='4em';
		$this->res->css_style['display']='none';
		$this->res_div=new dom_div;
		$this->append_child($this->res_div);
		$this->res_div->css_style['display']='none';
		$txt=new dom_statictext;
		$txt->text='Uploading.....';
		$this->res_div->append_child($txt);
		$this->main=$this->file_inp;
		
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap_part();
/*		
		$this->main->attributes['onfocus']='';
		$this->main->attributes['onblur']='';
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();*/
	}
	
	function html_inner()
	{
		$this->subm_btn->attributes['onclick']="\$i('".$this->res->id_gen()."').is_uploading=true;".
			"\$i('".$this->res_div->id_gen()."').style.display='';".
			"\$i('".$this->file_inp->id_gen()."').style.display='none';\$i('".$this->subm_btn->id_gen()."').style.display='none';";
		$reset=
			"\$i('".$this->res_div->id_gen()."').style.display='none';".
			"\$i('".$this->file_inp->id_gen()."').value='';".
			"\$i('".$this->file_inp->id_gen()."').style.display='';\$i('".$this->subm_btn->id_gen()."').style.display='';";
		if(isset($this->normal_postback))
		{
			$this->res->attributes['onload']="if(this.is_uploading){".
				"chse.send_or_push({static:".$this->send.",val:this.contentWindow.document.firstChild[text_content],c_id:this.id});".
				"\$i('".$this->res_div->id_gen()."').style.display='none';".
				"\$i('".$this->file_inp->id_gen()."').value='';".
				"\$i('".$this->file_inp->id_gen()."').style.display='';\$i('".$this->subm_btn->id_gen()."').style.display='';".
				"this.is_uploading=false;};"
				;
		}else{
			if(isset($this->onload))
			{
				$this->res->attributes['onload']="this.reset=function(){".$reset."};if(this.is_uploading){".
					"var resdiv=\$i('".$this->res_div->id_gen()."');".
					$this->onload.
					"this.is_uploading=false;};"
					;
			}else{
				
				$this->res->attributes['onload']="if(this.is_uploading){".
					"\$i('".$this->res_div->id_gen()."').innerHTML=this.contentWindow.document.firstChild.innerHTML;".
					//"alert(this.contentWindow.document.firstChild.textContent);".
					"this.is_uploading=false;};"
					;
			}
		}
		//$this->file_inp->attributes['onchange']="alert(this.value);if(\$i('".js_escape($this->form->id_gen())."').submit())this.style.display='none';";
		$this->res->attributes['name']=$this->res->id_gen();
		$this->form->attributes['target']=$this->res->attributes['name'];
		parent::html_inner();
	}
}






class editor_file_upload_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		for($k=1;$k<=10;$k++)
		{
			editor_generic::addeditor('t'.$k,new editor_file_upload);
			$this->append_child($this->editors['t'.$k]);
		}
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}


$tests_m_array['simple']['editor_file_upload_test']='editor_file_upload_test';

class editor_file_upload_test_custom extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->append_child(new dom_statictext('custom_js:'));
		$k=1;
			editor_generic::addeditor('t'.$k,new editor_file_upload);
			$this->append_child($this->editors['t'.$k]);
			$this->editors['t'.$k]->type_hidden->attributes['value']='rawname';
			$this->editors['t'.$k]->onload="resdiv.innerHTML=this.contentWindow.document.firstChild[text_content];";
		$this->append_child(new dom_statictext('normal_postback:'));
		$k=2;
			editor_generic::addeditor('t'.$k,new editor_file_upload);
			$this->append_child($this->editors['t'.$k]);
			$this->editors['t'.$k]->type_hidden->attributes['value']='rawname';
			$this->editors['t'.$k]->normal_postback=1;
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		if($ev->rem_name=='t2')
		{
			print "alert('Got file: ".js_escape($_POST['val'])."');";
		};
		editor_generic::handle_event($ev);
	}
}


$tests_m_array['simple']['editor_file_upload_test_custom']='editor_file_upload_test_custom';


######################################################################################################################
######################################################################################################################
######################################################################################################################

class container_resize_scroll extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->css_style['width']='100px';
		$this->css_style['height']='100px';
		//$this->css_style['background-color']='red';
		$this->css_style['border']='5px solid gray';
		$this->in=new dom_div;
		$this->in->css_style['overflow']='auto';
		$this->in->css_style['width']='100%';
		$this->in->css_style['height']='100%';
//		$this->in->css_style['background-color']='white';
		dom_div::append_child($this->in);
		
		
	}
	
	function append_child($o)
	{
		$this->in->append_child($o);
		return $this;
	}
	
	function id_gen()
	{
		return 'd'.$this->id.'_resizeable';
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
	}
	
	function html_head()
	{
		$this->css_style['width']=$this->rootnode->setting_val($this->oid,$this->long_name.'._width',100).'px';
		$this->css_style['height']=$this->rootnode->setting_val($this->oid,$this->long_name.'._height',100).'px';
		$this->attributes['onmouseup']="if(resizer.obj){ save_setting_value('".$this->oid."','".$this->long_name."._width',resizer.obj.clientWidth);";
		$this->attributes['onmouseup'].="save_setting_value('".$this->oid."','".$this->long_name."._height',resizer.obj.clientHeight);";
		$this->attributes['onmouseup'].="}";
		$this->in->attributes['onscroll']=
			"if(this.savetimeout)clearTimeout(this.savetimeout);".
			"this.savetimeout=setTimeout(\"".
			"save_setting_value('".$this->oid."','".$this->long_name."._scroll_x',\" + this.scrollLeft + \");".
			"save_setting_value('".$this->oid."','".$this->long_name."._scroll_y',\" + this.scrollTop + \");".
			"\",1000);".
			"";
		//$this->in->attributes['onclick']="this.scrollTop=20;";
			
		$this->rootnode->endscripts[$this->long_name.'_scroll_settings']=
		"document.onload_functions[document.onload_functions.length]=function(){\$i('".$this->in->id_gen()."').scrollLeft=".
			$this->rootnode->setting_val($this->oid,$this->long_name.'._scroll_x','0').";".
			"\$i('".$this->in->id_gen()."').scrollTop=".
			$this->rootnode->setting_val($this->oid,$this->long_name.'._scroll_y','0').";};".
			"";
		parent::html_head();
	}
}

######################################################################################################################
######################################################################################################################
######################################################################################################################

class container_resize extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->css_style['width']='100px';
		$this->css_style['height']='100px';
		//$this->css_style['background-color']='red';
		$this->css_style['border']='5px solid red';
		$this->in=new dom_div;
		$this->in->css_style['width']='100%';
		$this->in->css_style['height']='100%';
//		$this->in->css_style['background-color']='white';
		dom_div::append_child($this->in);
		
		
	}
	
	function append_child($o)
	{
		$this->in->append_child($o);
		return $this;
	}
	
	function id_gen()
	{
		return 'd'.$this->id.'_resizeable';
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
	}
	
	function html_head()
	{
		$this->css_style['width']=$this->rootnode->setting_val($this->oid,$this->long_name.'._width',100).'px';
		$this->css_style['height']=$this->rootnode->setting_val($this->oid,$this->long_name.'._height',100).'px';
		$this->attributes['onmouseup']="if(resizer.obj){ save_setting_value('".$this->oid."','".$this->long_name."._width',resizer.obj.clientWidth);";
		$this->attributes['onmouseup'].="save_setting_value('".$this->oid."','".$this->long_name."._height',resizer.obj.clientHeight);";
		$this->attributes['onmouseup'].="}";
		//$this->in->attributes['onclick']="this.scrollTop=20;";
			
		parent::html_head();
	}
}






class container_resize_scroll_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		for($t=0;$t<3;$t++)
		{
			editor_generic::addeditor('t'.$t,new container_resize_scroll);
			$this->append_child($this->editors['t'.$t]);
			$this->editors['t'.$t]->css_style['display']='inline-block';
			for($k=0;$k<300;$k++)
			{
				$r=new dom_div;
				$tx=new dom_statictext;
				$tx->text=md5(($t*1000+$k).$tx->text);
				$r->append_child($tx);
				$this->editors['t'.$t]->append_child($r);
				$this->editors['t'.$t]->css_style['vertical-align']='bottom';
			}
		}
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		foreach($this->editors as $i=>$e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}
}


$tests_m_array['simple']['container_resize_scroll_test']='container_resize_scroll_test';










class editor_fold extends dom_any
{
	function __construct($invert=false)
	{
		dom_any::__construct('button');
		$this->etype='editor_checkbox';
		$this->image_0=new dom_any('img');
		$this->image_1=new dom_any('img');
		if($invert)
		{
			$minus=&$this->image_0;
			$plus=&$this->image_1;
		}else{
			$plus=&$this->image_0;
			$minus=&$this->image_1;
		}
		$plus->attributes['src']='/i/fold_plus.png';
		$plus->attributes['alt']='+';
		$minus->attributes['src']='/i/fold_minus.png';
		$minus->attributes['alt']='-';
		$this->append_child($this->image_0);
		$this->append_child($this->image_1);
		$this->css_class='editor_fold';
		$this->main=$this;
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap();
		$this->attributes['onclick']=
		"var i_p=\$i('".js_escape($this->image_0->id_gen())."');".
		"var i_m=\$i('".js_escape($this->image_1->id_gen())."');".
		"if(i_p.style.display=='none'){i_p.style.display='';i_m.style.display='none';this.checked=true;}".
		"else{i_p.style.display='none';i_m.style.display='';this.checked=false;};".
		"chse.timerch(true);";
		
		//$this->attributes['onfocus']='';
		//$this->attributes['onblur']='';
		// focus persistence test
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	function html()
	{
		unset($this->image_1->css_style['display']);
		unset($this->image_0->css_style['display']);
		if($this->args[$this->context[$this->long_name]['var']]==1)$this->image_1->css_style['display']='none';
		else $this->image_0->css_style['display']='none';
		parent::html();
	}
}



#################################################################################
#################################################################################
###############   class editor_static_menu
#################################################################################


class editor_static_menu extends dom_div
{
	function __construct($struct=Array())
	{
		parent::__construct();
		$this->struct =$struct;
		//Array(String name=>Object(string text,children Array(...),...)
		$this->subitems=new dom_div;
		$this->tbl=new dom_table;
		$this->tr=new dom_tr;
		$this->td=new dom_td;
		$this->text=new dom_statictext;
		$this->append_child($this->text);
		$this->append_child(
			$this->subitems->append_child(
				$this->tbl->append_child(
					$this->tr->append_child(
						$this->td
		)	)	)	);
		$this->css_class=get_class($this);
		
	}
	
	function html_inner()
	{
		$this->text->html();
		if(is_array($this->struct) && count($this->struct)>0)
		{
			$this->subitems->html_head();
			$this->tbl->html_head();
			$struct=$this->struct;
			foreach($struct as $name => $item)
			{
				$this->tr->css_style['background']=string_to_color($item->text,2);
				$this->tr->html_head();
				$this->td->html_head();
				$this->text->text=$item->text;
				$this->struct=$item->children;
				$this->id_alloc();
				$this->html();
				$this->struct=$struct;
				$this->td->html_tail();
				$this->tr->html_tail();
			}
			$this->tbl->html_tail();
			$this->subitems->html_tail();
		}
	}
	
	function after_build_before_children()
	{
		$this->rootnode->exstyle['div.editor_static_menu']=Array(
			'min-width'=>'9em',
			'cursor'=>'pointer'
		);
		$this->rootnode->exstyle['div.editor_static_menu:hover']=Array(
			'background'=>'blue',
			'color'=>'white'
		);
		$this->rootnode->exstyle['div.editor_static_menu>div']=Array(
			'display'=>'none',
			'width'=>'auto'
		);
		$this->rootnode->exstyle['div.editor_static_menu:hover>div']=Array(
			'margin-left'=>'1em',
			'display'=>'block',
			'position'=>'absolute',
			'background'=>'white',
			'border'=>'1px solid black',
			'color'=>'black',
			'width'=>'auto'
		);
		$this->rootnode->exstyle['div.editor_static_menu:hover>div>table']=Array(
			'border-collapse'=>'collapse'
		);
		$this->rootnode->exstyle['div.editor_static_menu:hover>div>table>tr>td']=Array(
			'border'=>'1px solid black',
			'cursor'=>'pointer'
		);

	}
	
	function bootstrap()
	{
	}
	function handle_event($ev)
	{
	}
}

class editor_static_menu_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->menu=new editor_static_menu;
		$this->append_child($this->menu);
		$menu=$this->geni('a',5,4);
		$this->menu->struct=$menu;
		$this->menu->text->text='m';
		$this->menu->css_style['width']='10em';
	}
	
	function geni($pfx,$cnt,$dep)
	{
		$res=Array();
		if($dep==0)return $res;
		for($k=0;$k<$cnt;$k++)
		{
			$res[$pfx.'i'.$k]->text='Item N '.$pfx.$k;
			$res[$pfx.'i'.$k]->children=$this->geni($pfx.'i'.$k,$cnt,$dep-1);
		}
		return $res;
	}
	
	function bootstrap()
	{
	}
	function handle_event($ev)
	{
	}
}

$tests_m_array['simple']['editor_static_menu_test']='editor_static_menu_test';




#####################################################################################################
#####################################################################################################
#####################################################################################################
#####################################################################################################




class editor_dropdown_list extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->button=new dom_any_noterm('input');
		$this->button->attributes['value']='(....)';
		$this->button->attributes['type']='button';
		$this->append_child($this->button);
		$this->listdiv=new dom_div;
		$this->listdiv->css_style['display']='none';
		$this->listdiv->css_style['position']='absolute';
		$this->listdiv->css_style['border']='1px solid blue';
		$this->listdiv->css_style['background']='white';
		$this->append_child($this->listdiv);
		$this->actions=Array();
		$this->actions[]=(object)Array('acc'=>'a','text'=>'Action a','act'=>'do_a');
		$this->actions[]=(object)Array('acc'=>'s','text'=>'Action s','act'=>'do_s');
		$this->actions[]=(object)Array('acc'=>'d','text'=>'Action d','act'=>'do_d');
		$this->actions[]=(object)Array('acc'=>'w','text'=>'Action w','act'=>'do_w');
		$this->atable=new dom_table;
		$this->arow=new dom_tr;
		$this->acell=new dom_td;
		$this->atext=new dom_statictext;
		$this->listdiv->append_child(
			$this->atable->append_child(
				$this->arow->append_child(
					$this->acell->append_child(
						$this->atext
		)	)	)	);
		$this->main=$this->button;
	}
	
	function setmouse($a,$cn)
	{
		$this->arow->attributes['onmouseover']=
		"var text_inp=\$i('".js_escape($this->button->id_gen())."');".
		"if(text_inp.as_objects)".
		"{".
			"if(text_inp.as_id || text_inp.as_id==0)".
			"{".
				"var s=\$i(text_inp.as_objects[text_inp.as_id].id).style;".
				"s.backgroundColor='white';".
				"s.color='';".
			"};".
			"text_inp.as_id=".$cn.";".
			"if(text_inp.as_id || text_inp.as_id==0)".
			"{".
				"var s=\$i(text_inp.as_objects[text_inp.as_id].id).style;".
				"s.backgroundColor='blue';".
				"s.color='white';".
			"}".
		"}".
		"";
		$this->arow->attributes['onmouseout']=
		"var text_inp=\$i('".js_escape($this->button->id_gen())."');".
		"if(text_inp.as_objects)".
		"{".
			"if(text_inp.as_id || text_inp.as_id==0)".
			"{".
				"var s=\$i(text_inp.as_objects[text_inp.as_id].id).style;".
				"s.backgroundColor='white';".
				"s.color='';".
			"};".
			"text_inp.as_id=null;".
		"}".
		"";
		//$this->tr->attributes['onclick']=
		$click=
		"var text_inp=\$i('".js_escape($this->button->id_gen())."');".
		"if(text_inp.as_objects)".
		"{".
			"if(text_inp.as_id || text_inp.as_id==0)".
			"{".
				"var a=text_inp.as_objects[text_inp.as_id].val;".
				$this->sender_js.
			"}".
			"text_inp.focus();".
		"}".
		"";
		$this->arow->attributes['onmousedown']="event.preventDefault();\$i('".js_escape($this->button->id_gen())."').focus();";
		$this->arow->attributes['onmouseup']="\$i('".js_escape($this->button->id_gen())."').focus();$click";
		$this->arow->css_style['cursor']='pointer';
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap_part();
	}
	
	function html_head()
	{
		global $idcounter;
		//allocate ids
		$this->sender_js="chse.send_or_push({static:".$this->send.",val:a,c_id:'".js_escape($this->button->id_gen())."'});";
		$js='';
		foreach($this->actions as $a)
			{
				$a->id='x'.$idcounter;
				$idcounter++;
				if($js!='')$js.=',';
				$js.='{id:\''.js_escape($a->id).'\',val:\''.js_escape($a->act).'\',acc:\''.js_escape($a->acc).'\'}';
			}

		$this->button->attributes['onfocus']='this.as_objects=['.$js.'];this.as_id = null;'.
		"\$i('".js_escape($this->listdiv->id_gen())."').style.display='';";
		$this->button->attributes['onblur']=
		"\$i('".js_escape($this->listdiv->id_gen())."').style.display='none';";
		$this->button->attributes['onkeypress']="var a=editor_dropdown_button_keypress(this,event,'".js_escape($this->listdiv->id_gen())."');".
		"if(a){if(a==true)return true; ".
		$this->sender_js.
		" return false;} else return false;";
		$this->div->attributes['onmousedown']='event.preventDefault();event.stopPropagation();return false;$i(\''.js_escape($this->button->id_gen()).'\').focus();';
		
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
		
		
		parent::html_head();
	}
	
	function html_inner()
	{
		$this->button->html();
		$this->listdiv->html_head();
		$this->atable->html_head();
		$cn=0;
		foreach($this->actions as $a)
		{
			$this->arow->custom_id=$a->id;
			$this->setmouse($a,$cn);
			$cn++;
			$this->arow->html_head();
			$this->atext->text=$a->acc;
			$this->acell->html();
			$this->acell->id_alloc();
			$this->atext->text=$a->text;
			$this->acell->html();
			$this->acell->id_alloc();
			$this->arow->html_tail();
		}
		$this->atable->html_tail();
		$this->listdiv->html_tail();
	}
	
	
	function handle_event($ev)
	{
		
	}

	
}

class editor_dropdown_list_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
			editor_generic::addeditor('t',new editor_dropdown_list);
			$this->append_child($this->editors['t']);
		$this->resdiv=new dom_div;
		$this->append_child($this->resdiv);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		$this->context[$this->long_name]['retv']=$this->resdiv->id_gen();
			$i='t';
			$e=$this->editors[$i];
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		if($ev->rem_name=='t')
		{
			$id=$ev->context[$ev->parent_name]['retv'];
			print "\$i('".js_escape($id)."').innerHTML='".
				js_escape(htmlspecialchars("Gottcha: ".$_POST['val'],ENT_QUOTES)).
				"';";
		}
		
		editor_generic::handle_event($ev);
	}
}




$tests_m_array['simple']['editor_dropdown_list_test']='editor_dropdown_list_test';



#####################################################################################################
#####################################################################################################
#####################################################################################################
#####################################################################################################




class container_dropdown_div extends dom_div
{
	function __construct($model='raw')
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->button=new dom_any_noterm('input');
		$this->button->attributes['value']='(....)';
		$this->button->attributes['type']='button';
		parent::append_child($this->button);
		$this->listdiv=new dom_div;
		$this->listdiv->css_style['display']='none';
		$this->listdiv->css_style['position']='absolute';
		$this->listdiv->css_style['border']='1px solid blue';
		$this->listdiv->css_style['background']='white';
		parent::append_child($this->listdiv);
		$this->__model=$model;
		if(preg_match('/^table/',$model))
		{
			$this->__table_row_cnt=intval(preg_replace('/^table:/','',$this->__model));
			$this->__model='table';
			$this->listdiv_table=new dom_table;
			$this->listdiv->append_child($this->listdiv_table);
			$this->__table_row_pos=0;
		}
		
	}
	
	
	function bootstrap()
	{
		editor_generic::bootstrap_part();
	}
	//-------!!!!!!!!!! $model !!!!!!!!!!!!!!
	function append_child($c)
	{
		switch($this->__model)
		{
			case '':
			case 'raw': //simply put child into listdiv
				$this->listdiv->append_child($c);
				break;
			case 'div': //put child into individual div. Then put this div into listdiv
				$div=new dom_div;
				$div->append_child($c);
				$this->listdiv->append_child($div);
				break;
			case 'table'://the most quirky layout. Create fixed width rows
				if($this->__table_row_pos==0)//create a row
				{
					$this->__table_temp_row=new dom_tr;
					$this->listdiv_table->append_child($this->__table_temp_row);
					if($this->__table_row_cnt<=0)$this->__table_row_pos=1;
					else $this->__table_row_pos=$this->__table_row_cnt;
				}
				if($this->__table_row_pos>0)
				{
					$td=new dom_td;
					$td->append_child($c);
					$this->__table_temp_row->append_child($td);
					$this->__table_row_pos--;
					break;
				}
		}
	}
	
	function html_head()
	{
		global $idcounter;
		//allocate ids
		$lid=js_escape($this->listdiv->id_gen());
		$this->button->attributes['onclick']='var div=$i(\''.$lid.'\');'.
		"if(div.style.display=='')".
		"{".
		" div.style.display='none';".
		" this.style.backgroundColor='';".
		" this.style.color='';".
		"}else{".
		" div.style.display='';".
		" this.style.backgroundColor='black';".
		" this.style.color='white';".
		"};";
		
		parent::html_head();
	}
	
	
	
	function handle_event($ev)
	{
		
	}

	
}

class container_dropdown_div_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		editor_generic::addeditor('t_raw',new container_dropdown_div);
		$this->append_child($this->editors['t_raw']);
		$this->add_some_crap('t_raw');
		
		editor_generic::addeditor('t_div',new container_dropdown_div('div'));
		$this->append_child($this->editors['t_div']);
		$this->add_some_crap('t_div');
		
		editor_generic::addeditor('t_table',new container_dropdown_div('table'));
		$this->append_child($this->editors['t_table']);
		$this->add_some_crap('t_table');
		
		editor_generic::addeditor('t_table:2',new container_dropdown_div('table:2'));
		$this->append_child($this->editors['t_table:2']);
		$this->add_some_crap('t_table:2');
	}
	
	function add_some_crap($editor_name)
	{
		for($k=0;$k<4;$k++)
		{
			$txt=new dom_statictext;
			$txt->text='static nr '.$k;
			$this->editors[$editor_name]->append_child($txt);
		}
		for($k=0;$k<4;$k++)
		{
			$txt=new editor_text;
			$en=$editor_name.'_text_'.$k;
			editor_generic::addeditor($en,$txt);
			$this->editors[$editor_name]->append_child($txt);
		}
		for($k=0;$k<4;$k++)
		{
			$txt=new editor_button;
			$en=$editor_name.'_button_'.$k;
			$txt->attributes['value']='btn'.$k;
			editor_generic::addeditor($en,$txt);
			$this->editors[$editor_name]->append_child($txt);
		}
		for($k=0;$k<4;$k++)
		{
			$txt=new editor_checkbox;
			$en=$editor_name.'_chbox_'.$k;
			editor_generic::addeditor($en,$txt);
			$this->editors[$editor_name]->append_child($txt);
		}
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		//$this->context[$this->long_name]['retv']=$this->resdiv->id_gen();
		$this->args=Array();
		foreach($this->editors as $i=>$e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
			$this->args[$i]=$_SESSION[$this->etype][$i];
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
		}
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		$_SESSION[$this->etype][$ev->rem_name]=$_POST['val'];
		editor_generic::handle_event($ev);
	}
}




$tests_m_array['simple']['container_dropdown_div_test']='container_dropdown_div_test';



#####################################################################################################
#####################################################################################################
#####################################################################################################
#####################################################################################################

/*
<div>
<input type=text value="" onfocus="tddcb_activate(this,'aa00','aa bb cc dd ee ff kk',{obj:this,objtype:'".$this->etype."',static:'$send'});" onblur="tddcb_deactivate(this,'aa00');"
onkeypress="return tddcb_keypress(event,this,'aa00');" onchange="document.getElementById('res').innerHTML=this.value;" >
<div id=aa00 style="display:none;position:absolute;border:1px solid blue;">
</div>
</div>
*/

class editor_text_dropdown_set extends dom_div
{
	function __construct($config=NULL)
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->main=new dom_any_noterm('input');
		$this->main->attributes['type']='text';
		$this->append_child($this->main);
		$this->listdiv=new dom_div;
		$this->listdiv->css_style['display']='none';
		$this->listdiv->css_style['position']='absolute';
		$this->listdiv->css_style['border']='1px solid blue';
		$this->listdiv->css_style['background']='white';
		$this->append_child($this->listdiv);
		if(isset($config) && isset($config['editor_config']))
		{
			$uconfig=unserialize($config['editor_config']);
			if(is_object($uconfig) && isset($uconfig->full_set))$this->full_set=$uconfig->full_set;
			elseif(is_array($uconfig) && isset($uconfig['full_set']))$this->full_set=$uconfig['full_set'];
			
		}
	}
	
	
	function bootstrap()
	{
		$this->custom_id=$this->main_id();
		editor_generic::bootstrap_part();
		unset($this->custom_id);
		$this->main->attributes['onmouseover']='opera_fix(this);';
		$this->main->attributes['onfocus']="tddcb_activate(this,'".js_escape($this->listdiv->id_gen())."','".js_escape($this->full_set)."',{obj:this,objtype:'".$this->etype."',static:".$this->send."});";
		$this->main->attributes['onblur']="tddcb_deactivate(this,'".js_escape($this->listdiv->id_gen())."');";
		$this->main->attributes['onkeypress']="return tddcb_keypress(event,this,'".js_escape($this->listdiv->id_gen())."');";
		
		
		
		
		// focus persistence test
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	
	function html()
	{
		$this->main->attributes['value']=$this->args[$this->context[$this->long_name]['var']];
		parent::html();
	}
	
	
	function handle_event($ev)
	{
		if(isset($ev->failure))
		{
			//print 'var x=$i(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\');x.style.backgroundColor=\'pink\';'.
			print 'var x=chse.bgifc(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\',\'pink\');'.
//			'if(x){'.
//			'$i(x.failure_viewer).style.display=\'\';'.
//			'$i(x.failure_viewer_text).innerHTML=\''.js_escape(htmlspecialchars($ev->failure,ENT_QUOTES)).'\';'.
//			'}'
			''
			;
		}else
			print 'var x=chse.bgifc(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\',\'\');'.
//			'if(x){'.
//			'$i(x.failure_viewer).style.display=\'none\';'.
//			'}'
			''
			;
		
	}

	
}

class editor_dropdown_set_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
			editor_generic::addeditor('t',new editor_text_dropdown_set);
			$this->append_child($this->editors['t']);
		$this->editors['t']->delimiter=' ';
		$this->editors['t']->full_set='aa bb cc dd ee ff';
		$this->resdiv=new dom_div;
		$this->append_child($this->resdiv);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		$this->context[$this->long_name.'.t']['var']='t';
		$this->args['t']='cc ee';
		$this->context[$this->long_name]['retv']=$this->resdiv->id_gen();
			$i='t';
			$e=$this->editors[$i];
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;
		foreach($this->editors as $i=>$e)
			$e->bootstrap();
			
		
	}
	
	function handle_event($ev)
	{
		if($ev->rem_name=='t')
		{
			$id=$ev->context[$ev->parent_name]['retv'];
			print "\$i('".js_escape($id)."').innerHTML='".
				js_escape(htmlspecialchars("Gottcha: ".$_POST['val'],ENT_QUOTES)).
				"';";
		}
		
		editor_generic::handle_event($ev);
	}
}




$tests_m_array['simple']['editor_dropdown_set_test']='editor_dropdown_set_test';


###################################################################3
################# file_pick_or_upload #############################3
###################################################################3
class file_pick_or_upload extends dom_div
{
	function __construct($config=NULL)
	{
		parent::__construct();
		$this->etype=get_class($this);
		$this->picker=new dom_div;
		$this->append_child($this->picker);
		$picker=new container_autotable;
		$this->picker->append_child($picker);
		
		$this->uploader=new dom_div;
		$this->uploader->css_style['display']='none';
		$this->append_child($this->uploader);
		
		$uploader=new container_autotable;
		$this->uploader->append_child($uploader);
		
		$this->p_switchbutton=new dom_any_noterm('input');
		$this->p_switchbutton->attributes['type']='image';
		$this->p_switchbutton->attributes['src']='/i/file_pick_or_upload_open.png';
		$picker->append_child($this->p_switchbutton);
		
		$this->u_switchbutton=new dom_any_noterm('input');
		$this->u_switchbutton->attributes['type']='image';
		$this->u_switchbutton->attributes['src']='/i/file_pick_or_upload_pick.png';
		$uploader->append_child($this->u_switchbutton);
		
		$this->p_picker=new editor_text_autosuggest;
		$picker->append_child($this->p_picker);
		editor_generic::addeditor('p_picker',$this->p_picker);
		$this->p_picker->list_class='editor_text_autosuggest_list_FPOU';
		
		$this->p_del=new editor_button_image;
		$this->p_del->attributes['src']='/i/file_pick_or_upload_del.png';
		$picker->append_child($this->p_del);
		editor_generic::addeditor('p_del',$this->p_del);
		
		$this->u_uploader=new editor_file_upload;
		$uploader->append_child($this->u_uploader);
		editor_generic::addeditor('u_uploader',$this->u_uploader);
		
		$this->u_uploader->subm_btn->attributes['type']='image';
		$this->u_uploader->subm_btn->attributes['src']='/i/file_pick_or_upload_up.png';
		$this->default_editor='p_picker';
		
		
	}
	
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		$this->context[$this->long_name.'.p_picker']['var']=$this->context[$this->long_name]['var'];
		$this->context[$this->long_name]['p_picker_id']=$this->p_picker->text->id_gen();
		$this->p_switchbutton->attributes['onclick']=
			"\$i('".$this->uploader->id_gen()."').style.display='';\$i('".$this->picker->id_gen()."').style.display='none';";
		$this->u_switchbutton->attributes['onclick']=
			"\$i('".$this->uploader->id_gen()."').style.display='none';\$i('".$this->picker->id_gen()."').style.display='';";
		$this->u_uploader->type_hidden->attributes['value']='rawname';
		$this->u_uploader->onload="var pp=\$i('".$this->p_picker->text->id_gen()."');".
		"this.reset();".
		"\$i('".$this->uploader->id_gen()."').style.display='none';\$i('".$this->picker->id_gen()."').style.display='';".
		"pp.focus();pp.value=this.contentWindow.document.body.textContent.replace(/^.*\\//,'');".
		"";
		$this->p_del->val_js="\$i('".$this->p_picker->text->id_gen()."').value";
		/*	$i='t';
			$e=$this->editors[$i];
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
			$e->oid=$this->oid;*/
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
		switch($ev->rem_name)
		{
			case 'p_del':
				$picker=$ev->context[$ev->parent_name]['p_picker_id'];
				$f=$_SERVER['DOCUMENT_ROOT'].'uploads/'.preg_replace('/^.*\\//','',$_POST['val']);
				if(is_file($f))unlink($f);
				print "\$i('".js_escape($picker)."').value='';\$i('".js_escape($picker)."').focus();";
				break;
		}
		editor_generic::handle_event($ev);
	}

	
}

class editor_text_autosuggest_list_FPOU extends editor_text_autosuggest_list_example
{
	function __construct()
	{
		parent::__construct();
		$doc_root=$_SERVER['DOCUMENT_ROOT'];
		if(preg_match('#.*[^/]$#',$doc_root))$doc_root.='/';
		$dir=opendir($doc_root."uploads");
		$this->list_items=Array();
		while($fn=readdir($dir))
		{
			if(is_file($doc_root."uploads/".$fn) && is_readable($doc_root."uploads/".$fn))$this->list_items[]=$fn;
		}
		closedir($dir);
	}
}



class file_pick_or_upload_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('t',new file_pick_or_upload);
		$this->append_child($this->editors['t']);
		$this->resdiv=new dom_div;
		$this->append_child($this->resdiv);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		$this->context[$this->long_name.'.t']['var']='t';
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




$tests_m_array['complex']['file_pick_or_upload_test']='file_pick_or_upload_test';


###################################################################3
#################### util_small_pager #############################3
###################################################################3

class util_small_pager extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		
		$tbl=new container_autotable;
		$this->append_child($tbl);
		$this->tbl=$tbl;// !!!!!!!!!! $tbl exported
		
		
		$this->ed_zero=new dom_any_noterm('input');
		$this->ed_zero->attributes['type']='button';
		$this->ed_zero->attributes['value']='«';
		$this->ed_zero->attributes['title']='В начало';
		$tbl->append_child($this->ed_zero);
		
		$this->ed_less=new dom_any_noterm('input');
		$this->ed_less->attributes['type']='button';
		$this->ed_less->attributes['value']='<';
		$this->ed_less->attributes['title']='На страницу назад.';
		$tbl->append_child($this->ed_less);
		
		editor_generic::addeditor('ed_count',new editor_text);
		$tbl->append_child($this->editors['ed_count']);
		$this->editors['ed_count']->main->css_style['width']='4em';
		$this->editors['ed_count']->attributes['title']='Количество строк.';
		
		editor_generic::addeditor('ed_offset',new editor_text);
		$tbl->append_child($this->editors['ed_offset']);
		$this->editors['ed_offset']->main->css_style['width']='4em';
		$this->editors['ed_offset']->attributes['title']='Пропустить строк.';
		
		$this->ed_more=new dom_any_noterm('input');
		$this->ed_more->attributes['type']='button';
		$this->ed_more->attributes['value']='>';
		$this->ed_more->attributes['title']='На страницу вперед.';
		$tbl->append_child($this->ed_more);
		
		#editor_generic::addeditor('ed_rowcount',new query_result_viewer_single);
		#$tbl->append_child($this->editors['ed_rowcount']);
		$this->main=$this->editors['ed_offset']->main;
		
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		$this->ed_more->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"var cnt=\$i('".$this->editors['ed_count']->main_id()."');".
			"ofs.focus();".
			"var iofs=isNaN(parseInt(ofs.value))?0:parseInt(ofs.value);".
			"var icnt=isNaN(parseInt(cnt.value))?0:parseInt(cnt.value);".
			"ofs.value=iofs+icnt;".
//			"this.focus();".
			"";
		$this->ed_less->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"var cnt=\$i('".$this->editors['ed_count']->main_id()."');".
			"ofs.focus();".
			"var iofs=isNaN(parseInt(ofs.value))?0:parseInt(ofs.value);".
			"var icnt=isNaN(parseInt(cnt.value))?0:parseInt(cnt.value);".
			"ofs.value=(iofs>=icnt)?iofs-icnt:0;".
//			"this.focus();".
			"";
		$this->ed_zero->attributes['onclick']=
			"var ofs=\$i('".$this->editors['ed_offset']->main_id()."');".
			"ofs.focus();".
			"ofs.value=0;".
			"this.focus();".
			"";
		foreach($this->editors as $i=>$e)
		{
			$this->context[$this->long_name.'.'.$i]['var']=$i;
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
		editor_generic::handle_event($ev);
	}
}

class util_small_pager_test extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		editor_generic::addeditor('util_small_pager_test',new util_small_pager);
		$this->append_child($this->editors['util_small_pager_test']);
		$this->resdiv=new dom_div;
		$this->append_child($this->resdiv);
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->editors['t']->io=new editor_search_pick_def_io;
		$this->context[$this->long_name.'.util_small_pager_test.ed_count']['var']='cnt';
		$this->context[$this->long_name.'.util_small_pager_test.ed_offset']['var']='ofs';
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
		$id=$ev->context[$ev->parent_name]['retv'];
		switch($ev->rem_name)
		{
		case 'util_small_pager_test.ed_count':
			print "\$i('".js_escape($id)."').innerHTML='".
				js_escape(htmlspecialchars("ed_count: ".$_POST['val'],ENT_QUOTES)).
				"';";
			break;
		case 'util_small_pager_test.ed_offset':
			print "\$i('".js_escape($id)."').innerHTML='".
				js_escape(htmlspecialchars("ed_count: ".$_POST['val'],ENT_QUOTES)).
				"';";
			break;
		}
		
		editor_generic::handle_event($ev);
	}
}




$tests_m_array['complex']['util_small_pager_test']='util_small_pager_test';










?>