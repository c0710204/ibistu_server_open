<?php

class server_control
{
	function __autoload()
	{
		
		
	}
	public $default_action='list';
	function sql_list($sql,$data)
	{//$sql->debug=true;
		//$sql=new SQL();
		$sql->table="server_config";
		$res=$sql->select();
		$ans=array();
		if ($res>0)
		{
			while ($result=$sql->fetch_assoc($res))
			{
				$row=array();
				$row=$result;
				array_push($ans, $row);
			}
		}else return $res;
		return $ans;
	}

}