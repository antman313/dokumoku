<?php if (!defined('ABSPATH')) exit;

add_shortcode('dokumoku', function($atts){
	$a = shortcode_atts(['set'=>'','file'=>'index.md','only_logged_in'=>'1'], $atts, 'dokumoku');
	if ($a['only_logged_in']==='1' && !is_user_logged_in())
		return '<div class="dm-locked">'.__('Bitte einloggen, um die Dokumentation zu sehen.','dokumoku').'</div>';
	$set = sanitize_text_field($a['set']); if(!$set)
		return '<div class="notice notice-error"><p>'.__('Bitte set="..." angeben.','dokumoku').'</p></div>';
	$requested = isset($_GET['dokumoku_doc']) ? sanitize_text_field($_GET['dokumoku_doc']) : '';
	$file = $requested ?: sanitize_text_field($a['file']);
	return dokumoku_render_doc($set,$file,true);
});