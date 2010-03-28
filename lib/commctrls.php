<?php



require_once("lib/utils.php");
require_once("lib/dom.php");
require_once("lib/settings.php");




function reload_object($obj,$inneronly=false)
{
	global $sql,$idcounter;
	$settings_tool=new settings_tool;
	$tr=new dom_root_reload;
	$tr->append_child($obj);
	//$tr->for_each_set('oid',-1);
	$obj->bootstrap();

	$tr->collect_oids($settings_tool);
	$tr->settings_array=$settings_tool->read_oids($sql);
	$tr->after_build();
	//print "\$i('dom_meta_treeview_resize_style').innerHTML=".$tr->html();
	print "'";
	if($inneronly)return $tr->firstinner();
	return $tr->html();
}

function reload_object_create($obj)
{
	global $sql,$idcounter;
	$settings_tool=new settings_tool;
	$tr=new dom_root_reload;
	$tr->append_child($obj);
	//$tr->for_each_set('oid',-1);
	$obj->bootstrap();

	$tr->collect_oids($settings_tool);
	$tr->settings_array=$settings_tool->read_oids($sql);
	$tr->after_build();
	//print "\$i('dom_meta_treeview_resize_style').innerHTML=".$tr->html();
	return $tr;
}


// ##########################################################################################
// ##########################################################################################
// ####################     CORE COMPONENTS        ##########################################
// ##########################################################################################
// ##########################################################################################



//###############################################################################################3
//###############################################################################################3
//##################################   editor_generic definition  ###############################3
//###############################################################################################3
//###############################################################################################3



class editor_generic extends dom_any
{
	const em=0;
	
	function bootstrap1()
	{
	//postdata['name']=parent0_name.parent1_name.object_name
	//postdata['type']=perent0_type.parent1_type.object_type
	//postdata['context']=..
	//postdata['keys']=..
	//postdata['last_generated_id']=..
	//postdata['val']=...
	$this->long_name=editor_generic::long_name();
	$postargs['keys']=&$this->keys;
	//$postargs['context']=$this->context;
	
	$pp=explode('.',$this->long_name);
	$r='';
	foreach($pp as $pa)
	{
		if($r !='')$r.='.';
		$r.=$pa;
		$postargs['context'][$r]=$this->context[$r];
	}
	
	
	$postargs['name']=$this->long_name;
	$postargs['type']=editor_generic::long_type();
	$postargs['context'][$this->long_name]['htmlid']=$this->main_id();
	if(isset($postargs['context'][$this->long_name]['oid']))
		$this->oid=$postargs['context'][$this->long_name]['oid'];
	elseif(isset($this->oid))
		$postargs['context'][$this->long_name]['oid']=$this->oid;
	$send=editor_generic::array_to_post($postargs);
	$send.="&last_generated_id=' + last_generated_id + '";
	$send.="&val";
	$this->attributes['onfocus']="chse.activatemon({obj:this,objtype:'".$this->etype."',static:'$send'});";
	$this->attributes['onblur']='chse.latedeactivate(this);';
	
	}
	
	
	function bootstrap()
	{
	//postdata['name']=parent0_name.parent1_name.object_name
	//postdata['type']=perent0_type.parent1_type.object_type
	//postdata['context']=..
	//postdata['keys']=..
	//postdata['last_generated_id']=..
	//postdata['val']=...
	$this->long_name=editor_generic::long_name();
	//$postargs['keys']=&$this->keys;
	//$postargs['context']=$this->context;
	
	$pp=explode('.',$this->long_name);
	$r='';
	foreach($pp as $pa)
	{
		if($r !='')$r.='.';
		$r.=$pa;
		$context[$r]=$this->context[$r];
	}
	
	
	//$postargs['name']=$this->long_name;
	//$postargs['type']=editor_generic::long_type();
	$context[$this->long_name]['htmlid']=$this->main_id();
	/*if(isset($context[$this->long_name]['oid']))
		$this->oid=$context[$this->long_name]['oid'];
	elseif(isset($this->oid))*/
		$context[$this->long_name]['oid']=$this->oid;
	//$send=editor_generic::array_to_post($postargs);
	
	
	//print 'ue='.strlen(urlencode(serialize($context))).';gz='.strlen(urlencode(gzcompress(serialize($context)))).
	//	';b64='.strlen(urlencode(base64_encode(gzcompress(serialize($context))))).'<br/>';
	
	
	$send=	'keys='.		urlencode(serialize($this->keys)).
		'&context='.	(editor_generic::em?urlencode(serialize($context)):urlencode(base64_encode(gzcompress(serialize($context))))).
#		'&name='.	urlencode($this->long_name).
		'&name='.	urlencode(editor_generic::effective_name()).
		'&type='.	urlencode(editor_generic::long_type()).
		"&last_generated_id=' + last_generated_id + '".
		"&val";
	$this->attributes['onfocus']="chse.activatemon({obj:this,objtype:'".$this->etype."',static:'$send'});";
	$this->attributes['onblur']='chse.latedeactivate(this);';
	
	}
	
	
	function bootstrap_part1()
	{
	$this->long_name=editor_generic::long_name();
	$postargs['keys']=&$this->keys;
//	$postargs['context']=$this->context;
	
	$pp=explode('.',$this->long_name);
	$r='';
	foreach($pp as $pa)
	{
		if($r !='')$r.='.';
		$r.=$pa;
		$postargs['context'][$r]=$this->context[$r];
	}
	
	
	$postargs['name']=$this->long_name;
	$postargs['type']=editor_generic::long_type();
	$postargs['context'][$this->long_name]['htmlid']=$this->main_id();
	if(isset($postargs['context'][$this->long_name]['oid']))
		$this->oid=$postargs['context'][$this->long_name]['oid'];
	elseif(isset($this->oid))
		$postargs['context'][$this->long_name]['oid']=$this->oid;
	$send=editor_generic::array_to_post($postargs);
	$send.="&last_generated_id=' + last_generated_id + '";
	$send.="&val";
	$this->send=&$send;
	$this->postargs=&$postargs;
	}
	
	
	
	function bootstrap_part()
	{
	$this->long_name=editor_generic::long_name();

	$this->context[$this->long_name]['htmlid']=$this->main_id();
	/*if(isset($this->context[$this->long_name]['oid']))
		$this->oid=$this->context[$this->long_name]['oid'];
	elseif(isset($this->oid))*/
		$this->context[$this->long_name]['oid']=$this->oid;
	
	
	$pp=explode('.',$this->long_name);
	$r='';
	foreach($pp as $pa)
	{
		if($r !='')$r.='.';
		$r.=$pa;
		$context[$r]=$this->context[$r];
	}
	
	
	//print 'ue='.strlen(urlencode(serialize($context))).';gz='.strlen(urlencode(gzcompress(serialize($context)))).';b64='.strlen(urlencode(base64_encode(gzcompress(serialize($context))))).'<br/>';
	
	
	$send='keys='.urlencode(serialize($this->keys)).
	'&context='.	(editor_generic::em?urlencode(serialize($context)):urlencode(base64_encode(gzcompress(serialize($context))))).
#	'&name='.urlencode($this->long_name).
	'&name='.	urlencode(editor_generic::effective_name()).
	'&type='.urlencode(editor_generic::long_type());
	$send.="&last_generated_id=' + last_generated_id + '";
	$send.="&val";
	$this->send=&$send;
	//$this->postargs=&$postargs;
	}
	
	function array_to_post($arr)
	{
		$postdata='';
		foreach($arr as $k => $v)
		{
			if($postdata != '') $postdata.='&';
			if(is_array($v))$postdata.=urlencode($k).'='.urlencode(serialize($v));
			elseif(is_object($v))$postdata.=urlencode($k).'='.urlencode(serialize($v));
			else $postdata.=urlencode($k).'='.urlencode($v);
		}
		return $postdata;
	}
	
	function addeditor($name,$component)
	{
		$component->name=$name;
		$this->editors[$name]=$component;
		$component->com_parent=$this;
	}
	
	function long_name()
	{
		$n=$this->name;
		$e=$this;
		while(isset($e->com_parent))
		{
			$e=$e->com_parent;
#			if(isset($e->long_name))return $e->long_name.'.'.$n;
			$n=$e->name.'.'.$n;
		}
		return $n;
	}
	
	function effective_name()
	{
		$n=$this->name;
		$e=$this;
		$def=true;
		while(isset($e->com_parent))
		{
			$e=$e->com_parent;
			if($def)
			{
				if($e->default_editor!=$n)
				{
					$def=false;
				}else{
					$n=$e->name;
				}
			}
			if(!$def)
			{
				$n=$e->name.'.'.$n;
			}
		}
		return $n;
	}
	
