<?php
#simple file upload

$doc_root=$_SERVER['DOCUMENT_ROOT'];
if(preg_match('#.*[^/]$#',$doc_root))$doc_root.='/';
$uploaddir = $doc_root.'uploads/';
if(!is_dir($uploaddir))
{
	if(!mkdir($uploaddir))
	{
		if($_POST['rtype']=='ext')print "403\n";
		if($_POST['rtype']=='test')print "Failed to create upload directory.";
		exit;
	};
};
$name =preg_replace('/^.*\//','',$_FILES['file1']['name']);
$uploadfile = $uploaddir . $name;
if (move_uploaded_file($_FILES['file1']['tmp_name'], $uploadfile))
{
	chmod($uploadfile,0666);
	if($_POST['rtype']=='test')
	{
	print $_FILES['file1']['name'].' uploaded as <a href=\'http://'.$_SERVER['HTTP_HOST'].'/uploads/'.urlencode($name).'\'>'.
		htmlspecialchars($name).'</a>';
	};
	if($_POST['rtype']=='rawname')
	{
		header("Content-type: text/plain; charset=UTF-8");
		print $uploadfile;
	};
	if($_POST['rtype']=='ext')
	{
		header("Content-type: text/plain; charset=UTF-8");
		print "400\n".$uploadfile;
	};
}else{

	if($_POST['rtype']=='ext')print "403\n";
	if($_POST['rtype']=='test')print 'Failure: '.$_FILES['file1']['tmp_name'].' => '.$uploadfile;
}








?>