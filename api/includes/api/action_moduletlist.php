<?php

class moduletype
{
	function __autoload()
	{
		
		
	}
	public $default_action='list';
	function sql_list($sql,$data)
	{//$sql->debug=true;
		//$sql=new SQL();
		
		$sql->table="modulelist";
		$where=array();
		if (isset($data['id'])&&is_numeric($data['id']))
		{
			$id[0]='id';
			$id[1]=$data['id'];
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
				array_push($ans, $row);
			}
		}else return $res;
		return $ans;
	}

}