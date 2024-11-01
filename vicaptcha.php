<?php
/*
Plugin Name: Vicap
Plugin URI: https://myvicap.com/
Description: VICAP™ (Visual Integration CAPTCHA) is a novel technology that is based on human psychophysics approach. This means VICAP will not have any meaning for bots, but it is easy for humans. Watch the video to learn more.
Author: MRB Corporation
Version: 2.1
*/
if ( !defined( 'ABSPATH' ) ) exit;

add_action('admin_menu', 'vicap_captcha_menu');
function vicap_captcha_menu() {
    add_menu_page('Vicap Captcha Settings',
            'Vicap Captcha',
            'manage_options',
            'vicap-captcha',
            'vicap_captcha_settings'
	);
}
function vicap_captcha_settings(){?>
<div class="wrap">
  <form action="options.php" method="post">
    <?php settings_fields("section");?>
    <p class="shortcode"><strong>Use Shortcode: [vicap] in any of the form.</strong></p>
    <?php
	do_settings_sections("captcha-options");
	submit_button();
	?>
  </form>
</div>
<?php
}

function vicap_captcha_fields()
{
	add_settings_section("section", "Captcha Settings", null, "captcha-options");
	add_settings_field("vicap_tocken", "Captcha Token", "vicap_tocken_element", "captcha-options", "section");
	add_settings_field("vi_buttonclass", "Submit button class", "vicap_button_element", "captcha-options", "section");
	add_settings_field("vi_bootstrap_css", "Enable bootstrap css", "vicap_css_element", "captcha-options", "section");
	add_settings_field("vi_bootstrap_js", "Enable bootstrap js", "vicap_js_element", "captcha-options", "section");
	register_setting("section", "vicap_tocken");
	register_setting("section", "vi_buttonclass");
	register_setting("section", "vi_bootstrap_css");
	register_setting("section", "vi_bootstrap_js");
}

add_action("admin_init", "vicap_captcha_fields");
function vicap_tocken_element()
{
?>
<input type="text" name="vicap_tocken" size='40' id="vicap_tocken" value="<?php echo esc_attr(get_option('vicap_tocken')); ?>" />
<p class="description">
  <?php _e( 'Please Enter Token.' ); ?>
</p>
<a href="https://myvicap.com/Register" target="_blank">Get Token Here</a>
<?php
}
function vicap_button_element(){
?>
<input type="text" name="vi_buttonclass" size='40' id="vi_buttonclass" value="<?php echo esc_attr(get_option('vi_buttonclass')); ?>" />
<p class="description">
  <?php _e( 'Please submit button class. <strong>Like As:</strong> wpcf7-submit' ); ?>
</p>
<?php
}
function vicap_css_element(){
$options = get_option('vi_bootstrap_css');?>
<select id="vi_bootstrap_css" name='vi_bootstrap_css[vi_bootstrap_css]'>
  <option value='no' <?php selected( $options['vi_bootstrap_css'], 'no'); ?>>
  <?php _e( 'No',vi_text_domain); ?>
  </option>
  <option value='yes' <?php selected( $options['vi_bootstrap_css'], 'yes'); ?>>
  <?php _e( 'Yes',vi_text_domain ); ?>
  </option>
</select>
<p class="description">
  <?php _e( 'enable bootstrap css ?.'); ?>
</p>
<?php
}
function vicap_js_element(){
$options = get_option('vi_bootstrap_js');?>
<select id="vi_bootstrap_js" name='vi_bootstrap_js[vi_bootstrap_js]'>
  <option value='no' <?php selected( $options['vi_bootstrap_js'], 'no'); ?>>
  <?php _e( 'No',vi_text_domain); ?>
  </option>
  <option value='yes' <?php selected( $options['vi_bootstrap_js'], 'yes'); ?>>
  <?php _e( 'Yes',vi_text_domain ); ?>
  </option>
</select>
<p class="description">
  <?php _e( 'enable bootstrap js ?.'); ?>
</p>
<?php
}
function vicap_wp_enqueue_scripts() {
	$vicap_css = get_option('vi_bootstrap_css');
	$vicap_css = $vicap_css['vi_bootstrap_css'];
	$vicap_js = get_option('vi_bootstrap_js');
	$vicap_js = $vicap_js['vi_bootstrap_js'];
	wp_enqueue_script('captchaResources', 'https://myvicap.com/captcha_api/CaptchaFormate/CaptchaResources_WP.js',
	array(), null,true);
	// wp_enqueue_script('captchaResources', 'https://myvicap.com/captcha_api/CaptchaFormate/CaptchaResources.js');
	if($vicap_css == 'yes') {
		wp_enqueue_style('vicap-css-bootstrap', plugin_dir_url(__FILE__).'css/bootstrap.min.css' );		
	}
	if($vicap_js == 'yes') {
		wp_enqueue_script('vicap-js-bootstrap', plugin_dir_url(__FILE__).'js/bootstrap.min.js');
	}
}
add_action('wp_enqueue_scripts','vicap_wp_enqueue_scripts');

function vicap_footer_styles(){
	wp_enqueue_style('vicap-custom', plugin_dir_url(__FILE__).'css/vicapcustom.css');
}
add_action( 'get_footer', 'vicap_footer_styles' );

add_action( 'wp_footer', 'vicap_script');
function vicap_script(){
$vicap_tocken = get_option('vicap_tocken');
$vi_buttonclass = get_option('vi_buttonclass');
?>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<noscript>
		<META HTTP-EQUIV="Refresh" CONTENT="0;URL=https://myvicap.com/Email/noscript.html">
</noscript>
<?php if($vicap_tocken) { ?>
<script type="text/javascript">

jQuery('input:submit').hide();
jQuery('*').hasClass('CaptchaSubmit') ? '' : jQuery("script[src='https://myvicap.com/captcha_api/CaptchaFormate/CaptchaResources_WP.js']").remove(); 
jQuery(function () {	
	  InitCaptcha('<?php echo $vicap_tocken; ?>');
	  jQuery('input:submit').show();
		jQuery('.CaptchaSubmit').click(function () {
			if (CheckCaptcha() != true) {
					return false;
				}else {
					//put your submit Button Code...
				}
				  
						
			});
});

</script>
<?php
}

}
function vicap_captcha_output(){
ob_start();
?>
<div class="v-cap"></div>
<?php
return ob_get_clean();
}
add_shortcode('vicap', 'vicap_captcha_output');
if ( function_exists( 'wpcf7_add_shortcode' )) {
	wpcf7_add_shortcode('vicap', 'custom_lptitle_shortcode_handler', true);
}
function custom_lptitle_shortcode_handler( $tag ) {
	global $post;
	$html = '<div class="v-cap"></div>';
return $html;
}