<?php
function picheadloader($drives,$module)
{
	
	include $_SERVER['DOCUMENT_ROOT'].__CFG_document_place__.'/settings/drives_'.$module.'.php';
	//echo $cfg['drives'][$drives]['picheader'];
	return $cfg['drives'][$drives]['picheader'];
	
}
