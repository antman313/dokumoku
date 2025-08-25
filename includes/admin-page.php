<?php if (!defined('ABSPATH')) exit;

function dokumoku_admin_page(){
	if(!current_user_can(dokumoku_capability())) wp_die(__('Kein Zugriff','dokumoku'));

	$sets = dokumoku_docsets();
	$set  = isset($_GET['set']) ? sanitize_text_field($_GET['set']) : ($sets ? $sets[0] : '');
	if ($set && !in_array($set,$sets,true)) $set = $sets ? $sets[0] : '';

	$docs = $set ? dokumoku_list_docs($set) : [];
	$doc  = isset($_GET['doc']) ? sanitize_text_field($_GET['doc']) : '';
	if (!$doc || !in_array($doc,$docs,true)) $doc = in_array('index.md',$docs,true) ? 'index.md' : ($docs ? $docs[0] : '');

	$mode = isset($_GET['mode']) ? sanitize_key($_GET['mode']) : 'view';

	echo '<div class="wrap dm-wrap">';
	echo '<h1 class="screen-reader-text">DokuMoku</h1>';

	// Header
	$ver = get_file_data(DOKUMOKU_PATH.'dokumoku.php', ['Version'=>'Version'], false)['Version'] ?? '';
	echo '<div class="dm-header"><div class="dm-header-left"><span class="dm-logo"></span>
			<div><h2 class="dm-title">DokuMoku</h2>
			<p class="dm-sub">Markdown-Dokumentation im WP-Admin (Sets, interne Links, helles UI).</p></div></div>
		  <div class="dm-header-right"><span class="dm-badge">v'.esc_html($ver).'</span>
			<a class="button" href="https://github.com/antman313/dokumoku" target="_blank">GitHub</a></div></div>';

	// Notices
	if (isset($_GET['updated'])) {
		echo ($_GET['updated']==='1')
			? '<div class="notice notice-success is-dismissible"><p>Änderungen gespeichert.</p></div>'
			: '<div class="notice notice-error is-dismissible"><p>Konnte nicht speichern. Dateirechte prüfen.</p></div>';
	}

	// Topbar Auswahl
	echo '<div class="dm-topbar"><form method="get">
			<input type="hidden" name="page" value="dokumoku" />
			<label>Dokuset: <select name="set">';
	foreach($sets as $s){ $sel=($s===$set)?' selected':''; echo '<option value="'.esc_attr($s).'"'.$sel.'>'.esc_html($s).'</option>'; }
	echo '</select></label> ';
	if ($set){
		echo '<label>Datei: <select name="doc">';
		foreach($docs as $f){ $sel=($f===$doc)?' selected':''; echo '<option value="'.esc_attr($f).'"'.$sel.'>'.esc_html($f).'</option>'; }
		echo '</select></label> ';
	}
	echo '<button class="button button-primary">Öffnen</button></form></div>';

	// Layout
	echo '<div class="dm-layout">';
	echo '<aside class="dm-sidebar">'.($set? dokumoku_sidebar_tree($set,$doc):'').'</aside>';
	echo '<main class="dm-main">';

	$base = dokumoku_admin_doc_url($set,$doc);
	echo '<div class="dm-actions" style="margin:-6px 0 12px;display:flex;gap:12px;font-size:13px">';
	if ($mode==='edit'){
		echo '<strong>Bearbeiten</strong> · <a href="'.esc_url($base).'">Abbrechen</a>';
	} else {
		echo '<a href="'.esc_url($base.'&mode=edit').'">Bearbeiten</a> · ';
		echo '<a href="'.esc_url($base.'&mode=new').'">Neu</a> · ';
		echo '<a href="'.esc_url($base.'&mode=delete').'" onclick="return confirm(\'Datei wirklich löschen?\')">Löschen</a>';
	}
	echo '</div>';

	echo ($mode==='edit') ? dokumoku_render_editor($set,$doc) : dokumoku_render_doc($set,$doc,false);
	echo '</main></div>';

	echo '<div class="dm-footer">DokuMoku <strong>v'.esc_html($ver).'</strong> • © '.date('Y').' codekeks.de • <a href="https://codekeks.de" target="_blank" rel="noopener">Andreas Grzybowski</a> • <a href="https://github.com/antman313/dokumoku" target="_blank" rel="noopener">GitHub</a></div>';
	echo '</div>';
}

function dokumoku_sidebar_tree($set,$current){
	$files = dokumoku_list_docs($set);
	$tree = [];
	foreach($files as $rel){
		$parts = explode('/',$rel); $node=&$tree;
		foreach($parts as $i=>$p){
			if($i===count($parts)-1){ $node['__files'][]=$rel; }
			else { if(!isset($node[$p])) $node[$p]=[]; $node=&$node[$p]; }
		}
	}
	$html='<ul class="dm-tree">'.dokumoku_render_tree_nodes($set,$tree,$current).'</ul>';
	return $html;
}
function dokumoku_render_tree_nodes($set,$node,$current,$prefix=''){
	$html='';
	foreach($node as $key=>$val){
		if($key==='__files'){
			foreach($val as $rel){
				$active = ($rel===$current)?' class="active"':'';
				$html.='<li'.$active.'><a href="'.esc_url(dokumoku_admin_doc_url($set,$rel)).'">'.esc_html($rel).'</a></li>';
			} continue;
		}
		$html.='<li class="folder"><span>'.esc_html($key).'</span><ul>';
		$html.=dokumoku_render_tree_nodes($set,$val,$current,$prefix.$key.'/');
		$html.='</ul></li>';
	}
	return $html;
}