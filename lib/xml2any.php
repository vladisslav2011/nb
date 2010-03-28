<?php

class xml_node
{
	function print_html($depth=0)
	{
		$name=isset($this->name)?$this->name:'text';
		print "<div style='border:1px solid black;margin-left:2px;background:#".sprintf('%06X',(0xffffff^(0x1248<<$depth))).";'>";
		print "<div style='background:black;color:white;'>".htmlspecialchars($name).'</div>';
		if(isset($this->name))
		{
			if(is_array($this->attrs) && count($this->attrs)>0)
			{
				print "<div style='border:1px solid red;'>";
				print "<div style='background:red;color:white;'>";
				print 'Attributes:';
				print '</div><div><table>';
				foreach($this->attrs as $i => $a)
				{
					print '<tr>';
					print "<td>".htmlspecialchars($i)."</td>";
					print "<td>=</td>";
					print "<td>".htmlspecialchars($a)."</td>";
					print '</tr>';
				}
				print "</table></div></div>";
			}
		if(is_array($this->nodes))foreach($this->nodes as $n) $n->print_html($depth+1);
		}else print htmlspecialchars($this->text);
		print "</div>";
	}
	
	function dump($cb)
	{
		$name=isset($this->name)?$this->name:'text';
		if($this->is_root)
		{
			$cb('<?xml version="1.0" encoding="UTF-8"?>');
			if(is_array($this->nodes) && count($this->nodes)>0)
				foreach($this->nodes as $n) $n->dump($cb);
		}else{
			if(isset($this->name))
			{
				$cb('<'.$name);
				if(is_array($this->attrs) && count($this->attrs)>0)
					foreach($this->attrs as $i => $a)
						$cb(' '.htmlspecialchars($i).'="'.htmlspecialchars($a,ENT_QUOTES).'"');
				if(is_array($this->nodes) && count($this->nodes)>0)
				{
					$cb(">");
					foreach($this->nodes as $n) $n->dump($cb);
					$cb('</'.$name.'>');
				}else
					$cb(" />");
			}else $cb(htmlspecialchars($this->text));
		}
	}
	function dump_o($obj,$cb)
	{
		$name=isset($this->name)?$this->name:'text';
		if($this->is_root)
		{
			$obj->$cb('<?xml version="1.0" encoding="UTF-8"?>');
			if(is_array($this->nodes) && count($this->nodes)>0)
				foreach($this->nodes as $n) $n->dump_o($obj,$cb);
		}else{
			if(isset($this->name))
			{
				$obj->$cb('<'.$name);
				if(is_array($this->attrs) && count($this->attrs)>0)
					foreach($this->attrs as $i => $a)
						$obj->$cb(' '.htmlspecialchars($i).'="'.htmlspecialchars($a,ENT_QUOTES).'"');
				if(is_array($this->nodes) && count($this->nodes)>0)
				{
					$obj->$cb(">");
					foreach($this->nodes as $n) $n->dump_o($obj,$cb);
					$obj->$cb('</'.$name.'>');
				}else
					$obj->$cb(" />");
			}else $obj->$cb(htmlspecialchars($this->text));
		}
	}
}




class xml2Array {
	//var $result = array();
	var $resParser;
	var $strXmlData;
		//$current,$path
	function __construct()
	{
		$this->resParser = xml_parser_create ();
		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->resParser, "tagData");
		xml_parser_set_option ($this->resParser ,XML_OPTION_CASE_FOLDING , 0 );
		$this->result=new xml_node;
		$this->result->attr=Array();
		$this->result->nodes=Array();
		$this->result->name='root';
		$this->result->is_root=true;
		$this->current=$this->result;
	}
	
	function __destruct()
	{
		xml_parser_free($this->resParser);
	}
	
	
	
	function feed($strInputXML,$final=false)
	{
		$this->strXmlData = xml_parse($this->resParser,$strInputXML ,$final);
		if(!$this->strXmlData)
		{
			die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($this->resParser)),
			xml_get_current_line_number($this->resParser)));
		}
	}
	
	function tagOpen($parser, $name, $attrs)
	{
		//close textnode if any
		if(!isset($this->current->name))
			$this->current=$this->current->parent;
		//open a new node
		$tag=new xml_node;
		$tag->name=$name;
		$tag->attrs=$attrs;
		//link node
		$tag->parent=$this->current;
		//insert it
		$this->current->nodes[]=$tag;
		//set pointer
		$this->current=$tag;
		
	}
	
	function tagData($parser, $tagData)
	{
		//if currently processing textnode append text
		//textnode does not have a name
		if(!isset($this->current->name))
			$this->current->text.=$tagData;
		//create a new textnode
		else
		{
			//if(!trim($tagData)) return;
			if(preg_match('/^\\s+$/',$tagData)) return;
			$tag=new xml_node;
			$tag->parent=$this->current;
			$tag->text=$tagData;
			$this->current->nodes[]=$tag;
			$this->current=$tag;
		}
	}
	
	function tagClosed($parser, $name)
	{
		//close textnode if any
		if(!isset($this->current->name))
			$this->current=$this->current->parent;
		$this->current=$this->current->parent;
	}
}


