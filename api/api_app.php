<?php

define("__CFG_document_place__",'/api' );
	$GET=$_GET;
	$POST=$_POST;
	if (!(isset($_GET['table'])))
	{
	{header("HTTP/1.0 404 Not Found");die();}
	}
	$TABLE=$GET['table'];
	//app连接信息确定
	if ((!($GET['app_key']))||(@preg_match("\<[a-fA-F0-9]{32,32}\>", $GET['app_key'])))
	{header("HTTP/1.0 401 Unauthorized");die();}
	else 
	{$app_key=$GET['app_key'];}
	if ((!($GET['app_pass']))||(@preg_match("\<[a-fA-F0-9]{32,32}\>", $GET['app_pass'])))
	{header("HTTP/1.0 401 Unauthorized");die();}
	else
	{$app_pass=$GET['app_pass'];}
	//table存在确认
	$classinfo=file_exists($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/action_$TABLE.php");
	if (!($classinfo))
	{header("HTTP/1.0 405 Method Not Allowed");die();}
	//初始化sql／配置
	$start=time();
	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/setting.php';
	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/database/sql.php';
	$sql1=new SQL();
	//检查/计数 app接口使用
	$res=$sql1->query("select id from app_key where `key`='$app_key' and `pass`='$app_pass' and `state`='online' ");
	if (!($res)||$res<0)
		{echo "\"key/pass error!\"";die();}
	if (mysql_num_rows($res)!=1)
		{echo "\"key/pass not exists or app not online!\"";die();}
	$temp=$sql1->fetch_object($res);
	$app_keyid=$temp->id;
	$res=$sql1->query("select maxusage from app_moduleusage where `modulename`='$TABLE'");
	if (!($res)||$res<0)
		{echo "\"module error\"";die();}
	if (mysql_num_rows($res)!=1)	
		{echo "\"module not exists!\"";die();}
	$temp=$sql1->fetch_object($res);
	$app_maxusage=$temp->maxusage;
	$res=$sql1->query("update app_usage set app_usage.usage=case when now()-app_usage.time>60 then 0 else app_usage.usage+1 end,app_usage.`time`=now() where `modulename`='$TABLE' and (`usage`<$app_maxusage or now()-`time`>'60')and `keyid`='$app_keyid'");
	if (mysql_affected_rows($sql1->dblink)!=1)
		{echo "\"usage error!\"";die();}
	//开始查询
	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/action_$TABLE.php";
	$actionspace=new $TABLE();


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
