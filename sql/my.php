<?php

define('SQL_DIALECT','MY');
class sql
{
	public $conn,$qcnt=0;
	public $logquerys=false,$querytime=0;
	//public $querylog;
	function connect($server='localhost',$username='root',$password='')
	{
		$this->conn=mysql_connect($server,$username,$password) or die(mysql_error());
		return 0;
	}
	function close()
	{
		mysql_close($this->conn);
		$this->conn=NULL;
	}
	
	function dbg_bt()
	{
		$bt=debug_backtrace();
		foreach($bt as $a)print get_class($a['object']).'('.$a['function'].')/';
		print ';<br/>';
	}
	
	function query($q)
	{
		$perf=microtime(true);
		$this->qcnt++;
		if($this->logquerys)$log->q=$q;
		$res=mysql_query($q,$this->conn);
		if($this->logquerys)
		{
			$log->e=mysql_error();
			$this->querylog[]=$log;
		}
		$this->querytime+=microtime(true)-$perf;
		//$this->dbg_bt();
		return $res;
	}
	function free($r)
	{
		if($r && ($r !== TRUE))mysql_free_result($r);
		return 0;
	}
	function fetcha($r)
	{
		if(! $r) return NULL;
		if($r===TRUE) return NULL;
		return mysql_fetch_array($r,MYSQL_ASSOC);
	}
	function fetchn($r)
	{
		if(! $r) return NULL;
		if($r===TRUE) return NULL;
		return mysql_fetch_array($r,MYSQL_NUM);
	}
	function fetch1($r)
	{
		if(isset($r))
		{
			if($r===TRUE) return NULL;
			$a=$this->fetchn($r);
			return $a[0];
		}else{
			return null;
		}
	}
	function fetchm($r)
	{
		if($r===TRUE) return NULL;
		$cnt=0;
		while($a=$this->fetchn($r))
		{
			$res[$cnt]=$a[0];
			$cnt++;
		}
		return $res;
	}
	function fetchma($r)
	{
		if($r===TRUE) return NULL;
		$cnt=0;
		while($a=$this->fetcha($r))
		{
			$res[$cnt]=$a;
			$cnt++;
		}
		return $res;
	}
	function esc($s)
	{
		if(isset($this))if($this->conn)
			return mysql_real_escape_string($s,$this->conn);
		else
			return mysql_escape_string($s);
	}
	function err()
	{
		return mysql_error();
	}
	function ar()
	{
		if(isset($this))if($this->conn)
			return mysql_affected_rows($this->conn);
		else
			return -1;
	}
	//return array of associative arrays of results or null if failed or true if insert/delete like query
	function qa($q)
	{
		$res=$this->query($q);
		if($res===TRUE) return $res;
		if($res===FALSE) return $res;//is it real? Or always undef if failed
		if(!isset($res))return NULL;
		$ret=Array();
		while($row=$this->fetcha($res))
			$ret[]=$row;
		$this->free($res);
		return $ret;
	}
	//return array of normal arrays of results or null if failed or true if insert/delete like query
	function qn($q)
	{
		$res=$this->query($q);
		if($res===TRUE) return $res;
		if($res===FALSE) return $res;//is it real? Or always undef if failed
		if(!isset($res))return NULL;
		$ret=Array();
		while($row=$this->fetchn($res))
			$ret[]=$row;
		$this->free($res);
		return $ret;
	}
	//return associative arrays where key is the first query result and value is the second
	function qkv($q)
	{
		$res=$this->query($q);
		if($res===TRUE) return $res;
		if($res===FALSE) return $res;//is it real? Or always undef if failed
		if(!isset($res))return NULL;
		$ret=Array();
		while($row=$this->fetchn($res))
			$ret[$row[0]]=$row[1];
		$this->free($res);
		return $ret;
	}
	//return array of first query results
	function qv($q)
	{
		$res=$this->query($q);
		if($res===TRUE) return $res;
		if($res===FALSE) return $res;//is it real? Or always undef if failed
		if(!isset($res))return NULL;
		$ret=Array();
		while($row=$this->fetchn($res))
			$ret[]=$row[0];
		$this->free($res);
		return $ret;
	}
	//return single result
	function q1($q)
	{

		$res=$this->query($q);
		if($res===TRUE) return $res;
		if($res===FALSE) return $res;//is it real? Or always undef if failed
		if(!isset($res))return NULL;
		$row=$this->fetch1($res);
		if(isset($row))
		{
			$this->free($res);
			return $row;
		}
		return NULL;
	}
}














?>