//--------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------
class xml2sql_1 {
	//var $result = array();
	var $resParser;
	var $strXmlData;
	//$id,$current
	//$sql,$db,$tbl,$atbl
	//$tbl(id,nodeid,parent,nodename,textvalue)
	//$atbl(id,nodeid,attrname,textvalue)
	function __construct()
	{
		global $sql;
		$this->resParser = xml_parser_create ();
		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->resParser, "tagData");
		xml_parser_set_option ($this->resParser ,XML_OPTION_CASE_FOLDING , 0 );
		if(isset($sql))$this->sql=&$sql;
	}
	
	function __destruct()
	{
		xml_parser_free($this->resParser);
	}
	
	
	
	function feed($strInputXML,$final=false)
	{
		$this->strXmlData = xml_parse($this->resParser,$strInputXML ,$final);
		if(!$this->strXmlData)
		{
			die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($this->resParser)),
			xml_get_current_line_number($this->resParser)));
		}
	}
	
	function tagOpen($parser, $name, $attrs)
	{
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		$i_name="'".$this->sql->esc($name)."'";
		
		if(!$this->notrans)$this->sql->query('START TRANSACTION') or die($sql->err());
		if(!isset($this->id))
		{
			$new_id=$this->sql->fetch1($this->sql->query('SELECT max(id)+1 FROM '.$db.$tbl));
			if(!isset($new_id))$new_id=0;
			$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$new_id.",-1,NULL,".$i_name.",NULL)");
			$this->id=$new_id;
			$this->current=0;
			$parent=0;
			$next=0;
		}else{
			if(!isset($this->current))
			{
				$this->current=0;
				$next=0;
				$parent=-1;
			}else{
				
			//close textnode if any
				$ar=$this->sql->fetcha($this->sql->query(
					"SELECT".
					" a.nodename AS `istext`,".
					" a.nodeid AS `nodeid`,".
					" (SELECT max(b.nodeid)+1 FROM ".$db.$tbl." as b WHERE b.id=".$this->id.") AS `next`,".
					" a.parent AS `parent`".
					" FROM ".$db.$tbl." as a WHERE a.id=".$this->id." AND a.nodeid=".$this->current));
				//$this->current=intval($ar['curr']);
				$next=intval($ar['next']);
				$parent=($ar['istext']=='')?intval($ar['parent']):intval($ar['nodeid']);
			}
			//open a new node
			
			$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$next.",".$parent.",".$i_name.",NULL)");
			$this->current=$next;
		}
		foreach($attrs as $a=>$v)
		{
			$this->sql->query('INSERT INTO '.$db.$atbl."(id,nodeid,attrname,textvalue) VALUES(".$this->id.",".$next.",'".$this->sql->esc($a)."','".$this->sql->esc($v)."')");
		}
		if(!$this->notrans)$this->sql->query('COMMIT') or die($sql->err());
		
	}
	
	function tagData($parser, $tagData)
	{
		if(preg_match('/^\\s+$/',$tagData)) return;
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		$this->sql->query('START TRANSACTION');
		if(!isset($this->id))
		{
			$new_id=$this->sql->fetch1($this->sql->query('SELECT max(id)+1 FROM '.$db.$tbl));
			if(!isset($new_id))$new_id=0;
			$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$new_id.",0,NULL,NULL,'".$this->sql->esc($tagData)."')");
			$this->id=$new_id;
			$this->current=0;
			$parent=0;
			$next=0;
		}else{
			//close textnode if any
			$ar=$this->sql->fetcha($this->sql->query(
				"SELECT".
				" a.nodename IS NULL AS `istext`,".
				" (SELECT max(b.nodeid)+1 FROM ".$db.$tbl." as b WHERE b.id=".$this->id.") AS `next`".
				" FROM ".$db.$tbl." as a WHERE a.id=".$this->id." AND a.nodeid=".$this->current));
			$next=intval($ar['next']);
			if($ar['istext'])
			{
				$this->sql->query('UPDATE '.$db.$atbl." SET textvalue=CONCAT(textvalue,'".$this->sql->esc($tagData)."') WHERE id=".$this->id." AND nodeid=".$this->current);
			}else{
				//open a new node
				$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$next.",".$this->current.",NULL,'".$this->sql->esc($tagData)."')");
				$this->current=$next;
			}
		}
		$this->sql->query('COMMIT');
	}
	
	function tagClosed($parser, $name)
	{
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		$this->current=$this->sql->fetch1($this->sql->query(
			"SELECT".
			" if( (a.nodename IS NULL) ,  (SELECT b.parent FROM ".$db.$tbl." AS b WHERE b.id=a.id AND b.nodeid=a.parent), a.parent)".
			" FROM ".$db.$tbl." AS a WHERE a.id=".$this->id." AND a.nodeid=".$this->current));
	}
	
	function wo($nid,$wof,$woo=NULL)
	{
		
	}
}







