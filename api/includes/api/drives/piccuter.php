<?php

function piccuter($drives,$path,$module='album',$isbigpic=false)
{
	if ($drives=='null')return 1;
	$funcinfo['jpg']='jpeg';
	$funcinfo['png']='png';
	$funcinfo['gif']='gif';
	
	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/drives_'.$module.'.php';
	$pathinfo=pathinfo($path);
	$ext=$pathinfo['extension'];
	$funcpicloader='imagecreatefrom'.$funcinfo[$ext];
	$src_image=$funcpicloader($_SERVER['DOCUMENT_ROOT'].$path);
	$src_w=imagesx($src_image);
	$src_h=imagesy($src_image);
	$dst_w=$cfg['drives'][$drives]['width'];
	$dst_h=$cfg['drives'][$drives]['height'];
	if ($isbigpic)
	{
		/*
		设备分辨率为$dst_h/$dst_w(比如普通ipho$dst_we为480*320)
		图片分辨率为$src_h/$src_w
		图片目标分辨率为$dst_h/$dst_w
		*/
		$src_h_s=0;
		$src_h_e=$src_h;
		$src_w_s=0;
		$src_w_e=$src_w;
		//480 134
		//300 170
		if($dst_w/$dst_h>= $src_w/$src_h ){
		//设备宽高比大于图片宽高比，图片比较高，则以图片宽度为主缩放
			if($src_h<=$dst_h){
				$dst_h = $src_h;
				$dst_w = $src_w;
			}else{
				$dst_h = $dst_h;
				$dst_w = $dst_h*$src_w/$src_h;
			}

		}
		else{
		//设备宽高比小于图片宽高比，图片比较宽，则以图片高度为主缩放
			if($src_w<=$dst_w){
				$dst_h = $src_h;
				$dst_w = $src_w;
			}else{
				$dst_w = $dst_w;
				$dst_h = $dst_w*$src_h/$src_w;
			}
		}

	}
	else
	{
		if ($src_w>$src_h)
		{
			$len=($src_w-$src_h)/2;
			$src_h_s=0;
			$src_h_e=$src_h;
			$src_w_s=$len;
			$src_w_e=$src_h;
		}
		else
		{
			$len=($src_h-$src_w)/2;
			$src_w_s=0;
			$src_w_e=$src_w;
			$src_h_s=$len;
			$src_h_e=$src_w;
		}

	}
	//echo "0,0,$src_w_s,$src_h_s, $dst_w, $dst_h, $src_w_e, $src_h_e<br>";
	$dst_image= imagecreatetruecolor($dst_w, $dst_h);
	$succ=imagecopyresampled($dst_image, $src_image, 0,0,$src_w_s,$src_h_s, $dst_w, $dst_h, $src_w_e, $src_h_e);
	if (!($succ)) 
	{
		imagedestroy($src_image);
		return -1;
	}
	else
	{
		$drives_path=$_SERVER['DOCUMENT_ROOT'].$pathinfo['dirname'].'/'.$cfg['drives'][$drives]['picheader'].$pathinfo['basename'];
		$funcpicloader='imagejpeg';
		$funcpicloader($dst_image,$drives_path);
		chmod($drives_path, 0644);
		return 1;
	}
	
}
