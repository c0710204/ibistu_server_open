<?php
function coverpathloader_video($result,$valrow,$drive)
{
	
	$path=pathinfo($result['cover']);
	$drives_path=$path['dirname'].'/'.picheadloader($drive,'video').$path['basename'];
	$tampinfo=parse_url($drives_path);
	if (!(isset($tampinfo['scheme'])))
	{
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/servers.php';
		$drives_path=$cfg['servers']['pic'].$drives_path;
	}
	return $drives_path;
}