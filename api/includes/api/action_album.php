<?php
class album
{
	public $default_action='getalbum';
	//rest
	function sql_list($sql,$data)
	{
		return sql_getalbum($sql,$data);
	}
	function sql_detail($sql,$data)
	{
		return sql_getphoto($sql,$data);
	}
	function sql_new($sql,$data)
	{
		RETURN sql_addphoto($sql,$data);
	}
	function sql_delete($sql,$data)
	{
		return sql_deletephoto($sql,$data);
	}

	//rest-end
	function sql_getalbum($sql,$data)
	{//$sql=new SQL();
		$sql->table="album";
		
		
		
		//$sql=new SQL();
		if (!(isset($data['drives'])))
		{
			$data['drives']='default';
		}
		$where=array();
		if (isset($data['typeid']))
		{
			$id[0]='typeid';
			$id[1]=$data['typeid'];
			array_push($where,$id);
		}
		if (isset($data['id'])&&is_numeric($data['id']))
		{
			$id[0]='id';
			$id[1]=$data['id'];
			array_push($where,$id);
		}
		if (isset($data['orderby']))
		{
			$id['row']='publish';
			$id['mode']='desc';
			array_push($order,$id);
		}
		$sql->S_where=$where;
		$res=$sql->select();
		$ans=array();
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/drives/picloader.php";
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/coverpathloader/album.php";
		if ($res>0)
		{
			while ($result=$sql->fetch_assoc($res))
			{
				$row=array();
				$row=$result;
				
				
				$row['picpath']=coverpathloader_album($result,'',$data['drives']);
				$row['picpath_big']=coverpathloader_album($result,'',$data['drives'].'big');
				array_push($ans, $row);
			}
		}else return $res;
		return $ans;
	}
	
	function sql_getphoto($sql,$data)
	{//$sql=new SQL();
		$sql->table="album";
		$where=array();
		if (isset($data['typeid']))
		{
			$id[0]='typeid';
			$id[1]=$data['typeid'];
			array_push($where,$id);
		}
		if (isset($data['id'])&&is_numeric($data['id']))
		{
			$id[0]='id';
			$id[1]=$data['id'];
			array_push($where,$id);
		}
		if (isset($data['orderby']))
		{
			$id['row']='publish';
			$id['mode']='desc';
			array_push($order,$id);
		}
		$sql->S_where=$where;
		$res=$sql->select();
		$ans=array();
		
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/drives/picloader.php";
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/coverpathloader/album.php";
		if ($res>0)
		{
			while ($result=$sql->fetch_assoc($res))
			{
				$row=array();
				$row=$result;
				$row['picpath']=coverpathloader_album($result,'',$data['drives'].'big');
				array_push($ans, $row);
			}
		}else return $res;
		return $ans;
	}

