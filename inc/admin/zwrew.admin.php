<?php
/**
 *
 * Handles the admin functionality.
 *
 * @package WordPress
 * @subpackage Embed Videos For Product Image Gallery Using WooCommerce
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add settings link to plugins page
 */
add_filter( 'plugin_action_links_' . ZWREW_PLUGIN_BASENAME, 'zwrew_add_action_links' );
function zwrew_add_action_links ( $links ) {
	$settingslinks = array(
	'<a href="' . admin_url( 'admin.php?page=repeater-entries-widget-settings' ) . '"> '. __( 'Settings', ZWREW_TEXT_DOMAIN ) .'</a>',
	);
	return array_merge( $settingslinks, $links );
}

/**
 * Set up submenu under Settings main menu at admin side
 */
add_action('admin_menu', 'zwrew_repeater_content_widget_setup_menu');
function zwrew_repeater_content_widget_setup_menu(){
		add_submenu_page( 'options-general.php', 'Repeater Entries Widget', 'Repeater Entries Widget', 'manage_options', 'repeater-entries-widget-settings', 'zwrew_repeater_content_widget_init');
}

/**
 * Initialize the plugin and display all options at admin side
 */
function zwrew_repeater_content_widget_init(){
	?>
  <h1><?php echo esc_html_e( 'Repeater Entries Widget', ZWREW_TEXT_DOMAIN ); ?></h1>
  <form method="post" action="options.php">
	<?php settings_fields( 'repeater-entries-widget-settings' ); ?>
	<?php do_settings_sections( 'repeater-entries-widget-settings' ); ?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php echo esc_html_e( 'Maximum entries allowed', ZWREW_TEXT_DOMAIN ); ?>:</th>
			<td><select name="rew_max">
				<?php $maxvalues = array('5','10','15','20','25','30');
				foreach($maxvalues as $value){
					echo '<option value="'.esc_attr($value).'"'.selected( $value, get_option( 'rew_max' ) ).'>'.esc_html($value).'</option>';
				}?>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo esc_html_e( 'Add Image Field', ZWREW_TEXT_DOMAIN ); ?>:</th>
			<td><input type="checkbox" name="rew_image" value="1" <?php echo (get_option( 'rew_image' ) == 1) ? 'checked' : ''; ?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo esc_html_e( 'Add Link on Image Field', ZWREW_TEXT_DOMAIN ); ?>:</th>
			<td><input type="checkbox" name="rew_image_link" value="1" <?php echo (get_option( 'rew_image_link' ) == 1) ? 'checked' : ''; ?>/></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo esc_html_e( 'Add Caption Field', ZWREW_TEXT_DOMAIN ); ?>:</th>
			<td><input type="checkbox" name="rew_caption" value="1" <?php echo (get_option( 'rew_caption' ) == 1) ? 'checked' : ''; ?>/></td>
		</tr>
	   <tr valign="top">
			<th scope="row"><?php echo esc_html_e( 'Add Link on Caption Field', ZWREW_TEXT_DOMAIN ); ?>:</th>
			<td><input type="checkbox" name="rew_caption_link" value="1" <?php echo (get_option( 'rew_caption_link' ) == 1) ? 'checked' : ''; ?>/></td>
	   </tr>
		<tr valign="top">
			<th scope="row"><?php echo esc_html_e( 'Description', ZWREW_TEXT_DOMAIN ); ?>:</th>
			<td>
				<select name = "rew_description">
					<option value="short" <?php echo (get_option( 'rew_description' ) == 'short') ? 'selected': '';?>><?php echo esc_html_e( 'Short description with external link', ZWREW_TEXT_DOMAIN ); ?></option>
					<option value="full" <?php echo (get_option( 'rew_description' ) == 'full') ? 'selected': '';?>><?php echo esc_html_e( 'Full description with Read More button', ZWREW_TEXT_DOMAIN ); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo esc_html_e( 'Maximum entries allowed', ZWREW_TEXT_DOMAIN ); ?>Target Window:</th>
			<td>
				<select name = "rew_link_target">
					<option value="_blank" <?php echo (get_option( 'rew_link_target' ) == '_blank') ? 'selected': '';?>><?php echo esc_html_e( 'New Tab', ZWREW_TEXT_DOMAIN ); ?></option>
					<option value="_window" <?php echo (get_option( 'rew_link_target' ) == '_window') ? 'selected': '';?>>
						<?php echo esc_html_e( 'New Window', ZWREW_TEXT_DOMAIN ); ?>
					</option>
					<option value="_parent" <?php echo (get_option( 'rew_link_target' ) == '_parent') ? 'selected': '';?>><?php echo esc_html_e( 'Parent Window', ZWREW_TEXT_DOMAIN ); ?></option>
				</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php echo esc_html_e( 'Maximum entries allowed', ZWREW_TEXT_DOMAIN ); ?><?php echo esc_html_e( 'Content Alignment', ZWREW_TEXT_DOMAIN ); ?>:</th>
			<td>
				<select name = "content_align">
					<option value="left" <?php echo (get_option( 'content_align' ) == 'left') ? 'selected': '';?>><?php echo esc_html_e( 'Left', ZWREW_TEXT_DOMAIN ); ?></option>
					<option value="center" <?php echo (get_option( 'content_align' ) == 'center') ? 'selected': '';?>><?php echo esc_html_e( 'Center', ZWREW_TEXT_DOMAIN ); ?></option>
					<option value="right" <?php echo (get_option( 'content_align' ) == 'right') ? 'selected': '';?>><?php echo esc_html_e( 'Right', ZWREW_TEXT_DOMAIN ); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<?php submit_button(); ?>
	</div>
  </form>

<?php
}

/**
 * Registers all the setting options
 */
add_action( 'admin_init', 'zwrew_register_repeater_content_widget_settings' );
function zwrew_register_repeater_content_widget_settings() {
	register_setting( 'repeater-entries-widget-settings', 'rew_max' );
	register_setting( 'repeater-entries-widget-settings', 'rew_image' );
	register_setting( 'repeater-entries-widget-settings', 'rew_image_link' );
	register_setting( 'repeater-entries-widget-settings', 'rew_caption' );
	register_setting( 'repeater-entries-widget-settings', 'rew_caption_link' );
	register_setting( 'repeater-entries-widget-settings', 'rew_description' );
	register_setting( 'repeater-entries-widget-settings', 'rew_link_target' );
	register_setting( 'repeater-entries-widget-settings', 'content_align' );
}