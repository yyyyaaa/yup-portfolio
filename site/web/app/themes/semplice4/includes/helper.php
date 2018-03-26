<?php

// -----------------------------------------
// semplice
// /includes/helper.php
// -----------------------------------------

// post queries
require get_template_directory() . '/includes/helper/post_queries.php';

// images
require get_template_directory() . '/includes/helper/images.php';

// thumbnails
require get_template_directory() . '/includes/helper/thumbnails.php';

// onboarding
require get_template_directory() . '/includes/helper/onboarding.php';

// grid
require get_template_directory() . '/includes/helper/grid.php';

// typography
require get_template_directory() . '/includes/helper/typography.php';

// navigation
require get_template_directory() . '/includes/helper/navigation.php';

// sharebox
require get_template_directory() . '/includes/helper/sharebox.php';

// styles
require get_template_directory() . '/includes/helper/styles.php';

// covers
require get_template_directory() . '/includes/helper/covers.php';

// licensing
require get_template_directory() . '/includes/helper/licensing.php';

// notices
require get_template_directory() . '/includes/helper/notices.php';

// -----------------------------------------
// show content
// -----------------------------------------

function semplice_show_content($id, $what) {

	// globals
	global $semplice_content;
	global $admin_api;

	// if password required show form instead of content (only on pages and projects)
	if($what != 'posts' && $what != 'taxonomy' && post_password_required()) {
		// get frontend mode
		$frontend_mode = semplice_get_mode('frontend_mode');
		// set spa
		$spa = false;
		// check if frontend mode is dynamic
		if($frontend_mode == 'dynamic') {
			$spa = true;
		}
		$semplice_content['html'] = get_the_password_form();
	}

	// echo content
	echo '
		<div id="content-holder" data-active-post="' . $id . '">
			' . $admin_api->customize['navigations']->get('html', false, false, false) . '
			<div id="content-' . $id . '" class="content-container active-content ' . semplice_hide_on_init($id) . '">
				<div class="sections">
					' . $semplice_content['html'] . '
				</div>
			</div>
		</div>
	';
}

// -----------------------------------------
// generate ram ids
// -----------------------------------------

function semplice_generate_ram_ids($ram, $is_encoded, $is_block) {

	// is encoded?
	if($is_encoded) {
		// decode ram
		$ram = json_decode($ram, true);
	}

	// output
	$output = $ram;

	// images array
	$images = '';
	$images_arr = array();
	$image_modules = array('image', 'gallerygrid', 'video', 'gallery');

	// change ids
	foreach ($ram['order'] as $section_id => $section) {
		// isset?
		if(isset($ram[$section_id]) && $section_id != 'cover') {
			// get background image and add to images_array
			$images .= semplice_get_background_image($ram[$section_id]['styles']['xl']);
			// create new seciton id
			$new_section_id = 'section_' . substr(md5(rand()), 0, 9);
			// add to array
			$output['order'][$new_section_id] = array();
			// add section content to the output
			$output[$new_section_id] = $ram[$section_id];
			// delete old id rom new ram
			unset($output[$section_id]);
			unset($output['order'][$section_id]);
			// new section to iterate through
			$section_iterate = array();
			// is old single row mode?
			if(isset($section['columns'])) {
				//move columns to a virtual row to make it compatible with the new multi row system
				$section_iterate['row_' . substr(md5(rand()), 0, 9)]['columns'] = $section['columns'];
			} else {
				$section_iterate = $section;
			}
			// iterate rows
			foreach($section_iterate as $row_id => $columns) {
				// new row id
				$new_row_id = 'row_' . substr(md5(rand()), 0, 9);
				// add row to ram
				$output['order'][$new_section_id][$new_row_id] = array(
					'columns' => array(),
				);
				// iterate columns
				foreach ($columns['columns'] as $column_id => $column_content) {
					// get background image and add to images_array
					$images .= semplice_get_background_image($ram[$column_id]['styles']['xl']);
					// create new id
					$new_column_id = 'column_' . substr(md5(rand()), 0, 9);
					// add content to array
					$output['order'][$new_section_id][$new_row_id]['columns'][$new_column_id] = array();
					// add section content to column
					$output[$new_column_id] = $ram[$column_id];
					// delete old id rom new ram
					unset($output[$column_id]);
					foreach ($column_content as $content_id) {
						// get background image and add to images_array
						$images .= semplice_get_background_image($ram[$content_id]['styles']['xl']);
						// get all images used in module
						if(in_array($ram[$content_id]['module'], $image_modules)) {
							$images .= semplice_get_used_images($ram[$content_id]);
						}
						// create new id
						$new_content_id = 'content_' . substr(md5(rand()), 0, 9);
						// add to array
						$output['order'][$new_section_id][$new_row_id]['columns'][$new_column_id][] = $new_content_id;
						// add section content to column
						$output[$new_content_id] = $ram[$content_id];
						// delete old id rom new ram
						unset($output[$content_id]);
					}
				}
			}
		}
	}

	// add images to output if block
	if(true === $is_block) {
		// check if images array is empty?
		if(!empty($images)) {
			// remove last , from string
			if(substr($images, -1) == ',') {
				$images = substr($images, 0, -1);
			}
			$images = explode(",", $images);
			// fetch all image urls in case they have chnaged (ex domain)
			foreach ($images as $image_id) {
				// get image
				$images_arr[$image_id] = semplice_get_image($image_id, 'full');
			}
		} else {
			$images_arr = 'noimages';
		}
		// add images array to ouptut
		$output['images'] = $images_arr;
	}

	// return
	return $output;
}

