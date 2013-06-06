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
		�豸�ֱ���Ϊ$dst_h/$dst_w(������ͨipho$dst_weΪ480*320)
		ͼƬ�ֱ���Ϊ$src_h/$src_w
		ͼƬĿ��ֱ���Ϊ$dst_h/$dst_w
		*/
		$src_h_s=0;
		$src_h_e=$src_h;
		$src_w_s=0;
		$src_w_e=$src_w;
		//480 134
		//300 170
		if($dst_w/$dst_h>= $src_w/$src_h ){
		//�豸��߱ȴ���ͼƬ��߱ȣ�ͼƬ�Ƚϸߣ�����ͼƬ���Ϊ������
			if($src_h<=$dst_h){
				$dst_h = $src_h;
				$dst_w = $src_w;
			}else{
				$dst_h = $dst_h;
				$dst_w = $dst_h*$src_w/$src_h;
			}

		}
		else{
		//�豸��߱�С��ͼƬ��߱ȣ�ͼƬ�ȽϿ�����ͼƬ�߶�Ϊ������
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