class xml2sql_old
{
	//var $result = array();
	var $resParser;
	var $strXmlData;
	//$id,$current
	//$sql,$db,$tbl,$atbl
	//$tbl(id,nodeid,parent,nodename,textvalue)
	//$atbl(id,nodeid,attrname,textvalue)
	var $idstack=Array();
	function __construct()
	{
		global $sql;
		$this->resParser = xml_parser_create ();
		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->resParser, "tagData");
		xml_parser_set_option ($this->resParser ,XML_OPTION_CASE_FOLDING , 0 );
		if(isset($sql))$this->sql=&$sql;
	}
	
	function __destruct()
	{
		xml_parser_free($this->resParser);
	}
	
	
	
	function feed($strInputXML,$final=false)
	{
		$this->strXmlData = xml_parse($this->resParser,$strInputXML ,$final);
		if(!$this->strXmlData)
		{
			die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($this->resParser)),
			xml_get_current_line_number($this->resParser)));
		}
	}
	
	function tagOpen($parser, $name, $attrs)
	{
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		$i_name="'".$this->sql->esc($name)."'";
		
		if(!$this->notrans)$this->sql->query('START TRANSACTION') or die($sql->err());
		if(!isset($this->id))
		{
			$new_id=$this->sql->fetch1($this->sql->query('SELECT max(id)+1 FROM '.$db.$tbl));
			if(!isset($new_id))$new_id=0;
			$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$new_id.",-1,NULL,".$i_name.",NULL)");
			$this->id=$new_id;
			$this->current=0;
			$parent=0;
			$next=0;
			$this->idstack[]=-1;
			$this->istext=false;
		}else{
			if(!isset($this->current))
			{
				$this->current=0;
				$this->idstack[]=-1;
				$next=0;
				$parent=-1;
			}else{
				$next=$this->current+1;
				//close textnode if any
				if($this->istext)
				{
					array_pop($this->idstack);
					$this->istext=false;
				}
				$parent=$this->idstack[count($this->idstack)-1];
			}
			//open a new node
			
			$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$next.",".$parent.",".$i_name.",NULL)");
			$this->current=$next;
			$this->idstack[]=$this->current;
			
		}
		foreach($attrs as $a=>$v)
		{
			$this->sql->query('INSERT INTO '.$db.$atbl."(id,nodeid,attrname,textvalue) VALUES(".$this->id.",".$next.",'".$this->sql->esc($a)."','".$this->sql->esc($v)."')");
		}
		if(!$this->notrans)$this->sql->query('COMMIT') or die($sql->err());
		
	}
	
	function tagData($parser, $tagData)
	{
		if(preg_match('/^\\s+$/',$tagData)) return;
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		if(!$this->notrans)$this->sql->query('START TRANSACTION');
		if(!isset($this->id))
		{
			$new_id=$this->sql->fetch1($this->sql->query('SELECT max(id)+1 FROM '.$db.$tbl));
			if(!isset($new_id))$new_id=0;
			$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$new_id.",0,NULL,NULL,'".$this->sql->esc($tagData)."')");
			$this->id=$new_id;
			$this->current=0;
			$this->idstack[]=-1;
			$this->istext=true;
			$parent=0;
			$next=0;
		}else{
			if(!isset($this->current))
			{
				$this->current=0;
				$this->idstack[]=-1;
				$next=0;
				$parent=-1;
			}else{
				$next=$this->current+1;
				$parent=$this->idstack[count($this->idstack)-1];
			}
			if($this->istext)
			{
				$this->sql->query('UPDATE '.$db.$atbl." SET textvalue=CONCAT(textvalue,'".$this->sql->esc($tagData)."') WHERE id=".$this->id." AND nodeid=".$this->current);
			}else{
				//open a new node
				$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$next.",".$this->current.",NULL,'".$this->sql->esc($tagData)."')");
				$this->idstack[]=$this->current;
				$this->current=$next;
				$this->istext=true;
			}
		}
		if(!$this->notrans)$this->sql->query('COMMIT');
	}
	
	function tagClosed($parser, $name)
	{
		if($this->istext)
		{
			array_pop($this->idstack);
			$this->istext=false;
		}
		array_pop($this->idstack);
	}
	
	
	function wo($nid,$parent,$wof,$woo=NULL)
	{
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		if($parent==-1)
		{
			if(is_object($woo))$woo->$wof('<?xml version="1.0" encoding="UTF-8"?>'."\n");
			else $wof('<?xml version="1.0" encoding="UTF-8"?>'."\n");
		}
		$nodes_res=$this->sql->query("SELECT nodeid,parent,nodename,textvalue FROM ".$db.$tbl.
//		" WHERE id=".$nid." AND parent=".$parent." ORDER BY nodeid");
		" WHERE id=".$nid." AND parent=".$parent);
		while($nodes_row=$this->sql->fetcha($nodes_res))
		{
			//if text node, output it
			if($nodes_row['nodename']=='')
			{
				if(is_object($woo))$woo->$wof(htmlspecialchars($nodes_row['textvalue']));
				else $wof(htmlspecialchars($nodes_row['textvalue']));
			}else{
				//output open tag
				if(is_object($woo))$woo->$wof('<'.htmlspecialchars($nodes_row['nodename']));
				else $wof('<'.htmlspecialchars($nodes_row['nodename']));
				//output attributes
				$attr_res=$this->sql->query("SELECT attrname,textvalue FROM ".$db.$atbl.
				" WHERE id=".$nid." AND nodeid=".$nodes_row['nodeid']." ORDER BY attrname");
				while($attr_row=$this->sql->fetcha($attr_res))
					
					if(is_object($woo))$woo->$wof(' '.htmlspecialchars($attr_row['attrname'],ENT_QUOTES).'="'.
						htmlspecialchars($attr_row['textvalue'],ENT_QUOTES).'"');
					else $wof(' '.htmlspecialchars($attr_row['attrname'],ENT_QUOTES).'="'.
						htmlspecialchars($attr_row['textvalue'],ENT_QUOTES).'"');
				
				$this->sql->free($attr_res);
				
				if(is_object($woo))$woo->$wof('>');
				else $wof('>');
				
				//output childnodes
				$this->wo($nid,$nodes_row['nodeid'],$wof,$woo);
				//close node
				if(is_object($woo))$woo->$wof('</'.htmlspecialchars($nodes_row['nodename']).'>');
				else $wof('</'.htmlspecialchars($nodes_row['nodename']).'>');
			}
		}
		$this->sql->free($nodes_res);
	}
}