	function long_type()
	{
		$n=$this->etype;
		$e=$this;
		while(isset($e->com_parent))
		{
			$e=$e->com_parent;
			$n=$e->etype.'.'.$n;
		}
		return $n;
	}
	
	function add_focus_restore($obj=NULL,$tgt=NULL)
	{
		if(!isset($obj))$obj=&$this;
		if(!isset($tgt))
		{
			$tgt=&$this;
			if(isset($this->main))$tgt=$this->main;
		}
		/*$st=	'keys='.urlencode(serialize($this->keys)).
			'&name='.urlencode($this->long_name).
			"&oid";*/
		$coo=urlencode(serialize(Array('keys' => $this->keys, 'name' => $this->long_name, 'oid' => $this->oid)));
		
//		$obj->attributes['onfocus'].='chse.send_or_push({obj:chse.request_counter,uri:\'/settings/focus.php\',static:\''.js_escape($st).'\',val:\''.js_escape($this->oid).'\'});';
//		$obj->attributes['onblur'].='chse.send_or_push({obj:chse.request_counter,uri:\'/settings/focus.php\',static:\'name=\',val:\'null\'});';
		$tgt->attributes['onfocus'].='document.cookie=\'focus_restore=\'+\''.js_escape($coo).'\';';
		$tgt->attributes['onblur'].='document.cookie=\'focus_restore=\'+\'\';';
		//$obj->attributes['title']=serialize($this->keys).';'.$this->long_name.';'.$this->oid;
		if(isset($_COOKIE['focus_restore'])&&($_COOKIE['focus_restore']!=''))
		{
			$v=unserialize($_COOKIE['focus_restore']);
			if(($v['name'] != '') && ($v['name']==$this->long_name) && ($v['keys']==$this->keys) && ($v['oid']==$this->oid))
				$obj->rootnode->endscripts['restore-focus']='document.onload_functions.push(function(){$i(\''.js_escape($obj->main_id()).'\').focus();});'.
				'';
		}
	}
	
	function handle_event($ev)
	{
		$ev->etype=$etype=preg_replace('/\..*/','',$ev->rem_type);
		
		$ev->parent_type=$ev->parent_type.'.'.$etype;
		$ev->rem_type=preg_replace('/^[^.]*\./','',$ev->rem_type);
		$ev->name=preg_replace('/\..*/','',$ev->rem_name);
		if($ev->name==$ev->rem_name && $this->default_editor != '')
		{
			$ev->name=$this->default_editor;
			$ev->rem_name.=".".$ev->name;
			$ev->long_name.=".".$ev->name;
		}
		$ev->parent_name.='.'.$ev->name;
		$ev->rem_name=preg_replace('/^[^.]*\./','',$ev->rem_name);
		
		if(class_exists($etype))
		{
			$obj=new $etype;
			if(method_exists($obj,'handle_event'))
				$obj->handle_event($ev);
		}
	}
}









//###############################################################################################3
//##################################   failure_viewer definition  ###############################3
//  is a simple hidden div(show details button,div(close button,span))
//  is designed to be appended to any editor that supports $ev->failure status displaying
//  check in callback(handle_event) for $ev->failure and set up failure_viewer::msg::text with it
//  this viewer is designed to show up only when there is an attempt to store invalid value
//  this viewer is designed to be attached only to controls imlementing change->see difference->submit model
//  when storing such a value fails (and only when) it should appear and give indication that things gone wrong
//  user will be able to see description: click button to show description, click 'close' button or show
//  button one more time to hide hovering div

//TODO: move css to external file to support skinning
//###############################################################################################3


class failure_viewer extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->btn=new dom_any_noterm('input');
		$this->btn->attributes['type']='button';
		$this->btn->attributes['value']='(!)';
		$this->append_child($this->btn);
		$this->msg=new dom_div;
		$this->msg->close=new dom_any_noterm('input');
		$this->msg->close->attributes['type']='button';
		$this->msg->close->attributes['value']='x';
		$this->msg->close->css_style['float']='right';
		$this->msg->append_child($this->msg->close);
		$this->msg->text=new dom_any('span');
		$this->msg->append_child($this->msg->text);
		$this->msg->css_style['position']='absolute';
		$this->msg->css_style['overflow']='hidden';
		$this->msg->css_style['height']='auto';
		$this->msg->css_style['width']='30em';
		$this->msg->css_style['display']='none';
		$this->msg->css_style['border']='1px solid red';
		$this->msg->css_style['background']='pink';
		
		$this->append_child($this->msg);
		$this->css_style['display']='none';
		
		
	}
	function html_head()
	{
		$this->btn->attributes['onclick']='var x=$i(\''.$this->msg->id_gen().'\'); if(x.style.display==\'\') x.style.display=\'none\' ; else x.style.display=\'\';';
		$this->msg->attributes['onclick']='$i(\''.$this->msg->id_gen().'\').style.display=\'none\' ;';
		parent::html_head();
	}
}


//###############################################################################################3
//##################################   editor_checkbox definition  ##############################3
//###############################################################################################3

class editor_checkbox extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->attributes['onchange']='chse.timerch(true);';
		$this->attributes['type']='checkbox';
		$this->etype='editor_checkbox';
		$this->main=$this;
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap();
		$this->attributes['onchange']="chse.timerch(true);";
		
		//$this->attributes['onfocus']='';
		//$this->attributes['onblur']='';
		// focus persistence test
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	function html()
	{
		unset($this->attributes['checked']);
		if($this->args[$this->context[$this->long_name]['var']]==1)$this->attributes['checked']='checked';
		parent::html();
	}
}

//###############################################################################################3
//##################################   editor_select definition    ##############################3
//###############################################################################################3

class editor_select extends dom_any
{
	function __construct()
	{
		dom_any::__construct('select');
		$this->attributes['onchange']='chse.timerch(true);';
		$this->etype=get_class($this);
		$this->main=$this;
		$this->option=new dom_any('option');
		$this->option_text =new dom_statictext;
		$this->append_child($this->option);
		$this->option->append_child($this->option_text);

		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap();
		$this->attributes['onchange']="chse.timerch(true);";
		if(is_array($this->options) && !isset($this->context[$this->long_name]["options"]))
			$this->context[$this->long_name]["options"]=serialize($this->options);
		if(!is_array($this->options) && isset($this->context[$this->long_name]["options"]))
			$this->options=unserialize($this->context[$this->long_name]["options"]);
		$this->context[$this->long_name]['htmlid']=$this->id_gen();
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	
	function html_inner()
	{
		unset($this->option->id);
		unset($this->option_text->id);
		if(is_array($this->options))foreach($this->options as $val => $text)
		{
			$this->option->attributes['value']=$val;
			if($val==$this->args[$this->context[$this->long_name]['var']])
				$this->option->attributes['selected']='selected';
			else
				unset($this->option->attributes['selected']);
			$this->option_text->text=$text;
			$this->option->html();
		}
	}
	
	function handle_event($ev)
	{
			print 'var x=chse.bgifc(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\',\'\');'.
			'';
	}
}

//###############################################################################################3
//##################################   editor_statictext definition  ############################3
//###############################################################################################3
class editor_statictext extends dom_statictext
{
	function __construct()
	{
		dom_statictext::__construct();
		$this->etype='editor_statictext';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->arg_key=$this->context[$this->long_name]['var'];
		//$this->text=$this->args[$this->context[$this->long_name]['var']];
	}
	
	function html()
	{
		$this->text=$this->args[$this->arg_key];
		parent::html();
	}
}

//###############################################################################################3
//###############################   editor_statictext_af definition  ############################3
//###############################################################################################3
class editor_statictext_af extends dom_statichtml
{
	function __construct()
	{
		dom_statichtml::__construct();
		$this->etype='editor_statictext_af';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->arg_key=$this->context[$this->long_name]['var'];
		//$this->text=$this->args[$this->context[$this->long_name]['var']];
	}
	
	function html()
	{
		$this->text=htmlspecialchars($this->args[$this->arg_key]);
		if($this->text=='')$this->text="&nbsp;";
		
		parent::html();
	}
}

//###############################################################################################3
//##################################   editor_statichtml definition  ############################3
//###############################################################################################3
class editor_statichtml extends dom_statichtml
{
	function __construct()
	{
		dom_statichtml::__construct();
		$this->etype='editor_statictext';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		$this->arg_key=$this->context[$this->long_name]['var'];
		//$this->text=$this->args[$this->context[$this->long_name]['var']];
	}
	
