<?php

define("__CFG_document_place__",'/api' );
	$GET=$_GET;
	$POST=$_POST;
	$TABLE=$GET['table'];


	$classinfo=file_exists($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/action_$TABLE.php");
	if (!($classinfo))
	{header("HTTP/1.0 404 Not Found");die();}



	$start=time();
	
	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/setting.php';
	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/database/sql.php';

	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/action_$TABLE.php";
	$actionspace=new $TABLE();

	$sql1=new SQL();
	$flag=0;

	if (!(isset($GET['action']))) $GET['action']=$actionspace->default_action;
//	$sql1->debug=true;
	$ACTION=$GET['action'];
	//记录日志
		include_once $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/log/logger.php';
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['apilog']);
		
		$l->writelog($TABLE.'-'.$ACTION.json_encode($GET).' ip='.$_SERVER["REMOTE_ADDR"],'api-QUERY');


	//日志end
	

	//echo $ACTION;
	$ans=1;
	$ans1=array();
	$data=$GET;
	//$sql1->debug=true;
	if ((isset($data['DataLimitStart'])) &&  (isset($data['DataLimitLength'])))
	{
		$sql1->S_limit=array($data['DataLimitStart'],$data['DataLimitLength']);
	}
	if (!(isset($data['DataLimitStart'])) &&  (isset($data['DataLimitLength'])))
	{
		$sql1->S_limit=array(0,$data['DataLimitLength']);
	}
	if (isset($_GET['callback']))unset($data['callback']);
	unset($data['table']);
	unset($data['action']);
	if (isset($data['cache']))unset($data['cache']);
	if (isset($data['CacheUpdate']))unset($data['CacheUpdate']);
	if (isset($data['zipmode']))unset($data['zipmode']);
	unset($data['_']);
	if (!(isset($GET['cache']))) $GET['cache']=$cfg['cache'];
	$cachestat=file_exists($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/cache/$TABLE".'_'."$ACTION.json");
	if (!isset($GET['CacheUpdate'])) $GET['CacheUpdate']=false;
	
	$cache_used=false;
	//缓存+调用action
	if ((!($data))&&($GET['cache'])&&($cachestat)&&(!$GET['CacheUpdate'])&&(!(isset($cfg['nocache'][$TABLE]))))
	{
		$cache_used=true;
		$F_json=fopen($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/cache/$TABLE".'_'."$ACTION.json",'rb');
		flock($F_json, LOCK_SH);
		$json=fread($F_json,8388608);//最大缓存文件大小8M
		flock($F_json,LOCK_UN);
		fclose($F_json);
	//	echo( $json);	
	}
	elseif ($ans==-1) 
	{
		$json='{}';
	}
	else 
	{	
		$callback = isset($_GET['callback']) ? $_GET['callback'] : ''; //callback 跨域传输准备
		//action信息设定
		$sql1=new SQL();
		//$sql1->debug=true;
		$sql1->table=$TABLE;
		$ACTION1="sql_".$ACTION;  
		//抗注入
		/*
		foreach ($data as $key=>$val)
		{
			$data $
		}*/
		//执行action

		$ans1=$actionspace->$ACTION1($sql1,$data);
		$ans=array();
		$ans=$ans1;
		$json=json_encode($ans);
		//callback处理
		if (!empty($callback))
		{
			$json = $callback . '(' . $json . ')';
		}
		//缓存写入
		if ((!($data))&&($GET['cache'])&&(!$GET['CacheUpdate'])&&(!(isset($cfg['nocache'][$TABLE])))&&(!($cache_used)))
		{
			if ($cachestat)unlink($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/cache/$TABLE".'_'."$ACTION.json");
			$f=fopen($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/cache/$TABLE".'_'."$ACTION.json", 'wb+');
			 if(flock($f, LOCK_EX | LOCK_NB))
			{
				fwrite($f, $json);
				flock($f,LOCK_UN);
				fclose($f);	
			}
			
		}
		
	}
	echo $json;
//
?>