// -----------------------------------------
// get post settings
// -----------------------------------------

function semplice_generate_post_settings($settings, $post) {

	// check if row has page settings
	if($settings) {
		// always get the latest saved title and permalink to match wordpress
		$settings['meta']['post_title'] = $post->post_title;
		$settings['meta']['permalink'] = $post->post_name;
	} else {
		// define some post settings defaults
		$settings = array(
			'thumbnail' => array(
				'image' => '',
				'width'	=> '',
				'hover_visibility' => 'disabled',
			),
			'meta' => array(
				'post_title' 	=> $post->post_title,
				'permalink'  	=> $post->post_name,
			),
		);
	}

	// yoast seo settings
	$yoast = array('title', 'metadesc', 'opengraph-image', 'opengraph-title', 'opengraph-description', 'twitter-image', 'twitter-title', 'twitter-description', 'meta-robots-nofollow', 'meta-robots-noindex', 'canonical');
	$prefix = '_yoast_wpseo_';

	// get seo from db
	foreach ($yoast as $setting) {
		// get setting
		$setting = $prefix . $setting;
		// check if post meta is there
		$post_meta = get_post_meta($post->ID, $setting, true);
		if(!empty($post_meta)) {
			$settings['seo'][$setting] = get_post_meta($post->ID, $setting, true);
		} else {
			// is set still in semplice? delete it
			if(isset($settings['seo'][$setting])) {
				unset($settings['seo'][$setting]);
			}
		}
	}

	// still empty?
	if(!isset($settings['seo']) || empty($settings['seo'])) {
		$settings['seo'] = new stdClass();
	}
	
	return $settings;
}

// -----------------------------------------
// save spinner
// -----------------------------------------

function semplice_save_spinner() {
	return '
		<div class="save-spinner">
			<div class="semplice-mini-loader">
				<svg class="semplice-spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
					<circle class="path" fill="none" stroke-width="4" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
				</svg>
				<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" width="18" height="14" viewBox="0 0 18 14">
					<path id="Form_1" data-name="Form 1" d="M6.679,13.758L0.494,7.224,1.878,5.762l4.8,5.072L16.153,0.825l1.384,1.462Z"/>
				</svg>
				<span class="saving">Saving...</span>
				<span class="saved">Saved</span>
			</div>
		</div>
	';
}