	function html()
	{
		$this->text=$this->args[$this->arg_key];
		parent::html();
	}
}

//###############################################################################################3
//##################################   editor_href   definition  ################################3
//###############################################################################################3

class editor_href extends dom_any
{
	function __construct()
	{
		dom_any_noterm::__construct('a');
		$this->etype=get_class($this);
		$this->main=$this;
		//$this->keys;
		//$this->args
	}
	
	
	
	
	function bootstrap()
	{
		
		$this->long_name=editor_generic::long_name();
		//editor_generic::bootstrap_part();
		unset($this->attributes['onfocus']);
		unset($this->attributes['onblur']);
		// focus persistence test
		//if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	
	function html_head()
	{
		$this->attributes['href']=preg_replace('/%s/',$this->args[$this->context[$this->long_name]['var']],$this->href);
		parent::html_head();
	}
	
	function after_build_before_children()
	{
		$this->rootnode->scripts['settings.js']='../settings/settings.js';
		$this->rootnode->scripts['core.js']='../js/core.js';
		$this->rootnode->scripts['commoncontrols.js']='/js/commoncontrols.js';

	}
}


//###############################################################################################3
//##################################   editor_button definition  ################################3
//###############################################################################################3


class editor_button extends dom_any_noterm
{
	function __construct()
	{
		dom_any_noterm::__construct('input');
		$this->main=$this;
		$this->attributes['type']='submit';
		//$this->css_style['margin']='1px';
		//$this->css_style['padding']='1px';
#		$this->css_style['font-size']='12px';
		$this->etype=get_class($this);
		//$this->keys;
		//$this->args
	}
	
	
	
	
	function bootstrap()
	{
		
		editor_generic::bootstrap_part();
		if(isset($this->value))$value=js_escape($this->value);
		if(isset($this->context[$this->long_name]['var']))$value=$this->args[$this->context[$this->long_name]['var']];
		if(isset($this->val_js))
		{
			$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:".$this->val_js.",c_id:this.id});";
		}else
			$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:'".js_escape(urlencode($value))."',c_id:this.id});";
		
		$this->attributes['onfocus']='';
		$this->attributes['onblur']='';
		// focus persistence test
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	
	function after_build_before_children()
	{
		$this->rootnode->scripts['settings.js']='../settings/settings.js';
		$this->rootnode->scripts['core.js']='../js/core.js';
		$this->rootnode->scripts['commoncontrols.js']='/js/commoncontrols.js';

	}
}

class editor_text_button extends editor_button
{
	function bootstrap()
	{
		parent::bootstrap();
		$this->attributes['value']=$this->args[$this->context[$this->long_name]['var']];
	}
}

class editor_valbutton extends editor_button
{
	function html_head()
	{
		if(isset($this->value))$value=$this->value;
		elseif(isset($this->context[$this->long_name]['var']))$value=$this->args[$this->context[$this->long_name]['var']];
		$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:'".js_escape(urlencode($value))."',c_id:this.id});";
		
		parent::html_head();
	}
}

class editor_valbutton_button extends dom_any
{
	function __construct()
	{
		dom_any::__construct('button');
		$this->main=$this;
		$this->etype=get_class($this);
	}
	
	function bootstrap()
	{
		
		editor_generic::bootstrap_part();
		$this->attributes['onfocus']='';
		$this->attributes['onblur']='';
		// focus persistence test
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	
	function html_head()
	{
		if(isset($this->value))$value=$this->value;
		elseif(isset($this->context[$this->long_name]['var']))$value=$this->args[$this->context[$this->long_name]['var']];
		$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:'".js_escape(urlencode($value))."',c_id:this.id});";
		
		parent::html_head();
	}
}

class editor_valbutton_image extends editor_button
{
	function __construct()
	{
		dom_any::__construct('input');
		$this->attributes['type']='image';
		$this->etype=get_class($this);
		$this->main=$this;
	}
	
	function html_head()
	{
		if(isset($this->value))$value=$this->value;
		elseif(isset($this->context[$this->long_name]['var']))$value=$this->args[$this->context[$this->long_name]['var']];
		$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:'".js_escape(urlencode($value))."',c_id:this.id});";
		
		parent::html_head();
	}
}

//###############################################################################################3
//##################################   editor_button_image definition  ##########################3
//###############################################################################################3


class editor_button_image extends editor_button
{
	function __construct()
	{
		parent::__construct();
		$this->attributes['type']='image';
		$this->attributes['src']='/i/null.png';
		$this->css_style['margin']='1px';
		$this->css_style['padding']='1px';
		$this->css_style['font-size']='12px';
		$this->etype='editor_button';
		$this->main=$this;
		//$this->keys;
		//$this->args
	}
	
}


//###############################################################################################3
//##################################   editor_text definition  ##################################3
//###############################################################################################3



class editor_text extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->ed=new dom_any_noterm('input');
		
		$this->append_child($this->ed);
		$this->etype='editor_text';
		$this->failure_viewer=new failure_viewer;
		$this->append_child($this->failure_viewer);
		$this->main=$this->ed;
		$this->ed->attributes['type']='text';
		$this->ed->attributes['onmouseover']='opera_fix(this);';
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
#		$this->custom_id=$this->ed->id_gen();
		editor_generic::bootstrap();
#		unset($this->custom_id);
		$this->ed->attributes=$this->attributes;//?????
		unset($this->attributes);
//		$this->long_name=editor_generic::long_name();
		$this->ed->attributes['onfocus'].=
			';this.failure_viewer=\''.js_escape($this->failure_viewer->id_gen()).'\';'.
			'this.failure_viewer_text=\''.js_escape($this->failure_viewer->msg->text->id_gen()).'\';'.
			'';
		//$this->attributes['value']=implode($this->args,';');
		
		
		
		
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
			'if(x){'.
			'$i(x.failure_viewer).style.display=\'\';'.
			'$i(x.failure_viewer_text).innerHTML=\''.js_escape(htmlspecialchars($ev->failure,ENT_QUOTES)).'\';'.
			'}'
			;
		}else
			print 'var x=chse.bgifc(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\',\'\');'.
			'if(x){'.
			//print 'var x=$i(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\');x.style.backgroundColor=\'\';'.
			'$i(x.failure_viewer).style.display=\'none\';'.
			'}';
	}
}

//###############################################################################################3
//##################################   editor_pass definition  ##################################3
//###############################################################################################3

class editor_pass extends editor_text
{
	
	function html()
	{
		$this->ed->attributes['value']='';
		parent::html();
	}
}






//###############################################################################################3
//##################################   editor_text_submit definition  ###########################3
//###############################################################################################3


class editor_text_submit1 extends dom_void
{
	function __construct()
	{
		dom_void::__construct();
		$this->etype='editor_text_submit';
		$this->bindval='';
		$this->callback_prefix='';
		$this->callback_uri='';
		$this->text_input=new dom_any('input');
		$this->text_input->attributes['type']='text';
		$this->button=new dom_any('input');
		$this->button->attributes['type']='submit';
		$this->button->attributes['value']='>';
		$this->append_child($this->text_input);
		$this->append_child($this->button);
		$this->main=$this->text_input;

		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap_part();
//		$this->long_name=editor_generic::long_name();
		$this->text_input->attributes['value']=$this->args[$this->context[$this->long_name]['var']];
		$this->button->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:\$i('".js_escape($this->main_id())."').value,c_id:this.id});";
		//$this->attributes['value']=implode($this->args,';');
		
	}
}

