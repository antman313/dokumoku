<?php if (!defined('ABSPATH')) exit;

add_action('admin_enqueue_scripts', function($hook){
	if ($hook !== 'toplevel_page_dokumoku') return;

	$css = '
	  .dm-header{ clear:both; margin:8px 0 16px; padding:16px 18px; border:1px solid #dcdcde; border-radius:8px; background:#fff; display:flex; align-items:center; justify-content:space-between; gap:16px }
	  .dm-wrap > h1{ display:none }
	  .dm-header-left{ display:flex; gap:14px; align-items:center }
	  .dm-logo{ width:40px; height:40px; border-radius:10px; background:linear-gradient(135deg,#2271b1 0%, #43a047 100%); position:relative; display:inline-block; flex:0 0 40px }
	  .dm-logo:after{ content:""; position:absolute; inset:10px; border-radius:6px; background:#fff; box-shadow:0 0 0 2px rgba(255,255,255,.25) inset }
	  .btdocs-icon{ width:40px; height:40px;   position:relative; display:inline-block; flex:0 0 40px }
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
