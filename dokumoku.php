<?php
/*
Plugin Name: DokuMoku – Markdown-Dokumentation im WP-Admin
Description: Mehrere Markdown-Dokus (Sets) direkt im WP-Admin lesen & verlinken. Link-Rewrite für .md, helles UI, Header/Footer.
Author: codekeks.de & Andreas Grzybowski
Version: 0.1.0
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
License: GPLv2 or later
Text Domain: dokumoku
*/

if (!defined('ABSPATH')) exit;

define('DOKUMOKU_PATH', plugin_dir_path(__FILE__));
define('DOKUMOKU_URL', plugin_dir_url(__FILE__));
define('DOKUMOKU_DOCS_DIR', DOKUMOKU_PATH . 'docs');

function dokumoku_capability() { return apply_filters('dokumoku_capability', 'manage_options'); }

function dokumoku_menu_icon() {
    $svg = file_get_contents(DOKUMOKU_PATH.'assets/icon.svg');
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

add_action('admin_menu', function(){
    add_menu_page(
        __('DokuMoku','dokumoku'),
        __('DokuMoku','dokumoku'),
        dokumoku_capability(),
        'dokumoku',
        'dokumoku_admin_page',
        dokumoku_menu_icon(),
        58
    );
});

function dokumoku_normalize_path($path) {
    $path = str_replace('\\','/',$path);
    $parts = array();
    foreach (explode('/', $path) as $seg) {
        if ($seg === '' || $seg === '.') continue;
        if ($seg === '..') { array_pop($parts); continue; }
        $parts[] = $seg;
    }
    return implode('/', $parts);
}
function dokumoku_admin_doc_url($set, $rel) {
    $enc = implode('/', array_map('rawurlencode', explode('/', $rel)));
    return admin_url('admin.php?page=dokumoku&set='.rawurlencode($set).'&doc='.$enc);
}
function dokumoku_current_url() {
    $scheme = is_ssl() ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $uri  = isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '#') : '/';
    return $scheme.'://'.$host.$uri;
}
function dokumoku_frontend_doc_url($set, $rel) {
    $base = dokumoku_current_url();
    $enc = implode('/', array_map('rawurlencode', explode('/', $rel)));
    return add_query_arg(array('dokumoku_set'=>$set,'dokumoku_doc'=>$enc), $base);
}
function dokumoku_rewrite_doc_href($set, $currentDoc, $href, $frontend=false) {
    if ($href === '') return $href;
    if ($href[0] === '#' || preg_match('~^[a-z]+:~i',$href)) return $href;
    if ($href[0] === '/') return $href;
    $anchor=''; $pos=strpos($href,'#');
    if ($pos!==false){ $anchor=substr($href,$pos); $href=substr($href,0,$pos); }
    if (!preg_match('~\.md$~i',$href)) return $href.$anchor;
    $baseDir = strpos($currentDoc,'/')!==False ? substr($currentDoc,0,strrpos($currentDoc,'/')) : '';
    $targetRel = dokumoku_normalize_path(($baseDir?$baseDir.'/':'').$href);
    $url = $frontend ? dokumoku_frontend_doc_url($set,$targetRel) : dokumoku_admin_doc_url($set,$targetRel);
    return $url.$anchor;
}