// -----------------------------------------
// ajax save button
// -----------------------------------------

function semplice_ajax_save_button($link) {
	return $link . '
			<svg class="semplice-spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
				<circle class="path" fill="none" stroke-width="4" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
			</svg>
			<svg class="ajax-save-checkmark" xmlns="http://www.w3.org/2000/svg" width="18" height="14" viewBox="0 0 18 14">
				<path id="Form_1" data-name="Form 1" d="M6.679,13.758L0.494,7.224,1.878,5.762l4.8,5.072L16.153,0.825l1.384,1.462Z"/>
			</svg>
			<span class="save-button-text">Save</span>
		</a>
	';
}

// -----------------------------------------
// get the id we need
// -----------------------------------------

function semplice_get_id() {
	// get post id
	$post_id = get_the_ID();
	// format id
	$post_id = semplice_format_id($post_id, false);
	// return id
	return $post_id;
}

// -----------------------------------------
// format post id
// -----------------------------------------

function semplice_format_id($post_id, $is_crawler) {
	// get blog homepage id
	$blog_home = get_option('page_for_posts');
	// check if blog homepage is not set
	if($blog_home == 0) {
		$blog_home = 'posts';
	}
	// is blog home or not found?
	if(is_home() && !$is_crawler || $post_id == 'posts' || $post_id == $blog_home) {
		$post_id = 'posts';
	} else if(empty($post_id)) {
		$post_id = 'notfound';
	}
	// return id
	return $post_id;
}

// -----------------------------------------
// set the init visibility of our content div
// -----------------------------------------

function semplice_hide_on_init($post_id) {

	// set hide on init
	$hide_on_init = ' hide-on-init';

	// mode defaults
	$frontend_mode = semplice_get_mode('frontend_mode');

	// only remove the hide on init status if sr is disabled and if the static transitions are disabled or the post is not found
	// if there is not scroll reveal on static frontend to fade in the content it will get faded in via GSAP but it will always be hide on init for static transitions to make sure there is a transition
	if(semplice_static_transitions($frontend_mode) == 'disabled') {
		if(semplice_get_sr_status() == 'disabled' || $post_id == 'notfound' || post_password_required()) {
			$hide_on_init = '';
		}
	} else if(true === post_password_required()) {
		$hide_on_init = '';
	}
	
	// output
	return $hide_on_init;
}

// -----------------------------------------
// semplice get post ids
// -----------------------------------------

function semplice_get_post_Ids() {

	// wpdb
	global $wpdb;

	// define post ids array
	$post_ids = array();

	// get posts
	$posts = $wpdb->get_results("SELECT ID, post_name FROM $wpdb->posts WHERE post_status = 'publish'");

	// iterate posts
	foreach ($posts as $post) {
		$post_ids[$post->post_name] = $post->ID;
	}

	// return
	return $post_ids;
}

// -----------------------------------------
// get scroll reveal status on first post
// -----------------------------------------

function semplice_get_sr_status() {

	// vars
	global $post;
	$sr_status = 'enabled';

	// get content
	if(is_object($post)) {
		
		// format post id
		$post_id = semplice_format_id($post->ID, false);

		// instance of get smeplice content
		$semplice_get_content = new semplice_get_content;

		// get content
		$ram = $semplice_get_content->get_ram($post->ID, is_preview());

		// is semplice
		$is_semplice = get_post_meta($post->ID, '_is_semplice', true);

		// check sr status
		if($post_id == 'posts' || $post->post_type == 'post') {
			// is array?
			$sr_status = semplice_get_blog_sr_status();
		} else if(isset($ram['branding']['scroll_reveal']) && $ram['branding']['scroll_reveal'] == 'disabled' || $post_id == 'notfound' || !$is_semplice) {
			$sr_status = 'disabled';
		}
	}

	// return
	return $sr_status;
}