class editor_text_submit extends dom_any
{
	function __construct()
	{
		//dom_any::__construct('table');
		dom_any::__construct('div');
		//$this->css_style['width']='100px';
		//$this->css_style['height']='22px';
		$this->etype='editor_text_submit';
		$tbl=new dom_table;
		$this->append_child($tbl);
		//$this->css_class='editor_text_submit';
		$tbl->css_class='editor_text_submit';
		$tr=new dom_tr;
		//$this->append_child($tr);
		$tbl->append_child($tr);
		$td1=new dom_td;
		$tr->append_child($td1);
		$td2=new dom_td;
		$tr->append_child($td2);
		$td2->css_style['width']='1em';
		$this->text_input=new dom_any_noterm('input');
		$this->text_input->attributes['type']='text';
		$this->text_input->css_class='editor_text_submit';
		$this->button=new dom_any_noterm('input');
		$this->button->attributes['type']='submit';
		$this->button->css_style['width']='1.7em';
		$this->button->css_style['height']='1.7em';
		$this->button->attributes['value']='>';
		
		//$this->append_child($this->text_input);
		//$this->append_child($this->button);
		$td1->append_child($this->text_input);
		$td2->append_child($this->button);
		
		$td3=new dom_td;
		$tr->append_child($td3);
		$td3->css_style['width']='1em';
		$td4=new dom_td;
		$tr->append_child($td4);
		$td4->css_style['width']='1em';
		
		$this->button_cl=new dom_any_noterm('input');
		$this->button_cl->attributes['type']='submit';
		$this->button_cl->css_style['width']='1.7em';
		$this->button_cl->css_style['height']='1.7em';
		$this->button_cl->attributes['value']='X';
		$td3->append_child($this->button_cl);
		
		$this->button_un=new dom_any_noterm('input');
		$this->button_un->attributes['type']='submit';
		$this->button_un->css_style['width']='1.7em';
		$this->button_un->css_style['height']='1.7em';
		$this->button_un->attributes['value']='U';
		$td4->append_child($this->button_un);
		$this->main=$this->text_input;
		
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap_part();
//		$this->long_name=editor_generic::long_name();
		$this->text_input->attributes['onfocus']="if(!this.revert_val_stored){this.revert_val=this.value;this.revert_val_stored=1;}";
		$this->text_input->attributes['onmouseover']="if(!this.revert_val_stored){this.revert_val=this.value;this.revert_val_stored=1;}";
		$this->text_input->attributes['onkeydown']=
			"var mkc=event_to_mkc(event);".
			"if(mkc.m==mkc.CTRL && (mkc.keycode==90 || mkc.keycode==8))".
			"{".
					"event.preventDefault();".
			"}";
		
		$this->text_input->attributes['onkeypress']=
			"var mkc=event_to_mkc(event);".
			"if(mkc.keycode==90 && mkc.m==mkc.CTRL)".//ctrl+Z
			"{".
				"this.value=this.revert_val;".
				"event.preventDefault();".
			"};".
			"if(mkc.keycode==8 && mkc.m==mkc.CTRL)".//ctrl+Backspace
			"{".
				"this.value='';".
				"event.preventDefault();".
			"};".
			//"alert(event.keyCode);".
			//"this.value='c='+event.ctrlKey+';s='+event.shiftKey+';a='+event.altKey+';m='+event.metaKey;".
			"if(mkc.m==0 && mkc.keycode==13)".
			"{".
				"\$i('".$this->button->id_gen()."').onclick();".
			"}";
		$this->button->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:\$i('".js_escape($this->main_id())."').value,c_id:this.id});";
		$this->button_un->attributes['onclick']="var f=\$i('".js_escape($this->main_id())."');try{if(f.revert_val_stored)f.value=f.revert_val;}catch(e){}";
		$this->button_cl->attributes['onclick']="try{\$i('".js_escape($this->main_id())."').value='';}catch(e){};";
		
		//$this->attributes['value']=implode($this->args,';');
		
		// focus persistence test
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	
	function html()
	{
		$this->text_input->attributes['value']=$this->args[$this->context[$this->long_name]['var']];
		parent::html();
	}
	
	
	function after_build_before_children()
	{
		/*if(!isset($this->parentnode->inlinestyles['editor_text_submit']))
		$this->parentnode->inlinestyles['editor_text_submit']=
		'table.editor_text_submit{'.
		'height:100%;'.
		'width:100%;'.
		'border:0px;'.
		'border-collapse:collapse;'.
		'}'.
		'table.editor_text_submit tr td{'.
		'height:99%;'.
		'padding-bottom:3px;'.
		'}'.
		'input.editor_text_submit{'.
		'height:98%;'.
		'width:98%;'.
		'margin-top:-1px;'.
		'}'.
		'';*/
		
	}
}


//###############################################################################################3
//##################################   editor_textarea definition  ##############################3
//###############################################################################################3

class editor_textarea extends dom_any
{
	function __construct()
	{
		dom_any::__construct('textarea');
		$this->attributes['type']='text';
		$this->etype='editor_text';
		$this->attributes['onmouseover']='opera_fix(this);';
		$this->innerHTML=new dom_statictext;
		$this->append_child($this->innerHTML);
		$this->main=$this;
		//$this->keys;
		//$this->args
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap();
//		$this->long_name=editor_generic::long_name();
		//$this->attributes['value']=implode($this->args,';');
		
		// focus persistence test
		if(!isset($this->no_restore_focus))editor_generic::add_focus_restore();
	}
	
	function html()
	{
		$this->innerHTML->text=&$this->args[$this->context[$this->long_name]['var']];
		parent::html();
	}
	
	function handle_event($ev)
	{
		print 'chse.bgifc(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\',\'\');';
	}
}















//##########################################################################################
//##########################################################################################
//############################### COMPLEX COMPONENTS
//##########################################################################################
//##########################################################################################

//###############################################################################################3
//##################################   editor_pick_button definition  ###########################3
//###############################################################################################3


class editor_pick_button extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='editor_pick_button';
		$this->button=new editor_button;
		$this->button->value='editor_pick_button';
		$this->resdiv=new dom_div;
		$this->resdiv->css_style['display']='none';
		$this->resdiv->css_style['position']='absolute';
		$this->resdiv->css_style['border']='1px solid gray';
		$this->resdiv->css_style['background']='#DDDDDD';
		$this->append_child($this->button);
		$this->append_child($this->resdiv);
		editor_generic::addeditor('button',$this->button);
		$this->list_class='editor_pick_button_list';
		$this->main=$this->button;
	}
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		$this->context[$long_name]['oid']=$this->oid;
		$this->context[$long_name]['htmlid']=$this->id_gen();
		$this->context[$long_name]['list_class']=$this->list_class;
		$this->context[$long_name.'.button']['res_div']=$this->resdiv->id_gen();
		$this->context[$long_name.'.button']['control']='editor_pick_button';
		$this->button->keys=&$this->keys;
		$this->button->args=&$this->args;
		$this->button->context=&$this->context;
		if(!isset($this->button->attributes['value']))$this->button->attributes['value']='+';
//		$this->button->bootstrap();
	}
	function html_inner()
	{
		$this->button->bootstrap();
		dom_any::html_inner();
	}
	function html()
	{
		$this->html_inner();
	}
	function handle_event($ev)
	{
		switch($ev->rem_name)
		{
			//handle root object events here
		case 'button':
			
			$this->list_class=$ev->context[$ev->parent_name]['list_class'];
			$r= new $this->list_class;
			//$r->picklist=unserialize($ev->context[$ev->parent_name]['picklist']);
			//print 'chse.safe_alert(123,\''.$ev->parent_type.'\');';
			//exit;
			$r->etype=$ev->parent_type;
			$r->context=&$ev->context;
			$r->for_each_set('oid',$ev->context[$ev->parent_name]['oid']);
			$r->name=&$ev->parent_name;
			$r->keys=&$ev->keys;
			$r->bootstrap();
			print "var res=\$i('".js_escape($ev->context[$ev->long_name]['res_div'])."');".
//			"chse.safe_alert(123,res.style.display);".
			"if(res.style.display!='none')".
			"{".
			"res.style.display='none';".
			"}else{".
			"res.style.display='block';".
			"try{res.innerHTML=";
			reload_object($r);
			print
			"}catch(e){window.location.reload(true);};};";
			return true;
		default:
			;
		}
		editor_generic::handle_event($ev);
	}
}

//###############################################################################################3
//##################################   editor_pick_button_list definition  ######################3
//###############################################################################################3

class editor_pick_button_list extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->etype='editor_pick_button';
		$div=new dom_tr;
		$this->append_child($div);
		editor_generic::addeditor('text',new editor_statictext);
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['text']);
		
		editor_generic::addeditor('btnforw',new editor_button);
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['btnforw']);
		$this->editors['btnforw']->attributes['value']='123';
		$this->editors['btnforw']->no_restore_focus=1;
		
		editor_generic::addeditor('btnrev',new editor_button);
		$td=new dom_td;
		$div->append_child($td);
		$td->append_child($this->editors['btnrev']);
		$this->editors['btnrev']->attributes['value']='321';
		$this->editors['btnrev']->no_restore_focus=1;
		
	}
	
	function bootstrap()
	{
		//$this->picklist=$this->context
		$this->long_name=$long_name=editor_generic::long_name();
		$this->context[$long_name.'.text']['var']='c';
		$this->context[$long_name.'.btnforw']['var']='c';
		$this->context[$long_name.'.btnrev']['var']='c';
		
	}
	
	
	function html_inner()
	{
		//$this->long_name=$long_name=editor_generic::long_name();
		if(! isset($this->picklist))$this->picklist=unserialize($this->context[$this->long_name]['picklist']);
		reset($this->picklist);
		foreach($this->editors as $e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
		}
		foreach($this->picklist as $i => $r)
		{
			//$this->keys['col']=$r;
			$this->context[$this->long_name.'.btnforw']['dir']=0;
			$this->context[$this->long_name.'.btnrev']['dir']=1;
			//$this->editors['btnforw']->value=$r;
			//$this->editors['btnrev']->value=$r;
			$this->args['c']=$r;
			//$this->args['users.sort.dir']=$r->dir;
			$this->id_alloc();
			reset($this->editors);
			foreach($this->editors as $e)
				$e->bootstrap();
			dom_table::html_inner();
		}
	}
}



