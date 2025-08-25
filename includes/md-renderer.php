<?php if (!defined('ABSPATH')) exit;

function dokumoku_parse_inline($txt,$set='',$currentDoc='',$frontend=false){
	$txt = esc_html($txt);
	$txt = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function($m) use ($set,$currentDoc,$frontend){
		$text=$m[1]; $hrefRaw=$m[2];
		$url = dokumoku_rewrite_doc_href($set,$currentDoc,$hrefRaw,$frontend);
		$attrs = (strpos($url,'admin.php?page=dokumoku')!==false || strpos($url,'dokumoku_set=')!==false) ? '' : ' target="_blank" rel="noopener"';
		return '<a href="'.esc_url($url).'"'.$attrs.'>'.$text.'</a>';
	}, $txt);
	$txt = preg_replace('/\*\*(.+?)\*\*/s','<strong>$1</strong>',$txt);
	$txt = preg_replace('/\*(.+?)\*/s','<em>$1</em>',$txt);
	$txt = preg_replace_callback('/`([^`]+)`/', fn($m)=>'<code>'.esc_html($m[1]).'</code>', $txt);
	return $txt;
}

function dokumoku_parse_block_markdown($text,$set,$currentDoc,$frontend=false){
	$lines = explode("\n",$text);
	$html=''; $in_ul=false; $in_ol=false; $in_p=false;
	$flush=function()use(&$html,&$in_p){ if($in_p){$html.="</p>\n"; $in_p=false;} };
	$close=function()use(&$html,&$in_ul,&$in_ol){ if($in_ul){$html.="</ul>\n"; $in_ul=false;} if($in_ol){$html.="</ol>\n"; $in_ol=false;} };
	foreach($lines as $line){
		$t=rtrim($line);
		if($t===''){ $flush(); $close(); continue; }
		if(preg_match('/^(#{1,6})\s+(.*)$/',$t,$m)){
			$flush(); $close(); $lvl=strlen($m[1]);
			$html.='<h'.$lvl.'>'.dokumoku_parse_inline($m[2],$set,$currentDoc,$frontend).'</h'.$lvl.'>'."\n"; continue;
		}
		if(preg_match('/^\s*[-*]\s+(.*)$/',$t,$m)){
			$flush(); if(!$in_ul){$html.="<ul>\n"; $in_ul=true;}
			$html.='<li>'.dokumoku_parse_inline($m[1],$set,$currentDoc,$frontend)."</li>\n"; continue;
		}
		if(preg_match('/^\s*\d+\.\s+(.*)$/',$t,$m)){
			$flush(); if(!$in_ol){$html.="<ol>\n"; $in_ol=true;}
			$html.='<li>'.dokumoku_parse_inline($m[1],$set,$currentDoc,$frontend)."</li>\n"; continue;
		}
		if(!$in_p){ $html.="<p>"; $in_p=true; }
		$html .= dokumoku_parse_inline($t,$set,$currentDoc,$frontend).' ';
	}
	if($in_p) $html.="</p>\n"; if($in_ul) $html.="</ul>\n"; if($in_ol) $html.="</ol>\n";
	return $html;
}

function dokumoku_render_doc($set,$rel_file,$frontend=false){
	$allowed = dokumoku_list_docs($set);
	if (!in_array($rel_file,$allowed,true)){
		return '<div class="notice notice-error"><p>'.__('Datei nicht erlaubt oder nicht gefunden.','dokumoku').'</p></div>';
	}
	$md = dokumoku_read($set,$rel_file);
	if ($md===false) return '<div class="notice notice-error"><p>'.__('Datei konnte nicht gelesen werden.','dokumoku').'</p></div>';

	$md = str_replace(["\r\n","\r"],"\n",$md);
	$parts = preg_split('/^```([a-zA-Z0-9_-]*)\s*$/m',$md,-1,PREG_SPLIT_DELIM_CAPTURE);
	$html='';
	for($i=0;$i<count($parts);$i++){
		if($i%3==0){ $html .= dokumoku_parse_block_markdown($parts[$i],$set,$rel_file,$frontend); }
		else { $lang=sanitize_html_class($parts[$i]); $code=esc_html($parts[$i+1]); $i++;
			   $html.='<pre><code'.($lang?' class="language-'.$lang.'"':'').'>'.$code.'</code></pre>'; }
	}
	return '<article class="dm-article" data-set="'.esc_attr($set).'" data-doc="'.esc_attr($rel_file).'">'.$html.'</article>';
}