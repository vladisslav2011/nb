<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once('lib/ddc_meta.php');
require_once('lib/settings.php');
require_once('lib/commctrls.php');
$tests_m_array[]='test_controls_hover_buttons';
class test_controls_hover_buttons extends dom_div
{
	function __construct()
	{
		dom_div::__construct();
		$this->etype='test_controls_hover_buttons';
		$this->indd=new dom_div;
		$this->append_child($this->indd);
		$l=new dom_statictext;
		$this->indd->append_child($l);
		$l->text='<.>';
		
	}
	
	function bootstrap()
	{
	}
	
	function handle_event($ev)
	{
		switch($ev->rem_name)
		{
		case 'test':
			if($_POST['val']=='error')
			{
				$ev->failure='!';
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


class hiera_test extends dom_div
{
	function __construct()
	{
		
	}
	
	function bootstrap()
	{
	}
	
	function handle_event($ev)
	{
		editor_generic::handle_event($ev);
	}


}



$tests_m_array[]='test_controls_hover_buttons';




?>