// -----------------------------------------
// get blog sr mode
// -----------------------------------------

function semplice_get_blog_sr_status() {

	// get options
	$blog_options = json_decode(get_option('semplice_customize_blog'), true);

	// status
	$status = 'enabled';

	// set blog sr status individually
	if(is_array($blog_options)) {
		if(isset($blog_options['blog_scroll_reveal']) && $blog_options['blog_scroll_reveal'] == 'disabled') {
			$status = 'disabled';
		}
	}

	// return
	return $status;
}

// -----------------------------------------
// get mode
// -----------------------------------------

function semplice_get_mode($mode) {

	// frontend settings
	$settings = semplice_settings('general');

	// defaults
	$defaults = array(
		'frontend_mode' 	=> 'static',
	);

	// check if mode option in the admin is already set
	if(semplice_rest_url() == 'no-rest-api') {
		return 'static';
	} if(isset($settings) && isset($settings[$mode])) {
		return $settings[$mode];
	} else {
		return $defaults[$mode];
	}
}

// -----------------------------------------
// get breakpoints
// -----------------------------------------

function semplice_get_breakpoints() {
	return array(
		'lg' => array(
			'min' => ' and (min-width: 992px)',
			'max' => ' and (max-width: 1169.9px)'
		),
		'md' => array(
			'min' => ' and (min-width: 768px)',
			'max' => ' and (max-width: 991.9px)',
		),
		'sm' => array(
			'min' => ' and (min-width: 544px)',
			'max' => ' and (max-width: 767.9px)',
		),
		'xs' => array(
			'min' => '',
			'max' => ' and (max-width: 543.9px)',
		),
	);
}

// -----------------------------------------
// static transitions
// -----------------------------------------

function semplice_static_transitions($mode) {

	// frontend settings
	$settings = semplice_settings('general');

	// check if mode option in the admin is already set
	if($mode == 'static') {
		if(isset($settings['static_transitions']) && $settings['static_transitions'] == 'disabled') {
			return 'disabled';
		} else {
			return 'enabled';
		}
	} else {
		return 'disabled';
	}
}

// -----------------------------------------
// get modules
// -----------------------------------------

function semplice_get_modules() {

	// modules
	$modules = array(
		'oembed' 		=> 'oEmbed',
		'portfoliogrid' => 'Portfolio Grid',
		'code'			=> 'Code',
		'share'			=> 'Share',
		'dribbble'		=> 'Dribbble',
		'instagram'		=> 'Instagram',
		'gallerygrid'   => 'Gallery Grid',
		'mailchimp'		=> 'Mailchimp',
	);

	// list
	$list = '';

	foreach ($modules as $module => $content) {
		// add to list
		$list .= '<li><a class="add-content add-module" data-module="' . $module . '"><span>' . get_svg('backend', 'icons/module_' . $module) . '</span>' . $content . '</a></li>';
	}

	// output list
	return '
		<h4>Add Content with</br>our custom modules.</h4>
		<div class="modules">
			<ul class="modules-list">
				' . $list . '
			</ul>
		</div>
	';
}

// -----------------------------------------
// check wp version requirement
// -----------------------------------------

function semplice_wp_version_is($method, $version) {
	// get wp version
	global $wp_version;
	// version compare
	if(version_compare($wp_version, $version, $method)) {
		return true;
	} else {
		return false;
	}
}

// -----------------------------------------
// get rest api url
// -----------------------------------------

function semplice_rest_url() {
	// get rest url
	if(function_exists('rest_url')) {
		return rest_url();
	} else {
		return 'no-rest-api';
	}
}

// -----------------------------------------
// check if value is boolean
// -----------------------------------------

function semplice_boolval($val) {
	return filter_var($val, FILTER_VALIDATE_BOOLEAN);
}

// -----------------------------------------
// semplice head
// -----------------------------------------