//###############################################################################################3
//##################################   editor_pick_button_static   ##############################3
//###############################################################################################3



class editor_pick_button_static extends dom_any
{
	function __construct()
	{
		dom_any::__construct('div');
		$this->etype='editor_pick_button_static';
		$this->button=new editor_button;
		$this->button->value='editor_pick_button';
		$this->resdiv=new dom_div;
		$this->resdiv->css_style['display']='none';
		$this->resdiv->css_style['position']='absolute';
		$this->resdiv->css_style['border']='1px solid gray';
		$this->resdiv->css_style['background']='#DDDDDD';
		$this->append_child($this->button);
		$this->append_child($this->resdiv);
		$this->list=new editor_pick_button_static_list;
		editor_generic::addeditor('button',$this->button);
		$this->main=$this->button;
		$this->resdiv->append_child($this->list);
		$this->context=Array();
	}
	function bootstrap()
	{
		$long_name=editor_generic::long_name();
		$this->context[$long_name]['oid']=$this->oid;
		$this->context[$long_name]['htmlid']=$this->id_gen();
		$this->context[$long_name.'.button']['res_div']=$this->resdiv->id_gen();
		$this->button->keys=&$this->keys;
		$this->button->args=&$this->args;
		$this->button->context=&$this->context;
		if(!isset($this->button->attributes['value']))
		{
			$this->button->attributes['value']='+';
			$a=$this->args[$this->context[$long_name]['var']];
			if($a != '')$this->button->attributes['value']=$a;
		}
		$this->list->name=$this->name;
		$this->list->show_button=&$this->button;
		if(isset($this->picklist))$this->list->picklist=$this->picklist;
		if(isset($this->buttons))$this->list->buttons=$this->buttons;
		$this->list->keys=&$this->keys;
		$this->list->args=&$this->args;
		$this->list->context=&$this->context;
		$this->list->com_parent=$this->com_parent;
		
	}
	function html_inner()
	{
		$this->button->bootstrap();
		$this->button->attributes['onclick'].=
		"var res=\$i('".js_escape($this->resdiv->id_gen())."');".
		"if(res.style.display!='none')".
		"{".
		"res.style.display='none';".
		"}else{".
		"res.style.display='block';".
		"};";
		$this->list->bootstrap();
		//dom_any::html_inner();
		$this->button->html();
		$this->resdiv->html();
	}
	function html()
	{
		$this->html_inner();
	}
	function handle_event($ev)
	{
	}
}

//###############################################################################################3
//##################################   editor_pick_button_static_list definition  ###############3
//###############################################################################################3


class editor_pick_button_static_list extends dom_table
{
	function __construct()
	{
		dom_table::__construct();
		$this->etype='editor_pick_button_static';
		$this->div=new dom_tr;
		$this->append_child($this->div);
		
		editor_generic::addeditor('text',new editor_statictext);
		$this->td1=$td=new dom_td;
		$this->div->append_child($td);
		$td->append_child($this->editors['text']);
		
		editor_generic::addeditor('button',new editor_button);
		$this->td2=$td=new dom_td;
		$this->div->append_child($td);
		$td->append_child($this->editors['button']);
		$this->editors['button']->no_restore_focus=1;
	}
	
	function bootstrap()
	{
		//$this->picklist=$this->context
		$this->long_name=$long_name=editor_generic::long_name();
		$this->context[$long_name.'.text']['var']='c';
		if(! isset($this->buttons))$this->buttons=unserialize($this->context[$this->long_name]['buttons']);
		if(isset($this->buttons) && is_array($this->buttons))
			foreach($this->buttons as $i => $v)
			{
				$this->context[$long_name.'.'.$i]['var']='c';
			}
		else{
			$this->buttons=Array('select'=>'<?>');
			$this->context[$long_name.'.select']['var']='c';
		}
	}
	
	
	function html_inner()
	{
		//$this->long_name=$long_name=editor_generic::long_name();
		if(! isset($this->picklist))$this->picklist=unserialize($this->context[$this->long_name]['picklist']);
		//print $this->long_name;
		reset($this->editors);
		foreach($this->editors as $e)
		{
			$e->args= &$this->args;
			$e->keys= &$this->keys;
			$e->context= &$this->context;
		}
		reset($this->picklist);
		foreach($this->picklist as $i => $r)
		{
			//$this->keys['col']=$r;
			$this->args['c']=$r;
			//$this->args['users.sort.dir']=$r->dir;
			$this->id_alloc();
			$this->editors['text']->bootstrap();
			$this->div->html_head();
			$this->td1->html();
			
			foreach($this->buttons as $bi => $br)
			{
				$this->editors['button']->name=$bi;
				$this->editors['button']->id_alloc();
				$this->editors['button']->bootstrap();
				$this->editors['button']->attributes['value']=$br;
				$this->editors['button']->attributes['onclick'].='$i(\''.js_escape($this->show_button->id_gen()).'\').value=\''.js_escape($r).'\';$i(\''.js_escape($this->parentnode->id_gen()).'\').style.display=\'none\';';
				$this->td2->html();
			}
			//dom_table::html_inner();
			$this->div->html_tail();
		}
	}
}




//###############################################################################################3
//##################################   editor_text_autosuggest definition  ######################3
//###############################################################################################3


class editor_text_autosuggest extends dom_void
{
	function __construct()
	{
		$this->text=new dom_any_noterm('input');
		$this->ed=&$this->text;
		$this->main=$this->text;
		$this->text->attributes['type']='text';
		$this->text->attributes['autocomplete']='off';
		$this->etype=get_class($this);
		$this->div=new dom_div;
		$this->div->css_style['display']='none';
		$this->div->css_style['position']='absolute';
		$this->div->css_style['min-width']='50px';
		$this->div->css_style['max-height']='200px';
		$this->div->css_style['overflow']='auto';
		//editor_generic::addeditor('text',$this->text);
		$this->append_child($this->text);
		$this->append_child($this->div);
		$this->keys=Array();
		$this->context=Array();
		$this->list_class='editor_text_autosuggest_list_example';
	}
	
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		//$this->context[$this->long_name.'.text']['var']=$this->context[$this->long_name]['var'];
		//print $this->context[$this->long_name.'.text']['var'];
		$this->context[$this->long_name]['retid']=$this->div->id_gen();
		$this->context[$this->long_name]['htmlid']=$this->text->id_gen();
		$this->context[$this->long_name]['list_class']=$this->list_class;
		//$this->text->args=&$this->args;
		$this->text->keys=&$this->keys;
		$this->text->context=&$this->context;
		/*if(isset($this->context[$this->long_name]['oid']))
			$this->oid=$this->context[$this->long_name]['oid'];
		elseif(isset($this->oid))*/
			$this->context[$this->long_name]['oid']=$this->oid;
		
