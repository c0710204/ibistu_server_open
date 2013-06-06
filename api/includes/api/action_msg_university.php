<?php

class msg_university
{
	function __autoload()
	{
		
		
	}
	public $default_action='list';
	function sql_list($sql,$data)
	{//$sql->debug=true;
		//$sql=new SQL();
		
		$sql->table="msg_university";
		$where=array();
		if (isset($data['time']))
		{
			$dt=strtotime($data['time']);
			$day=date('Y-m-d H:i:s',$dt);

			$id[0]='time';
			$id[1]=$day;
			$id[2]='>';
			array_push($where,$id);
		}
		$sql->S_where=$where;
		$res=$sql->select();
		$ans=array();
		if ($res>0)
		{
			while ($result=$sql->fetch_assoc($res))
			{
				$row=array();
				$row=$result;
				$row['sourcename']="网管中心";
				$row['type']='学校';
				array_push($ans, $row);

			}
		}else return $res;
		return $ans;
	}

}