function dokumoku_parse_inline($txt, $set='', $currentDoc='', $frontend=false){
    $txt = esc_html($txt);
    $txt = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function($m) use ($set,$currentDoc,$frontend){
        $text = $m[1]; $hrefRaw = $m[2];
        $url = dokumoku_rewrite_doc_href($set,$currentDoc,$hrefRaw,$frontend);
        $attrs = (strpos($url,'admin.php?page=dokumoku')!==false || strpos($url,'dokumoku_set=')!==false) ? '' : ' target="_blank" rel="noopener"';
        return '<a href="'.esc_url($url).'"'.$attrs.'>'.$text.'</a>';
    }, $txt);
    $txt = preg_replace('/\*\*(.+?)\*\*/s','<strong>$1</strong>',$txt);
    $txt = preg_replace('/\*(.+?)\*/s','<em>$1</em>',$txt);
    $txt = preg_replace_callback('/`([^`]+)`/', function($m){ return '<code>'.esc_html($m[1]).'</code>'; }, $txt);
    return $txt;
}
function dokumoku_parse_block_markdown($text, $set, $currentDoc, $frontend=false){
    $lines = explode("\n", $text);
    $html=''; $in_ul=false; $in_ol=false; $in_p=false;
    $flush_p=function()use(&$html,&$in_p){ if($in_p){ $html.="</p>\n"; $in_p=false; } };
    $close_lists=function()use(&$html,&$in_ul,&$in_ol){ if($in_ul){$html.="</ul>\n";$in_ul=false;} if($in_ol){$html.="</ol>\n";$in_ol=false;} };
    foreach($lines as $line){
        $trim=rtrim($line);
        if($trim===''){ $flush_p(); $close_lists(); continue; }
        if(preg_match('/^(#{1,6})\s+(.*)$/',$trim,$m)){
            $flush_p(); $close_lists();
            $lvl=strlen($m[1]); $html.='<h'.$lvl.'>'.dokumoku_parse_inline($m[2],$set,$currentDoc,$frontend).'</h'.$lvl.'>'."\n"; continue;
        }
        if(preg_match('/^\s*[-*]\s+(.*)$/',$trim,$m)){
            $flush_p(); if(!$in_ul){$html.="<ul>\n";$in_ul=true;} $html.='<li>'.dokumoku_parse_inline($m[1],$set,$currentDoc,$frontend)."</li>\n"; continue;
        }
        if(preg_match('/^\s*\d+\.\s+(.*)$/',$trim,$m)){
            $flush_p(); if(!$in_ol){$html.="<ol>\n";$in_ol=true;} $html.='<li>'.dokumoku_parse_inline($m[1],$set,$currentDoc,$frontend)."</li>\n"; continue;
        }
        if(!$in_p){ $html.="<p>"; $in_p=true; }
        $html .= dokumoku_parse_inline($trim,$set,$currentDoc,$frontend).' ';
    }
    if($in_p) $html.="</p>\n"; if($in_ul) $html.="</ul>\n"; if($in_ol) $html.="</ol>\n";
    return $html;
}
function dokumoku_render_doc($set, $rel_file, $frontend=false){
    $allowed = dokumoku_list_docs($set);
    if (!in_array($rel_file,$allowed,true)){
        return '<div class="notice notice-error"><p>'.__('Datei nicht erlaubt oder nicht gefunden.','dokumoku').'</p></div>';
    }
    $full = DOKUMOKU_DOCS_DIR.'/'.$set.'/'.$rel_file;
    $md = @file_get_contents($full);
    if ($md===false) return '<div class="notice notice-error"><p>'.__('Datei konnte nicht gelesen werden.','dokumoku').'</p></div>';
    $md = str_replace(array("\r\n","\r"),"\n",$md);
    $parts = preg_split('/^```([a-zA-Z0-9_-]*)\s*$/m',$md,-1,PREG_SPLIT_DELIM_CAPTURE);
    $html='';
    for($i=0;$i<count($parts);$i++){
        if($i%3==0){ $html .= dokumoku_parse_block_markdown($parts[$i],$set,$rel_file,$frontend); }
        else { $lang=sanitize_html_class($parts[$i]); $code=esc_html($parts[$i+1]); $i++; $html.='<pre><code'.($lang?' class="language-'.$lang.'"':'').'>'.$code.'</code></pre>'; }
    }
    return '<article class="dm-article" data-set="'.esc_attr($set).'" data-doc="'.esc_attr($rel_file).'">'.$html.'</article>';
}

function dokumoku_scan_tree($base_dir){
    $res=array(); if(!is_dir($base_dir)) return $res;
    $iter=function($dir,$prefix='') use(&$res,&$iter){
        foreach(scandir($dir) as $f){
            if($f==='.'||$f==='..') continue;
            $p=$dir.'/'.$f;
            if(is_dir($p)) $iter($p,$prefix.$f.'/');
            elseif(preg_match('/\.md$/i',$f)) $res[]=$prefix.$f;
        }
    };
    $iter($base_dir,'');
    sort($res,SORT_NATURAL|SORT_FLAG_CASE);
    return $res;
}
function dokumoku_docsets(){
    $sets=array(); if(!is_dir(DOKUMOKU_DOCS_DIR)) return $sets;
    foreach(scandir(DOKUMOKU_DOCS_DIR) as $f){
        if($f==='.'||$f==='..') continue;
        if(is_dir(DOKUMOKU_DOCS_DIR.'/'.$f)) $sets[]=$f;
    }
    sort($sets,SORT_NATURAL|SORT_FLAG_CASE);
    return $sets;
}
function dokumoku_list_docs($set){
    $base = DOKUMOKU_DOCS_DIR.'/'.$set;
    if(!is_dir($base)) return array();
    return dokumoku_scan_tree($base);
}

