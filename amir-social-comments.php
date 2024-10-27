<?php
/**
 * Plugin bootstrap file
 *
 * @amir-social-comments
 * Plugin Name:       Amir Social Comments
 * Plugin URI:        https://www.heateor.com/amir-social-comments/#live_demo
 * Description:       Allow your website visitors to post comment via their Facebook account
 * Version:           1.0
 * Author:            Mohammad Aamir Khan
 * Author URI:        https://www.linkedin.com/in/amir-khan-449853193/
 * Text Domain:       amir-social-comments
 * Domain Path:       /languages
 */
defined( 'ABSPATH' ) or die( "Cheating........Uh!!" );

define( 'ASC_VERSION', '1.0' );

$asc_options = get_option( 'asc' );

/**
 * Save default plugin options
 */
function asc_save_default_options() {
	// default options
	add_option( 'asc', array(
		 'title' => 'Leave a comment',
		'title_color' => '',
		'title_font' => '12',
		'title_background_color' => '',
		'data-width' => '',
		'title_data-order-by' => 'social',
		'data-colorscheme' => 'light',
		'data-mobile' => 'true',
		'title_no_comment' => '5',
		'position_bottom' => '1',
		'load_comment' => 'default',
		'enables_commenting' => '1',
		'enabled_commenting' => '1',
		'title_tag' => 'h4',
		'text_language' => 'en_US' 
	) );
	// plugin version
	add_option( 'asc_version', ASC_VERSION );
}

/**
 * Plugin activation function
 */
function asc_activate_plugin( $network_wide ) {
	global $wpdb;
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		if ( $network_wide ) {
			$old_blog = $wpdb->blogid;
			//Get all blog ids
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				asc_save_default_options();
			}
			switch_to_blog( $old_blog );
			return;
		}
	}
	asc_save_default_options();
}
register_activation_hook( __FILE__, 'asc_activate_plugin' );

/**
 * Renders Facebook comments box at the webpages
 */
function asc_renders_facebook_comments( $content ) {
	global $asc_options, $post;
	if ( $asc_options['load_comment'] == 'default' )
		$load_comment = get_permalink( $post->ID );
	if ( $asc_options['load_comment'] == 'home' )
		$load_comment = get_home_url( $post->ID );
	$fb_comments_html = "<div id='fb-root'></div>
<script async defer crossorigin='anonymous' src='https://connect.facebook.net/" . $asc_options['text_language'] . "/sdk.js#xfbml=1&version=v6.0&appId=1929022817243805&autoLogAppEvents=1'></script><div class='fb-comments' data-href='" . $load_comment . "' data-width =" . $asc_options['data-width'] . " data-order-by='social' data-mobile='" . $asc_options['data-mobile'] . "'  data-colorscheme='" . $asc_options['data-colorscheme'] . "' data-numposts='" . $asc_options['title_no_comment'] . "' data-order-by='" . $asc['title_data-order-by'] . "' style='background-color:" . $asc_options['title_background_color'] . ";'></div>";
	if ( isset( $asc_options['enable_commenting'] ) && is_front_page() ) {
		// show facebook comments
		if ( isset( $asc_options['position_top'] ) ) {
			$content = $fb_comments_html . $content;
		}
		if ( isset( $asc_options['position_bottom'] ) ) {
			$content = $content . $fb_comments_html;
		}
	}
	if ( isset( $asc_options['enables_commenting'] ) && is_single() ) {
		if ( isset( $asc_options['position_top'] ) ) {
			$content = $fb_comments_html . $content;
		}
		if ( isset( $asc_options['position_bottom'] ) ) {
			$content = $content . $fb_comments_html;
		}
	}
	if ( isset( $asc_options['enabled_commenting'] ) && is_page() ) {
		if ( isset( $asc_options['position_top'] ) ) {
			$content = $fb_comments_html . $content;
		}
		if ( isset( $asc_options['position_bottom'] ) ) {
			$content = $content . $fb_comments_html;
		}
	}
	if ( isset( $asc_options['enableo_commenting'] ) && is_attachment() ) {
		if ( isset( $asc_options['position_top'] ) ) {
			$content = $fb_comments_html . $content;
		}
		if ( isset( $asc_options['position_bottom'] ) ) {
			$content = $content . $fb_comments_html;
		}
	}
	if ( isset( $asc_options['enablek_commenting'] ) && is_category( $category = '' ) ) {
		if ( isset( $asc_options['position_top'] ) ) {
			$content = $fb_comments_html . $content;
		}
		if ( isset( $asc_options['position_bottom'] ) ) {
			$content = $content . $fb_comments_html;
		}
	}
	if ( isset( $asc_options['enableb_commenting'] ) && is_archive() ) {
		if ( isset( $asc_options['position_top'] ) ) {
			$content = $fb_comments_html . $content;
		}
		if ( isset( $asc_options['position_bottom'] ) ) {
			$content = $content . $fb_comments_html;
		}
	}
	return $content;
}
add_filter( 'the_content', 'asc_renders_facebook_comments' );

