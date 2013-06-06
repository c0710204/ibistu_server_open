<?php
class moduleupdate
{
	function sql_getUpdateList($sql,$data)
	{
		//$sql=new SQL();
		$sql->table="moduleupdate";
		$where=array();
		if (isset($data['id'])&&is_numeric($data['id']))
		{
			$id[0]='id';
			$id[1]=$data['id'];
			$id[2]='>';
			array_push($where,$id);
		}
		$sql->S_where=$where;
		$sql->S_row=array('id','tableName','updateTime','tableLength');
		$res=$sql->select();
		if (isset($data['id'])&&is_numeric($data['id']))	$id_new=$data['id']; else $id_new=0;
		$ans1=array();
		if ($res>0)
		{
			while ($result=$sql->fetch_object($res))
			{
				
				$ans1[$result->tableName]=$result->updateTime;

				$id_new=$result->id;
			}
		}
		$ans=array();
		$ans['id']=$id_new;
		$ans['updateList']=$ans1;
		return $ans;
	}
	function sql_getUpadtaStatue($sql,$data)
	{
		$sql->table="moduleupdate";
		$where=array();
		if (isset($data['tableName']))
		{
			$id[0]='tableName';
			$id[1]=$data['tableName'];
			array_push($where,$id);
			$sql->S_where=$where;
			$sql->S_row=array('id','tableName','updateTime','tableLength');
			$res=$sql->select();
			$ans1=-1;
			if ($res>0)
			{
				while ($result=$sql->fetch_object($res))
				{
			
					$ans1=$result->updateTime;
				}
			}else return $res;
			return $ans1;
		}
	}
	function sql_setUpdataFlag_All($sql,$data)
	{//$sql=new SQL();
		
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/tables.php';
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/api/action_helper.php';
		$help=new helper();
		$sql->table="moduleupdate";
		$sql1=new SQL();
		foreach($cfg['tableNameList'] as $tableName)
		{
			$sql->I_data['tableName']=$tableName;
			$sql->I_data['tableLength']=$help->sql_getTableLength($sql1,array('table_length'=>$tableName));
			$res=$sql->insert();
			if ($res<0) return $res;
		}
		return true;
	}
	
	function sql_setUpdataFlag($sql,$data)
	{//$sql=new SQL();
		$sql->table="moduleupdate";
		if (isset($data['tableName']))
		{				
			
			include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/tables.php';
			include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/api/action_helper.php';
			$help=new helper();
			$sql->table="moduleupdate";
			$sql1=new SQL();
			$sql->I_data['tableName']=$tableName;
			$sql->I_data['tableLength']=$help->sql_getTableLength($sql1,array('table_length'=>$tableName));
			$res=$sql->insert();
			if ($res<0) return $res;
		}
	}
}