function semplice_head($settings) {

	// define output
	$output = '';

	// settings?
	if(is_array($settings)) {
		// google analytics
		if(isset($settings['google_analytics']) && !empty($settings['google_analytics'])) {
			// is script?
			if (strpos($settings['google_analytics'], '<script') !== false) {
				$output .= $settings['google_analytics'];
			}
		}
		// favicon
		if(isset($settings['favicon']) && !empty($settings['favicon'])) {
			// get image url
			$favicon = wp_get_attachment_image_src($settings['favicon'], 'full', false);
			if($favicon) {
				$output .= '<link rel="shortcut icon" type="image/png" href="' . $favicon[0] . '" sizes="32x32">';
			}
		}
	}

	// output
	return $output;
}

// -----------------------------------------
// get category base
// ----------------------------------------

function semplice_get_category_base() {
	// category base
	global $wp_rewrite;
	$category_base = str_replace('%category%', '', $wp_rewrite->get_category_permastruct());
	// return
	return $category_base;
}

// ----------------------------------------
// get tag base
// ----------------------------------------

function semplice_get_tag_base() {
	// category base
	global $wp_rewrite;
	$tag_base = str_replace('%post_tag%', '', $wp_rewrite->get_tag_permastruct());
	// return
	return $tag_base;
}

// ----------------------------------------
// get general settings
// ----------------------------------------

function semplice_get_general_settings() {
	// get general settings and add homepage settings
	$settings = json_decode(get_option('semplice_settings_general'), true);
	// add homepage settings from WP
	$settings['show_on_front'] = get_option('show_on_front');
	$settings['page_on_front']  = get_option('page_on_front');
	$settings['page_for_posts'] = get_option('page_for_posts ');
	// site meta
	$settings['site_title'] = get_option('blogname');
	$settings['site_tagline'] = get_option('blogdescription');
	// return
	return $settings;
}

// ----------------------------------------
// semplice about
// ----------------------------------------

function semplice_about() {

	// get currect license
	$license = semplice_get_license();

	// define licenses
	$licenses = array(
		's4-single'				=> 'Single',
		's4-studio'				=> 'Studio',
		's4-single-to-studio'	=> 'Studio',
		's4-business'			=> 'Business',
		's4-single-to-business'	=> 'Business',
		's4-studio-to-business'	=> 'Business'
	);

	// license
	$about = array();

	if(!$license['is_valid']) {
		$about['registered-to'] = 'Unregistered';
		$about['license-type'] = 'Inactive';
	} else {
		$about['registered-to'] = $license['name'];
		$about['license-type'] = $licenses[$license['product']] . ' License';
	}

	return '
		<p class="first">
			<span>Theme</span><br />
			Semplice ' . ucfirst(semplice_theme('edition')) . ' ' . semplice_theme('version') . '<br />
			<a class="changelog" href="https://www.semplice.com/changelog-v4-studio" target="_blank">Changelog</a>
		</p>
		<p>
			<span>License</span><br />
			' . $about['license-type'] . '
		</p>
		<p>
			<span>Owner</span><br />
			' . $about['registered-to'] . '
		</p>
		<p>
			<span>PHP Version</span><br/>
			php: ' . semplice_theme('php_version') . '
		</p>
		<p class="last">
			<span>Support</span><br />
			<a href="http://help.semplice.com" target="_blank">Helpdesk</a><br />
		</p>
	';
}

// ----------------------------------------
// semplice get mobile css
// ----------------------------------------