/**
 * Creates plugin-menu in admin area
 */
function asc_plugin_menu() {
	$page = add_menu_page( __( 'Amir Social Comments', 'amir-social-comments' ), 'Amir Social Comments', 'manage_options', 'amir-social-comments-options', 'asc_plugin_option_page', plugins_url( 'images/logo.png', __FILE__ ) );
	// options
	$options_page = add_submenu_page( 'amir-social-comments-options', __( "Amir Social Comments - General Options", 'amir-social-comments' ), __( "Amir Social Comments", 'amir-social-comments' ), 'manage_options', 'amir-social-comments-options', 'asc_plugin_option_page' );
}
add_action( 'admin_menu', 'asc_plugin_menu' );

/**
 * Renders option-form of plugin
 */
function asc_plugin_option_page() {
	echo '<h1>Amir Social Comments</h1>';
	global $asc_options;
	?>
	<form action="options.php" method="post">
	<?php
	settings_fields( 'asc_options' );
	?>
	<label for="text"><?php
	_e( 'Title', 'amir-social-comments' );
	?></label><br>
	<input type="text" name="asc[title]" value="<?php
	echo $asc_options['title'];
	?>" ><br>
	<small><?php
	_e( 'Title to display above Facebook Comments box', 'amir-social-comments' );
	?></small>
	<br></br>
	<label for="text">HTML tag of the title</label><br> 
	<select  name="asc[title_tag]"> 
		<option value="h1">h1</option>
		<option value="h2">h2</option>
		<option value="tag">h3</option>
		<option value="h4" selected="">h4</option>
		<option value="h5">h5</option>
		<option value="h6">h6</option>
		<option value="div">Div</option>
		<option value="span">Scan</option>
	</select>
	<br></br>
	<label for="text"><?php
	_e( 'Title-text color', 'amir-social-comments' );
	?></label><br>
	<input type="text" name="asc[title_color]" value="<?php
	echo $asc_options['title_color'];
	?>" ><br>
	<br>
	<label for="text"><?php
	_e( 'Font-size of title', 'amir-social-comments' );
	?></label><br>
	<input type="text" name="asc[title_font]" value="<?php
	echo $asc_options['title_font'];
	?>" ><br>
	<br>
	<label for="text"><?php
	_e( 'Background color of comment box', 'amir-social-comments' );
	?></label><br>
	<input type="text" name="asc[title_background_color]" value="<?php
	echo $asc_options['title_background_color'];
	?>" ><br>
	<br>
	<label for="text"><?php
	_e( 'Width of comment box', 'amir-social-comments' );
	?></label><br>
	<input type="text" name="asc[data-width]" value="<?php
	echo $asc_options['data-width'];
	?>" >
	<br></br>
	<label for="text"><?php
	_e( 'Order comments by', 'amir-social-comments' );
	?></label><br>
	<select  name="asc[title_data-order-by]">
	<option value="social" <?php
	echo $asc_options['title_data-order-by'] == 'social' ? 'selected' : '';
	?>>Social</option>
	<option value="reverse_time" <?php
	echo $asc_options['title_data-order-by'] == 'reverse_time' ? 'selected' : '';
	?>>Reverse Time</option>
	<option value="time" <?php
	echo $asc_options['title_data-order-by'] == 'time' ? 'selected' : '';
	?>>Time</option>
	</select> <br>
	<small><?php
	_e( '<b>Social</b>:	This is also known as "Top". The comments plugin uses social signals to surface the highest quality comments. Comments are ordered so that the most relevant comments from friends and friends of friends are shown first, as well as the most-liked or active discussion threads. Comments marked as spam are hidden from view.', 'amir-social-comments' );
	?></small><br>
	<small><?php
	_e( '<b>Time</b>:
	Comments are shown in the order that they were posted, with the oldest comments at the top and the newest at the bottom.', 'amir-social-comments' );
	?></small><br>
	<small><?php
	_e( '<b>Reverse Time</b>:
	Comments are shown in the opposite order that they were posted, with the newest comments at the top and the oldest at the bottom.', 'amir-social-comments' );
	?></small>
	<br></br>
	<label for="text"><?php
	_e( 'Color-scheme', 'amir-social-comments' );
	?></label><br>
	<select  name="asc[data-colorscheme]">
	<option value="light" <?php
	echo $asc_options['data-colorscheme'] == 'light' ? 'selected' : '';
	?>>light</option>
	<option value="dark" <?php
	echo $asc_options['data-colorscheme'] == 'dark' ? 'selected' : '';
	?>>dark</option>
	</select> 
	<br></br>

	<label for="text">Data mobile</label><br>
	<select  name="asc[data-mobile]">
	<option value="true" <?php
	echo $asc_options['data-mobile'] == 'true' ? 'selected' : '';
	?>>true</option>
	<option value="false" <?php
	echo $asc_options['data-mobile'] == 'false' ? 'selected' : '';
	?>>false</option>
	</select><br>
	<small><?php
	_e( 'A boolean value that specifies whether to show the mobile-optimized version or not.', 'amir-social-comments' );
	?></small> 
	<br></br>	
	<label for="text">Number of commemts</label><br>
	<input type="text" name="asc[title_no_comment]" value="<?php
	echo $asc_options['title_no_comment'];
	?>" ><br> <small><?php
	_e( 'The number of comments to show by default. The minimum value is 1', 'amir-social-comments' );
	?></small><br>
	<br>
	<label for="text">Position of Comment Box</label><br>
	<input name="asc[position_top]" type="checkbox" <?php
	if ( isset( $asc_options['position_top'] ) ) {
		echo 'checked="checked"';
	}
	?> value="1">
	<label for="text">Top of the content</label>
	<br>
	<input name="asc[position_bottom]" type="checkbox" <?php
	if ( isset( $asc_options['position_bottom'] ) ) {
		echo 'checked="checked"';
	}
	?> value="1">
	<label for="">Bottom of the content</label> 

	<br></br>
	<label for="Load comments"><?php
	_e( 'Load cooments for', 'amir-social-comments' );
	?></label>
	<br>
	<td >
	<input id="load_comments" name="asc[load_comment]" type="radio"  <?php
	if ( $asc_options['load_comment'] == 'default' ) {
		echo 'checked="checked"';
	}
	?> value="default">
	<label for="load_comments">Url of the webpage where Facebook Comments box is placed (default)</label><br>
	<input id="loads_comment" name="asc[load_comment]" type="radio"  <?php
	if ( $asc_options['load_comment'] == 'home' ) {
		echo 'checked="checked"';
	}
	?> value="home">
	<label for="loads_comment">Url of the homepage of your website</label><br>

	</td>
	<br>
	<td>
	<label>Enable Social Commenting at</label>
	<br>
	<input id="enable_commenting" name="asc[enable_commenting]" type="checkbox" <?php
	if ( isset( $asc_options['enable_commenting'] ) ) {
		echo 'checked="checked"';
	}
	?> value="1">
	<label for="enable_commenting">Homepage</label><br>
	<input id="enables_commenting" name="asc[enables_commenting]" type="checkbox" <?php
	if ( isset( $asc_options['enables_commenting'] ) ) {
		echo 'checked="checked"';
	}
	?> value="1">

	<label for="enables_commenting">Post(s)</label><br>
	<input id="enabled_commenting" name="asc[enabled_commenting]" type="checkbox"<?php
	if ( isset( $asc_options['enabled_commenting'] ) ) {
		echo 'checked="checked"';
	}
	?>value="1">
	<label for="enabled_commenting">Page(s)</label><br>
	<input id="enbaleded_commenting" name="asc[enableo_commenting]" type="checkbox" <?php
	if ( isset( $asc_options['enableo_commenting'] ) ) {
		echo 'checked="checked"';
	}
	?> value="1">
	<label for="enbaleded_commenting">Attachment(s)</label><br>
	<input id="able_commenting" name="asc[enablek_commenting]" type="checkbox" <?php
	if ( isset( $asc_options['enablek_commenting'] ) ) {
		echo 'checked="checked"';
	}
	?> value="1">
	<label for="able_commenting">Category Archives</label><br>
	<input id="abled_commenting" name="asc[enableb_commenting]" type="checkbox" <?php
	if ( isset( $asc_options['enableb_commenting'] ) ) {
		echo 'checked="checked"';
	}
	?> value="1">
	<label for="abled_commenting">Archive Pages (Tag, Author or Date based pages)</label><br>
	<br>
	<label ><?php
	_e( 'Language of Facebook Comments interface', 'amir-social-comments' );
	?></label><br>
	<input name="asc[text_language]" type="text" value="<?php
	echo $asc_options['text_language'];
	?>">
	<br>
	<small>

	<?php
	echo sprintf( __( 'Enter the code of the language you want to use to display commenting. You can find the language codes at <a href="%s" target="_blank">this link</a>', 'amir-social-comments' ), 'http://fbdevwiki.com/wiki/Locales#Complete_List_.28as_of_2012-06-10.29' );
	?></small><br></br>
	<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">

	</form>
	<br>
	<?php
}

/**
 * Saves option-form
 */
function asc_plugin_options_init() {
	register_setting( 'asc_options', 'asc', 'validate_options' );
}
add_action( 'admin_init', 'asc_plugin_options_init' );