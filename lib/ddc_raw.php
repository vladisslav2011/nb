<?php

class ddc_raw
{
	public $ctbl='';
	public $cols;
	public $keys;
	public $querys;
	public $actionlist;
	function load_table($tbl,$sql)
	{
		$this->ctbl=$tbl;
		$this->sql=$sql;
		$res1=$sql->query("SHOW FULL COLUMNS FROM `$tbl`");
		$tmpcols=$sql->fetchma($res1);
		$sql->free($res1);
		unset($this->cols);
		$this->prev_col='';
		if(is_array($tmpcols))
			foreach($tmpcols as $i => $v)
			{
				$rr->name=$v['Field'];
				$rr->type->raw=$v['Type'];
				$rr->type->name=preg_replace('/^([a-z]*).*/','$1',$v['Type']);
				$wp=explode(',',preg_replace('/.*\(([^)]*)\).*/','$1',$v['Type']));
				$rr->type->width=$wp[0];
				$rr->type->prec=$wp[1];
				$rr->comment=$v['Comment'];
				$rr->null=($v['Null']=='YES')?1:0;
				$rr->default=(($v['Default']=='') && (! $this->is_text_type($v['Type'])))?NULL:$v['Default'];
				$rr->sequence=($v['Extra']=='auto_increment')?1:0;
				$rr->prev_col=$this->prev_col;
				$this->prev_col=$rr->name;
				$this->cols[$i]=$rr;
				unset($rr);
			}
		$res1=$sql->query("SHOW KEYS FROM `$tbl`");
		$tmpkeys=$sql->fetchma($res1);
		$sql->free($res1);
		unset($this->keys);
		if(is_array($tmpkeys))foreach($tmpkeys as $i => $v)
		{
			$this->keys[$v['Key_name']][$v['Seq_in_index']]->col=$v['Column_name'];
			$this->keys[$v['Key_name']][$v['Seq_in_index']]->sub=$v['Sub_part'];
		}
		
	}
	
	function is_text_type($t)
	{
		return preg_match('/.*(CHAR|VARCHAR|BINARY|VARBINARY|BLOB|TEXT|ENUM|SET).*/',$t);
	}
	
