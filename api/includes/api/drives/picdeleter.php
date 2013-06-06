<?php
include_once $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/log/logger.php';


function picdeleter($drives,$path,$module)
{

	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';	
	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/drives_'.$module.'.php';
	//echo $cfg['drives'][$drives]['picheader'];
	$pathinfo=pathinfo($path);
	$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['serverlog']);
	if (isset($cfg['drives'][$drives]['picheader']))
		$picheader=$cfg['drives'][$drives]['picheader'];
	else
		$picheader='';
	$drives_path=$_SERVER['DOCUMENT_ROOT'].$pathinfo['dirname'].'/'.$picheader.$pathinfo['basename'];
	$l->writelog('delete file:'.$drives_path);
	@unlink($drives_path);
	if (file_exists($drives_path)) return 1; else return -1;
}
