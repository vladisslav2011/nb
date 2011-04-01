<?php


if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

function js_escape($s)
{
 return addcslashes($s,'/\\'."'\n\r");
}


function js_print($js)
{
	print "<script type='text/javascript'>$js</script>\n";
}

function js_create_timer($function,$period)
{
	
	print "<script type='text/javascript'>setInterval(\"$function\",$period);\n</script>";
}

function string_to_color($s,$light=0)
{
	if($light)
	{
		$div=2<<$light;
		$add=256-256/$div;
		$s=substr(md5($s),0,6);
		$re='0x'.substr($s,0,2);
		$gr='0x'.substr($s,2,2);
		$bl='0x'.substr($s,4,2);
		$re=$re/$div+$add;
		$gr=$gr/$div+$add;
		$bl=$bl/$div+$add;
		return sprintf("%s%2x%2x%2x",'#',$re,$gr,$bl);
		
	}
	else
		return '#'.substr(md5($s),0,6);
}

function bgcolor_to_color($ir)
{
	if(strlen($ir)==4)
	{
		$re='0x'.substr($ir,1,1).'0';
		$gr='0x'.substr($ir,2,1).'0';
		$bl='0x'.substr($ir,3,1).'0';
	}else{
		$re='0x'.substr($ir,1,2);
		$gr='0x'.substr($ir,3,2);
		$bl='0x'.substr($ir,5,2);
	}
	if($re*2+$gr*6+$bl*2>128*10)return 'black' ; else return 'white';

}


class csv
{
	public $quotes='"';
	public $delimiter=",";
	function split($str)
	{
	
		$entered_quotes=false;
		$ind=0;
		$res=Array();
		$coll='';
		$len=strlen($str);
	
		for($k=0;$k<$len;$k++)
		{
	
			if(($str[$k]==$this->delimiter)&&($entered_quotes==false))
			{
				$res[$ind]=$coll;
				$ind++;
				$coll='';
				continue;
			};
			if(($str[$k]==$this->quotes)&&($entered_quotes==false))
			{
				$entered_quotes=true;
				continue;
			};
			if(($str[$k]==$this->quotes)&&($entered_quotes==true))
			{
				if($k==($len-1))
				{
					$res[$ind]=$coll;
					$ind++;
					return $res;
				};
				if(($k<($len-1))&($str[$k+1]==$this->quotes))
				{
					$coll.=$str[$k];
					$k++;
					continue;
				};
				
				$entered_quotes=false;
				continue;
			};
			$coll.=$str[$k];
		};
		$res[$ind]=$coll;
	
		return $res;
	}
	
	function join($arr)
	{
		$res="";
		foreach($arr as $e)
		{
			if($res!=="")$res.=$this->delimiter;
			if(strstr($e,$this->delimiter)!==false || strstr($e,$this->quotes)!==false)
				$ne=preg_replace('/'.preg_quote($this->quotes).'/',$this->quotes.$this->quotes,$e);
			if(isset($ne))$res.=$this->delimiter.$ne.$this->delimiter;
			else $res.=$e;
		}
		return $res;
	}
}


?>