class xml2sql
{
	//var $result = array();
	var $resParser;
	var $strXmlData;
	//$id,$current
	//$sql,$db,$tbl,$atbl
	//$tbl(id,nodeid,parent,nodename,textvalue)
	//$atbl(id,nodeid,attrname,textvalue)
	//$buffer(id,nodeid,nodename,textvalue)
	var $idstack=Array();
	function __construct()
	{
		global $sql;
		$this->resParser = xml_parser_create ();
		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->resParser, "tagData");
		xml_parser_set_option ($this->resParser ,XML_OPTION_CASE_FOLDING , 0 );
		if(isset($sql))$this->sql=&$sql;
	}
	
	function __destruct()
	{
		xml_parser_free($this->resParser);
	}
	
	
	
	function feed($strInputXML,$final=false)
	{
		$this->strXmlData = xml_parse($this->resParser,$strInputXML ,$final);
		if(!$this->strXmlData)
		{
			die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($this->resParser)),
			xml_get_current_line_number($this->resParser)));
		}
	}
	
	function tagOpen($parser, $name, $attrs)
	{
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		$i_name="'".$this->sql->esc($name)."'";
		
		if(!$this->notrans)$this->sql->query('START TRANSACTION') or die($sql->err());
		if(!isset($this->id))
		{
			$new_id=$this->sql->fetch1($this->sql->query('SELECT max(id)+1 FROM '.$db.$tbl));
			if(!isset($new_id))$new_id=0;
			//$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$new_id.",0,-1,".$i_name.",NULL)");
			
			$this->buffer->nodeid=0;
			$this->buffer->parent=-1;
			$this->buffer->nodename=$i_name;
			unset($this->buffer->textvalue);
			
			$this->id=$new_id;
			$this->current=0;
			$parent=-1;
			$next=0;
			$this->idstack[]=-1;
			$this->istext=false;
		}else{
			if(!isset($this->current))
			{
				$this->current=0;
				$this->idstack[]=-1;
				$next=0;
				$parent=-1;
			}else{
				$next=$this->current+1;
				//close textnode if any
				if($this->istext)
				{
					$textid=array_pop($this->idstack);
					$this->istext=false;
				}
				$parent=$this->idstack[count($this->idstack)-1];
			}
			if(isset($this->buffer))
			{
				$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$this->buffer->nodeid.",".$this->buffer->parent.",".$this->buffer->nodename.",NULL)");
				if(isset($textid))
					$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$textid.",".$this->buffer->nodeid.",NULL,'".$this->sql->esc($this->buffer->textvalue)."')");
				unset($this->buffer);
			}
			//open a new node
			
			//$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$next.",".$parent.",".$i_name.",NULL)");
			$this->buffer->nodeid=$next;
			$this->buffer->parent=$parent;
			$this->buffer->nodename=$i_name;
			
			$this->current=$next;
			$this->idstack[]=$this->current;
			
		}
		foreach($attrs as $a=>$v)
		{
			$this->sql->query('INSERT INTO '.$db.$atbl."(id,nodeid,attrname,textvalue) VALUES(".$this->id.",".$next.",'".$this->sql->esc($a)."','".$this->sql->esc($v)."')");
		}
		if(!$this->notrans)$this->sql->query('COMMIT') or die($sql->err());
		
	}
	
	function tagData($parser, $tagData)
	{
		if(preg_match('/^\\s+$/',$tagData)) return;
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		if(!isset($this->id))
		{
			$new_id=$this->sql->fetch1($this->sql->query('SELECT max(id)+1 FROM '.$db.$tbl));
			if(!isset($new_id))$new_id=0;
			$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$new_id.",0,NULL,NULL,'".$this->sql->esc($tagData)."')");
			$this->id=$new_id;
			$this->current=0;
			$this->idstack[]=-1;
			$this->istext=true;
			$parent=0;
			$next=0;
		}else{
			if(!isset($this->current))
			{
				$this->current=0;
				$this->idstack[]=-1;
				$next=0;
				$parent=-1;
			}else{
				$next=$this->current+1;
				$parent=$this->idstack[count($this->idstack)-1];
			}
			if($this->istext)
			{
				//$this->sql->query('UPDATE '.$db.$atbl." SET textvalue=CONCAT(textvalue,'".$this->sql->esc($tagData)."') WHERE id=".$this->id." AND nodeid=".$this->current);
				$this->buffer->textvalue.=$tagData;
			}else{
				//open a new node
				if(!isset($this->buffer))
					$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$next.",".$this->current.",NULL,'".$this->sql->esc($tagData)."')");
				else
					$this->buffer->textvalue=$tagData;
				$this->idstack[]=$this->current;
				$this->current=$next;
				$this->istext=true;
			}
		}
	}
	
	function tagClosed($parser, $name)
	{
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		
		if($this->istext)
		{
			if(isset($this->buffer))
			{
				$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$this->buffer->nodeid.",".$this->buffer->parent.",".$this->buffer->nodename.",'".$this->sql->esc($this->buffer->textvalue)."')");
				//$this->current=$this->buffer->nodeid;
				unset($this->buffer);
			}
			array_pop($this->idstack);
			$this->istext=false;
		}else{
			if(isset($this->buffer))
			{
				$this->sql->query('INSERT INTO '.$db.$tbl."(id,nodeid,parent,nodename,textvalue) VALUES(".$this->id.",".$this->buffer->nodeid.",".$this->buffer->parent.",".$this->buffer->nodename.",NULL)");
				unset($this->buffer);
			}
		}
		array_pop($this->idstack);
	}
	
	
	function wo($nid,$parent,$wof,$woo=NULL)
	{
		if(!is_object($this->sql))die('xml2sql:tagOpen:sql not set');
		if($this->tbl=='')die('xml2sql:tagOpen:tbl not set');
		if($this->atbl=='')die('xml2sql:tagOpen:atbl not set');
		if($this->db!='')$db='`'.$this->sql->esc($this->db).'`.';
		$tbl='`'.$this->sql->esc($this->tbl).'`';
		$atbl='`'.$this->sql->esc($this->atbl).'`';
		if($parent==-1)
		{
			if(is_object($woo))$woo->$wof('<?xml version="1.0" encoding="UTF-8"?>'."\n");
			else $wof('<?xml version="1.0" encoding="UTF-8"?>'."\n");
		}
		$nodes_res=$this->sql->query("SELECT nodeid,parent,nodename,textvalue FROM ".$db.$tbl.
//		" WHERE id=".$nid." AND parent=".$parent." ORDER BY nodeid");
		" WHERE id=".$nid." AND parent=".$parent);
		while($nodes_row=$this->sql->fetcha($nodes_res))
		{
			//if text node, output it
			if($nodes_row['nodename']=='')
			{
				if(is_object($woo))$woo->$wof(htmlspecialchars($nodes_row['textvalue']));
				else $wof(htmlspecialchars($nodes_row['textvalue']));
			}else{
				//output open tag
				if(is_object($woo))$woo->$wof('<'.htmlspecialchars($nodes_row['nodename']));
				else $wof('<'.htmlspecialchars($nodes_row['nodename']));
				//output attributes
				$attr_res=$this->sql->query("SELECT attrname,textvalue FROM ".$db.$atbl.
				" WHERE id=".$nid." AND nodeid=".$nodes_row['nodeid']." ORDER BY attrname");
				while($attr_row=$this->sql->fetcha($attr_res))
					
					if(is_object($woo))$woo->$wof(' '.htmlspecialchars($attr_row['attrname'],ENT_QUOTES).'="'.
						htmlspecialchars($attr_row['textvalue'],ENT_QUOTES).'"');
					else $wof(' '.htmlspecialchars($attr_row['attrname'],ENT_QUOTES).'="'.
						htmlspecialchars($attr_row['textvalue'],ENT_QUOTES).'"');
				
				$this->sql->free($attr_res);
				
				if(is_object($woo))$woo->$wof('>');
				else $wof('>');
				
				//output childnodes
				if($nodes_row['textvalue']!='')
				{
					if(is_object($woo))$woo->$wof(htmlspecialchars($nodes_row['textvalue']));
					else $wof(htmlspecialchars($nodes_row['textvalue']));
				}
				else
					$this->wo($nid,$nodes_row['nodeid'],$wof,$woo);
				//close node
				if(is_object($woo))$woo->$wof('</'.htmlspecialchars($nodes_row['nodename']).'>');
				else $wof('</'.htmlspecialchars($nodes_row['nodename']).'>');
			}
		}
		$this->sql->free($nodes_res);
	}
}








?>