	function fetch_cols()
	{
		if(!is_array($this->cols)) return null;
		//$res;
		reset($this->cols);
		foreach($this->cols as $i => $r)
		{
			$item[0]=$r->name;
			$item[1]=$r->type->raw;
			$item[2]=$r->null;
			$item[3]=$r->default;
			$item[4]=$r->sequence;
			$item[5]=$r->comment;
			$res[]=$item;
		}
		return $res;
	}
	function print_cols()
	{
		reset($this->cols);
		$tblstyle='style="border-collapse:collapse;"';
		$tdstyle='style="border:1px solid black;"';
		print "<table $tblstyle>";
		print "<tr>";
		print	"<td $tdstyle>*</td>".
			"<td $tdstyle>name</td>".
			"<td $tdstyle>type.raw</td>".
			"<td $tdstyle>type.name</td>".
			"<td $tdstyle>type.width</td>".
			"<td $tdstyle>type.prec</td>".
			"<td $tdstyle>default</td>".
			"<td $tdstyle>null</td>".
			"<td $tdstyle>sequence</td>".
			"<td $tdstyle>comment</td>".
			"";
		print "</tr>";
		foreach($this->cols as $i => $r)
		{
		print "<tr>";
		print	"<td $tdstyle>${i}</td>".
			"<td $tdstyle>".$r->name."</td>".
			"<td $tdstyle>".$r->type->raw."</td>".
			"<td $tdstyle>".$r->type->name."</td>".
			"<td $tdstyle>".$r->type->width."</td>".
			"<td $tdstyle>".$r->type->prec."</td>".
			"<td $tdstyle>".$r->default."</td>".
			"<td $tdstyle>".$r->null."</td>".
			"<td $tdstyle>".$r->sequence."</td>".
			"<td $tdstyle>".$r->comment."</td>".
			"";
		print "</tr>";
		}
		print "</table>";
	}
	function fetch_keys()
	{
		if(!is_array($this->keys)) return;
		reset($this->keys);
		
	}
	function print_keys()
	{
		if(!is_array($this->keys)) return null;
		reset($this->keys);
		foreach($this->keys as $i => $r)
			foreach($r as $s => $row)
		{
			
			$item[0]=$s;
			$item[1]=$row->col;
			$item[2]=$row->sub;
			$res[]=$item;
			}
		return $res;
	}
	function print_querys()
	{
		if(!is_array($this->querys)) return;
		reset($this->querys);
		$tblstyle='style="border-collapse:collapse;"';
		$tdstyle='style="border:1px solid black;"';
		print "<table $tblstyle>";
		print "<tr>";
		print	"<td $tdstyle>n</td>".
			"<td $tdstyle>q</td>".
			"";
		print "</tr>";
		foreach($this->querys as $i => $r)
		{
			
			print "<tr>";
			print	"<td $tdstyle>${i}</td>".
				"<td $tdstyle>${r}</td>".
			"";
			print "</tr>";
			}
		print "</table>";
	}
	function create_query($name)
	{
		$q='';
		foreach($this->cols as $i => $r)
		{
			$def='`'.sql::esc($r->name).'` '.$r->type->raw;
			if($r->null==0) $def.=" NOT NULL";
			if($r->sequence==1) $def.=" auto_increment";
			if(isset($r->default)) $def.=" DEFAULT '".sql::esc($r->default)."'"; else if($r->null && $r->type->raw != 'timestamp') $def.=" DEFAULT NULL";
			if(isset($r->comment)) $def.=" COMMENT '".sql::esc($r->comment)."'";
			if($q != '')$q.=', ';
			$q.=$def;
		}
		$k='';
		if(is_array($this->keys))
		foreach($this->keys as $key => $rw)
		{
			if($key=='PRIMARY')
				$tk="PRIMARY KEY (";
			else
				$tk="KEY (";
			$cols='';
			foreach($rw as $seq => $row)
			{
				if($cols != '')$cols.=',';
				$cols.="`".sql::esc($row->col)."`";
				if($row->sub)$cols.="(".$row->sub.")";
			}
			if($k != '')$k.=',';
			$k.=$tk.$cols.')';
		}
		if($k != '') $q .= ", $k";
		return "CREATE TABLE `".sql::esc($name)."` ($q)";
		
	}
	function addcol($name,$type,$null,$default,$sequence,$comment)
	{
		$t->name=$name;
		$t->type->raw=$type;
		$t->type->name=preg_replace('/^([a-z]*).*/','$1',$type);
		$wp=explode(',',preg_replace('/.*\(([^)]*)\).*/','$1',$type));
		$t->type->width=$wp[0];
		$t->type->prec=$wp[1];
		$t->null=$null;
		$t->default=$default;
		$t->sequence=$sequence;
		$t->comment=$comment;
		if(count($this->cols)==0)$this->prev_col='';
		$t->prev_col=$this->prev_col;
		$this->prev_col=$name;
		$this->cols[sizeof($this->cols)]=$t;
	}
	function changecol($name,$type,$null,$default,$sequence,$comment)
	{
		$t->name=$name;
		$t->type->raw=$type;
		$t->type->name=preg_replace('/^([a-z]*).*/','$1',$type);
		$wp=explode(',',preg_replace('/.*\(([^)]*)\).*/','$1',$type));
		$t->type->width=$wp[0];
		$t->type->prec=$wp[1];
		$t->null=$null;
		$t->default=$default;
		$t->sequence=$sequence;
		$t->comment=$comment;
		$t->prev_col='';
		foreach($this->cols as $i => $c)
			if($c->name==$name)
			{
				$this->cols[$i]=$t;
				if(isset($this->cols[$i-1]))$this->cols[$i]->prev_col=$this->cols[$i-1]->name;
				return true;
			};
		return false;
	}
	function delcol($name)
	{
		$c=0;
		$prev='';
		foreach($this->cols as $i => $v)
			if($v->name != $name)
			{
				$tmp[$c]=$v;
				$tmp[$c]->prev_col=$prev;
				$prev=$v->name;
				$c++;
			}
		$this->cols=$tmp;
	}
	function delcol_from_key($name)
	{
	foreach($this->keys as $key => $rw)
	{
		$found=false;
		foreach($rw as $col) if($col->col == $name) $found=true;
		if($found)
		{
			$c=1;
			unset($new);
			foreach($rw as $i => $col) if($col->col != $name) $new[$c++]=$col;
			$this->keys[$key]=$new;
		}
		
	}
	unset($new);
	foreach($this->keys as $key => $rw)if(sizeof($rw)!=0)$new[$key]=$rw;
	$this->keys=$rw;
	}
	function addcol_to_key($key,$name,$sub)
	{
		if(! $this->has_col($name))return false;
		if($key=='')
		{
			$key=$name;
			while($this->keys[$key])$key.='0';
		}
		$new->col=$name;
		$new->sub=$sub;
		$this->keys[$key][sizeof($this->keys[$key])+1]=$new;
		return true;
	}
	