		$this->custom_id=$this->text->id_gen();
		editor_generic::bootstrap_part();
		unset($this->custom_id);
	}
	
	function html_inner()
	{
	$this->text->attributes['value']=$this->args[$this->context[$this->long_name]['var']];
	$this->text->attributes['onfocus']="chse.activatemon({obj:this,objtype:'editor_text',static:'".$this->send."'});this.selectionStart=0;this.selectionEnd=this.value.length;clearTimeout(this.hide_timeout);chse.send_or_push({static:'".$this->send."',val:encodeURIComponent(this.value),c_id:this.id});";
	$this->text->attributes['onfocus'].="\$i('".js_escape($this->div->id_gen())."').tabIndex=1000;";
	$this->text->attributes['onblur']="chse.latedeactivate(this);if(this.refresh_timeout)clearTimeout(this.refresh_timeout);this.hide_timeout=setTimeout('\$i(\\'".js_escape($this->div->id_gen())."\\').style.display=\\'none\\';',200);";
	
//	$this->div->attributes['onmousedown']='this.style.backgroundColor=\'red\';event.preventDefault();event.stopPropagation();return false;$i(\''.js_escape($this->text->id_gen()).'\').focus();';
	$this->div->attributes['onmousedown']='event.preventDefault();event.stopPropagation();return false;$i(\''.js_escape($this->text->id_gen()).'\').focus();';
//	$this->div->attributes['onmouseup']='this.style.backgroundColor=\'blue\'';

//	$this->text->attributes['onkeypress']="editor_text_autosuggest_keypress(object,event,inp_id,div_id);";
	$this->text->attributes['onkeypress']="editor_text_autosuggest_keypress(this,event,'".js_escape($this->text->id_gen())."','".js_escape($this->text->id_gen())."','".js_escape($this->div->id_gen())."');";
	//up=38,down=40,left=37,right=39,enter=13
	//keynum = e.keyCode;
	// focus persistence test
	if(!isset($this->no_restore_focus))editor_generic::add_focus_restore($this->ed);
	dom_void::html_inner();
	}
	
	function handle_event($ev)
	{
		//print 'alert(\''.$ev->long_name.'\');';
		// exit;
			//handle root object events here
		
		if($ev->rem_type==$this->etype)//self targeted event
		{
			
			$customid=$ev->context[$ev->long_name]['retid'];
			//print $customid;exit;
			$oid=$ev->context[$ev->long_name]['oid'];
			$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$list_class=$ev->context[$ev->long_name]['list_class'];
			
/*			$r= new $list_class;
			$r->context=&$ev->context;
			$r->input_part=$_POST['val'];
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->text_inp=$htmlid;
			$r->bootstrap();
			print "var a=\$i('".js_escape($customid)."');try{a.innerHTML=".reload_object($r).
			"a.scrollTop=0;}catch(e){ window.location.reload(true);};";
			print 'a.style.display=\'block\';';
			$js='';
			foreach($r->result_array as $v)
			{
				if($js!='')$js.=',';
				$js.='{id:\''.js_escape($v->id).'\',val:\''.js_escape($v->val).'\'}';
			}
			print '$i(\''.js_escape($htmlid).'\').as_objects=['.$js.'];';
			print '$i(\''.js_escape($htmlid).'\').as_id = null;';*/
		}
		print "/*\nln:".$ev->long_name.';rn:'.$ev->rem_name.';pn:'.$ev->parent_name.';n:'.$ev->name.";*/\n";
		if($ev->rem_name=='text')
		{
			//child node targeted event
			
			$customid=$ev->context[$ev->parent_name]['retid'];
			$oid=$ev->context[$ev->long_name]['oid'];
			$htmlid=$ev->context[$ev->long_name]['htmlid'];
			$list_class=$ev->context[$ev->parent_name]['list_class'];
		}
			//common part
			$r= new $list_class;
			$r->context=&$ev->context;
			$r->keys=&$ev->keys;
			$r->input_part=$_POST['val'];
			$r->oid=$oid;
			$r->name=$ev->parent_name;
			$r->etype=$ev->parent_type;
			$r->text_inp=$htmlid;
			$r->bootstrap();
			print "var nya=\$i('".js_escape($customid)."');".
			"if(!nya.hide_timeout && chse.ismonitored(\$i('".js_escape($htmlid)."')))".
			"{".
			"try{nya.innerHTML=";
			reload_object($r);
			print
			"nya.scrollTop=0;}catch(e){ window.location.reload(true);};";
			print 'nya.style.display=\'block\';';
			$js='';
			foreach($r->result_array as $v)
			{
				if($js!='')$js.=',';
				$js.='{id:\''.js_escape($v->id).'\',val:\''.js_escape($v->val).'\'}';
			}
			print '$i(\''.js_escape($htmlid).'\').as_objects=['.$js.'];';
			print '$i(\''.js_escape($htmlid).'\').as_id = null;};';
			//exit;
		//}
		//editor_text::handle_event($ev);
		print 'chse.bgifc(\''.js_escape($ev->context[$ev->long_name]['htmlid']).'\',\'\');';
		return;
		if($ev->rem_type==$this->etype)//always stop propagation of self targeted events
			return;
		
		editor_generic::handle_event($ev);
	}
}




//###############################################################################################3
//##################################   editor_text_autosuggest_list example implementation   ####3
//###############################################################################################3