function semplice_get_css($selector, $attribute, $css_attributes, $values, $filters, $negative, $output) {
	// prefix
	$prefix = '';
	if(true === $negative) {
		$prefix = '-';
	}
	// transform
	$transform = array('translateY', 'translateX', 'scale', 'move', 'rotate');
	// css for xl breakpoint
	if(isset($values[$attribute]) && !empty($values[$attribute])) {
		foreach ($css_attributes as $css_attribute) {
			if(in_array($css_attribute, $transform)) {
				$output['css'] .= $selector . ' { transform: ' . $css_attribute . '(' . $prefix . semplice_get_value($values[$attribute], $filters) . '); }';
			} else {
				$output['css'] .= $selector . ' { ' . $css_attribute . ': ' . $prefix . semplice_get_value($values[$attribute], $filters) . '; }';
			}
		}
	}
	// get breakpoints
	$breakpoints = semplice_get_breakpoints();
	// iterate breakpoints
	foreach ($breakpoints as $breakpoint => $width) {
		if(isset($values[$attribute . '_' . $breakpoint]) && !empty($values[$attribute . '_' . $breakpoint])) {
			foreach ($css_attributes as $css_attribute) {
				if(in_array($css_attribute, $transform)) {
					$output['mobile_css'][$breakpoint] .= $selector . ' { transform: ' . $css_attribute . '(' . $prefix . semplice_get_value($values[$attribute . '_' . $breakpoint], $filters) . '); }';
				} else {
					$output['mobile_css'][$breakpoint] .= $selector . ' { ' . $css_attribute . ': ' . $prefix . semplice_get_value($values[$attribute . '_' . $breakpoint], $filters) . '; }';
				}
			}
			
		}
	}
	// return
	return $output;
}

// ----------------------------------------
// get semplice value
// ----------------------------------------

function semplice_get_value($value, $filters) {
	// apply filters
	if(false !== $filters) {
		foreach ($filters as $filter) {
			switch($filter) {
				case 'rem-split':
					$value = floatval(str_replace('rem', '', $value));
					$value = ($value / 2) . 'rem';
				break;
				case 'hamburger-area':
					$value_in_px = floatval(str_replace('rem', '', $value) * 18);
					if($value_in_px <= 20) {
						$value = ($value_in_px / 18) . 'rem';
					} else {
						$value = (20 / 18) . 'rem';
					}
				break;
				case 'hamburger-hover':
					$value = $value + 2;
				break;
				case 'add-px':
					$value = $value . 'px';
				break;
				case 'divide-half':
					$value = $value / 2;
				break;
			}
		}
	}
	// return
	return $value;
}

// ----------------------------------------
// get hamburger height
// ----------------------------------------

function semplice_get_hamburger_height($navigation) {
	// desktop height
	$navigation['hamburger_height'] = $navigation['hamburger_thickness'] + ($navigation['hamburger_padding'] * 2);
	// get breakpoints
	$breakpoints = semplice_get_breakpoints();
	// iterate breakpoints
	foreach ($breakpoints as $breakpoint => $width) {
		// hamburger thickness
		$hamburger_thickness = $navigation['hamburger_thickness'];
		if(isset($navigation['hamburger_thickness_' . $breakpoint]) && !empty($navigation['hamburger_thickness_' . $breakpoint])) {

			$hamburger_thickness = $navigation['hamburger_thickness_' . $breakpoint];
		}
		// hamburger padding
		$hamburger_padding = $navigation['hamburger_padding'];
		if(isset($navigation['hamburger_padding_' . $breakpoint]) && !empty($navigation['hamburger_padding_' . $breakpoint])) {
			$hamburger_padding = $navigation['hamburger_padding_' . $breakpoint];
		}
		// hamburger height
		$navigation['hamburger_height_' . $breakpoint] = $hamburger_thickness + ($hamburger_padding * 2);
	}

	return $navigation;
}

// ----------------------------------------
// get blog navbar
// ----------------------------------------

function semplice_get_blog_navbar() {
	// define
	$navbar = false;
	// blog customization
	$blog = json_decode(get_option('semplice_customize_blog'), true);
	// check nav
	if(isset($blog['blog_navbar']) && !empty($blog['blog_navbar']) && $blog['blog_navbar'] !== 'default') {
		$navbar = $blog['blog_navbar'];
	}
	return $navbar;
}

?>