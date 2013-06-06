<?php
function defaulttrans($result,$valrow,$drive)
{
	return $result[$valrow];
}
class moduletype
{
	function __autoload()
	{
		
		
	}
	public $default_action='getmoduletype';
	function loaddic($table,$keyrow,$valrow,$dic=array(),$function='defaulttrans',$drives='')
	{
		
		$sql=new SQL();
		$sql->table=$table;

		$res=$sql->select();
		
		if ($res>0)
		{
			while ($result=$sql->fetch_assoc($res))
			{
				$dic[$table.$result[$keyrow]]=$function($result,$valrow,$drives);
			}
		}
		return $dic;
	}
	function gettypenamelist($moduleid)
	{
		
		include_once $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/database/sql.php";
		$sql=new SQL;
		$sql->table="moduletype";
		$where=array();
		$id[0]='moduleid';
		$id[1]=$moduleid;
		array_push($where,$id);
		$sql->S_where=$where;
		$sql->S_row=array('typename','id');
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
	//************************
	function sql_configs($sql,$data)
	{//$sql->debug=true;
		//$sql=new SQL();
		
		$sql->table="modulelist";
		//$sql->S_row=array('id','module','valid');
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
	//****************
	function sql_getmoduletype($sql,$data)
	{
		
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/drives/picloader.php"; 
		
		//$sql=new SQL();
		if (!(isset($data['drives'])))
		{
			$data['drives']='default';
		}
		$moduledic=array();
		$moduledic=$this->loaddic('modulelist','id','module');
	//	var_dump($moduledic); 
		$dic=array();
		foreach ($moduledic as $key => $val)
		{
			if ($val!='')
			{	
				if (file_exists($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/coverpathloader/$val.php"))
				{
					include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/coverpathloader/$val.php";
					$funcname='coverpathloader_'.$val;
					$dic=$this->loaddic($val, 'id', 'path',$dic,$funcname,$data['drives']);
				}
				else
				{

				}
			}
		}
		$sql->table="moduletype";
		$where=array();
		if (isset($data['id'])&&is_numeric($data['id']))
		{
			$id[0]='id';
			$id[1]=$data['id'];
			array_push($where,$id);
		}
		if (isset($data['moduleid']))
		{
			$id[0]='moduleid';
			$id[1]=$data['moduleid'];
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
				
				if ((isset($moduledic['modulelist'.$row['moduleid']]))&&(isset($dic[$moduledic['modulelist'.$row['moduleid']].$row['coverid']])))
				$row['coverpath']=$dic[$moduledic['modulelist'.$row['moduleid']].$row['coverid']];
				array_push($ans, $row);
			}
		}else return $res;
		return $ans;
	}

	/*
	 * add moduletype
	* in
	* 	moduletype.typename
	* 	moduletype.moduleid
	*/
	function sql_addmoduletype($sql,$data)
	{
		$sql->table='moduletype';
		if (!(isset($POST['moduleid']))) $POST['moduleid']='0'; 
		if (!(isset($POST['typename']))) $POST['typename']='UnName';
		if (!(isset($POST['coverid']))) $POST['coverid']='0';
		
		$sql->I_data['moduleid']=$POST['moduleid'];
		$sql->I_data['typename']=$POST['typename'];
		$sql->I_data['coverid']=$POST['coverid'];
		
		$res=$sql->insert();
			if ($res<0) $out="'添加失败-无法添加信息'";
			else $out="'添加成功'";
			echo "
			<script>
				alert($out);
				location.href='backstage/loader.php?f=addmoduletype';
			</script>
			";
			return $res;
	}
	/*
	 * 
	 */
	function sql_setalbumcover($sql,$data)
	{
		$sql=new SQL();
		$sql->table='moduletype';
		if ((isset($data['id']))&&(isset($data['coverid'])))
		{
			array_push($sql->S_where,array('id',$data['id']));
			$res1=$sql->select();
			if (($res1)&&($result=$sql->fetch_assoc($res1)))
			{
				array_push($sql->U_where,array('id',$result['id']));
				array_push($sql->U_where,array('moduleid',$result['moduleid']));
				array_push($sql->U_where,array('typename',$result['typename']));
				array_push($sql->U_where,array('coverid',$result['coverid']));
				$sql->U_set['coverid']=$data['coverid'];
				$res=$sql->update();
		$res=$sql->insert();
			if ($res<0) $out="'设置失败-无法设置信息'";
			else $out="'设置成功'";
			echo "
			<script>
				alert($out);
				location.href='backstage/loader.php?f=show".$data['from']."';
			</script>
			";
			return $res;
			}
			else 
			{
				$out="'设置失败-不存在此分类'";
				echo "
				<script>
					alert($out);
				location.href='backstage/loader.php?f=show".$data['from']."';
				</script>
				";
				return -2;
			}
		}
		else
		{
				$out="'设置失败-输入数据不全'";
				echo "
				<script>
					alert($out);
				location.href='backstage/loader.php?f=show".$data['from']."';
				</script>
				";
				return -1;
		}
	}
}