class editor_text_autosuggest_list_example extends dom_table
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
		$this->etype='editor_text_autosuggest';
		$this->args=Array();
		$this->keys=Array();
		$this->list_items=Array('this','is','example','of','editor_text_autosuggest','implementation.','you','forgot','to','set','editor_text_autosuggest::list_class','to','editor_text_autosuggest_list_example','implementation','or','set','editor_text_autosuggest_list_example::list_items,','damn','bastard','type','some','letters','to','see','filtered','list');
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
		
		$this->tr->attributes['onmouseover']=
		"var text_inp=\$i('".js_escape($this->text_inp)."');".
		"if(text_inp.as_objects)".
		"{".
			"if(text_inp.as_id || text_inp.as_id==0)".
			"{".
				"var s=\$i(text_inp.as_objects[text_inp.as_id].id).style;".
				"s.backgroundColor='white';".
				"s.color='';".
			"};".
			"text_inp.as_id='".js_escape($it)."';".
			"if(text_inp.as_id || text_inp.as_id==0)".
			"{".
				"var s=\$i(text_inp.as_objects[text_inp.as_id].id).style;".
				"s.backgroundColor='blue';".
				"s.color='white';".
			"}".
		"}".
		"";
		$this->tr->attributes['onmouseout']=
		"var text_inp=\$i('".js_escape($this->text_inp)."');".
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
		"var text_inp=\$i('".js_escape($this->text_inp)."');".
		"if(text_inp.as_objects)".
		"{".
			"if(text_inp.as_id || text_inp.as_id==0)".
				"text_inp.value=text_inp.as_objects[text_inp.as_id].val;".
			"text_inp.focus();".
		"}".
		"";
		$this->tr->attributes['onmousedown']="event.preventDefault();\$i('".js_escape($this->text_inp)."').focus();";
		$this->tr->attributes['onmouseup']="\$i('".js_escape($this->text_inp)."').focus();$click";
		$this->tr->css_style['cursor']='pointer';

	}
	
	function html_inner()
	{
		$a=Array();
		foreach($this->list_items as $v)
		{
			if(!isset($this->nofilter))
				if(!preg_match('/'.preg_quote($this->input_part,'/').'/',$v))continue;
			if($this->input_part=='')
			{
				$this->args['i']=htmlspecialchars($v);
			}else{
				$v1=htmlspecialchars($this->input_part);
				$v2=htmlspecialchars($v);
				$this->args['i']=preg_replace('/'.preg_quote($v1,'/').'/','<span style=\'font-size:1.2em;\'>'.$v1.'</span>',$v2);
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
//##################################   editor_text_autosuggest_session  #########################3
//###############################################################################################3




class editor_text_autosuggest_session extends editor_text_autosuggest
{
	function __construct()
	{
		parent::__construct();
		$this->etype='editor_text_autosuggest_session';
		$this->list_class='editor_text_autosuggest_sess_list';
	}
	function bootstrap()
	{
		$this->long_name=editor_generic::long_name();
		if(!isset($this->as_id))
		{
			$values=&$_SESSION['etas'][$this->long_name];
			if(is_array($values))$as_id=count($values); else $this->as_id=0;
			$this->context[$this->long_name]['as_id']=$as_id;
		}
		parent::bootstrap();
	}
}





class editor_text_autosuggest_sess_list extends editor_text_autosuggest_list_example
{
	function __construct()
	{
		parent::__construct();
		unset($this->list_items);
	}
	
	function html_inner()
	{
		$a=Array();
		//print_r($_SESSION);
		$this->list_items=&$_SESSION['etas'][$this->long_name];
		$this->as_id=$this->context[$this->long_name]['as_id'];
		if(!is_array($this->list_items))
		{
			$this->list_items=Array();
		}
		unset($this->list_items[$this->as_id]);
		if(!in_array($this->input_part,$this->list_items))
			if($this->input_part!='')
				$this->list_items[$this->as_id]=$this->input_part;
		foreach($this->list_items as $v)
		{
			if(!preg_match('/'.preg_quote($this->input_part,'/').'/',$v))continue;
			if($this->input_part=='')
			{
				$this->args['i']=htmlspecialchars($v);
			}else{
				$v1=htmlspecialchars($this->input_part);
				$v2=htmlspecialchars($v);
				$this->args['i']=preg_replace('/'.preg_quote($v1,'/').'/','<span style=\'font-size:1.2em;\'>'.$v1.'</span>',$v2);
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






//##########################################################################################
//##########################################################################################
//################################# CONTAINERS
//##########################################################################################
//##########################################################################################





//##########################################################################################
//#############################  container_captioned  ######################################
//##########################################################################################

class container_captioned extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->topdiv=new dom_div;
		$this->bottomdiv=new dom_div;
		$this->toptext= new dom_statictext;
		dom_div::append_child($this->topdiv);
		dom_div::append_child($this->bottomdiv);
		$this->topdiv->append_child($this->toptext);
		$this->css_style['background']='#FFE0FF';
		$this->css_style['border']='1px solid #887888';
		$this->css_style['margin']='4px';
		$this->topdiv->css_style['background']='#220022';
		$this->topdiv->css_style['color']='white';
		$this->bottomdiv->css_style['background']='white';
		
	}
	function append_child($child)
	{
		$this->bottomdiv->append_child($child);
		return $this;
	}
	function html_inner()
	{
		$this->toptext->text=$this->caption;
		parent::html_inner();
	}
}









//##########################################################################################
//#############################  dom_tab_control  ##########################################
/*
	div tabarea(div tabs[id].selector)
	div tabs[id].tab
	function add_tab(id,name)
	

*/

class dom_tab_control extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->tabarea=new dom_div;
		$this->tabarea->css_style['background-color']='white';
		$this->tabarea->css_style['overflow']='hidden';
		$this->tabarea->css_style['width']='auto';
		$this->tabarea->css_style['height']='auto';
		$this->append_child($this->tabarea);
		
		$this->js=new dom_js;
		$this->append_child($this->js);
//		$this->itemarea=new dom_div;
//		$this->itemarea->css_style['height']='auto';
//		$this->append_child($this->itemarea);
	}
	function add_tab($id,$name)
	{
		$tab->div=new dom_div;
		$tab->div->css_style['display']='none';
		$tab->selector=new dom_div;
		$tab->selector->css_style['float']='left';
		$tab->selector->css_style['margin-left']='3px';
		$tab->selector->css_style['margin-right']='3px';
		$txt=new dom_statictext;
		$txt->text=$name;
		$tab->selector->append_child($txt);
		$this->tabs[$id]=$tab;
		$this->tabarea->append_child($tab->selector);
		$this->append_child($tab->div);
//		$this->itemarea->append_child($tab->div);
	}
	
	function preload_settings($oid,$setting,$settings_array)
	{
		$this->activetab=$settings_array[$setting];
		$this->settings_data->oid=$oid;
		$this->settings_data->setting=$setting;
	}
	
	function html()
	{
		$thisid=$this->id_gen();
		foreach($this->tabs as $ind => $tab)
		{
			$tabdivid=$tab->div->id_gen();
			$this->activetab=$this->rootnode->setting_val($this->oid,$this->name.'.activetab','');
			$setting_store="save_setting_value('".js_escape($this->oid)."','".js_escape($this->name.".activetab")."','".js_escape($ind)."');";
			$setting_store_null="save_setting_value('".js_escape($this->oid)."','".js_escape($this->name.".activetab")."','".js_escape($ind)."');";
			$tab->selector->attributes['onclick']=
			"if(\$i('".$thisid."').active_tab)".
				"\$i(\$i('".$thisid."').active_tab).style.display='none';".
			"if(\$i('".$thisid."').active_tab_selector)".
			"{".
				"var inactive_sel_style=\$i(\$i('".$thisid."').active_tab_selector).style;".
				"inactive_sel_style.fontWeight='';".
				"inactive_sel_style.backgroundColor='white';".
				"inactive_sel_style.borderTop='';".
				"inactive_sel_style.borderLeft='';".
				"inactive_sel_style.borderRight='';".
			"}".
			"if(\$i('".$thisid."').active_tab!='".$tabdivid."'){\n".
			$setting_store.
			
			"var st=\$i('".$tabdivid."').style;".
			"st.display='block';".
			"this.style.backgroundColor=st.backgroundColor;".
			"this.style.borderTop='2px groove black';".
			"this.style.borderLeft='2px groove black';".
			"this.style.borderRight='2px groove black';".
			"this.style.fontWeight='bold';".
			"\$i('".$thisid."').active_tab='".$tabdivid."';".
			"\$i('".$thisid."').active_tab_selector=this.id;".
			
			"}else{\n".
			$setting_store_null.
			"\$i('".$thisid."').active_tab=null;".
			"\$i('".$thisid."').active_tab_selector=null;}".
			
			"";
		}
		if(isset($this->activetab))
		{
			#if(isset($this->tabs[$this->activetab]->selector))$this->rootnode->endscripts[]="\$i('".$this->tabs[$this->activetab]->selector->id_gen()."').onclick();";
			if(isset($this->tabs[$this->activetab]->selector))$this->js->script="\$i('".$this->tabs[$this->activetab]->selector->id_gen()."').onclick();";
		}
		dom_div::html();
	}
	
	function after_build_before_children()
	{
		$this->rootnode->scripts['settings.js']='../settings/settings.js';
		$this->rootnode->scripts['core.js']='../js/core.js';
		$this->rootnode->scripts['commoncontrols.js']='/js/commoncontrols.js';

	}
}










//###############################################################################################3
//##################################   container_tab_control definition  ########################3
//###############################################################################################3




class container_tab_control extends dom_div
{
	//$this->no_settings - inhibit usage of settings backend
	
	function __construct()
	{
		dom_div::__construct();
		$this->etype='container_tab_control';
		$this->tabarea=new dom_div;
		$this->tabarea->css_style['background-color']='white';
		$this->tabarea->css_style['overflow']='hidden';
		$this->tabarea->css_style['width']='auto';
		$this->tabarea->css_style['height']='auto';
		$this->append_child($this->tabarea);
		
//		$this->itemarea=new dom_div;
//		$this->itemarea->css_style['height']='auto';
//		$this->append_child($this->itemarea);
	}
	function add_tab($id,$name)
	{
		$tab->div=new dom_div;
		$tab->div->css_style['display']='none';
		//$tab->selector=new dom_div;
		$tab->selector=new dom_any('button');
		$tab->selector->css_style['float']='left';
		$tab->selector->css_style['margin-left']='3px';
		$tab->selector->css_style['margin-right']='3px';
		$txt=new dom_statictext;
		$txt->text=$name;
		$tab->selector->append_child($txt);
		$this->tabs[$id]=$tab;
		$this->tabarea->append_child($tab->selector);
		$this->append_child($tab->div);
//		$this->itemarea->append_child($tab->div);
	}
	
	function html_inner()
	{
		
		$long_name=editor_generic::long_name();
		$thisid=$this->id_gen();
		if(!isset($this->no_settings))
			$this->activetab=$this->rootnode->setting_val($this->oid,$long_name.'.activetab','');
		else
			unset($this->activetab);
		$cnt=0;
		foreach($this->tabs as $ind => $tab)
		{
			$cnt++;
			$tabdivid=$tab->div->id_gen();
			if(isset($this->no_settings))
			{
				$setting_store='';
				$setting_store_null='';
			}else{
				$setting_store="save_setting_value('".js_escape($this->oid)."','".js_escape($long_name.".activetab")."','".js_escape($ind)."');";
				$setting_store_null="save_setting_value('".js_escape($this->oid)."','".js_escape($long_name.".activetab")."','');";
			};
			$tab->selector->attributes['onclick'].=
			"var ti=\$i('".$thisid."');".
			"if(ti.active_tab)".
				"ti.childNodes[ti.active_tab].style.display='none';".
			"if(ti.active_tab_selector)".
			"{".
				"var inactive_sel_style=\$i(ti.active_tab_selector).style;".
				"inactive_sel_style.fontWeight='';".
				"inactive_sel_style.backgroundColor='white';".
				"inactive_sel_style.borderTop='';".
				"inactive_sel_style.borderLeft='';".
				"inactive_sel_style.borderRight='';".
			"}".
			"if(ti.active_tab!='".$cnt."'){\n".
			$setting_store.
			
			"var st=ti.childNodes[".$cnt."].style;".
			"st.display='block';".
			"this.style.backgroundColor=st.backgroundColor;".
			"this.style.borderTop='2px groove black';".
			"this.style.borderLeft='2px groove black';".
			"this.style.borderRight='2px groove black';".
			"this.style.fontWeight='bold';".
			"ti.active_tab='".$cnt."';".
			"ti.active_tab_selector=this.id;".
			
			"}else{\n".
			$setting_store_null.
			"ti.active_tab=null;".
			"ti.active_tab_selector=null;}".
			
			"";
		}
		//$this->js=new dom_js;
		//$this->append_child($this->js);
		if(isset($this->activetab))
		{
//		print '//'.$this->activetab;
			//reverted to old activation method
			//may introduce weird behavior in workers_container
			if(isset($this->tabs[$this->activetab]->selector))$this->rootnode->endscripts[]="\$i('".$this->tabs[$this->activetab]->selector->id_gen()."').onclick();";
			//if(isset($this->tabs[$this->activetab]->selector))$this->js->script="\$i('".$this->tabs[$this->activetab]->selector->id_gen()."').onclick();";
		}
		dom_div::html_inner();
	}
	
	function bootstrap()
	{
		foreach($this->tabs as $ind => $tab)$tab->selector->attributes['onclick']='';
	}
	
	function after_build_before_children()
	{
		$this->rootnode->scripts['settings.js']='../settings/settings.js';
		$this->rootnode->scripts['core.js']='../js/core.js';
		$this->rootnode->scripts['commoncontrols.js']='/js/commoncontrols.js';

	}
}


class container_tab_control_l extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype='container_tab_control_l';
		
//		$this->itemarea=new dom_div;
//		$this->itemarea->css_style['height']='auto';
//		$this->append_child($this->itemarea);
	}
	function add_tab($id,$name)
	{
		$tab->div=new dom_div;
		$tab->selector=new dom_div;
		$txt=new dom_statictext;
		$txt->text=$name;
		$tab->selector->append_child($txt);
		$this->tabs[$id]=$tab;
		
		$this->tabarea=new dom_div;
		$this->append_child($this->tabarea);
		
		$this->tabarea->append_child($tab->selector);
		$this->tabarea->append_child($tab->div);
//		$this->itemarea->append_child($tab->div);
		$tab->selector->css_style['text-align']='center';
		//$tab->selector->css_style['font-size']='1.5em';
	}
	
	
	function bootstrap()
	{
	}
	
}


class container_hidden1 extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->css_class='container_hidden';
		$this->title_div=new dom_div;
		dom_div::append_child($this->title_div);
		$this->hidden_div=new dom_div;
		dom_div::append_child($this->hidden_div);
		$this->hidden_div->css_style['display']='none';
		$this->hideshow_button=new dom_textbutton;
		$this->hideshow_button->css_class='container_hidden';
		$this->title_div->append_child($this->hideshow_button);
		$this->hideshow_button->css_style['float']='left';
		$this->hideshow_button->attributes['value']='+';
		$this->title_text=new dom_statictext;
		$this->title_div->append_child($this->title_text);
		
	}
	function append_child($child)
	{
		$this->hidden_div->append_child($child);
		return $this;
	}
	function html_inner()
	{
		if(isset($this->title))$this->title_text->text=&$this->title;
		$this->hideshow_button->attributes['onclick']=
		'var div=$i(\''.js_escape($this->hidden_div->id_gen()).'\');'.
		'if(div){if(div.style.display==\'none\')div.style.display=\'\';else div.style.display=\'none\';};';
		parent::html_inner();
	}
}