function dokumoku_admin_page(){
    if(!current_user_can(dokumoku_capability())) wp_die(__('Kein Zugriff','dokumoku'));
    $sets = dokumoku_docsets();
    $set  = isset($_GET['set']) ? sanitize_text_field($_GET['set']) : ($sets ? $sets[0] : '');
    if ($set && !in_array($set,$sets,true)) $set = $sets ? $sets[0] : '';
    $docs = $set ? dokumoku_list_docs($set) : array();
    $doc  = isset($_GET['doc']) ? sanitize_text_field($_GET['doc']) : '';
    if (!$doc || !in_array($doc,$docs,true)) {
        $doc = in_array('index.md',$docs,true) ? 'index.md' : ($docs ? $docs[0] : '');
    }

    echo '<div class="wrap dm-wrap">';
    echo '<h1 class="screen-reader-text">DokuMoku</h1>';
    echo '
    <div class="dm-header">
      <div class="dm-header-left">
        <span class="dm-logo" aria-hidden="true"></span>
        <div>
          <h2 class="dm-title">DokuMoku</h2>
          <p class="dm-sub">Markdown‑Dokumentation im WP‑Admin (Sets, interne Links, helles UI).</p>
        </div>
      </div>
      <div class="dm-header-right">
        <span class="dm-badge">v0.1.0</span>
        <a class="button" href="https://wordpress.org/plugins/" target="_blank" rel="noopener">Mehr Plugins</a>
      </div>
    </div>';

    echo '<div class="dm-topbar"><form method="get">';
    echo '<input type="hidden" name="page" value="dokumoku" />';
    echo '<label>Dokuset: <select name="set">';
    foreach($sets as $s){ $sel = ($s===$set)?' selected':''; echo '<option value="'.esc_attr($s).'"'.$sel.'>'.esc_html($s).'</option>'; }
    echo '</select></label> ';
    if ($set){
        echo '<label>Datei: <select name="doc">';
        foreach($docs as $f){ $sel = ($f===$doc)?' selected':''; echo '<option value="'.esc_attr($f).'"'.$sel.'>'.esc_html($f).'</option>'; }
        echo '</select></label> ';
    }
    echo '<button class="button button-primary">Öffnen</button>';
    echo '</form></div>';

    echo '<div class="dm-layout">';
    echo '<aside class="dm-sidebar">'; if($set) echo dokumoku_sidebar_tree($set,$doc); echo '</aside>';
    echo '<main class="dm-main">'; if($set && $doc) echo dokumoku_render_doc($set,$doc,false); echo '</main>';
    echo '</div>';

    echo '<div class="dm-footer">DokuMoku <strong>v0.1.0</strong> • © '.date('Y').' codekeks.de • <a href="https://codekeks.de" target="_blank" rel="noopener">Andreas Grzybowski</a>
    • <a href="https://github.com/antman313/dokumoku" target="_blank" rel="noopener">GitHub</a>

    </div>';
    echo '</div>';
}

function dokumoku_sidebar_tree($set,$current){
    $files = dokumoku_list_docs($set);
    $tree = array();
    foreach($files as $rel){
        $parts = explode('/',$rel); $node=&$tree;
        foreach($parts as $i=>$p){
            if($i===count($parts)-1){ $node['__files'][]=$rel; }
            else { if(!isset($node[$p])) $node[$p]=array(); $node=&$node[$p]; }
        }
    }
    $html='<ul class="dm-tree">'; $html.=dokumoku_render_tree_nodes($set,$tree,$current); $html.='</ul>';
    return $html;
}
function dokumoku_render_tree_nodes($set,$node,$current,$prefix=''){
    $html='';
    foreach($node as $key=>$val){
        if($key==='__files'){
            foreach($val as $rel){
                $active = ($rel===$current)?' class="active"':'';
                $url = dokumoku_admin_doc_url($set,$rel);
                $html.='<li'.$active.'><a href="'.esc_url($url).'">'.esc_html($rel).'</a></li>';
            }
            continue;
        }
        $html.='<li class="folder"><span>'.esc_html($key).'</span><ul>';
        $html.=dokumoku_render_tree_nodes($set,$val,$current,$prefix.$key.'/');
        $html.='</ul></li>';
    }
    return $html;
}

add_shortcode('dokumoku', function($atts){
    $a = shortcode_atts(array('set'=>'','file'=>'index.md','only_logged_in'=>'1'), $atts, 'dokumoku');
    if ($a['only_logged_in']==='1' && !is_user_logged_in()) return '<div class="dm-locked">'.__('Bitte einloggen, um die Dokumentation zu sehen.','dokumoku').'</div>';
    $set = sanitize_text_field($a['set']); if(!$set) return '<div class="notice notice-error"><p>'.__('Bitte set="..." angeben.','dokumoku').'</p></div>';
    $requested = isset($_GET['dokumoku_doc']) ? sanitize_text_field($_GET['dokumoku_doc']) : '';
    $file = $requested ?: sanitize_text_field($a['file']);
    return dokumoku_render_doc($set,$file,true);
});

