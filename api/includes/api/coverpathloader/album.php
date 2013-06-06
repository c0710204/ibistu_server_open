<?php

function coverpathloader_album($result,$valrow,$drive)
{
	
	$drives_path=$result['path'].'/'.picheadloader($drive,'album').$result['filename'];
	$tampinfo=parse_url($drives_path);
	if (!(isset($tampinfo['scheme'])))
	{
		include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/servers.php';
		$drives_path=$cfg['servers']['pic'].$drives_path;
	}
	return $drives_path;
}