	function has_col($str)
	{
		foreach($this->cols as $r)
			if($r->name==$str) return true;
		return false;
	}
	function caddcol($name,$type,$null,$default,$sequence,$comment,$prev_col)
	{
		if(preg_match('/.*auto_increment.*/i',$type))
		{
			$this->auto_inc=(object)array('name'=>$name,'type'=>$type,'null'=>$null,'default'=>$default,'sequence'=>1,'comment'=>$comment,'prev_col'=>$prev_col);
		};
		$type=preg_replace('/auto_increment/i','',$type);
		if($sequence==1)
		{
			$this->auto_inc=(object)array('name'=>$name,'type'=>$type,'null'=>$null,'default'=>$default,'sequence'=>1,'comment'=>$comment,'prev_col'=>$prev_col);
		};
		$sequence=0;
		$q="ALTER TABLE `".sql::esc($this->ctbl)."` ADD COLUMN `".sql::esc($name)."` $type";
		if($null==0) $q.=' NOT NULL';
		if($sequence==1) $q.=" auto_increment";
		if(isset($default)) $q.=" DEFAULT '".sql::esc($default)."'"; else if($null && $type != 'timestamp') $q.=" DEFAULT NULL";
		if(isset($comment)) $q.=" COMMENT '".sql::esc($comment)."'";
		if($prev_col=='')$q.=" FIRST" ; else $q.=" AFTER `".sql::esc($prev_col)."`";
		$sz=is_array($this->querys)?sizeof($this->querys):0;
		$this->querys[$sz]=$q;
	}
	function cchangecol($name,$newname,$type,$null,$default,$sequence,$comment,$prev_col,$force=false)
	{
		if(! $force)
		{
			if(preg_match('/.*auto_increment.*/i',$type))
			{
				$this->auto_inc=(object)array('name'=>$name,'type'=>$type,'null'=>$null,'default'=>$default,'sequence'=>1,'comment'=>$comment,'prev_col'=>$prev_col);
			};
			$type=preg_replace('/auto_increment/i','',$type);
			if($sequence==1)
			{
				$this->auto_inc=(object)array('name'=>$name,'type'=>$type,'null'=>$null,'default'=>$default,'sequence'=>1,'comment'=>$comment,'prev_col'=>$prev_col);
			};
			$sequence=0;
		}
		$q="ALTER TABLE `".sql::esc($this->ctbl)."` CHANGE COLUMN `".sql::esc($name)."` `".sql::esc($newname)."` $type";
		if($null==0) $q.=' NOT NULL';
		if($sequence==1) $q.=" auto_increment";
		if(isset($default)) $q.=" DEFAULT '".sql::esc($default)."'"; else if($null && $type != 'timestamp') $q.=" DEFAULT NULL";
		if(isset($comment)) $q.=" COMMENT '".sql::esc($comment)."'";
		if($prev_col=='')$q.=" FIRST" ; else $q.=" AFTER `".sql::esc($prev_col)."`";
		$sz=is_array($this->querys)?sizeof($this->querys):0;
		$this->querys[$sz]=$q;
	}
	function cdelcol($name)
	{
		$q="ALTER TABLE `".sql::esc($this->ctbl)."` DROP COLUMN `".sql::esc($name)."`";
		$sz=is_array($this->querys)?sizeof($this->querys):0;
		$this->querys[$sz]=$q;
	}
	function has_key($akey)
	{
		if(! is_array($this->keys)) return false;
		foreach($this->keys as $r)
		{
			$found=true;
			foreach($r as $i => $k)
				if($k->col != $akey[$i]->col || $k->sub != $akey[$i]->sub ) $found=false;
			foreach($akey as $i => $k)
				if($k->col != $r[$i]->col || $k->sub != $r[$i]->sub ) $found=false;
			if( $found) return true;
		}
		return false;
	}
	function cdelkey($name)
	{
		if(! is_array($this->keys))return;
		$q="ALTER TABLE `".sql::esc($this->ctbl)."` DROP ";
		if($name=='PRIMARY') $q.='PRIMARY KEY';
		else $q.=" KEY `".sql::esc($name)."`";
		if($name=='PRIMARY' && is_array($this->keys[$name]))
		{
			foreach($this->keys[$name] as $k)
				foreach($this->cols as $co)
					if($co->name==$k->col && $co->sequence==1)
						$this->cchangecol($co->name,$co->name,$co->type->raw,$co->null,$co->default,0,$co->comment,$co->prev_col);
		}
		$sz=is_array($this->querys)?sizeof($this->querys):0;
		$this->querys[$sz]=$q;
	}
	function caddkey($n,$key)
	{
		if(is_array($this->keys) && is_array($this->keys[$n]) && is_array($this->cols))
		foreach($this->keys[$n] as $k)
			foreach($this->cols as $co)
				if($co->name==$k->col && $co->sequence==1)
			$this->auto_inc=(object)array('name'=>$co->name,'type'=>$co->type->raw,'null'=>$co->null,'default'=>$co->default,'sequence'=>$co->sequence,'comment'=>$co->comment,'prev_col'=>$co->prev_col);
		$q="ALTER TABLE `".sql::esc($this->ctbl)."` ADD ";
		if($n=='PRIMARY') $q.='PRIMARY KEY';
		else $q.=" KEY ";
		$cols='';
		foreach($key as $col)
		{
			if($cols!='') $cols .= ', ';
			$cols .= "`".sql::esc($col->col)."`";
			if($col->sub) $cols .= "(".$col->sub.")";
		}
		$q.=" ($cols)";
		$sz=is_array($this->querys)?sizeof($this->querys):0;
		$this->querys[$sz]=$q;
	}
	
	
	
	
	function gen_changes($new)
	{
		unset($this->querys);
		unset($this->auto_inc);
		if(is_array($this->keys))
		{
			reset($this->keys);
			foreach($this->keys as $i => $r)
				if(! $new->has_key($r))
					$this->cdelkey($i);
		}
		reset($new->cols);
		foreach($new->cols as $r)
			if(! $this->has_col($r->name)) $this->caddcol($r->name,$r->type->raw,$r->null,$r->default,$r->sequence,$r->comment,$r->prev_col);
		reset($this->cols);
		foreach($this->cols as $r)
			if(! $new->has_col($r->name))$this->cdelcol($r->name);
			else
			{
				foreach($new->cols as $i => $n)
				 if($n->name==$r->name &&
				 ($n->type->raw!=$r->type->raw ||
				 $n->null!=$r->null ||
				 ($n->default!=$r->default && $r->type->raw != 'timestamp') ||
				 $n->sequence!=$r->sequence ||
				 $n->comment!=$r->comment ||
				 $n->prev_col!=$r->prev_col))
				 	$this->cchangecol($n->name,$n->name,$n->type->raw,$n->null,$n->default,$n->sequence,$n->comment,$n->prev_col);
			}
		if(is_array($new->keys))
		{
			reset($new->keys);
			foreach($new->keys as $i => $r)
				if(! $this->has_key($r))
					$this->caddkey($i,$r);
		}
		if($this->auto_inc)$this->cchangecol($this->auto_inc->name,$this->auto_inc->name,$this->auto_inc->type,$this->auto_inc->null,$this->auto_inc->default,$this->auto_inc->sequence,$this->auto_inc->comment,$this->auto_inc->prev_col,true);
	}
	function commit_changes($sql)
	{
		if(! is_array($this->querys))return false;
		foreach($this->querys as $q)
		{
			print htmlspecialchars($q)."<br>";
			if(! $sql->query($q)) return $q.":".$sql->err();
		}
		unset($this->querys);
		return false;
	}
	function sync($tabl,$sql)
	{
		if(! is_array($this->cols))return false;
		$v1=new ddc_raw;
		$v1->load_table($tabl,$sql);
		$this->gen_changes($v1);
		return $this->commit_changes($sql);
	}

}


