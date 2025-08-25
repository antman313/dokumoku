<?php if (!defined('ABSPATH')) exit;

function dokumoku_render_editor($set,$rel_file){
	$allowed = dokumoku_list_docs($set);
	if (!in_array($rel_file,$allowed,true)) {
		return '<div class="notice notice-error"><p>Datei nicht erlaubt oder nicht gefunden.</p></div>';
	}
	$content = dokumoku_read($set,$rel_file); if ($content===false) $content='';
	$nonce = wp_create_nonce('dokumoku_save_'.$set.'|'.$rel_file);

	$html  = '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'" class="dm-editor-form">';
	$html .= '<input type="hidden" name="action" value="dokumoku_save">';
	$html .= '<input type="hidden" name="set" value="'.esc_attr($set).'">';
	$html .= '<input type="hidden" name="doc" value="'.esc_attr($rel_file).'">';
	$html .= '<input type="hidden" name="_wpnonce" value="'.esc_attr($nonce).'">';
	$html .= '<textarea name="content" rows="24" style="width:100%;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:13px;border-radius:6px;padding:12px;">'
		  . esc_textarea($content) . '</textarea>';
	$html .= '<div style="margin-top:12px;display:flex;gap:8px">';
	$html .= '<button type="submit" class="button button-primary">Speichern</button>';
	$html .= '<a class="button" href="'.esc_url(dokumoku_admin_doc_url($set,$rel_file)).'">Abbrechen</a>';
	$html .= '</div></form>';
	return $html;
}

add_action('admin_post_dokumoku_save', function(){
	if (!current_user_can(dokumoku_capability())) wp_die('Kein Zugriff');
	$set = sanitize_text_field($_POST['set'] ?? '');
	$doc = sanitize_text_field($_POST['doc'] ?? '');
	$content = (string) ($_POST['content'] ?? '');

	if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'],'dokumoku_save_'.$set.'|'.$doc)) wp_die('Nonce ungültig');

	if (!$set || !$doc || !in_array($doc, dokumoku_list_docs($set), true)) wp_die('Ungültige Datei');

	$ok = dokumoku_write($set,$doc,$content);
	$redirect = add_query_arg(['page'=>'dokumoku','set'=>$set,'doc'=>$doc,'updated'=>$ok?'1':'0'], admin_url('admin.php'));
	wp_safe_redirect($redirect); exit;
});