add_action('admin_enqueue_scripts', function($hook){
    if ($hook !== 'toplevel_page_dokumoku') return;

    $css = '
      .dm-header{ clear:both; margin:8px 0 16px; padding:16px 18px; border:1px solid #dcdcde; border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:space-between; gap:16px }
      .dm-wrap > h1{ display:none }
      .dm-header-left{ display:flex; gap:14px; align-items:center }
      .dm-logo{ width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,#2271b1 0%, #43a047 100%); position:relative; display:inline-block; flex:0 0 40px }
      .dm-logo:after{ content:""; position:absolute; inset:10px; border-radius:6px; background:#fff; box-shadow:0 0 0 2px rgba(255,255,255,.25) inset }
      .dm-title{ margin:0; line-height:1.2; font-size:20px; font-weight:700; color:#1d2327 }
      .dm-sub{ margin:.25rem 0 0; color:#50575e }
      .dm-header-right{ display:flex; align-items:center; gap:8px }
      .dm-badge{ display:inline-block; padding:2px 8px; border-radius:999px; font-size:12px; background:#f0f6fc; color:#2271b1; border:1px solid #d0e3f2 }
      .dm-topbar{ margin:12px 0 }
      .dm-layout{ display:grid; grid-template-columns:260px 1fr; gap:20px }
      .dm-sidebar{ background:#f9f9f9; border:1px solid #dcdcde; border-radius:8px; padding:12px; max-height:70vh; overflow:auto }
      .dm-sidebar .folder>span{ font-weight:600; display:block; margin-top:8px; color:#1d2327 }
      .dm-sidebar li{ margin:0 0 6px 0 }
      .dm-sidebar li.active>a{ font-weight:700; text-decoration:underline }
      .dm-main{ background:#fff; border:1px solid #dcdcde; border-radius:8px; padding:22px; max-height:70vh; overflow:auto; color:#1d2327 }
      .dm-article h1,.dm-article h2,.dm-article h3{ margin-top:1.2em; color:#1d2327 }
      .dm-article a{ color:#2271b1 }
      .dm-article pre{ background:#f6f7f7; color:#111; padding:14px; border-radius:8px; overflow:auto; font-size:13px; line-height:1.45; border:1px solid #e4e6e7 }
      .dm-article code{ background:#f0f0f1; padding:2px 6px; border-radius:4px; font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace; border:1px solid #e4e6e7 }
      .dm-footer{ margin-top:16px; padding:10px 0; color:#6b7280; font-size:12px; border-top:1px solid #e5e7eb }
    ';
    wp_register_style('dokumoku-admin', false);
    wp_enqueue_style('dokumoku-admin');
    wp_add_inline_style('dokumoku-admin', $css);

    $js = "
      document.addEventListener('click', function(e){
        const a = e.target.closest('.dm-article a'); if(!a) return;
        const href = a.getAttribute('href') || '';
        if (!href || href.startsWith('#') || /^[a-z]+:/i.test(href) || href.startsWith('/')) return;
        if (!/\.md($|#)/i.test(href)) return;
        e.preventDefault();
        const art = a.closest('.dm-article');
        const set = art?.dataset?.set || '';
        const cur = art?.dataset?.doc || '';
        function norm(p){ const out=[]; p.split('/').forEach(seg=>{ if(!seg||seg==='.') return; if(seg==='..') out.pop(); else out.push(seg); }); return out.join('/'); }
        const hashIndex = href.indexOf('#');
        const link = hashIndex>=0 ? href.slice(0,hashIndex) : href;
        const anchor = hashIndex>=0 ? href.slice(hashIndex) : '';
        const baseDir = cur.includes('/') ? cur.slice(0, cur.lastIndexOf('/'))+'/' : '';
        const target = norm(baseDir + link);
        const enc = target.split('/').map(encodeURIComponent).join('/');
        const url = '".admin_url('admin.php?page=dokumoku')."&set=' + encodeURIComponent(set) + '&doc=' + enc + anchor;
        window.location.href = url;
      }, {capture:true});

      (function(){
        const setSel = document.querySelector('select[name=set]');
        const docSel = document.querySelector('select[name=doc]');
        if (setSel && docSel) {
          setSel.addEventListener('change', () => {
            for (const o of docSel.options) { if (o.value === 'index.md') { o.selected = true; break; } }
          });
        }
      })();
    ";
    wp_register_script('dokumoku-admin-js', '');
    wp_enqueue_script('dokumoku-admin-js');
    wp_add_inline_script('dokumoku-admin-js', $js);
});