function ddc_gentable_n($tbl,$cols,$keys,$sql)
{
	$sa=new ddc_raw;
	foreach ($cols as $e)
		$sa->addcol($e[0],$e[1],$e[2],$e[3],$e[4],$e[5]);
	foreach ($keys as $e)
		$sa->addcol_to_key($e[0],$e[1],$e[2]);
	$sa->ctbl=$tbl;
	if($sql->query("SHOW COLUMNS FROM `$tbl`"))
	{
		$sb=new ddc_raw;
		$sb->load_table($tbl,$sql);
		$sb->gen_changes($sa);
		//$sb->print_querys();//dbg
		$v=$sb->commit_changes($sql);
		if($v)print $v;
	}else{
	$res=$sql->query($sa->create_query($tbl));
	if(!$res)die($sql->err());
	}
}

function ddc_gentable_a($tbl,$cols,$keys,$sql)
{
	$sa=new ddc_raw;
	foreach ($cols as $e)
//	function addcol($name,$type,$null,$default,$sequence,$comment)
		$sa->addcol($e['name'],$e['type'],$e['null'],$e['default'],$e['sequence'],$e['comment']);
	foreach ($keys as $e)
//	function addcol_to_key($key,$name,$sub)
		$sa->addcol_to_key($e['key'],$e['name'],$e['sub']);
	$sa->ctbl=$tbl;
	if($sql->query("SHOW COLUMNS FROM `$tbl`"))
	{
		$sb=new ddc_raw;
		$sb->load_table($tbl,$sql);
		$sb->gen_changes($sa);
		$v=$sb->commit_changes($sql);
		if($v)print $v;
	}else{
	$res=$sql->query($sa->create_query($tbl));
	if(!$res)die($sql->err());
	}
}

