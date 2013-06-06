<?php
include_once "includes/helper/token.php";
include_once $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/api/action_member.php';
include_once $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/log/logger.php';

class app
{
	public $default_action='noaction';
	//noaction
	function sql_noaction($sql,$data)
	{
		return false;
	}
	function sql_getmodulelist($sql,$data)
	{
		$querystr='
		SELECT modulename 
		FROM  `app_moduleusage` 
		';
		//echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$ans=array();
			while($row=$sql->fetch_object($res))
			{	
				array_push($ans, $row->modulename);
			}			
			return array('status'=>1,'modules'=>$ans);
		}
		else return array('status'=>0,'message'=>'查询失败');
	}

	function sql_newapp($sql,$data)
	{

		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-newapp');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-newapp');
		$app_key=buildAccessToken();
		$app_pass=buildAccessToken();
		$querystr='
		select count(*) as id 
		from app_key 
		where 
		`uid`= (
                                select 
                                min(id)
                                from member 
                        where 
                                userid='."'".$data['uid']."'".'
                        )';
		$res=$sql->query($querystr);
		if ($res>0)
		{	
			$row=$sql->fetch_object($res);
			if ($row->id>=1)
				//return array('status'=>1,'app_key'=>$data['app_key'],'app_pass'=>$app_pass);
				return array('status'=>0,'message'=>'超过限制');
			//else
				//return array('status'=>0,'message'=>'未修改');
		}
		else
		{
			return array('status'=>0,'message'=>'限制搜索失败');
		}
		$querystr='
		INSERT INTO `app_key`
		( `key`, `pass`,`uid`) 
		VALUES 
		( 
			'."'".$app_key."'".',
			'."'".$app_pass."'".',
			(
				select 
				min(id)
				from member 
			where 
				userid='."'".$data['uid']."'".'
			)
		)
		';
		//echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$l->writelog('success new app key='.$app_key.' pass='.$app_pass,'app-newapp');
			return array('status'=>1,'app_key'=>$app_key,'app_pass'=>$app_pass);
		}
		else return array('status'=>0,'message'=>'添加失败');
	}
	function sql_addmodule($sql,$data)
	{

		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-newapp');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-addmodule');
		$querystr='
		INSERT INTO `app_usage`
		( `keyid`, `modulename`) 
		VALUES 
		( 
			(
				select 
				min(id)
				from app_key 
			where 
				`key`='."'".$data['app_key']."'".'
			)
			,'."'".$data['module']."'".'
		)
		';
		//echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$l->writelog('success add module key='.$data['app_key'].' module='.$data['module'],'app-addmodule');
			return array('status'=>1,'message'=>'添加成功');
		}
		else return array('status'=>0,'message'=>'添加失败');
	}
	function sql_renewapp($sql,$data)
	{
		//$sql->debug=true;
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-renewapp');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-newapp');
		$app_pass=buildAccessToken();
		$querystr='
		update  `app_key`
		set `pass`= '."'".$app_pass."'".'
		where
			`key`='."'".$data['app_key']."'".'
		';
		//	echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			if (mysql_affected_rows()>0)
				return array('status'=>1,'app_key'=>$data['app_key'],'app_pass'=>$app_pass);
			else
				return array('status'=>0,'message'=>'未修改');
		}
		else return array('status'=>0,'message'=>'修改失败');
	}
	function sql_delapp($sql,$data)
	{
		//$sql->debug=true;
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-newapp');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-delapp');
		$querystr='
		delete from`app_usage`
		where
			`keyid`=
			(
				select 
				min(id)
				from app_key 
			where 
				`key`='."'".$data['app_key']."'".'
			)
		';
		//	echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			
		
			$querystr='
			delete from`app_key`
			where
				`key`='."'".$data['app_key']."'".'
			';
			//	echo $querystr;
			$res=$sql->query($querystr);
			if ($res>0)
			{
				if (mysql_affected_rows()>0)
					return array('status'=>1,'message'=>'删除成功');
				else
					return array('status'=>0,'message'=>'未找到');
			}
			else return array('status'=>0,'message'=>'非法');
		}
		else return array('status'=>0,'message'=>'非法');
	}
	function sql_delmodule($sql,$data)
	{
		//$sql->debug=true;
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-newapp');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-delmodule');
		$querystr='
		delete from`app_usage`
		where
			`keyid`=
			(
				select 
				min(id)
				from app_key 
			where 
				`key`='."'".$data['app_key']."'".'
			) and
			modulename='."'".$data['module']."'".'
		';
		//	echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			if (mysql_affected_rows()>0)
				return array('status'=>1,'message'=>'删除成功');
			else
				return array('status'=>0,'message'=>'未找到');
		}
		else return array('status'=>0,'message'=>'非法');
	}
	function sql_listapp($sql,$data)
	{
		//$sql->debug=true;
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-listapp');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-listapp');
		$querystr='
		select
			`key`,
			`pass`,
			`state`
		from`app_key`
		where
			`uid`= (
				select 
				min(id)
				from member 
			where 
				userid='."'".$data['uid']."'".'
			) 
		';
		//echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$ans=array();
			while($row=$sql->fetch_object($res))
			{
				array_push($ans, $row);
			}
			//var_dump($ans);
			$querystr='
			select
				`app_usage`.`modulename`,
				`app_key`.`key` as id
			from`app_key`,`app_usage`
			where
				(
					select 
					min(id)
					from member 
				where 
					userid='."'".$data['uid']."'".'
				)and 
				`app_key`.id=`app_usage`.keyid
			';	
			$res1=$sql->query($querystr);
			$ans_dic=array();
			while($row=$sql->fetch_object($res1))
			{
				if (isset($ans_dic[$row->id]))
				{
					array_push($ans_dic[$row->id], $row->modulename);
				}
				else
				{
					$ans_dic[$row->id]=array();
					array_push($ans_dic[$row->id], $row->modulename);	
				}
			}			
			
			return array('status'=>1,'apps'=>$ans,'moduleinfo'=>$ans_dic);

		}
		else return array('status'=>0,'message'=>'无法获取app列表');
	}
	//获取申请条件
	function sql_getreason($sql,$data)
	{
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-getreason');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-getreason');
		$querystr='
		select
			`reason`
		from`app_key`
		where
			`uid`= (
				select 
				min(`id`)
				from `member` 
			where 
				`userid`='."'".$data['uid']."'".'
			) and
			(
			`state`= \'wait for review\' or
			`state`= \'reviewing\' 
			)and
			`key`='."'".$data['app_key']."'".'

		';
		//	echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$row=$sql->fetch_object($res);
			$sql->query('update `app_key` set `state`=\'reviewing\' where `key`='."'".$data['app_key']."'");
				return array('status'=>1,'app_key'=>$data['app_key'],'reason'=>$row->reason);
		}
		else return array('status'=>0,'message'=>'读取失败');
	}
	//获取待审核列表
	function sql_listreviewapp($sql,$data)
	{
		//$sql->debug=true;
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-listreviewapp');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-listreviewapp');
		$querystr='
		select
			`key`,
			`pass`,
			`state`
		from`app_key`
		where
			(
			`state`= \'wait for review\' or
			`state`= \'reviewing\' 
			)
		';
		//echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$ans=array();
			while($row=$sql->fetch_object($res))
			{
				array_push($ans, $row);
			}
			//var_dump($ans);
		
			
			return array('status'=>1,'apps'=>$ans);

		}
		else return array('status'=>0,'message'=>'无法获取app列表');
	}
	//使应用上线
	function sql_setonline($sql,$data)
	{
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'app-getreason');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-getreason');
		$querystr='
		select
			`reason`
		from`app_key`
		where
			`uid`= (
				select 
				min(`id`)
				from `member` 
			where 
				`userid`='."'".$data['uid']."'".'
			) and
			(
			`state`= \'offline\' 
			)and
			`key`='."'".$data['app_key']."'".'

		';
		//	echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$row=$sql->fetch_object($res);
			$res=$sql->query('update `app_key` set `state`=\'online\' where `key`='."'".$data['app_key']."'");
			if ($res>0)
			{
			if (mysql_affected_rows()>0)
				return array('status'=>1,'app_key'=>$data['app_key'],'state'=>'online');
			else
				return array('status'=>0,'message'=>'设定失败');
			}
		}
		else return array('status'=>0,'message'=>'读取失败');
	}
	//使应用下线
	function sql_setoffline($sql,$data)
	{
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'appx-getreason');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-getreason');
		$querystr='
		select
			`reason`
		from`app_key`
		where
			`uid`= (
				select 
				min(`id`)
				from `member` 
			where 
				`userid`='."'".$data['uid']."'".'
			) and
			(
			`state`= \'online\' 
			)and
			`key`='."'".$data['app_key']."'".'

		';
		//	echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$row=$sql->fetch_object($res);
			$res=$sql->query('update `app_key` set `state`=\'offline\' where `key`='."'".$data['app_key']."'");
			if ($res>0)
			{
			if (mysql_affected_rows()>0)
				return array('status'=>1,'app_key'=>$data['app_key'],'state'=>'offine');
			else
				return array('status'=>0,'message'=>'设定失败');
			}
		}
		else return array('status'=>0,'message'=>'读取失败');
	}
	function sql_upreason($sql,$data)
	{
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['applog']);
		$data1['userid']=$data['uid'];
		$data1['pass']=$data['upass'];
		$ulog=new member();
		$uinfo=$ulog->login(new SQL,$data1);
		if ($uinfo<0)
		{
			$l->writelog($data['uid'].'登入失败 code='.$uinfo,'appx-upreason');
			return array('status'=>0,'message'=>'登入失败');
		}
		$l->writelog($data['uid'].'登入','app-upreason');
		$querystr='
		select
			`reason`
		from`app_key`
		where
			`uid`= (
				select 
				min(`id`)
				from `member` 
			where 
				`userid`='."'".$data['uid']."'".'
			) and
			(
			`state`= \'wait for edit\' 
			)and
			`key`='."'".$data['app_key']."'".'

		';
		//	echo $querystr;
		$res=$sql->query($querystr);
		if ($res>0)
		{
			$row=$sql->fetch_object($res);
			$res=$sql->query('update `app_key` set `state`=\'wait for review\',`reason`=\''.mysql_real_escape_string($data['reason']).'\' where `key`='."'".$data['app_key']."'");
			if ($res>0)
			{
			if (mysql_affected_rows()>0)
				return array('status'=>1,'app_key'=>$data['app_key'],'state'=>'wait for review');
			else
				return array('status'=>0,'message'=>'设定失败');
			}
		}
		else return array('status'=>0,'message'=>'读取失败');
	}



}
