<?php if (!defined('ABSPATH')) exit;

function dokumoku_normalize_path($path){
	$path = str_replace('\\','/',$path);
	$parts = [];
	foreach (explode('/', $path) as $seg) {
		if ($seg === '' || $seg === '.') continue;
		if ($seg === '..') { array_pop($parts); continue; }
		$parts[] = $seg;
	}
	return implode('/', $parts);
}

function dokumoku_admin_doc_url($set, $rel){
	$enc = implode('/', array_map('rawurlencode', explode('/', $rel)));
	return admin_url('admin.php?page=dokumoku&set='.rawurlencode($set).'&doc='.$enc);
}

function dokumoku_current_url(){
	$scheme = is_ssl() ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'] ?? '';
	$uri  = isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '#') : '/';
	return $scheme.'://'.$host.$uri;
}

function dokumoku_frontend_doc_url($set, $rel){
	$base = dokumoku_current_url();
	$enc  = implode('/', array_map('rawurlencode', explode('/', $rel)));
	return add_query_arg(['dokumoku_set'=>$set,'dokumoku_doc'=>$enc], $base);
}

function dokumoku_rewrite_doc_href($set, $currentDoc, $href, $frontend=false){
	if ($href === '' || $href[0] === '#' || preg_match('~^[a-z]+:~i',$href) || $href[0] === '/') return $href;
	$anchor=''; $pos=strpos($href,'#'); if ($pos!==false){ $anchor=substr($href,$pos); $href=substr($href,0,$pos); }
	if (!preg_match('~\.md$~i',$href)) return $href.$anchor;
	$baseDir = strpos($currentDoc,'/')!==false ? substr($currentDoc,0,strrpos($currentDoc,'/')) : '';
	$targetRel = dokumoku_normalize_path(($baseDir?$baseDir.'/':'').$href);
	$url = $frontend ? dokumoku_frontend_doc_url($set,$targetRel) : dokumoku_admin_doc_url($set,$targetRel);
	return $url.$anchor;
}