	/*
	 * add photo
	 * in
	 * 	album.title
	 *  album.intro
	 *  publish(auto data())
	 * 	photo temp file path
	 * out
	 *  
	 */
	function sql_addphoto($sql,$data)
	{
		include_once $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/log/logger.php';
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
		$l=new logger($_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.$cfg['file']['log']['serverlog']);
		
		$l->writelog('addphoto');
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/api/drives/piccuter.php';
		if (!(isset($_POST['title']))) $data['title']='UnName'; else $data['title']=$_POST['title'];
		if (!(isset($_POST['intro']))) $data['intro']=''; else $data['intro']=$_POST['intro'];
		if (!(isset($_POST['typeid']))) $data['typeid']='0';else $data['typeid']=$_POST['typeid'];
		if (!(isset($_POST['path']))) $data['path']=''; else $data['path']=$_POST['path'];
		if (!(isset($_POST['filename']))) $data['filename']=''; else $data['filename']=$_POST['filename'];
		if (!(isset($_POST['publish']))) $data['publish']=date('Y-m-d H:i:s'); else $data['publish']=$_POST['publish'];
		
		if ($_FILES['upload_pic']['error']==0)
		{
			//echo $_FILES['upload_pic']['tmp_name'].'<br>';
			include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
			include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/drives_album.php';		
			$extenstr=pathinfo($_FILES['upload_pic']['name']);
			$uploadfile=$cfg['file']['pic']['album_dir'].'.'.$extenstr['extension'];
			//echo 	$_SERVER['DOCUMENT_ROOT'].$cfg['file']['pic']['album_dir'].'.'.$extenstr['extension'];
			if (move_uploaded_file($_FILES['upload_pic']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$uploadfile)) 
			{
				chmod($_SERVER['DOCUMENT_ROOT'].$uploadfile, 0664);
				$path=pathinfo($uploadfile);
				$data['path']=$path['dirname'];
				$data['filename']=$path['basename'];
				$dri=$cfg['drives'];
				foreach($dri as $drive=>$inf)
				{	
					$res=piccuter($drive,$uploadfile,'album',stripos($drive,'big'));
				}
			}
			else
			{
				$out="'添加失败-文件上传出错'";
				echo "
				<script>
					alert($out);
					location.href='backstage/loader.php?f=showpic';
				</script>";
				return -1;
			}
		}
		else
		{
			//echo $_FILES['upload_pic']['error'];
			$out="'添加失败-文件上传出错'";
			echo "
			<script>
				alert($out);
				location.href='backstage/loader.php?f=showpic';
			</script>";
			return -1;
		}
		$sql->table='moduletype';
		$sql->S_row=array('amount');
		array_push($sql->S_where,array('id',$data['typeid']));
		$res=$sql->select();
		if ($result=$sql->fetch_assoc($res))
		{	
			$sql->U_set['publish']=$data['publish'];
			$sql->U_set['amount']=$result['amount']+1;
			array_push($sql->U_where,array('id',$data['typeid']));
			$res=$sql->update();
			$sql->table='album';
			$sql->I_data['title']=$data['title'];
			$sql->I_data['intro']=$data['intro'];
			$sql->I_data['typeid']=$data['typeid'];
			$sql->I_data['path']=$data['path'];
			$sql->I_data['filename']=$data['filename'];		
			$sql->I_data['publish']=$data['publish'];
			$res=$sql->insert();
			if ($res<0) $out="'添加失败-无法添加图片信息'";
			else $out="'添加成功'";
			echo "
			<script>
				alert($out);
				location.href='backstage/loader.php?f=showpic';
			</script>
			";
			return $res;
		}
		else
		{
			$out="'添加失败-分类不存在'";
			echo "
			<script>
				alert($out);
				location.href='backstage/loader.php?f=showpic';
			</script>";
			return -1;
		}
	}
	function sql_deletephoto($sql,$data)
	{
		
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/includes/api/drives/picdeleter.php';
		$sql->table='album';
		if (isset($data['id'])&&is_numeric($data['id']))
		{
			$id[0]='id';
			$id[1]=$data['id'];
			array_push($sql->S_where,$id);
			$res=$sql->select();
			if ($result=$sql->fetch_assoc($res))
			{
				include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
				include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/drives_album.php';	
				include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__."/includes/api/coverpathloader/album.php";
				$path=$result['path'].'/'.$result['filename'];
				//echo 	$_SERVER['DOCUMENT_ROOT'].$cfg['file']['pic']['album_dir'].'.'.$extenstr['extension'];
				$dri=$cfg['drives'];
				$res=picdeleter('',$path,'album');
				foreach($dri as $drive=>$inf)
				{	
					$res=picdeleter($drive,$path,'album');
				//	if ($res<0) 
				//	{
				//		unlink($uploadfile);
				//		return -2;
				//	}
				}
			}
			else
				{
					$out="'删除失败-图片不存在'";
					echo "
					<script>
						alert($out);
						location.href='backstage/loader.php?f=showpic';
					</script>
					";
					return -1;
				}
		}
		else
		{
			$out="'删除失败-未给出要删除的图片的编号'";
			echo "
			<script>
				alert($out);
				location.href='backstage/loader.php?f=showpic';
			</script>
			";
			return -1;
		}
		$id[0]='id';
		$id[1]=$result['id'];
		array_push($sql->D_where,$id);
		$res=$sql->delete();
		if ($res<0) $out="'删除失败-无法删除图片信息'";
		else $out="'删除成功'";
		echo "
		<script>
			alert($out);
			location.href='backstage/loader.php?f=showpic';
		</script>
		";
		return $res;
	}
}

