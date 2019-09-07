<?php if (!defined('ABSPATH')) die(); 

	function MediumEditor_register_settings() { 
	   add_option( 'MediumEditor', 'Medium Editor');
	   register_setting( 'myplugin_options_group', 'MediumEditor', 'myplugin_callback' );
	}
	add_action( 'admin_init', 'MediumEditor_register_settings' );

	function MediumEditor_register_options_page() {
	  add_options_page('Better Editor', 'Better Editor', 'manage_options', 'BetterEditor', 'MediumEditor_options_page');
	}
	add_action('admin_menu', 'MediumEditor_register_options_page');

	//add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'MediumEditor_action_links' );

	function MediumEditor_action_links( $links ) {
	   $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=BetterEditor') ) .'">Settings</a>';
	   return $links;
	}

	$MediumEditor =  get_option('MediumEditor');
	if($MediumEditor == 'on'){ 
		// disable for posts
		add_filter('gutenberg_can_edit_post', '__return_false', 10);

		// disable for post types
		add_filter('gutenberg_can_edit_post_type', '__return_false', 10);

		// disable for posts
		add_filter('use_block_editor_for_post', '__return_false', 10);

		// disable for post types
		add_filter('use_block_editor_for_post_type', '__return_false', 10);
	}
function MediumEditor_options_page()
{
	$MediumEditor =  get_option('MediumEditor');
	if($MediumEditor){
		$MediumEditor_select = 'checked';
	}else{
		$MediumEditor_select = '';
	}
?>
  <div>
	  <h1>Better Editor</h1><hr>
	  <form method="post" name="MediumEditor_form" action="options.php">
	  	<?php //wp_nonce_field( 'MediumEditor_form_submit', 'MediumEditor_form_nonce' ); ?>
	  	  <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('MediumEditor')?>">
		  <?php settings_fields( 'myplugin_options_group' ); ?>
		  <h3>Better Editor</h3>
		  <p>Better Editor is still at it's initial stage and we're still trying to figure out which features to add and what settings should this plugin offer. <br>If you have a suggection or just wanna talk about plugin development or WordPress in general message us at <a href="https://wporb.com/">WPorb</a> </p>
		  <br>
		  <h3>Gutenberg Editor</h3>
		  <table>
			  <tr valign="top">
			  	<th scope="row"><label for="MediumEditor">Disable Gutenberg Editor</label></th>
			  	<td><input type="checkbox" id="MediumEditor" name="MediumEditor"  <?php echo $MediumEditor_select;  ?> /></td>
			  </tr>
		  </table>
		  <?php  submit_button(); ?>
	  </form>
  </div>
<?php
	//if(check_admin_referer( 'MediumEditor_form_submit', 'MediumEditor_form_nonce' )){
		if(isset($_POST['MediumEditor_form']) && isset($_POST['nonce']) &&wp_verify_nonce($_POST['nonce'])){
			if(isset($_POST['MediumEditor']) && !empty($_POST['MediumEditor'])){
				$_POST['MediumEditor'] = filter_var($_POST['MediumEditor'], FILTER_SANITIZE_STRING);
				update_option( 'MediumEditor', sanitize_text_field($_POST['MediumEditor']));
			}
		}
	//}
} 
?>