class container_hidden extends dom_div
{
	const PLUS_PNG='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9gMCRYQCaH0414AAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAABBUlEQVQoz6WTQU4CQRBFX2EjGscFCRBi4s4rsPJAnskDeQt3JIQIAQOowcnw3XxMOXEj/qQy3Z15nar61YElqQOcA1dA39ED9sDa8QZ8AooIlQSeAV2gAkYR8ZQuvgdqg41DpQVeAtfAkJ8aAhvgwyCSKE61C1wY7AODFjwAVobxty6usUrgDTBuwWNg5/Ua2AK7YmDk1Ab+8bYFH/cVsAQWwAuSJoD+GpImHdtxinod+3iK9iHp7reaI+Ih+fwITIF5rrm4e7V9XKWuZk2BZ2DW7vZx5N6Tj1ULnhucGfz2OY9cAK9OLWvp860zq4GmRMRBEmlyNq4pa5HGswaaiDjEf17VF/nLgbK5EkeuAAAAAElFTkSuQmCC';
	const
	MINUS_PNG='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9gMCRYPCWyu7cAAAAAZdEVYdENvbW1lbnQAQ3JlYXRlZCB3aXRoIEdJTVBXgQ4XAAAAlUlEQVQoz6XTzWrCQBSG4Wfi1ChdVfAOvIXc/wUV7EokrebrZgQ3AU0OvMvzzpw/VkSBJOXtxFJSknTomugVSRpTxRa18RDNxdS44Vbx2dg30WbmB8Edv7jiUvGFIw5N8jHz+oQ/XHDGtyTDUx0vk2To0C+cVN9hXJg8liSnpTVX/LQOnt/tdkmyWzrnVRu2arfXHJV/MrNOe3WpLV8AAAAASUVORK5CYII=';
	function __construct()
	{
		parent::__construct();
		$this->css_class='container_hidden';
		$this->title_div=new dom_div;
		dom_div::append_child($this->title_div);
		$this->hidden_div=new dom_div;
		dom_div::append_child($this->hidden_div);
		$this->hidden_div->css_style['display']='none';
		$this->hideshow_button=new dom_any_noterm('input');
		$this->hideshow_button->attributes['type']='image';
		
		$this->hideshow_button->attributes['src']=container_hidden::PLUS_PNG;
		$this->hideshow_button->attributes['alt']='+';
		$this->hideshow_button->css_class='container_hidden';
		$this->title_div->append_child($this->hideshow_button);
		$this->hideshow_button->css_style['float']='left';
		$this->title_text=new dom_statictext;
		$this->title_div->append_child($this->title_text);
		
	}
	function append_child($child)
	{
		$this->hidden_div->append_child($child);
		return $this;
	}
	function html_inner()
	{
		$this->fold_state=$this->rootnode->setting_val($this->oid,$long_name.'.fold_state','');
		
		if(isset($this->title))$this->title_text->text=&$this->title;
		$this->hideshow_button->attributes['onclick']=
		'var div=$i(\''.js_escape($this->hidden_div->id_gen()).'\');'.
		'if(div)'.
		'{'.
			'if(div.style.display==\'none\')'.
			'{'.
				'div.style.display=\'\';'.
				"save_setting_value('".js_escape($this->oid)."','".js_escape($long_name.".fold_state")."','1');".
			'}'.
			'else'.
			'{'.
				'div.style.display=\'none\';'.
				"save_setting_value('".js_escape($this->oid)."','".js_escape($long_name.".fold_state")."','');".
			'}'.
		'};';
		$this->hideshow_button->attributes['onclick'].=
		'var hsbtn=$i(\''.js_escape($this->hideshow_button->id_gen()).'\');'.
		'if(div && hsbtn){'.
		//'alert(hsbtn.attributes.getNamedItem(\'src\').nodeValue);'.
			'if(div.style.display==\'none\')'.
			'{'.
				'hsbtn.src=\''.js_escape(container_hidden::PLUS_PNG).'\';'.
				'hsbtn.alt=\'+\';'.
			'}'.
			'else '.
			'{'.
				'hsbtn.src=\''.js_escape(container_hidden::MINUS_PNG).'\';'.
				'hsbtn.alt=\'-\';'.
			'};'.
		'};';
		if($this->fold_state=='1')
		{
			$this->hideshow_button->attributes['src']=container_hidden::MINUS_PNG;
			$this->hideshow_button->attributes['alt']='-';
			unset($this->hidden_div->css_style['display']);
		}
		
		parent::html_inner();
	}
}


###################################################################3
################# container_autotable #############################3
###################################################################3
class container_autotable extends dom_table
{
	function __construct()
	{
		parent::__construct();
	}
	
	function html_inner()
	{
		if(is_array($this->nodes))
		{
			$this->rootnode->out("<tr>");
			reset($this->nodes);
			foreach($this->nodes as $node)
			{
				$this->rootnode->out("<td>");
				$node->html();
				$this->rootnode->out("</td>");
			}
			$this->rootnode->out("</tr>");
		}
		
	}
	
}

##################################################
############# 
############# use "var a=\$i('".$editor_hidden->id_gen()."');
############# chse.send_or_push({static:'".$editor_hidden->send."',val:'xxx',c_id:this.id});
############# then handle generated event


class editor_hidden extends dom_any_noterm
{
	function __construct()
	{
		parent::__construct('input');
		$this->etype=get_class($this);
		$this->attributes['type']='hidden';
		$this->main=$this;
		
	}
	
	function bootstrap()
	{
		editor_generic::bootstrap_part();
	}
	
	function html_head()
	{
		$this->attributes['value']=$this->args[$this->context[$this->long_name]['var']];
		parent::html_head();
	}
	
	function handle_event($ev)
	{
	}
}

class editor_divbutton extends dom_div
{
	function __construct()
	{
		parent::__construct();
		$this->etype=get_class($this);
		#$this->attributes['type']='hidden';
		$this->text=new dom_statictext;
		$this->append_child($this->text);
		$this->main=$this;
	}
	
	function html_head()
	{
		if(isset($this->attributes['value']))
		{
			$this->text->text=$this->attributes['value'];
			unset($this->attributes['value']);
		}
		if($this->usevar)
		{
			$this->text->text=$this->args[$this->context[$this->long_name]['var']];
		}
		parent::html_head();
	}
	
	function bootstrap()
	{
		
		editor_generic::bootstrap_part();
		if(isset($this->value))$value=js_escape($this->value);
		if(isset($this->context[$this->long_name]['var']))$value=$this->args[$this->context[$this->long_name]['var']];
		if(isset($this->val_js))
		{
			$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:".$this->val_js.",c_id:this.id});";
		}else
			$this->attributes['onclick']="chse.send_or_push({static:'".$this->send."',val:'".js_escape(urlencode($value))."',c_id:this.id});";
		
		// focus persistence test
	}
	
	function handle_event($ev)
	{
	}
	
}



















?>