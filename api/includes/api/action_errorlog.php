<?php
class errorlog
{
	public $default_action='';
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
	function sql_heartlinktoserver($sql,$data)
	{
		return 1;
	}
	function sql_adderrorlog($sql,$data)
	{
				
		if ($_FILES['errorlog']['error']==0)
		{
			//echo $_FILES['upload_pic']['tmp_name'].'<br>';
			include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/files.php';
			$extenstr=pathinfo($_FILES['errorlog']['name']);
			$uploadfile=$cfg['file']['log']['errorlog'].'.'.$extenstr['extension'];
			//echo 	$_SERVER['DOCUMENT_ROOT'].$cfg['file']['pic']['album_dir'].'.'.$extenstr['extension'];
			if (move_uploaded_file($_FILES['upload_pic']['tmp_name'], $_SERVER['DOCUMENT_ROOT'].$uploadfile)) {
				$data['path']=$uploadfile;

			} else {
				return -1;
			}
		}
		else
		{
			//echo $_FILES['upload_pic']['error'];
			return -1.5;
		}
		
		$sql->table='errorloglist';
		$sql->I_data['path']=$data['path'];
		$res=$sql->insert();
		return $res;
	}

}