/*ddc_object
$o->name='table1';
$o->cols=Array(Array('name' => 'id',..),Array()...);
$o->keys=Array(Array('key' => 'PRIMARY','name' => 'id', 'sub' => NULL)...);

*/

$ddc_tables=Array();
function ddc_gentable_o($obj,$sql)
{
	$sa=new ddc_raw;
	foreach ($obj->cols as $e)
//		$sa->addcol($e['name'],$e['type'],$e['null'],$e['default'],$e['sequence'],$e['comment']);
		$sa->addcol($e['name'],$e['sql_type'],$e['sql_null'],$e['sql_default'],$e['sql_sequence'],$e['sql_comment']);
	foreach ($obj->keys as $e)
//	function addcol_to_key($key,$name,$sub)
		$sa->addcol_to_key($e['key'],$e['name'],$e['sub']);
	$sa->ctbl=$obj->name;
	if($sql->query("SHOW COLUMNS FROM `".$sql->esc($obj->name)."`"))
	{
		$sb=new ddc_raw;
		$sb->load_table($obj->name,$sql);
		$sb->gen_changes($sa);
		$v=$sb->commit_changes($sql);
		if($v)print $v;
	}else{
	$res=$sql->query($sa->create_query($obj->name));
	if(!$res)die($sql->err());
	}
}



?>