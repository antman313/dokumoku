<?php if (!defined('ABSPATH')) exit;

function dokumoku_docsets(){
	$sets=[]; if(!is_dir(DOKUMOKU_DOCS_DIR)) return $sets;
	foreach(scandir(DOKUMOKU_DOCS_DIR) as $f){
		if($f==='.'||$f==='..') continue;
		if(is_dir(DOKUMOKU_DOCS_DIR.'/'.$f)) $sets[]=$f;
	}
	sort($sets, SORT_NATURAL|SORT_FLAG_CASE);
	return $sets;
}

function dokumoku_scan_tree($base_dir){
	$res=[]; if(!is_dir($base_dir)) return $res;
	$it=function($dir,$prefix='') use(&$res,&$it){
		foreach(scandir($dir) as $f){
			if($f==='.'||$f==='..') continue;
			$p=$dir.'/'.$f;
			if(is_dir($p)) $it($p,$prefix.$f.'/');
			elseif(preg_match('/\.md$/i',$f)) $res[]=$prefix.$f;
		}
	}; $it($base_dir,'');
	sort($res,SORT_NATURAL|SORT_FLAG_CASE);
	return $res;
}

function dokumoku_list_docs($set){
	$base = DOKUMOKU_DOCS_DIR.'/'.$set;
	if(!is_dir($base)) return [];
	return dokumoku_scan_tree($base);
}

function dokumoku_read($set,$rel){
	$full = DOKUMOKU_DOCS_DIR.'/'.$set.'/'.$rel;
	return @file_get_contents($full);
}

function dokumoku_write($set,$rel,$content){
	$full = DOKUMOKU_DOCS_DIR.'/'.$set.'/'.$rel;
	// Backup
	if(file_exists($full)) @copy($full, $full.'.'.date('Ymd-His').'.bak');
	$content = str_replace(["\r\n","\r"], "\n", $content);
	return @file_put_contents($full, $content)!==false;
}